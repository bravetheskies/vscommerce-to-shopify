<?php

include('functions/csv.php');

$input_args = getopt('s::f:');

if (!$input_args['f']) {
   die("No file selected, use -f filename.csv\n");
}

$input_filename = $input_args['f'];
$output_filename = "rewrites_" . $input_filename;

$data = getCsvAsAssArray($input_filename);
$export = array(array(
    'Redirect from',
    'Redirect to'
));

foreach ($data as $row) {
    $redirect_from = $row['Parent Product Url'];
    $url = explode('/', $row['Parent Product Url']);
    $handle = trim( end( $url ) );
    $redirect_to = "/products/" . $handle;

    $export[] = array(
        $row['Parent Product Url'], // Redirct from
        $redirect_to, // Redirect to
    );
}

writeDataToCsv($output_filename, $export);

echo "File " . $output_filename . " written." . PHP_EOL;