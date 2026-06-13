import paramiko
client=paramiko.SSHClient()
client.set_missing_host_key_policy(paramiko.AutoAddPolicy())
client.connect('145.223.108.47', port=65002, username='u380603901', password='Farm12345@#')
stdin, stdout, stderr = client.exec_command('cat /home/u380603901/domains/jostru.site/public_html/resources/views/admin/finances.blade.php | grep editFinanceModal | wc -l')
print("Count:", stdout.read().decode().strip())
client.close()
