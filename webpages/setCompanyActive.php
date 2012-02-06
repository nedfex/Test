<?php
function setCompanyActive($SYMBOL,$link)
{
	include("/config/config.php");
	//$link = ConnectDB($SQL);
	$selectresult = mysql_select_db("finance",$link);
	$query = "UPDATE `newest_date` SET `ACTIVE` = 1 WHERE `newest_date`.`SYMBOL` = '$SYMBOL' ;"; 
	mysql_query($query);
	
	$query = "INSERT `onmarket` (`SYMBOL`,`Flag`) VALUES( '$SYMBOL' , 1);"; 
	mysql_query($query);
	
	//mysql_close($link);
}



?>