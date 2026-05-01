$ftpHost = "145.223.108.47"
$ftpUser = "u380603901"
$ftpPass = "Lk412119"

# Files to upload: [local path] => [FTP path]
$fileMappings = @{
    "resources\views\layouts\app.blade.php"   = "/htdocs/resources/views/layouts/app.blade.php"
    "resources\views\auth\login.blade.php"     = "/htdocs/resources/views/auth/login.blade.php"
    "resources\views\auth\register.blade.php"  = "/htdocs/resources/views/auth/register.blade.php"
    "resources\views\welcome.blade.php"        = "/htdocs/resources/views/welcome.blade.php"
}

$baseDir = "d:\Jostru Community Sistem\Jostru_community"

# First, list root to determine correct path
Write-Host "Testing FTP connection..."
try {
    $req = [System.Net.FtpWebRequest]::Create("ftp://${ftpHost}/")
    $req.Method = [System.Net.WebRequestMethods+Ftp]::ListDirectory
    $req.Credentials = New-Object System.Net.NetworkCredential($ftpUser, $ftpPass)
    $req.Timeout = 15000
    $resp = $req.GetResponse()
    $reader = New-Object System.IO.StreamReader($resp.GetResponseStream())
    Write-Host "Root contents:"
    Write-Host ($reader.ReadToEnd())
    $reader.Close()
    $resp.Close()
    Write-Host "FTP connection OK!"
} catch {
    Write-Host "FTP connection failed: $($_.Exception.Message)"
    exit 1
}
