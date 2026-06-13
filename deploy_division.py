import paramiko

host = "145.223.108.47"
port = 65002
username = "u380603901"
password = "Farm12345@#"

local_file = "d:/Jostru Community Sistem/Jostru_community/app/Http/Controllers/DivisionController.php"
remote_file = "/home/u380603901/domains/jostru.site/public_html/app/Http/Controllers/DivisionController.php"

try:
    transport = paramiko.Transport((host, port))
    transport.connect(username=username, password=password)
    sftp = paramiko.SFTPClient.from_transport(transport)
    
    print("Uploading DivisionController.php...")
    sftp.put(local_file, remote_file)
    print("Upload OK.")
    
    sftp.close()
    transport.close()
except Exception as e:
    print(e)
