<?php
require_once '../../config/config.php';

if (!isLoggedIn() || !isAdmin()) {
    redirect('../../auth/login.php');
}

$gallery_id = $_GET['id'] ?? 0;

// Get gallery data
$query = "SELECT * FROM gallery WHERE id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $gallery_id);
$stmt->execute();
$result = $stmt->get_result();
$gallery = $result->fetch_assoc();

if (!$gallery) {
    redirect('gallery.php');
}

// Delete gallery
$delete_query = "DELETE FROM gallery WHERE id = ?";
$delete_stmt = $conn->prepare($delete_query);
$delete_stmt->bind_param("i", $gallery_id);

if ($delete_stmt->execute()) {
    // Delete image file
    if ($gallery['gambar'] && file_exists('../../uploads/gallery/' . $gallery['gambar'])) {
        unlink('../../uploads/gallery/' . $gallery['gambar']);
    }
    redirect('gallery.php');
} else {
    redirect('gallery.php');
}
?>
