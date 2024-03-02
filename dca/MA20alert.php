<?php   /* Jack , 2024 Jan. for calculating MA20 */

if ($argc != 3)
  die("Usage: php countMA20 <productID> <threshold>" . PHP_EOL);

$productID = $argv[1];
$threshold = $argv[2]; // if value/MA20 < (100-threshold)% then alert
$MA20Ary = array();

$str   = file_get_contents( "data/" . $productID . ".txt" );
$pairs = explode(' ', $str);
$date_array  = explode(',' , $pairs[0] );
$value_array = explode(',' , $pairs[1] );

// Count MA20 by each day value
for ($i=19 ; $i< count($value_array) ; $i++)
{
  $sum=0;
  for($j=$i-19 ; $j<=$i ; $j++)
    $sum = $sum + $value_array[$j];
  
  $avg = $sum/20;
  $MA20Ary[] = $avg;
}

// Show all value and MA20
echo "date," . "value," . "MA20," . "alert" .PHP_EOL;
for ($i=0; $i<count($MA20Ary); $i++)
{ 
  if (  (1-($value_array[$i+19] / $MA20Ary[$i]))*100 > $threshold )     
    echo $date_array[$i+19] .",".$value_array[$i+19] . "," . $MA20Ary[$i] . ",加碼點" . PHP_EOL;
  else
    echo  $date_array[$i+19] . "," . $value_array[$i+19] . "," . $MA20Ary[$i]. PHP_EOL;
}

?>
