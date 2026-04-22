<?php
require 'vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$messages = App\Models\Message::where('receiver_id', 3)->where('status', 'approved')->paginate(15);
echo json_encode($messages->toArray(), JSON_PRETTY_PRINT);
