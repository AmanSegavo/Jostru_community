$ftpHost = "ftp://145.223.108.47"
$ftpUser = "u380603901"
$ftpPass = "Bima010817@#"

function Rename-File($oldPath, $newPath) {
    $uri = "$ftpHost$oldPath"
    Write-Host "Renaming: $oldPath to $newPath" -ForegroundColor Yellow
    try {
        $ftpRequest = [System.Net.FtpWebRequest]::Create($uri)
        $ftpRequest.Method = [System.Net.WebRequestMethods+Ftp]::Rename
        $ftpRequest.Credentials = New-Object System.Net.NetworkCredential($ftpUser, $ftpPass)
        $ftpRequest.RenameTo = $newPath
        $ftpRequest.GetResponse().Dispose()
        Write-Host "Rename successful." -ForegroundColor Green
    } catch {
        Write-Host ("Error renaming " + $oldPath + ": " + $_) -ForegroundColor Red
    }
}

Rename-File "/domains/jostru.site/public_html/public/posts" "posts_conflicted"
