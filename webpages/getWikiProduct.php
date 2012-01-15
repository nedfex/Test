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
	$sql = "SELECT * FROM `intro` JOIN `company` ON intro.SYMBOL = company.SYMBOL ORDER BY company.MARKET_CAP DESC;";
	
	$result = mysql_query($sql,$link);
	$i = 0;
	while ($row = mysql_fetch_array($result, MYSQL_ASSOC)) {
		$company_name = $row["CompanyName"];
		
		$symbol = $row["SYMBOL"];
		$product = getWikiSingleCompanyData($company_name);
		/*
		$sql = "UPDATE `finance`.`intro` SET `PRODUCTS` = $product WHERE `intro`.`SYMBOL` = $symbol;";
		//$update_result = mysql_query($sql,$link);
		*/
		$i++;
		
		if ($i>3){
			break;
		}
		
	}
	
	mysql_free_result($result);
}

function cutBetween($content, $start_str, $end_str){
	$content_array = parse_array($content, $start_str, $end_str);
	$content = "";
	if (count($content_array) > 0){
		$content = $content_array[0];
	}
	return $content;
}

function getWikiSingleCompanyData($company_name){
	$company_name_for_wiki = str_replace(' ', '+', $company_name);
	$url = "http://en.wikipedia.org/w/index.php?title=Special%3ASearch&search=$company_name_for_wiki";
	print $url."<br>";
	
	$returened_array = http_get($url,"");
	$webpage_content = $returened_array['FILE'];
	
	//Case1: The search result is redirected to the company's wiki
	//TODO: Rewrite the following repetitive codes as one function

	$infobox_content = cutBetween($webpage_content, 'infobox vcard', '/table');
	$info['traded_as'] = cutBetween($infobox_content, 'Traded as', '/tr');
	$info['product'] = cutBetween($infobox_content, 'Products', '/tr');
	$info['industry'] = cutBetween($infobox_content, 'Industry', '/tr');
	$info['area_served'] = cutBetween($infobox_content, 'Area served', '/tr');
	$info['key_people'] = cutBetween($infobox_content, 'Key people', '/tr');
	$info['type'] = cutBetween($infobox_content, 'Type', '/tr');
	$info['genre'] = cutBetween($infobox_content, 'Genre', '/tr');
	$info['employees'] = cutBetween($infobox_content, 'Employees', '/tr');
	$info['founders'] = cutBetween($infobox_content, 'Founder(s)', '/tr');
	$info['subsidiaries'] = cutBetween($infobox_content, 'Subsidiaries', '/tr');
	$info['website'] = cutBetween($infobox_content, 'Website', '/tr');
	
	/*
	//Case2: The search result is a ranked result page.  In this case, get the first result
	
	$content_array = parse_array($webpage_content,'mw-search-result-heading',  'href');
	*/
	
	
	foreach($info as $key => $value){
		print '<b>'.strtoupper($key).'</b>'.$value."<br><br>";
	}
	echo '<br>';
	//print '<b>PRODUCTS</b>'.$info['product']."<br><br>";
	//print '<b>WEBSITE</b>'.$info['website']."<br><br><br>";
	
	return 1;
}

getWikiCompanyData();

?>