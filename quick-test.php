<?php
/**
 * Quick Test Script - Kiểm tra nhanh hệ thống
 */

echo "🚀 C+ Scoring System - Quick Test\n";
echo "================================\n\n";

// Test 1: Kiểm tra Laravel
echo "1. Testing Laravel Framework...\n";
try {
    require_once 'vendor/autoload.php';
    $app = require_once 'bootstrap/app.php';
    echo "✅ Laravel loaded successfully\n";
} catch (Exception $e) {
    echo "❌ Laravel error: " . $e->getMessage() . "\n";
    exit(1);
}

// Test 2: Kiểm tra Database
echo "\n2. Testing Database Connection...\n";
try {
    $pdo = new PDO('sqlite:database/database.sqlite');
    echo "✅ Database connected successfully\n";
} catch (Exception $e) {
    echo "❌ Database error: " . $e->getMessage() . "\n";
}

// Test 3: Kiểm tra Models
echo "\n3. Testing Models...\n";
try {
    $app->make(\App\Models\Criteria::class);
    $app->make(\App\Models\Project::class);
    $app->make(\App\Models\Client::class);
    echo "✅ Models loaded successfully\n";
} catch (Exception $e) {
    echo "❌ Models error: " . $e->getMessage() . "\n";
}

// Test 4: Kiểm tra Routes
echo "\n4. Testing Routes...\n";
try {
    $routes = $app->make('router')->getRoutes();
    $apiRoutes = array_filter($routes->getRoutes(), function($route) {
        return strpos($route->uri(), 'api/') === 0;
    });
    echo "✅ Found " . count($apiRoutes) . " API routes\n";
} catch (Exception $e) {
    echo "❌ Routes error: " . $e->getMessage() . "\n";
}

// Test 5: Kiểm tra Controllers
echo "\n5. Testing Controllers...\n";
try {
    $app->make(\App\Http\Controllers\Api\CriteriaApiController::class);
    $app->make(\App\Http\Controllers\Api\ProjectApiController::class);
    $app->make(\App\Http\Controllers\Api\DashboardApiController::class);
    echo "✅ Controllers loaded successfully\n";
} catch (Exception $e) {
    echo "❌ Controllers error: " . $e->getMessage() . "\n";
}

echo "\n🎉 Quick Test Complete!\n";
echo "\n📋 Next Steps:\n";
echo "1. Start Laravel server: php artisan serve\n";
echo "2. Open browser: http://localhost:8000/admin/login\n";
echo "3. Test API: http://localhost:8000/api/dashboard/statistics\n";
echo "4. Run full test: php test-api.php\n";

echo "\n🔗 Important URLs:\n";
echo "- Login: http://localhost:8000/admin/login (admin@cplus.com / admin123)\n";
echo "- Dashboard: http://localhost:8000/admin/dashboard\n";
echo "- API Docs: http://localhost:8000/api/dashboard/statistics\n";

echo "\n📝 Troubleshooting:\n";
echo "- If database error: Check database/database.sqlite exists\n";
echo "- If server error: Check php artisan serve is running\n";
echo "- If 404 error: Check routes are registered properly\n";

?>
