import paramiko

host = '145.223.108.47'
port = 65002
username = 'u380603901'
password = 'Anakancol12345@#'
try:
    transport = paramiko.Transport((host, port))
    transport.connect(username=username, password=password)
    ssh = paramiko.SSHClient()
    ssh.set_missing_host_key_policy(paramiko.AutoAddPolicy())
    ssh._transport = transport
    cmd = """cd /home/u380603901/domains/jostru.site/public_html && php artisan tinker --execute="
$migrator = app('migrator');
$migrator->run(database_path('migrations'), ['path' => 'database/migrations/2026_06_01_000003_create_erp_tables.php']);
" """
    stdin, stdout, stderr = ssh.exec_command(cmd)
    output = stdout.read().decode('utf-8', errors='replace')
    err_output = stderr.read().decode('utf-8', errors='replace')
    with open('migrate_output2.txt', 'w', encoding='utf-8') as f:
        f.write('STDOUT:\n' + output + '\nSTDERR:\n' + err_output)
    print('Migrate 2 done')
    ssh.close()
except Exception as e:
    print('Error:', e)
