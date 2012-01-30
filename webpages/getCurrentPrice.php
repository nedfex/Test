<?php

function getCurrentPrice($SYMBOL,$Update)
{	

	$query = "SELECT PRICE FROM `company` WHERE `SYMBOL` = '$SYMBOL';";
	$row = mysql_fetch_array(mysql_query($query));
	
	if($row['PRICE'] != Null && $Update == false)
		return $row['PRICE'];
	
	$target = "http://beta.investing.money.msn.com/investments/stock-price?symbol=".$SYMBOL;

	$web_page = http_get($target, "");
	
	$temp = parse_array( $web_page['FILE'], "<table class=\" mnytbl\" summary=\"\">", "</table>");
	$price = parse_array($temp[0] , "<span" , "/span>");
	//print_r($price);
	return strip_tags(trim($price[3]));
}

function getCurrentPE($SYMBOL)
{	

	$query = "SELECT * FROM `company` WHERE `SYMBOL` = '$SYMBOL';";
	$row = mysql_fetch_array(mysql_query($query));
	
	if( $row['PE'] != Null)
	{
		$ans['PRICE'] = $row['PRICE'];
		$ans['PE'] = $row['PE'];
		$ans['FORWARD_PE'] = $row['FORWARD_PE'];
		$ans['MSN_GROWTH'] = $row['MSN_GROWTH'];
		$ans['EPS'] = $row['EPS'];
		$ans['MARKET_CAP'] = $row['MARKET_CAP'];
	  $ans['SHARES_OUTSTANDING'] = $row['SHARES_OUTSTANDING'];
		 
		return $ans;
	}
	
	$target = "http://investing.money.msn.com/investments/stock-price?Symbol=".$SYMBOL;
	$web_page = http_get($target, "");
	$row_array = parse_array( $web_page['FILE'], "<tr" , "/tr>");
	
	$Price = SearchTr($row_array,"Previous Close");
	$CurrentPE = SearchTr($row_array,'P/E');
	$ForwardPE = SearchTr($row_array,"Forward P/E");
	$EPS = SearchTr($row_array,"EPS");
	$Market_Cap = SearchTr($row_array,"Market Cap");
	$Shares_Outstanding = SearchTr($row_array,'Shares Outstanding');

	//echo "!!!$Price[1] $CurrentPE[1] $ForwardPE[1] $EPS[1]!!!</br>";
	$ans['PRICE'] = parse_money_to_mil($Price[1]);
	$ans['PE'] = parse_money_to_mil($CurrentPE[1]);
	$ans['FORWARD_PE'] = parse_money_to_mil($ForwardPE[1]);
	$ans['EPS'] = parse_money_to_mil($EPS[1]);
	$ans['MARKET_CAP'] = parse_money_to_mil($Market_Cap[1]);
	$ans['SHARES_OUTSTANDING'] = parse_money_to_mil($Shares_Outstanding[1]);
		
	//msn growth	
	$target = "http://moneycentral.msn.com/investor/invsub/analyst/earnest.asp?Page=EarningsGrowthRates&symbol=".$SYMBOL;
	$web_page = http_get($target,"");
	$product_row_array = parse_array( $web_page['FILE'], "<tr","</tr>");
	$MSN_GROWTH = SearchTr( $product_row_array ,'Company' );
	$ans['MSN_GROWTH'] = NULL;
	
	if(count($MSN_GROWTH)!=0)
		$ans['MSN_GROWTH'] = substr($MSN_GROWTH[4],1,-2)/100;
		
	$query = "UPDATE `finance`.`company` SET `PRICE` = $ans[PRICE] ," ;
	if($ans['PE']!= NULL)
		$query.=" `PE` = $ans[PE] ," ;
	if($ans['FORWARD_PE']!= NULL)
		$query.=" `FORWARD_PE` = $ans[FORWARD_PE] ," ;
	if($ans['EPS']!= NULL)
		$query.=" `EPS` = $ans[EPS] ," ;
	if($ans['MARKET_CAP']!= NULL)
		$query.=" `MARKET_CAP` = $ans[MARKET_CAP] ," ;
	if($ans['MSN_GROWTH']!= NULL)
		$query.=" `MSN_GROWTH` = $ans[MSN_GROWTH] ," ;
	if($ans['SHARES_OUTSTANDING']!= NULL)
		$query.=" `SHARES_OUTSTANDING` = $ans[SHARES_OUTSTANDING] ";
	$query.=" WHERE `SYMBOL` = '$SYMBOL' ;";
	//echo $query;
	//echo "Catch Completeed...</br>";
	mysql_query($query);
	 
	return $ans;
}
?>