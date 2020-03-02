<?php

include('functions/csv.php');

$input_args = getopt('s::f:');

if (!$input_args['f']) {
   die("No file selected, use -f filename.csv\n");
}

if (isset($input_args['s'])) {
    echo "Split tag provided, splitting name into Vendor and Title by '-'" . PHP_EOL;
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
    //'Metafield: specs.range [integer]',
    //'Variant Metafield: something [string]',
    //'Metafield: custom.json [json_string]'
));

foreach ($data as $row) {
    $url = explode('/', $row['Parent Product Url']);
    $handle = trim( end( $url ) );
    $images = implode(';', explode(PHP_EOL, $row['Parent Product Images']));
    $categories = explode(',', $row['Categories']);
    $categories_parts = explode('>', reset( $categories ));
    $product_type = trim( end( $categories_parts ) );

    $option1name = null;
    $option1value = null;

    if ($row['Attribute 1 (Size)']) {
        $option1name = 'Size';
        $option1value = $row['Attribute 1 (Size)'];
    }

    # Name and vendor
    $product_title = $row['Parent Product Title'];
    $vendor = $row['Brand'];
    if (isset($input_args['s'])) {
        $split_text = " - ";
        $nameandvendor = explode($split_text, $row['Parent Product Title']);
        if (isset($nameandvendor[1])) {
            $vendor = $nameandvendor[0];
            $product_title = $nameandvendor[1];
        }
    }

    # Tags
    $tag_array = array();

    ## Colour tags
    if ($row['Tag 2 (Colour)']) {
        $colour_tags = explode( ',', $row['Tag 2 (Colour)']);

        foreach ($colour_tags as &$value) {
            $tag_array[] = 'Colour:' . trim($value);
        }
    }

    ## Style tags
    if ($row['Tag 1 (Style)']) {
        $colour_tags = explode( ',', $row['Tag 1 (Style)']);

        foreach ($colour_tags as &$value) {
            $tag_array[] = 'Style:' . trim($value);
        }
    }

    ## Size tags
    if ($row['Attribute 1 (Size)']) {
        $tag_array[] = 'Size:' . trim($row['Attribute 1 (Size)']);
    }

    # Publish
    if ($row['Parent Active'] == 'Y') {
        $published = 'TRUE';
        $published_scope = 'global';
    } else {
        $published = 'FALSE';
        $published_scope = 'web';
    }

    # Tax
    if ($row['VAT Rate'] > 0 ) {
        $variant_taxable = 'TRUE';
    } else {
        $variant_taxable = 'FALSE';
    }

    $tags = implode( ', ', $tag_array );

    $export[] = array(
        null, // ID
        $handle, // Handle
        'UPDATE', // Command
        $product_title, // Title
        $row['Product Summary'] . "\n" . $row['Product Description'], // Body HTML
        $vendor, // Vendor
        $product_type, // Type
        $tags, // Tags
        'REPLACE', // Tags Command
        null, // Updated At
        $published, // Published
        null, // Published At
        $published_scope, // Published Scope
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
        'TRUE', // Variant Requires Shipping
        $variant_taxable, // Variant Taxable
        null, // Variant Tax Code
        $row['EAN'], // Variant Barcode
        null, // Variant Image
        'shopify', // Variant Inventory Tracker
        'deny', // Variant Inventory Policy
        'manual', // Variant Fulfillment Service
        $row['Stock Value'], // Variant Inventory Qty
        null, // Variant Inventory Adjust
        $row['Meta Description'], // Metafield: description_tag
        $row['Meta Title'], // Metafield: title_tag
        //null, // Metafield: specs.range [integer]
        //null, // Variant Metafield: something [string]
        //null, // Metafield: custom.json [json_string]
    );
}

writeDataToCsv($output_filename, $export);

echo "File " . $output_filename . " written." . PHP_EOL;