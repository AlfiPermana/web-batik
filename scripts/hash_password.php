<?php
$password = 'password123';
$hash = password_hash($password, PASSWORD_BCRYPT);
echo "Hashed password: " . $hash . "\n";
?>
