<?php

declare(strict_types=1);

namespace Guikejia\HeiMaCharge;

use Guikejia\HeiMaCharge\Exceptions\Exception;
use Random\RandomException;

class Authentication
{
    public function __construct(
        protected Config $config
    ) {}

    public function encryptedData($data): string
    {
        $tag = '';
        $encrypted = $this->AesGcmEncrypt($data, $tag);
        return base64_encode($encrypted);
    }

    public function getNonce($length = 16): string
    {
        $str = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz1234567890';
        $randStr = str_shuffle($str);//打乱字符串
        $rands= substr($randStr,0,$length);//substr(string,start,length);返回字符串的一部分
        return $rands;
    }

    /**
     * @throws Exception
     */
    public function genSignature($method, $requestPath, $timestamp, $nonce, $encryptedRequestData): string
    {
        // Step 1: Concatenate the input data
        $data = implode('', [$method, $requestPath, $timestamp, $nonce, $encryptedRequestData]);

        // Step 2: Compute SHA-256 hash of the concatenated string
        $hash = hash('sha256', $data, true); // Generate raw binary hash

        // Step 3: Sign the hash using the RSA private key
        $privateKey = $this->config->getPrivateKey();
        $privateKeyResource = openssl_pkey_get_private($privateKey);
        if (!$privateKeyResource) {
            throw new Exception("Invalid private key.");
        }

        $signature = '';
        $success = openssl_sign($hash, $signature, $privateKeyResource, OPENSSL_ALGO_SHA256);

        if (!$success) {
            throw new Exception("Failed to generate the digital signature.");
        }

        // Step 4: Encode the signature in Base64
        return base64_encode($signature);
    }

    /**
     * AES-GCM encryption and decryption
     *
     * @param string $key Encryption key (16/24/32 bytes for AES-128/192/256)
     * @param string $plaintext Plaintext to encrypt
     * @param string $aad Additional authenticated data
     * @param string &$tag Reference to hold the authentication tag
     * @return string            Encrypted ciphertext
     * @throws RandomException
     * @throws Exception
     */
    function AesGcmEncrypt(string $plaintext, string &$tag, string $aad = ''): string
    {
        $iv = random_bytes(12); // GCM recommended IV length is 12 bytes
        $ciphertext = openssl_encrypt(
            $plaintext,
            'aes-256-gcm',
            $this->config->getPrivateKey(),
            OPENSSL_RAW_DATA,
            $iv,
            $tag,
            $aad
        );

        if ($ciphertext === false) {
            throw new Exception('Encryption failed: ' . openssl_error_string());
        }

        return $iv . $ciphertext; // Prepend IV to the ciphertext
    }

    /**
     * AES-GCM decryption
     *
     * @param string $key Encryption key (16/24/32 bytes for AES-128/192/256)
     * @param string $ciphertext Ciphertext to decrypt
     * @param string $aad Additional authenticated data
     * @param string $tag Authentication tag
     * @return string            Decrypted plaintext
     * @throws Exception
     */
    function AesGcmDecrypt(string $ciphertext, string $tag, string $aad = ''): string
    {
        $iv = substr($ciphertext, 0, 12); // Extract the IV
        $ciphertext_raw = substr($ciphertext, 12);

        $plaintext = openssl_decrypt(
            $ciphertext_raw,
            'aes-256-gcm',
            $this->config->getHeiMaPublicKey(),
            OPENSSL_RAW_DATA,
            $iv,
            $tag,
            $aad
        );

        if ($plaintext === false) {
            throw new Exception('Decryption failed: ' . openssl_error_string());
        }

        return $plaintext;
    }
}
