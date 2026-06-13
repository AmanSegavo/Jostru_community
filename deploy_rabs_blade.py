import paramiko

host = "145.223.108.47"
port = 65002
username = "u380603901"
password = "Farm12345@#"

local_file = "d:/Jostru Community Sistem/Jostru_community/resources/views/admin/rabs.blade.php"
remote_file = "/home/u380603901/domains/jostru.site/public_html/resources/views/admin/rabs.blade.php"

try:
    transport = paramiko.Transport((host, port))
    transport.connect(username=username, password=password)
    sftp = paramiko.SFTPClient.from_transport(transport)
    
    print("Uploading rabs.blade.php...")
    sftp.put(local_file, remote_file)
    print("Upload OK.")
    
    sftp.close()
    
    client = paramiko.SSHClient()
    client.set_missing_host_key_policy(paramiko.AutoAddPolicy())
    client.connect(host, port=port, username=username, password=password)
    
    print("Clearing view cache...")
    stdin, stdout, stderr = client.exec_command("php /home/u380603901/domains/jostru.site/public_html/artisan view:clear")
    print(stdout.read().decode())
    
    client.close()
    transport.close()
except Exception as e:
    print(e)
