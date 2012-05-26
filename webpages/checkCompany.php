<?php
function checkCompany($symbol)
{
	include("config\config.php");
	
	$URL['check_company']="http://investing.money.msn.com/investments/stock-price?Symbol=";
	$web_page = http_get( $URL['check_company'].$symbol, "");
	$table_array = parse_array($web_page['FILE'], "<html", "</html>");
	
	//echo "$URL[check_company]"."$symbol</br>";
	if (strpos( $table_array[0] , "Information on <b>$symbol</b> is not available") )
	 	return false;
	else
		return true;
}
?>
<span> DATE </span>