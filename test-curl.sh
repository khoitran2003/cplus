#!/bin/bash

# C+ Scoring System API Test Script
# Chạy script này để test các API endpoints bằng curl

BASE_URL="http://localhost:8000/api"

echo "🚀 C+ Scoring System API Test với cURL"
echo "Base URL: $BASE_URL"
echo "================================================"

# Test Dashboard Statistics
echo "1. Testing Dashboard Statistics..."
curl -s -w "Status: %{http_code}\n" "$BASE_URL/dashboard/statistics" | jq '.' 2>/dev/null || echo "Response received"
echo ""

# Test Criteria Types
echo "2. Testing Criteria Types..."
curl -s -w "Status: %{http_code}\n" "$BASE_URL/criteria/types" | jq '.' 2>/dev/null || echo "Response received"
echo ""

# Test Industries
echo "3. Testing Industries..."
curl -s -w "Status: %{http_code}\n" "$BASE_URL/industries" | jq '.' 2>/dev/null || echo "Response received"
echo ""

# Test Criteria List
echo "4. Testing Criteria List..."
curl -s -w "Status: %{http_code}\n" "$BASE_URL/criteria" | jq '.' 2>/dev/null || echo "Response received"
echo ""

# Test Criteria Hierarchy
echo "5. Testing Criteria Hierarchy..."
curl -s -w "Status: %{http_code}\n" "$BASE_URL/criteria/hierarchy" | jq '.' 2>/dev/null || echo "Response received"
echo ""

# Test Projects List
echo "6. Testing Projects List..."
curl -s -w "Status: %{http_code}\n" "$BASE_URL/projects" | jq '.' 2>/dev/null || echo "Response received"
echo ""

# Test Recent Projects
echo "7. Testing Recent Projects..."
curl -s -w "Status: %{http_code}\n" "$BASE_URL/dashboard/recent-projects" | jq '.' 2>/dev/null || echo "Response received"
echo ""

# Test Scoring Status
echo "8. Testing Scoring Status..."
curl -s -w "Status: %{http_code}\n" "$BASE_URL/dashboard/scoring-status" | jq '.' 2>/dev/null || echo "Response received"
echo ""

# Test Project Comparison
echo "9. Testing Project Comparison..."
curl -s -w "Status: %{http_code}\n" "$BASE_URL/dashboard/project-comparison" | jq '.' 2>/dev/null || echo "Response received"
echo ""

# Test Criteria Usage
echo "10. Testing Criteria Usage..."
curl -s -w "Status: %{http_code}\n" "$BASE_URL/dashboard/criteria-usage" | jq '.' 2>/dev/null || echo "Response received"
echo ""

echo "🎉 API Testing Complete!"
echo ""
echo "📊 Test Results:"
echo "- Check HTTP status codes above"
echo "- 200 = Success"
echo "- 500 = Server Error (check Laravel logs)"
echo "- Connection refused = Server not running"
echo ""
echo "🔗 Access URLs:"
echo "- Web Interface: http://localhost:8000/admin/login"
echo "- API Base: http://localhost:8000/api"
echo "- Dashboard: http://localhost:8000/admin/dashboard"
echo ""
echo "📝 Troubleshooting:"
echo "1. Make sure Laravel server is running: php artisan serve"
echo "2. Check database connection"
echo "3. Check Laravel logs: storage/logs/laravel.log"
