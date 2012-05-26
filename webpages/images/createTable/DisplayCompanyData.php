<?php
//include("..\utility.php");
//
//include("..\..\W3C_lib\LIB_http.php");
//include("..\..\W3C_lib\LIB_parse.php");
//
//include("..\..\W3C_lib\LIB_searchTr.php");

function DisplayCompanyData($TICKER,$link)
{
	//include("..\..\config\config.php");
	
	//$link = ConnectDB($SQL);
	$selectresult=mysql_select_db("finance_ms",$link);

	$query = "SELECT * FROM `companyb3` WHERE `TICKER` = '$TICKER';";
	$row = mysql_fetch_array(mysql_query($query));
	$companyName = $row['CompanyName'];	
		
	$today = getdate();
	$query = "SELECT * FROM `newest_date` WHERE `TICKER` = '$TICKER';";
	
	//如果newest_date裡面沒有存在這檔股票資訊 就跳過
	$result = mysql_query($query );
	
	if( mysql_num_rows($result)==0 )
	{
		echo "$TICKER is not avalible</br>"; 
		return;
	}
	else
	{
		$r = mysql_fetch_array($result);
		if(!$r['ACTIVE'] ){
			echo "$TICKER is not avalible</br>"; 
			return;
		}
	}
	
	//$r = mysql_fetch_array($result);
	$today['year'] = $r['ANNUAL']; 	
	$today['qtr'] =  $r['QUARTER'];

	echo "<table border = 2>";
	echo "<th colspan = 1 bgcolor = #CCCCCC>".$companyName."(".$TICKER.")"; 
	echo "<td align = center bgcolor = DDDDDD><b>".strtolower($row['SECTOR'])." / ".strtolower($row['INDUSTRY'])."</td></th>";
	echo "<tr><td><table border = 2>";
	echo "<th colspan = 5>Growth Data (Untill <b>$today[year] , $today[qtr]</b>)</th>";
	echo "<tr><td></td><td align = center> 1 Year </td> <td align = center> 3 Year </td> <td align = center> 5 Year </td> <td align = center> 9 Year </td></tr>";
	
	$query = "SELECT * FROM `$today[year]"."_keyratio"."` WHERE `TICKER` = '$TICKER';";
	//echo $query;
	$result = mysql_query($query);
	$row = mysql_fetch_array($result);
	$this_year_net_income = $row['Net Income'];
	
	echo "<tr><td bgcolor = #CCCCCC><strong>ROC(ROIC)</td><td>".roundpoint2($row['Return on Invested Capital'])."%</td><td>".roundpoint2($row['Return on Invested Capital 3 year'])."%</td><td>".roundpoint2($row['Return on Invested Capital 5 year'])."%</td><td>".roundpoint2($row['Return on Invested Capital 9 year'])."%</td></tr>";
	echo "<tr><td bgcolor = #CCCCCC><strong>ROE</td><td>".roundpoint2($row['Return on Invested Capital'])."%</td><td>".roundpoint2($row['Return on Equity 3 year'])."%</td><td>".roundpoint2($row['Return on Equity 5 year'])."%</td><td>".roundpoint2($row['Return on Equity 9 year'])."%</td></tr>";
	
	//get newest debt
	//$query = "SELECT * FROM `$today[qtr]` WHERE `TICKER` = '$TICKER';";
	//$row = mysql_fetch_array(mysql_query($query));
	$debt_year = roundpoint2($row['Long-Term Debt'] / $this_year_net_income);
	//echo $row[$COLUMN_ID['Total Long Term Debt']]." ".$this_year_net_income."</br>";

	$query = "SELECT * FROM `growth` WHERE `TICKER` = '$TICKER';";
	$result = mysql_query($query);
	$row = mysql_fetch_array($result);
	
	// ROIC , ROE
	$ind = array(0,5,1,3,2,6,4);
	
	echo "<tr><td bgcolor = #CCCCCC><strong>Revenue(Sale)</td>";
	echo "<td>".changetopercent($row['Revenue 1 year'])."%</td>";
	echo "<td>".changetopercent($row['Revenue 3 year'])."%</td>";
	echo "<td>".changetopercent($row['Revenue 5 year'])."%</td>";
	echo "<td>".changetopercent($row['Revenue 9 year'])."%</td>";
	echo "</tr>";	
	
	echo "<tr><td bgcolor = #CCCCCC><strong>EPS</td>";
	echo "<td>".changetopercent($row['EPS 1 year'])."%</td>";
	echo "<td>".changetopercent($row['EPS 3 year'])."%</td>";
	echo "<td>".changetopercent($row['EPS 5 year'])."%</td>";
	echo "<td>".changetopercent($row['EPS 9 year'])."%</td>";
	echo "</tr>";
	
	echo "<tr><td bgcolor = #CCCCCC><strong>BVPS</td>";
	echo "<td>".changetopercent($row['BVPS 1 year'])."%</td>";
	echo "<td>".changetopercent($row['BVPS 3 year'])."%</td>";
	echo "<td>".changetopercent($row['BVPS 5 year'])."%</td>";
	echo "<td>".changetopercent($row['BVPS 9 year'])."%</td>";
	echo "</tr>";

	echo "<tr><td bgcolor = #CCCCCC><strong>Operating Cash Flow</td>";
	echo "<td>".changetopercent($row['Operating Cash Flow 1 year'])."%</td>";
	echo "<td>".changetopercent($row['Operating Cash Flow 3 year'])."%</td>";
	echo "<td>".changetopercent($row['Operating Cash Flow 5 year'])."%</td>";
	echo "<td>".changetopercent($row['Operating Cash Flow 9 year'])."%</td>";
	echo "</tr>";
	
	echo "<tr><td>Operating Income</td>";
	echo "<td>".changetopercent($row['Operating Income 1 year'])."%</td>";
	echo "<td>".changetopercent($row['Operating Income 3 year'])."%</td>";
	echo "<td>".changetopercent($row['Operating Income 5 year'])."%</td>";
	echo "<td>".changetopercent($row['Operating Income 9 year'])."%</td>";
	echo "</tr>";
	
	echo "<tr><td>Free Cash Flow</td>";
	echo "<td>".changetopercent($row['Free Cash Flow 1 year'])."%</td>";
	echo "<td>".changetopercent($row['Free Cash Flow 3 year'])."%</td>";
	echo "<td>".changetopercent($row['Free Cash Flow 5 year'])."%</td>";
	echo "<td>".changetopercent($row['Free Cash Flow 9 year'])."%</td>";
	echo "</tr>";

	echo "<tr><td>Gross Profit</td>";
	echo "<td>".changetopercent($row['Gross Profit 1 year'])."%</td>";
	echo "<td>".changetopercent($row['Gross Profit 3 year'])."%</td>";
	echo "<td>".changetopercent($row['Gross Profit 5 year'])."%</td>";
	echo "<td>".changetopercent($row['Gross Profit 9 year'])."%</td>";
	echo "</tr>";
	
	echo "<tr><td>Retained Earing</td>";
	echo "<td>".changetopercent($row['Retained Earnings 1 year'])."%</td>";
	echo "<td>".changetopercent($row['Retained Earnings 3 year'])."%</td>";
	echo "<td>".changetopercent($row['Retained Earnings 5 year'])."%</td>";
	echo "<td>".changetopercent($row['Retained Earnings 9 year'])."%</td>";
	echo "</tr>";	
	
	echo "<tr><td>Income After Tax</td>";
	echo "<td>".changetopercent($row['Income After Tax 1 year'])."%</td>";
	echo "<td>".changetopercent($row['Income After Tax 3 year'])."%</td>";
	echo "<td>".changetopercent($row['Income After Tax 5 year'])."%</td>";
	echo "<td>".changetopercent($row['Income After Tax 9 year'])."%</td>";
	echo "</tr>";	
	
	$query = "SELECT * FROM `companyb3` WHERE `TICKER` = '$TICKER';";
	$row = mysql_fetch_array( mysql_query($query));
	
	echo"<tr><td>Market Cap</td>";
	if($row['MARKET_CAP']<100)
		echo "<td bgcolor =#FF0000>".$row['MARKET_CAP']." Mil</td><td></td>";
	else
		echo "<td bgcolor =#00FF00>".$row['MARKET_CAP']." Mil</td><td></td>";
		
	if ($debt_year<4)
		echo "<td>Debt Payback</td><td bgcolor = #00FF00>$debt_year years</td></tr>";
	else
		echo "<td>Debt Payback</td><td  bgcolor = #FF0000>$debt_year years</td></tr>";

	echo "</table></td>";
	
	echo "<td><table border = 2>";
	
	$CurrentData = getCurrentPE($row['TICKER'],$row['SYMBOL']);
	$CurrentData['PRICE']=getCurrentPrice($row['TICKER'],$row['SYMBOL'],false);
	//$CurrentData['PRICE']=getCurrentPrice($row['TICKER'],false);//$CurrentData['PRICE']=getCurrentPrice($row['TICKER'],true););
	$ans = estimatePrice($row['TICKER'],$row['SYMBOL'],$CurrentData );

	$payback_years = paybacktime( $row['MARKET_CAP'],$this_year_net_income,$ans['final_growth']);

	echo "<tr><td>Market Cap</td><td>$row[MARKET_CAP]</td><td>Net Income</td><td>$this_year_net_income</td><td><font color = #0000ff>Payback</font></td>";
	if($payback_years<=10 && $payback_years>6)
		echo "<td bgcolor = yellow>$payback_years  years</td></tr>";
	else if ($payback_years<=6)
		echo "<td bgcolor = #00FF00>$payback_years  years</td></tr>";
	else
		echo "<td bgcolor = #CCCCCC>$payback_years  years</td></tr>";
	
	echo "<tr><td>Now Price</td><td colspan=5><p align = center><b>$".$CurrentData['PRICE']."</b></td></tr>";
	echo "<tr><td>Rule #1 Safety Price</td><td colspan=5><p align = center>$<b class='now_safty_price_value'>".roundpoint2($ans['safety price'])."</b></td></tr>";
	if($ans['safety price'] > $CurrentData['PRICE'])	
		echo "<th colspan =6 bgcolor = 00ff00><font color = #ff0000>BUY</font></th>";
	else
		echo "<th colspan =6 bgcolor = 00AAAAAA><font color = #0000ff>WAIT</font></th>";
	
	echo "</table></td></tr></table></br>";
	//mysql_close($link);
}

?>
