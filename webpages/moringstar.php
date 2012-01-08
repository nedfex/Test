<?
include("..\W3C_lib\LIB_http.php");
include("..\W3C_lib\LIB_parse.php");
include("..\W3C_lib\LIB_SearchTr.php");
include("config\config.php");
include("utility.php");

set_time_limit(3600);

$link = ConnectDB($SQL);
$today = getdate(); 

$query= "SELECT * FROM `company` WHERE `SYMBOL` =  '$row[SYMBOL]';";
$result =mysql_query($query,$link);
if( $result && mysql_num_rows($result)==0 )
{
	echo "SYMBOL <b>\" $row[SYMBOL] \"</b> is not found.</br>";
	return;
}

//if (checkActive($row['SYMBOL'])==False )
if (false)	
{
	echo "<font color= #ff0000>Balance Sheet</font></br>";
	BalanceSheet($row['SYMBOL'],$link);
	echo "<font color= #ff0000>Income Statement</font></br>";
	Income_Statement($row['SYMBOL']);
	echo "<font color= #ff0000>Cash Flow</font></br>";
	Cash_Flow($row['SYMBOL']);
	echo "<font color= #ff0000>PE_BOOK_VALUE</font></br>";
	PE_BOOK_VALUE($row['SYMBOL']);
	echo "<font color= #ff0000>EPS_Sales_Income</font></br>";
	
	EPS_Sales_Income($row['SYMBOL']);
	echo "<font color= #ff0000>Calculate other features</font></br>";
	calculate($row['SYMBOL']);
	echo "<font color= #ff0000>ROE</font></br>";
	ROE($row['SYMBOL']); //處裡自行計算之ROE , ROC 以及從MSN抓之一年期及五年期平均ROE
	echo "<font color= #ff0000>GROWTH</font></br>";
	growth($row['SYMBOL']);
	setCompanyActive($row['SYMBOL']);
}

DisplayCompanyData($row['SYMBOL']);
echo "<table border = 2><th colspan =2 >Other Imformation of $row[SYMBOL]</th><tr><td>";
getCompetitor($row['SYMBOL']);
echo "</td><td>";
getCompanyProfile($row['SYMBOL']);
echo "</td></tr></table>";

function getCompetitor($SYMBOL) //by CHART
{
	global $SQL,$URL,$today;
	$link = mysql_connect($SQL['address'],$SQL['user'],$SQL['password']);
	$selectresult = mysql_select_db($SQL['database'],$link);
	//from gurufocus.com
	
	$sql = "SELECT * FROM `competitor` WHERE `SYMBOL` = '$SYMBOL'";
	$result = mysql_query($sql,$link);
	
	if( $result && mysql_num_rows($result)!=0 )
	{
		echo "<table border = 2>";
	  echo "<th>Related Competitors...</th>";
		for($i=0;$i< mysql_num_rows($result);$i++)
		{
			$row = mysql_fetch_array($result);
			echo "</tr><td><a href = \"catchData.php?SYMBOL_NAME=$row[COMPETITOR]\">$row[CompanyName]($row[COMPETITOR])</a></td></tr>";	
		}
		echo "</table>";
	}
	else
	{
		mysql_query("DELETE FROM `competitor` WHERE `SYMBOL` = $SYMBOL");
		
		$web_page = http_get("http://www.gurufocus.com/compare.php?symbol=$SYMBOL","");
		$table_array = return_between( $web_page['FILE'],"<input name=\"tickers\" type=\"text\" id=\"tickers\" value=\"", "\" size=","EXCL");//echo count($table_array);
		//echo $table_array;
		$chunk = explode(',',$table_array);
		echo "<table border = 2>";
		echo "<th>Related Competitors...(First Catch)</th>";
		for($i=1;$i< count($chunk);$i++)
		{
			$sql = "SELECT `CompanyName` FROM `company` WHERE `SYMBOL` = '$chunk[$i]';";
			$result = mysql_query($sql,$link);
			$row = mysql_fetch_array($result);
			echo "</tr><td><a href = \"catchData.php?SYMBOL_NAME=$chunk[$i]\">$row[CompanyName]($chunk[$i])</a></td></tr>";	
			$sql = "INSERT INTO `competitor` (`SYMBOL`,`COMPETITOR`,`CompanyName`,`UPDATE_DATE`) VALUES( '$SYMBOL' ,'$chunk[$i]','$row[CompanyName]','$today[year]/$today[month]/$today[mday]');";
			mysql_query($sql);

		}
		echo "</table>";
	}
	
}

?>