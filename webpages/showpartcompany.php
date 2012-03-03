<html>
<link href="htc.css" rel="stylesheet" type="text/css">
<body>
<?php

include("/config/config.php");
include("..\W3C_lib\LIB_http.php");
include("..\W3C_lib\LIB_parse.php");
include("..\W3C_lib\LIB_SearchTr.php");
include("utility.php");
include("getCurrentPrice.php");
include("estimatePrice.php");
include("DisplayCompanyData.php");

set_time_limit(3600);
$link = ConnectDB($SQL);
$sector = $_GET['SECTOR'];
$industry = $_GET['INDUSTRY'];
$sql = "SELECT * FROM company WHERE SECTOR='".$sector."' AND( INDUSTRY='".$industry."')";
echo "<align = center><strong>SECTOR : $sector ,Industry : $industry </strong></br>";
$R = mysql_query($sql);
for($h=0; $h < mysql_num_rows($R);$h++)
{
	$row = mysql_fetch_array($R);
	DisplayCompanyData($row['SYMBOL'],$link);	
	echo "<a target = new href = \"catchData.php?SYMBOL_NAME=$row[SYMBOL]\">$row[SYMBOL]</a></br>";	
}

?>
</body></html>

