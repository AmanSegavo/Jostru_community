<?php
require __DIR__.'/../vendor/autoload.php';
$app = require_once __DIR__.'/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Post;
use Illuminate\Http\UploadedFile;

// Mock an upload
$file = UploadedFile::fake()->image('test_image.jpg');

$destinationPath = public_path('feed');
if (!file_exists($destinationPath)) {
    echo "Creating directory: $destinationPath\n";
    mkdir($destinationPath, 0755, true);
}

$fileName = time() . '_' . uniqid() . '.jpg';
$fullPath = $destinationPath . '/' . $fileName;

if (copy($file->getRealPath(), $fullPath)) {
    echo "File saved to: $fullPath\n";
    Post::create([
        'user_id' => 1, // Assuming admin ID is 1
        'content' => 'Test post with image',
        'media_path' => $fileName,
        'media_type' => 'image'
    ]);
    echo "Post created in database.\n";
} else {
    echo "Failed to save file.\n";
}
