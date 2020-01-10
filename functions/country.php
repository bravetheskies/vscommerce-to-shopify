<?php

function lookup_country($country) {
    $country_lookup = array(
        'UK - Mainland' => 'United Kingdom',
        'USA' => 'United States'
    );

    if (array_key_exists($country, $country_lookup)) {
        return $country_lookup[$country];
    } else {
        return $country;
    }
}