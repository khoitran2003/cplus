@echo off
echo 🚀 C+ Scoring System API Test cho Windows
echo ========================================

set BASE_URL=http://localhost:8000/api

echo Testing API endpoints...
echo.

echo 1. Testing Dashboard Statistics...
curl -s "%BASE_URL%/dashboard/statistics"
echo.
echo.

echo 2. Testing Criteria Types...
curl -s "%BASE_URL%/criteria/types"
echo.
echo.

echo 3. Testing Industries...
curl -s "%BASE_URL%/industries"
echo.
echo.

echo 4. Testing Criteria List...
curl -s "%BASE_URL%/criteria"
echo.
echo.

echo 5. Testing Criteria Hierarchy...
curl -s "%BASE_URL%/criteria/hierarchy"
echo.
echo.

echo 6. Testing Projects List...
curl -s "%BASE_URL%/projects"
echo.
echo.

echo 7. Testing Recent Projects...
curl -s "%BASE_URL%/dashboard/recent-projects"
echo.
echo.

echo 8. Testing Scoring Status...
curl -s "%BASE_URL%/dashboard/scoring-status"
echo.
echo.

echo 🎉 API Testing Complete!
echo.
echo 📊 Access URLs:
echo - Web Interface: http://localhost:8000/admin/login
echo - API Base: http://localhost:8000/api
echo - Dashboard: http://localhost:8000/admin/dashboard
echo.
echo 📝 Next Steps:
echo 1. Make sure Laravel server is running: php artisan serve
echo 2. Open web browser and test the interface
echo 3. Check database connection
echo.
pause
