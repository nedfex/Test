<?php
#Initialization
include("..\W3C_lib\LIB_http.php");
include("..\W3C_lib\LIB_parse.php");
set_time_limit(3600*3);

#Download the target (store) web page 
$YChart_base = "http://ycharts.com";
$sector_target = "http://ycharts.com/sectors";

$web_page = http_get($sector_target, "");

$table_array = parse_array($web_page['FILE'], "<table", "</table");
$row_array = parse_array( $table_array[0], "<tr", "</tr>"); 

$DELAY = 30;
$link = ConnectDB($SQL);
$selectresult = mysql_select_db("finance",$link);

$counter = 1;

if( $selectresult )
{
	for( $x=1 ; $x <= count($row_array) ; $x++ ) # drop first row
	{	
		$temp_array = parse_array( $row_array[$x] , "<th" , "</th"); 	
		$flink = return_between($temp_array[0], "href=\"", "\"", 'EXCL');
		
		$temp_array2 = parse_array( $temp_array[0] , "<a" , "</a");
		$sector = strip_tags(trim($temp_array2[0]));//get sector
			
		//echo $sector."</br>";
		//echo $YChart_base.$flink;
		
		$web_page_industry = http_get( $YChart_base.$flink, "");
		$table_array2 = parse_array($web_page_industry['FILE'], "<table", "</table");
		$row_array2 = parse_array( $table_array2[0], "<tr", "</tr>"); 
		
		for( $xx=1 ; $xx < count($row_array2) ; $xx++ ) 
		{	
			$temp_array = parse_array( $row_array2[$xx] , "<th" , "</th"); 	
			
			$temp_array2 = parse_array( $temp_array[0] , "<a" , "</a");
			$industry = strip_tags(trim($temp_array2[0]));//get industry
			
			$ilink = return_between($temp_array[0], "href=\"", "\"", 'EXCL');
			
			//echo $industry."</br>";
			
			$web_page_company = http_get( $YChart_base.$ilink, "");
			$table_array3 = parse_array($web_page_company['FILE'], "<table", "</table");
			$row_array3 = parse_array( $table_array3[0], "<tr", "</tr>");
			
			for( $xxx=1 ; $xxx < count($row_array3) ; $xxx++ ) 
			{	
				$temp_array = parse_array( $row_array3[$xxx] , "<th" , "</th"); 	
				
				$temp_array2 = parse_array( $temp_array[0] , "<a" , "</a");
				$CompanyName = strip_tags(trim($temp_array2[0]));//get company
				
				$clink = return_between($temp_array[0], "href=\"", "\"", 'EXCL');
				$SYMBOL = return_between($temp_array[0], "companies/", "\"", 'EXCL');
				
				//echo $CompanyName.$SYMBOL."</br>";
				
				$query = "INSERT INTO companyY VALUES ('$sector','$industry','$CompanyName','$SYMBOL','0','$counter','0','0','0','0','0','0','0')";
				mysql_query($query);
				$counter++;						
			}
			
			$random_delay = rand(1,$DELAY);
			echo $industry.'Completed...'."\r\n";
			sleep($random_delay); 
						
		}
			
	}
	
	
	
	mysql_close($link);
}
else
{
	echo "DataBase link Error";
}


?>                                                                                  