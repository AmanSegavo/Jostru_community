import paramiko
import os

host = "145.223.108.47"
port = 65002
username = "u380603901"
password = "Anakancol12345@#"
remote_base = "/home/u380603901/domains/jostru.site/public_html"

files_to_upload = [
    "resources/views/layouts/app.blade.php",
    "public/css/style.css",
    "resources/views/layouts/admin.blade.php",
    "resources/views/admin/settings.blade.php",
    "resources/views/admin/waste_categories.blade.php",
    "resources/views/member/profile.blade.php",
    "resources/views/admin/waste_deposits.blade.php"
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
                print(f"Directory {remote_dir} does not exist. Try creating it.")
                try:
                    sftp.mkdir(remote_dir)
                except Exception as e:
                    pass
            
            sftp.put(local_path, remote_path)
            print("OK")
            
        sftp.close()
        transport.close()
        print("All UI files uploaded successfully!")
    except Exception as e:
        print(f"Error: {e}")

if __name__ == "__main__":
    upload_files()
