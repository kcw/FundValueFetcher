<?php /* Jack 2024.Jan ; get fund everyday price save to file */

$productID = $argv[1];
$fileName  = "./data/" . $productID . ".txt" ;

$Date_A = date("2000-01-01");
$Date_B = date("Y-m-d");
$url    = "https://www.moneydj.com/funddj/bcd/tBCDNavList.djbcd?a={$productID}&B={$Date_A}&C={$Date_B}";

$rtn = file_get_contents($url);

file_put_contents($fileName, $rtn);

if ($rtn !== false)
    echo "File saved successfully - " . $productID  . PHP_EOL ;
?>
