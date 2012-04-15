<?php

include("..\..\W3C_lib\LIB_http.php");
include("..\..\W3C_lib\LIB_parse.php");
include("..\..\W3C_lib\LIB_SearchTr.php");
include("..\..\W3C_lib\LIB_resolve_addresses.php");
include("..\config\config.php");
include("..\utility.php");
require_once '../1.7.6/Classes/PHPExcel/IOFactory.php';

$link = ConnectDB($SQL);
mysql_select_db("finance_ms",$link);

$query = "SELECT * FROM `2011_keyratio`;";
$result = mysql_query($query,$link);

for($i=0; $i < mysql_num_rows($result) ; $i++)
{
	$row = mysql_fetch_array($result);
	//if($row['ACTIVE'])
	{
		growth($row['TICKER']	,$link);
		echo "$row[ANNUAL] processed..\n";
	}
}
growth("CAKE:US"	,$link);
mysql_close($link);


function growth($TICKER	,$link)
{
	//include("..\config\config.php");

	$selectresult = mysql_select_db("finance_ms",$link);
	
	//$today = getdate();
	$query = "SELECT * FROM `newest_date` WHERE `TICKER` = '$TICKER';";
	$result = mysql_fetch_array(mysql_query($query ));
	$newest_year = $result['ANNUAL'];  
	
	$YD = array(1,3,5,9);
	
	//$query = "SELECT * FROM `$newest_year"."_keyratio` WHERE `TICKER` = '$TICKER';" ;
	//$this_year_data= mysql_fetch_array(mysql_query($query));
	//$query = "SELECT * FROM `$newest_year"."_bs` WHERE `TICKER` = '$TICKER';" ;
	//$row = mysql_fetch_array(mysql_query($query));
	//$this_year_data['Retained earnings'] = $row['Retained earnings'];
	
	mysql_query("INSERT INTO `growth` (`TICKER`) VALUES( '$TICKER' );");
	$insert = "UPDATE `growth` SET ";

	/*for($i=0;$i < count($YD) ; $i++)
	{
		$T = $newest_year-$YD[$i];
		$result = mysql_query("SELECT * FROM `$T"."_keyratio` WHERE `TICKER` = '$TICKER';");
		//echo "SELECT * FROM `$T"."_keyratio` WHERE `TICKER` = '$TICKER';\n";
		if( mysql_num_rows($result)>0 )
		{
			$row = mysql_fetch_array($result); 
	
			$growth_data["BVPS ".$YD[$i]." year"] = growth_rate($row['Book Value Per Share'] , $this_year_data['Book Value Per Share'] , $YD[$i] );
			$growth_data["Revenue ".$YD[$i]." year"] = growth_rate($row['Revenue'] , $this_year_data['Revenue'] , $YD[$i] );
			$growth_data["Gross Profit ".$YD[$i]." year"] = growth_rate($row['Gross Margin']/100 * $row['Revenue'] , $this_year_data['Gross Margin']/100 * $this_year_data['Revenue'] , $YD[$i] );
			$growth_data["Operating Income ".$YD[$i]." year"] = growth_rate($row['Operating Income'] , $this_year_data['Operating Income'] , $YD[$i] );
			//echo $row['Operating Income']/$this_year_data['Operating Income']."\n" ;
			//echo pow($row['Operating Income']/$this_year_data['Operating Income'],1/$YD[$i])." : ".(1/$YD[$i])."\n";
			$growth_data["Income After Tax ".$YD[$i]." year"] = growth_rate($row['Net Income'] , $this_year_data['Net Income'] , $YD[$i] );
			$growth_data["EPS ".$YD[$i]." year"] = growth_rate($row['Earnings Per Share'] , $this_year_data['Earnings Per Share'] , $YD[$i] );
		}
		else
		{
			$result = mysql_query("SELECT * FROM `$T"."_keyratio` WHERE `TICKER` = '$TICKER';");
		}
	}*/
	
	$query = "SELECT * FROM `$newest_year"."_is` WHERE `TICKER` = '$TICKER';" ;
	$this_year_data= mysql_fetch_array(mysql_query($query));
	for($i=0;$i < count($YD) ; $i++)//Income Statement
	{
		$T = $newest_year-$YD[$i];
		$result = mysql_query("SELECT * FROM `$T"."_is` WHERE `TICKER` = '$TICKER';");	
		if( mysql_num_rows($result)==0 )
			continue;
		$row = mysql_fetch_array($result); 
		$growth_data["Revenue ".$YD[$i]." year"] = growth_rate($row['Revenue'] , $this_year_data['Revenue'] , $YD[$i] );
		$growth_data["Gross Profit ".$YD[$i]." year"] = growth_rate($row['Gross profit'] , $this_year_data['Gross profit'] , $YD[$i] );	
		$growth_data["Operating Income ".$YD[$i]." year"] = growth_rate($row['Operating income'] , $this_year_data['Operating income'] , $YD[$i] );	
		$growth_data["Income After Tax ".$YD[$i]." year"] = growth_rate($row['Net income'] , $this_year_data['Net income'] , $YD[$i] );		
	}
	
	$query = "SELECT * FROM `$newest_year"."_bs` WHERE `TICKER` = '$TICKER';" ;
	$this_year_data= mysql_fetch_array(mysql_query($query));
	for($i=0;$i < count($YD) ; $i++)//for balance sheet retained earning
	{
		$T = $newest_year-$YD[$i];
		$result = mysql_query("SELECT * FROM `$T"."_bs` WHERE `TICKER` = '$TICKER';");	
		if( mysql_num_rows($result)==0 )
			continue;
		$row = mysql_fetch_array($result); 
		$growth_data["Retained Earnings ".$YD[$i]." year"] = growth_rate($row['Retained earnings'] , $this_year_data['Retained earnings'] , $YD[$i] );	
	}
	
	$query = "SELECT * FROM `$newest_year"."_cf` WHERE `TICKER` = '$TICKER';" ;
	$this_year_data= mysql_fetch_array(mysql_query($query));
	for($i=0;$i < count($YD) ; $i++)//Casf Flow
	{
		$T = $newest_year-$YD[$i];
		$result = mysql_query("SELECT * FROM `$T"."_cf` WHERE `TICKER` = '$TICKER';");	
		if( mysql_num_rows($result)==0 )
			continue;
		$row = mysql_fetch_array($result); 
		$growth_data["Operating Cash Flow ".$YD[$i]." year"] = growth_rate($row['Operating cash flow'] , $this_year_data['Operating cash flow'] , $YD[$i] );
		$growth_data["Free Cash Flow ".$YD[$i]." year"] = growth_rate($row['Free Cash Flow'] , $this_year_data['Free Cash Flow'] , $YD[$i] );	
		echo $T.$row['Free Cash Flow']."\n";
	}
	
	if( isset($growth_data) == Null )
		return;
		
	$keys = array_keys($growth_data);
	$insert = "UPDATE `growth` SET ";
	
	for($i=0;$i < count($keys);$i++)
	{
		//if($growth_data[$keys[$i]]!=Null)
		$insert .= "`".$keys[$i]."` = '".$growth_data[$keys[$i]]."',";
	}
	$insert[strlen($insert)-1] = " ";//�h���̫�@�� ','
	$insert.=" WHERE `TICKER` = '$TICKER' ;";
	
  echo $insert."</br>";
 	mysql_query($insert);   
}

?>