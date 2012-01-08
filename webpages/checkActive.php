<?php

function checkActive($SYMBOL)
{
	include("config/config.php");
	$link = ConnectDB($SQL);
	$selectresult = mysql_select_db("finance",$link);
	$query = "SELECT FROM `newest_date` WHERE `SYMBOL` = '$SYMBOL' ;"; 
	$result = mysql_query($query);
	mysql_close($link);
	
	if( $result == False || mysql_num_rows($result) == 0)
		return False;
	else 
		return True;
	
}
?>