#!/usr/bin/env php
<?php
require 'vendor/autoload.php';

use GuzzleHttp\Client;

if(! $itemfile =@ $argv[1] ) {
    exit("No item csv file specified.\n");
}
if(! $csvfh = @fopen( $itemfile, "r" ) ) {
    exit("Couldn't open file: $php_errormsg\n");
}


// Set up Guzzle client to make requests for marcxml 
$client = new Client(['base_uri' => 'http://52.0.88.11']); 


// Get marcxml for all of the mssids in our list
$first=TRUE;
while($line = fgetcsv($csvfh ) ){

    // skip first line, it's a header
    if($first== TRUE) {
	$first=FALSE;
	continue;
    }

    // get marcxml for a book
    $mssid= $line[1];
    $bagname = $line[2];
    fwrite(STDOUT, "downloading marcxml record mssid ".$mssid. " for bag ".$bagname."\n" );
    $response =$client->get('.', ['query' => ['bib_id' => $mssid]]);

    // save it to a file based on the bag name
    if( $outfh = @fopen( $bagname.".xml", "w" ) ) {
	fwrite( $outfh, $response->getBody());
	fclose( $outfh );
    }
}
