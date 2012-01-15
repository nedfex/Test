<?php
include( "/config/config.php" );	
include( "utility.php" );
include("..\W3C_lib\LIB_http.php");

$link = ConnectDB($SQL);
set_time_limit(3600);

/***** FILL MARKETCAP
$query= "SELECT * FROM `company`;";
$result =mysql_query($query,$link);
echo mysql_num_rows($result);
for($i=0;$i < mysql_num_rows($result);$i++)
{
	$row = mysql_fetch_array($result);

	//if($row['MARKET_CAP'] =="" || $row['MARKET_CAP']=="NULL")
	{
		$MC = $row['PRICE'] * $row['SHARES_OUTSTANDING'];
		$sql = "UPDATE `finance`.`company` SET `MARKET_CAP` = $MC WHERE `SYMBOL` = '$row[SYMBOL]';";
		mysql_query($sql);
		echo "$row[SYMBOL] FILLed...</br>";
	}
}
**************/

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
/***************************modify ROIC and ROE*/
//ROE = ($COLUMN_NAME[19] + $COLUMN_NAME[92] )/ total eqity(¥h¦~)
//$COLUMN_NAME[77] = 'Total Equity';$COLUMN_NAME[65] = 'Total Long Term Debt';

/*$years = array('2012','2011','2010','2009','2008','2007','2006','2005','2004');
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
			
			if( is_null($last_year_row[$COLUMN_ID['Total Equity']]) || $last_year_row[$COLUMN_ID['Total Equity']]==0 )
				continue;
			
			$ROE = ($this_year_row[$COLUMN_ID['Income After Tax']] + $this_year_row[$COLUMN_ID['Total Cash Dividends Paid']])/($last_year_row[$COLUMN_ID['Total Equity']]);
			$ROC = ($this_year_row[$COLUMN_ID['Income After Tax']] + $this_year_row[$COLUMN_ID['Total Cash Dividends Paid']])/($last_year_row[$COLUMN_ID['Total Equity']]+$last_year_row[$COLUMN_ID['Total Long Term Debt']]);
			
			//echo $ROE."--".$ROIC."</br>";
			$sql = "UPDATE `$years[$i]` SET `".$COLUMN_ID['ROE']."` = '$ROE' , `".$COLUMN_ID['ROC']."` = '$ROC' WHERE `SYMBOL` = '$this_year_row[SYMBOL]';";
			//echo $sql."\n";
			mysql_query($sql);
			//return;
		}
		
	}
}*/
/*** ROC 3,5 years average***/
$years = array('2011','2012','2010');
for( $i = 0 ; $i < count( $years ) ; $i++)
{
	$sql = "SELECT `SYMBOL` , `".$COLUMN_ID['ROE']."` , `".$COLUMN_ID['ROC']."` FROM `".$years[$i]."`;";
	$this_year_data = mysql_query($sql);
	
	for( $j = 0 ; $j < mysql_num_rows($this_year_data);$j++)
	{
		$this_year_row = mysql_fetch_array($this_year_data );
		
		$one_year_row   =  mysql_fetch_array( mysql_query( "SELECT `".$COLUMN_ID['ROE']."` , `".$COLUMN_ID['ROC']."` FROM `".($years[$i]-1)."` WHERE SYMBOL = '$this_year_row[SYMBOL]';"));
		$two_year_row   =  mysql_fetch_array( mysql_query( "SELECT `".$COLUMN_ID['ROE']."` , `".$COLUMN_ID['ROC']."` FROM `".($years[$i]-2)."` WHERE SYMBOL = '$this_year_row[SYMBOL]';"));
		$three_year_row =  mysql_fetch_array( mysql_query( "SELECT `".$COLUMN_ID['ROE']."` , `".$COLUMN_ID['ROC']."` FROM `".($years[$i]-3)."` WHERE SYMBOL = '$this_year_row[SYMBOL]';"));
		$four_year_row  =  mysql_fetch_array( mysql_query( "SELECT `".$COLUMN_ID['ROE']."` , `".$COLUMN_ID['ROC']."` FROM `".($years[$i]-4)."` WHERE SYMBOL = '$this_year_row[SYMBOL]';"));
		
		$ROE_mean_3_years = ($this_year_row[$COLUMN_ID['ROE']] + $one_year_row[$COLUMN_ID['ROE']] + $two_year_row[$COLUMN_ID['ROE']])/3;
		$ROE_mean_5_years = ($this_year_row[$COLUMN_ID['ROE']] + $one_year_row[$COLUMN_ID['ROE']] + $two_year_row[$COLUMN_ID['ROE']]+ $three_year_row[$COLUMN_ID['ROE']]+ $four_year_row[$COLUMN_ID['ROE']])/5;
		$ROC_mean_3_years = ($this_year_row[$COLUMN_ID['ROC']] + $one_year_row[$COLUMN_ID['ROC']] + $two_year_row[$COLUMN_ID['ROC']])/3;
		$ROC_mean_5_years = ($this_year_row[$COLUMN_ID['ROC']] + $one_year_row[$COLUMN_ID['ROC']] + $two_year_row[$COLUMN_ID['ROC']]+ $three_year_row[$COLUMN_ID['ROC']]+ $four_year_row[$COLUMN_ID['ROC']])/5;
		
		$sql = "UPDATE `$years[$i]` SET `".$COLUMN_ID['ROE 3 year']."` = '$ROE_mean_3_years' , `".$COLUMN_ID['ROE 5 year']."` = '$ROE_mean_5_years' , `".$COLUMN_ID['ROC 3 year']."` = '$ROC_mean_3_years' , `".$COLUMN_ID['ROC 5 year']."` = '$ROC_mean_5_years' WHERE `SYMBOL` = '$this_year_row[SYMBOL]';";
		mysql_query($sql);
		//echo, $sql."\n";
		//echo mysql_affected_rows($link);
		
	}
}

?>