import paramiko

host = '145.223.108.47'
port = 65002
username = 'u380603901'
password = 'Farm12345@#'
remote_base = '/home/u380603901/domains/jostru.site/public_html'

migration_code = """<?php
use Illuminate\\Support\\Facades\\Schema;
use Illuminate\\Database\\Schema\\Blueprint;
use Illuminate\\Support\\Str;
use App\\Models\\Division;
use App\\Models\\Gallery;

if (!Schema::hasColumn('divisions', 'slug')) {
    Schema::table('divisions', function (Blueprint $table) {
        $table->string('slug')->nullable()->unique()->after('name');
        $table->text('about_text')->nullable();
        $table->json('faq_data')->nullable();
        $table->string('phone_number')->nullable();
        $table->string('email')->nullable();
        $table->string('address')->nullable();
        $table->string('meta_keywords')->nullable();
        $table->text('meta_description')->nullable();
    });
    
    foreach(Division::all() as $div) {
        $div->update(['slug' => Str::slug($div->name)]);
    }
}

if (!Schema::hasColumn('galleries', 'division_id')) {
    Schema::table('galleries', function (Blueprint $table) {
        $table->foreignId('division_id')->nullable()->constrained('divisions')->onDelete('set null');
        $table->string('orientation')->default('landscape');
    });
    
    $farm = Division::where('name', 'like', '%Farm%')->first();
    if ($farm) {
        Gallery::whereNull('division_id')->update(['division_id' => $farm->id]);
    }
}
echo "Database migration applied successfully!\\n";
"""

try:
    ssh = paramiko.SSHClient()
    ssh.set_missing_host_key_policy(paramiko.AutoAddPolicy())
    ssh.connect(host, port, username, password)
    
    sftp = ssh.open_sftp()
    with sftp.file(f'{remote_base}/tmp_migrate.php', 'w') as f:
        f.write(migration_code)
    sftp.close()
    
    stdin, stdout, stderr = ssh.exec_command(f'cd {remote_base} && php artisan tinker tmp_migrate.php')
    print('Output:', stdout.read().decode())
    print('Errors:', stderr.read().decode())
    
    ssh.exec_command(f'rm {remote_base}/tmp_migrate.php')
    ssh.close()
except Exception as e:
    print(f'Error: {e}')
