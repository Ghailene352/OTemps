<?php

try {
    $pdo = new PDO('mysql:host=127.0.0.1;port=3307;dbname=mon_musee', 'root', '');
    echo "OK\n";
} catch (Throwable $e) {
    echo "ERR: " . $e->getMessage() . "\n";
}

