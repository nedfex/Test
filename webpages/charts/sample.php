<?php
//include charts.php to access the SendChartData function
include "charts.php";

//the chart's data
$chart [ 'chart_data' ] = array ( array ( "",         "2001", "2002", "2003", "2004" ),
                                  array ( "Region A",     5,     10,     30,     63  ),
                                  array ( "Region B",   100,     20,     65,     55  ),
                                  array ( "Region C",    56,     21,      5,     90  )
                                );
 
//send the new data to charts.swf
SendChartData ( $chart );

?>
