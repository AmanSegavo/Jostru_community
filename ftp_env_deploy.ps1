# Upload .env YANG BENAR ke server (dengan DB jostru yang betul)
$ftpHost = "ftp://ftpupload.net"
$ftpUser = "if0_41649436"
$ftpPass = "djafu12345"

$envContent = @"
APP_NAME="Jostru Community"
APP_ENV=production
APP_KEY=base64:OGc2Z2drM2Y0aWloa3V3dnBma285OGRja2l6azh5c3Y=
APP_DEBUG=false
APP_TIMEZONE=Asia/Jakarta
APP_URL=https://jostru.kesug.com

APP_LOCALE=en
APP_FALLBACK_LOCALE=en
APP_FAKER_LOCALE=en_US

APP_MAINTENANCE_DRIVER=file

BCRYPT_ROUNDS=12

LOG_CHANNEL=stack
LOG_STACK=single
LOG_DEPRECATIONS_CHANNEL=null
LOG_LEVEL=error

DB_CONNECTION=mysql
DB_HOST=sql100.infinityfree.com
DB_PORT=3306
DB_DATABASE=if0_41649436_jostru
DB_USERNAME=if0_41649436
DB_PASSWORD=djafu12345

SESSION_DRIVER=database
SESSION_LIFETIME=120
SESSION_ENCRYPT=false
SESSION_PATH=/
SESSION_DOMAIN=null

BROADCAST_CONNECTION=log
FILESYSTEM_DISK=local
QUEUE_CONNECTION=database

CACHE_STORE=database
CACHE_PREFIX=

MEMCACHED_HOST=127.0.0.1

REDIS_CLIENT=phpredis
REDIS_HOST=127.0.0.1
REDIS_PASSWORD=null
REDIS_PORT=6379

MAIL_MAILER=log
MAIL_SCHEME=null
MAIL_HOST=127.0.0.1
MAIL_PORT=2525
MAIL_USERNAME=null
MAIL_PASSWORD=null
MAIL_FROM_ADDRESS="hello@jostru.kesug.com"
MAIL_FROM_NAME="Jostru Community"

AWS_ACCESS_KEY_ID=
AWS_SECRET_ACCESS_KEY=
AWS_DEFAULT_REGION=us-east-1
AWS_BUCKET=
AWS_USE_PATH_STYLE_ENDPOINT=false

VITE_APP_NAME="Jostru Community"

# Google OAuth (Socialite)
GOOGLE_CLIENT_ID=182853815737-cp69o6jogtfjuicln5ngnbfqgvd6jle2.apps.googleusercontent.com
GOOGLE_CLIENT_SECRET=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
GOOGLE_REDIRECT_URL=https://jostru.kesug.com/auth/google/callback
"@

$tempFile = "$env:TEMP\jostru_env_fixed.txt"
[System.IO.File]::WriteAllText($tempFile, $envContent, [System.Text.Encoding]::UTF8)

function Upload-File($localPath, $remotePath) {
    $uri = "$ftpHost$remotePath"
    Write-Host "  Uploading: $remotePath" -ForegroundColor Cyan
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

Write-Host "`n=== Upload .env BENAR (DB: if0_41649436_jostru) ===" -ForegroundColor Yellow
try {
    Upload-File $tempFile "/htdocs/.env"
    Write-Host "`n.env berhasil diupload dengan DB yang benar!" -ForegroundColor Green
} catch {
    Write-Host "  GAGAL: $_" -ForegroundColor Red
}
Remove-Item $tempFile -ErrorAction SilentlyContinue
