$ftpHost = "ftp://145.223.108.47"
$ftpUser = "u380603901"
$ftpPass = "Bima010817@#"

function Upload-File($localFile, $remoteFile) {
    $uri = "$ftpHost$remoteFile"
    Write-Host "Uploading: $localFile to $remoteFile" -ForegroundColor Yellow
    try {
        $ftpRequest = [System.Net.FtpWebRequest]::Create($uri)
        $ftpRequest.Method = [System.Net.WebRequestMethods+Ftp]::UploadFile
        $ftpRequest.Credentials = New-Object System.Net.NetworkCredential($ftpUser, $ftpPass)
        $ftpRequest.UseBinary = $true
        $ftpRequest.UsePassive = $true
        $ftpRequest.KeepAlive = $false
        
        $fileContent = [System.IO.File]::ReadAllBytes($localFile)
        $ftpRequest.ContentLength = $fileContent.Length
        
        $requestStream = $ftpRequest.GetRequestStream()
        $requestStream.Write($fileContent, 0, $fileContent.Length)
        $requestStream.Close()
        
        $response = $ftpRequest.GetResponse()
        Write-Host "Upload successful: $($response.StatusDescription)" -ForegroundColor Green
        $response.Dispose()
    } catch {
        Write-Host ("Error uploading " + $localFile + ": " + $_) -ForegroundColor Red
    }
}

Upload-File "d:\Jostru Community Sistem\Jostru_community\app\Http\Controllers\AdminController.php" "/domains/jostru.site/public_html/app/Http/Controllers/AdminController.php"
Upload-File "d:\Jostru Community Sistem\Jostru_community\app\Http\Controllers\AuthController.php" "/domains/jostru.site/public_html/app/Http/Controllers/AuthController.php"
Upload-File "d:\Jostru Community Sistem\Jostru_community\check_db_tables.php" "/domains/jostru.site/public_html/public/check_db_tables.php"
