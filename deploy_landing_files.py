import paramiko
import os

host = '145.223.108.47'
port = 65002
username = 'u380603901'
password = 'Farm12345@#'
remote_base = '/home/u380603901/domains/jostru.site/public_html'
local_base = r'd:\Jostru Community Sistem\Jostru_community'

files_to_upload = [
    'app/Http/Controllers/ERPController.php',
    'app/Http/Controllers/AdminController.php',
    'app/Http/Controllers/MemberController.php',
    'app/Http/Controllers/DividendController.php',
    'app/Models/ChatRelation.php',
    'app/Models/Chat.php',
    'routes/web.php',
    'resources/views/admin/members.blade.php',
    'resources/views/admin/erp/index.blade.php',
    'resources/views/admin/erp/tools.blade.php',
    'resources/views/admin/erp/roles.blade.php',
    'resources/views/admin/erp/chat_relations.blade.php',
    'resources/views/layouts/admin.blade.php',
    'resources/views/layouts/app.blade.php',
    'resources/views/member/finances.blade.php',
    'resources/views/member/dashboard.blade.php',
    'resources/views/member/chat_room.blade.php',
    'resources/views/admin/dividends/certificate_print.blade.php',
    'database/migrations/2026_06_05_100000_create_chat_relations_table.php',
    'database/migrations/2026_06_05_110000_add_attachments_to_chats_table.php'
]

try:
    transport = paramiko.Transport((host, port))
    transport.connect(username=username, password=password)
    sftp = paramiko.SFTPClient.from_transport(transport)

    for file_path in files_to_upload:
        local_path = os.path.join(local_base, file_path).replace('\\', '/')
        remote_path = f"{remote_base}/{file_path}"
        
        remote_dir = os.path.dirname(remote_path)
        try:
            sftp.stat(remote_dir)
        except IOError:
            print(f"Creating remote directory: {remote_dir}")
            # Try to create parent dirs
            parts = file_path.split('/')[:-1]
            current_dir = remote_base
            for part in parts:
                current_dir = f"{current_dir}/{part}"
                try:
                    sftp.stat(current_dir)
                except IOError:
                    sftp.mkdir(current_dir)
            
        print(f"Uploading {local_path} to {remote_path}...")
        sftp.put(local_path, remote_path)

    sftp.close()
    transport.close()
    print("Upload completed successfully.")
except Exception as e:
    print(f"An error occurred: {e}")
