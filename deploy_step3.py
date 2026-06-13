import paramiko
import os

host = "145.223.108.47"
port = 65002
username = "u380603901"
password = "Farm12345@#"

files_to_upload = [
    ("database/migrations/2026_06_10_021800_add_rab_id_to_finances_table.php", "/home/u380603901/domains/jostru.site/public_html/database/migrations/2026_06_10_021800_add_rab_id_to_finances_table.php"),
    ("app/Models/Finance.php", "/home/u380603901/domains/jostru.site/public_html/app/Models/Finance.php"),
    ("app/Models/Rab.php", "/home/u380603901/domains/jostru.site/public_html/app/Models/Rab.php"),
    ("app/Http/Controllers/AdminController.php", "/home/u380603901/domains/jostru.site/public_html/app/Http/Controllers/AdminController.php"),
    ("resources/views/admin/finances.blade.php", "/home/u380603901/domains/jostru.site/public_html/resources/views/admin/finances.blade.php"),
    ("resources/views/admin/rabs.blade.php", "/home/u380603901/domains/jostru.site/public_html/resources/views/admin/rabs.blade.php"),
    ("migrate_hutang.php", "/home/u380603901/domains/jostru.site/public_html/migrate_hutang.php")
]

try:
    transport = paramiko.Transport((host, port))
    transport.connect(username=username, password=password)
    sftp = paramiko.SFTPClient.from_transport(transport)
    
    for local, remote in files_to_upload:
        print(f"Uploading {local} to {remote}...")
        sftp.put(local, remote)
        print("OK.")
        
    sftp.close()
    
    client = paramiko.SSHClient()
    client.set_missing_host_key_policy(paramiko.AutoAddPolicy())
    client.connect(host, port=port, username=username, password=password)
    
    print("Running artisan migrate...")
    stdin, stdout, stderr = client.exec_command("php /home/u380603901/domains/jostru.site/public_html/artisan migrate --force")
    print(stdout.read().decode())
    
    print("Running migrate_hutang.php...")
    stdin, stdout, stderr = client.exec_command("php /home/u380603901/domains/jostru.site/public_html/migrate_hutang.php")
    print(stdout.read().decode())
    
    print("Deleting migrate_hutang.php...")
    client.exec_command("rm /home/u380603901/domains/jostru.site/public_html/migrate_hutang.php")
    
    client.close()
    transport.close()
    print("All tasks finished!")
except Exception as e:
    print(f"Error: {e}")
