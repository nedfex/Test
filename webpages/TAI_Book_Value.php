<?php

# Initialization
include("..\W3C_lib\LIB_http.php");
include("..\W3C_lib\LIB_parse.php");
include("..\W3C_lib\LIB_SearchTr.php");

$product_array=array();
$product_count=0;

$Book_Value_target = "http://beta.moneycentral.msn.com/investor/invsub/results/compare.asp?Page=TenYearSummary&Symbol=US:ARO";

$web_page = http_get($Book_Value_target, "");

$table_array = parse_array($web_page['FILE'], "<table", "</table>");

//echo "number of table ".count($table_array)."\n";

$today = getdate(); 

for($xx=0; $xx<count($table_array); $xx++) 
{
        if(stristr( $table_array[$xx], "BOOK VALUE/ SHARE" ))
        {

                $product_row_array = parse_array($table_array[$xx], "<tr", "</tr>");
                
                $temp = parse_array($product_row_array[0], "<th", "</th>");                
                for($xxx=0;$xxx<count($temp);$xxx++)
                        if(stristr( $temp[$xxx], "BOOK VALUE/ SHARE" )) 
                                $Book_Value_ind = $xxx;
                
                for($xxx=1;$xxx<count($product_row_array);$xxx++)
                {
                        $temp = parse_array($product_row_array[$xxx], "<td", "</td>");
                        $Ann[$xxx-1] = strip_tags(trim($temp[0]));
                        $Book_Value_Share[$xxx-1] = strip_tags(trim($temp[$Book_Value_ind]));                        
                        
                        //echo $Ann[$xxx-1].' ';
                        //echo $Book_Value_Share[$xxx-1]."\n";                
                }
        }
}

?>

