<?php
use com\google\i18n\phonenumbers\PhoneNumberUtil;
use com\google\i18n\phonenumbers\PhoneNumberFormat;
use com\google\i18n\phonenumbers\NumberParseException;

require_once 'libphonenumber/PhoneNumberUtil.php';

include('functions/csv.php');
include('functions/phone.php');
include('functions/country.php');

$input_args = getopt('f:');

if (!$input_args['f']) {
   die("No file selected, use -f filename.xml\n");
}

$input_filename = $input_args['f'];
$output_filename = "shopify_ordes_" . str_replace(".xml", ".csv", $input_filename);

$data = simplexml_load_file($input_filename, "SimpleXMLElement");

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

foreach ($data as $element) {

    # Notes
    $notes = null;
    if (!empty($element->order->order_notes) && !empty($element->order->order_customer_comments)) {
        $notes = $element->order->order_notes . "\n" . $element->order->order_customer_comments;
    } elseif (!empty($element->order->order_notes)) {
        $notes = $element->order->order_notes;
    } elseif (!empty($element->order->order_customer_comments)) {
        $notes = $element->order->order_customer_comments;
    }

    # Email marketing
    if ( $element->customer->email_opt_in == "Us" ) {
        $accepts_marketing = true;
    } else {
        $accepts_marketing = false;
    }

    # City
    if (!empty($element->customer->billing_city)) {
        $customer_billing_city = $element->customer->billing_city;
    } else {
        $customer_billing_city = $element->customer->billing_town;
    }
    if (!empty($element->customer->delivery_city)) {
        $customer_shipping_city = $element->customer->delivery_city;
    } else {
        $customer_shipping_city = $element->customer->delivery_town;
    }

    # Country
    $customer_billing_country = null;
    $customer_shipping_country = null;
    if (!empty($element->customer->billing_country_name)) {
        $customer_billing_country = lookup_country($element->customer->billing_country_name);
    }
    if (!empty($element->customer->delivery_country_name)) {
        $customer_shipping_country = lookup_country($element->customer->delivery_country_name);
    }

    # Last name
    if (empty($element->customer->billing_lastname)) {
        $customer_billing_lastname = '-';
    } else {
        $customer_billing_lastname = $element->customer->billing_lastname;
    }
    if (empty($element->customer->delivery_lastname)) {
        $customer_shipping_lastname = '-';
    } else {
        $customer_shipping_lastname = $element->customer->delivery_lastname;
    }

    # Phone
    $phoneUtil = PhoneNumberUtil::getInstance();

    ## Billing phone

    $billing_country_code = country_name_to_code($customer_billing_country);

    if (!$billing_country_code) {
        echo "No country code found: " . $customer_billing_country . PHP_EOL;
    }

    if ($billing_country_code && !empty($element->customer->billing_telephone)) {
        try {
            $customer_billing_phone = $phoneUtil->parseAndKeepRawInput($element->customer->billing_telephone, $billing_country_code);
            $customer_billing_phone = $phoneUtil->format($customer_billing_phone, PhoneNumberFormat::E164);
        } catch (NumberParseException $e) {
            echo "Issue with phone: " . $element->customer->billing_telephone . " in country " . $billing_country_code . PHP_EOL;
            echo $e . PHP_EOL;
        }
    } else {
        $customer_billing_phone = null;
    }

    ## Shipping phone

    $shipping_country_code = country_name_to_code($customer_shipping_country);

    if (!$shipping_country_code) {
        echo "No country code found: " . $customer_shipping_country . PHP_EOL;
    }

    if ($shipping_country_code && !empty($element->customer->delivery_telephone)) {
        try {
            $customer_shipping_phone = $phoneUtil->parseAndKeepRawInput($element->customer->delivery_telephone, $shipping_country_code);
            $customer_shipping_phone = $phoneUtil->format($customer_shipping_phone, PhoneNumberFormat::E164);
        } catch (NumberParseException $e) {
            echo "Issue with phone: " . $element->customer->delivery_telephone . " in country " . $shipping_country_code . PHP_EOL;
            echo $e . PHP_EOL;
        }
    } else {
        $customer_shipping_phone = null;
    }

    # Create CSV

    $count = 0;

    foreach ($element->products->product as $product) {
        $count++;
        
        if ($count == 1) {

            $row = array(
                null, // ID
                $element->order->order_reference, // Name
                'REPLACE', // Command
                0, // Send Receipt
                'bypass', // Inventory Behaviour
                $element->order->order_id, // Number
                $notes, // Note
                null, // Tags
                'REPLACE', // Tags Command
                $element->order->order_date, // Created At
                null, // Updated At
                null, // Cancelled At
                null, // Cancel: Reason
                null, // Cancel: Send Receipt
                null, // Cancel: Refund
                null, // Processed At
                null, // Closed At
                $element->order->order_currency, // Currency
                $element->order->order_type, // Source
                null, // User ID
                null, // Checkout ID
                null, // Cart Token
                null, // Token
                null, // Order Status URL
                null, // Weight Total
                $element->order->product_total_inc, // Price: Total Line Items
                null, // Price: Subtotal
                'VAT', // Tax 1: Title
                20, // Tax 1: Rate
                $element->order->grand_total_vat, // Tax 1: Price
                null, // Tax 2: Title
                null, // Tax 2: Rate
                null, // Tax 2: Price
                null, // Tax 3: Title
                null, // Tax 3: Rate
                null, // Tax 3: Price
                null, // Tax: Included
                $element->order->grand_total_vat, // Tax: Total
                $element->order->grand_total_inc, // Price: Total
                null, // Payment: Status
                $element->payment->payment_type, // Payment: Processing Method
                null, // Order Fulfillment Status
                null, // Additional Details
                null, // Customer: ID
                $element->customer->email_address, // Customer: Email
                $customer_billing_phone, // Customer: Phone
                $element->customer->billing_firstname, // Customer: First Name
                $element->customer->billing_lastname, // Customer: Last Name
                null, // Customer: Note
                null, // Customer: Orders Count
                null, // Customer: State
                null, // Customer: Total Spent
                null, // Customer: Tags
                $accepts_marketing, // Customer: Accepts Marketing
                $element->customer->billing_firstname, // Billing: First Name
                $customer_billing_lastname, // Billing: Last Name
                $element->customer->billing_company_name, // Billing: Company
                $customer_billing_phone, // Billing: Phone
                $element->customer->billing_address1, // Billing: Address 1
                $element->customer->billing_address2, // Billing: Address 2
                $element->customer->billing_postcode, // Billing: Zip
                $customer_billing_city, // Billing: City
                $element->customer->delivery_county, // Billing: Province
                null, // Billing: Province Code
                $customer_billing_country, // Billing: Country
                null, // Billing: Country Code
                $element->customer->delivery_firstname, // Shipping: First Name
                $customer_shipping_lastname, // Shipping: Last Name
                $element->customer->delivery_company_name, // Shipping: Company
                $customer_shipping_phone, // Shipping: Phone
                $element->customer->delivery_address1, // Shipping: Address 1
                $element->customer->delivery_address2, // Shipping: Address 2
                $element->customer->delivery_postcode, // Shipping: Zip
                $customer_shipping_city, // Shipping: City
                $element->customer->delivery_county, // Shipping: Province
                null, // Shipping: Province Code
                $customer_shipping_country, // Shipping: Country
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
                $count, // Row #
                1, // Top Row
                'Line Item', // Line: Type
                null, // Line: ID
                null, // Line: Product ID
                null, // Line: Product Handle
                $product->title, // Line: Title
                $product->title, // Line: Name
                null, // Line: Variant ID
                $product->summary, // Line: Variant Title
                $product->reference, // Line: SKU
                $product->quantity, // Line: Quantity
                $product->price_ex, // Line: Price
                null, // Line: Discount
                $product->price_inc, // Line: Total
                $product->weight, // Line: Grams
                null, // Line: Requires Shipping
                null, // Line: Vendor
                $product->attribute_summary, // Line: Properties
                null, // Line: Gift Card
                null, // Line: Taxable
                'VAT', // Line: Tax 1 Title
                0.2, // Line: Tax 1 Rate
                $product->price_vat, // Line: Tax 1 Price
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
        } else {
            $row = array(
                null, // ID
                $element->order->order_reference, // Name
                'REPLACE', // Command
                0, // Send Receipt
                'bypass', // Inventory Behaviour
                $element->order->order_id, // Number
                $notes, // Note
                null, // Tags
                'REPLACE', // Tags Command
                $element->order->order_date, // Created At
                null, // Updated At
                null, // Cancelled At
                null, // Cancel: Reason
                null, // Cancel: Send Receipt
                null, // Cancel: Refund
                null, // Processed At
                null, // Closed At
                $element->order->order_currency, // Currency
                $element->order->order_type, // Source
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
                $count, // Row #
                null, // Top Row
                'Line Item', // Line: Type
                null, // Line: ID
                null, // Line: Product ID
                null, // Line: Product Handle
                $product->title, // Line: Title
                $product->title, // Line: Name
                null, // Line: Variant ID
                $product->summary, // Line: Variant Title
                $product->reference, // Line: SKU
                $product->quantity, // Line: Quantity
                $product->price_ex, // Line: Price
                null, // Line: Discount
                $product->price_inc, // Line: Total
                $product->weight, // Line: Grams
                null, // Line: Requires Shipping
                null, // Line: Vendor
                $product->attribute_summary, // Line: Properties
                null, // Line: Gift Card
                null, // Line: Taxable
                'VAT', // Line: Tax 1 Title
                0.2, // Line: Tax 1 Rate
                $product->price_vat, // Line: Tax 1 Price
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

        $export[] = $row;
    }
}

writeDataToCsv($output_filename, $export);