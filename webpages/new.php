<?php
include("config\config.php");
include("utility.php");

$link = ConnectDB($SQL);
$selectresult = mysql_select_db('finance',$link);

$result = mysql_query("SELECT * FROM  `companyb`;");
echo mysql_num_rows($result);

?>