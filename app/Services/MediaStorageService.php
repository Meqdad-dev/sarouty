<?php

namespace App\Services;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use RuntimeException;

class MediaStorageService
{
    public function uploadUploadedFile(UploadedFile $file, string $folder = 'uploads'): string
    {
        if (! $this->isCloudinaryConfigured()) {
            return $file->store($folder, 'public');
        }

        return $this->uploadBinary(
            contents: $file->get(),
            folder: $folder,
            originalName: $file->getClientOriginalName(),
            mimeType: $file->getMimeType(),
        );
    }

    public function uploadFromUrl(string $url, string $folder = 'uploads'): ?string
    {
        if (! filter_var($url, FILTER_VALIDATE_URL)) {
            return null;
        }

        if (! $this->isCloudinaryConfigured()) {
            $response = Http::timeout(20)->get($url);

            if (! $response->successful()) {
                return null;
            }

            $extension = $this->extensionFromContentType($response->header('Content-Type'));
            $path = trim($folder, '/') . '/' . Str::uuid() . '.' . $extension;
            Storage::disk('public')->put($path, $response->body());

            return $path;
        }

        $response = Http::asForm()->timeout(30)->post($this->uploadEndpoint(), array_merge(
            $this->signedParameters([
                'folder' => trim($folder, '/'),
                'public_id' => trim($folder, '/') . '/' . Str::uuid(),
            ]),
            [
                'file' => $url,
            ]
        ));

        if (! $response->successful()) {
            return null;
        }

        return $response->json('secure_url');
    }

    public function delete(?string $path): void
    {
        if (blank($path)) {
            return;
        }

        if ($this->isRemoteUrl($path)) {
            if ($this->isCloudinaryUrl($path) && $this->isCloudinaryConfigured()) {
                $publicId = $this->extractPublicIdFromUrl($path);

                if ($publicId) {
                    Http::asForm()->timeout(20)->post($this->destroyEndpoint(), $this->signedParameters([
                        'public_id' => $publicId,
                    ]));
                }
            }

            return;
        }

        Storage::disk('public')->delete($path);
    }

    public function exists(?string $path): bool
    {
        if (blank($path)) {
            return false;
        }

        return $this->isRemoteUrl($path) ? true : Storage::disk('public')->exists($path);
    }

    public function url(?string $path): ?string
    {
        if (blank($path)) {
            return null;
        }

        return $this->isRemoteUrl($path) ? $path : asset('storage/' . ltrim($path, '/'));
    }

    public function isRemoteUrl(?string $path): bool
    {
        return filled($path) && Str::startsWith($path, ['http://', 'https://']);
    }

    public function isCloudinaryConfigured(): bool
    {
        return filled(env('CLOUDINARY_CLOUD_NAME'))
            && filled(env('CLOUDINARY_API_KEY'))
            && filled(env('CLOUDINARY_API_SECRET'));
    }

    private function uploadBinary(string $contents, string $folder, string $originalName, ?string $mimeType = null): string
    {
        $extension = pathinfo($originalName, PATHINFO_EXTENSION) ?: $this->extensionFromContentType($mimeType);
        $publicId = trim($folder, '/') . '/' . Str::of(pathinfo($originalName, PATHINFO_FILENAME))->slug('-') . '-' . Str::lower(Str::random(8));

        $response = Http::attach(
            'file',
            $contents,
            basename($publicId . '.' . $extension)
        )->timeout(30)->post($this->uploadEndpoint(), $this->signedParameters([
            'folder' => trim($folder, '/'),
            'public_id' => (string) $publicId,
        ]));

        if (! $response->successful() || blank($response->json('secure_url'))) {
            throw new RuntimeException('Cloudinary upload failed.');
        }

        return $response->json('secure_url');
    }

    private function extensionFromContentType(?string $contentType): string
    {
        return match (true) {
            str_contains((string) $contentType, 'png') => 'png',
            str_contains((string) $contentType, 'webp') => 'webp',
            str_contains((string) $contentType, 'gif') => 'gif',
            default => 'jpg',
        };
    }

    private function uploadEndpoint(): string
    {
        return 'https://api.cloudinary.com/v1_1/' . env('CLOUDINARY_CLOUD_NAME') . '/image/upload';
    }

    private function destroyEndpoint(): string
    {
        return 'https://api.cloudinary.com/v1_1/' . env('CLOUDINARY_CLOUD_NAME') . '/image/destroy';
    }

    private function signedParameters(array $parameters): array
    {
        $parameters['timestamp'] = time();
        ksort($parameters);

        $toSign = collect($parameters)
            ->filter(fn ($value) => $value !== null && $value !== '')
            ->map(fn ($value, $key) => $key . '=' . $value)
            ->implode('&');

        $parameters['api_key'] = env('CLOUDINARY_API_KEY');
        $parameters['signature'] = sha1($toSign . env('CLOUDINARY_API_SECRET'));

        return $parameters;
    }

    private function isCloudinaryUrl(string $url): bool
    {
        return str_contains($url, 'res.cloudinary.com');
    }

    private function extractPublicIdFromUrl(string $url): ?string
    {
        $path = parse_url($url, PHP_URL_PATH);

        if (! $path || ! str_contains($path, '/upload/')) {
            return null;
        }

        $afterUpload = explode('/upload/', $path, 2)[1] ?? null;

        if (! $afterUpload) {
            return null;
        }

        $segments = array_values(array_filter(explode('/', trim($afterUpload, '/'))));

        $versionIndex = null;
        foreach ($segments as $index => $segment) {
            if (preg_match('/^v\d+$/', $segment)) {
                $versionIndex = $index;
                break;
            }
        }

        if ($versionIndex !== null) {
            $segments = array_slice($segments, $versionIndex + 1);
        }

        if (empty($segments)) {
            return null;
        }

        $lastSegment = array_pop($segments);
        $lastSegment = pathinfo($lastSegment, PATHINFO_FILENAME);
        $segments[] = $lastSegment;

        return implode('/', $segments);
    }
}
