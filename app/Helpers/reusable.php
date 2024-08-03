<?php

/**
 * Created by PhpStorm.
 * User: Raqibul Hasan Moon
 * Date: 20/07/2024
 * Time: 03:58 PM
 */

if (!function_exists('getIPAddress')) {
    function getIPAddress()
    {
        $ipKeys = [
            'HTTP_CF_CONNECTING_IP', // Cloudflare
            'HTTP_CLIENT_IP',
            'HTTP_X_FORWARDED_FOR',
            'HTTP_X_FORWARDED',
            'HTTP_X_CLUSTER_CLIENT_IP',
            'HTTP_FORWARDED_FOR',
            'HTTP_FORWARDED',
            'REMOTE_ADDR',
        ];

        foreach ($ipKeys as $key) {
            if (isset($_SERVER[$key])) {
                $ipAddresses = explode(',', $_SERVER[$key]);

                foreach ($ipAddresses as $ip) {
                    $ip = trim($ip);

                    if (validateIP($ip)) {
                        return $ip;
                    }
                }
            }
        }

        return request()->ip(); // Fallback to Laravel's default method
    }
}

if (!function_exists('validateIP')) {
    function validateIP($ip)
    {
        if (filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_IPV4 | FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE) === false) {
            return false;
        }
        return true;
    }
}

if (!function_exists('randomDigits')) {
    function randomDigits($len = 3): string
    {
        return str_pad(mt_rand(0, 999), $len, '0', STR_PAD_LEFT);
    }
}

if (!function_exists('openSSLEncryptDecrypt')) {
    function openSSLEncryptDecrypt($string, $action = 'encrypt')
    {
        $encrypt_method = 'AES-256-CBC';
        $secret_key = "!@#$@n@1iv1vr($%^&*)";
        $secret_iv = '^%$0n@l1v1vr%^';

        if (!in_array($action, ['encrypt', 'decrypt'])) {
            throw new InvalidArgumentException('Invalid action parameter');
        }

        $iv = $action == 'encrypt' ? openssl_random_pseudo_bytes(16) : substr(hash('sha256', $secret_iv), 0, 16);

        $key = hash('sha256', $secret_key);

        $output = null;

        if ($action == 'encrypt') {
            $output = openssl_encrypt($string, $encrypt_method, $key, 0, $iv);
            if ($output === false) {
                throw new RuntimeException('Encryption failed');
            }
            $output = base64_encode($iv . $output);
        } elseif ($action == 'decrypt') {
            $string = base64_decode($string);
            $iv = substr($string, 0, 16);
            $string = substr($string, 16);
            $output = openssl_decrypt($string, $encrypt_method, $key, 0, $iv);
            if ($output === false) {
                throw new RuntimeException('Decryption failed');
            }
        }

        return $output;
    }
}

if (!function_exists('decodeJsonIfValid')) {
    function decodeJsonIfValid($jsonString)
    {
        // Check if the string is a valid JSON
        if (is_string($jsonString) && !empty($jsonString) && isJson($jsonString)) {
            // Decode the JSON string
            $decodedData = json_decode($jsonString, true);

            // Check if the JSON decoding was successful
            if ($decodedData !== null && json_last_error() === JSON_ERROR_NONE) {
                return $decodedData;
            } else {
                // Error occurred while decoding JSON
                return null;
            }
        } else {
            // Not a valid JSON string
            // Return the original value
            return $jsonString;
        }
    }
}

if (!function_exists('isJson')) {

    function isJson($string)
    {
        json_decode($string);
        return (json_last_error() === JSON_ERROR_NONE);
    }
}

if (!function_exists('uploadMediaGetPath')) {

    function uploadMediaGetPath($media, $path = 'images/profile')
    {
        if (empty($media)) {
            return null;
        }

        // Extract the base64 part of the image
        $imageParts = explode(";base64,", $media);
        if (count($imageParts) !== 2) {
            return null; // Invalid base64 image
        }

        // Extract image type and decode the base64 image
        $imageType = explode("image/", $imageParts[0])[1] ?? null;
        if (!$imageType) {
            return null; // Invalid image type
        }

        $imageBase64 = base64_decode($imageParts[1]);
        if ($imageBase64 === false) {
            return null; // Failed to decode base64
        }

        // Generate a unique filename and save the image
        $fileNameToStore = uniqid() . '.' . $imageType;
        $filePath = public_path("uploads/" . $path . "/" . $fileNameToStore);

        if (file_put_contents($filePath, $imageBase64) === false) {
            return null; // Failed to save file
        }

        return $path . "/" . $fileNameToStore;
    }
}
