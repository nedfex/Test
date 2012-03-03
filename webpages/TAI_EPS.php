<?php
# for EPS
# CATCH SALES,EPS,TOTAL NET INCOME(TABLE1) and CURRENT ASSETS, LIBILITES,LONR TERM DEBT(TABLE 2)
function EPS_Sales_Income($SYMBOL,$link)
{
	include("/config/config.php");
	$fid = fopen('data.html','w');
	//$link = ConnectDB($SQL);
	$selectresult = mysql_select_db("finance",$link);
	
	$query = "SELECT * FROM `newest_date` WHERE `SYMBOL` = '$SYMBOL';";
	$result = mysql_fetch_array(mysql_query($query ));
	//$today['year'] = $result['ANNUAL'];
	
	//網頁改舊版 $target = "http://beta.moneycentral.msn.com/investor/invsub/results/statemnt.aspx?lstStatement=10YearSummary&Symbol=US:"."$SYMBOL";
	$target = "http://investing.money.msn.com/investments/financial-statements?symbol=".$SYMBOL;

	$web_page = http_get($target, "");
	//改回舊版$table_array = parse_array($web_page['FILE'], "ctl00_ctl00_ctl00_ctl00_HtmlBody_HtmlBody_HtmlBody_Column2_INCS", "/table");
	$table_array = parse_array($web_page['FILE'], "<table class=\" mnytbl\" summary=\"\">","</table");
	echo count($table_array);
	if(count($table_array)==0)
	{
		echo "EPS table not found ,please check EPS.php</br>";
		return;
	}
	
	$EPS_row = 0;
	
	$product_row_array = parse_array($table_array[0], "<tr", "</tr>");
	$temp_array = parse_array($product_row_array[0],"<th", "</th>");
	

	//fwrite( $fid , $web_page['FILE'] );
	for( $i = 0 ; $i < count($temp_array) ; $i++ ) # Record column Name
	{
		$col_name[$i] = trim(strip_tags(trim($temp_array[$i])));
		fwrite($fid,$col_name[$i]);
		echo "$col_name[$i]\n";
	}
	//fclose($fid);
	$col_name[0] = 'Date';
	
	for($i=1;$i<count($product_row_array);$i++)# Parse the Table
	{
		$temp_array = parse_array($product_row_array[$i],"<td", "</td>");
		
		for( $j = 0 ; $j < count($col_name) ; $j++ )
		{
			//$An = "20".substr($Ann[$xxx-1], -2);
			$product_array[$i-1][$col_name[$j]] = trim(strip_tags($temp_array[$j]));
		}
	}
	/*********************************** table2*/ 
	//舊版網頁第二個TABLE就是所需的$table_array = parse_array($web_page['FILE'], "ctl00_ctl00_ctl00_ctl00_HtmlBody_HtmlBody_HtmlBody_Column2_BALS", "/table");
	
	$product_row_array2 = parse_array($table_array[1], "<tr", "</tr>");
	$temp_array2 = parse_array($product_row_array2[0],"<th", "</th>");
	
	for($i=0;$i < count($temp_array2);$i++) # Record column Name
	{
		$col_name2[$i] = trim(strip_tags($temp_array2[$i]));
		echo "$col_name2[$i]\n";
	}
	$col_name2[0] = 'Date';
	
	for($i=1;$i < count($product_row_array2);$i++)# Parse the Table
	{
		$temp_array2 = parse_array($product_row_array2[$i],"<td", "</td>");
		for($j=0;$j < count($col_name2);$j++)
		{
			//$An = "20".substr($Ann[$xxx-1], -2);
			$product_array[$i-1][$col_name2[$j]] = trim(strip_tags($temp_array2[$j]));
		}
	}
	
	for($i=0; $i < count($product_row_array2)-1;$i++)
	{
		$product_array[$i]['Date'] = "20".substr($product_array[$i]['Date'], -2);
		//$product_array[$i]['Date'] = $today['year'] - $i;
	}	
	/****************/
	for($i=0; $i < count($product_row_array2)-1;$i++)// parse 10 years data
	{
		//echo count($product_row_array2)."grgr\n";
		$query = "UPDATE `finance`.`".$product_array[$i]['Date']."` SET "; 
		$query.="`".$COLUMN_ID['Total Revenue']."` = ".str_replace(",","",$product_array[$i]['SALES']).",";
		$query.="`".$COLUMN_ID['EPS']."` = ".str_replace(",","",$product_array[$i]['EPS'])."," ;
		$query.="`".$COLUMN_ID['Income After Tax']."` = ".str_replace(",","",$product_array[$i]['TOTAL NETINCOME'])."," ;
		$query.="`".$COLUMN_ID['Total Assets']."` = ".str_replace(",","",$product_array[$i]['CURRENTASSETS']).",";
		$query.="`".$COLUMN_ID['Total Liabilities']."` = ".str_replace(",","",$product_array[$i]['CURRENTLIABILITIES'])." ,";
		$query.="`".$COLUMN_ID['Total Long Term Debt']."` = ".str_replace(",","",$product_array[$i]['LONG TERMDEBT'])." ";
		$query.="WHERE `".$product_array[$i]['Date']."`.`SYMBOL` = '$SYMBOL' ;";  
		echo $query."</br>\n";
		//echo $product_array[$i]['Date'].$product_array[$i]['EPS']."</br>";
		echo(mysql_query($query));
	} 	
	
	//print_r($product_array);
	
	//mysql_close($link);
}


function EPS_Sales_IncomeMS($SYMBOL,$link)//20120211 , catch from moringstar
{
	$SYMBOL = 'ARO';
	include("/config/config.php");
	//$link = ConnectDB($SQL);
	$selectresult=mysql_select_db("finance",$link);
	
	$query = "SELECT * FROM `newest_date` WHERE `SYMBOL` = '$SYMBOL';";
	$result = mysql_fetch_array(mysql_query($query ));
	$today['year'] = $result['ANNUAL'];

	$target = "http://financials.morningstar.com/ratios/r.html?t=$SYMBOL&region=USA&culture=en-us";
	echo "$target.</br>";
	
	$web_page = http_get($target, "");
	
	$table_array = parse_array($web_page['FILE'], "<table class=\"r_table1 text2\"","</table");
	$fid = fopen('data.html','w');
	fwrite($fid,$web_page['FILE']);
	fclose($fid);
	
	if(count($table_array)==0)
	{
		echo "EPS table not found ,please check EPS.php</br>";
		return false;
	}                                    
	                                           
	$EPS_row = 0;
	
	$product_row_array = parse_array($table_array[0], "<tr", "</tr>");//第一個tr是年分
	$temp_array = parse_array($product_row_array[0],"<th", "</th>");//第一個th(左上角)是空的,第二個開始才有值
	
	for($i=0 ; $i < count($temp_array);$i++) # Record column Name
	{
		$col_name[$i] = str_replace(" ","",strip_tags(trim($temp_array[$i])));
		echo 'aa'.$col_name[$i]."aa";
	}
	return;
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
	//舊版網頁第二個TABLE就是所需的$table_array = parse_array($web_page['FILE'], "ctl00_ctl00_ctl00_ctl00_HtmlBody_HtmlBody_HtmlBody_Column2_BALS", "/table");
	
	$product_row_array2 = parse_array($table_array[1], "<tr", "</tr>");
	$temp_array2 = parse_array($product_row_array2[0],"<td", "</td>");
	
	for($i=0;$i < count($temp_array2);$i++) # Record column Name
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
		//mysql_query($query);
	} 	
	
	//print_r($product_array);
	
	//mysql_close($link);
	return true;
}

?>
