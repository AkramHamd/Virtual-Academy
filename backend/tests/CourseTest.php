<?php

use PHPUnit\Framework\TestCase;

class CourseTest extends TestCase {

    private $baseUrl = 'http://localhost/virtual-academy/backend/api/courses/';
    private $cookieFile = './tmp/admin_test_cookie.txt';  // Persistent cookie file
    private $createdCourseId;  // Store ID of created course for subsequent tests

    protected function setUp(): void {
        if (file_exists($this->cookieFile)) {
            unlink($this->cookieFile);
        }

        $loginSuccess = $this->adminLogin();
        $this->assertTrue($loginSuccess, "Failed to log in as admin.");
        $this->assertTrue(file_exists($this->cookieFile) && filesize($this->cookieFile) > 0, "Cookie file not created or empty after login.");
    }

    private function adminLogin() {
        $loginUrl = 'http://localhost/virtual-academy/backend/api/auth/login.php';
        $loginData = json_encode([
            'email' => 'admin@example.com',
            'password' => 'adminPassword123'
        ]);

        $response = $this->makeHttpRequest('POST', $loginUrl, $loginData, true);
        echo "Login Response: ";
        print_r($response);

        return ($response && isset($response->message) && $response->message === 'Login successful.');
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

        if ($useCookies) {
            curl_setopt($ch, CURLOPT_COOKIEJAR, $this->cookieFile);
            curl_setopt($ch, CURLOPT_COOKIEFILE, $this->cookieFile);
        }

        $response = curl_exec($ch);

        if (curl_errno($ch)) {
            echo 'cURL error: ' . curl_error($ch);
            return null;
        }

        $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        echo "HTTP Code: $http_code\n";

        curl_close($ch);

        $cleanedResponse = $this->extractJsonFromResponse($response);

        if ($this->isValidJson($cleanedResponse)) {
            return json_decode($cleanedResponse);
        } else {
            echo "Raw Response: $response\n";
            return null;
        }
    }

    private function extractJsonFromResponse($response) {
        $jsonStart = strpos($response, '{');
        $jsonEnd = strrpos($response, '}');
        
        if ($jsonStart !== false && $jsonEnd !== false) {
            return substr($response, $jsonStart, $jsonEnd - $jsonStart + 1);
        }
        
        return $response;
    }

    private function isValidJson($string) {
        json_decode($string);
        return (json_last_error() == JSON_ERROR_NONE);
    }

    public function testCreateCourse() {
        $data = json_encode([
            'title' => 'Unique Test Course ' . uniqid(),  // Generate unique title
            'description' => 'This is a test course',
            'category' => 'Testing'
        ]);

        $response = $this->makeHttpRequest('POST', $this->baseUrl . 'create_course.php', $data);

        echo "testCreateCourse Response: ";
        print_r($response);

        $this->assertNotNull($response, "Failed to get response from API.");
        $this->assertTrue(isset($response->id), "No course ID returned for created course.");
        $this->createdCourseId = $response->id; // Assign course ID for use in other tests
    }

    public function testGetCourses() {
        $response = $this->makeHttpRequest('POST', $this->baseUrl . 'get_courses.php');
        
        echo "testGetCourses Response: ";
        print_r($response);

        $this->assertNotNull($response, "Failed to get response from API.");
        $this->assertIsArray($response, "Response should be an array.");
        $this->assertNotEmpty($response, "Course list should not be empty.");
    }

    public function testGetCourseById() {
        $this->assertNotNull($this->createdCourseId, "No course ID set for test.");

        $data = json_encode(['course_id' => $this->createdCourseId]);
        $response = $this->makeHttpRequest('POST', $this->baseUrl . 'get_course_by_id.php', $data);

        echo "testGetCourseById Response: ";
        print_r($response);

        $this->assertNotNull($response, "Failed to get response from API.");
        $this->assertTrue(property_exists($response, 'title'), "Response does not contain 'title' property.");
        $this->assertEquals($this->createdCourseId, $response->id);
    }

    public function testUpdateCourse() {
        $data = json_encode([
            'course_id' => $this->createdCourseId,
            'title' => 'Updated Test Course ' . uniqid(),
            'description' => 'Updated description'
        ]);

        $response = $this->makeHttpRequest('POST', $this->baseUrl . 'update_course.php', $data);
        $this->assertNotNull($response, "Failed to get response from API.");
        $this->assertEquals('Course updated successfully.', $response->message);
    }

    public function testDeleteCourse() {
        $data = json_encode(['course_id' => $this->createdCourseId]);

        $response = $this->makeHttpRequest('POST', $this->baseUrl . 'delete_course.php', $data);
        $this->assertNotNull($response, "Failed to get response from API.");
        $this->assertEquals('Course deleted successfully.', $response->message);
    }
}
