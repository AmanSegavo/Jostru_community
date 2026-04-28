# List files in htdocs
$ftpHost = "ftp://ftpupload.net"
$ftpUser = "if0_41649436"
$ftpPass = "djafu12345"

function List-Files($remoteDir) {
    $uri = "$ftpHost$remoteDir"
    Write-Host "Listing: $remoteDir" -ForegroundColor Yellow
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
}

List-Files "/htdocs/"
List-Files "/htdocs/public/"
