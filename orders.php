<?php

include('functions/phone.php');
include('functions/country.php');

$input_args = getopt('f:');

if (!$input_args['f']) {
   die("No file selected, use -f filename.xml\n");
}

$input_filename = $input_args['f'];
$output_filename = "shopify_ordes_" . str_replace(".xml", ".csv", $input_filename);

$data = simplexml_load_file($input_filename);

$export = array(array(
    'ID',
    'Name',
    'Command',
    'Send Receipt',
    'Inventory Behaviour',
    'Number',
    'Note',
    'Tags',
    'Tags Command',
    'Created At',
    'Updated At',
    'Cancelled At',
    'Cancel: Reason',
    'Cancel: Send Receipt',
    'Cancel: Refund',
    'Processed At',
    'Closed At',
    'Currency',
    'Source',
    'User ID',
    'Checkout ID',
    'Cart Token',
    'Token',
    'Order Status URL',
    'Weight Total',
    'Price: Total Line Items',
    'Price: Subtotal',
    'Tax 1: Title',
    'Tax 1: Rate',
    'Tax 1: Price',
    'Tax 2: Title',
    'Tax 2: Rate',
    'Tax 2: Price',
    'Tax 3: Title',
    'Tax 3: Rate',
    'Tax 3: Price',
    'Tax: Included',
    'Tax: Total',
    'Price: Total',
    'Payment: Status',
    'Payment: Processing Method',
    'Order Fulfillment Status',
    'Additional Details',
    'Customer: ID',
    'Customer: Email',
    'Customer: Phone',
    'Customer: First Name',
    'Customer: Last Name',
    'Customer: Note',
    'Customer: Orders Count',
    'Customer: State',
    'Customer: Total Spent',
    'Customer: Tags',
    'Customer: Accepts Marketing',
    'Billing: First Name',
    'Billing: Last Name',
    'Billing: Company',
    'Billing: Phone',
    'Billing: Address 1',
    'Billing: Address 2',
    'Billing: Zip',
    'Billing: City',
    'Billing: Province',
    'Billing: Province Code',
    'Billing: Country',
    'Billing: Country Code',
    'Shipping: First Name',
    'Shipping: Last Name',
    'Shipping: Company',
    'Shipping: Phone',
    'Shipping: Address 1',
    'Shipping: Address 2',
    'Shipping: Zip',
    'Shipping: City',
    'Shipping: Province',
    'Shipping: Province Code',
    'Shipping: Country',
    'Shipping: Country Code',
    'Browser: IP',
    'Browser: Width',
    'Browser: Height',
    'Browser: User Agent',
    'Browser: Landing Page',
    'Browser: Referrer',
    'Browser: Referrer Domain',
    'Browser: Search Keywords',
    'Browser: Ad URL',
    'Browser: UTM Source',
    'Browser: UTM Medium',
    'Browser: UTM Campaign',
    'Browser: UTM Term',
    'Browser: UTM Content',
    'Row #',
    'Top Row',
    'Line: Type',
    'Line: ID',
    'Line: Product ID',
    'Line: Product Handle',
    'Line: Title',
    'Line: Name',
    'Line: Variant ID',
    'Line: Variant Title',
    'Line: SKU',
    'Line: Quantity',
    'Line: Price',
    'Line: Discount',
    'Line: Total',
    'Line: Grams',
    'Line: Requires Shipping',
    'Line: Vendor',
    'Line: Properties',
    'Line: Gift Card',
    'Line: Taxable',
    'Line: Tax 1 Title',
    'Line: Tax 1 Rate',
    'Line: Tax 1 Price',
    'Line: Tax 2 Title',
    'Line: Tax 2 Rate',
    'Line: Tax 2 Price',
    'Line: Tax 3 Title',
    'Line: Tax 3 Rate',
    'Line: Tax 3 Price',
    'Line: Fulfillable Quantity',
    'Line: Fulfillment Service',
    'Line: Fulfillment Status',
    'Shipping Origin: Name',
    'Shipping Origin: Country Code',
    'Shipping Origin: Province Code',
    'Shipping Origin: City',
    'Shipping Origin: Address 1',
    'Shipping Origin: Address 2',
    'Shipping Origin: Zip',
    'Refund: ID',
    'Refund: Created At',
    'Refund: Note',
    'Refund: Restock',
    'Refund: Restock Location',
    'Refund: Send Receipt',
    'Transaction: ID',
    'Transaction: Kind',
    'Transaction: Processed At',
    'Transaction: Amount',
    'Transaction: Currency',
    'Transaction: Status',
    'Transaction: Message',
    'Transaction: Gateway',
    'Transaction: Test',
    'Transaction: Authorization',
    'Transaction: Error Code',
    'Transaction: CC AVS Result',
    'Transaction: CC Bin',
    'Transaction: CC CVV Result',
    'Transaction: CC Number',
    'Transaction: CC Company',
    'Risk: Source',
    'Risk: Score',
    'Risk: Recommendation',
    'Risk: Cause Cancel',
    'Risk: Message',
    'Fulfillment: ID',
    'Fulfillment: Status',
    'Fulfillment: Created At',
    'Fulfillment: Processed At',
    'Fulfillment: Tracking Company',
    'Fulfillment: Location',
    'Fulfillment: Shipment Status',
    'Fulfillment: Tracking Number',
    'Fulfillment: Tracking URL',
    'Fulfillment: Send Receipt'
));

foreach ($data as $row) {


    $export[] = array(
        null, // ID
        null, // Name
        null, // Command
        null, // Send Receipt
        null, // Inventory Behaviour
        null, // Number
        null, // Note
        null, // Tags
        null, // Tags Command
        null, // Created At
        null, // Updated At
        null, // Cancelled At
        null, // Cancel: Reason
        null, // Cancel: Send Receipt
        null, // Cancel: Refund
        null, // Processed At
        null, // Closed At
        null, // Currency
        null, // Source
        null, // User ID
        null, // Checkout ID
        null, // Cart Token
        null, // Token
        null, // Order Status URL
        null, // Weight Total
        null, // Price: Total Line Items
        null, // Price: Subtotal
        null, // Tax 1: Title
        null, // Tax 1: Rate
        null, // Tax 1: Price
        null, // Tax 2: Title
        null, // Tax 2: Rate
        null, // Tax 2: Price
        null, // Tax 3: Title
        null, // Tax 3: Rate
        null, // Tax 3: Price
        null, // Tax: Included
        null, // Tax: Total
        null, // Price: Total
        null, // Payment: Status
        null, // Payment: Processing Method
        null, // Order Fulfillment Status
        null, // Additional Details
        null, // Customer: ID
        null, // Customer: Email
        null, // Customer: Phone
        null, // Customer: First Name
        null, // Customer: Last Name
        null, // Customer: Note
        null, // Customer: Orders Count
        null, // Customer: State
        null, // Customer: Total Spent
        null, // Customer: Tags
        null, // Customer: Accepts Marketing
        null, // Billing: First Name
        null, // Billing: Last Name
        null, // Billing: Company
        null, // Billing: Phone
        null, // Billing: Address 1
        null, // Billing: Address 2
        null, // Billing: Zip
        null, // Billing: City
        null, // Billing: Province
        null, // Billing: Province Code
        null, // Billing: Country
        null, // Billing: Country Code
        null, // Shipping: First Name
        null, // Shipping: Last Name
        null, // Shipping: Company
        null, // Shipping: Phone
        null, // Shipping: Address 1
        null, // Shipping: Address 2
        null, // Shipping: Zip
        null, // Shipping: City
        null, // Shipping: Province
        null, // Shipping: Province Code
        null, // Shipping: Country
        null, // Shipping: Country Code
        null, // Browser: IP
        null, // Browser: Width
        null, // Browser: Height
        null, // Browser: User Agent
        null, // Browser: Landing Page
        null, // Browser: Referrer
        null, // Browser: Referrer Domain
        null, // Browser: Search Keywords
        null, // Browser: Ad URL
        null, // Browser: UTM Source
        null, // Browser: UTM Medium
        null, // Browser: UTM Campaign
        null, // Browser: UTM Term
        null, // Browser: UTM Content
        null, // Row #
        null, // Top Row
        null, // Line: Type
        null, // Line: ID
        null, // Line: Product ID
        null, // Line: Product Handle
        null, // Line: Title
        null, // Line: Name
        null, // Line: Variant ID
        null, // Line: Variant Title
        null, // Line: SKU
        null, // Line: Quantity
        null, // Line: Price
        null, // Line: Discount
        null, // Line: Total
        null, // Line: Grams
        null, // Line: Requires Shipping
        null, // Line: Vendor
        null, // Line: Properties
        null, // Line: Gift Card
        null, // Line: Taxable
        null, // Line: Tax 1 Title
        null, // Line: Tax 1 Rate
        null, // Line: Tax 1 Price
        null, // Line: Tax 2 Title
        null, // Line: Tax 2 Rate
        null, // Line: Tax 2 Price
        null, // Line: Tax 3 Title
        null, // Line: Tax 3 Rate
        null, // Line: Tax 3 Price
        null, // Line: Fulfillable Quantity
        null, // Line: Fulfillment Service
        null, // Line: Fulfillment Status
        null, // Shipping Origin: Name
        null, // Shipping Origin: Country Code
        null, // Shipping Origin: Province Code
        null, // Shipping Origin: City
        null, // Shipping Origin: Address 1
        null, // Shipping Origin: Address 2
        null, // Shipping Origin: Zip
        null, // Refund: ID
        null, // Refund: Created At
        null, // Refund: Note
        null, // Refund: Restock
        null, // Refund: Restock Location
        null, // Refund: Send Receipt
        null, // Transaction: ID
        null, // Transaction: Kind
        null, // Transaction: Processed At
        null, // Transaction: Amount
        null, // Transaction: Currency
        null, // Transaction: Status
        null, // Transaction: Message
        null, // Transaction: Gateway
        null, // Transaction: Test
        null, // Transaction: Authorization
        null, // Transaction: Error Code
        null, // Transaction: CC AVS Result
        null, // Transaction: CC Bin
        null, // Transaction: CC CVV Result
        null, // Transaction: CC Number
        null, // Transaction: CC Company
        null, // Risk: Source
        null, // Risk: Score
        null, // Risk: Recommendation
        null, // Risk: Cause Cancel
        null, // Risk: Message
        null, // Fulfillment: ID
        null, // Fulfillment: Status
        null, // Fulfillment: Created At
        null, // Fulfillment: Processed At
        null, // Fulfillment: Tracking Company
        null, // Fulfillment: Location
        null, // Fulfillment: Shipment Status
        null, // Fulfillment: Tracking Number
        null, // Fulfillment: Tracking URL
        null, // Fulfillment: Send Receipt
    );
}

writeDataToCsv($output_filename, $export);