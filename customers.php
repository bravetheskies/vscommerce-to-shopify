<?php

include('functions/csv.php');

$input_args = getopt('f:');

if (!$input_args['f']) {
   die("No file selected, use -f filename.csv\n");
}

$input_filename = $input_args['f'];
$output_filename = "shopify_customers_" . $input_filename;

$data = getCsvAsAssArray($input_filename);
$export = array(array(
    'ID',
    'Email',
    'Command',
    'First Name',
    'Last Name',
    'Phone',
    'State',
    'Accepts Marketing',
    'Created At',
    'Updated At',
    'Note',
    'Verified Email',
    'Tax Exempt',
    'Tags',
    'Tags Command',
    'Total Spent',
    'Total Orders',
    'Last Order: Name',
    'Row #',
    'Top Row',
    'Address ID',
    'Address Command',
    'Address First Name',
    'Address Last Name',
    'Address Phone',
    'Address Company',
    'Address Line 1',
    'Address Line 2',
    'Address City',
    'Address Province',
    'Address Province Code',
    'Address Country',
    'Address Country Code',
    'Address Zip',
    'Address Is Default',
    'Account Activation URL',
    'Send Account Activation Email',
    'Send Welcome Email',
    'Password',
    'Multipass Identifier',
    'Metafield: checkbox_field',
    'Metafield: integer_field',
    'Metafield: string_field',
    'Metafield: birthday'
));

foreach ($data as $row) {
    
    $export[] = array(
        null, // ID
        null, // Email
        null, // Command
        null, // First Name
        null, // Last Name
        null, // Phone
        null, // State
        null, // Accepts Marketing
        null, // Created At
        null, // Updated At
        null, // Note
        null, // Verified Email
        null, // Tax Exempt
        null, // Tags
        null, // Tags Command
        null, // Total Spent
        null, // Total Orders
        null, // Last Order: Name
        null, // Row #
        null, // Top Row
        null, // Address ID
        null, // Address Command
        null, // Address First Name
        null, // Address Last Name
        null, // Address Phone
        null, // Address Company
        null, // Address Line 1
        null, // Address Line 2
        null, // Address City
        null, // Address Province
        null, // Address Province Code
        null, // Address Country
        null, // Address Country Code
        null, // Address Zip
        null, // Address Is Default
        null, // Account Activation URL
        null, // Send Account Activation Email
        null, // Send Welcome Email
        null, // Password
        null, // Multipass Identifier
        null, // Metafield: checkbox_field
        null, // Metafield: integer_field
        null, // Metafield: string_field
        null, // Metafield: birthday
    );
}

writeDataToCsv($output_filename, $export);