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

include("..\..\W3C_lib\LIB_http.php");
include("..\..\W3C_lib\LIB_parse.php");
include("..\..\W3C_lib\LIB_SearchTr.php");
include("..\config\config.php");

include("DisplayCompanyData.php");
include("estimatePrice.php");
include("getCurrentPrice.php");
include("..\utility.php");

set_time_limit(3600);

$link = ConnectDB($SQL);
$selectresult = mysql_select_db("finance_ms",$link);
$today = getdate(); 

if(array_key_exists('SYMBOL_NAME',$_GET))
{
	$row['TICKER'] = strtoupper($_GET['SYMBOL_NAME']).":US";
	//echo $row['SYMBOL'];
}
else
{
	$row['TICKER'] = "GOOG:US";
}

$query= "SELECT * FROM `company3` WHERE `TICKER` =  '$row[TICKER]:US';";
$result =mysql_query($query,$link);
if( $result && mysql_num_rows($result)==0 )
{
	echo "SYMBOL <b>\" $row[TICKER]:US \"</b> is not found.</br>";
	//return;
	
	$query= "SELECT * FROM `company` WHERE `CompanyName` LIKE '%$row[TICKER]%';";
	$result = mysql_query($query);
	
	if(mysql_num_rows($result)==0)
	{
		echo "Company  <b>\" $row[TICKER]:US \"</b> is not found</br>";
		return;
	}
	
	for($i=0;$i < mysql_num_rows($result) ; $i++)
	{
		$row = mysql_fetch_array($result);
		echo "<a href = \"catchData.php?SYMBOL_NAME=$row[TICKER]:US\">Do you mean : <b>$row[CompanyName]($row[TICKER])</b>?</a></br>";
	}
	return;
}

DisplayCompanyData($row['TICKER'],$link);

mysql_close($link);

=======
DisplayCompanyData($row['TICKER'],$link);
$row['SYMBOL'] = str_replace(':US','',$row['TICKER']);
echo "<table border = 2><th colspan =3 bgcolor=#c0c0c0>Other Imformation of $row[SYMBOL]</th><tr><td>";
//getCompetitor($row['TICKER'],$row['SYMBOL'],$link);
echo "</td><td>";
getCompanyProfile($row['TICKER'],$row['SYMBOL'],$link);
echo "</td>";
echo "<td><table border = 2><tr><td><a href = \"http://investing.money.msn.com/investments/stock-price?symbol=$row[SYMBOL]&ocid=qbeb\"  target=blank>MSN Money ($row[SYMBOL])</a></td></tr>";
echo "<tr><td><a href = \"http://finance.yahoo.com/q?s=$row[SYMBOL]&ql=1\" target=blank>Yahoo! Finance ($row[SYMBOL])</a></td></tr>";
echo "<tr><td><a href = \"http://www.google.com/finance?q=$row[SYMBOL]\" target=blank>Google Finance ($row[SYMBOL])</a></td></tr>";
echo "<tr><td><a href = \"http://investing.businessweek.com/research/stocks/snapshot/snapshot.asp?ticker=$row[SYMBOL]:US&submit=GO\" target=blank>Bloomberg ($row[SYMBOL])</a></td></tr>";
echo "<tr><td><a href = \"http://ycharts.com/companies/$row[SYMBOL]\" target=blank>YChart ($row[SYMBOL])</a></td></tr>";
echo "<tr><th bgcolor=#c0c0c0>Charts</th></tr>";
echo "<tr><td><a href = \"http://investing.money.msn.com/investments/charts/?symbol=$row[SYMBOL]#symbol=$row[SYMBOL]&event=&BB=off&CCI=off&EMA=10&FSO=off&MACD=17,8,9&MFI=off&PSAR=off&RSI=off&SMA=off&SSO=14,5&Volume=off&period=6m&linetype=Line&scale=Auto&comparelist=".'$indu,$compx,$inx'."\" target=blank><strong>(MSN)MACD+KD+EMA LINES</strong></a></td></tr>";
echo "<tr><td><a href = \"http://finance.yahoo.com/echarts?s=$row[SYMBOL]+Interactive#symbol=$row[SYMBOL];range=3m;compare=;indicator=sma(10)+stochasticslow(14,5)+macd(17,8,9);charttype=area;crosshair=on;ohlcvalues=1;logscale=off;source=undefined;\" target=blank><strong>(Yahoo)MACD+KD+EMA LINES</strong></a></td></tr>";
echo "<tr><th bgcolor=#c0c0c0>Major Holders</th></tr>";
echo "<tr><td><a href = \"http://finance.yahoo.com/q/mh?s=".$row['SYMBOL']."+Major+Holders\" target=blank><strong>Holders</strong></a></td></tr>";
echo "<tr><th bgcolor=#c0c0c0>Officers And Insider Trading</th></tr>";
echo "<tr><td><a href = \"http://www.reuters.com/finance/stocks/companyOfficers?symbol=$row[SYMBOL]\" target=blank><strong>Officers</strong></a></td></tr>";
echo "</table></tr></table>";


echo "<a href = \"../catchData.php?SYMBOL_NAME=$row[SYMBOL]\">MSN DATA PAGE</a>";
mysql_close($link);

function getCompetitor($TICKER,$SYMBOL,$link) //by CHART
{
	global $SQL,$URL,$today;
	//link = mysql_connect($SQL['address'],$SQL['user'],$SQL['password']);
	//$selectresult = mysql_select_db($SQL['database'],$link);
	//from gurufocus.com
	
	$sql = "SELECT * FROM `competitor` WHERE `TICKER` = '$TICKER'";
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
		mysql_query("DELETE FROM `competitor` WHERE `TICKER` = $TICKER");
		
		$web_page = http_get("http://www.gurufocus.com/compare.php?symbol=$SYMBOL","");
		$fid = fopen("web.txt",'w');fwrite($fid,$web_page['FILE']);fclose($fid);
		$table_array = return_between( $web_page['FILE'],"name=\"tickers\" type=\"text\" id=\"tickers\" value=\"", "\" size=","EXCL");//echo count($table_array);
		echo $table_array;
		$chunk = explode(',',$table_array);
		echo "<table border = 2>";
		echo "<th>Related Competitors...(First Catch)</th>";
		for($i=1;$i< count($chunk);$i++)
		{
			$sql = "SELECT `CompanyName` FROM `companyb3` WHERE `SYMBOL` = '$chunk[$i]';";
			echo $chunk[$i];
			$result = mysql_query($sql,$link);
			$row = mysql_fetch_array($result);
			echo "</tr><td><a href = \"catchData.php?SYMBOL_NAME=$chunk[$i]\">$row[CompanyName]($chunk[$i])</a></td></tr>";	
			$sql = "INSERT INTO `competitor` (`TICKER`,`COMPETITOR`,`CompanyName`,`UPDATE_DATE`) VALUES( '$TICKER' ,'$chunk[$i]','$row[CompanyName]','$today[year]/$today[month]/$today[mday]');";
			//mysql_query($sql);

		}
		echo "</table>";
	}
}
function getCompanyProfile($TICKER,$SYMBOL,$link)
{
		global $SQL,$URL,$today;
		//$link = mysql_connect($SQL['address'],$SQL['user'],$SQL['password']);
	  //$selectresult = mysql_select_db($SQL['database'],$link);

		//$web_page = http_get("http://financials.morningstar.com/company-profile/c.action?t=".$SYMBOL."&region=USA&culture=en-us", "");
		$sql = "SELECT * FROM `intro` WHERE `TICKER` = '$TICKER';";
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
				mysql_query("UPDATE `intro` SET `TICKER` = '$TICKER',`INTRODUCTION` = '$table_array',`UPDATE_DATE` = '$today[year]/$today[month]/$today[mday]' WHERE `TICKER` = '$TICKER';");
			
		}
		echo "<table width= 650 border = 2><th>Company Introduction...</th>";
		echo "<tr><td>".$table_array."</td></tr></table>";
}


?>