import paramiko

host = "145.223.108.47"
port = 65002
username = "u380603901"
password = "Farm12345@#"

try:
    client = paramiko.SSHClient()
    client.set_missing_host_key_policy(paramiko.AutoAddPolicy())
    client.connect(host, port=port, username=username, password=password)
    
    print("Mengecek file finances.blade.php di server...")
    stdin, stdout, stderr = client.exec_command("grep -C 1 'bi-pencil' /home/u380603901/domains/jostru.site/public_html/resources/views/admin/finances.blade.php")
    print(stdout.read().decode())
    
    print("Mengecek file rabs.blade.php di server...")
    stdin, stdout, stderr = client.exec_command("grep -C 1 'Progres Serapan Dana' /home/u380603901/domains/jostru.site/public_html/resources/views/admin/rabs.blade.php")
    print(stdout.read().decode())
    
    print("Mengecek file layouts/admin.blade.php di server...")
    stdin, stdout, stderr = client.exec_command("grep -C 1 'strtolower(auth()->user()->role) === \\'admin\\'' /home/u380603901/domains/jostru.site/public_html/resources/views/layouts/admin.blade.php")
    print(stdout.read().decode())

    client.close()
except Exception as e:
    print(e)
