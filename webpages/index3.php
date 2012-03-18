<html>
<link href="htc.css" rel="stylesheet" type="text/css">
<body>


<?php

include("..\W3C_lib\LIB_http.php");
include("..\W3C_lib\LIB_parse.php");
include("config\config.php");
include("utility.php");


set_time_limit(3600);
$link = ConnectDB($SQL);
$sector = $_GET['SECTOR'];

echo "<strong>SECTOR : $sector</strong></br>";
$sql = "SELECT DISTINCT INDUSTRY FROM company WHERE SECTOR='".$sector."' ORDER BY INDUSTRY";
$result= mysql_query($sql,$link);
for($i=0;$i < mysql_num_rows($result);$i++)
{
	$row = mysql_fetch_array($result);
	echo "<a href = \"http://140.114.213.58/Finance/webpages/showpartcompany.php?SECTOR=".urlencode($sector)."&INDUSTRY=".urlencode($row['INDUSTRY'])."\">$row[INDUSTRY]</a></br>";
}

?>
</body>
</html>
