<?php
/* ----------------------------------------------------
   Jack 2024 Nov.3 - Fetch fund performance ratio
   php fundPerfRatio.php <begin> <end> <step>
   php fundPerfRatio.php 2023-01-01 2024-01-01 20
   ---------------------------------------------------- */

if ($argc < 3) {
    echo "Usage: php fundPerfRatio.php <start_date> <end_date> <step>" .PHP_EOL;
    exit(1);
}

$start_date = $argv[1];
$end_date   = $argv[2];
$step       = isset($argv[3]) ? $argv[3] : 1;    // step(optional), default=1

$url = "https://www.moneydj.com/funddj/bcd/BCDROIList5Applet.djbcd?a=ACDD04&b=ACPS10&c=AC0050&d=0&e=0&f=0&g=0&h=0&i=$start_date&j=$end_date";
$response = file_get_contents($url);

// Check for errors
if ($response === FALSE)
    die("Error fetching data.\n");
else
{
    // Split the response into three parts using space as a delimiter
    $parts   = explode(' ', $response);
    $dateAry = explode(',', $parts[0]);   // date
    $ratioA  = explode(',', $parts[1]);   // acdd04 安聯台科
    $ratioB  = explode(',', $parts[2]);   // acps10 統一奔騰
    $ratioC  = explode(',', $parts[3]);   // 0050

    $dateAry = array_map(function($date) {
        return DateTime::createFromFormat('Ymd', (string)$date)->format('Y-m-d');
    }, $dateAry);

    $len = count($dateAry);
    echo "日期 安聯台科 統一奔騰 台灣50" .PHP_EOL;
    for ($i=0; $i<$len; $i+=$step)
      echo $dateAry[$i] . " " . $ratioA[$i] . " " . $ratioB[$i] . " " . $ratioC[$i] . PHP_EOL;
}
?>
