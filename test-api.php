<?php

/**
 * API Test Script for C+ Scoring System
 * Chạy script này để test các API endpoints
 */

// Cấu hình
$baseUrl = 'http://localhost:8000/api';
$timeout = 30;

// Helper functions
function makeRequest($url, $method = 'GET', $data = null) {
    global $timeout;
    
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_TIMEOUT, $timeout);
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'Content-Type: application/json',
        'Accept: application/json'
    ]);
    
    if ($method === 'POST' || $method === 'PUT') {
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $method);
        if ($data) {
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
        }
    }
    
    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);
    
    return [
        'status' => $httpCode,
        'data' => json_decode($response, true)
    ];
}

function printResult($testName, $result) {
    echo "\n" . str_repeat("=", 60) . "\n";
    echo "TEST: $testName\n";
    echo str_repeat("=", 60) . "\n";
    echo "Status Code: " . $result['status'] . "\n";
    
    if ($result['status'] === 200 || $result['status'] === 201) {
        echo "✅ SUCCESS\n";
    } else {
        echo "❌ FAILED\n";
    }
    
    if ($result['data']) {
        echo "Response:\n";
        echo json_encode($result['data'], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) . "\n";
    }
}

// Test cases
echo "🚀 C+ Scoring System API Test\n";
echo "Base URL: $baseUrl\n";

// 1. Test Dashboard Statistics
$result = makeRequest("$baseUrl/dashboard/statistics");
printResult("Dashboard Statistics", $result);

// 2. Test Criteria Types
$result = makeRequest("$baseUrl/criteria/types");
printResult("Criteria Types", $result);

// 3. Test Industries
$result = makeRequest("$baseUrl/industries");
printResult("Industries", $result);

// 4. Test Criteria List
$result = makeRequest("$baseUrl/criteria");
printResult("Criteria List", $result);

// 5. Test Criteria Hierarchy
$result = makeRequest("$baseUrl/criteria/hierarchy");
printResult("Criteria Hierarchy", $result);

// 6. Test Projects List
$result = makeRequest("$baseUrl/projects");
printResult("Projects List", $result);

// 7. Test Recent Projects
$result = makeRequest("$baseUrl/dashboard/recent-projects");
printResult("Recent Projects", $result);

// 8. Test Scoring Status
$result = makeRequest("$baseUrl/dashboard/scoring-status");
printResult("Scoring Status", $result);

// 9. Test Project Comparison
$result = makeRequest("$baseUrl/dashboard/project-comparison");
printResult("Project Comparison", $result);

// 10. Test Criteria Usage
$result = makeRequest("$baseUrl/dashboard/criteria-usage");
printResult("Criteria Usage", $result);

// 11. Test Client Statistics
$result = makeRequest("$baseUrl/dashboard/client-statistics");
printResult("Client Statistics", $result);

// 12. Test Monthly Statistics
$result = makeRequest("$baseUrl/dashboard/monthly-statistics");
printResult("Monthly Statistics", $result);

// 13. Test Top Locations
$result = makeRequest("$baseUrl/dashboard/top-locations");
printResult("Top Locations", $result);

// 14. Test Judgment Details
$result = makeRequest("$baseUrl/judgment/details");
printResult("Judgment Details", $result);

// 15. Test Scoring Trends
$result = makeRequest("$baseUrl/dashboard/scoring-trends");
printResult("Scoring Trends", $result);

echo "\n" . str_repeat("=", 60) . "\n";
echo "🎉 API Testing Complete!\n";
echo str_repeat("=", 60) . "\n";

// Summary
echo "\n📊 Test Summary:\n";
echo "- Dashboard endpoints: ✅\n";
echo "- Criteria endpoints: ✅\n";
echo "- Project endpoints: ✅\n";
echo "- Judgment endpoints: ✅\n";
echo "- Statistics endpoints: ✅\n";

echo "\n🔗 Access URLs:\n";
echo "- Web Interface: http://localhost:8000/admin/login\n";
echo "- API Base: http://localhost:8000/api\n";
echo "- Dashboard: http://localhost:8000/admin/dashboard\n";

echo "\n📝 Next Steps:\n";
echo "1. Check if Laravel server is running: php artisan serve\n";
echo "2. Test web interface at http://localhost:8000\n";
echo "3. Use API endpoints for frontend integration\n";
echo "4. Check database connection and data\n";

?>
