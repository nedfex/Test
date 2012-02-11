<?php

function calculate($SYMBOL,$link)
{
	include("/config/config.php");
	//include("utility.php");
	//$link = ConnectDB($SQL);
	$selectresult=mysql_select_db("finance",$link);
	
	$today = getdate();
	$query = "SELECT * FROM `newest_date` WHERE `SYMBOL` = '$SYMBOL';";
	$result = mysql_fetch_array(mysql_query($query ));
	$today['year'] = $result['ANNUAL'];  
	
	for($Y = $today['year'];$Y>=$today['year']-9;$Y--)
	{
		$query = "SELECT * FROM `$Y` WHERE `SYMBOL` = '$SYMBOL';";
		////echo $query."</br>";
		$result = mysql_query($query);
		$row = mysql_fetch_array($result);
		
		$OtherFeatures['Gross Margin']             = divide($row[ $COLUMN_ID['Gross Profit'] ] , $row[ $COLUMN_ID['Total Revenue'] ],NULL,NULL);		$OtherFeatures['S-Expense/Gross Profit']   = divide($row[ $COLUMN_ID['Selling/General/Administrative Expenses, Total'] ] , $row[ $COLUMN_ID['Gross Profit'] ],NULL,NULL);
		$OtherFeatures['R&D-Exp/Gross Profit low'] = divide($row[ $COLUMN_ID['Research & Development'] ] , $row[ $COLUMN_ID['Gross Profit'] ],NULL,NULL);
		$OtherFeatures['Depre./Gross Profit']      = divide($row[ $COLUMN_ID['Depreciation/Amortization'] ] , $row[ $COLUMN_ID['Gross Profit'] ],NULL,NULL);
		$OtherFeatures['Interset/Gross Profit']    = divide($row[ $COLUMN_ID['Interest Expense (Income), Net Operating'] ] , $row[ $COLUMN_ID['Gross Profit'] ],NULL,NULL);
		$OtherFeatures['Income/Gross Profit']      = divide($row[ $COLUMN_ID['Income After Tax'] ] , $row[ $COLUMN_ID['Gross Profit'] ],NULL,NULL);
		$OtherFeatures['Receivables/Sales low']    = divide($row[ $COLUMN_ID['Total Receivables, Net'] ] , $row[ $COLUMN_ID['Total Revenue'] ],NULL,NULL);
		$OtherFeatures['Cost/Inventory High']      = divide($row[ $COLUMN_ID['Cost of Revenue, Total'] ] , $row[ $COLUMN_ID['Total Inventory'] ],NULL,NULL);
		$OtherFeatures['Equipment/Asset low']      = divide($row[ $COLUMN_ID['Property/Plant/Equipment, Total - Net'] ] , $row[ $COLUMN_ID['Total Assets'] ],NULL,NULL);
		$OtherFeatures['Debt Redeem yr']           = divide($row[ $COLUMN_ID['Total Long Term Debt'] ] , $row[ $COLUMN_ID['Income After Tax'] ],NULL,NULL);;	
		$OtherFeatures['Debt/Equity']              = divide($row[ $COLUMN_ID['Total Long Term Debt'] ] , $row[ $COLUMN_ID['Total Equity'] ],NULL,NULL);
		$OtherFeatures['C-E/Net Income']           = divide($row[ $COLUMN_ID['Capital Expenditures'] ] , $row[ $COLUMN_ID['Income After Tax'] ],NULL,NULL);
		
		$query2 = "UPDATE `finance`.`$Y` SET "; 
		$query2.="`".$COLUMN_ID['Gross Margin']."` = ".$OtherFeatures['Gross Margin'].",";
		$query2.="`".$COLUMN_ID['S-Expense/Gross Profit']."` = ".$OtherFeatures['S-Expense/Gross Profit'].",";
		$query2.="`".$COLUMN_ID['R&D-Exp/Gross Profit low']."` = ".$OtherFeatures['R&D-Exp/Gross Profit low'].",";
		$query2.="`".$COLUMN_ID['Depre./Gross Profit']."` = ".$OtherFeatures['Depre./Gross Profit']."," ;
		$query2.="`".$COLUMN_ID['Interset/Gross Profit']."` = ".$OtherFeatures['Interset/Gross Profit']."," ;
		$query2.="`".$COLUMN_ID['Income/Gross Profit']."` = ".$OtherFeatures['Income/Gross Profit']." ,";
		$query2.="`".$COLUMN_ID['Receivables/Sales low']."` = ".$OtherFeatures['Receivables/Sales low']." ,";
		$query2.="`".$COLUMN_ID['Cost/Inventory High']."`=  ".$OtherFeatures['Cost/Inventory High'].",";
		$query2.="`".$COLUMN_ID['Equipment/Asset low']."`=  ".$OtherFeatures['Equipment/Asset low'].",";
		$query2.="`".$COLUMN_ID['Debt Redeem yr']."` = ".$OtherFeatures['Debt Redeem yr']." ,";
		$query2.="`".$COLUMN_ID['Debt/Equity']."` =  ".$OtherFeatures['Debt/Equity']." ,";
		$query2.="`".$COLUMN_ID['C-E/Net Income']."` =  ".$OtherFeatures['C-E/Net Income']." ";
		$query2.="WHERE `$Y`.`SYMBOL` = '$SYMBOL' ;"; 
		//echo $query2."</br>";  
		mysql_query($query2);

		for($j=1;$j<=4;$j++)
		{
			$QY = "$Y"."_Q"."$j"; 
			$query = "SELECT * FROM `$QY` WHERE `SYMBOL` = '$SYMBOL';";
			$result = mysql_query($query);
	
			if( table_exist($QY)== False)
				continue;
					
			$row = mysql_fetch_array($result);
			
			$OtherFeatures['Gross Margin']             = divide($row[ $COLUMN_ID['Gross Profit'] ] , $row[ $COLUMN_ID['Total Revenue'] ],NULL,NULL);		$OtherFeatures['S-Expense/Gross Profit']   = divide($row[ $COLUMN_ID['Selling/General/Administrative Expenses, Total'] ] , $row[ $COLUMN_ID['Gross Profit'] ],NULL,NULL);
			$OtherFeatures['R&D-Exp/Gross Profit low'] = divide($row[ $COLUMN_ID['Research & Development'] ] , $row[ $COLUMN_ID['Gross Profit'] ],NULL,NULL);
			$OtherFeatures['Depre./Gross Profit']      = divide($row[ $COLUMN_ID['Depreciation/Amortization'] ] , $row[ $COLUMN_ID['Gross Profit'] ],NULL,NULL);
			$OtherFeatures['Interset/Gross Profit']    = divide($row[ $COLUMN_ID['Interest Expense (Income), Net Operating'] ] , $row[ $COLUMN_ID['Gross Profit'] ],NULL,NULL);
			$OtherFeatures['Income/Gross Profit']      = divide($row[ $COLUMN_ID['Income After Tax'] ] , $row[ $COLUMN_ID['Gross Profit'] ],NULL,NULL);
			$OtherFeatures['Receivables/Sales low']    = divide($row[ $COLUMN_ID['Total Receivables, Net'] ] , $row[ $COLUMN_ID['Total Revenue'] ],NULL,NULL);
			$OtherFeatures['Cost/Inventory High']      = divide($row[ $COLUMN_ID['Cost of Revenue, Total'] ] , $row[ $COLUMN_ID['Total Inventory'] ],NULL,NULL);
			$OtherFeatures['Equipment/Asset low']      = divide($row[ $COLUMN_ID['Property/Plant/Equipment, Total - Net'] ] , $row[ $COLUMN_ID['Total Assets'] ],NULL,NULL);
			$OtherFeatures['Debt Redeem yr']           = divide($row[ $COLUMN_ID['Total Long Term Debt'] ] , $row[ $COLUMN_ID['Income After Tax'] ],NULL,NULL);;	
			$OtherFeatures['Debt/Equity']              = divide($row[ $COLUMN_ID['Total Long Term Debt'] ] , $row[ $COLUMN_ID['Total Equity'] ],NULL,NULL);
			$OtherFeatures['C-E/Net Income']           = divide($row[ $COLUMN_ID['Capital Expenditures'] ] , $row[ $COLUMN_ID['Income After Tax'] ],NULL,NULL);
		
			$query2 = "UPDATE `finance`.`$QY` SET "; 
			$query2.="`".$COLUMN_ID['Gross Margin']."` = ".$OtherFeatures['Gross Margin'].",";
			$query2.="`".$COLUMN_ID['S-Expense/Gross Profit']."` = ".$OtherFeatures['S-Expense/Gross Profit'].",";
			$query2.="`".$COLUMN_ID['R&D-Exp/Gross Profit low']."` = ".$OtherFeatures['R&D-Exp/Gross Profit low'].",";
			$query2.="`".$COLUMN_ID['Depre./Gross Profit']."` = ".$OtherFeatures['Depre./Gross Profit']."," ;
			$query2.="`".$COLUMN_ID['Interset/Gross Profit']."` = ".$OtherFeatures['Interset/Gross Profit']."," ;
			$query2.="`".$COLUMN_ID['Income/Gross Profit']."` = ".$OtherFeatures['Income/Gross Profit']." ,";
			$query2.="`".$COLUMN_ID['Receivables/Sales low']."` = ".$OtherFeatures['Receivables/Sales low']." ,";
			$query2.="`".$COLUMN_ID['Cost/Inventory High']."`=  ".$OtherFeatures['Cost/Inventory High'].",";
			$query2.="`".$COLUMN_ID['Equipment/Asset low']."`=  ".$OtherFeatures['Equipment/Asset low'].",";
			$query2.="`".$COLUMN_ID['Debt Redeem yr']."` = ".$OtherFeatures['Debt Redeem yr']." ,";
			$query2.="`".$COLUMN_ID['Debt/Equity']."` =  ".$OtherFeatures['Debt/Equity']." ,";
			$query2.="`".$COLUMN_ID['C-E/Net Income']."` =  ".$OtherFeatures['C-E/Net Income']." ";
			$query2.="WHERE `$QY`.`SYMBOL` = '$SYMBOL' ;"; 
			//echo $query2."</br>";  
			mysql_query(	$query2);
		}
	}
	
	//mysql_close($link);
}
?>