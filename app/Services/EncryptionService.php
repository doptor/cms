<?php

namespace App\Services;

use Illuminate\Support\Facades\Log;

class EncryptionService
{
    private string $key;

    public function __construct()
    {
        // Derive a 32-byte key from APP_KEY for AES-256
        $this->key = substr(hash('sha256', config('app.key')), 0, 32);
    }

    /**
     * Encrypt a string (API key) using AES-256-CBC.
     */
    public function encrypt(string $plaintext): string
    {
        $iv        = random_bytes(16);
        $encrypted = openssl_encrypt($plaintext, 'AES-256-CBC', $this->key, OPENSSL_RAW_DATA, $iv);
        $hmac      = hash_hmac('sha256', $encrypted, $this->key, true);

        return base64_encode($hmac . $iv . $encrypted);
    }

    /**
     * Decrypt a stored encrypted API key.
     * Returns null if tampered or invalid.
     */
    public function decrypt(string $ciphertext): ?string
    {
        try {
            $decoded   = base64_decode($ciphertext);
            $hmac      = substr($decoded, 0, 32);
            $iv        = substr($decoded, 32, 16);
            $encrypted = substr($decoded, 48);

            // Verify HMAC integrity
            $expectedHmac = hash_hmac('sha256', $encrypted, $this->key, true);
            if (!hash_equals($hmac, $expectedHmac)) {
                Log::warning('API key decryption HMAC mismatch — possible tampering.');
                return null;
            }

            return openssl_decrypt($encrypted, 'AES-256-CBC', $this->key, OPENSSL_RAW_DATA, $iv);
        } catch (\Exception $e) {
            Log::error('Decryption error: ' . $e->getMessage());
            return null;
        }
    }

    /**
     * Mask an API key for display (e.g. sk-ant-api03-••••••••1234)
     */
    public function mask(string $key): string
    {
        if (strlen($key) < 8) return str_repeat('•', strlen($key));
        $prefix = substr($key, 0, 12);
        $suffix = substr($key, -4);
        return $prefix . str_repeat('•', 10) . $suffix;
    }
}
