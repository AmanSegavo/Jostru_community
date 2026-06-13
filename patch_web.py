import paramiko

client=paramiko.SSHClient()
client.set_missing_host_key_policy(paramiko.AutoAddPolicy())
client.connect('145.223.108.47', port=65002, username='u380603901', password='Farm12345@#', timeout=10)
sftp=client.open_sftp()
sftp.get('/home/u380603901/domains/jostru.site/public_html/routes/web.php', 'routes/web.php')
sftp.close()
client.close()

with open('routes/web.php', 'r', encoding='utf-8') as f:
    lines = f.readlines()

new_lines = []
for line in lines:
    new_lines.append(line)
    if "Route::post('/divisions/{id}/finances', [\App\Http\Controllers\DivisionController::class, 'storeFinance'])->name('admin.divisions.finances.store');" in line:
        new_lines.append("        Route::put('/divisions/{id}/finances/{finance_id}', [\App\Http\Controllers\DivisionController::class, 'updateFinance'])->name('admin.divisions.finances.update');\n")
        new_lines.append("        Route::delete('/divisions/{id}/finances/{finance_id}', [\App\Http\Controllers\DivisionController::class, 'destroyFinance'])->name('admin.divisions.finances.destroy');\n")

with open('routes/web.php', 'w', encoding='utf-8') as f:
    f.writelines(new_lines)

print('Recovered and updated web.php successfully!')
