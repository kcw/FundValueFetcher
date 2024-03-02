<?php
/* [Jack, 2024 Jan] - dca.php (Dollar Cost Averaging)
   php dca.php <productID> <buyCycleDate> <amount> <startDate> <endDate> <buyBackDate>
   php dca.php acic01 28 3000 20230101 20231230 20240101
*/

// php dca.php acic01 28 3000 20230101 20231230 20240101
if ($argc != 7)
{
  die("Usage: php dca.php <productID> <periodDate> <Invest_$> <startDate> <endDate> <buyBackDate>\n");
}
  
$productID    = $argv[1];
$periodDate   = $argv[2];
$periodInvest = $argv[3];
$startDate    = $argv[4];
$endDate      = $argv[5];
$buyBackDate  = $argv[6];

if ( $periodDate > 31 ) die("Only 31 days in a month" . PHP_EOL);

function enumerateDates($startDate, $endDate, $periodDate)
{ // if $periodDate=15, will return array("20230115", "20230215", ...)
  $dates = [];

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
    {
        $day = $lastDayOfMonth;
    }

    $date = DateTime::createFromFormat('Y-m-d', "$year-$month-$day");

    // Ensure the date is within the specified range
    if ($date >= $currentDate && $date <= $endDateObj)
    {
        $dates[] = $date->format('Ymd');
    }
    $currentDate->modify('first day of next month');
  }
  //var_dump($dates);
  return $dates;  // return array
}

function getValueByDate($productID, $periodDateAry)
{ // return eachday value array
  $priceAry = array();

  $str   = file_get_contents( "data/" . $productID . ".txt" );
  $pairs = explode(' ', $str);
  $date_array  = explode(',' , $pairs[0] );
  $value_array = explode(',' , $pairs[1] );

  // find each day in array then retrieve corresponding price
  foreach ( $periodDateAry as $targetDay )
  {
    $key = array_search($targetDay, $date_array);
    if ($key !== false) {
      //echo "Date: " . $date_array[ $key ] . PHP_EOL;
      //echo "Price: ". $value_array[ $key ] . PHP_EOL;
      $priceAry[] = $value_array[ $key ];
    }
    else
    {
      $lower_key = null;
      foreach ($date_array as $index => $value)
      {
        if ($value <= $targetDay)
          $lower_key = $index;
        else
          break;
      }
      if ($lower_key !== null)
      {
        //echo "Date: " . $date_array[ $lower_key ] . PHP_EOL;
        //echo "Price: ". $value_array[ $lower_key ] . PHP_EOL;
        $priceAry[] = $value_array[ $lower_key ];
      }
      else
        die('Value not found' . PHP_EOL);
    }
  }
  return $priceAry;
}

function sumPeriodUnit($investValue, $priceAry)
{
  $rtn_units = 0;
  foreach($priceAry as $cost)
  {    
    $rtn_units = $rtn_units + ( $investValue/$cost );
  }
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

echo "[" . $productID . "]" . PHP_EOL;
echo "Ratio = %" . round(($ratio-1)*100, 2) . PHP_EOL;
echo "Profit= $" . round($profit, 2) . PHP_EOL ;
echo "Base  = $" . $base . PHP_EOL;
echo "Income= $" . round(($buyBackPriceAry[0] * $totalUnits), 2) . PHP_EOL;
echo "===" . PHP_EOL;
//echo "Times = " . count($periodDateAry) . PHP_EOL;
//echo "Units = " . $totalUnits . PHP_EOL;
//echo "BuyBackPrice = " . $buyBackPriceAry[0] . PHP_EOL;

?>
