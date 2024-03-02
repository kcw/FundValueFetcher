<?php  /* Jack 2024 Jan */

$url = "https://fund.ksrv.net/fetchFundValue.php";
$html = file_get_contents($url);

if ($html !== false)
{
  file_put_contents("/var/www/html/fund.ksrv.net/index_static.html", $html);
  echo "fetch ok" . PHP_EOL;
}

?>
