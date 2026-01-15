<?php

namespace App\Console\Commands\Traits;

use Illuminate\Support\Facades\Log;

trait EncryptsBackups
{
    /**
     * Check if backup encryption is enabled.
     */
    protected function isEncryptionEnabled(): bool
    {
        return config('backup.encryption_enabled', false)
            && !empty(config('backup.encryption_key'));
    }

    /**
     * Encrypt file contents using AES-256-GCM.
     *
     * @param string $data The data to encrypt
     * @return string The encrypted data with IV and tag prepended
     * @throws \RuntimeException If encryption fails
     */
    protected function encryptData(string $data): string
    {
        $key = $this->getEncryptionKey();
        $cipher = 'aes-256-gcm';

        // Generate random IV
        $ivLength = openssl_cipher_iv_length($cipher);
        $iv = random_bytes($ivLength);

        // Encrypt with authentication tag
        $tag = '';
        $encrypted = openssl_encrypt(
            $data,
            $cipher,
            $key,
            OPENSSL_RAW_DATA,
            $iv,
            $tag,
            '',
            16 // Tag length
        );

        if ($encrypted === false) {
            throw new \RuntimeException('Encryption failed: ' . openssl_error_string());
        }

        // Prepend IV and tag to encrypted data
        // Format: [16 bytes IV][16 bytes tag][encrypted data]
        return $iv . $tag . $encrypted;
    }

    /**
     * Decrypt file contents.
     *
     * @param string $encryptedData The encrypted data with IV and tag
     * @return string The decrypted data
     * @throws \RuntimeException If decryption fails
     */
    protected function decryptData(string $encryptedData): string
    {
        $key = $this->getEncryptionKey();
        $cipher = 'aes-256-gcm';

        $ivLength = openssl_cipher_iv_length($cipher);

        // Extract IV, tag, and encrypted data
        $iv = substr($encryptedData, 0, $ivLength);
        $tag = substr($encryptedData, $ivLength, 16);
        $encrypted = substr($encryptedData, $ivLength + 16);

        if (strlen($iv) !== $ivLength || strlen($tag) !== 16) {
            throw new \RuntimeException('Invalid encrypted data format');
        }

        $decrypted = openssl_decrypt(
            $encrypted,
            $cipher,
            $key,
            OPENSSL_RAW_DATA,
            $iv,
            $tag
        );

        if ($decrypted === false) {
            throw new \RuntimeException('Decryption failed - data may be corrupted or key is invalid');
        }

        return $decrypted;
    }

    /**
     * Get the encryption key from config.
     *
     * @return string The binary encryption key
     * @throws \RuntimeException If key is not configured
     */
    protected function getEncryptionKey(): string
    {
        $key = config('backup.encryption_key');

        if (empty($key)) {
            throw new \RuntimeException(
                'Backup encryption key not configured. Set BACKUP_ENCRYPTION_KEY in .env'
            );
        }

        // Decode from base64
        $decoded = base64_decode($key, true);

        if ($decoded === false || strlen($decoded) !== 32) {
            // If not valid base64 or wrong length, derive key from the string
            $decoded = hash('sha256', $key, true);
        }

        return $decoded;
    }

    /**
     * Get the encrypted file extension.
     */
    protected function getEncryptedExtension(): string
    {
        return '.enc';
    }
}
