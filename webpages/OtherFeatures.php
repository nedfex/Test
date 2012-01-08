<?php
function OtherFeatures($SYMBOL)
{
	include("/config/config.php");
	$link=ConnectDB($SQL);
	$selectresult=mysql_select_db("finance",$link);
	
	$today = getdate(); 
	
	for($Y = $today['year'];$Y>=$today['year']-10;$Y--)
	{
		$query = "SELECT * FROM `$Y` WHERE `SYMBOL` = '$SYMBOL';";
		//echo $query."</br>";
		$result = mysql_query($query);
		$row = mysql_fetch_array($result);
		//print_r($row);
	}
	
	mysql_close($link);
}


?>