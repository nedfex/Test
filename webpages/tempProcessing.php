<?
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

/********* PROCESSING INSERT INTO INTRODUCTION*/
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




?>