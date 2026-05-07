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
        
        $fileContent = [System.IO.File]::ReadAllBytes($localFile)
        $ftpRequest.ContentLength = $fileContent.Length
        
        $requestStream = $ftpRequest.GetRequestStream()
        $requestStream.Write($fileContent, 0, $fileContent.Length)
        $requestStream.Close()
        $ftpRequest.GetResponse().Dispose()
        Write-Host "Upload successful." -ForegroundColor Green
    } catch {
        Write-Host ("Error uploading " + $localFile + ": " + $_) -ForegroundColor Red
    }
}

Upload-File "d:\Jostru Community Sistem\Jostru_community\routes\web.php" "/domains/jostru.site/public_html/routes/web.php"
