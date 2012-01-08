<?php
#Initialization

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

$query="SELECT * FROM company;";
$result = mysql_query($query);

$c = mysql_num_rows(mysql_query("SELECT * FROM `onmarket`;"));
for($i=0;$i < $c ;$i++)
	$row = mysql_fetch_array( $result );
	
for( ;$i <= mysql_num_rows($result) ;$i++)
{
	$row = mysql_fetch_array( $result );
	echo "$row[SYMBOL] ::: ";
	
	$loaded = mysql_query("SELECT * FROM `onmarket` WHERE `SYMBOL` = '$row[SYMBOL]';"); 
	if( mysql_num_rows($loaded) == 1)
	{
		echo "$row[SYMBOL] existed...</br>";
		continue;
	}

	if( checkCompany($row['SYMBOL'])==true )
	{
		BalanceSheet($row['SYMBOL'],$link);
		Income_Statement($row['SYMBOL']);
		Cash_Flow($row['SYMBOL']);
		PE_BOOK_VALUE($row['SYMBOL']);
		EPS_Sales_Income($row['SYMBOL']);
		calculate($row['SYMBOL']);
		ROE($row['SYMBOL']); //處裡自行計算之ROE 以及從MSN抓之一年期及五年期平均ROE
		growth($row['SYMBOL']);
		setCompanyActive($row['SYMBOL']);
		echo "$row[SECTOR]: $row[INDUSTRY] : $row[SYMBOL] 'Completed...\n</br>";
		$link=mysql_connect($SQL['address'],$SQL['user'],$SQL['password']);		$selectresult=mysql_select_db($SQL['database'],$link);		$query = "INSERT `onmarket` (`SYMBOL`,`Flag`) VALUES( '$row[SYMBOL]' , 1);"; 
		mysql_query($query);
		
		$random_delay = rand(1,60);
		//sleep($random_delay);
	}
	else
	{
		echo "$row[SYMBOL] dose not exists \n</br>";		
		$link=mysql_connect($SQL['address'],$SQL['user'],$SQL['password']);
		$selectresult=mysql_select_db($SQL['database'],$link);		
		$query = "INSERT `onmarket` (`SYMBOL`,`Flag`) VALUES( '$row[SYMBOL]' , 0);"; 			
		mysql_query($query);				
		$random_delay=rand(1,5);
		//sleep($random_delay);
	}	
	
}

?>