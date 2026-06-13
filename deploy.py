import paramiko
import sys
import os

host = "145.223.108.47"
port = 65002
username = "u380603901"
password = "Farm12345@#"

files_to_upload = [
    ("add_certificate_security.php", "/home/u380603901/domains/jostru.site/public_html/add_certificate_security.php"),
    ("app/Models/Shareholder.php", "/home/u380603901/domains/jostru.site/public_html/app/Models/Shareholder.php"),
    ("app/Http/Controllers/AdminController.php", "/home/u380603901/domains/jostru.site/public_html/app/Http/Controllers/AdminController.php"),
    ("app/Http/Controllers/DividendController.php", "/home/u380603901/domains/jostru.site/public_html/app/Http/Controllers/DividendController.php"),
    ("app/Http/Controllers/MemberController.php", "/home/u380603901/domains/jostru.site/public_html/app/Http/Controllers/MemberController.php"),
    ("routes/web.php", "/home/u380603901/domains/jostru.site/public_html/routes/web.php"),
    ("resources/views/admin/dividends/index.blade.php", "/home/u380603901/domains/jostru.site/public_html/resources/views/admin/dividends/index.blade.php"),
    ("resources/views/admin/dividends/scanner.blade.php", "/home/u380603901/domains/jostru.site/public_html/resources/views/admin/dividends/scanner.blade.php"),
    ("resources/views/verify_certificate.blade.php", "/home/u380603901/domains/jostru.site/public_html/resources/views/verify_certificate.blade.php"),
    ("resources/views/member/dashboard.blade.php", "/home/u380603901/domains/jostru.site/public_html/resources/views/member/dashboard.blade.php"),
    ("resources/views/member/profile.blade.php", "/home/u380603901/domains/jostru.site/public_html/resources/views/member/profile.blade.php"),
    ("resources/views/member/card_auth.blade.php", "/home/u380603901/domains/jostru.site/public_html/resources/views/member/card_auth.blade.php"),
    ("app/Http/Controllers/AuthController.php", "/home/u380603901/domains/jostru.site/public_html/app/Http/Controllers/AuthController.php"),
    ("resources/views/member/card_editor.blade.php", "/home/u380603901/domains/jostru.site/public_html/resources/views/member/card_editor.blade.php"),
    ("resources/views/admin/cards.blade.php", "/home/u380603901/domains/jostru.site/public_html/resources/views/admin/cards.blade.php"),
    ("resources/views/layouts/app.blade.php", "/home/u380603901/domains/jostru.site/public_html/resources/views/layouts/app.blade.php"),
    ("resources/views/layouts/admin.blade.php", "/home/u380603901/domains/jostru.site/public_html/resources/views/layouts/admin.blade.php"),
    ("resources/views/admin/members.blade.php", "/home/u380603901/domains/jostru.site/public_html/resources/views/admin/members.blade.php"),
    ("resources/views/admin/dashboard.blade.php", "/home/u380603901/domains/jostru.site/public_html/resources/views/admin/dashboard.blade.php"),
    ("add_2fa_card.php", "/home/u380603901/domains/jostru.site/public_html/add_2fa_card.php"),
    ("resources/views/welcome.blade.php", "/home/u380603901/domains/jostru.site/public_html/resources/views/welcome.blade.php"),
    ("database/migrations/2026_06_07_120000_create_debts_table.php", "/home/u380603901/domains/jostru.site/public_html/database/migrations/2026_06_07_120000_create_debts_table.php"),
    ("database/migrations/2026_06_07_120001_create_permission_delegations_table.php", "/home/u380603901/domains/jostru.site/public_html/database/migrations/2026_06_07_120001_create_permission_delegations_table.php"),
    ("app/Models/Debt.php", "/home/u380603901/domains/jostru.site/public_html/app/Models/Debt.php"),
    ("app/Models/PermissionDelegation.php", "/home/u380603901/domains/jostru.site/public_html/app/Models/PermissionDelegation.php"),
    ("app/Models/User.php", "/home/u380603901/domains/jostru.site/public_html/app/Models/User.php"),
    ("resources/views/admin/finances.blade.php", "/home/u380603901/domains/jostru.site/public_html/resources/views/admin/finances.blade.php"),
    ("resources/views/member/finances.blade.php", "/home/u380603901/domains/jostru.site/public_html/resources/views/member/finances.blade.php"),
    ("resources/views/admin/delegations.blade.php", "/home/u380603901/domains/jostru.site/public_html/resources/views/admin/delegations.blade.php"),
    ("resources/views/member/delegations.blade.php", "/home/u380603901/domains/jostru.site/public_html/resources/views/member/delegations.blade.php"),
    ("run_migration.php", "/home/u380603901/domains/jostru.site/public_html/run_migration.php"),
    ("database/migrations/2026_06_07_133000_create_data_lake_records_table.php", "/home/u380603901/domains/jostru.site/public_html/database/migrations/2026_06_07_133000_create_data_lake_records_table.php"),
    ("app/Models/DataLakeRecord.php", "/home/u380603901/domains/jostru.site/public_html/app/Models/DataLakeRecord.php"),
    ("app/Http/Controllers/DataLakeController.php", "/home/u380603901/domains/jostru.site/public_html/app/Http/Controllers/DataLakeController.php"),
    ("resources/views/admin/data_lake/index.blade.php", "/home/u380603901/domains/jostru.site/public_html/resources/views/admin/data_lake/index.blade.php"),
    ("resources/views/admin/data_lake/ingest.blade.php", "/home/u380603901/domains/jostru.site/public_html/resources/views/admin/data_lake/ingest.blade.php"),
    ("sync_data_lake.php", "/home/u380603901/domains/jostru.site/public_html/sync_data_lake.php"),
    ("database/migrations/2026_06_07_140000_create_settings_table.php", "/home/u380603901/domains/jostru.site/public_html/database/migrations/2026_06_07_140000_create_settings_table.php"),
    ("app/Models/Setting.php", "/home/u380603901/domains/jostru.site/public_html/app/Models/Setting.php"),
    ("app/Http/Controllers/SettingsController.php", "/home/u380603901/domains/jostru.site/public_html/app/Http/Controllers/SettingsController.php"),
    ("resources/views/admin/settings.blade.php", "/home/u380603901/domains/jostru.site/public_html/resources/views/admin/settings.blade.php"),
    ("app/Services/ChatbotService.php", "/home/u380603901/domains/jostru.site/public_html/app/Services/ChatbotService.php"),
    ("app/Http/Controllers/ChatbotController.php", "/home/u380603901/domains/jostru.site/public_html/app/Http/Controllers/ChatbotController.php"),
    ("app/Http/Controllers/SharedReportController.php", "/home/u380603901/domains/jostru.site/public_html/app/Http/Controllers/SharedReportController.php"),
    ("resources/views/public/shared_finance.blade.php", "/home/u380603901/domains/jostru.site/public_html/resources/views/public/shared_finance.blade.php"),
    ("resources/views/public/shared_datalake.blade.php", "/home/u380603901/domains/jostru.site/public_html/resources/views/public/shared_datalake.blade.php"),
]

def deploy():
    try:
        transport = paramiko.Transport((host, port))
        transport.connect(username=username, password=password)
        sftp = paramiko.SFTPClient.from_transport(transport)
        
        for local, remote in files_to_upload:
            print(f"Uploading {local} to {remote}...")
            remote_dir = os.path.dirname(remote)
            
            # Create remote directory structure if it doesn't exist
            dirs = remote_dir.split('/')
            current_dir = ''
            for d in dirs:
                if not d: continue
                current_dir += '/' + d
                try:
                    sftp.stat(current_dir)
                except IOError:
                    try:
                        sftp.mkdir(current_dir)
                    except IOError:
                        pass
                        
            sftp.put(local, remote)
            print("OK.")
            
        sftp.close()
        transport.close()
        print("All uploads finished!")
    except Exception as e:
        print(f"Error: {e}")

if __name__ == "__main__":
    deploy()
