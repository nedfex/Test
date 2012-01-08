<?php
#Initialization
include("/config/config.php");
include("..\W3C_lib\LIB_http.php");
include("..\W3C_lib\LIB_parse.php");
set_time_limit(3600*3);

#Download the target (store) web page 
$sector_target = "http://www.google.com/finance";
//$domain = "http://biz.yahoo.com/p/";

$web_page = http_get($sector_target, "");

$table_array = parse_array($web_page['FILE'], "secperf", '</div');
//echo "div id=\"secperf\"";
echo "<font color = ''00ff00''>"."number of table ".count($table_array)."</font></br>";
$row_array = parse_array( $table_array[0], "<tr", "</tr>"); 
echo count($row_array)."rows</br>";
#for($x=1;$x<count($row_array);$x++) # drop first row
$DELAY = 30;
//$link = ConnectDB($SQL);
//echo $link."</br>".$selectresult;
$counter = 1;


for( $x=1 ; $x < count($row_array) ; $x+=2 ) # drop first row
{	
	$temp_array = parse_array( $row_array[$x] , "<td" , "</td>"); 
	$sector = strip_tags(trim($temp_array[0]));
	echo strip_tags(trim($temp_array[0]))."</br>";	

	$html_link = return_between( $temp_array[0] , "href=" , ">",'EXCL');
	$industry_target = str_replace( "\"" ,"", "http://www.google.com".$html_link);		
	
	echo $industry_target."</br>";	
	continue;
	
	$web_page2 = http_get($industry_target, "");
	$table_array2 = parse_array($web_page2['FILE'], "<td><table", "</table");
	$row_array2 = parse_array( $table_array2[0], "<tr", "</tr>"); 

	
	for($xx=3;$xx<count($row_array2);$xx++) # strat form index 3 (drop 3 rows)
	{	
		$temp_array2 = parse_array( $row_array2[$xx] , "<td" , "</td>");  	
		$html_link2  = return_between( $temp_array2[0] , "href=" , ">",'EXCL');
		$industry    = strip_tags(trim($temp_array2[0]));
		//echo "<td>".strip_tags(trim($temp_array2[0]))."</td>";
		
		$company_target = $domain.$html_link2;
		//echo $company_target."<br>";
		$web_page3 = http_get( $company_target, "");
		//$table_array3 = parse_array( $web_page3['FILE'], "<table", "</table");//got problem.
		$table_array3 = return_between($web_page3['FILE'],"<td><table", "</table",'EXCL');
		$row_array3   = parse_array( $table_array3, "<tr", "</tr>"); 		

		for($xxx=4;$xxx<count($row_array3);$xxx++) # strat form index 4 (drop 4 rows)
		{
				
			$temp_array3 = parse_array( $row_array3[$xxx] , "<td" , "</td>");
			$CompanyName =  strip_tags(trim($temp_array3[0]));
			$SYMBOL = parse_array( $CompanyName , "\(" , "\)" );

			$SYMBOL = $SYMBOL[count($SYMBOL)-1];
			$SYMBOL = str_replace( "(","" ,$SYMBOL );
			$SYMBOL = str_replace( ")","" ,$SYMBOL );
			
			$CompanyName = str_replace('('.$SYMBOL.')',"" ,$CompanyName );
			$query = "INSERT INTO company VALUES ('$sector','$industry','$CompanyName','$SYMBOL','0','$counter')";
			//mysql_query($query);
			$counter++;
			//echo $CompanyName.'--'.$SYMBOL.'--'."\r\n";
			
		}
		$random_delay = rand(1,$DELAY);
		echo $industry.'Completed...'."\r\n";
		sleep($random_delay);

	} 
}
//mysql_close($link);

?>                                                                                  