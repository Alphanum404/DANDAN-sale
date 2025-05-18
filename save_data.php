<?php
header('Content-Type: application/json');

// Ambil data JSON dari request
$json = file_get_contents('php://input');
$products = json_decode($json, true);

if (!is_array($products)) {
    echo json_encode(['status' => 'error', 'message' => 'Invalid data format']);
    exit;
}

// Simpan ke file JSON
$filename = 'products_data.json';
file_put_contents($filename, json_encode($products, JSON_PRETTY_PRINT));

// Buat juga file CSV untuk kemudahan penggunaan data
$csvFilename = 'products_data.csv';
$csvFile = fopen($csvFilename, 'w');

// Header CSV
$headers = [
    'ID', 'Name', 'Slug', 'Price', 'Discount Value', 'Final Price',
    'Brand ID', 'Brand Name', 'Category ID', 'Category Name', 'Picture URL'
];
fputcsv($csvFile, $headers);

// Data rows
foreach ($products as $product) {
    $finalPrice = $product['discount_value'] > 0 ? 
                  $product['price'] - $product['discount_value'] : 
                  $product['price'];
                  
    $row = [
        $product['id'],
        $product['name'],
        $product['slug'],
        $product['price'],
        $product['discount_value'] ?? 0,
        $finalPrice,
        $product['brand']['id'] ?? '',
        $product['brand']['name'] ?? '',
        $product['category']['id'] ?? '',
        $product['category']['name'] ?? '',
        $product['picture']
    ];
    fputcsv($csvFile, $row);
}

fclose($csvFile);

echo json_encode([
    'status' => 'success', 
    'message' => 'Data saved successfully',
    'total_products' => count($products),
    'files' => [
        'json' => $filename,
        'csv' => $csvFilename
    ]
]);