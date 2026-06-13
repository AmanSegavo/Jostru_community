import paramiko

host = "145.223.108.47"
port = 65002
username = "u380603901"
password = "Farm12345@#"

files_to_deploy = [
    ("d:/Jostru Community Sistem/Jostru_community/app/Http/Controllers/AdminController.php", "/home/u380603901/domains/jostru.site/public_html/app/Http/Controllers/AdminController.php"),
    ("d:/Jostru Community Sistem/Jostru_community/app/Http/Controllers/DivisionController.php", "/home/u380603901/domains/jostru.site/public_html/app/Http/Controllers/DivisionController.php"),
    ("d:/Jostru Community Sistem/Jostru_community/resources/views/admin/rabs.blade.php", "/home/u380603901/domains/jostru.site/public_html/resources/views/admin/rabs.blade.php")
]

try:
    print("Connecting to SSH...")
    client = paramiko.SSHClient()
    client.set_missing_host_key_policy(paramiko.AutoAddPolicy())
    client.connect(host, port=port, username=username, password=password)
    
    sftp = client.open_sftp()
    
    for local_file, remote_file in files_to_deploy:
        print(f"Uploading {local_file} to {remote_file}...")
        sftp.put(local_file, remote_file)
        print("Upload successful!")
        
    sftp.close()
    
    # Run artisan view:clear
    print("Clearing cache on server...")
    stdin, stdout, stderr = client.exec_command("cd /home/u380603901/domains/jostru.site/public_html && php artisan view:clear && php artisan cache:clear")
    print(stdout.read().decode())
    
    client.close()
    print("Deployment completed successfully!")
except Exception as e:
    print(f"Error: {e}")
