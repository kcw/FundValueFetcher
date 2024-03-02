<?php
/* [Jack, 2024 Jan] - dca.php (Dollar Cost Averaging)
   php dca.php <productID> <Recurring Invest Dates> <amount> <startDate> <endDate> <buyBackDate>
   php dca.php acic01 15,20 3000 20230101 20231230 20240101
*/

// dca2 version -  Support multiple recurring investment date

if ($_SERVER["REQUEST_METHOD"] !== "POST") die("NO POST Data!");

  $productID    = trim( $_POST["productID"]    );
  $periodDate   = trim( $_POST["periodDate"]   );
  $periodInvest = trim( $_POST["periodInvest"] );
  $startDate    = trim( $_POST["startDate"]    );
  $endDate      = trim( $_POST["endDate"]      );
  $buyBackDate  = trim( $_POST["buyBackDate"]  );

//if ( $periodDate > 31 ) { $response = array('productID' => '月投資日錯誤'); echo json_encode($response); die(); }
if ( $buyBackDate < $endDate ) { $response = array('productID' => '買回日錯誤'); echo json_encode($response); die(); }
if ( $startDate > $endDate )   { $response = array('productID' => '起終日異常'); echo json_encode($response); die(); }

function enumerateDates($startDate, $endDate, $periodDate)
{ // $periodDate can be number(15), or string("10,20") ;
  // if $periodDate=15, return enum array("20230115", "20230215", ...) ; ChatGPT generate
  
  $dates = [];
  $x = explode(',', $periodDate);  // string("10,20") will convert into $x=array(10,20)

  foreach($x as $periodDate)
  {
    $currentDate = new DateTime($startDate);
    $endDateObj  = new DateTime($endDate);

    while ($currentDate <= $endDateObj)
    {
      $month = $currentDate->format('m');
      $year  = $currentDate->format('Y');
      $day   = $periodDate;

      // Check if the day exists in the month, considering different month lengths
      $lastDayOfMonth = $currentDate->format('t');
      if ($day > $lastDayOfMonth)
        $day = $lastDayOfMonth;

      $date = DateTime::createFromFormat('Y-m-d', "$year-$month-$day");

      // Ensure the date is within the specified range
      if ($date >= $currentDate && $date <= $endDateObj)
        $dates[] = $date->format('Ymd');

      $currentDate->modify('first day of next month');
    }
  }
  return $dates;  // return array
}

function getValueByDate($productID, $periodDateAry)
{ // return eachday value array
  $priceAry = array();

  $str   = file_get_contents( "data/" . $productID . ".txt" );
  $pairs = explode(' ', $str);
  $date_array  = explode(',' , $pairs[0] );            // $date[]  = array('20240101', '20240102' ...)
  $value_array = explode(',' , $pairs[1] );            // $value[] = array('price1'  , 'price2'   ...)

  // find each day in array then retrieve corresponding price
  foreach ( $periodDateAry as $targetDay )
  {
    $key = array_search($targetDay, $date_array);
    if ($key !== false)
    {
      //echo "Date: " . $date_array[ $key ] . PHP_EOL;
      //echo "Price: ". $value_array[ $key ] . PHP_EOL;
      $priceAry[] = $value_array[ $key ];
    }
    else
    {
      $lower_key = null;
      foreach ($date_array as $index => $value)
      {
        if ($value <= $targetDay) $lower_key = $index;
        else break;
      }
      if ($lower_key !== null)
      {
        //echo "Date: " . $date_array[ $lower_key ] . PHP_EOL;
        //echo "Price: ". $value_array[ $lower_key ] . PHP_EOL;
        $priceAry[] = $value_array[ $lower_key ];
      }
      else die('Value not found' . PHP_EOL);
    }
  }
  return $priceAry;
}

function sumPeriodUnit($investValue, $priceAry)
{
  $rtn_units = 0;
  foreach($priceAry as $cost)
    $rtn_units = $rtn_units + ( $investValue/$cost );

  return $rtn_units;
}

$periodDateAry  = enumerateDates($startDate, $endDate, $periodDate);  // get period invest day list array
$periodPriceAry = getValueByDate($productID, $periodDateAry);         // get price of each day
$totalUnits     = sumPeriodUnit($periodInvest, $periodPriceAry);      // get unit summation

// Buy back array ONLY have one value
$buyBackPriceAry= getValueByDate($productID, array($buyBackDate) );

$base   = $periodInvest * count($periodDateAry);
$profit = ($buyBackPriceAry[0] * $totalUnits) - ( $periodInvest * count($periodDateAry) );
$ratio  = ($buyBackPriceAry[0] * $totalUnits) / ( $periodInvest * count($periodDateAry) );

$response = array
(
  'totalUnits'      => round($totalUnits, 1),
  'buyBackPriceAry' => $buyBackPriceAry,
  'productID'       => $productID,
  'ratio'           => round(($ratio - 1) * 100, 1),
  'profit'          => round($profit, 1),
  'base'            => $base,
  'income'          => round(($buyBackPriceAry[0] * $totalUnits), 1)
);
echo json_encode($response);

?>
