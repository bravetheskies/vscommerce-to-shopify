<?php

include('functions/csv.php');

$input_args = getopt('f:');

if (!$input_args['f']) {
   die("No file selected, use -f filename.csv\n");
}

$input_filename = $input_args['f'];
$output_filename = "shopify_products_" . $input_filename;

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
    $product_type = trim( end( explode('>', reset( explode( ',', $row['Categories'] ) ) ) ) );

    $option1name = null;
    $option1value = null;

    if ($row['Attribute 1 (Size)']) {
        $option1name = 'Size';
        $option1value = $row['Attribute 1 (Size)'];
    }

    $export[] = array(
        null, // ID
        $handle, // Handle
        'UPDATE', // Command
        $row['Parent Product Title'], // Title
        $row['Product Summary'], // Body HTML
        $row['Brand'], // Vendor
        $product_type, // Type
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
        'MERGE', // Variant Command
        $option1name, // Option1 Name
        $option1value, // Option1 Value
        null, // Option2 Name
        null, // Option2 Value
        null, // Option3 Name
        null, // Option3 Value
        null, // Variant Generate From Options
        null, // Variant Position
        $row['Child Reference'], // Variant SKU
        $row['Weight (in KGs)'], // Variant Weight
        'kg', // Variant Weight Unit
        null, // Variant HS Code
        null, // Variant Country of Origin
        $row['Sale Price (Inc VAT)'], // Variant Price
        $row['Price (Inc VAT)'], // Variant Compare At Price
        $row['Cost Price (Inc VAT)'], // Variant Cost
        null, // Variant Requires Shipping
        null, // Variant Taxable
        null, // Variant Tax Code
        $row['EAN'], // Variant Barcode
        null, // Variant Image
        null, // Variant Inventory Tracker
        null, // Variant Inventory Policy
        null, // Variant Fulfillment Service
        $row['Stock Value'], // Variant Inventory Qty
        null, // Variant Inventory Adjust
        $row['Meta Description'], // Metafield: description_tag
        $row['Meta Title'], // Metafield: title_tag
        null, // Metafield: specs.range [integer]
        null, // Variant Metafield: something [string]
        null, // Metafield: custom.json [json_string]
    );
}

writeDataToCsv($output_filename, $export);