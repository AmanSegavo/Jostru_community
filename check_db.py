import paramiko

host = "145.223.108.47"
port = 65002
username = "u380603901"
password = "Farm12345@#"

try:
    client = paramiko.SSHClient()
    client.set_missing_host_key_policy(paramiko.AutoAddPolicy())
    client.connect(host, port=port, username=username, password=password)
    
    script = """
    $finances = Schema::getColumnListing('finances');
    $rabs = Schema::getColumnListing('rabs');
    dump($finances);
    dump($rabs);
    """
    
    stdin, stdout, stderr = client.exec_command(f'php /home/u380603901/domains/jostru.site/public_html/artisan tinker --execute="{script}"')
    
    print(stdout.read().decode())
    print(stderr.read().decode())
    client.close()
except Exception as e:
    print(e)
