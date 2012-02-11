<?php
# for EPS
# CATCH SALES,EPS,TOTAL NET INCOME(TABLE1) and CURRENT ASSETS, LIBILITES,LONR TERM DEBT(TABLE 2)
function EPS_Sales_Income($SYMBOL,$link)
{
	include("/config/config.php");
	//$link = ConnectDB($SQL);
	$selectresult=mysql_select_db("finance",$link);
	
	$query = "SELECT * FROM `newest_date` WHERE `SYMBOL` = '$SYMBOL';";
	$result = mysql_fetch_array(mysql_query($query ));
	$today['year'] = $result['ANNUAL'];  
	
	//ºô­¶§ïÂÂª© $target = "http://beta.moneycentral.msn.com/investor/invsub/results/statemnt.aspx?lstStatement=10YearSummary&Symbol=US:"."$SYMBOL";
	$target = "http://beta.investing.money.msn.com/investments/financial-statements?symbol=US%3a".$SYMBOL;
	
	$web_page = http_get($target, "");
	$table_array = parse_array($web_page['FILE'], "ctl00_ctl00_ctl00_ctl00_HtmlBody_HtmlBody_HtmlBody_Column2_INCS", "/table");
	                                    
	if(count($table_array)==0)
	{
		echo "EPS table not found ,please check EPS.php</br>";
		return;
	}                                    
	                                           
	$EPS_row = 0;
	
	$product_row_array = parse_array($table_array[0], "<tr", "</tr>");
	$temp_array = parse_array($product_row_array[0],"<td", "</td>");
	
	for($i=0;$i<count($temp_array);$i++) # Record column Name
		$col_name[$i] = strip_tags(trim($temp_array[$i]));
	$col_name[0] = 'Date';
	
	for($i=1;$i<count($product_row_array);$i++)# Parse the Table
	{
		$temp_array = parse_array($product_row_array[$i],"<td", "</td>");
		
		for($j=0;$j<count($col_name);$j++)
		{
		//$An = "20".substr($Ann[$xxx-1], -2);
		$product_array[$i-1][$col_name[$j]] = strip_tags(trim($temp_array[$j]));
		}
	}
	/*********************************** table2*/ 
	$table_array = parse_array($web_page['FILE'], "ctl00_ctl00_ctl00_ctl00_HtmlBody_HtmlBody_HtmlBody_Column2_BALS", "/table");
	
	$product_row_array2 = parse_array($table_array[0], "<tr", "</tr>");
	$temp_array2 = parse_array($product_row_array2[0],"<td", "</td>");
	
	for($i=0;$i<count($temp_array2);$i++) # Record column Name
		$col_name2[$i] = strip_tags(trim($temp_array2[$i]));
	$col_name2[0] = 'Date';
	
	for($i=1;$i < count($product_row_array2);$i++)# Parse the Table
	{
		$temp_array2 = parse_array($product_row_array2[$i],"<td", "</td>");
		for($j=0;$j<count($col_name2);$j++)
		{
			//$An = "20".substr($Ann[$xxx-1], -2);
			$product_array[$i-1][$col_name2[$j]] = strip_tags(trim($temp_array2[$j]));
		}
	}
	
	for($i=0; $i < count($product_row_array2)-1;$i++)
	{
		//$product_array[$i]['Date'] = "20".substr($product_array[$i]['Date'], -2);
		$product_array[$i]['Date'] = $today['year'] - $i;
	}	
	/****************/
	for($i=0; $i < count($product_row_array2)-1;$i++)// parse 10 years data
	{
		//echo count($product_row_array2)."grgr\n";
		$query = "UPDATE `finance`.`".$product_array[$i]['Date']."` SET "; 
		$query.="`".$COLUMN_ID['Total Revenue']."` = ".str_replace(",","",$product_array[$i]['Sales']).",";
		$query.="`".$COLUMN_ID['EPS']."` = ".str_replace(",","",$product_array[$i]['EPS'])."," ;
		$query.="`".$COLUMN_ID['Income After Tax']."` = ".str_replace(",","",$product_array[$i]['Total Net Income'])."," ;
		$query.="`".$COLUMN_ID['Total Assets']."` = ".str_replace(",","",$product_array[$i]['Current Assets']).",";
		$query.="`".$COLUMN_ID['Total Liabilities']."` = ".str_replace(",","",$product_array[$i]['Current Liabilities'])." ,";
		$query.="`".$COLUMN_ID['Total Long Term Debt']."` = ".str_replace(",","",$product_array[$i]['Long Term Debt'])." ";
		$query.="WHERE `".$product_array[$i]['Date']."`.`SYMBOL` = '$SYMBOL' ;";  
		//echo $query."</br>";
		//echo $product_array[$i]['Date'].$product_array[$i]['EPS']."</br>";
		mysql_query($query);
	} 	
	
	//print_r($product_array);
	
	//mysql_close($link);
}
?>