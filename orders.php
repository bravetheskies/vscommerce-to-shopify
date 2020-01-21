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

$columns = array(
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
);

$export = array($columns);

$empty_row = getEmptyRowFromColumns($columns);

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
    $accepts_marketing = false;
    if ( $element->customer->email_opt_in == "Us" ) {
        $accepts_marketing = true;
    } else {
        $accepts_marketing = false;
    }

    # City
    $customer_billing_city = null;
    $customer_shipping_city = null;
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
    $customer_billing_lastname = null;
    $customer_shipping_lastname = null;
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
    $customer_billing_phone = null;
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
    }

    ## Shipping phone
    $customer_shipping_phone = null;
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
    }

    # Order status
    $payment_status = null;
    $fulfillment_status = null;
    $requires_shipping = null;

    switch ($element->order->order_state) {
        case "Order Dispatched":
            $payment_status = "paid";
            $fulfillment_status = "success";
            $requires_shipping = 1;
            break;
        case "Payment Received":
            $payment_status = "paid";
            $requires_shipping = 1;
            break;
        case "Payment Failed":
        case "Order Cancelled":
            $payment_status = "voided";
            $fulfillment_status = "cancelled";
            break;
        case "Order Refunded":
            $payment_status = "refunded";
            break;
        case "Order Incomplete":
            $payment_status = "pending";
            $fulfillment_status = "pending";
            $requires_shipping = 1;
            break;
        case "Order Partially Refunded":
            $payment_status = "partially_refunded";
            break;
    }

    # Create CSV

    $count = 0;

    $row_order_template = $empty_row;

    $row_order_template['Name'] = $element->order->order_reference;
    $row_order_template['Command'] = 'REPLACE';
    $row_order_template['Send Receipt'] = 0;
    $row_order_template['Inventory Behaviour'] = 'bypass';
    $row_order_template['Number'] = $element->order->order_id;
    $row_order_template['Note'] = $notes;
    $row_order_template['Tags Command'] = 'REPLACE';
    $row_order_template['Created At'] = $element->order->order_date;
    $row_order_template['Currency'] = $element->order->order_currency;
    $row_order_template['Source'] = 'vscommerce_' . $element->order->order_type;

    foreach ($element->products->product as $product) {
        $count++;
        
        if ($count == 1) {
            # First row of order
            $row = $row_order_template;

            $row['Price: Total Line Items'] = $element->order->product_total_inc;
            $row['Tax 1: Title'] = 'VAT';
            $row['Tax 1: Rate'] = 20;
            $row['Tax 1: Price'] = $element->order->grand_total_vat;
            $row['Tax: Total'] = $element->order->grand_total_vat;
            $row['Price: Total'] = $element->order->grand_total_inc;
            $row['Payment: Status'] = $payment_status;
            $row['Payment: Processing Method'] = $element->payment->payment_type;
            $row['Order Fulfillment Status'] = $fulfillment_status;
            $row['Customer: Email'] = $element->customer->email_address;
            $row['Customer: Phone'] = $customer_billing_phone;
            $row['Customer: First Name'] = $element->customer->billing_firstname;
            $row['Customer: Last Name'] = $element->customer->billing_lastname;
            $row['Customer: Accepts Marketing'] = $accepts_marketing;
            $row['Billing: First Name'] = $element->customer->billing_firstname;
            $row['Billing: Last Name'] = $customer_billing_lastname;
            $row['Billing: Company'] = $element->customer->billing_company_name;
            $row['Billing: Phone'] = $customer_billing_phone;
            $row['Billing: Address 1'] = $element->customer->billing_address1;
            $row['Billing: Address 2'] = $element->customer->billing_address2;
            $row['Billing: Zip'] = $element->customer->billing_postcode;
            $row['Billing: City'] = $customer_billing_city;
            $row['Billing: Province'] = $element->customer->delivery_county;
            $row['Billing: Country'] = $customer_billing_country;
            $row['Shipping: First Name'] = $element->customer->delivery_firstname;
            $row['Shipping: Last Name'] = $customer_shipping_lastname;
            $row['Shipping: Company'] = $element->customer->delivery_company_name;
            $row['Shipping: Phone'] = $customer_shipping_phone;
            $row['Shipping: Address 1'] = $element->customer->delivery_address1;
            $row['Shipping: Address 2'] = $element->customer->delivery_address2;
            $row['Shipping: Zip'] = $element->customer->delivery_postcode;
            $row['Shipping: City'] = $customer_shipping_city;
            $row['Shipping: Province'] = $element->customer->delivery_county;
            $row['Shipping: Country'] = $customer_shipping_country;
            $row['Row #'] = $count;
            $row['Top Row'] = 1;
            $row['Line: Type'] = 'Line Item';
            $row['Line: Title'] = $product->title;
            $row['Line: Name'] = $product->title;
            $row['Line: Variant Title'] = $product->summary;
            $row['Line: SKU'] = $product->reference;
            $row['Line: Quantity'] = $product->quantity;
            $row['Line: Price'] = $product->price_ex;
            $row['Line: Total'] = $product->price_inc;
            $row['Line: Grams'] = $product->weight;
            $row['Line: Requires Shipping'] = $requires_shipping;
            $row['Line: Properties'] = $product->attribute_summary;
            $row['Line: Tax 1 Title'] = 'VAT';
            $row['Line: Tax 1 Rate'] = 0.2;
            $row['Line: Tax 1 Price'] = $product->price_vat;
            $row['Line: Fulfillment Status'] = $fulfillment_status;
            $row['Fulfillment: Status'] = $fulfillment_status;
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
                'vscommerce_' . $element->order->order_type, // Source
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
                $payment_status, // Payment: Status
                $element->payment->payment_type, // Payment: Processing Method
                $fulfillment_status, // Order Fulfillment Status
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
                $requires_shipping, // Line: Requires Shipping
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
                $fulfillment_status, // Line: Fulfillment Status
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
                $fulfillment_status, // Fulfillment: Status
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