<?php

/* 2024 Nov. Jack - fund performance ratio fetch backend */

if ($_SERVER['REQUEST_METHOD'] == 'POST')
{
    $start_date = $_POST['start_date'];
    $end_date   = $_POST['end_date'];
    $step       = isset($_POST['step']) ? (int)$_POST['step'] : 1;

    $_a = "ACDD04";  // 安聯台科
	$_b = "ACPS10";  // 統一奔騰
	$_c = "AC0050";  // 台灣50

    $url = "https://www.moneydj.com/funddj/bcd/BCDROIList5Applet.djbcd?a=$_a&b=$_b&c=$_c&d=0&e=0&f=0&g=0&h=0&i=$start_date&j=$end_date";
    $response = file_get_contents($url);

    // Check for errors
    if ($response === FALSE) {
        echo json_encode(['error' => 'Error fetching data.']);
        exit;
    }

    // Process the response
    $parts   = explode(' ', $response);
    $dateAry = explode(',', $parts[0]);
    $ratioA  = explode(',', $parts[1]);
    $ratioB  = explode(',', $parts[2]);
    $ratioC  = explode(',', $parts[3]);

    $dateAry = array_map(function($date) {
        return DateTime::createFromFormat('Ymd', (string)$date)->format('Y-m-d');
    }, $dateAry);

    $len = count($dateAry);
    $_dateAry = [];
    $_ratioA = [];
    $_ratioB = [];
    $_ratioC = [];

    for ($i = 0; $i < $len; $i += $step) {
        $_dateAry[] = $dateAry[$i];
        $_ratioA[] = $ratioA[$i];
        $_ratioB[] = $ratioB[$i];
        $_ratioC[] = $ratioC[$i];
    }

    echo json_encode([
        'dates' => $_dateAry,
        'ratioA' => $_ratioA,
        'ratioB' => $_ratioB,
        'ratioC' => $_ratioC
    ]);
}
?>

