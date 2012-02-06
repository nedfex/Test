<?php

//Market Cap 必須大於 100 mill
// 新增Market Cap Shares Outstanding
// http://investing.money.msn.com/investments/stock-price?Symbol=ORCL

include("/config/config.php");
include("..\W3C_lib\LIB_http.php");
include("..\W3C_lib\LIB_parse.php");
include("..\W3C_lib\LIB_SearchTr.php");
include("utility.php");
include("getCurrentPrice.php");
include("estimatePrice.php");
include("DisplayCompanyData.php");

set_time_limit(3600);
$link = ConnectDB($SQL);

$selectresult=mysql_select_db("finance",$link);
$GC=0;
$GL = 0.10;
$query = "SELECT * FROM `growth`;";
$R = mysql_query($query);
for($h=0; $h < mysql_num_rows($R);$h++)
{
	$flag = True;
	$row = mysql_fetch_array($R);
	
	$NullCount = 0;
	$GoodCount = 0;
	for($j = 1;$j<=26;$j++)
	{
		if($row[$j] == Null)
		{
			$NullCount++;
		}
		else if( $row["$j"] > $GL )
		{
			//$flag = False;
			//break;
			$GoodCount++;
		}
	}
	
	if( $flag==True && $NullCount < 10 && $GoodCount > 17)
	{
		//$query = "UPDAT `growth` SET `29` = 1 WHERE `SYMBOL` = '$row[SYMBOL]';";
		//mysql_query($query);
		
		/*$query = "SELECT * FROM `company` WHERE `SYMBOL` = '$row[SYMBOL]';";
		$row = mysql_fetch_array(mysql_query($query));
		$companyName = $row['CompanyName'];	
		
		$query = "SELECT * FROM `growth` WHERE `SYMBOL` = '$row[SYMBOL]';";
		$result = mysql_query($query);
		$rowg = mysql_fetch_array($result);
		
		echo "<table border = 2>";
		echo "<th colspan = 5>".$row['SECTOR'].'::'.$row['INDUSTRY'].'::'.$companyName."(".$row['SYMBOL'].")</th>";
		echo "<tr><td></td><td> one year </td> <td> 3 year </td> <td> 5 year </td> <td> 9 year </td></tr>";
		for($i=1;$i<=28;$i+=4)
		{
			echo "<tr><td>".$GROWTH_ELEMENT[floor($i/4)]."</td>";
			echo "<td>".check_element($rowg[$i]*100)."%</td>";
			echo "<td>" .check_element($rowg[$i+1]*100)."%</td>";
			echo "<td>" .check_element($rowg[$i+2]*100)."%</td>";
			echo "<td>" .check_element($rowg[$i+3]*100)."%</td>";
				
			echo "</tr>";
		}
		
		$Now_Price = getCurrentPrice($row['SYMBOL']);
		$First_Law_Savety_Price = roundpoint2(estimatePrice($row['SYMBOL']));
		echo "<tr><td>Now price</td><td>$Now_Price</td></tr>";
		echo "<tr><td>Safety Buy price below</td><td>$First_Law_Savety_Price</td>"; 
		if($First_Law_Savety_Price > $Now_Price)	
			echo "<td><font color = #ff0000>!! BUY IN !!</font></td>";
		else
			echo "<td><font color = #0000ff> ...WAIT...</font></td></tr>";
		echo "</table>";*/		
		DisplayCompanyData($row['SYMBOL']);	
		//echo "<a target = new href = \"catchData.php?SYMBOL_NAME=$row[SYMBOL]\">$row[SYMBOL]</a></br>";	
		$GC++;
	}
	
	
}
echo "<p>There are $GC good companies.(Growth > ".(100*$GL)."%)</p>";
//mysql_close($link);

?>

