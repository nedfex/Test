<?php
# Initialization
include("LIB_http.php");
include("LIB_parse.php");

$product_array=array();
$product_count=0;
echo "Clear"."\n";
# Download the target (store) web page

$web_page = array();
$target = "revenu.HTML";
#$web_page['FILE'] = readfile($target);
#$web_page = http_get($target, "");
#echo $web_page;
#<span id="yfs_l10_orcl">28.05</span>
# Parse all the tables on the web page into an array
$table_array = parse_array($web_page['FILE'], "<tr class=\"ft1\">", "</tr>");
echo "Find ".count($table_array) ."labels\n";
#for($xx=0;$xx<count($table_array);$xx++)
#{
#	echo $table_array[$xx];
#	echo "\n";
#}
# Look for the table that contains the product information

?>