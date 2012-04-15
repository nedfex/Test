<?php

include("..\config\config.php");
include("..\utility.php");

$link = ConnectDB($SQL);
$selectresult = mysql_select_db('finance_ms',$link);
set_time_limit(36000); 

$type = 'cf';

$dbname = "finance_ms"; 
$sql = "SELECT * FROM `$type`;";
$result = mysql_query($sql);
for($i=0 ; $i < mysql_num_rows($result) ; $i++ )
{
	$row = mysql_fetch_array($result);
	$sql = "SELECT * FROM `2010_".$type."` WHERE `$row[colname]` IS NOT NULL ;";
	echo "$row[colname] : ".mysql_num_rows(mysql_query($sql))."\n";
	$sql2 = "UPDATE `finance_ms`.`$type` SET `count` = ".mysql_num_rows(mysql_query($sql))." WHERE `colname` = '$row[colname]';";
	mysql_query($sql2); 
}

mysql_free_result($result);
mysql
