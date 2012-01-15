<?php
function OtherFeatures($SYMBOL)
{
	include("/config/config.php");
	$link=mysql_connect('140.115.49.72','root','721215');
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