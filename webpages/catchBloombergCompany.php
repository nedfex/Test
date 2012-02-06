<?php

// catchData :: catch Bloomberg company, only US stocks
include("..\W3C_lib\LIB_http.php");
include("..\W3C_lib\LIB_parse.php");
include("..\W3C_lib\LIB_SearchTr.php");
include("config\config.php");
include("TAI_Balance_Sheet.php");
include("TAI_Income_Statement.php");
include("TAI_Cash_Flow.php");
include("TAI_PE.php");
include("TAI_EPS.php");
include("checkCompany.php");
include("calculate2.php");
include("DisplayCompanyData.php");
include("TAI_ROE.php");
include("growth.php");
include("utility.php");

set_time_limit(3600*10);

$DELAY=2;

$link = ConnectDB($SQL);

$query = "SELECT * FROM `companyb` WHERE `COUNTRY_SYMBOL` = 'US';";//先搞定在美國上市的股票
$result = mysql_query($query);

echo 'There are '.(mysql_num_rows($result)).' US companys\n';
//return;

//$c = mysql_num_rows(mysql_query("SELECT * FROM `onmarket`;"));
//for($i=0;$i < $c ;$i++)
//	$row = mysql_fetch_array( $result );
	
for( $i=0;$i <= 10/*mysql_num_rows($result)*/ ;$i++)
{
	$row = mysql_fetch_array( $result );
	echo "$row[SYMBOL] ::: ";
	
	$loaded = mysql_query("SELECT * FROM `onmarket` WHERE `SYMBOL` = '$row[SYMBOL]';"); 
	if( mysql_num_rows($loaded) == 1)
	{
		echo "$row[SYMBOL] existed...</br>\n";
		continue;
	}

	if( checkCompany($row['SYMBOL'])==true )
	{
		BalanceSheet($row['SYMBOL'],$link);
		Income_Statement($row['SYMBOL'],$link);
		Cash_Flow($row['SYMBOL'],$link);
		PE_BOOK_VALUE($row['SYMBOL'],$link);
		EPS_Sales_Income($row['SYMBOL'],$link);
		calculate($row['SYMBOL'],$link);
		ROE($row['SYMBOL'],$link); //處裡自行計算之ROE 以及從MSN抓之一年期及五年期平均ROE
		growth($row['SYMBOL'],$link);
		setCompanyActive($row['SYMBOL']);
		echo "$row[SECTOR]: $row[INDUSTRY] : $row[SYMBOL] 'Completed...\n</br>";
		//$link=mysql_connect($SQL['address'],$SQL['user'],$SQL['password']);		$selectresult=mysql_select_db($SQL['database'],$link);		
		//$query = "INSERT `onmarket` (`SYMBOL`,`Flag`) VALUES( '$row[SYMBOL]' , 1);"; 
		//mysql_query($query);
		
		$random_delay = rand(1,60);
		//sleep($random_delay);
	}
	else
	{
		echo "$row[SYMBOL] dose not exists \n</br>";		
		//$link=mysql_connect($SQL['address'],$SQL['user'],$SQL['password']);
		//$selectresult=mysql_select_db($SQL['database'],$link);		
		$query = "INSERT `onmarket` (`SYMBOL`,`Flag`) VALUES( '$row[SYMBOL]' , 0);"; 			
		mysql_query($query);				
		$random_delay=rand(1,5);
		//sleep($random_delay);
	}	
	
}

?>