<?php

/**
 * Configuration file for Raintree API Client
 */

// Load environment variables from .env file
$envFile = __DIR__ . '/../.env';
if (!file_exists($envFile)) {
    throw new Exception(".env file not found");
}

// Processing the .env file
foreach (file($envFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES) as $line) {
    if (strpos(trim($line), '#') === 0 || strpos($line, '=') === false) continue;
    list($key, $value) = explode('=', $line, 2);
    $_ENV[trim($key)] = trim($value);
}

// Define constants from environment variables
define('API_BASE_URL', $_ENV['API_BASE_URL'] ?? '');
define('CLIENT_ID', $_ENV['CLIENT_ID'] ?? '');
define('CLIENT_SECRET', $_ENV['CLIENT_SECRET'] ?? '');
define('APP_ID', $_ENV['APP_ID'] ?? '');

// Default search criteria
$defaultSearchCriteria = [
    'first_name' => 'Patricia',
    'last_name' => 'Doe',
    'date_of_birth' => '1955-03-02',
    'email' => 'patricia@doemail.com'
];
