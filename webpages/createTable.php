<?php

include("config/config.php");
include("utility.php");
//$link = mysql_connect('127.0.0.1','root','721215');
$link = ConnectDB($SQL);
$selectresult = mysql_select_db("finance",$link);
	
//$Y = array('2011','2010','2009','2008','2007','2006','2005','2004','2003','2002','2001');
$Y = array('2012');
//$Y = array('2006','2005','2004','2003','2002');

for( $i=0 ; $i <count($Y);$i++)
{
	createTable($Y[$i]);
	if($Y[$i] == '2011' || $Y[$i] == '2010'||$Y[$i] == '2012')
	{
		createTable($Y[$i].'_q1');
		createTable($Y[$i].'_q2');
		createTable($Y[$i].'_q3');
		createTable($Y[$i].'_q4');
	}
}
function createTable( $year )
{
  include("config/config.php");
	$query = "CREATE TABLE `$year` (";
	$query.= "`SYMBOL` VARCHAR( 50 ) NOT NULL ,";
	$query.= "`1` VARCHAR( 50 ) NULL ,";
	
	for($i=2;$i<=count($COLUMN_NAME);$i++)
		$query.= "`$i` FLOAT NULL ,";

	$query.= "PRIMARY KEY ( `SYMBOL` )";
	$query.= ") ENGINE = INNODB;";
	
	echo $query."</br>";
	//mysql_query($query);
}
?>








