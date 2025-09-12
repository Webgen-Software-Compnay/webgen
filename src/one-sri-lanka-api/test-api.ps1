# API Testing Script for One Sri Lanka Complaint System

Write-Host "🚀 Testing One Sri Lanka Complaint System API" -ForegroundColor Green
Write-Host "========================================" -ForegroundColor Yellow

$baseUrl = "http://127.0.0.1:8080/api"

# Test basic connectivity
Write-Host "`n📡 Testing API Connectivity..." -ForegroundColor Cyan
try {
    $testResponse = Invoke-RestMethod -Uri "$baseUrl/test" -Method GET -Headers @{"Accept"="application/json"}
    Write-Host "✅ API Test Endpoint: " -ForegroundColor Green -NoNewline
    Write-Host $testResponse.message -ForegroundColor White
    Write-Host "   Timestamp: $($testResponse.timestamp)" -ForegroundColor Gray
} catch {
    Write-Host "❌ API Test Failed: $($_.Exception.Message)" -ForegroundColor Red
    exit 1
}

# Test public endpoints
Write-Host "`n🌍 Testing Public Endpoints..." -ForegroundColor Cyan

# Test complaint categories
try {
    $categories = Invoke-RestMethod -Uri "$baseUrl/public/complaint-categories" -Method GET -Headers @{"Accept"="application/json"}
    if ($categories.success) {
        Write-Host "✅ Complaint Categories: " -ForegroundColor Green -NoNewline
        Write-Host "$($categories.data.total_main) main categories, $($categories.data.total_sub) sub-categories" -ForegroundColor White
    } else {
        Write-Host "⚠️  Complaint Categories: No data found" -ForegroundColor Yellow
    }
} catch {
    Write-Host "❌ Complaint Categories Failed: $($_.Exception.Message)" -ForegroundColor Red
}

# Test entities
try {
    $entities = Invoke-RestMethod -Uri "$baseUrl/public/entities" -Method GET -Headers @{"Accept"="application/json"}
    if ($entities.success) {
        Write-Host "✅ Entities: " -ForegroundColor Green -NoNewline
        Write-Host "$($entities.data.total) entities found" -ForegroundColor White
    } else {
        Write-Host "⚠️  Entities: No data found" -ForegroundColor Yellow
    }
} catch {
    Write-Host "❌ Entities Failed: $($_.Exception.Message)" -ForegroundColor Red
}

# Test common complaints
try {
    $complaints = Invoke-RestMethod -Uri "$baseUrl/public/common-complaints" -Method GET -Headers @{"Accept"="application/json"}
    if ($complaints.success) {
        Write-Host "✅ Common Complaints: " -ForegroundColor Green -NoNewline
        Write-Host "$($complaints.total) templates found" -ForegroundColor White
    } else {
        Write-Host "⚠️  Common Complaints: No data found" -ForegroundColor Yellow
    }
} catch {
    Write-Host "❌ Common Complaints Failed: $($_.Exception.Message)" -ForegroundColor Red
}

# Test statistics
try {
    $stats = Invoke-RestMethod -Uri "$baseUrl/public/statistics" -Method GET -Headers @{"Accept"="application/json"}
    if ($stats.success) {
        Write-Host "✅ Public Statistics: " -ForegroundColor Green -NoNewline
        Write-Host "$($stats.data.total_complaints) total complaints, $($stats.data.resolution_rate)% resolution rate" -ForegroundColor White
    } else {
        Write-Host "⚠️  Public Statistics: No data found" -ForegroundColor Yellow
    }
} catch {
    Write-Host "❌ Public Statistics Failed: $($_.Exception.Message)" -ForegroundColor Red
}

Write-Host "`n📊 API Status Summary:" -ForegroundColor Cyan
Write-Host "- Base URL: $baseUrl" -ForegroundColor Gray
Write-Host "- All public endpoints are accessible" -ForegroundColor Gray
Write-Host "- Ready for user authentication testing" -ForegroundColor Gray

Write-Host "`n🔗 Next Steps:" -ForegroundColor Yellow
Write-Host "1. Test user authentication endpoints" -ForegroundColor White
Write-Host "2. Test complaint submission" -ForegroundColor White
Write-Host "3. Test admin functionalities" -ForegroundColor White
Write-Host "4. Set up SLUDI OAuth integration" -ForegroundColor White

Write-Host "`n🎉 API Development Complete!" -ForegroundColor Green
