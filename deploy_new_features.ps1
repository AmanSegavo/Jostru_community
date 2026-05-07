# =============================================
# FTP Deploy Script - New Features (May 2026)
# =============================================
$ftpHost    = "ftp://145.223.108.47"
$ftpUser    = "u380603901"
$ftpPass    = "Monyetbima12345@#"
$localBase  = "D:\Jostru Community Sistem\Jostru_community"
$remoteBase = "/domains/jostru.site/public_html"

$files = @{
    "app\Models\User.php" = "$remoteBase/app/Models/User.php"
    "app\Http\Controllers\AdminController.php" = "$remoteBase/app/Http/Controllers/AdminController.php"
    "routes\web.php" = "$remoteBase/routes/web.php"
    "resources\views\layouts\app.blade.php" = "$remoteBase/resources/views/layouts/app.blade.php"
    "resources\views\layouts\admin.blade.php" = "$remoteBase/resources/views/layouts/admin.blade.php"
    "resources\views\admin\members.blade.php" = "$remoteBase/resources/views/admin/members.blade.php"
    "resources\views\admin\media.blade.php" = "$remoteBase/resources/views/admin/media.blade.php"
    "resources\views\pages\about.blade.php" = "$remoteBase/resources/views/pages/about.blade.php"
    "resources\views\pages\faq.blade.php" = "$remoteBase/resources/views/pages/faq.blade.php"
    "resources\views\pages\privacy_policy.blade.php" = "$remoteBase/resources/views/pages/privacy_policy.blade.php"
    "resources\views\member\waste_report.blade.php" = "$remoteBase/resources/views/member/waste_report.blade.php"
    "database\migrations\2026_05_07_155000_add_post_and_comment_permissions_to_users_table.php" = "$remoteBase/database/migrations/2026_05_07_155000_add_post_and_comment_permissions_to_users_table.php"
}

# Create remote directories if they don't exist (basic implementation for /pages and /media)
function Create-Dir($remotePath) {
    $uri = "$ftpHost$remotePath"
    $ftpRequest = [System.Net.FtpWebRequest]::Create($uri)
    $ftpRequest.Method = [System.Net.WebRequestMethods+Ftp]::MakeDirectory
    $ftpRequest.Credentials = New-Object System.Net.NetworkCredential($ftpUser, $ftpPass)
    try {
        $response = $ftpRequest.GetResponse()
        $response.Dispose()
    } catch {}
}

Create-Dir "$remoteBase/resources/views/pages"
Create-Dir "$remoteBase/resources/views/admin"

function Upload-File($localPath, $remotePath) {
    $uri = "$ftpHost$remotePath"
    Write-Host "  Uploading: $localPath => $remotePath" -ForegroundColor Cyan

    $ftpRequest = [System.Net.FtpWebRequest]::Create($uri)
    $ftpRequest.Method = [System.Net.WebRequestMethods+Ftp]::UploadFile
    $ftpRequest.Credentials = New-Object System.Net.NetworkCredential($ftpUser, $ftpPass)
    $ftpRequest.UseBinary = $true
    $ftpRequest.UsePassive = $true

    $fileContent = [System.IO.File]::ReadAllBytes($localPath)
    $ftpRequest.ContentLength = $fileContent.Length

    $requestStream = $ftpRequest.GetRequestStream()
    $requestStream.Write($fileContent, 0, $fileContent.Length)
    $requestStream.Close()

    $response = $ftpRequest.GetResponse()
    Write-Host "  OK: $($response.StatusDescription)" -ForegroundColor Green
    $response.Dispose()
}

Write-Host "`n=== Deploying New Features ===" -ForegroundColor Yellow
foreach ($entry in $files.GetEnumerator()) {
    $localFull = Join-Path $localBase $entry.Key
    if (Test-Path $localFull) {
        try {
            Upload-File $localFull $entry.Value
        } catch {
            Write-Host "  GAGAL: $_" -ForegroundColor Red
        }
    } else {
        Write-Host "  File tidak ditemukan: $localFull" -ForegroundColor Red
    }
}
Write-Host "`n=== Deploy Selesai! ===" -ForegroundColor Yellow
