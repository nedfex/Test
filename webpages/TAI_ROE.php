<?php
function ROE($SYMBOL,$link) //處裡自行計算之ROE 以及從MSN抓之一年期及五年期平均ROE(只有今年可以抓到一年期及五年期平均)
{
	include( "/config/config.php" );
	//include( "utility.php" );
	
	//$link = ConnectDB($SQL);
	$selectresult = mysql_select_db("finance",$link);
	$today = getdate();

	$query = "SELECT * FROM `newest_date` WHERE `SYMBOL` = '$SYMBOL';";
	$result = mysql_fetch_array(mysql_query($query ));
	$today['year'] = $result['ANNUAL']; 
	
	//舊版連結 $roe_target = $MSN_BASE.$MSN_PAGE[2].$MSN_PARAM['ROE'].$SYMBOL;
	$roe_target = "http://investing.money.msn.com/investments/key-ratios?symbol=".$SYMBOL."&page=InvestmentReturns";
	
	$web_page    = http_get($roe_target, "");
	$table_array = parse_array($web_page['FILE'], "<table", "</table>");
	$row_array   = parse_array($table_array[0], "<tr", "</tr>");
	
	$ROE_1_year      = SearchTrOld( $row_array , "Return On Equity" );
	$ROE_5_years_avg = SearchTrOld( $row_array , "Return On Equity (5-Year Avg.)" );
	
	$ROC_1_year      = SearchTrOld( $row_array , "Return On Capital" );
	$ROC_5_years_avg = SearchTrOld( $row_array , "Return On Capital (5-Year Avg.)" );

	$query  = "UPDATE `finance`.`$today[year]` SET ";
	$query .= "`".$COLUMN_ID['MSN ROE 1 year']."` = $ROE_1_year[1] ,";
	$query .= "`".$COLUMN_ID['MSN ROE 5 years avg']."` = $ROE_5_years_avg[1] ,";
	$query .= "`".$COLUMN_ID['MSN ROC 1 year']."` = $ROC_1_year[1] ,";
	$query .= "`".$COLUMN_ID['MSN ROC 5 years avg']."` = $ROC_5_years_avg[1] ";
	$query .= "WHERE `$today[year]`.`SYMBOL` = '$SYMBOL' ; ";
    
   //echo $query;
	//echo $query."</br>";
 	mysql_query($query);
 	
	for($Y=$today['year'] ;$Y>=$today['year']-7;$Y--)
 	{
 		$query = "SELECT  * FROM `".$Y."` WHERE `SYMBOL` = '$SYMBOL';";
 	
 		$result = mysql_query($query);
 		$row = mysql_fetch_array($result); 	
 		$Return  = $row[$COLUMN_ID['Income After Tax']]+$row[$COLUMN_ID['Total Cash Dividends Paid']];
 		$Income_After_Tax = $row[$COLUMN_ID['Income After Tax']];
 		
 		$query = "SELECT * FROM `".($Y-1)."` WHERE `SYMBOL` = '$SYMBOL'; ";//今年的income 除去年equity
 		$result = mysql_query($query);
 		$row = mysql_fetch_array($result); 
 		
 		$Equity  = $row[$COLUMN_ID['Total Equity']];
 		$Captical = $row[$COLUMN_ID['Total Equity']] + $row[$COLUMN_ID['Income After Tax']];
 		
 		$ROE = divide($Income_After_Tax,$Equity,NULL,NULL); 
 		$ROC = divide($Return,$Captical,NULL,NULL); 
 		
 		$query  = "UPDATE `finance`.`$Y` SET ";
		$query .= "`".$COLUMN_ID['ROE']."` = $ROE ,";
		$query .= "`".$COLUMN_ID['ROC']."` = $ROC ";
		$query .= "WHERE `$Y`.`SYMBOL` = '$SYMBOL' ; ";
		
		mysql_query($query);
		//echo $query."</br>";	
 	}
}
?>