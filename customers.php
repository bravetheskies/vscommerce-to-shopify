<?php

include('functions/csv.php');
include('functions/phone.php');
include('functions/country.php');

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
    'Multipass Identifier'
));

foreach ($data as $row) {

    # Phone
    $phone = null;
    if (validate_phone_number($row['Phone'])) {
        $phone = $row['Phone']; # TODO: Needs to do something with numbers missing a +
    }

    # Tags
    $tag_array = array();

    if ($row['Email Opt In']) {
        $tag_array[] = 'Email Opt In:' . $row['Email Opt In'];
    }
    $tag_array[] = 'Imported from vscommerce';

    $tags = implode(',', $tag_array);

    # Country
    $country = null;
    if (array_key_exists('Country', $row)) {
        $country = lookup_country($row['Country']);
    }

    $export[] = array(
        null, // ID
        $row['Email'], // Email
        'UPDATE', // Command
        $row['Firstname'], // First Name
        $row['Surname'], // Last Name
        $phone, // Phone
        null, // State
        null, // Accepts Marketing
        null, // Created At
        null, // Updated At
        null, // Note
        null, // Verified Email
        null, // Tax Exempt
        $tags, // Tags
        'REPLACE', // Tags Command
        null, // Total Spent
        null, // Total Orders
        null, // Last Order: Name
        null, // Row #
        null, // Top Row
        null, // Address ID
        null, // Address Command
        $row['Firstname'], // Address First Name
        $row['Surname'], // Address Last Name
        $phone, // Address Phone
        $row['Company Name'], // Address Company
        $row['Address1'], // Address Line 1
        $row['Address2'], // Address Line 2
        $row['Town'], // Address City
        $row['County'], // Address Province
        null, // Address Province Code
        $country, // Address Country
        null, // Address Country Code
        $row['Post Code'], // Address Zip
        null, // Address Is Default
        null, // Account Activation URL
        null, // Send Account Activation Email
        null, // Send Welcome Email
        null, // Password
        null // Multipass Identifier
    );
}

writeDataToCsv($output_filename, $export);