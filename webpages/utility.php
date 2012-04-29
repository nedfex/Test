<?php
function ConnectDB($SQL)
{
	$link = mysql_connect($SQL['address'],$SQL['user'],$SQL['password']);
	$selectresult = mysql_select_db($SQL['database'],$link);

	return $link;
}
function TestConnection()
{
	return true;
}
function divide($A,$B,$BZeroDefault,$NuLLDefault)
{
	if($B == 0)
		return $BZeroDefault;
	else if( $A == NULL || $B == NULL)
		return $NuLLDefault;
	else
		return $A/$B;
}
function roundpoint2($A)
{
	if (is_null($A)  )
		return NULL;
	else if ($A == 0)
		return 0;
	else
		return round($A*100)/100;
}
function changetopercent($A)
{
	if (is_null($A) )
		return 'NA';
	else
		return roundpoint2($A*100);
}
function nonNullmean($a)
{
	$counter = 0;
	$s = 0;
	for($i=0;$i<count($a);$i++)
	{
		if(is_numeric($a[$i]))
		{
			$s += $a[$i];
			$counter++;
		}	
	}
	
	if($counter == 0)
		return 10000000;
	else
		return $s/$counter;
}
function table_exist($table)
{
	if( mysql_num_rows( mysql_query("SHOW TABLES LIKE '".$table."'")) != 0 )
		return True;
	else
		return False;
}

function growth_rate($start,$end,$years)
{
	if( $start == Null || $end == Null || $end == 0)
		return Null;
	else
	{
		if( $end >= $start)
			return pow( $end/$start , 1/$years )-1;
		else
		{
			return -(1-pow( $end/$start , 1/$years ));
		}
	}
}
function rule72($start,$end,$years)
{
	if( $start == Null || $end == Null || $end == 0)
		return Null;
	else
	{
		if( $end >= $start)
			return pow( $end/$start , 1/$years )-1;
		else
		{
			return -(1-pow( $end/$start , 1/$years ));
		}
	}
}
function check_element($element)
{
	if( is_null($element) )
		return 'N/A';
	else
	return $element;
}
function return_annual($period)
{
	$month = substr($period,0,2);
	$year = substr($period,3,4);
	
	if( $month == '01' || $month == '02' || $month == '03' || $month == '04' || $month == '05' || $month == '06' )
	{
		echo $period." ".$month." ".$year."-".('20'.$year-1)."</br>";
		return ('20'.$year)-1;
	}
	else
	{
		echo $period." ".$month." ".$year."-".('20'.$year)."</br>";
		return '20'.$year;
	}

}
function setCompanyActive($SYMBOL,$link)
{
	//$link = mysql_connect('127.0.0.1','root','721215');
	$selectresult = mysql_select_db("finance");
	$query = "UPDATE `newest_date` SET `ACTIVE` = 1 WHERE `newest_date`.`SYMBOL` = '$SYMBOL' ;"; 
	mysql_query($query);
	//mysql_close($link);
}
function checkActive($SYMBOL)
{
	//$link = mysql_connect('127.0.0.1','root','721215');
	$selectresult = mysql_select_db("finance");
	$query = "SELECT * FROM `newest_date` WHERE `SYMBOL` = '$SYMBOL' ;"; 
	$result = mysql_query($query);
	//mysql_close($link);
	
	if( $result == False || mysql_num_rows($result) == 0)
		return False;
	else
	{
		$rows = mysql_fetch_array($result);
		if($rows['ACTIVE']==0)
			return False;
	}
	return True;
	
}
function parse_money_to_mil($money)
{

//echo $money;
	if( strstr($money,'NA') || strstr($money,'na') || strstr($money,'N/A') ||strstr($money,'n/a'))
		return NULL;

	$posB = strstr( strtolower($money) , 'b');
	$posM = strstr( strtolower($money) , 'm');
	
	$ans = str_replace('bil','',strtolower($money));
	$ans = str_replace('mil','',strtolower($money));
	$ans = str_replace('$','',$ans);
	$ans = str_replace(' ','',$ans);
	$ans = str_replace(',','.',$ans);
	
	if($posB)
		$ans = $ans * 1000;
	
	/*if( $posB == Null && $posM == Null )
		return $money;	
	else if($posB != Null && $posM == Null)
	{
		$
	}*/
			
	return $ans;
	
}
function paybacktime($MarketCap,$Income,$Growth)
{
	$m =0;
	$year = 0;
	
	if($Growth <= 0 || $Income <= 0)
	{
		//echo $Growth;
		return 'N/A';
	}
		
	while($m < $MarketCap)
	{
		$Income = $Income *(1+$Growth);
		$m = $m + $Income ;
		$year++;
		//echo $year.'-'.$Growth."\n";
	}
	return $year;
}
function UpdateROEROIC($SYMBOL,$link)//update the newest ROE ROIC data , this function will be called after catching a new finicial data.(ROE ROIC only need to be computed once).
{
	$query = "SELECT * FROM `newest_date` WHERE `SYMBOL` = '$SYMBOL' ;"; 
	$result = mysql_query($query,$link);
	
	if($result == false)
		return false;
	
	$row = mysql_fetch_array($result);
	$year  = $row['ANNUAL'];
	
	$sql = "SELECT `".$COLUMN_ID['ROE']."` , `".$COLUMN_ID['ROC']."` FROM `".$year."` WHERE `SYMBOL` = '$SYMBOL';";
	$this_year_data = mysql_query($sql);
	
	$this_year_row = mysql_fetch_array($this_year_data );
	
	$one_year_row   =  mysql_fetch_array( mysql_query( "SELECT `".$COLUMN_ID['ROE']."` , `".$COLUMN_ID['ROC']."` FROM `".($year-1)."` WHERE SYMBOL = '$SYMBOL';"));
	$two_year_row   =  mysql_fetch_array( mysql_query( "SELECT `".$COLUMN_ID['ROE']."` , `".$COLUMN_ID['ROC']."` FROM `".($year-2)."` WHERE SYMBOL = '$SYMBOL';"));
	$three_year_row =  mysql_fetch_array( mysql_query( "SELECT `".$COLUMN_ID['ROE']."` , `".$COLUMN_ID['ROC']."` FROM `".($year-3)."` WHERE SYMBOL = '$SYMBOL';"));
	$four_year_row  =  mysql_fetch_array( mysql_query( "SELECT `".$COLUMN_ID['ROE']."` , `".$COLUMN_ID['ROC']."` FROM `".($year-4)."` WHERE SYMBOL = '$SYMBOL';"));
	
	$ROE_mean_3_years = ($this_year_row[$COLUMN_ID['ROE']] + $one_year_row[$COLUMN_ID['ROE']] + $two_year_row[$COLUMN_ID['ROE']])/3;
	$ROE_mean_5_years = ($this_year_row[$COLUMN_ID['ROE']] + $one_year_row[$COLUMN_ID['ROE']] + $two_year_row[$COLUMN_ID['ROE']]+ $three_year_row[$COLUMN_ID['ROE']]+ $four_year_row[$COLUMN_ID['ROE']])/5;
	$ROC_mean_3_years = ($this_year_row[$COLUMN_ID['ROC']] + $one_year_row[$COLUMN_ID['ROC']] + $two_year_row[$COLUMN_ID['ROC']])/3;
	$ROC_mean_5_years = ($this_year_row[$COLUMN_ID['ROC']] + $one_year_row[$COLUMN_ID['ROC']] + $two_year_row[$COLUMN_ID['ROC']]+ $three_year_row[$COLUMN_ID['ROC']]+ $four_year_row[$COLUMN_ID['ROC']])/5;
	
	$sql = "UPDATE `$year` SET `".$COLUMN_ID['ROE 3 year']."` = '$ROE_mean_3_years' , `".$COLUMN_ID['ROE 5 year']."` = '$ROE_mean_5_years' , `".$COLUMN_ID['ROC 3 year']."` = '$ROC_mean_3_years' , `".$COLUMN_ID['ROC 5 year']."` = '$ROC_mean_5_years' WHERE `SYMBOL` = '$this_year_row[SYMBOL]';";
	mysql_query($sql);

	return true;
	
}
?>
