# =============================================
# FTP Deploy Script - Jostru Community
# =============================================
$ftpHost    = "ftp://ftpupload.net"
$ftpUser    = "if0_41649436"
$ftpPass    = "djafu12345"
$localBase  = "D:\Jostru Community Sistem\Jostru_community"

# Mapping: local relative path => remote path on server
$files = @{
    "config\services.php"                           = "/htdocs/config/services.php"
    "resources\views\auth\login.blade.php"          = "/htdocs/resources/views/auth/login.blade.php"
    "resources\views\auth\register.blade.php"       = "/htdocs/resources/views/auth/register.blade.php"
    "app\Http\Controllers\AdminController.php"      = "/htdocs/app/Http/Controllers/AdminController.php"
    "app\Models\User.php"                           = "/htdocs/app/Models/User.php"
    ".htaccess"                                     = "/htdocs/.htaccess"
    "jostru_migrate.sql"                           = "/htdocs/jostru_migrate.sql"
}

function Upload-File($localPath, $remotePath) {
    $uri = "$ftpHost$remotePath"
    Write-Host "  Uploading: $localPath => $remotePath" -ForegroundColor Cyan

    $ftpRequest = [System.Net.FtpWebRequest]::Create($uri)
    $ftpRequest.Method = [System.Net.WebRequestMethods+Ftp]::UploadFile
    $ftpRequest.Credentials = New-Object System.Net.NetworkCredential($ftpUser, $ftpPass)
    $ftpRequest.UseBinary = $true
    $ftpRequest.UsePassive = $true
    $ftpRequest.KeepAlive = $false

    $fileContent = [System.IO.File]::ReadAllBytes($localPath)
    $ftpRequest.ContentLength = $fileContent.Length

    $requestStream = $ftpRequest.GetRequestStream()
    $requestStream.Write($fileContent, 0, $fileContent.Length)
    $requestStream.Close()

    $response = $ftpRequest.GetResponse()
    Write-Host "  OK: $($response.StatusDescription)" -ForegroundColor Green
    $response.Dispose()
}

Write-Host "`n=== Jostru Community FTP Deployer ===" -ForegroundColor Yellow
Write-Host "Target: $ftpHost`n"

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

Write-Host "`n=== Selesai! ===" -ForegroundColor Yellow
