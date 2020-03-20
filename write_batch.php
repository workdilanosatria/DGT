<?php
//Error Verbose (Comment to enable error output)
error_reporting(0);

function batch($openResultFile, $outputDirName, $outputFileName, $delim) {
    $row = array();
	$writeResultFile = $outputDirName . '/' . $outputFileName;

	if (($handle = fopen($openResultFile, "r")) !== FALSE) {
	    while (($data = fgetcsv($handle, 0, ",")) !== FALSE) {

	        $row[] = $data[0] . $delim . $data[1] . $delim . $data[2] . $delim . $data[3] . $delim . $data[4] . $delim . $data[5] . $delim . $data[6] . $delim . $data[7] . $delim . $data[8] . $delim . $data[9] . $delim . $data[10] . $delim . $data[11] . $delim . $data[12] . $delim . $data[13] . $delim . $data[14] . $delim . $data[15] . $delim . $data[16] . $delim . $data[17] . $delim . $data[18] . $delim . $data[19] . $delim . $data[20] . $delim . $data[21] . $delim . $data[22];
	    }
	    fclose($handle);
	}

	$handlew = fopen($writeResultFile, 'a');
	unset($row[0]);
	unset($row[1]);
	foreach ($row as $info) {
	    fputcsv($handlew, array($info), ',', ' ');
	}

	echo date("[H:i:s]") . " => " . "Data successfully appended to " . $writeResultFile . "!\n";
	fclose($handlew);
}

?>
