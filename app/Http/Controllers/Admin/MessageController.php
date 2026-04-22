<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Message;
use App\Services\EmailService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class MessageController extends Controller
{
    public function __construct(protected EmailService $emailService) {}

    public function index(Request $request)
    {
        $query = Message::with(['sender', 'receiver', 'listing'])->latest();

        $status = $request->get('status', 'pending');
        
        if ($status === 'read') {
            $query->where('is_read', true);
        } elseif ($status === 'unread') {
            $query->where('is_read', false);
        } elseif (in_array($status, ['pending', 'approved', 'rejected'])) {
            $query->where('status', $status);
        }

        if ($request->filled('q')) {
            $q = $request->string('q');
            $query->where(function ($sub) use ($q) {
                $sub->where('message', 'like', "%{$q}%")
                    ->orWhere('sender_name', 'like', "%{$q}%")
                    ->orWhere('sender_email', 'like', "%{$q}%")
                    ->orWhereHas('listing', fn ($listingQuery) => $listingQuery->where('title', 'like', "%{$q}%"));
            });
        }

        $messages = $query->paginate(20)->withQueryString();

        $stats = [
            'total'   => Message::count(),
            'pending' => Message::where('status', 'pending')->count(),
            'unread'  => Message::where('is_read', false)->where('status', 'approved')->count(),
        ];

        return view('pages.admin.messages.index', compact('messages', 'stats', 'status'));
    }

    public function approve(Request $request, Message $message): RedirectResponse
    {
        $validated = $request->validate([
            'edited_message' => 'nullable|string|max:2000',
        ]);

        $message->update([
            'message' => $validated['edited_message'] ?? $message->message,
            'status'  => 'approved',
        ]);

        // Create notification for receiver
        \DB::table('user_notifications')->insert([
            'user_id'    => $message->receiver_id,
            'type'       => $message->replied_to_id ? 'message_reply' : 'new_message',
            'title'      => $message->replied_to_id ? "Réponse de {$message->sender_name}" : "Nouveau message de {$message->sender_name}",
            'body'       => $message->replied_to_id ? "Vous avez reçu une réponse à votre message." : "Vous avez reçu un nouveau message concernant votre annonce.",
            'url'        => route('user.messages.show', $message->id),
            'read'       => false,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // Envoyer l'email au propriétaire de l'annonce avec les coordonnees du client
        if (!$message->replied_to_id) {
            $this->emailService->sendContactMessage($message);
        }

        return back()->with('success', 'Message approuvé et transféré au destinataire avec succès.');
    }

    public function reject(Message $message): RedirectResponse
    {
        $message->update(['status' => 'rejected']);
        return back()->with('success', 'Message rejeté.');
    }

    public function markRead(Message $message): RedirectResponse
    {
        $message->update(['is_read' => true]);

        return back()->with('success', 'Message marqué comme lu.');
    }

    public function reply(Request $request, Message $message): RedirectResponse
    {
        $validated = $request->validate([
            'reply' => 'required|string|min:5|max:2000',
        ]);

        try {
            // Send reply email to the sender
            $this->emailService->send(
                $message->sender_email,
                "Re: Votre message concernant \"{$message->listing->title}\"",
                'emails.message-reply',
                [
                    'originalMessage' => $message,
                    'replyContent'    => $validated['reply'],
                    'listingTitle'    => $message->listing->title ?? 'Annonce',
                    'adminName'       => auth()->user()->name ?? 'Support Sarouty',
                ]
            );

            // Mark as read
            $message->update(['is_read' => true]);

            return back()->with('success', 'Réponse envoyée avec succès à ' . $message->sender_email);
        } catch (\Throwable $e) {
            return back()->withErrors(['error' => 'Erreur lors de l\'envoi de la réponse : ' . $e->getMessage()]);
        }
    }

    public function destroy(Message $message): RedirectResponse
    {
        $message->delete();

        return back()->with('success', 'Message supprimé avec succès.');
    }
}
