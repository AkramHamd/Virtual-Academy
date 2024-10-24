<?php
use PHPUnit\Framework\TestCase;

class AuthTest extends TestCase {

    private $baseUrl = 'http://localhost/virtual-academy/backend/api/auth/';
    private $cookieFile;

    // Setup function: Called before each test
    protected function setUp(): void {
        // Use a temporary file for cookies during each test
        $this->cookieFile = tempnam(sys_get_temp_dir(), 'cookie');
    }

    // Teardown function: Called after each test
    protected function tearDown(): void {
        // Remove the temporary cookie file after the test is done
        if (file_exists($this->cookieFile)) {
            unlink($this->cookieFile);
        }
    }

    // Test User Registration
    public function testUserRegistration() {
        $data = json_encode([
            'name' => 'Jane Doe',
            'email' => 'jane@example.com',
            'password' => 'password123'
        ]);

        $response = $this->makeHttpRequest('POST', $this->baseUrl . 'register.php', $data);

        // Check if the response is null
        if ($response === null) {
            $this->fail("Failed to get a valid response from the API.");
        }

        // Print the raw response for debugging
        print_r($response);

        // Assert response contains the 'message' property
        $this->assertTrue(property_exists($response, 'message'), "Response does not contain 'message'.");
        $this->assertEquals('User registered successfully.', $response->message);
    }

    // Test User Login
    public function testUserLogin() {
        $data = json_encode([
            'email' => 'jane@example.com',
            'password' => 'password123'
        ]);

        $response = $this->makeHttpRequest('POST', $this->baseUrl . 'login.php', $data);

        // Check if the response is null
        if ($response === null) {
            $this->fail("Failed to get a valid response from the API.");
        }

        // Print the raw response for debugging
        print_r($response);

        // Assert response contains the 'message' property
        $this->assertTrue(property_exists($response, 'message'), "Response does not contain 'message'.");
        $this->assertEquals('Login successful.', $response->message);
    }

    // Test User Logout
    public function testUserLogout() {
        $response = $this->makeHttpRequest('POST', $this->baseUrl . 'logout.php');

        // Check if the response is null
        if ($response === null) {
            $this->fail("Failed to get a valid response from the API.");
        }

        // Print the raw response for debugging
        print_r($response);

        // Assert response contains the 'message' property
        $this->assertTrue(property_exists($response, 'message'), "Response does not contain 'message'.");
        $this->assertEquals('Logged out successfully.', $response->message);
    }

    // Test Accessing Protected Route without Login
    public function testAccessProtectedRouteWithoutLogin() {
        // Don't send the cookie file to simulate logged-out behavior
        $response = $this->makeHttpRequest('GET', $this->baseUrl . 'user.php', null, false);

        // Check if the response is null
        if ($response === null) {
            $this->fail("Failed to get a valid response from the API.");
        }

        // Print the raw response for debugging
        print_r($response);

        // Assert response contains the 'message' property
        $this->assertTrue(property_exists($response, 'message'), "Response does not contain 'message'.");
        $this->assertEquals('Access denied. User is not logged in.', $response->message);
    }


    private function extractJsonFromResponse($response) {
        // Find the first '{' which indicates the start of JSON and the last '}' to get valid JSON
        $jsonStart = strpos($response, '{');
        $jsonEnd = strrpos($response, '}');
        
        if ($jsonStart !== false && $jsonEnd !== false) {
            // Return only the portion that is valid JSON
            return substr($response, $jsonStart, $jsonEnd - $jsonStart + 1);
        }
        
        // If no valid JSON found, return the original response for debugging
        return $response;
    }
    

    private function makeHttpRequest($method, $url, $data = null, $useCookies = true) {
        $ch = curl_init();
    
        if ($method === 'POST') {
            curl_setopt($ch, CURLOPT_POST, 1);
            if ($data) {
                curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
            }
        }
    
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
    
        // Handle cookies
        if ($useCookies) {
            curl_setopt($ch, CURLOPT_COOKIEJAR, $this->cookieFile);  // Save cookies
            curl_setopt($ch, CURLOPT_COOKIEFILE, $this->cookieFile);  // Use saved cookies
        }
    
        $response = curl_exec($ch);
    
        if (curl_errno($ch)) {
            echo 'cURL error: ' . curl_error($ch);
            return null;
        }
    
        $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        echo "HTTP Code: $http_code\n";
    
        curl_close($ch);
    
        // Clean the response and return only the valid JSON part
        $cleanedResponse = $this->extractJsonFromResponse($response);
    
        // If response is not valid JSON, return it as a string for debugging
        if ($this->isValidJson($cleanedResponse)) {
            return json_decode($cleanedResponse);
        } else {
            echo "Raw Response: $response\n";  // Print raw response for debugging
            return null;
        }
    }
    

    // Helper function to check if a string is valid JSON
    private function isValidJson($string) {
        json_decode($string);
        return (json_last_error() == JSON_ERROR_NONE);
    }
}
