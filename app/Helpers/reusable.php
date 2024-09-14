<?php

/**
 * Created by PhpStorm.
 * User: Raqibul Hasan Moon
 * Date: 20/07/2024
 * Time: 03:58 PM
 */

use Carbon\Carbon;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

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

if (!function_exists('generateTicketUuid')) {

    function generateTicketUuid()
    {
        return 'NGD' . date('ymdhis') . str_pad(rand(11111, 99999), 4, '0', STR_PAD_LEFT);
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

if (!function_exists("userCaseWord")) {
    function userCaseWord($str)
    {
        return ucwords(\Illuminate\Support\Str::of($str)->replace(['-', '_'], ' '));

    }
}

if (!function_exists("sendValidationErrorResponse")) {

    function sendValidationErrorResponse($validator)
    {
        $message = "";
        foreach ($validator->errors()->getMessages() ?? [] as $value) {
            $message .= ' ' . $value[0];
        }

        $error = [
            'title' => 'Validation Error',
            'message' => $message,
        ];

        return sendErrorResponse($error);
    }

}

if (!function_exists("sendErrorResponse")) {
    function sendErrorResponse($error, $code = 404)
    {
        $response = [
            'errors' => $error,
        ];
        return response()->json($response, $code);
    }
}

if (!function_exists("formatTime")) {
    function formatTime($time)
    {
        $time = Carbon::parse($time);
        return [
            'formattedTime' => $time->format('h:i'),
            'suffix' => $time->format('a'),
        ];
    }

}

if (!function_exists("getLocationName")) {
    function getLocationName($lat, $lon)
    {

        ini_set('max_execution_time', 60);
        // Validate latitude and longitude
        if (!is_numeric($lat) || !is_numeric($lon)) {
            Log::error('Invalid latitude or longitude value.');
            return [
                'location' => 'Unknown',
                'city_country' => 'Unknown',
            ];
        }

        $cacheKey = "location-{$lat}-{$lon}";

        // Retrieve from cache if available
        $cachedData = Cache::get($cacheKey);
        if ($cachedData) {
            return $cachedData;
        }

        $url = "https://nominatim.openstreetmap.org/reverse?format=json&lat={$lat}&lon={$lon}&addressdetails=1";
        $attempts = 3;

        while ($attempts > 0) {
            try {
                $response = Http::withHeaders([
                    'Accept-Language' => 'en',
                    'User-Agent' => 'NagadWeb/1.0 (contact@nagad.com.bd)',
                ])->get($url);

                $logMessage = sprintf(
                    "GET-LOCATION-NAME|Response Status: %d | Response Headers: %s | Response Body: %s | %s",
                    $response->status(),
                    json_encode($response->headers()),
                    $response->body(),
                    'Response Data: ' . json_encode($response->json())
                );

                Log::info($logMessage);

                $data = $response->json();

                if (isset($data['address'])) {
                    $city = $data['address']['city'] ?? '';
                    $country = $data['address']['country'] ?? '';
                    $quarter = $data['address']['quarter'] ?? '';
                    $suburb = $data['address']['suburb'] ?? '';
                    $result = [
                        'location' => $quarter . ', ' . $suburb,
                        'city_country' => $city . ', ' . $country,
                    ];
                } else {
                    $result = [
                        'location' => 'Unknown',
                        'city_country' => 'Unknown',
                    ];
                }

                // Store result in cache
                Cache::put($cacheKey, $result, now()->addMinutes(120));

                return $result;

            } catch (\Exception $e) {
                Log::error('Error fetching location name: ' . $e->getMessage());
                $attempts--;
                if ($attempts === 0) {
                    return [
                        'location' => 'Unknown',
                        'city_country' => 'Unknown',
                    ];
                }
                sleep(1); // Wait before retrying
            }
        }
    }
}

/* if (!function_exists("getLocationName")) {

function getLocationName($lat, $lon)
{
// Validate latitude and longitude
if (!is_numeric($lat) || !is_numeric($lon)) {
Log::error('Invalid latitude or longitude value.');
return [
'location' => 'Unknown',
'city_country' => 'Unknown',
];
}

$url = "https://nominatim.openstreetmap.org/reverse?format=json&lat={$lat}&lon={$lon}&addressdetails=1";

try {
$response = Http::withHeaders([
'Accept-Language' => 'en',
'User-Agent' => 'NagadWeb/1.0 (contact@nagad.com.bd)',
])->get($url);

$logMessage = sprintf(
"GET-LOCATION-NAME|Response Status: %d | Response Headers: %s | Response Body: %s | %s",
$response->status(),
json_encode($response->headers()),
$response->body(),
'Response Data: ' . json_encode($response->json())
);

Log::info($logMessage);

$data = $response->json();

if (isset($data['address'])) {
$city = $data['address']['city'] ?? '';
$country = $data['address']['country'] ?? '';
$quarter = $data['address']['quarter'] ?? '';
$suburb = $data['address']['suburb'] ?? '';
return [
'location' => $quarter . ', ' . $suburb,
'city_country' => $city . ', ' . $country,
];
} else {
return [
'location' => 'Unknown',
'city_country' => "Unknown",
];
}
} catch (\Exception $e) {
Log::error('Error fetching location name: ' . $e->getMessage());
return [
'location' => 'Unknown',
'city_country' => "Unknown",
];
// return 'Error fetching location';
}
}
} */

/**
 * Helper method to get the device icon class based on the device name.
 */

if (!function_exists("getDeviceIcon")) {
    function getDeviceIcon($deviceName)
    {
        if (str_contains($deviceName, 'Windows') || str_contains($deviceName, 'Linux') || str_contains($deviceName, 'Mac')) {
            return 'icon-laptop';
        } elseif (str_contains($deviceName, 'iPhone') || str_contains($deviceName, 'Android')) {
            return 'icon-smartphone';
        } elseif (str_contains($deviceName, 'iPad') || str_contains($deviceName, 'Tablet')) {
            return 'icon-tablet';
        } else {
            return 'icon-laptop';
        }
    }

}
