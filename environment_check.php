<?php

function check($outputDirName, $outputFileName, $delim, $headerRow) {

	if (!is_dir($outputDirName)) {
    echo "No output directory, Creating new one " . $outputDirName . "\n";
    mkdir($outputDirName, 0777, true);
    } else {
    	echo "The directory $outputDirName exists: OK\n";
    }

    if (!is_dir('temp')) {
        mkdir('temp', 0777, true);
    }

    $csvCheck = $outputDirName . '/' . $outputFileName;
    $csvCheck2 = $outputDirName . '/summary_' . $outputFileName;
    if (file_exists($csvCheck)) {
        echo "The file $csvCheck exists: OK\n";
    } else {
        echo "The file $csvCheck does not exist, creating new batch output\n";
        $handle = fopen($csvCheck, 'a');
        fwrite($handle, $headerRow);
        fclose($handlew); 
    }

    if (file_exists($csvCheck2)) {
        echo "The file $csvCheck2 exists: OK\n";
    } else {
        echo "The file $csvCheck2 does not exist, creating new summary output\n";
        $handle = fopen($csvCheck2, 'a');
        fwrite($handle, "Tanggal_Scrapping $delim Waktu_Scrapping $delim Nama_Toko $delim Total_Produk $delim URL_toko\n");
        fclose($handlew); 
    }

    echo "\n";
}

?>