<?php
require __DIR__.'/../vendor/autoload.php';
$app = require_once __DIR__.'/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Post;

$posts = Post::latest()->take(10)->get();
foreach ($posts as $post) {
    echo "ID: " . $post->id . " | Content: " . substr($post->content, 0, 20) . "... | MediaPath: [" . ($post->media_path ?? 'N/A') . "] | MediaType: [" . ($post->media_type ?? 'N/A') . "]\n";
}
