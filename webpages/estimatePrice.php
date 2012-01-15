<?php

function estimatePrice($SYMBOL , $CurrentData)
{
		include("config\config.php");
		$query = "SELECT * FROM `newest_date` WHERE `SYMBOL` = '$SYMBOL';";
		$row = mysql_fetch_array(mysql_query($query));
		$ANNUAL = $row['ANNUAL'];
		
		//current EPS
		$query = "SELECT * FROM `$ANNUAL` WHERE `SYMBOL` = '$SYMBOL';";
		$row = mysql_fetch_array(mysql_query($query));
		$EPS = $CurrentData['EPS'];
		//echo "$ANNUAL = EPS = $EPS</br>";
		
		//BVPS 
		$query = "SELECT * FROM `growth` WHERE `SYMBOL` = '$SYMBOL';";
		$row = mysql_fetch_array(mysql_query($query));
		$S = 0;
		
		$counter = 0;
		for($i=1;$i<=4;$i++)//BVPS growth
		{
			if($row[$i]!=Null)
			{
				$S+=$row[$i];
				$counter++;
			}
		}
		$Avg_BVPS_Growth = $S / $counter; // ¥�w¦¨ª� 

		$FINAL_GROWTH = $Avg_BVPS_Growth;
		if($FINAL_GROWTH > $CurrentData['MSN_GROWTH'] && $CurrentData['MSN_GROWTH']!= Null)
			$FINAL_GROWTH = $CurrentData['MSN_GROWTH'];
		
						
		$default_PE = 2*100*$FINAL_GROWTH;
		$futureEPS = roundpoint2($EPS * pow(1+$FINAL_GROWTH , 10));
				
		// ¥¼¨ҐE ­pº޺growth *2  forwardPE ²{¦b PE , fundmental - key ratio 5 year summary ¤§ 5 ¦~¥­§¡(5Ŧ¦꧍

		for($i=0;$i<=4;$i++) //key ratio 5 year summary ¤§ 5 ¦~¥­§¡(5Ŧ¦꧍
		{
			$query = "SELECT `".$COLUMN_ID['Avg P/E']."` FROM `".($ANNUAL-$i)."` WHERE `SYMBOL` = '$SYMBOL';";
			$row = mysql_fetch_array(mysql_query($query));
			$AVPE[$i] = $row[$COLUMN_ID['Avg P/E']];					 
		}
		
		$FINAL_PE = min( $default_PE ,$CurrentData['PE'],$CurrentData['FORWARD_PE'],nonNullmean($AVPE) ); 
		//echo "Avg_BVPS_Growth = $Avg_BVPS_Growth ,MSN_GROWTH = $CurrentData[MSN_GROWTH] , FINAL_GROWTH = $FINAL_GROWTH , default_PE=$default_PE , CurrentPE = $CurrentData[PE], ForwardPE = $CurrentData[FORWARD_PE], ";
		//echo "5 years PE =".nonNullmean($AVPE).", Final PE = <font color = red>$FINAL_PE</font></br>";
		echo "<tr><td colspan =6><p align = center><b>Price/Earning Eistmation</b></td></tr>";
		echo "<tr><td>BVPS Growth</td><td>".roundpoint2(check_element($Avg_BVPS_Growth*100))."%</td>";
		echo "<td>MSN Growth</td><td>".roundpoint2(check_element($CurrentData['MSN_GROWTH']*100))."%</td>";
		echo "<td><font color = #0000ff>Final Growth</font></td><td><font color = #ff0000>".roundpoint2(check_element($FINAL_GROWTH*100))."%</font></td></tr>";
		echo "<form name='pe_select_form' id='pe_select_form'>";
		echo "<tr><td><input class='pe_ratio_title' type='radio' name='pe_ratio' value='default' />Default P/E</td><td class='pe_ratio_value'>".roundpoint2($default_PE)."</td>";
		echo "<td><input class='pe_ratio_title' type='radio' name='pe_ratio' value='current' />Current P/E</td><td class='pe_ratio_value'>$CurrentData[PE]</td><td>User input:</td><td><input class='pe_ratio_title user_input' type='radio' name='pe_ratio' value='user_input' /><input class='pe_ratio_input' type='text' placeholder='Type a PE ratio' /></td></tr>";
		echo "<tr><td><input class='pe_ratio_title' type='radio' name='pe_ratio' value='forward' />Forward P/E</td><td class='pe_ratio_value'>$CurrentData[FORWARD_PE]</td>";
		echo "<td><input class='pe_ratio_title' type='radio' name='pe_ratio' value='5years' />5 years P/E</td><td class='pe_ratio_value'>".roundpoint2(nonNullmean($AVPE))."</td>";
		echo "<td><font color = #0000ff>Final P/E</font></td><td><font color = #ff0000>".roundpoint2($FINAL_PE)."</font></td></tr>"; 
		echo "</form>";
		$Future_Price_Afte_Ten_Year = roundpoint2( $FINAL_PE * $futureEPS);
		$Future_Price_Afte_Five_Year = roundpoint2( $FINAL_PE * roundpoint2($EPS * pow(1+$FINAL_GROWTH , 5)));
		
		//echo "EPS = $CurrentData[EPS] , futureEPS = $futureEPS";
		//echo ", Future_Price 10 years = $Future_Price_Afte_Ten_Year</br>";
		echo "<tr><td colspan =6><p align = center><b>EPS And Price Eistimation</b></td></tr>";
		echo "<tr><td>Now EPS</td><td>$CurrentData[EPS]</td><td>Future EPS</td><td class='future_eps_value'>".roundpoint2($futureEPS)."</td>";
		echo "<td><font color = #0000ff>".($ANNUAL+10)." Price</font></td><td><font color = #ff0000 class='future_price_value'>$Future_Price_Afte_Ten_Year</font></td></tr>";
		
		for($i=0;$i<=9;$i++) //ROE 10 ¦~¥­§¡
		{
			$query = "SELECT `".$COLUMN_ID['ROE']."` FROM `".($ANNUAL-$i)."` WHERE `SYMBOL` = '$SYMBOL';";
			$row = mysql_fetch_array(mysql_query($query));
			$ROE[$i] = $row[$COLUMN_ID['ROE']];		
		}
		$AVG_ROE = nonNullmean($ROE);
		$query = "SELECT `".$COLUMN_ID['Book Value/Share']."` FROM `".($ANNUAL)."` WHERE `SYMBOL` = '$SYMBOL';";
		$row = mysql_fetch_array(mysql_query($query));
		$BVPS = $row[$COLUMN_ID['Book Value/Share']];	
		
		$ROE_price = roundpoint2($AVG_ROE * $BVPS *15);
		
		//echo "10 year price = $Future_Price_Afte_Ten_Year , Safety Price =" .($Future_Price_Afte_Ten_Year/8)."</br>";
		//echo "5  year price = $Future_Price_Afte_Five_Year , Safety Price =" .($Future_Price_Afte_Five_Year/4)."</br>";
		//echo "ROE price = $ROE_price , Safety Price =" .($ROE_price/2)."</br>";	
		
		echo "<tr><td>".($ANNUAL+10)." Price</td><td class='future_price_value'>$Future_Price_Afte_Ten_Year </td><td>Now Safety Price</td><td class='now_safty_price_value'>".roundpoint2($Future_Price_Afte_Ten_Year/8)."</td></tr>";
		//echo "<tr><td>".($ANNUAL+5)." Price</td><td>$Future_Price_Afte_Five_Year </td><td>Now Safety Price</td><td>".roundpoint2($Future_Price_Afte_Five_Year/4)."</td></tr>";
		echo "<tr><td>ROE Price</td><td>$ROE_price</td><td>Now Safety Price</td><td>".roundpoint2($ROE_price/2)."</td></tr>";
		$ans['safety price'] = $Future_Price_Afte_Ten_Year/8;
		$ans['final_growth'] = $FINAL_GROWTH;
		return $ans;
		
		//ª�񟌡rket Cap  ¥²¶·­n¤j©񞯰0 mil
		
}
?>