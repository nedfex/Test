<?php
// 解析Moring star 提供財報CSV檔 判別欄位名稱並自動匯入資料庫
// 年份可以不用在意 , CSV檔有提供fiscal end year 欄位 可以自己判別要塞入的資料表
include("..\..\W3C_lib\LIB_http.php");
include("..\..\W3C_lib\LIB_parse.php");
include("..\..\W3C_lib\LIB_SearchTr.php");
include("..\..\W3C_lib\LIB_resolve_addresses.php");
include("..\config\config.php");
include("..\utility.php");
require_once '../1.7.6/Classes/PHPExcel/IOFactory.php';

$MONTH = array('january','february','march','april','may','june','july','august','september', 'october' , 'november' , 'december' );

$link = ConnectDB($SQL);
$selectresult = mysql_select_db('finance_ms',$link);
set_time_limit(36000); 

$type = 'is';
switch ($type)
{
	case 'is':
		$TYPE ="IncomeStatement";
		break;
	case 'bs':
		$TYPE ="BalanceSheet";
		break;
	case 'cf':
		$TYPE ="CashFlow";
		break;
}

$dbname = "finance_ms"; 
$sql = "SELECT * FROM `$type`;";
$result = mysql_query($sql);
$col_name = record_col($result);
mysql_free_result($result);

for($y=2000;$y<=2012;$y++)
	createTable( $type , $y ,$link ,'finance_ms');//建立資料表

$target_country_symbol = "US";
$sql="SELECT * from `companyb3` WHERE `COUNTRY_SYMBOL` = '$target_country_symbol';";
$result = mysql_query($sql,$link);
$num_US_company = mysql_num_rows($result);

//$d = dir("C:/xampp/htdocs/Finance/webpages/datas/Annual/BalanceSheet/");
/*********************ANNUAL PARSE********************************/
$base_ANNUAL = "C:/xampp/htdocs/Finance/webpages/datas/Annual/$TYPE/";
$base_QUATER = "C:/xampp/htdocs/Finance/webpages/datas/Quater/$TYPE/";
for($i=0;$i < $num_US_company;$i++)
{
	$row = mysql_fetch_array($result);

	if(file_exists($base_ANNUAL.$row['SYMBOL']."_Annual_2012.csv"))
	{
		$reader = PHPExcel_IOFactory::createReader('CSV'); // 讀取舊版 excel 檔案 
		$PHPExcel = $reader->load( $base_ANNUAL.$row['SYMBOL']."_Annual_2012.csv"); // 檔案名稱 
		$sheet = $PHPExcel->getSheet(0); // 讀取第一個工作表(編號從 0 開始) 
		$highestRow = $sheet->getHighestRow(); // 取得總列數 
		$highestCol = ord($sheet->getHighestColumn())-ord('@');	
		
		$companyName = $sheet->getCellByColumnAndRow(0, 1)->getValue();
		$TICKER_in_file = return_between($companyName,'(',')','EXCL').":US";
		
		if(strcmp($row['TICKER'],$TICKER_in_file)==0)//file and TICKER must be match
		{
			for($c = 1 ; $c < $highestCol ; $c++ )
			{
				//解析 year
				$year =  explode("-",$sheet->getCellByColumnAndRow($c, 2)->getValue());
				//echo "year : $year\n";
				
				if( $year[0] < 2000 )
					break;					
					
				$sql = "INSERT INTO `$year[0]"."_$type` (`TICKER`) VALUES( '$row[TICKER]');";
				mysql_query($sql);
				
				$sql = "UPDATE `finance_ms`.`$year[0]"."_$type` SET "; 
				
				for ($r = 3; $r <= $highestRow; $r++) 
	    	{
	    		$val = $sheet-> getCellByColumnAndRow(0, $r)->getValue(); 
	    		$money = $sheet-> getCellByColumnAndRow($c, $r)->getValue(); 
	    		 
	    		$table_col_name = return_colname($val,$col_name,$type);
	    		if( $table_col_name && is_numeric($money))
	    		{
	    			//echo $val ." ".$money." FOUND\n";
	    			$sql .= "`".$table_col_name."` = ".$money.",";   			
	    		}
	
				} 
			  $sql .= "`Fiscal year end` = '$year[0]"."-"."$year[1]' WHERE `$year[0]"."_$type`.`TICKER` = '$row[TICKER]' ;";
			  //echo $sql."\n";
		  	if( mysql_query($sql) == false)
		  	{
		  		echo "ERROR\n$sql\n";
		  		return;
		  	}
			}
			echo "$row[TICKER] processed...\n";	
		}
		else
			echo "$row[TICKER]:$TICKER_in_file not match\n";		
	}
	
	/***********PARSE QUATER***************
	if(file_exists($base_QUATER.$row['SYMBOL']."_Quater_2012.csv"))
	{
		$reader = PHPExcel_IOFactory::createReader('CSV'); // 讀取舊版 excel 檔案 
		$PHPExcel = $reader->load( $base_QUATER.$row['SYMBOL']."_Quater_2012.csv"); // 檔案名稱 
		$sheet = $PHPExcel->getSheet(0); // 讀取第一個工作表(編號從 0 開始) 
		$highestRow = $sheet->getHighestRow(); // 取得總列數 
		$highestCol = ord($sheet->getHighestColumn())-ord('@');	
		
		$companyName = $sheet->getCellByColumnAndRow(0, 1)->getValue();
		$TICKER_in_file = return_between($companyName,'(',')','EXCL').":US";
		
		if(strcmp($row['TICKER'],$TICKER_in_file)==0)//file and TICKER must be match
		{
			for($c = 1 ; $c < $highestCol ; $c++ )
			{
				//解析 year
				$year =  explode("-",$sheet->getCellByColumnAndRow($c, 2)->getValue());
				//echo "year : $year\n";
				
				if( $year[0] < 2000 )
					break;					
					
				$sql = "INSERT INTO `$year[0]"."_$type` (`TICKER`) VALUES( '$row[TICKER]');";
				mysql_query($sql);
				
				$sql = "UPDATE `finance_ms`.`$year[0]"."_$type` SET "; 
				
				for ($r = 3; $r <= $highestRow; $r++) 
	    	{
	    		$val = $sheet-> getCellByColumnAndRow(0, $r)->getValue(); 
	    		$money = $sheet-> getCellByColumnAndRow($c, $r)->getValue(); 
	    		 
	    		$table_col_name = return_colname($val,$col_name,$type);
	    		if( $table_col_name && is_numeric($money))
	    		{
	    			//echo $val ." ".$money." FOUND\n";
	    			$sql .= "`".$table_col_name."` = ".$money.",";   			
	    		}
	
				} 
			  $sql .= "`Fiscal year end` = '$year[0]"."-"."$year[1]' WHERE `$year[0]"."_$type`.`TICKER` = '$row[TICKER]' ;";
		  	if( mysql_query($sql) == false)
		  	{
		  		echo "ERROR\n$sql\n";
		  		return;
		  	}
			}
			echo "$row[TICKER] processed...\n";	
		}
		else
			echo "$row[TICKER]:$TICKER_in_file not match\n";		
	}*/
}
mysql_free_result($result);


function createTable( $type , $year ,$link ,$dbname)
{
	$sql = "SELECT * FROM `$type`;";
	$result = mysql_query($sql,$link);
	$sql = 	"CREATE TABLE `$dbname`.`$year"."_"."$type` (`TICKER` VARCHAR( 50 ) NOT NULL ,";
	
	for( $i=0 ; $i < mysql_num_rows($result) ; $i++ )
	{
		$row  = mysql_fetch_array($result);
		$sql .= "`$row[colname]` FLOAT NULL ,";
	}
	$sql .= "`Fiscal year end` VARCHAR( 50 ) NULL,PRIMARY KEY ( `TICKER` ));";
	
	$fid= fopen('sql.txt','w');
	fwrite($fid,$sql);
	echo $sql."\n";
	if (mysql_query($sql))
		echo "create success\n$sql.\n;";
	else
		echo "create error\n";
		fclose($fid);
}
function record_col($result)
{
	for($i=0;$i< mysql_num_rows($result);$i++)
	{
		$row = mysql_fetch_array($result);
		$col_name[$i] = $row['colname'];
	}
	return $col_name;
}
function return_colname($string,$colname,$type)
{
	if($type == 'bs')
	{
		for($i=0;$i< count($colname);$i++)
				if(strcmp($string,$colname[$i])==0)
					return $colname[$i];
					
		if(strcmp($string,"Restricted cash and cash equivalents")==0)
			return "Cash and cash equivalents";
		elseif(strcmp($string,"Securities purchased under resale agreements")==0)
			return "Securities available for sale";
		elseif(strcmp($string,"Deferred tax liabilities")==0)
			return "Deferred taxes liabilities";
	}
	elseif($type == 'is')
	{
		for($i=0;$i< count($colname);$i++)
			if(strcmp($string,$colname[$i])==0)
				return $colname[$i];
				
		//data adjustment
		if(strcmp($string,"Advertising and promotion")==0)
			return "Advertising and marketing";
		elseif(strcmp($string,"Cumulative effect of accounting change")==0)
			return "Cumulative effect of accounting changes";
		elseif(strcmp($string,"Income before income taxes")==0)
			return "Income before taxes";
		elseif(strcmp($string,"Interest Expense")==0)
			return "Interest expenses";
		elseif(strcmp($string,"Net income from continuing ops")==0)
			return "Net income from continuing operations";
		elseif(strcmp($string,"Other expenses")==0)
			return "Other expense";
		elseif(strcmp($string,"Preferred dividends")==0)
			return "Preferred dividend";
		elseif(strcmp($string,"Provision (benefit) for income taxes")==0)
			return "Provision (benefit) for taxes";
		elseif(strcmp($string,"Provision for income taxe")==0)
			return "Provision (benefit) for taxes";
		elseif(strcmp($string,"Revenues")==0)
			return "Revenue";
		elseif(strcmp($string,"Securities")==0)
			return "Securities gains (losses)";
		elseif(strcmp($string,"Sales, General and administrative")==0)
			return "Selling, general and administrative";
		elseif(strcmp($string,"Technology and occupancy")==0)
			return "Tech, communication and equipment";	
	}
	elseif($type == 'cf')
	{
		for($i=0;$i< count($colname);$i++)
			if(strcmp($string,$colname[$i])==0)
				return $colname[$i];
				
		//data adjustment
		if(strcmp($string,"Amortization of debt discount/premium and issuance costs")==0)
			return "Amortization of debt and issuance costs";
		elseif(strcmp($string,"Deferred tax (benefit) expense")==0)
			return "Deferred tax(benefit) expense";
		elseif(strcmp($string,"Investments losses (gains)")==0)
			return "Investments (gains) losses";
		elseif(strcmp($string,"Sales/maturity of investments")==0)
			return "Sales/maturities of fixed maturity and equity securities";
		elseif(strcmp($string,"Sales/Maturities of investments")==0)
			return "Sales/maturities of fixed maturity and equity securities";
	}
	
	return false;
}
function dateAnalyizer( $string )
{
	//parse fiscal end year
}
function parseMonth($string)
{
	global $MONTH;
	for($i=0;$i < count($M);$i++)
		if( strpos(strtolower($string),$MONTH[$i]))
			return $i+1;
	return false;
}
function returnQuaterNumber($month,$fiscal_end_month)
{
	$t = $fiscal_end_month - $month;
	if($t < 0 )
		$t += 12;
		
	if($t <=2 )
		return 4;
	elseif($t >= 3 && $t <=5)
		return 3;
	else if($t >= 6 && $t <=8)
		return 2;
	else if($t >= 9 && $t <= 11)
		return 1;
				
	return false;
}
?>