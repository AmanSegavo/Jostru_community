$ftpHost = "ftp://145.223.108.47"
$ftpUser = "u380603901"
$ftpPass = "Bima010817@#"

function List-Files($remoteDir) {
    $uri = "$ftpHost$remoteDir"
    Write-Host "Listing: $remoteDir" -ForegroundColor Yellow
    try {
        $ftpRequest = [System.Net.FtpWebRequest]::Create($uri)
        $ftpRequest.Method = [System.Net.WebRequestMethods+Ftp]::ListDirectoryDetails
        $ftpRequest.Credentials = New-Object System.Net.NetworkCredential($ftpUser, $ftpPass)
        $ftpRequest.UseBinary = $true
        $ftpRequest.UsePassive = $true
        $ftpRequest.KeepAlive = $false
        $response = $ftpRequest.GetResponse()
        $reader = New-Object System.IO.StreamReader($response.GetResponseStream())
        $content = $reader.ReadToEnd()
        Write-Host $content
        $reader.Close()
        $response.Dispose()
    } catch {
        Write-Host ("Error listing " + $remoteDir + ": " + $_) -ForegroundColor Red
    }
}

List-Files "/"
function Download-Log($remoteFile, $localFile) {
    $uri = "$ftpHost$remoteFile"
    Write-Host "Downloading: $remoteFile to $localFile" -ForegroundColor Yellow
    try {
        $ftpRequest = [System.Net.FtpWebRequest]::Create($uri)
        $ftpRequest.Method = [System.Net.WebRequestMethods+Ftp]::DownloadFile
        $ftpRequest.Credentials = New-Object System.Net.NetworkCredential($ftpUser, $ftpPass)
        $ftpRequest.UseBinary = $true
        $ftpRequest.UsePassive = $true
        $ftpRequest.KeepAlive = $false
        $response = $ftpRequest.GetResponse()
        $stream = $response.GetResponseStream()
        $fileStream = [System.IO.File]::Create($localFile)
        $stream.CopyTo($fileStream)
        $fileStream.Close()
        $stream.Close()
        $response.Dispose()
        
        Write-Host "Download complete. Reading last 100 lines..." -ForegroundColor Green
        Get-Content $localFile -Tail 100
    } catch {
        Write-Host ("Error downloading " + $remoteFile + ": " + $_) -ForegroundColor Red
    }
}

Download-Log "/domains/jostru.site/public_html/public/index.php" "d:\Jostru Community Sistem\Jostru_community\scratch\remote_index.php"
