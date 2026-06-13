import paramiko
import os

host = "145.223.108.47"
port = 65002
username = "u380603901"
password = "Farm12345@#"

local_file = "d:/Jostru Community Sistem/Jostru_community/insert_farm_rabs.php"
remote_file = "/home/u380603901/domains/jostru.site/public_html/insert_farm_rabs.php"

try:
    transport = paramiko.Transport((host, port))
    transport.connect(username=username, password=password)
    sftp = paramiko.SFTPClient.from_transport(transport)
    
    print("Uploading script...")
    sftp.put(local_file, remote_file)
    print("Upload OK.")
    sftp.close()
    
    client = paramiko.SSHClient()
    client.set_missing_host_key_policy(paramiko.AutoAddPolicy())
    client.connect(host, port=port, username=username, password=password)
    
    print("Running script...")
    stdin, stdout, stderr = client.exec_command("php /home/u380603901/domains/jostru.site/public_html/insert_farm_rabs.php")
    print(stdout.read().decode())
    err = stderr.read().decode()
    if err:
        print("Error:", err)
        
    print("Deleting script...")
    client.exec_command("rm /home/u380603901/domains/jostru.site/public_html/insert_farm_rabs.php")
    
    client.close()
    transport.close()
    print("Done!")
except Exception as e:
    print(f"Error: {e}")
