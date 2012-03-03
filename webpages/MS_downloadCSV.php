<?PHP
include("..\W3C_lib\LIB_http.php");
include("..\W3C_lib\LIB_parse.php");
include("..\W3C_lib\LIB_SearchTr.php");
include("..\W3C_lib\LIB_resolve_addresses.php");
include("config\config.php");
include("utility.php");
require_once '/1.7.6/Classes/PHPExcel/IOFactory.php';

$link = ConnectDB($SQL);
$selectresult = mysql_select_db('finance',$link);
set_time_limit(36000); 

$sql="SELECT * from `companyb3` WHERE `COUNTRY_SYMBOL` = 'US';";
$result = mysql_query($sql,$link);
echo mysql_num_rows($result);


//for($i=0;$i < mysql_num_rows($result);$i++)
for($i=0;$i < 10;$i++)
{
	$row = mysql_fetch_array($result);
	downloadMSCSV($row['SYMBOL']);
}


return;



function detectCompany($SYMBOL)
{
	$MS_link = "http://financials.morningstar.com/ratios/r.html?t=$SYMBOL";
	$source = file_get_contents($MS_link);
	if(strstr($source,"There is no available information in our database to display")==false)
		return true;
	else
		return false;
}
function downloadMSCSV($SYMBOL)
{
	//ANNUAL
	
	if( detectCompany($SYMBOL)==false )
	{
		echo "$SYMBOL is not available on moriningStar</br>\n";
		return;
	}	
	$IS_link = "http://financials.morningstar.com/ajax/ReportProcess4CSV.html?t=$SYMBOL&region=USA&culture=en_us&reportType=is&period=12&dataType=A&order=asc&columnYear=5&rounding=1&view=raw&productCode=COM&r".rand( 0 , 999999 )."&denominatorView=raw&number=1";
	$BS_link = "http://financials.morningstar.com/ajax/ReportProcess4CSV.html?t=$SYMBOL&region=USA&culture=en_us&reportType=bs&period=12&dataType=A&order=asc&columnYear=5&rounding=1&view=raw&productCode=COM&r=".rand( 0 , 999999 )."&denominatorView=raw&number=1";
	$CF_link = "http://financials.morningstar.com/ajax/ReportProcess4CSV.html?t=$SYMBOL&region=USA&culture=en_us&reportType=cf&period=12&dataType=A&order=asc&columnYear=5&rounding=1&view=raw&productCode=COM&r=".rand( 0 , 999999 )."&denominatorView=raw&number=1";

	$source = file_get_contents($IS_link);
	
	/*if(strstr($source,"We apologize for any inconvenience but this page is currently unavailable")!= False )
	{
		echo "$SYMBOL is not available on moriningStar</br>\n";
		return;
	}
	else if()
	$fid = fopen("datas/Annual/IncomeStatement/".$SYMBOL."_Annual_2012.csv",'w');
	fwrite($fid,$source);
	fclose($fid);*/
	
	$source = file_get_contents($BS_link);
	$fid = fopen("datas/Annual/BalanceSheet/".$SYMBOL."_Annual_2012.csv",'w');
	fwrite($fid,$source);
	fclose($fid);
	
	$source = file_get_contents($CF_link);
	$fid = fopen("datas/Annual/CashFlow/".$SYMBOL."_Annual_2012.csv",'w');
	fwrite($fid,$source);
	fclose($fid);
	
	//QUATER
	$IS_link = "http://financials.morningstar.com/ajax/ReportProcess4CSV.html?t=$SYMBOL&region=USA&culture=en_us&reportType=is&period=3&dataType=A&order=asc&columnYear=5&rounding=1&view=raw&productCode=COM&r".rand( 0 , 999999 )."&denominatorView=raw&number=1";
	$BS_link = "http://financials.morningstar.com/ajax/ReportProcess4CSV.html?t=$SYMBOL&region=USA&culture=en_us&reportType=bs&period=3&dataType=A&order=asc&columnYear=5&rounding=1&view=raw&productCode=COM&r=".rand( 0 , 999999 )."&denominatorView=raw&number=1";
	$CF_link = "http://financials.morningstar.com/ajax/ReportProcess4CSV.html?t=$SYMBOL&region=USA&culture=en_us&reportType=cf&period=3&dataType=A&order=asc&columnYear=5&rounding=1&view=raw&productCode=COM&r=".rand( 0 , 999999 )."&denominatorView=raw&number=1";

	$source = file_get_contents($IS_link);
	$fid = fopen("datas/Quater/IncomeStatement/".$SYMBOL."_Quater_2012.csv",'w');
	fwrite($fid,$source);
	fclose($fid);
	
	$source = file_get_contents($BS_link);
	$fid = fopen("datas/Quater/BalanceSheet/".$SYMBOL."_Quater_2012.csv",'w');
	fwrite($fid,$source);
	fclose($fid);
	
	$source = file_get_contents($CF_link);
	$fid = fopen("datas/Quater/CashFlow/".$SYMBOL."_Quater_2012.csv",'w');
	fwrite($fid,$source);
	fclose($fid);
	
	
  $tenYear_link = "http://financials.morningstar.com/ajax/exportKR2CSV.html?t=$SYMBOL";
  $source = file_get_contents($tenYear_link);
	$fid = fopen("datas/10year/".$SYMBOL."_2012.csv",'w');
	fwrite($fid,$source);
	fclose($fid);	
	
}

function readCSV($filename)
{
	$reader = PHPExcel_IOFactory::createReader('CSV'); // 讀取舊版 excel 檔案 
	$PHPExcel = $reader->load($filename); // 檔案名稱 
	$sheet = $PHPExcel->getSheet(0); // 讀取第一個工作表(編號從 0 開始) 
	$highestRow = $sheet->getHighestRow(); // 取得總列數 
	echo "==".$highestRow."<br />\n"; 
	// 一次讀取一列 
	for ($row = 0; $row <= $highestRow; $row++) {
	
	    for ($column = 0; $column <= 6; $column++) { 
	        $val = $sheet->getCellByColumnAndRow($column, $row)->getValue(); 
	        echo $val .' '; 
	    } 
	    echo "<br />\n";
	
	} 
}
?>