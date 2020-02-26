<?php

include('functions/csv.php');

$input_args = getopt('s::f:');

if (!$input_args['f']) {
   die("No file selected, use -f filename.csv\n");
}

$input_filename = $input_args['f'];
$output_filename = "shopify_products_stock_" . $input_filename;

$data = getCsvAsAssArray($input_filename);
$export = array(array(
    'Variant SKU',
    'Variant Inventory Adjust'
));

foreach ($data as $row) {
    $url = explode('/', $row['Parent Product Url']);
    $handle = trim( end( $url ) );

    $export[] = array(
        $row['Child Reference'], // Variant SKU
        $row['Stock Value'] // Variant Inventory Adjust
    );
}

writeDataToCsv($output_filename, $export);

echo "File " . $output_filename . " written." . PHP_EOL;