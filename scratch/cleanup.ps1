$ftpHost = "ftp://145.223.108.47"
$ftpUser = "u380603901"
$ftpPass = "Bima010817@#"

function Delete-File($remoteFile) {
    $uri = "$ftpHost$remoteFile"
    Write-Host "Deleting: $remoteFile" -ForegroundColor Yellow
    try {
        $ftpRequest = [System.Net.FtpWebRequest]::Create($uri)
        $ftpRequest.Method = [System.Net.WebRequestMethods+Ftp]::DeleteFile
        $ftpRequest.Credentials = New-Object System.Net.NetworkCredential($ftpUser, $ftpPass)
        $ftpRequest.GetResponse().Dispose()
        Write-Host "Delete successful." -ForegroundColor Green
    } catch {
        Write-Host ("Error deleting " + $remoteFile + ": " + $_) -ForegroundColor Red
    }
}

Delete-File "/domains/jostru.site/public_html/public/check_db_tables.php"
