<?php
declare(strict_types=1);

/**
 * Encryption — AES-256-GCM symmetric encryption helper.
 *
 * Each encrypt() call uses a unique 12-byte random IV, making every
 * ciphertext unique even for identical plaintexts. The GCM tag (16 bytes)
 * is appended to the ciphertext before base64-encoding, providing
 * authenticated encryption (integrity + confidentiality).
 *
 * Key is loaded from ENCRYPTION_KEY constant (64 hex chars = 32 bytes).
 */
class Encryption
{
    private static string $key;
    private const CIPHER     = 'aes-256-gcm';
    private const TAG_LENGTH = 16;  // bytes
    private const IV_LENGTH  = 12;  // bytes (96-bit for GCM)

    public static function init(): void
    {
        $keyHex = ENCRYPTION_KEY;

        if (strlen($keyHex) !== 64) {
            // Normalize to 32 bytes via SHA-256 if key is wrong length
            error_log('[Encryption] WARNING: ENCRYPTION_KEY should be 64 hex chars. Using SHA-256 fallback.');
            $keyHex = hash('sha256', $keyHex);
        }

        self::$key = hex2bin($keyHex);
    }

    /**
     * Encrypt plaintext. Returns ['ciphertext' => base64, 'iv' => base64].
     *
     * @throws RuntimeException on failure
     */
    public static function encrypt(string $plaintext): array
    {
        $iv  = random_bytes(self::IV_LENGTH);
        $tag = '';

        $ciphertext = openssl_encrypt(
            $plaintext,
            self::CIPHER,
            self::$key,
            OPENSSL_RAW_DATA,
            $iv,
            $tag,
            '',
            self::TAG_LENGTH
        );

        if ($ciphertext === false) {
            throw new RuntimeException('Encryption failed: ' . openssl_error_string());
        }

        return [
            'ciphertext' => base64_encode($ciphertext . $tag),
            'iv'         => base64_encode($iv),
        ];
    }

    /**
     * Decrypt base64-encoded ciphertext using base64-encoded IV.
     * Returns empty string on failure (never throws).
     */
    public static function decrypt(string $ciphertextB64, string $ivB64): string
    {
        $raw = base64_decode($ciphertextB64, true);
        $iv  = base64_decode($ivB64, true);

        if ($raw === false || $iv === false || strlen($raw) <= self::TAG_LENGTH) {
            return '';
        }

        $ciphertext = substr($raw, 0, -self::TAG_LENGTH);
        $tag        = substr($raw, -self::TAG_LENGTH);

        $plaintext = openssl_decrypt(
            $ciphertext,
            self::CIPHER,
            self::$key,
            OPENSSL_RAW_DATA,
            $iv,
            $tag
        );

        return $plaintext === false ? '' : $plaintext;
    }
}

Encryption::init();
