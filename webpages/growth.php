<?php

function growth($SYMBOL	,$link)
{
	include("/config/config.php");
	//include("utility.php");
	//$link = ConnectDB($SQL);
	$selectresult = mysql_select_db("finance",$link);
	
	$today = getdate();
	$query = "SELECT * FROM `newest_date` WHERE `SYMBOL` = '$SYMBOL';";
	$result = mysql_fetch_array(mysql_query($query ));
	$today['year'] = $result['ANNUAL'];  
	
	$YD = array(1,3,5,9);
	
	$query = "SELECT * FROM `$today[year]` WHERE `SYMBOL` = '$SYMBOL';" ;
	$this_year_data= mysql_fetch_array(mysql_query($query));
	
	mysql_query("INSERT INTO `growth` (`SYMBOL`) VALUES( '$SYMBOL' );");
	//$insert = "UPDATE `finance`.`growth` SET ";
	
	for($i=0;$i < count($YD) ; $i++)
	{
		$T = $today['year']-$YD[$i];
		$rows = mysql_query("SELECT * FROM `$T` WHERE `SYMBOL` = '$SYMBOL';");
		echo "SELECT * FROM `$T` WHERE `SYMBOL` = '$SYMBOL';</br>";
		
		if( mysql_num_rows($rows)==0 )
			continue;
 
		$result = mysql_fetch_array($rows); 

		$growth_data["BVPS ".$YD[$i]." year"] = growth_rate($result[$COLUMN_ID['Book Value/Share']] , $this_year_data[$COLUMN_ID['Book Value/Share']] , $YD[$i] );
		$growth_data["Revenue ".$YD[$i]." year"] = growth_rate($result[$COLUMN_ID['Total Revenue']] , $this_year_data[$COLUMN_ID['Total Revenue']] , $YD[$i] );
		$growth_data["Gross Profit ".$YD[$i]." year"] = growth_rate($result[$COLUMN_ID['Gross Profit']] , $this_year_data[$COLUMN_ID['Gross Profit']] , $YD[$i] );
		$growth_data["Operating Income ".$YD[$i]." year"] = growth_rate($result[$COLUMN_ID['Operating Income']] , $this_year_data[$COLUMN_ID['Operating Income']] , $YD[$i] );
		$growth_data["Income After Tax ".$YD[$i]." year"] = growth_rate($result[$COLUMN_ID['Income After Tax']] , $this_year_data[$COLUMN_ID['Income After Tax']] , $YD[$i] );
		$growth_data["EPS ".$YD[$i]." year"] = growth_rate($result[$COLUMN_ID['EPS']] , $this_year_data[$COLUMN_ID['EPS']] , $YD[$i] );
		$growth_data["Retained Earnings ".$YD[$i]." year"] = growth_rate($result[$COLUMN_ID['Retained Earnings (Accumulated Deficit)']] , $this_year_data[$COLUMN_ID['Retained Earnings (Accumulated Deficit)']] , $YD[$i] );	
	}
	
	if( isset($growth_data) == Null )
		return;
	
	$keys = array_keys($growth_data);
	$insert = "UPDATE `growth` SET ";
	
	for($i=0;$i < count($keys);$i++)
	{
		//if($growth_data[$keys[$i]]!=Null)
		$insert .= "`".$GROWTH_ID[$keys[$i]]."` = '".$growth_data[$keys[$i]]."',";
	}
	$insert[strlen($insert)-1] = " ";//去掉最後一個 ','
	$insert.=" WHERE `SYMBOL` = '$SYMBOL' ;";
	
  echo $insert."</br>";
 	mysql_query($insert);   
}
?>