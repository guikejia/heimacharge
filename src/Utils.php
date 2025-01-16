<?php

declare(strict_types=1);

namespace Guikejia\HeiMaCharge;

use Guikejia\HeiMaCharge\Exceptions\Exception;
use Random\RandomException;

class Utils
{
    public function __construct(
        protected Config $config
    ) {}

    public function encryptedData($data, $iv): string
    {
        $cipher = 'AES-256-GCM';
        $ciphertext = openssl_encrypt(
            $data,
            $cipher,
            $this->config->getClientSecret(),
            OPENSSL_RAW_DATA,
            $iv,
            $tag
        );

        if ($ciphertext === false) {
            throw new Exception('Encryption failed: ' . openssl_error_string());
        }

        return base64_encode($ciphertext . $tag);
    }

    public function decryptedData(string $encryptedData, string $iv): string
    {
        $publicKey = $this->config->getClientSecret();
        $tagLength = 16;  // AES-256-GCM 的 tag 长度通常为 16 字节

        // 先将 Base64 解码
        $encryptedData = base64_decode($encryptedData);

        // 分离加密数据和 tag
        $encryptedDataWithoutTag = substr($encryptedData, 0, -$tagLength);
        $tag = substr($encryptedData, -$tagLength);

        $decrypted = openssl_decrypt($encryptedDataWithoutTag, 'aes-256-gcm', $publicKey, OPENSSL_RAW_DATA, $iv, $tag);

        if ($decrypted === false) {
            throw new Exception('Decryption failed');
        }

        return $decrypted;
    }

    public function getNonce($length = 16): string
    {
        $str = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz1234567890';
        $randStr = str_shuffle($str);
        return substr($randStr, 0, $length);
    }

    public function getSignatureData($method, $requestPath, $timestamp, $nonce, $encryptedRequestData): string
    {
        echo '---'.implode('', [$method, $requestPath, $timestamp, $nonce, $encryptedRequestData]);
        return implode('', [$method, $requestPath, $timestamp, $nonce, $encryptedRequestData]);
    }

    /**
     * @param mixed $data
     * @throws Exception
     */
    public function genSignature($data): string
    {
        $hash = md5($data);

        $privateKey = $this->config->getPrivateKey();
        $privateKeyResource = openssl_pkey_get_private($privateKey);
        if (! $privateKeyResource) {
            throw new Exception('Invalid private key.');
        }

        $signature = '';
        $success = openssl_sign(hex2bin($hash), $signature, $privateKeyResource, OPENSSL_ALGO_SHA256);

        if (! $success) {
            throw new Exception('Failed to generate the digital signature.');
        }

        return base64_encode($signature);
    }

    public function verifySignature($signContent = null, $signatureStr = '')
    {
        $public_key = $this->config->getPublicKey();
        $key = openssl_get_publickey($public_key);
        return openssl_verify($signContent, base64_decode($signatureStr), $key, OPENSSL_ALGO_SHA256);
    }

    /**
     * AES-GCM encryption and decryption.
     *
     * @param string $plaintext Plaintext to encrypt
     * @param string &$tag Reference to hold the authentication tag
     * @return string Encrypted ciphertext
     * @throws RandomException
     * @throws Exception
     */
    public function AesGcmEncrypt(string $plaintext, string $iv, string &$tag): string
    {
        $cipher = 'AES-256-GCM';
        $ivLen = openssl_cipher_iv_length($cipher);
        //        $iv = random_bytes(12); // GCM recommended IV length is 12 bytes
        $ciphertext = openssl_encrypt(
            $plaintext,
            $cipher,
            $this->config->getHeiMaPublicKey(),
            OPENSSL_RAW_DATA,
            $iv,
            $tag
        );

        if ($ciphertext === false) {
            throw new Exception('Encryption failed: ' . openssl_error_string());
        }

        return $ciphertext; // Prepend IV to the ciphertext
    }

    /**
     * AES-GCM decryption.
     *
     * @param string $ciphertext Ciphertext to decrypt
     * @return string Decrypted plaintext
     * @throws Exception
     */
    public function AesGcmDecrypt(string $ciphertext): string
    {
        $iv = substr($ciphertext, 0, 12); // Extract the IV
        $ciphertext_raw = substr($ciphertext, 12);

        $plaintext = openssl_decrypt(
            $ciphertext_raw,
            'aes-256-gcm',
            $this->config->getPrivateKey(),
            OPENSSL_RAW_DATA,
            $iv,
        );

        if ($plaintext === false) {
            throw new Exception('Decryption failed: ' . openssl_error_string());
        }

        return $plaintext;
    }

    public function verifySignatureWithBH(string $sign, string $data, string $requestPath, int $timestamp, string $nonce, string $method): bool
    {
        $before_md5 = sprintf('%s%s%s%s%s', $method, $requestPath, $timestamp, $nonce, $data);
        $after_md5 = md5($before_md5);
        $hex2bin_md5 = hex2bin($after_md5);
        $public_key = $this->config->getHeiMaPublicKey();
        $key = openssl_get_publickey($public_key);
        return (bool) openssl_verify($hex2bin_md5, base64_decode($sign), $key, OPENSSL_ALGO_SHA256);
    }


    public function getToken()
    {

    }
}
