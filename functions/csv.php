<?php

function getCsvAsAssArray($filename) { // Why are we not using str_getcsv()? I don't remember
    $assoc_array = [];
    if (($handle = fopen($filename, "r")) !== false) { // open for reading
        if (($data = fgetcsv($handle, 0, ",")) !== false) { // extract header data
            $keys = $data; // save as keys
        }
        while (($data = fgetcsv($handle, 0, ",")) !== false) { // loop remaining rows of data
            $assoc_array[] = array_combine($keys, $data); // push associative subarrays
        }
        fclose($handle); // close when done
    }

    return $assoc_array;
}

function writeDataToCsv($filename, $data) {
    if (($handle = fopen($filename, "w")) !== FALSE) {
        foreach ($data as $row) {
           fputcsv($handle, $row);
        }
        fclose($handle);
    }
}