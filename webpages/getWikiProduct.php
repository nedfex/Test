<?php
/*
This program crawls the Industry, Products, Parent, LOGO, Subsidiaries, Website of a company and stores them to our DB
*/
include("config\config.php");
include("utility.php");
include("..\W3C_lib\LIB_http.php");
include("..\W3C_lib\LIB_parse.php");

function getWikiCompanyData(){
	global $SQL;
	$link = ConnectDB($SQL);
	$sql = "SELECT * FROM `intro` JOIN `company` ON intro.SYMBOL = company.SYMBOL;";
	echo $sql;
	$result = mysql_query($sql,$link);

	while ($row = mysql_fetch_array($result, MYSQL_ASSOC)) {
		$company_name = $row["CompanyName"];

		$symbol = $row["SYMBOL"];
		$product = getWikiSingleCompanyData($company_name);
		
		$sql = "UPDATE `finance`.`intro` SET `PRODUCTS` = $product WHERE `intro`.`SYMBOL` = $symbol;";
		//$update_result = mysql_query($sql,$link);
		break;
	}
	
	mysql_free_result($result);
}

function getWikiSingleCompanyData($company_name){
	$company_name_for_wiki = str_replace(' ', '+', $company_name);
	$url = "http://en.wikipedia.org/w/index.php?title=Special%3ASearch&search=$company_name_for_wiki";
	print $url;
	
	$returened_array = http_get($url,"");
	$webpage_content = $returened_array['FILE'];
	
	//Case1: The serch result is a ranked result page.  In this case, get the first result
	$content = return_between($webpage_content, 'mw-search-result-heading', ' href', 'EXCL');
	$content_array = parse_array($webpage_content,'mw-search-result-heading',  'href');

	if (length($content_array) > 0){
		$content = $content_array[0];
	}
	//print $webpage_content;
	print $content;
	
	return 1;
}

getWikiCompanyData();
?>