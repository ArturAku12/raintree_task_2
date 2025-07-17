<?php

/**
 * Raintree API Demo
 * This script demonstrates how to authenticate and retrieve patient data from the Raintree API
 */

// Load configuration
require_once __DIR__ . '/config/config.php';

/**
 * Execute a cURL request and return the response
 * 
 * @param CurlHandle $ch cURL handle
 * @param string $operation Description of the operation for error messages
 * @return string|false Response body or false on error
 */
function executeCurlRequest($ch, $operation)
{
    $response = curl_exec($ch);

    if (curl_errno($ch)) {
        echo "cURL error during $operation: " . curl_error($ch) . PHP_EOL;
        return false;
    }

    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    if ($httpCode >= 400) {
        echo "HTTP error during $operation: $httpCode" . PHP_EOL;
        echo "Response: $response" . PHP_EOL;
        return false;
    }

    echo "$operation Response: $response" . PHP_EOL;
    return $response;
}

/**
 * Get access token from Raintree API
 * 
 * @return string|null Access token or null on failure
 */
function getAccessToken()
{
    $tokenUrl = API_BASE_URL . '/token';

    $tokenData = [
        'client_id' => CLIENT_ID,
        'client_secret' => CLIENT_SECRET,
        'grant_type' => 'client_credentials'
    ];

    $ch = curl_init($tokenUrl);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($tokenData));
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'Content-Type: application/x-www-form-urlencoded',
        'AppId: ' . APP_ID
    ]);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

    $response = executeCurlRequest($ch, 'Token Request');
    curl_close($ch);

    if ($response === false) {
        return null;
    }

    $tokenData = json_decode($response, true);
    return $tokenData['access_token'] ?? null;
}

/**
 * Search for patients by criteria
 * 
 * @param string $accessToken API access token
 * @param array $searchCriteria Search parameters
 * @return array|null Patient records or null on failure
 */
function searchPatients($accessToken, $searchCriteria)
{
    $queryString = http_build_query($searchCriteria);
    $patientsUrl = API_BASE_URL . '/patients?' . $queryString;

    $ch = curl_init($patientsUrl);
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'Authorization: Bearer ' . $accessToken,
        'Content-Type: application/json'
    ]);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

    $response = executeCurlRequest($ch, 'Patients Search');
    curl_close($ch);

    if ($response === false) {
        return null;
    }

    $patientData = json_decode($response, true);
    return $patientData['records'] ?? [];
}

/**
 * Get detailed patient information by patient number
 * 
 * @param string $accessToken API access token
 * @param string $patientNumber Patient number
 * @return array|null Patient details or null on failure
 */
function getPatientDetails($accessToken, $patientNumber)
{
    $patientDetailsUrl = API_BASE_URL . '/patients/' . $patientNumber;

    $ch = curl_init($patientDetailsUrl);
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'Authorization: Bearer ' . $accessToken,
        'Content-Type: application/json'
    ]);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

    $response = executeCurlRequest($ch, 'Patient Details');
    curl_close($ch);

    if ($response === false) {
        return null;
    }

    return json_decode($response, true);
}

// Main execution
echo "Raintree API Demo" . PHP_EOL . PHP_EOL;

// Step 1: Get access token
echo "Step 1: Authentication" . PHP_EOL;
$accessToken = getAccessToken();

if (!$accessToken) {
    echo "Failed to obtain access token. Exiting." . PHP_EOL;
    exit(1);
}

echo "Authentication successful!" . PHP_EOL . PHP_EOL;

// Step 2: Search for patients
echo "Step 2: Searching for patients..." . PHP_EOL;

$patients = searchPatients($accessToken, $defaultSearchCriteria);

if (!$patients || empty($patients)) {
    echo "No patients found with the specified criteria." . PHP_EOL;
    exit(0);
}

echo "Found " . count($patients) . " patient(s)." . PHP_EOL . PHP_EOL;

// Step 3: Get detailed information for the first patient
echo "Step 3: Getting detailed patient information..." . PHP_EOL;
$firstPatient = $patients[0];
$patientNumber = $firstPatient['pn'] ?? '';

if (!$patientNumber) {
    echo "Patient number not found in search results." . PHP_EOL;
    exit(1);
}

$patientDetails = getPatientDetails($accessToken, $patientNumber);

if (!$patientDetails) {
    echo "Failed to retrieve patient details." . PHP_EOL;
    exit(1);
}

echo "Patient details retrieved successfully!" . PHP_EOL;
