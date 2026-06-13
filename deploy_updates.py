import paramiko
import os

host = "145.223.108.47"
port = 65002
username = "u380603901"
password = "Farm12345@#"

files_to_upload = [
    ("routes/web.php", "/home/u380603901/domains/jostru.site/public_html/routes/web.php"),
    ("app/Http/Controllers/DivisionController.php", "/home/u380603901/domains/jostru.site/public_html/app/Http/Controllers/DivisionController.php"),
    ("resources/views/admin/divisions/index.blade.php", "/home/u380603901/domains/jostru.site/public_html/resources/views/admin/divisions/index.blade.php"),
    ("resources/views/admin/divisions/finances.blade.php", "/home/u380603901/domains/jostru.site/public_html/resources/views/admin/divisions/finances.blade.php")
]

try:
    transport = paramiko.Transport((host, port))
    transport.connect(username=username, password=password)
    sftp = paramiko.SFTPClient.from_transport(transport)
    
    for local, remote in files_to_upload:
        print(f"Uploading {local} to {remote}...")
        remote_dir = os.path.dirname(remote)
        try:
            sftp.stat(remote_dir)
        except IOError:
            sftp.mkdir(remote_dir)
            
        sftp.put(local, remote)
        print("OK.")
        
    sftp.close()
    transport.close()
    print("All uploads finished!")
except Exception as e:
    print(f"Error: {e}")
