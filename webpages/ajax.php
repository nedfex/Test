<?php
include("DisplayCompanyData.php");
include("utility.php");
include("getCurrentPrice.php");
include("../W3C_lib/LIB_http.php");
include("../W3C_lib/LIB_parse.php");
include("estimatePrice.php");
include("../W3C_lib/LIB_searchTr.php");

$service = $_GET['service'];

if ($service == 'a'){
	get_sector_list();
}else if($service == 'b'){
	$sector = $_GET['sector'];
	get_industry_list($sector);
}else if($service == 'c'){
	$sector = $_GET['sector'];
	$industry = $_GET['industry'];
	get_company_list($sector, $industry);
}else if($service == 'd'){
	$symbol = $_GET['symbol'];
	get_company_summary($symbol);
}else{
	return '{}';
}

/*
Connect to the Awesome Stock database
*/
function connect_db(){
	$link = ConnectDB($SQL);
	if (!$link) {
		die('Could not connect: ' . mysql_error());
	}
	mysql_select_db("finance");
}

/*
Get sector list and return it as JSON format
*/
function get_sector_list(){
	connect_db();
	$sql = "SELECT DISTINCT SECTOR FROM company ORDER BY SECTOR";
	$result = mysql_query($sql);
	$result_array = array();
	while ($row = mysql_fetch_array($result)) { 
		$result_array[] = $row['SECTOR'];		
	}
	$result_json = json_encode($result_array);
	echo $result_json;
}

/*
Get industry list of a particular sector and return it as JSON format
*/
function get_industry_list($sector){
	connect_db();
	$sql = html_entity_decode("SELECT DISTINCT INDUSTRY FROM company WHERE SECTOR='".$sector."' ORDER BY INDUSTRY");
	$result = mysql_query($sql);
	$result_array = array();
	while ($row = mysql_fetch_array($result)) { 
		$result_array[] = $row['INDUSTRY'];		
	}
	$result_json = json_encode($result_array);
	echo $result_json;
}

/*
Get company list of a particular industry in a particular sector and return it as JSON format
*/
function get_company_list($sector, $industry){
	connect_db();
	$sql = html_entity_decode("SELECT DISTINCT CompanyName, SYMBOL FROM company WHERE SECTOR='".$sector."' AND INDUSTRY='".$industry."' ORDER BY CompanyName");
	$result = mysql_query($sql);
	$result_array = array();
	while ($row = mysql_fetch_array($result)) { 
		$result_array[0][] = $row['CompanyName'];
		$result_array[1][] = $row['SYMBOL']; 
	}
	$result_json = json_encode($result_array);
	echo $result_json;
}

/*
Get the summary of a particular company and return it as JSON format
*/
function get_company_summary($symbol){
	DisplayCompanyData($symbol);
}

?>