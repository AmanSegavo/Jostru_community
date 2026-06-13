import paramiko
import sys

host = "145.223.108.47"
port = 65002
username = "u380603901"
password = "Farm12345@#"

def run_cmd(cmd):
    try:
        client = paramiko.SSHClient()
        client.set_missing_host_key_policy(paramiko.AutoAddPolicy())
        client.connect(host, port, username, password, timeout=10)
        
        print(f"Running: {cmd}")
        stdin, stdout, stderr = client.exec_command(cmd)
        
        out = stdout.read().decode('utf-8', errors='replace')
        err = stderr.read().decode('utf-8', errors='replace')
        
        if out:
            print("STDOUT:")
            print(out)
        if err:
            print("STDERR:")
            print(err)
            
        client.close()
    except Exception as e:
        print(f"Error: {e}")

if __name__ == "__main__":
    if len(sys.argv) > 1:
        run_cmd(sys.argv[1])
    else:
        run_cmd("php /home/u380603901/domains/jostru.site/public_html/add_permissions_and_dividends.php")
