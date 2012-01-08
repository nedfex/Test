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
	
	$result = mysql_query($sql,$link);
	$i = 0;
	while ($row = mysql_fetch_array($result, MYSQL_ASSOC)) {
		$company_name = $row["CompanyName"];
		
		$symbol = $row["SYMBOL"];
		$product = getWikiSingleCompanyData($company_name);
		
		$sql = "UPDATE `finance`.`intro` SET `PRODUCTS` = $product WHERE `intro`.`SYMBOL` = $symbol;";
		//$update_result = mysql_query($sql,$link);
		$i++;
		/*
		if ($i>10){
			break;
		}
		*/
	}
	
	mysql_free_result($result);
}

function getWikiSingleCompanyData($company_name){
	$company_name_for_wiki = str_replace(' ', '+', $company_name);
	$url = "http://en.wikipedia.org/w/index.php?title=Special%3ASearch&search=$company_name_for_wiki";
	print $url."<br>";
	
	$returened_array = http_get($url,"");
	$webpage_content = $returened_array['FILE'];
	
	//Case1: The search result is redirected to the company's wiki
	//TODO: Rewrite the following repetitive codes as one function
	$content_array = parse_array($webpage_content,'infobox vcard',  '/table');
	$content = "";
	if (count($content_array) > 0){
		$content = $content_array[0];
	}
	
	$content_array = parse_array($content,'Products',  '/tr');
	$content = "";
	if (count($content_array) > 0){
		$content = $content_array[0];
	}
	/*
	//Case2: The search result is a ranked result page.  In this case, get the first result
	
	$content_array = parse_array($webpage_content,'mw-search-result-heading',  'href');
	*/
	
	
	print $content."<br>";
	
	return 1;
}

getWikiCompanyData();

?>