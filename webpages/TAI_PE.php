<?php
# Initialization
// Catch AVG PE , BOOK VALUE/SHARE 
function PE_BOOK_VALUE($SYMBOL)
{
	include("/config/config.php");
	$link = ConnectDB($SQL);
	$selectresult = mysql_select_db("finance",$link);
	
	$query = "SELECT * FROM `newest_date` WHERE `SYMBOL` = '$SYMBOL';";
	$result = mysql_fetch_array(mysql_query($query ));
	$today['year'] = $result['ANNUAL'];  
	
	$Avg_PE_target = "http://beta.moneycentral.msn.com/investor/invsub/results/compare.asp?Page=TenYearSummary&Symbol=US:".$SYMBOL;
	$web_page = http_get($Avg_PE_target, "");
	$table_array = parse_array($web_page['FILE'], "<table", "</table>");
	
	if(count($table_array)==0)
		return;
	
	if(stristr( $table_array[0], "AVG P/E" ))
	{
	
		$product_row_array = parse_array($table_array[0], "<tr", "</tr>");
		$temp = parse_array($product_row_array[0], "<th", "</th>");		
		
		for($xxx=0;$xxx<count($temp);$xxx++)
			if(stristr( $temp[$xxx], "AVG P/E" )) 
				$Avg_PE_ind = $xxx;
		
		for($xxx=1;$xxx<count($product_row_array);$xxx++)
		{
			$temp = parse_array($product_row_array[$xxx], "<td", "</td>");
			$Ann[$xxx-1] = ($today['year']+1)-$xxx;
			//$Ann[$xxx-1] = "20".substr($Ann[$xxx-1], -2);
			
			/*if( table_exists($Ann[$xxx-1]) == false )
			{
				$CN = $Ann[$xxx-1];
	  		$query = "INSERT INTO `$CN` (`SYMBOL`) VALUES( '$SYMBOL' );";
	  		//echo $query."</br>";
	  		mysql_query($query);
			}*/
			
			$Avg_PE[$xxx-1] = strip_tags(trim($temp[$Avg_PE_ind]));			
			
			////echo $Ann[$xxx-1].' ';
			////echo $Avg_PE[$xxx-1]."</br>";		
		}
	}
	
	if(stristr( $table_array[1], "BOOK VALUE/ SHARE" ))
	{
	
		$product_row_array = parse_array($table_array[1], "<tr", "</tr>");
		$temp = parse_array($product_row_array[0], "<th", "</th>");		
		
		for($xxx=0;$xxx<count($temp);$xxx++)
			if(stristr( $temp[$xxx], "BOOK VALUE/SHARE" )) 
				$Avg_PE_ind = $xxx;
		
		for($xxx=1;$xxx<count($product_row_array);$xxx++)
		{
			$temp = parse_array($product_row_array[$xxx], "<td", "</td>");
			$BookValueShare[$xxx-1] = str_replace("$","",strip_tags(trim($temp[$Avg_PE_ind])));		
			$BookValueShare[$xxx-1] = str_replace(",","",$BookValueShare[$xxx-1]);
			
			////echo $Ann[$xxx-1].' ';
			////echo $BookValueShare[$xxx-1]."</br>";		
		}
	}
	
	$PE = 0;
	$PE_counter =0;
	for($i=0;$i<count($Ann);$i++)
	{
		$AN = $Ann[$i];
		$query = "UPDATE `finance`.`$AN` SET "; 
		$query.="`".$COLUMN_ID['Book Value/Share']."` = $BookValueShare[$i],";
		$query.="`".$COLUMN_ID['Avg P/E']."` = $Avg_PE[$i] " ;
		$query.="WHERE `$AN`.`SYMBOL` = '$SYMBOL' ;";  
		
		$query = str_replace("NA","NULL",$query);
                
		//echo $query."</br>";
  	mysql_query($query);
  	
  	if( is_numeric($Avg_PE[$i]) &&( $AN >= ($today['year']-9)))
  	{
  		////echo $Avg_PE[$i]."</br>";
  		$PE = $PE + $Avg_PE[$i];
  		$PE_counter++;
  	}
  	   
	}
	
	if($PE_counter==0)
		$PE_counter++;
			
 	$query = "UPDATE `finance`.`$today[year]` SET "; 
	$query.="`".$COLUMN_ID['PE']."` = ".($PE/$PE_counter)." " ;
	$query.="WHERE `$today[year]`.`SYMBOL` = '$SYMBOL' ;"; 
	mysql_query($query);
	//echo $query."</br>";
	
	mysql_close($link);
}


function table_exists($table) 
{ 

	$selectresult = mysql_select_db("finance",$link);
	$sql = "SHOW TABLES FROM `finance`;";
	$result = mysql_query($sql);
	
	while ($row = mysql_fetch_array($result)) 
	{
		//print_r($row);
		//echo "</br>";
    if(strcmp($table , $row[0]) == true)
    	return TRUE;
	}

	return FALSE;
}
?>