<?php

// todo: ada json di product page, ada kota pengiriman, dll

echo "Tokopedia web scraping V.2.0 RELEASE\n";
echo "Author: Dilano Satria\n";
echo "\n";
echo date("[H:i:s]") . " => " . "STARTING...\n";
echo "\n";
echo "Current working directory: " . getcwd() . "\n"; 

//======================================================================= Config

//Time Zome
date_default_timezone_set('asia/jakarta');

//Error Verbose (Comment to enable error output)
error_reporting(0);

//Business Lists
$businessNameList = array("attyourlife", "purecase", "fxhobby");

//Output Directory ex: getcwd() . '/output'
$outputDirName = getcwd() . '/output';

//Output Directory
$outputFileName = 'tokopedia.csv';

//Delimiter
$delim = ',';

//User Agent
$userAgent = "Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/78.0.3904.87 Safari/537.36";

//Automatically Delete Temp File? ['Y'/'N']
$removeTemp = 'y';

//Write Scrapped Business Summary? ['Y'/'N']
$writeSummary = 'y';

//CSV Header Row ex: "No $delim Tanggal_Scrapping $delim Waktu_Scrapping ... $delim Total_Penjualan\n"
$headerRow = "No $delim Tanggal_Scrapping $delim Waktu_Scrapping $delim Nama_Toko $delim Nama_Produk $delim Deskripsi_Produk $delim Berat_Produk $delim Kondisi_Produk $delim Asuransi_Pengiriman $delim Harga_Produk $delim Jumlah_Lihat $delim Jumlah_Terjual $delim Jumlah_Bintang $delim Jumlah_Review $delim Kategori_1 $delim Kategori_2 $delim Kategori_3 $delim Kategori_4 $delim Kategori_5 $delim URL_Gambar $delim URL_Produk $delim Total_Penjualan\n";

//NOTE: 
//======================================================================= Script

require_once('environment_check.php');
require_once('write_batch.php');
require_once('write_summary.php');

check($outputDirName, $outputFileName, $delim, $headerRow);

function getStr($start, $end, $string) {
    if (!empty($string)) {
    $setring = explode($start,$string);
    $setring = explode($end,$setring[1]);
    return $setring[0];
    }
}

for($i = 0; $i < count($businessNameList); $i++) {
    
    $productList = array();
    $page = 1;
    
    do{
    $userTokped = $businessNameList[$i];
    $resultFile = getcwd() . '/temp/' . date("dmY") . '_' . date("His") . '_' . $userTokped . '.csv';

    }while($userTokped=="" OR $resultFile=="");
    $handle = fopen($resultFile, 'a');


    // ===============================================================================================================
        do{
        echo date("[H:i:s]") . " => " . "Scrapping $userTokped's page $page products...\n";
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, "https://www.tokopedia.com/$userTokped/page/$page");
        curl_setopt($ch, CURLOPT_USERAGENT, $userAgent);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
                                            "Host: www.tokopedia.com",
                                            "Connection: keep-alive",
                                            "Upgrade-Insecure-Requests: 1",
                                            "User-Agent: $userAgent",
                                            "Accept: text/html,application/xhtml+xml,application/xml;q=0.9,image/webp,image/apng,*/*;q=0.8,application/signed-exchange;v=b3",
                                            "Sec-Fetch-Site: none",
                                            "Sec-Fetch-Mode: navigate",
                                            "Accept-Language: en-US,en;q=0.9,id;q=0.8"
        ));
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);

        $result = curl_exec($ch);

        $checkNextPage = $page+1;

        if(!strpos($result, '<a href="/' . $userTokped . '/page/' . $checkNextPage)){
            $noNextPage = 1;
        }else{
            $noNextPage = 0;
            $page++;
        }

        $needle = "https://www.tokopedia.com/$userTokped/";
        $lastPos = 0;
        $positions = array();

        while (($lastPos = strpos($result, $needle, $lastPos))!== false) {
            $positions[] = $lastPos;
            $lastPos = $lastPos + strlen($needle);
        }

        foreach($positions as $productPos){
            $product = substr($result, $productPos, 1000);
            $product = getStr($needle, '"', $product);
            $productURL = $needle . $product;
            if(!strpos($productURL, "/page")){
                array_push($productList, $productURL);
            }
        }

        }while($noNextPage!=1);

        echo date("[H:i:s]") . " => " . count($productList) ." products detected on $userTokped!\n";

        //WRITE HEADER
        fwrite($handle, $headerRow); 
        fwrite($handle, count($productList) . " PRODUCTS ($needle)\n"); 

        $productNo = 1;

        foreach($productList as $productURL){
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $productURL);
            curl_setopt($ch, CURLOPT_USERAGENT, $userAgent);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($ch, CURLOPT_HTTPHEADER, array(
                                                "Host: www.tokopedia.com",
                                                "Connection: keep-alive",
                                                "Upgrade-Insecure-Requests: 1",
                                                "User-Agent: $userAgent",
                                                "Accept: text/html,application/xhtml+xml,application/xml;q=0.9,image/webp,image/apng,*/*;q=0.8,application/signed-exchange;v=b3",
                                                "Sec-Fetch-Site: none",
                                                "Sec-Fetch-Mode: navigate",
                                                "Accept-Language: en-US,en;q=0.9,id;q=0.8"
            ));
            curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
        
            $result = curl_exec($ch);
            $storeName = str_replace($delim, "", getStr('data-testid="llbPDPFooterShopName">', '</a>', $result));
            $productName = str_replace($delim, "", getStr('name="title" content="', '"', $result));
            $productDesc = str_replace($delim, "", getStr('"description": "', '"', $result));
            $productWeight = str_replace($delim, "", getStr('data-testid="PDPDetailWeightValue">', '</p>', $result));
            $productCondition = str_replace($delim, "", getStr('data-testid="PDPDetailConditionValue">', '</p>', $result));
            $productInsurance = str_replace($delim, "", getStr('data-testid="PDPInfoInsuranceValue">', '</p>', $result));
            // $productLocation = str_replace($delim, "", getStr('class="css-7u7x65-unf-heading e1qvo2ff8">Dari', '</p>', $result));
            $productPrice = str_replace('.', "", str_replace($delim, "", getStr('property="twitter:data1" content="Rp', '"', $result)));
            $productSold = str_replace($delim, "", getStr('Terjual<!-- --> <!-- -->', '<!-- --> <!-- -->Produk', $result));
            $totalSales = floatval($productPrice) * floatval($productSold);
            $productView = str_replace($delim, "", getStr('"lblPDPDetailProductSeenCounter"><b class="[object Object]">', '<!-- -->x</b> <!-- -->Dilihat', $result));
            $productStar = str_replace($delim, "", getStr('<span data-testid="lblPDPDetailProductRatingNumber">', '</span>', $result));
            $productReview = str_replace($delim, "", getStr('"lblPDPDetailProductRatingCounter" class="[object Object]">(<!-- -->', '<!-- -->)', $result));
            $productCategory1 = str_replace($delim, "", getStr('"position": 1,
            "item": {
              "@id": "https://www.tokopedia.com/",
              "name": "', '"
            }', $result));
            $productCategory2 = str_replace($delim, "", getStr('"position": 2,
      "item": {
        "@id": "https://tokopedia.com/p/', '",', $result));
            $productCategory3 = str_replace($delim, "", getStr('"position": 3,
      "item": {
        "@id": "https://tokopedia.com/p/'.$productCategory2.'/', '",', $result));
            $productCategory4 = str_replace($delim, "", getStr('"position": 4,
      "item": {
        "@id": "https://tokopedia.com/p/'.$productCategory2.'/'.$productCategory3.'/', '",', $result));
            $productCategory4 = str_replace($delim, "", getStr('"position": 4,
      "item": {
        "@id": "https://tokopedia.com/p/'.$productCategory2.'/'.$productCategory3.'/'.$productCategory4.'/', '",', $result));
            //$productImage = str_replace($delim, "", getStr('<div data-testid="PDPImageMain" class="css-r3x4jh"><div class="css-hnnye ew904gd0"><div class="css-1ans2w0 e18n9kgb0" height="auto"><img class="success fade" src="', '"" alt="product image" title=""></div></div></div>', $result));

            //WRITE RESULTS
            fwrite($handle, "$productNo$delim".date("d-m-Y")."$delim".date("H:i:s")."$delim$storeName$delim$productName$delim$productDesc$delim$productWeight$delim$productCondition$delim$productInsurance$delim$productPrice$delim$productView$delim$productSold$delim$productStar$delim$productReview$delim$productCategory1$delim$productCategory2$delim$productCategory3$delim$productCategory4$delim$productCategory5$delim$productImage$delim$productURL$delim$totalSales\n");

            echo date("[H:i:s]") . " => " . "Product [$productNo] has been saved to $resultFile!\n";

            set_time_limit(0);

            curl_setopt($ch, CURLOPT_TIMEOUT, 50);
            //curl_setopt($ch, CURLOPT_FILE, $folderHandle);
            curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);

            $data = curl_exec($ch);

            $productNo++;
        }

        //Append result file into output file 
        batch($resultFile, $outputDirName, $outputFileName, $delim);
        
        //Write Summary
        if ($writeSummary == 'y') {
            writeSummary($userTokped, $writeRow = count($productList), $needle, $outputDirName, $outputFileName, $delim);
        }

        //Remove Temp File
        if($removeTemp == 'y') {
            unlink($resultFile);
            echo date("[H:i:s]") . " => " . $resultFile . "Successfully Deleted!\n";
        }
        echo "\n";
}

echo date("[H:i:s]") . " => " . "FINISHED!";

?>