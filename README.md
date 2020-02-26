# vsCommerce to Shopify

Scripts for converting exports from vsCommerce to the Shopify/Excelify format. These are quick and baisc PHP scripts to be run in the command line.

## Orders

`vscommerce-to-shopify/orders.php -f example.xml`

For multipule files example: `for f in *.xml; do php ../../../vscommerce-to-shopify/orders.php -f "$f" ; done`

The order script runs on the order export XML. It attmepts to fix and format phone numbers ready for Shopify.

## Customers

`vscommerce-to-shopify/customers.php -f example.csv`

The customer script runs on the customer export CSV with all columns included. It attmepts to fix and format phone numbers ready for Shopify.

## Products

`vscommerce-to-shopify/products.php -f example.csv`

The product script runs on the product export CSV with all columns included. It has a `-s` flag to split products titles into two halfs by a dash. Example "Vendor - Product title".