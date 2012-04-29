<?php
include("..\..\W3C_lib\LIB_http.php");
include("..\..\W3C_lib\LIB_parse.php");
include("..\..\W3C_lib\LIB_SearchTr.php");
include("..\..\W3C_lib\LIB_resolve_addresses.php");
include("..\config\config.php");
include("..\utility.php");

$link = ConnectDB($SQL);
set_time_limit(3600);

//***** FILL MARKETCAP
//$query= "SELECT * FROM `company`;";
//$result =mysql_query($query,$link);
//echo mysql_num_rows($result);
//for($i=0;$i < mysql_num_rows($result);$i++)
//{
//	$row = mysql_fetch_array($result);
//
//	//if($row['MARKET_CAP'] =="" || $row['MARKET_CAP']=="NULL")
//	{
//		$MC = $row['PRICE'] * $row['SHARES_OUTSTANDING'];
//		$sql = "UPDATE `finance`.`company` SET `MARKET_CAP` = $MC WHERE `SYMBOL` = '$row[SYMBOL]';";
//		mysql_query($sql);
//		echo "$row[SYMBOL] FILLed...</br>";
//	}
//}
//**************/

/********* PROCESSING INSERT INTO INTRODUCTION*
$query= "SELECT * FROM `companyy`;";
$result =mysql_query($query,$link);
echo mysql_num_rows($result);
for($i=0;$i < mysql_num_rows($result);$i++)
{
	$row = mysql_fetch_array($result);
	{
		$sql = "INSERT INTO `intro` (`SYMBOL`) VALUES( '$row[SYMBOL]');";
		mysql_query($sql);
		echo "$row[SYMBOL] INSERT...</br>";
	}
}
/***************************
$query = 'SELECT DISTINCT COUNTRY_SYMBOL FROM companyb';
$result = mysql_query($query,$link);
//echo mysql_num_rows($result);
$fp = fopen('excange.txt','w');
for($i=0;$i < mysql_num_rows($result);$i++)
{
	$row = mysql_fetch_array($result);
	if( strlen($row['COUNTRY_SYMBOL'] )!=0 )
	{
		$query = "SELECT * FROM `companyb` WHERE COUNTRY_SYMBOL = '$row[COUNTRY_SYMBOL]' LIMIT 1;";
		$result2 = mysql_query($query);
		$row2 = mysql_fetch_array($result2);
		echo $row2['SYMBOL'].' - '.$row2['COUNTRY_SYMBOL'].'<br>';
		fprintf($fp,"%s\t%s\n", "$row2[SYMBOL]" , "$row2[COUNTRY_SYMBOL]");
	}
}

fclose($fp);

/***************************modify ROIC and ROE*
//ROE = ($COLUMN_NAME[19] + $COLUMN_NAME[92] )/ total eqity(�h�~)
//$COLUMN_NAME[77] = 'Total Equity';$COLUMN_NAME[65] = 'Total Long Term Debt';

$years = array('2012','2011','2010','2009','2008','2007','2006','2005','2004');
print_r($years);
for($i = 0;$i < count( $years ) ; $i++)
{
	$sql = "SELECT `SYMBOL` , `".$COLUMN_ID['Income After Tax']."` , `".$COLUMN_ID['Total Cash Dividends Paid']."` FROM `".$years[$i]."`;";
	$this_year_data = mysql_query($sql);
	//echo $sql."</br>";
	
	for($j = 0; $j < mysql_num_rows($this_year_data);$j++)
	{
		$this_year_row = mysql_fetch_array($this_year_data );
		//print_r($this_year_row);
		$sql = "SELECT `SYMBOL` , `".$COLUMN_ID['Total Equity']."`  , `".$COLUMN_ID['Total Long Term Debt']."` FROM `".($years[$i]-1)."` WHERE `SYMBOL` = '$this_year_row[SYMBOL]';";
		//echo $sql."</br>";
		$last_year_data = mysql_query($sql);
		
		if(mysql_num_rows($last_year_data)!=0)
		{
			$last_year_row = mysql_fetch_array($last_year_data);
			
			if( is_null($last_year_row[$COLUMN_ID['Total Equity']]) )
				continue;
			
			$ROE = ($this_year_row[$COLUMN_ID['Income After Tax']] + $this_year_row[$COLUMN_ID['Total Cash Dividends Paid']])/($last_year_row[$COLUMN_ID['Total Equity']]);
			$ROC = ($this_year_row[$COLUMN_ID['Income After Tax']] + $this_year_row[$COLUMN_ID['Total Cash Dividends Paid']])/($last_year_row[$COLUMN_ID['Total Equity']]+$last_year_row[$COLUMN_ID['Total Long Term Debt']]);
			
			//echo $ROE."--".$ROIC."</br>";
			$sql = "UPDATE `$years[$i]` SET `".$COLUMN_ID['ROE']."` = '$ROE' , `".$COLUMN_ID['ROC']."` = '$ROC' WHERE `SYMBOL` = '$this_year_row[SYMBOL]';";
			mysql_query($sql);
			//echo mysql_query($sql);
			//return;
		}
		
	}
}
*** ROC 3,5 years average***/
//$years = array('2011','2012','2010');
//for( $i = 0 ; $i < count( $years ) ; $i++)
//{
//	$sql = "SELECT `SYMBOL` , `".$COLUMN_ID['ROE']."` , `".$COLUMN_ID['ROC']."` FROM `".$years[$i]."`;";
//	$this_year_data = mysql_query($sql);
//	
//	for( $j = 0 ; $j < mysql_num_rows($this_year_data);$j++)
//	{
//		$this_year_row = mysql_fetch_array($this_year_data );
//		
//		$one_year_row   =  mysql_fetch_array( mysql_query( "SELECT `".$COLUMN_ID['ROE']."` , `".$COLUMN_ID['ROC']."` FROM `".($years[$i]-1)."` WHERE SYMBOL = '$this_year_row[SYMBOL]';"));
//		$two_year_row   =  mysql_fetch_array( mysql_query( "SELECT `".$COLUMN_ID['ROE']."` , `".$COLUMN_ID['ROC']."` FROM `".($years[$i]-2)."` WHERE SYMBOL = '$this_year_row[SYMBOL]';"));
//		$three_year_row =  mysql_fetch_array( mysql_query( "SELECT `".$COLUMN_ID['ROE']."` , `".$COLUMN_ID['ROC']."` FROM `".($years[$i]-3)."` WHERE SYMBOL = '$this_year_row[SYMBOL]';"));
//		$four_year_row  =  mysql_fetch_array( mysql_query( "SELECT `".$COLUMN_ID['ROE']."` , `".$COLUMN_ID['ROC']."` FROM `".($years[$i]-4)."` WHERE SYMBOL = '$this_year_row[SYMBOL]';"));
//		
//		$ROE_mean_3_years = ($this_year_row[$COLUMN_ID['ROE']] + $one_year_row[$COLUMN_ID['ROE']] + $two_year_row[$COLUMN_ID['ROE']])/3;
//		$ROE_mean_5_years = ($this_year_row[$COLUMN_ID['ROE']] + $one_year_row[$COLUMN_ID['ROE']] + $two_year_row[$COLUMN_ID['ROE']]+ $three_year_row[$COLUMN_ID['ROE']]+ $four_year_row[$COLUMN_ID['ROE']])/5;
//		$ROC_mean_3_years = ($this_year_row[$COLUMN_ID['ROC']] + $one_year_row[$COLUMN_ID['ROC']] + $two_year_row[$COLUMN_ID['ROC']])/3;
//		$ROC_mean_5_years = ($this_year_row[$COLUMN_ID['ROC']] + $one_year_row[$COLUMN_ID['ROC']] + $two_year_row[$COLUMN_ID['ROC']]+ $three_year_row[$COLUMN_ID['ROC']]+ $four_year_row[$COLUMN_ID['ROC']])/5;
//		
//		$sql = "UPDATE `$years[$i]` SET `".$COLUMN_ID['ROE 3 year']."` = '$ROE_mean_3_years' , `".$COLUMN_ID['ROE 5 year']."` = '$ROE_mean_5_years' , `".$COLUMN_ID['ROC 3 year']."` = '$ROC_mean_3_years' , `".$COLUMN_ID['ROC 5 year']."` = '$ROC_mean_5_years' WHERE `SYMBOL` = '$this_year_row[SYMBOL]';";
//		mysql_query($sql);
//		//echo, $sql."\n";
//		//echo mysql_affected_rows($link);
//		
//	}
//}

/*** ROC 3,5 years average(Moring Star)***
mysql_select_db('finance_ms',$link);
$years = array('2011','2012','2010');
for( $i = 0 ; $i < count( $years ) ; $i++)
{
	$sql = "SELECT `TICKER` , `Return on Equity` , `Return on Invested Capital` FROM `".$years[$i]."_keyratio`;";
	//echo $sql;
	$this_year_data = mysql_query($sql);
	
	for( $j = 0 ; $j < mysql_num_rows($this_year_data);$j++)
	{
		$this_year_row = mysql_fetch_array($this_year_data );
		
		$one_year_row    =  mysql_fetch_array( mysql_query( "SELECT `Return on Equity` , `Return on Invested Capital` FROM `".($years[$i]-1)."_keyratio` WHERE `TICKER` = '$this_year_row[TICKER]';"));
		$two_year_row    =  mysql_fetch_array( mysql_query( "SELECT `Return on Equity` , `Return on Invested Capital` FROM `".($years[$i]-2)."_keyratio` WHERE `TICKER` = '$this_year_row[TICKER]';"));
		$three_year_row  =  mysql_fetch_array( mysql_query( "SELECT `Return on Equity` , `Return on Invested Capital` FROM `".($years[$i]-3)."_keyratio` WHERE `TICKER` = '$this_year_row[TICKER]';"));
		$four_year_row   =  mysql_fetch_array( mysql_query( "SELECT `Return on Equity` , `Return on Invested Capital` FROM `".($years[$i]-4)."_keyratio` WHERE `TICKER` = '$this_year_row[TICKER]';"));
		$five_year_row   =  mysql_fetch_array( mysql_query( "SELECT `Return on Equity` , `Return on Invested Capital` FROM `".($years[$i]-5)."_keyratio` WHERE `TICKER` = '$this_year_row[TICKER]';"));
		$six_year_row    =  mysql_fetch_array( mysql_query( "SELECT `Return on Equity` , `Return on Invested Capital` FROM `".($years[$i]-6)."_keyratio` WHERE `TICKER` = '$this_year_row[TICKER]';"));
		$seven_year_row  =  mysql_fetch_array( mysql_query( "SELECT `Return on Equity` , `Return on Invested Capital` FROM `".($years[$i]-7)."_keyratio` WHERE `TICKER` = '$this_year_row[TICKER]';"));
		$eight_year_row  =  mysql_fetch_array( mysql_query( "SELECT `Return on Equity` , `Return on Invested Capital` FROM `".($years[$i]-8)."_keyratio` WHERE `TICKER` = '$this_year_row[TICKER]';"));
		$nine_year_row   =  mysql_fetch_array( mysql_query( "SELECT `Return on Equity` , `Return on Invested Capital` FROM `".($years[$i]-9)."_keyratio` WHERE `TICKER` = '$this_year_row[TICKER]';"));

		$ROE_mean_3_years = ($this_year_row['Return on Equity'] + $one_year_row['Return on Equity'] + $two_year_row['Return on Equity'])/3;
		$ROE_mean_5_years = ($this_year_row['Return on Equity'] + $one_year_row['Return on Equity'] + $two_year_row['Return on Equity']+ $three_year_row['Return on Equity']+ $four_year_row['Return on Equity'])/5;
		$ROE_mean_9_years = ($ROE_mean_5_years*5+$five_year_row['Return on Equity'] + $six_year_row['Return on Equity']+ $seven_year_row['Return on Equity']+ $eight_year_row['Return on Equity'])/9;
		$ROC_mean_3_years = ($this_year_row['Return on Invested Capital'] + $one_year_row['Return on Invested Capital'] + $two_year_row['Return on Invested Capital'])/3;
		$ROC_mean_5_years = ($this_year_row['Return on Invested Capital'] + $one_year_row['Return on Invested Capital'] + $two_year_row['Return on Invested Capital']+ $three_year_row['Return on Invested Capital']+ $four_year_row['Return on Invested Capital'])/5;
		$ROC_mean_9_years = ($ROC_mean_5_years*5+$five_year_row['Return on Invested Capital'] + $six_year_row['Return on Invested Capital']+ $seven_year_row['Return on Invested Capital']+ $eight_year_row['Return on Invested Capital'])/9;
		
		$sql = "UPDATE `$years[$i]"."_keyratio` SET `Return on Equity 3 year` = '$ROE_mean_3_years' , `Return on Equity 5 year` = '$ROE_mean_5_years' ,`Return on Equity 9 year` = '$ROE_mean_9_years' ,";
		$sql.="`Return on Invested Capital 3 year` = '$ROC_mean_3_years' , `Return on Invested Capital 5 year` = '$ROC_mean_5_years' , `Return on Invested Capital 9 year` = '$ROC_mean_5_years'";
		$sql.= " WHERE `TICKER` = '$this_year_row[TICKER]';";
		mysql_query($sql);
		echo $sql."\n";
		//echo mysql_affected_rows($link);
	}
}



/**************************/
//$query= "SELECT * FROM`newest_date` WHERE`ANNUAL`<100";
//$result =mysql_query($query,$link);
//echo mysql_num_rows($result);
//for($i=0;$i<mysql_num_rows($result);$i++)
//{
//	$row = mysql_fetch_array($result);
//	echo $row['SYMBOL'].$row['ANNUAL']."</br>";
//	
//	for($j=0;$j<10;$j++)
//	{
//		$Y = 2012-$j;
//		$r = mysql_query($sql = "SELECT * FROM `$Y` WHERE `SYMBOL` = '$row[SYMBOL]';");
//		if(mysql_num_rows($r)!=0)
//		{
//			$sql = "UPDATE `newest_date` SET `ANNUAL`='$Y' WHERE `SYMBOL` = '$row[SYMBOL]';";
//			echo $sql."</br>";
//			mysql_query($sql);
//			break;
//			
//		}
//		
//		}	
//}

/*********insert into newestDate**/
//$query= "SELECT * FROM `companyb3`;";
//$selectresult = mysql_select_db('finance_ms',$link);
//$result =mysql_query($query,$link);
//echo mysql_num_rows($result);
//
//for($i=0;$i < mysql_num_rows($result);$i++)
//{
//	$row = mysql_fetch_array($result);
//	//echo $row['TICKER']."</br>";
//	$sql = "INSERT INTO `newest_date` (`TICKER` ,`ACTIVE` ) VALUES( '$row[TICKER]' , '0');";
//	mysql_query($sql,$link);
//	
//}
/************check annual report and set active in newest_data*****************/

//$selectresult = mysql_select_db('finance_ms',$link);
//
//for($Y = 2000; $Y <= 2012;$Y++)
//{
//	$sql = "SELECT * FROM `$Y"."_bs`;";
//	$result = mysql_query($sql,$link);
//	echo "$Y : ".mysql_num_rows($result)."\n";
//	
//	for($j=0;$j< mysql_num_rows($result);$j++)
//	{
//		$row = mysql_fetch_array($result);
//		$u = "UPDATE `newest_date` SET `ANNUAL`='$Y' ,`ACTIVE`='1'  WHERE `TICKER` = '$row[TICKER]';";
//		$updateResult = mysql_query($u);
//		if(!$updateResult)
//			echo "UPDATE ERROR\n";
//	}
//	
//	
//}
/************ Update keyratio data into each year BS IS CF data*****************/

mysql_select_db('finance_ms',$link);
for($i=2004;$i>=2000;$i--)
{
	echo $i."\n";
	$sql = "SELECT * FROM `$i"."_keyratio`";
	//$sql = "SELECT * FROM `$i"."_keyratio` WHERE `TICKER` = 'WLT:US'";
	$result = mysql_query($sql);
	for($j=0;$j<mysql_num_rows($result);$j++)
	{
		$row =  mysql_fetch_array($result);
		
		if(mysql_fetch_array(mysql_query( "SELECT * FROM `$i"."_is` WHERE `TICKER` = '$row[TICKER]';"))==0)
			mysql_query( "INSERT INTO `$i"."_is` (`TICKER`) VALUES( '$row[TICKER]');");
		
		$result2 = mysql_query( "SELECT * FROM `$i"."_is` WHERE `TICKER` = '$row[TICKER]';");
		
		if(mysql_num_rows($result2)>0)
		{
					
			$row2 = mysql_fetch_array($result2);
			if( !is_null($row['Revenue']) && is_null($row2['Revenue']) )
			{
				$sql =  "UPDATE `$i"."_is` SET `Revenue`= '$row[Revenue]' WHERE `TICKER` = '$row[TICKER]';";
				if(!mysql_query($sql))
					echo $sql."\n";
			}
			if( !is_null($row['Operating Income']) && is_null($row2['Operating income']))
			{
				$sql =  "UPDATE `$i"."_is` SET `Operating income`= '".$row['Operating Income']."' WHERE `TICKER` = '$row[TICKER]';";
				if(!mysql_query($sql))
					echo $sql."\n";
			}
			if( !is_null($row['Net Income']) && is_null($row2['Net income']))
			{
				$sql =  "UPDATE `$i"."_is` SET `Net income`='".$row['Net Income']."' WHERE `TICKER` = '$row[TICKER]';";
				if(!mysql_query($sql))
					echo $sql."\n";
			}
			
		}
		
		if(mysql_fetch_array(mysql_query( "SELECT * FROM `$i"."_cf` WHERE `TICKER` = '$row[TICKER]';"))==0)
			mysql_query( "INSERT INTO `$i"."_cf` (`TICKER`) VALUES( '$row[TICKER]');");
		
		$result2 = mysql_query( "SELECT * FROM `$i"."_cf` WHERE `TICKER` = '$row[TICKER]';");
		if(mysql_num_rows($result2)>0)
		{
			$row2 = mysql_fetch_array($result2);
			if( !is_null($row['Operating Cash Flow']) && is_null($row2['Operating cash flow']) )
			{
				$sql =  "UPDATE `$i"."_cf` SET `Operating cash flow`= '".$row['Operating Cash Flow']."' WHERE `TICKER` = '$row[TICKER]';";
				if(!mysql_query($sql))
					echo $sql."\n";
			}
			if( !is_null($row['Cap Spending']) && is_null($row2['Capital expenditure']))
			{
				$sql =  "UPDATE `$i"."_cf` SET `Capital expenditure`= '".$row['Cap Spending']."' WHERE `TICKER` = '$row[TICKER]';";
				if(!mysql_query($sql))
					echo $sql."\n";
			}
			if( !is_null($row['Free Cash Flow']) && is_null($row2['Free Cash Flow']))
			{
				$sql =  "UPDATE `$i"."_cf` SET `Free Cash Flow`='".$row['Free Cash Flow']."' WHERE `TICKER` = '$row[TICKER]';";
				if(!mysql_query($sql))
					echo $sql."\n";
				//return;
			}
				
			
		}	
	}
}
/************************************/
?>
