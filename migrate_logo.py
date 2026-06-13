import os
import sys

# Change to the application directory
app_dir = r"d:\Jostru Community Sistem\Jostru_community"
os.chdir(app_dir)

# Create a temporary PHP script to run the migration
php_script = """<?php
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
        echo "Successfully added 'logo' to divisions.\\n";
    } else {
        echo "'logo' already exists in divisions.\\n";
    }
} catch (\\Exception $e) {
    echo "Error: " . $e->getMessage() . "\\n";
}
"""

with open("temp_migrate_logo.php", "w") as f:
    f.write(php_script)

import subprocess
result = subprocess.run(["php", "temp_migrate_logo.php"], capture_output=True, text=True)
print(result.stdout)
if result.stderr:
    print("Error:", result.stderr)

os.remove("temp_migrate_logo.php")
