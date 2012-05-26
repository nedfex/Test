<?
function getCompetitor($SYMBOL) //by CHART
{

	//$web_page = http_get("http://www.google.com/finance?q=".$SYMBOL, "");
	/*$web_page = http_get("http://ycharts.com/companies/".$SYMBOL."/performance","");
	$table_array = parse_array($web_page['FILE'], "You may also be interested in these", "</div>");//echo count($table_array);
  $row_array = parse_array($table_array[0] , "<a href","</a") ;//echo count($row_array);

	echo "<table border = 2>";
	echo "<th>Related Competitors...</th>";
	for($i=0;$i<count($row_array);$i++)
	{
		$related_SYMBOL = return_between($row_array[$i], "companies/", "\"", 'EXCL');
		$company_name = strip_tags(trim($row_array[$i]));
		echo "</tr><td><a href = \"catchData.php?SYMBOL_NAME=$related_SYMBOL\">".$company_name."</a></td></tr>";		
	}
	echo "</table>";*/

	//%fp = fopen('data.txt', 'w');fwrite($fp, $web_page['FILE']);fclose($fp);
	//echo $table_array[0];
	
	//from gurufocus.com
	$web_page = http_get("http://www.gurufocus.com/compare.php?symbol=$SYMBOL","");
	$table_array = return_between( $web_page['FILE'],"<input name=\"tickers\" type=\"text\" id=\"tickers\" value=\"", "\" size=","EXCL");//echo count($table_array);
	//echo $table_array;
	$chunk = explode(',',$table_array);
	echo "<table border = 2>";
	echo "<th>Related Competitors...</th>";
	for($i=1;$i< count($chunk);$i++)
	{
		echo "</tr><td><a href = \"catchData.php?SYMBOL_NAME=$chunk[$i]\">".$chunk[$i]."</a></td></tr>";		
	}
	echo "</table>";
	
}
?>