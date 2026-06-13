import paramiko
import os

host = '145.223.108.47'
port = 65002
username = 'u380603901'
password = 'Farm12345@#'
remote_base = '/home/u380603901/domains/jostru.site/public_html'

try:
    ssh = paramiko.SSHClient()
    ssh.set_missing_host_key_policy(paramiko.AutoAddPolicy())
    ssh.connect(host, port, username, password)
    
    php_code = """<?php
    require __DIR__.'/vendor/autoload.php';
    $app = require_once __DIR__.'/bootstrap/app.php';
    $kernel = $app->make(Illuminate\\Contracts\\Console\\Kernel::class);
    $kernel->bootstrap();
    
    use Illuminate\\Support\\Facades\\Schema;
    use Illuminate\\Database\\Schema\\Blueprint;
    
    try {
        if (!Schema::hasColumn('divisions', 'logo')) {
            Schema::table('divisions', function (Blueprint $table) {
                $table->string('logo')->nullable()->after('description');
            });
            echo "SUCCESS_COL_ADDED";
        } else {
            echo "COL_ALREADY_EXISTS";
        }
    } catch (\\Exception $e) {
        echo "ERROR_ADD_COL: " . $e->getMessage();
    }
    
    try {
        $division = \\App\\Models\\Division::find(1);
        if ($division) {
            $division->update([
                'description' => 'Jostru Farm adalah pelopor pertanian terpadu berbasis ekonomi sirkular yang mengubah limbah menjadi sumber daya. Kami tidak hanya mengolah limbah organik, tetapi mentransformasikannya menjadi pakan ternak berkualitas dan produk pangan sehat guna menciptakan ekosistem pertanian yang berkelanjutan dan mandiri',
                'about_text' => "Jostru Farm adalah pusat pertanian terpadu (integrated farming) yang menerapkan ekonomi sirkular. Di sini, limbah diubah menjadi energi dan siklus nutrisi. Sisa bahan organik diolah bukan sekadar untuk pupuk, melainkan menjadi pakan ternak bernutrisi dan produk pangan mandiri yang sehat dan berkelanjutan.\\n\\n1. Daur Ulang Limbah (Waste Processing)\\nPengomposan: Sisa kotoran ternak diolah menjadi pupuk kandang padat dan kompos untuk menyuburkan tanah.\\nDaur Ulang Nutrisi: Menggunakan teknologi maggot (lalat Black Soldier Fly) dan budidaya cacing untuk mempercepat penguraian limbah organik secara alami\\n\\nProduksi Pakan Alternatif (Feed Production)\\nMagat Penuh Protein: Larva maggot yang memakan limbah dapur dan pertanian dipanen untuk menjadi pakan berprotein tinggi bagi ikan, unggas, dan hewan ternak lainnya\\n\\nFermentasi Hijauan: Limbah pertanian diolah kembali melalui teknik fermentasi sederhana untuk menghasilkan pakan yang mudah dicerna hewan.\\n\\nKemandirian Pangan (Food Production)\\nHasil Panen Segar: Sayuran dan buah-buahan yang ditanam di area pertanian menjadi sumber pangan sehat bagi keluarga dan masyarakat sekitar\\n\\nSiklus Terbuka: Tanaman pangan menghasilkan sisa yang kembali menjadi pakan, kotoran hewan menjadi pupuk tanaman, membentuk ekosistem nol-limbah (zero-waste)",
                'phone_number' => '08137929313',
                'email' => 'plicocommunity@gmail.com',
                'address' => 'Jl Gotong Royong 2 lrg anggrek Wak Talang Buluh Kec Talang Klp',
                'meta_keywords' => 'peternakan, pakan, pangan, jostru, jostru farm, limbah, limbah organik, pupuk',
                'meta_description' => 'Kelola limbah rumah tangga dengan cerdas dan dapatkan poin reward bersama Jostru Farm. Bergabunglah dengan komunitas kami untuk wujudkan bumi yang lebih lestari!',
                'logo' => '1780604061_WhatsApp_Image_2026-06-05_at_01.59.38-removebg-preview.png'
            ]);
            echo " SUCCESS_DATA_RESTORED";
        }
    } catch (\\Exception $e) {
        echo " ERROR_RESTORE: " . $e->getMessage();
    }
    """
    
    with open("force_db.php", "w") as f:
        f.write(php_code)
        
    sftp = ssh.open_sftp()
    sftp.put("force_db.php", f"{remote_base}/force_db.php")
    sftp.close()
    
    stdin, stdout, stderr = ssh.exec_command(f'cd {remote_base} && php force_db.php')
    output = stdout.read()
    # Decode ignoring errors to avoid charmap crash
    print("Script output:", output.decode('utf-8', errors='ignore'))
    
    ssh.exec_command(f'rm {remote_base}/force_db.php')
    ssh.close()
    os.remove("force_db.php")
    print("All tasks completed.")
except Exception as e:
    print(f'Error: {e}')
