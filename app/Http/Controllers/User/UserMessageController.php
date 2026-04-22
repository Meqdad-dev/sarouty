<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Message;
use App\Models\Listing;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class UserMessageController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Display all messages (inbox and sent).
     */
    public function index(Request $request)
    {
        $user = Auth::user();
        $tab = $request->get('tab', 'inbox');

        // Inbox messages (received and approved)
        $inbox = Message::with(['listing.images', 'sender'])
            ->where('receiver_id', $user->id)
            ->where('status', 'approved')
            ->latestFirst()
            ->paginate(15, ['*'], 'inbox_page');

        // Sent messages
        $sent = Message::with(['listing.images', 'receiver'])
            ->sentBy($user->id)
            ->latestFirst()
            ->paginate(15, ['*'], 'sent_page');

        // Stats
        $stats = [
            'inbox_total' => Message::where('receiver_id', $user->id)->where('status', 'approved')->count(),
            'inbox_unread' => Message::where('receiver_id', $user->id)->where('status', 'approved')->unread()->count(),
            'sent_total' => Message::sentBy($user->id)->count(),
        ];

        return view('pages.user.messages.index', compact('inbox', 'sent', 'stats', 'tab'));
    }

    /**
     * Show a specific message conversation.
     */
    public function show(Message $message)
    {
        $user = Auth::user();

        // Authorization - user must be sender or receiver
        if ($message->sender_id !== $user->id && $message->receiver_id !== $user->id) {
            abort(403, 'Accès non autorisé.');
        }

        // Mark as read if user is the receiver
        if ($message->receiver_id === $user->id) {
            if (!$message->is_read) {
                $message->markAsRead();
            }
            
            // Delete related notifications now that the user is viewing the message
            \DB::table('user_notifications')
                ->where('user_id', $user->id)
                ->where('url', route('user.messages.show', $message->id))
                ->delete();
        }

        $message->load(['listing.images', 'listing.user', 'sender', 'receiver', 'replies']);

        // Get conversation thread (only approved replies for the receiver context?)
        // The sender sees their own pending messages, but let's just make it simpler: thread includes all. If they are sender, they see pending. If receiver, they shouldn't see pending.
        // Actually, it's safer to only show approved message thread, UNLESS user is the sender.
        // For now, let's keep it simple: just show thread, since receiver shouldn't have the link to the pending message anyway.
        $thread = collect([$message]);
        if ($message->replied_to_id) {
            $parent = $message->repliedTo;
            while ($parent) {
                $thread->prepend($parent);
                $parent = $parent->replied_to_id ? $parent->repliedTo : null;
            }
        }

        // Add replies
        foreach ($message->replies as $reply) {
            // If user is receiver of this reply, and it's not approved, don't show it.
            if ($reply->receiver_id === $user->id && $reply->status !== 'approved') {
                continue;
            }
            $thread->push($reply);
        }

        return view('pages.user.messages.show', compact('message', 'thread'));
    }

    /**
     * Show form to contact a listing owner.
     */
    public function create(Request $request, Listing $listing)
    {
        // Check if user owns this listing
        if ($listing->user_id === Auth::id()) {
            return back()->with('error', 'Vous ne pouvez pas contacter votre propre annonce.');
        }

        return view('pages.user.messages.create', compact('listing'));
    }

    /**
     * Send a message to a listing owner.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'listing_id' => 'required|exists:listings,id',
            'message' => 'required|string|min:10|max:2000',
            'sender_phone' => 'nullable|string|max:20',
        ]);

        $listing = Listing::findOrFail($validated['listing_id']);

        // Can't message own listing
        if ($listing->user_id === Auth::id()) {
            return back()->with('error', 'Vous ne pouvez pas contacter votre propre annonce.');
        }

        $message = Message::create([
            'listing_id' => $validated['listing_id'],
            'sender_id' => Auth::id(),
            'receiver_id' => $listing->user_id,
            'sender_name' => Auth::user()->name,
            'sender_email' => Auth::user()->email,
            'sender_phone' => $validated['sender_phone'] ?? Auth::user()->phone,
            'message' => $validated['message'],
            'subject' => 'Intérêt pour: ' . $listing->title,
            'is_read' => false,
            'status' => 'pending',
        ]);

        return redirect()->route('user.messages.show', $message)
            ->with('success', 'Message envoyé avec succès ! Il est en attente de modération par l\'administration.');
    }

    /**
     * Reply to a message.
     */
    public function reply(Request $request, Message $message)
    {
        $user = Auth::user();

        // Authorization
        if ($message->sender_id !== $user->id && $message->receiver_id !== $user->id) {
            abort(403, 'Accès non autorisé.');
        }

        $validated = $request->validate([
            'message' => 'required|string|min:5|max:2000',
        ]);

        // Determine the receiver (the other person in the conversation)
        $receiverId = $message->sender_id === $user->id ? $message->receiver_id : $message->sender_id;

        $reply = Message::create([
            'listing_id' => $message->listing_id,
            'sender_id' => $user->id,
            'receiver_id' => $receiverId,
            'sender_name' => $user->name,
            'sender_email' => $user->email,
            'sender_phone' => $user->phone,
            'message' => $validated['message'],
            'subject' => 'Re: ' . ($message->subject ?? $message->listing->title),
            'replied_to_id' => $message->id,
            'is_read' => false,
            'status' => 'pending',
        ]);

        return redirect()->route('user.messages.show', $message)
            ->with('success', 'Réponse envoyée ! Elle est en attente de modération par l\'administration.');
    }

    /**
     * Mark a message as read.
     */
    public function markRead(Message $message)
    {
        $user = Auth::user();

        if ($message->receiver_id !== $user->id) {
            abort(403, 'Accès non autorisé.');
        }

        $message->markAsRead();

        // Delete related notifications
        \DB::table('user_notifications')
            ->where('user_id', Auth::id())
            ->where('url', route('user.messages.show', $message->id))
            ->delete();

        return back()->with('success', 'Message marqué comme lu.');
    }

    /**
     * Delete a message.
     */
    public function destroy(Message $message)
    {
        $user = Auth::user();

        // User must be sender or receiver
        if ($message->sender_id !== $user->id && $message->receiver_id !== $user->id) {
            abort(403, 'Accès non autorisé.');
        }

        // Delete related notifications
        \DB::table('user_notifications')
            ->where('user_id', Auth::id())
            ->where('url', route('user.messages.show', $message->id))
            ->delete();

        $message->delete();

        return redirect()->route('user.messages.index')
            ->with('success', 'Message supprimé.');
    }

    /**
     * Get unread messages count (for AJAX).
     */
    public function unreadCount()
    {
        $count = Message::receivedBy(Auth::id())
            ->where('status', 'approved')
            ->unread()
            ->count();

        return response()->json(['count' => $count]);
    }

    /**
     * Create an in-app notification.
     */
    private function createNotification($userId, $type, $data)
    {
        \DB::table('user_notifications')->insert([
            'user_id' => $userId,
            'type' => $type,
            'title' => $this->getNotificationTitle($type, $data),
            'body' => $this->getNotificationBody($type, $data),
            'url' => route('user.messages.show', $data['message_id']),
            'read' => false,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    private function getNotificationTitle($type, $data): string
    {
        return match($type) {
            'new_message' => "Nouveau message de {$data['sender_name']}",
            'message_reply' => "Réponse de {$data['sender_name']}",
            default => 'Nouvelle notification',
        };
    }

    private function getNotificationBody($type, $data): string
    {
        return match($type) {
            'new_message' => "Vous avez reçu un nouveau message concernant votre annonce.",
            'message_reply' => "Vous avez reçu une réponse à votre message.",
            default => '',
        };
    }
}
