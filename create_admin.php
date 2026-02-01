<?php
require_once '../config/config.php';

$password = password_hash('admin123', PASSWORD_DEFAULT);

$sql = "INSERT INTO users (username, email, password, full_name, role)
        VALUES ('admin', 'admin@desa.id', ?, 'Administrator', 'admin')";

$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $password);

if ($stmt->execute()) {
    echo "Admin berhasil dibuat";
} else {
    echo "Gagal: " . $stmt->error;
}
