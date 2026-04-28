$ftpHost = "ftp://ftpupload.net"
$ftpUser = "if0_41649436"
$ftpPass = "djafu12345"
$localBase = "D:\Jostru Community Sistem\Jostru_community"

function Upload-Folder-Recursive($localPath, $remotePath) {
    if (!(Test-Path $localPath)) { return }
    
    # Ensure remote directory exists
    try {
        $uri = "$ftpHost$remotePath"
        $request = [System.Net.FtpWebRequest]::Create($uri)
        $request.Method = [System.Net.WebRequestMethods+Ftp]::MakeDirectory
        $request.Credentials = New-Object System.Net.NetworkCredential($ftpUser, $ftpPass)
        $request.GetResponse().Dispose()
        Write-Host "Created Directory: $remotePath" -ForegroundColor Green
    } catch {
        # Directory probably already exists
    }

    $items = Get-ChildItem $localPath
    foreach ($item in $items) {
        $newLocal = $item.FullName
        $newRemote = "$remotePath/$($item.Name)"
        
        if ($item.PSIsContainer) {
            Upload-Folder-Recursive $newLocal $newRemote
        } else {
            Upload-File $newLocal $newRemote
        }
    }
}

function Upload-File($localPath, $remotePath) {
    $uri = "$ftpHost$remotePath"
    Write-Host "  Uploading: $remotePath" -ForegroundColor Cyan
    try {
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
        $ftpRequest.GetResponse().Dispose()
    } catch {
        Write-Host "  FAILED: $remotePath - $_" -ForegroundColor Red
    }
}

Write-Host "Starting sync of Socialite dependencies..." -ForegroundColor Yellow
Upload-Folder-Recursive "$localBase\vendor\laravel\socialite" "/htdocs/vendor/laravel/socialite"
Upload-Folder-Recursive "$localBase\vendor\league\oauth1-client" "/htdocs/vendor/league/oauth1-client"
Write-Host "Done!" -ForegroundColor Yellow
