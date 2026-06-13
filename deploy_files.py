import paramiko
import os

host = '145.223.108.47'
port = 65002
username = 'u380603901'
password = 'Farm12345@#'
remote_base = '/home/u380603901/domains/jostru.site/public_html'
local_base = r'd:\Jostru Community Sistem\Jostru_community'

files_to_upload = [
    'app/Http/Controllers/DivisionController.php',
    'app/Models/Gallery.php',
    'database/migrations/2026_06_05_000000_add_logo_to_divisions_table.php',
    'resources/views/admin/divisions/index.blade.php',
    'resources/views/admin/dividends/certificate_print.blade.php'
]

try:
    ssh = paramiko.SSHClient()
    ssh.set_missing_host_key_policy(paramiko.AutoAddPolicy())
    ssh.connect(host, port, username, password)
    
    sftp = ssh.open_sftp()
    
    for file_path in files_to_upload:
        local_path = os.path.join(local_base, file_path)
        remote_path = f"{remote_base}/{file_path.replace(chr(92), '/')}"
        
        # Ensure remote directory exists
        remote_dir = os.path.dirname(remote_path)
        try:
            sftp.stat(remote_dir)
        except FileNotFoundError:
            # Create directories
            dirs = remote_dir.replace(remote_base, '').strip('/').split('/')
            current_dir = remote_base
            for d in dirs:
                current_dir = f"{current_dir}/{d}"
                try:
                    sftp.stat(current_dir)
                except FileNotFoundError:
                    sftp.mkdir(current_dir)
                    
        print(f"Uploading {local_path} to {remote_path}")
        sftp.put(local_path, remote_path)
        
    sftp.close()
    
    print("Clearing caches and migrating...")
    stdin, stdout, stderr = ssh.exec_command(f'cd {remote_base} && php artisan migrate --force && php artisan route:clear && php artisan view:clear && php artisan config:clear')
    print('Output:', stdout.read().decode('utf-8', errors='replace'))
    print('Errors:', stderr.read().decode('utf-8', errors='replace'))
    
    ssh.close()
    print("Deployment completed successfully.")
except Exception as e:
    print(f'Error: {e}')
