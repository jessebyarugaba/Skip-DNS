<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

$server = ""; // Replace with your new server IP
$host = ""; // Replace with your domain name

// Build the full URL to fetch
$request_uri = $_SERVER['REQUEST_URI'];
$full_url = "https://$server";

// Initialize cURL session
$ch = curl_init();

// Set the URL to fetch from the new server
curl_setopt($ch, CURLOPT_URL, $full_url);

// Set headers to include the host
$headers = [
    "Host: $host",
    "X-Forwarded-For: " . $_SERVER['REMOTE_ADDR'],
    "User-Agent: " . $_SERVER['HTTP_USER_AGENT']
];
curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

// Follow redirects
curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);

// Return the response instead of outputting
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

// Ignore SSL certificate verification
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);

// Execute the cURL request
$response = curl_exec($ch);

// Check for cURL errors
if (curl_errno($ch)) {
    echo 'cURL error: ' . curl_error($ch);
} else {
    // Output the response
    $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $content_type = curl_getinfo($ch, CURLINFO_CONTENT_TYPE);

    if ($http_code == 200) {
        header("Content-Type: $content_type");

        // Inject <base> tag into the HTML response
        if (strpos($content_type, 'text/html') !== false) {
            $base_tag = '<base href="https://' . $host . '">';
            $response = preg_replace('/<head([^>]*)>/i', '<head$1>' . $base_tag, $response, 1);
        }

        echo $response;
    } else {
        echo $response;
    }
}

// Close the cURL session
curl_close($ch);
?>
