<?php
/*include("utility.php");
include("getCurrentPrice.php");
include("../W3C_lib/LIB_http.php");
include("../W3C_lib/LIB_parse.php");
include("estimatePrice.php");
include("../W3C_lib/LIB_searchTr.php");*/

function DisplayCompanyData($SYMBOL)
{
	include("/config/config.php");
	
	$link = ConnectDB($SQL);
	$selectresult=mysql_select_db("finance",$link);

	$query = "SELECT * FROM `company` WHERE `SYMBOL` = '$SYMBOL';";
	$row = mysql_fetch_array(mysql_query($query));
	$companyName = $row['CompanyName'];	
		
	$today = getdate();
	$query = "SELECT * FROM `newest_date` WHERE `SYMBOL` = '$SYMBOL';";
	$result = mysql_fetch_array(mysql_query($query ));
	$today['year'] = $result['ANNUAL']; 	
	$today['qtr'] =  $result['QUARTER'];

	echo "<table border = 2>";
	echo "<th colspan = 1 bgcolor = #CCCCCC>".$companyName."(".$SYMBOL.")"; 
	echo "<td align = center bgcolor = DDDDDD><b>".$row['SECTOR']." / ".$row['INDUSTRY']."</td></th>";
	echo "<tr><td><table border = 2>";
	echo "<th colspan = 5>Growth Data (Untill <b>$today[year] , $today[qtr]</b>)</th>";
	echo "<tr><td></td><td align = center> 1 Year </td> <td align = center> 3 Year </td> <td align = center> 5 Year </td> <td align = center> 9 Year </td></tr>";
	
	$query = "SELECT * FROM `$today[year]` WHERE `SYMBOL` = '$SYMBOL';";
	$result = mysql_query($query);
	$row = mysql_fetch_array($result);
	$this_year_net_income = $row[$COLUMN_ID['Income After Tax']];
	
	echo "<tr><td bgcolor = #CCCCCC>ROC(ROIC)</td><td>".roundpoint2(100*$row[$COLUMN_ID['ROC']])."%</td><td>".roundpoint2(100*$row[$COLUMN_ID['ROC 3 year']])."%</td><td>".roundpoint2(100*$row[$COLUMN_ID['ROC 5 year']])."%</td><td>NA%</td></tr>";
	echo "<tr><td bgcolor = #CCCCCC>ROE</td><td>".roundpoint2(100*$row[$COLUMN_ID['ROE']])."%</td><td>".roundpoint2(100*$row[$COLUMN_ID['ROE 3 year']])."%</td><td>".roundpoint2(100*$row[$COLUMN_ID['ROE 5 year']])."%</td><td>NA%</td></tr>";
	
	//get newest debt
	$query = "SELECT * FROM `$today[qtr]` WHERE `SYMBOL` = '$SYMBOL';";
	$row = mysql_fetch_array(mysql_query($query));
	$debt_year = roundpoint2($row[$COLUMN_ID['Total Long Term Debt']] / $this_year_net_income);
	//echo $row[$COLUMN_ID['Total Long Term Debt']]." ".$this_year_net_income."</br>";

	$query = "SELECT * FROM `growth` WHERE `SYMBOL` = '$SYMBOL';";
	$result = mysql_query($query);
	$row = mysql_fetch_array($result);
	
	// ¥ý¦L¥XROC , ROE
	$ind = array(0,5,1,3,2,6,4);
	/*for($i=1;$i<=28;$i+=4)
	{
		echo "<tr><td>".$GROWTH_ELEMENT[floor($i/4)]."</td>";
		echo "<td>".roundpoint2(check_element($row[$i]*100))."%</td>";
		echo "<td>".roundpoint2(check_element($row[$i+1]*100))."%</td>";
		echo "<td>".roundpoint2(check_element($row[$i+2]*100))."%</td>";
		echo "<td>".roundpoint2(check_element($row[$i+3]*100))."%</td>";
			
		echo "</tr>";
	}	*/
	for($i=0;$i<=3;$i++)
	{
		echo "<tr><td bgcolor = #CCCCCC>".$GROWTH_ELEMENT[$ind[$i]]."</td>";
		echo "<td>".changetopercent($row[$ind[$i]*4+1])."%</td>";
		echo "<td>".changetopercent($row[$ind[$i]*4+2])."%</td>";
		echo "<td>".changetopercent($row[$ind[$i]*4+3])."%</td>";
		echo "<td>".changetopercent($row[$ind[$i]*4+4])."%</td>";
			
		echo "</tr>";
	}	
	
	for(;$i<=6;$i++)
	{
		echo "<tr><td>".$GROWTH_ELEMENT[$ind[$i]]."</td>";
		echo "<td>".changetopercent($row[$ind[$i]*4+1])."%</td>";
		echo "<td>".changetopercent($row[$ind[$i]*4+2])."%</td>";
		echo "<td>".changetopercent($row[$ind[$i]*4+3])."%</td>";
		echo "<td>".changetopercent($row[$ind[$i]*4+4])."%</td>";
			
		echo "</tr>";
	}	

	$query = "SELECT * FROM `company` WHERE `SYMBOL` = '$SYMBOL';";
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
	
	$CurrentData = getCurrentPE($row['SYMBOL']);
	//$CurrentData['PRICE']=getCurrentPrice($row['SYMBOL'],true);
	$CurrentData['PRICE']=getCurrentPrice($row['SYMBOL'],false);//$CurrentData['PRICE']=getCurrentPrice($row['SYMBOL'],true););
	$ans = estimatePrice($row['SYMBOL'],$CurrentData );
		
	$payback_years = 	 paybacktime( $row['MARKET_CAP'],$this_year_net_income,$ans['final_growth']);
	echo "<tr><td>Market Cap</td><td>$row[MARKET_CAP]</td><td>Net Income</td><td>$this_year_net_income</td><td><font color = #0000ff>Payback</font></td>";
	if($payback_years<=10 && $payback_years>6)
		echo "<td bgcolor = yellow>$payback_years  years</td></tr>";
	else if ($payback_years<=6)
		echo "<td bgcolor = #00FF00>$payback_years  years</td></tr>";
	else
		echo "<td bgcolor = #CCCCCC>$payback_years  years</td></tr>";
	
	echo "<tr><td>Now Price</td><td colspan=5><p align = center><b>$".$CurrentData['PRICE']."</b></td></tr>";
	echo "<tr><td>Rule #1 Safety Price</td><td colspan=5><p align = center>$<b class='now_safty_price_value'>".$ans['safety price']."</b></td></tr>";
	if($ans['safety price'] > $CurrentData['PRICE'])	
		echo "<th colspan =6 bgcolor = 00ff00><font color = #ff0000>BUY</font></th>";
	else
		echo "<th colspan =6 bgcolor = 00AAAAAA><font color = #0000ff>WAIT</font></th>";
	
	echo "</table></td></tr></table></br>";
	mysql_close($link);
}

?>

