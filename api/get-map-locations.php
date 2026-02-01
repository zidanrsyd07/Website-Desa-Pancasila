<?php
require_once '../config/config.php';

header('Content-Type: application/json');

$query = "
    SELECT 
        id, 
        nama, 
        kategori, 
        alamat, 
        latitude, 
        longitude, 
        deskripsi 
    FROM map_locations 
    WHERE LOWER(status) = 'aktif'
    ORDER BY nama ASC
";

$result = $conn->query($query);

if (!$result) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'Query error: ' . $conn->error
    ]);
    exit;
}

$locations = [];

$categoryEmojis = [
    'kantor'    => '🏛️',
    'sekolah'   => '🏫',
    'kesehatan' => '🏥',
    'ibadat'    => '🕌',
    'lainnya'   => '📍'
];

while ($row = $result->fetch_assoc()) {
    $kategori = strtolower(trim($row['kategori']));
    $row['emoji'] = $categoryEmojis[$kategori] ?? '📍';
    $locations[] = $row;
}

echo json_encode([
    'success'   => true,
    'locations' => $locations,
    'count'     => count($locations)
]);
