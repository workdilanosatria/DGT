<?php
//Error Verbose (Comment to enable error output)
//error_reporting(0);

function writeSummary($userTokped, $noRow, $needle, $outputDirName, $outputFileName, $delim) {
	$writeResultFile = $outputDirName . '/summary_' . $outputFileName;
	$handle = fopen($writeResultFile, 'a');

	fwrite($handle, date("d-m-Y")."$delim".date("H:i:s")."$delim$userTokped$delim$noRow$delim$needle\n");

	echo date("[H:i:s]") . " => " . "Summary Data successfully appended to " . $writeResultFile . "!\n";
	fclose($handlew);
}

?>
