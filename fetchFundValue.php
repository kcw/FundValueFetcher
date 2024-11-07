<!doctype html>
<!-- Jack 2024, Jan - Get everyday fund value -->
<html>
<head>
  <meta charset="utf-8" />
  <link rel="icon" href="/favicon.ico" />
  <link rel="apple-touch-icon" sizes="180x180" href="favicon.png" />
  <link rel="stylesheet" href="https://cdn.datatables.net/1.11.3/css/jquery.dataTables.min.css">
  <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
  <script src="https://cdn.datatables.net/1.11.3/js/jquery.dataTables.min.js"></script>
  <title>奔向月球</title>
  <script>
  $(document).ready(
  function() {
    var currentTime = new Date();
    var currentHour = currentTime.getHours();
    var currentMinu = currentTime.getMinutes().toString().padStart(2, '0');
    var currentHM   = currentHour.toString() + currentMinu.toString();
    var defaultOrderColumn = 2;  // Sorting by update time

    console.log(currentHM);
    if ( currentHM >= 1700 )     // After 5:00 pm sorting by ratio
      defaultOrderColumn = 1;
  
    $('#example').DataTable({ "paging": false, "info": false, "searching": false,
      "order": [ [defaultOrderColumn, 'desc'] ]
    });
  });
  </script>
  <style>
    td {text-align: center;}
    table#example { margin-top: 0%; font-size: 2em; font-weight: bolder; width: 90%; }  /* for iPhone width */
    @media only screen and (min-width: 1200px) { table#example { width: 45%; } }        /* for PC desktop width */
  </style>
</head>
<body>
  <h1 title="更新時間" style="text-align: center;"><?php echo date("Y-m-d H:i"); ?></h1>
  <table id="example" class="display cell-border">
  <thead><tr><th>ID</th><th>%</th><th>VAL</th></tr></thead>
  <tbody>
<?php
/* Jack 2024, Jan - Get everyday fund value */
$Date_A = (new DateTime())->sub(new DateInterval('P14D'));  // Current-(14 days) for Chinese New Year vacation
$Date_A = $Date_A->format("Y-m-d");
$Date_B = date("Y-m-d");                                    // today

function getFundValue($ID, $cName, $flag)
{
  global $Date_A, $Date_B;

  if ( $flag == "FUND" )
    $url = "https://www.moneydj.com/funddj/bcd/tBCDNavList.djbcd?a={$ID}&B={$Date_A}&C={$Date_B}";
  elseif ( $flag === "ETF" )
    $url = "https://mis.twse.com.tw/stock/api/getStockInfo.jsp?ex_ch={$ID}";

  $str = file_get_contents($url);

  // Split the string into date and value pairs
  if ( $flag == "FUND" )
  {
    $pairs = explode(' ', $str);
    $date_array  = explode(',' , $pairs[0] );     // $date[]  = array('20240101', '20240102' ...)
    $value_array = explode(',' , $pairs[1] );     // $value[] = array('price1'  , 'price2'   ...)
  }
  elseif ( $flag === "ETF" )
  {
    $rtnData   = json_decode($str, true);
    $date_array  = array();
    $value_array = array();

    // [y]=yesterday value ;  [z]=today value ; [d]=today number
    array_push( $date_array , "DO_NOT_CARE_YESTERDAY" , $rtnData['msgArray'][0]["d"]);
    array_push( $value_array , $rtnData['msgArray'][0]["y"] , $rtnData['msgArray'][0]["z"] );
  }

  // [$last] means today value ; [$last-1] means yesterday value
  $last  = count( $value_array ) - 1;
  $ratio = round( (($value_array[$last]/$value_array[$last-1])-1)*100 , 2);

  // if day gain ratio not reasonable, return NULL
  if ( abs($ratio)> 20 ) $ratio = "*";

  echo "<tr>" . "<td>" . $cName . "</td>";

  // Check value is updated or not
  $updateFlag = ($date_array[$last] == date("Ymd"));

  // For negative ratio% , use GREEN
  if     ( $ratio < 0 && $updateFlag ) echo "<td style='color:green;'>" . $ratio . "</td>";
  elseif ( $ratio > 0 && $updateFlag ) echo "<td style='color:red;'>" . $ratio . "</td>";
  else                  echo "<td>" . $ratio . "</td>";

  echo "<td>" .$date_array[$last]  . ' | ' . round($value_array[$last], 2) . "</td>" . "</tr>";
}

getFundValue("tse_0050.tw", "0050", "ETF" );
getFundValue("ACDD04", "安聯台科*", "FUND");
getFundValue("ACPS10", "統一奔騰*", "FUND");
getFundValue("ACPS02", "統一黑馬" , "FUND");
getFundValue("acic01", "野村優質", "FUND");
getFundValue("ACIC04", "野村成長" , "FUND");
// end of PHP
?>
  </tbody></table>
  <h2 style="text-align: center;">
  <a style="text-decoration: none;" href="about:blank">#</a> | 
  <a style="text-decoration: none;" href="dca/" target="_blank">$</a> |
  <a style="text-decoration: none;" href="dca/fundPerfChartRender.html" target="_blank">%</a>
  </h2>
</body>
</html>
