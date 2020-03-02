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

$redirects = array();

foreach ($data as $row) {
    $redirect_from = $row['Parent Product Url'];
    $url = explode('/', $row['Parent Product Url']);
    $handle = trim( end( $url ) );
    $redirect_to = "/products/" . $handle;

    $categories = explode(',', $row['Categories']);
    $categories_parts = explode('>', reset( $categories ));

    array_pop($url);
    $last_uri = null;

    foreach ($categories_parts as $index => $category) {
        if (isset($url[$index + 1])) {
            $redirect_from_category = $last_uri . "/" . $url[$index + 1];
            $redirects[$redirect_from_category] = "/collections/" . preg_replace("/[^\w]+/", "-", strtolower(trim($category)));
            $last_uri = $redirect_from_category;
        }
    }

    if (!isset($redirects[$redirect_from])) {
        $redirects[$redirect_from] = $redirect_to;
    }
}

foreach ($redirects as $redirect_from => $redirect_to) {
    $export[] = array(
        $redirect_from, // Redirct from
        $redirect_to, // Redirect to
    );
}

writeDataToCsv($output_filename, $export);

echo "File " . $output_filename . " written." . PHP_EOL;