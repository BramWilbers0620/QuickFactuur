<?php

use App\Console\Commands\Traits\EncryptsBackups;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

describe('Backup Encryption', function () {
    beforeEach(function () {
        // Create a test class that uses the trait
        $this->encryptor = new class {
            use EncryptsBackups;

            public function testEncrypt(string $data): string
            {
                return $this->encryptData($data);
            }

            public function testDecrypt(string $data): string
            {
                return $this->decryptData($data);
            }

            public function testIsEnabled(): bool
            {
                return $this->isEncryptionEnabled();
            }
        };
    });

    it('encrypts and decrypts data correctly', function () {
        config(['backup.encryption_enabled' => true]);
        config(['backup.encryption_key' => base64_encode(random_bytes(32))]);

        $originalData = 'This is sensitive backup data that needs to be encrypted!';

        $encrypted = $this->encryptor->testEncrypt($originalData);
        $decrypted = $this->encryptor->testDecrypt($encrypted);

        expect($encrypted)->not->toBe($originalData);
        expect($decrypted)->toBe($originalData);
    });

    it('produces different ciphertext for same plaintext', function () {
        config(['backup.encryption_enabled' => true]);
        config(['backup.encryption_key' => base64_encode(random_bytes(32))]);

        $data = 'Test data';

        $encrypted1 = $this->encryptor->testEncrypt($data);
        $encrypted2 = $this->encryptor->testEncrypt($data);

        expect($encrypted1)->not->toBe($encrypted2);
    });

    it('returns false when encryption is disabled', function () {
        config(['backup.encryption_enabled' => false]);
        config(['backup.encryption_key' => null]);

        expect($this->encryptor->testIsEnabled())->toBeFalse();
    });

    it('returns false when key is not set', function () {
        config(['backup.encryption_enabled' => true]);
        config(['backup.encryption_key' => null]);

        expect($this->encryptor->testIsEnabled())->toBeFalse();
    });

    it('returns true when properly configured', function () {
        config(['backup.encryption_enabled' => true]);
        config(['backup.encryption_key' => base64_encode(random_bytes(32))]);

        expect($this->encryptor->testIsEnabled())->toBeTrue();
    });

    it('throws exception when decrypting corrupted data', function () {
        config(['backup.encryption_enabled' => true]);
        config(['backup.encryption_key' => base64_encode(random_bytes(32))]);

        $corruptedData = random_bytes(100);

        $this->encryptor->testDecrypt($corruptedData);
    })->throws(RuntimeException::class);

    it('handles large data efficiently', function () {
        config(['backup.encryption_enabled' => true]);
        config(['backup.encryption_key' => base64_encode(random_bytes(32))]);

        // Generate 1MB of test data
        $largeData = str_repeat('A', 1024 * 1024);

        $encrypted = $this->encryptor->testEncrypt($largeData);
        $decrypted = $this->encryptor->testDecrypt($encrypted);

        expect($decrypted)->toBe($largeData);
    });
});
