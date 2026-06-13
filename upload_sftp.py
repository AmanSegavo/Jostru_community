import paramiko
import sys
import os

host = "145.223.108.47"
port = 65002
username = "u380603901"
password = "Djafu12345@#"

def upload_file(local_path, remote_path):
    try:
        transport = paramiko.Transport((host, port))
        transport.connect(username=username, password=password)
        sftp = paramiko.SFTPClient.from_transport(transport)
        
        print(f"Uploading {local_path} to {remote_path}...")
        sftp.put(local_path, remote_path)
        print("Upload successful!")
        
        sftp.close()
        transport.close()
    except Exception as e:
        print(f"Error: {e}")

if __name__ == "__main__":
    if len(sys.argv) == 3:
        upload_file(sys.argv[1], sys.argv[2])
    else:
        print("Usage: python upload_sftp.py <local_path> <remote_path>")
