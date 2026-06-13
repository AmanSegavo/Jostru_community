import paramiko

host = '145.223.108.47'
port = 65002
username = 'u380603901'
password = 'Farm12345@#'

try:
    transport = paramiko.Transport((host, port))
    transport.connect(username=username, password=password)
    ssh = paramiko.SSHClient()
    ssh.set_missing_host_key_policy(paramiko.AutoAddPolicy())
    ssh._transport = transport
    cmd = '''cd /home/u380603901/domains/jostru.site/public_html && php artisan tinker --execute="
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use App\Models\Shareholder;
use App\Models\Division;

if (!Schema::hasColumn('shareholders', 'division_id')) {
    Schema::table('shareholders', function (Blueprint \$table) {
        \$table->foreignId('division_id')->nullable()->after('user_id')->constrained('divisions')->nullOnDelete();
    });
    echo 'Column added. ';
} else {
    echo 'Column already exists. ';
}

// Map existing shareholders to 'Farm' division
\$farm = Division::where('name', 'Farm')->first();
if (\$farm) {
    Shareholder::whereNull('division_id')->update(['division_id' => \$farm->id]);
    echo 'Data updated to Farm.';
}
" '''
    stdin, stdout, stderr = ssh.exec_command(cmd)
    print('Output:', stdout.read().decode())
    print('Error:', stderr.read().decode())
    ssh.close()
except Exception as e:
    print('Error:', e)
