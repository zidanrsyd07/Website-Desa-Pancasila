<?php
require_once '../../config/config.php';

if (!isLoggedIn() || !isAdmin()) {
    redirect('../../auth/login.php');
}

$location_id = $_GET['id'] ?? 0;

// Get location data
$query = "SELECT * FROM map_locations WHERE id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $location_id);
$stmt->execute();
$result = $stmt->get_result();
$location = $result->fetch_assoc();

if (!$location) {
    redirect('peta.php');
}

// Delete location
$delete_query = "DELETE FROM map_locations WHERE id = ?";
$delete_stmt = $conn->prepare($delete_query);
$delete_stmt->bind_param("i", $location_id);

if ($delete_stmt->execute()) {
    redirect('peta.php');
} else {
    redirect('peta.php');
}
?>
