<?php

namespace App\Services;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class LogoService
{
    private const ALLOWED_MIME_TYPES = ['image/png', 'image/jpeg'];

    /**
     * Process and store an uploaded logo file.
     *
     * @param UploadedFile $file The uploaded logo file
     * @param int $userId The user ID for organizing storage
     * @return array{logo_data: string|null, logo_path: string|null, warning: string|null}
     */
    public function processLogo(UploadedFile $file, int $userId): array
    {
        $result = [
            'logo_data' => null,
            'logo_path' => null,
            'warning' => null,
        ];

        try {
            // Validate actual MIME type (not client-provided extension)
            $actualMimeType = $file->getMimeType();

            if (!in_array($actualMimeType, self::ALLOWED_MIME_TYPES)) {
                $result['warning'] = 'Logo heeft een ongeldig bestandstype. Alleen PNG en JPG zijn toegestaan.';
                Log::warning('Logo rejected: invalid MIME type', ['mime' => $actualMimeType]);
                return $result;
            }

            // Determine extension from actual MIME type
            $extension = $actualMimeType === 'image/png' ? 'png' : 'jpg';

            // Generate safe filename
            $logoFileName = 'logo-' . time() . '-' . bin2hex(random_bytes(4)) . '.' . $extension;
            $logoPath = 'logos/' . $userId . '/' . $logoFileName;

            // Store the file
            Storage::disk('local')->put($logoPath, file_get_contents($file->getRealPath()));

            // Convert to base64 for PDF generation
            $imageData = file_get_contents($file->getRealPath());
            $logoData = 'data:' . $actualMimeType . ';base64,' . base64_encode($imageData);

            $result['logo_data'] = $logoData;
            $result['logo_path'] = $logoPath;

            Log::info('Logo processed successfully', ['path' => $logoPath, 'user_id' => $userId]);

        } catch (\Exception $e) {
            $result['warning'] = 'Logo kon niet worden verwerkt.';
            Log::warning('Logo processing failed', ['error' => $e->getMessage()]);
        }

        return $result;
    }

    /**
     * Delete a stored logo file.
     *
     * @param string $logoPath The path to the logo file
     * @return bool
     */
    public function deleteLogo(string $logoPath): bool
    {
        if (Storage::disk('local')->exists($logoPath)) {
            return Storage::disk('local')->delete($logoPath);
        }

        return false;
    }

    /**
     * Get logo data as base64 string from storage.
     *
     * @param string $logoPath The path to the logo file
     * @return string|null
     */
    public function getLogoBase64(string $logoPath): ?string
    {
        if (!Storage::disk('local')->exists($logoPath)) {
            return null;
        }

        $content = Storage::disk('local')->get($logoPath);
        $mimeType = Storage::disk('local')->mimeType($logoPath);

        return 'data:' . $mimeType . ';base64,' . base64_encode($content);
    }
}
