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
    try {
        \\Illuminate\\Support\\Facades\\DB::statement('ALTER TABLE waste_deposits ADD COLUMN latitude DECIMAL(10,8) NULL AFTER file_size, ADD COLUMN longitude DECIMAL(11,8) NULL AFTER latitude;');
    } catch (\\Exception $e) {
        echo "Waste deposit cols exist or error: " . $e->getMessage() . "\\n";
    }

    try {
        \\Illuminate\\Support\\Facades\\Schema::create('notifications', function ($table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('title');
            $table->text('message');
            $table->string('url')->nullable();
            $table->boolean('is_read')->default(false);
            $table->timestamps();
        });
        echo "Notifications table created.\\n";
    } catch (\\Exception $e) {
        echo "Notifications table exist or error: " . $e->getMessage() . "\\n";
    }
    """
    
    stdin, stdout, stderr = ssh.exec_command(f'cd /home/u380603901/domains/jostru.site/public_html && php artisan tinker --execute="{script}"')
    
    print('Tinker Output:', stdout.read().decode('utf-8', 'ignore'))
    print('Tinker Errors:', stderr.read().decode('utf-8', 'ignore'))
    
    ssh.close()
except Exception as e:
    print('Exception:', e)
