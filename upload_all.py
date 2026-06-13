import paramiko
import os

host = "145.223.108.47"
port = 65002
username = "u380603901"
password = "Anakancol12345@#"
remote_base = "/home/u380603901/domains/jostru.site/public_html"

files_to_upload = [
    "database/migrations/2026_06_01_000000_create_system_settings_table.php",
    "database/migrations/2026_06_01_000001_create_waste_categories_table.php",
    "database/migrations/2026_06_01_000002_alter_users_and_waste_deposits_tables.php",
    "app/Models/SystemSetting.php",
    "app/Models/WasteCategory.php",
    "app/Models/User.php",
    "app/Models/WasteDeposit.php",
    "app/Http/Controllers/AdminController.php",
    "app/Http/Controllers/MemberController.php",
    "routes/web.php",
    "resources/views/admin/waste_categories.blade.php",
    "resources/views/admin/settings.blade.php",
    "resources/views/admin/members.blade.php",
    "resources/views/admin/waste_deposits.blade.php",
    "resources/views/member/waste_report.blade.php",
    "resources/views/layouts/admin.blade.php"
]

def upload_files():
    try:
        transport = paramiko.Transport((host, port))
        transport.connect(username=username, password=password)
        sftp = paramiko.SFTPClient.from_transport(transport)
        
        for file in files_to_upload:
            local_path = file
            remote_path = f"{remote_base}/{file}"
            print(f"Uploading {local_path} to {remote_path}...")
            
            # Ensure remote directory exists
            remote_dir = os.path.dirname(remote_path)
            try:
                sftp.stat(remote_dir)
            except FileNotFoundError:
                print(f"Directory {remote_dir} does not exist. Please create it first (manually or modify script).")
                # Try simple mkdir, this might fail if multiple parent dirs are missing but works for 1 level
                try:
                    sftp.mkdir(remote_dir)
                except Exception as e:
                    print(f"Could not mkdir {remote_dir}: {e}")
            
            sftp.put(local_path, remote_path)
            print("OK")
            
        sftp.close()
        transport.close()
        print("All files uploaded successfully!")
    except Exception as e:
        print(f"Error: {e}")

if __name__ == "__main__":
    upload_files()
