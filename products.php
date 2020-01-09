<?php

include('functions/csv.php');

$input_args = getopt('f:');

if (!$input_args['f']) {
   die("No file selected, use -f filename.csv\n");
}

$input_filename = $input_args['f'];
$output_filename = "shopify_" . $input_filename;

$data = getCsvAsAssArray($input_filename);
$export = array(array(
    'ID',
    'Handle',
    'Command',
    'Title',
    'Body HTML',
    'Vendor',
    'Type',
    'Tags',
    'Tags Command',
    'Updated At',
    'Published',
    'Published At',
    'Published Scope',
    'Template Suffix',
    'Custom Collections',
    'Row #',
    'Top Row',
    'Image Src',
    'Image Command',
    'Image Position',
    'Image Width',
    'Image Height',
    'Image Alt Text',
    'Variant ID',
    'Variant Command',
    'Option1 Name',
    'Option1 Value',
    'Option2 Name',
    'Option2 Value',
    'Option3 Name',
    'Option3 Value',
    'Variant Generate From Options',
    'Variant Position',
    'Variant SKU',
    'Variant Weight',
    'Variant Weight Unit',
    'Variant HS Code',
    'Variant Country of Origin',
    'Variant Price',
    'Variant Compare At Price',
    'Variant Cost',
    'Variant Requires Shipping',
    'Variant Taxable',
    'Variant Tax Code',
    'Variant Barcode',
    'Variant Image',
    'Variant Inventory Tracker',
    'Variant Inventory Policy',
    'Variant Fulfillment Service',
    'Variant Inventory Qty',
    'Variant Inventory Adjust',
    'Metafield: description_tag',
    'Metafield: title_tag',
    'Metafield: specs.range [integer]',
    'Variant Metafield: something [string]',
    'Metafield: custom.json [json_string]'
));



foreach ($data as $row) {
    $handle = trim( end( explode('/', $row['Parent Product Url']) ) );
    $images = implode(';', explode(PHP_EOL, $row['Parent Product Images']));

    var_dump($images); die();

    $export[] = array(
        null, // ID
        $handle, // Handle
        'UPDATE', // Command
        null, // Title'
        $row['Product Summary'], // Body HTML
        $row['Brand'], // Vendor
        null, // Type
        null, // Tags
        null, // Tags Command
        null, // Updated At
        null, // Published
        null, // Published At
        null, // Published Scope
        null, // Template Suffix
        null, // Custom Collections
        null, // Row #
        null, // Top Row
        $images, // Image Src
        null, // Image Command
        null, // Image Position
        null, // Image Width
        null, // Image Height
        null, // Image Alt Text
        null, // Variant ID
        null, // Variant Command
        null, // Option1 Name
        null, // Option1 Value
        null, // Option2 Name
        null, // Option2 Value
        null, // Option3 Name
        null, // Option3 Value
        null, // Variant Generate From Options
        null, // Variant Position
        null, // Variant SKU
        null, // Variant Weight
        null, // Variant Weight Unit
        null, // Variant HS Code
        null, // Variant Country of Origin
        null, // Variant Price
        null, // Variant Compare At Price
        null, // Variant Cost
        null, // Variant Requires Shipping
        null, // Variant Taxable
        null, // Variant Tax Code
        null, // Variant Barcode
        null, // Variant Image
        null, // Variant Inventory Tracker
        null, // Variant Inventory Policy
        null, // Variant Fulfillment Service
        null, // Variant Inventory Qty
        null, // Variant Inventory Adjust
        null, // Metafield: description_tag
        null, // Metafield: title_tag
        null, // Metafield: specs.range [integer]
        null, // Variant Metafield: something [string]
        null, // Metafield: custom.json [json_string]
    );
}

writeDataToCsv($output_filename, $export);