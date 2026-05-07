<?php
require __DIR__.'/../vendor/autoload.php';
$app = require_once __DIR__.'/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Post;

$posts = Post::latest()->take(5)->get();
foreach ($posts as $post) {
    echo "ID: " . $post->id . " | Content: " . $post->content . " | Path: [" . $post->image_path . "]\n";
}
