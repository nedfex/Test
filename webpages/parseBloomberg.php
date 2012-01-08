<?php
include("..\W3C_lib\LIB_http.php");
include("..\W3C_lib\LIB_parse.php");
include("..\W3C_lib\LIB_SearchTr.php");
include("..\W3C_lib\LIB_resolve_addresses.php");
include("config\config.php");
include("utility.php");

//$link = ConnectDB($SQL);

//$selectresult=mysql_select_db("finance",$link);
$link = ConnectDB($SQL);
$selectresult = mysql_select_db('finance',$link);
set_time_limit(36000); 

$letter = array('A','B','C','D','E','F','G','H','I','J','K','L','M','N','O','P','Q','R','S','T','U','V','W','X','Y','Z','0','1','2','3','4','5','6','7','8','9');

$fp = fopen('currency.txt', 'r');
$info = fscanf($fp, "%d %d %d");
list($alpha_start,$page_start,$company_start) = $info;
echo "START AT ".$letter[$alpha_start].", PAGE : ".$page_start.", company_start : ".$company_start."\n";
fclose($fp);

//return;

for( $h = $alpha_start ; $h < count($letter) ; $h++)
{
	$webpage = http_get("http://investing.businessweek.com/research/common/symbollookup/symbollookup.asp?letterIn=".$letter[$h],"");

	$result = return_between($webpage['FILE'],"returned ","public company results",'EXCL');
	$number_of_company = str_replace(',','',$result);
	$number_of_pages = ceil($number_of_company/180);
	
	for($i=$page_start;$i< $number_of_pages;$i++)
	{
		$firstrow = $i * 180; 
		$URL = "http://investing.businessweek.com/research/common/symbollookup/symbollookup.asp?letterIn=".$letter[$h]."&firstrow=$firstrow";
		echo $URL."\n";
		//return;
		$webpages = http_get($URL,"");
		$result = parse_array($webpages['FILE'],"<table","</table");
		//print_r($result);
		//echo count($result);
		$result = parse_array($result[1],"<tr","</tr");//table 在第二個
		//echo count($result)."</br>";
		
		$baseURL = get_base_page_address($URL);
		$error_counter = 0;
		
		for($j=$company_start;$j < count( $result );$j++)
		{
			$element = parse_array($result[$j],"<td>","</td>");
			
			$companyName = addslashes(trim(strip_tags($element[0])));
			$companyurl = addslashes(str_replace("\"","",return_between($element[0], "a href=",">","EXCL")));
			$country = addslashes(trim(strip_tags($element[1])));
			$subindustry = addslashes(trim(strip_tags($element[2])));
			/*echo $companyName.$companyurl.$country.$industry."</br>";*/
			//echo $baseURL.$companyurl."</br>";
			//echo $companyurl."\n";
			$capid = return_between( $companyurl."END" ,".asp?capId=","END" ,"EXCL");
			//echo $capid;
			
			$webpage2 = http_get($baseURL.$companyurl , "");
			
			if( strlen($webpage2['FILE'])<= 100)
			{
				echo "CONNECTION ERROR";
				$j--;
				continue;			
			}
					
			$result2 = parse_array($webpage2['FILE'],"<table summary=\"Recently viewed\"","</table");
			$temp = parse_array($webpage2['FILE'] ,"<a class=\"link_xs\" href=\"../../sectorandindustry/sectors", " SECTOR");
			$sector = addslashes(strip_tags($temp[0]."</a>"));
			$temp = parse_array($webpage2['FILE'] ,"<a class=\"link_xs\" href=\"../../sectorandindustry/industries"," INDUSTRY" );
			$industry = addslashes(strip_tags($temp[0]."</a>"));
			$temp = return_between($webpage2['FILE'] ,"<a class=\"link_sb\" href=\"","\" target" ,"EXCL");
			
			if(strlen($temp)>100)
				$temp = "";
			$company_webpage = $temp;

			$result2 = parse_array($result2[0],"<th ","</th");
			$SYMBOL = explode(":", strip_tags($result2[0]));
			$SYMBOL[0] = addslashes(trim($SYMBOL[0]));
			$SYMBOL[1] = addslashes(trim($SYMBOL[1]));
			
			/*echo $country."</br>";
			echo $companyName.$SYMBOL[0]."-".$SYMBOL[1]."</br>";
			echo $sector."</br>".$industry.$subindustry."</br>";*/
			$sector = trim($sector);
			$industry = trim($industry);
			$subindustry = trim($subindustry);
			$companyName = trim($companyName);
			
		  $sql = "INSERT INTO `companyb` (`SECTOR`,`INDUSTRY`,`SUB_INDUSTRY`,`CompanyName`,`SYMBOL`,`COUNTRY_SYMBOL`,`COUNTRY`,`WEBSITE`,`TICKER`,`CAPID`) VALUES( '$sector' ,'$industry','$subindustry','$companyName','$SYMBOL[0]','$SYMBOL[1]','$country','$company_webpage','$SYMBOL[0]:$SYMBOL[1]',$capid);";
		  mysql_query($sql);
		  echo $sql."=".mysql_affected_rows($link)."\n";
		  
		  if($SYMBOL[0]=="UNDEFINED")
		  {
		  	echo "UNDEFINED::PASSED\n";
		  	continue;
		  }
		  
		  if( mysql_affected_rows($link)==-1 )
		  {
		  	if( mysql_num_rows( mysql_query( "SELECT * FROM  `companyb` WHERE  `CAPID` = '$capid';")) ==1 )
		  	{
		  		echo "$capid exists\n";
		  		$error_counter = 0;
		  		continue;
		  	}
		  	
		  	$j--;
		  	$error_counter++;
		  	
		  	if($error_counter>10)
		  	{
		  		echo "MYSQL CONNECTION ERROR : TERMINATE\n";
		  		return;
		  	}
		  	continue;
		  }
		  else
		  {
		  	echo "SQL INSERT SUCCESS\n";
		  	$error_counter = 0;
		  }
		  
		  $fp = fopen('currency.txt', 'w');
		  fprintf($fp,"%d %d %d",$h,$i,$j);
		  fclose($fp);
	
		}
		$company_start = 1;	
	}
	$page_start = 0;
	
}	
		
?>