<html>
<link href="htc.css" rel="stylesheet" type="text/css">
	<head>
    	<script type="text/javascript" src="https://ajax.googleapis.com/ajax/libs/jquery/1.7.1/jquery.min.js"></script>
    </head>
	<body>
		<form action="catchData.php" method="get"> 
          <input type="text" name="SYMBOL_NAME" placeholder="ARO" value=<? echo (array_key_exists('SYMBOL_NAME',$_GET))?$_GET['SYMBOL_NAME']:"ARO"; ?> >
		  <input type="submit" value="Extrat Data">
		</form>
        <script language="javascript">

		function updateOnPEChange(pe)
		{
			var future_eps = $('.future_eps_value').html();
			var new_future_price = future_eps * pe;
			var new_now_safty_price = new_future_price / 8;
			$('.future_price_value').html(new_future_price.toFixed(2));
			$('.now_safty_price_value').html(new_now_safty_price.toFixed(2));
		}
        $(document).ready(function(){
			var min_pe = 10000;
			var min_pe_radio;
			$('.pe_ratio_value').each(function(){
				if (parseFloat($(this).html()) < min_pe){
					min_pe = parseFloat($(this).html());
					min_pe_radio = $(this).prev().find('input');
				}
			});
			min_pe_radio.attr('checked', true);
			$(".pe_ratio_title").click(function(event){
				var selected_pe = $(this).parent().next('.pe_ratio_value').html();
				updateOnPEChange(selected_pe);
			});
			$('.pe_ratio_input').blur(function(){
				if ($('.pe_ratio_title.user_input').is(':checked')){
					var input_pe = $(this).val();
					updateOnPEChange(input_pe);
				}
			});
			$('.pe_ratio_input').click(function(){
				$('.pe_ratio_title.user_input').attr('checked', true);
			});
		});
        </script>
	</body>
</html>

<?php
#Initialization

include("..\W3C_lib\LIB_http.php");
include("..\W3C_lib\LIB_parse.php");
include("..\W3C_lib\LIB_SearchTr.php");
include("config\config.php");
include("TAI_Balance_Sheet.php");
include("TAI_Income_Statement.php");
include("TAI_Cash_Flow.php");
include("TAI_PE.php");
include("TAI_EPS.php");
include("checkCompany.php");
include("OtherFeatures.php");
include("calculate2.php");
include("DisplayCompanyData.php");
include("TAI_ROE.php");
include("growth.php");
include("utility.php");
include("getCurrentPrice.php");
include("estimatePrice.php");

set_time_limit(3600);

$link = ConnectDB($SQL);
$today = getdate(); 

if(array_key_exists('SYMBOL_NAME',$_GET))
{
	$row['SYMBOL'] = $_GET['SYMBOL_NAME'];
	//echo $row['SYMBOL'];
}
else
{
	$row['SYMBOL'] = "GOOG";
}

$query= "SELECT * FROM `company` WHERE `SYMBOL` =  '$row[SYMBOL]';";
$result =mysql_query($query,$link);
if( $result && mysql_num_rows($result)==0 )
{
	echo "SYMBOL <b>\" $row[SYMBOL] \"</b> is not found.</br>";
	//return;
	
	$query= "SELECT * FROM `company` WHERE `CompanyName` LIKE '%$row[SYMBOL]%';";
	$result = mysql_query($query);
	
	if(mysql_num_rows($result)==0)
	{
		echo "Company  <b>\" $row[SYMBOL] \"</b> is not found</br>";
		return;
	}
	
	for($i=0;$i<mysql_num_rows($result);$i++)
	{
		$row = mysql_fetch_array($result);
		echo "<a href = \"catchData.php?SYMBOL_NAME=$row[SYMBOL]\">Do you mean : <b>$row[CompanyName]($row[SYMBOL])</b>?</a></br>";
	}
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
echo "<table border = 2><th colspan =2 bgcolor=#c0c0c0>Other Imformation of $row[SYMBOL]</th><tr><td>";
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
function getCompanyProfile($SYMBOL)
{
		global $SQL,$URL,$today;
		$link = mysql_connect($SQL['address'],$SQL['user'],$SQL['password']);
	  $selectresult = mysql_select_db($SQL['database'],$link);

		//$web_page = http_get("http://financials.morningstar.com/company-profile/c.action?t=".$SYMBOL."&region=USA&culture=en-us", "");
		$sql = "SELECT * FROM `intro` WHERE `SYMBOL` = '$SYMBOL';";
		$result = mysql_query($sql,$link);
	
		if ( $result && mysql_num_rows($result)!= 0 )
		{
			$row =  mysql_fetch_array($result);
			$table_array  = $row['INTRODUCTION'];
		}
		
		if( mysql_num_rows($result)== 0 || strlen($table_array )==0)//如果資料庫沒有這筆 , 或是intro沒有資料 就重抓
		{
			$web_page = http_get("http://www.google.com/finance?q=".$SYMBOL , "");
			$table_array = strip_tags( trim( return_between( $web_page['FILE'], "<div class=companySummary>", "<div","EXCL")));
			//mysql_query("UPDATE `intro` SET `INTRODUCTION`='$table_array' ,`UPDATE_DATE`='$today[year]/$today[month]/$today[mday]' WHERE `SYMBOL`='$SYMBOL';");
			if($table_array!="")
				mysql_query("UPDATE `intro` SET `SYMBOL` = '$SYMBOL',`INTRODUCTION` = '$table_array',`UPDATE_DATE` = '$today[year]/$today[month]/$today[mday]' WHERE `SYMBOL` = '$SYMBOL';");
			
		}
		echo "<table width= 650 border = 2><th>Company Introduction...</th>";
		echo "<tr><td>".$table_array."</td></tr></table>";
}
?>