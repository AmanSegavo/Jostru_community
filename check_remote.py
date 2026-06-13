import paramiko

host = '145.223.108.47'
port = 65002
username = 'u380603901'
password = 'Djafu12345@#'

try:
    transport = paramiko.Transport((host, port))
    transport.connect(username=username, password=password)
    ssh = paramiko.SSHClient()
    ssh.set_missing_host_key_policy(paramiko.AutoAddPolicy())
    ssh._transport = transport
    
    script = """
    $users = \\Illuminate\\Support\\Facades\\DB::table('users')->get();
    foreach ($users as $user) {
        echo $user->id . ' - ' . $user->name . ' | Role: ' . $user->role . ' | Status: ' . $user->status . ' | Izin: ' . ($user->can_input_waste ?? 'N/A') . "\\n";
    }
    """
    
    stdin, stdout, stderr = ssh.exec_command(f'cd /home/u380603901/domains/jostru.site/public_html && php artisan tinker --execute="{script}"')
    
    print('Output:', stdout.read().decode('utf-8', 'ignore'))
    print('Error:', stderr.read().decode('utf-8', 'ignore'))
    
    ssh.close()
except Exception as e:
    print('Exception:', e)
