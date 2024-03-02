<?php   /* Jack , 2024 Jan. for calculating MA10 */

if ($argc != 3)
  die("Usage: php countMA10 <productID> <threshold>" . PHP_EOL);

$productID = $argv[1];
$threshold = trim($argv[2]); // if value/MA10 < (100-threshold)% then alert
$MA10Ary = array();

$str   = file_get_contents( "data/" . $productID . ".txt" );
$pairs = explode(' ', $str);
$date_array  = explode(',' , $pairs[0] );
$value_array = explode(',' , $pairs[1] );

// Count MA10 by each day value
for ($i=9 ; $i< count($value_array) ; $i++)
{
  $sum=0;
  for($j=$i-9 ; $j<=$i ; $j++)
    $sum = $sum + $value_array[$j];
  
  $avg = $sum/10;
  $MA10Ary[] = $avg;
}

// Show all value and MA10
echo "date," . "value," . "MA10," . "alert" .PHP_EOL;
for ($i=0; $i<count($MA10Ary); $i++)
{ 
  if (  (1-($value_array[$i+9] / $MA10Ary[$i]))*100 > $threshold )     
    echo $date_array[$i+9] .",".$value_array[$i+9] . "," . $MA10Ary[$i] . ",加碼點" . PHP_EOL;
  else
    echo  $date_array[$i+9] . "," . $value_array[$i+9] . "," . $MA10Ary[$i]. PHP_EOL;
}

?>
