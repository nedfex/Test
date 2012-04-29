<?php
// �ѪRMoring star ���Ѱ]��CSV�� �P�O���W�٨æ۰ʶפJ��Ʈw
// �~���i�H���Φb�N , CSV�ɦ�����fiscal end year ��� �i�H�ۤv�P�O�n��J����ƪ�
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
$TYPE ="IncomeStatement";

$dbname = "finance_ms"; 
$sql = "SELECT * FROM `10`;";
$result = mysql_query($sql);
$col_name = record_col($result);
mysql_free_result($result);

for($y=2000;$y<=2012;$y++)
	createKeyRatioTable( '10' , $y ,$link ,$dbname);//�إ߸�ƪ�

$target_country_symbol = "US";
$sql="SELECT * from `companyb3` WHERE `COUNTRY_SYMBOL` = '$target_country_symbol';";
$result = mysql_query($sql,$link);
$num_US_company = mysql_num_rows($result);

//$d = dir("C:/xampp/htdocs/Finance/webpages/datas/Annual/BalanceSheet/");
/*********************ANNUAL PARSE********************************/
// keyratio always contains 108 lines;
// 4~16 Financials
// 21~28 %of revenue (20 is 100%)
// 31~37 %
// 42~45 revenue growth
// 47~50 operating income growth
// 52~55 Net income growth
// 57~60 EPS growth
// 64~68 cash flow ratios
// 72~91 72+~79 = 80(100) ,81+~90 = 91(100%) , (80 91 are all 100%)
// 94~97 Liquidity/Financial Health
// 101~108 Key Ratios -> Efficiency Ratios

// ignore 20, 80, 91
// terms must be reinserted into database : 
// Revenue, Operating Income , Net Income , Earnings Per Share,operating cash flow,cap spending(Capital expenditure),
// Free Cash Flow, Working Capital
$base = "C:/xampp/htdocs/Finance/webpages/datas/10year/";

//for($i=0;$i < $num_US_company;$i++)
for($i=0;$i < $num_US_company;$i++)
{
	$row = mysql_fetch_array($result);
	if(file_exists($base.$row['SYMBOL']."_2012.csv"))
	{
		$reader = PHPExcel_IOFactory::createReader('CSV'); // Ū���ª� excel �ɮ� 
		$PHPExcel = $reader->load( $base.$row['SYMBOL']."_2012.csv"); // �ɮצW�� 
		$sheet = $PHPExcel->getSheet(0); // Ū��Ĥ@�Ӥu�@��(�s���q 0 �}�l) 
		$highestRow = $sheet->getHighestRow(); // ��o�`�C�� 
		$highestCol = ord($sheet->getHighestColumn())-ord('@');		
		$companyName = $sheet->getCellByColumnAndRow(0, 1)->getValue();
		
		for($c = 1 ; $c < $highestCol ; $c++ )
		{
			//�ѪR year
			//echo $sheet->getCellByColumnAndRow($c, 3)->getValue();
			$year = explode("-",$sheet->getCellByColumnAndRow($c, 3)->getValue());
			//echo "year : $year[0]\n";

			if( $year[0] < 2000|| $year[0] == 'TTM' )
				continue;					
				
			$sql = "INSERT INTO `$year[0]"."_keyratio` (`TICKER`) VALUES( '$row[TICKER]');";
			mysql_query($sql);
			
			$sql = "UPDATE `finance_ms`.`$year[0]"."_keyratio` SET "; 
															
			for( $r = 3; $r <= 16; $r++ ) 
    		{
	    		$val = process_report_column_name($sheet-> getCellByColumnAndRow(0, $r)->getValue()); 
	    		if( !is_numeric(str_replace(',','',$sheet-> getCellByColumnAndRow($c, $r)->getValue())) )
	    			continue;
	    		$money = $val[2] * str_replace(',','',$sheet-> getCellByColumnAndRow($c, $r)->getValue()); 
	    		 
	    		$table_col_name = return_colname($val[0],$col_name);
	    		//echo $val[0],'-'.$table_col_name.'-'.$money."\n";
	    		if( $table_col_name && is_numeric($money))
	    		{
	    			//echo $val ." ".$money." FOUND\n";
	    			$sql .= "`".$table_col_name."` = ".$money.",";   			
	    		}
			} //end Financials
			
			for($r = 21; $r <= 28; $r++) 
    		{
	    		$val = process_report_column_name($sheet-> getCellByColumnAndRow(0, $r)->getValue()); 
	    		if( !is_numeric(str_replace(',','',$sheet-> getCellByColumnAndRow($c, $r)->getValue())) )
	    			continue;
	    		$money = $val[2] * str_replace(',','',$sheet-> getCellByColumnAndRow($c, $r)->getValue()); 
	    		 
	    		$table_col_name = return_colname($val[0],$col_name);
	    		//echo $val[0],'-'.$table_col_name.'-'.$money."\n";
	    		if( $table_col_name && is_numeric($money))
	    		{
	    			//echo $val ." ".$money." FOUND\n";
	    			$sql .= "`".$table_col_name."` = ".$money.",";   			
	    		}
			} //end Key Ratios -> Profitability
			
			for($r = 31; $r <= 37; $r++) 
    		{
	    		$val = process_report_column_name($sheet-> getCellByColumnAndRow(0, $r)->getValue());
	    		if( !is_numeric(str_replace(',','',$sheet-> getCellByColumnAndRow($c, $r)->getValue())) )
	    			continue; 
	    		$money = $val[2] * str_replace(',','',$sheet-> getCellByColumnAndRow($c, $r)->getValue()); 
	    		 
	    		$table_col_name = return_colname($val[0],$col_name);
	    		//echo $val[0],'-'.$table_col_name.'-'.$money."\n";
	    		if( $table_col_name && is_numeric($money))
	    		{
	    			//echo $val ." ".$money." FOUND\n";
	    			$sql .= "`".$table_col_name."` = ".$money.",";   			
	    		}
			} //end Profitability
			
			for($r = 42; $r <= 45; $r++) 
    		{
	    		$val = process_report_column_name($sheet-> getCellByColumnAndRow(0, $r)->getValue()); 
	    		
	    		if( !is_numeric(str_replace(',','',$sheet-> getCellByColumnAndRow($c, $r)->getValue())) )
	    			continue;
	    		$money = $val[2] * str_replace(',','',$sheet-> getCellByColumnAndRow($c, $r)->getValue()); 
	    		
	    		 
	    		$table_col_name = return_colname('Revenue '.$val[0],$col_name );
	    		//echo $val[0],'-'.$table_col_name.'-'.$money."\n";
	    		if( $table_col_name && is_numeric($money))
	    		{
	    			//echo $val ." ".$money." FOUND\n";
	    			$sql .= "`".$table_col_name."` = ".$money.",";   			
	    		}
			} //revenus growth
			
			for($r = 47; $r <= 50; $r++) 
    		{
	    		$val = process_report_column_name($sheet-> getCellByColumnAndRow(0, $r)->getValue()); 
	    		
	    		if( !is_numeric(str_replace(',','',$sheet-> getCellByColumnAndRow($c, $r)->getValue())) )
	    			continue;
	    		$money = $val[2] * str_replace(',','',$sheet-> getCellByColumnAndRow($c, $r)->getValue()); 
	    		 
	    		$table_col_name = return_colname('Operating Income '.$val[0],$col_name );
	    		//echo $val[0],'-'.$table_col_name.'-'.$money."\n";
	    		if( $table_col_name && is_numeric($money))
	    		{
	    			//echo $val ." ".$money." FOUND\n";
	    			$sql .= "`".$table_col_name."` = ".$money.",";   			
	    		}
			} //Operating Income growth
			
			for($r = 52; $r <= 55; $r++) 
    		{
	    		$val = process_report_column_name($sheet-> getCellByColumnAndRow(0, $r)->getValue()); 
	    		if( !is_numeric(str_replace(',','',$sheet-> getCellByColumnAndRow($c, $r)->getValue())) )
	    			continue;    		
	    		$money = $val[2] * str_replace(',','',$sheet-> getCellByColumnAndRow($c, $r)->getValue()); 
	    		 
	    		$table_col_name = return_colname('Net Income '.$val[0],$col_name );
	    		//echo $val[0],'-'.$table_col_name.'-'.$money."\n";
	    		if( $table_col_name && is_numeric($money))
	    		{
	    			//echo $val ." ".$money." FOUND\n";
	    			$sql .= "`".$table_col_name."` = ".$money.",";   			
	    		}
			} //Net Income growth
			
			for($r = 57; $r <= 60; $r++) 
    		{
	    		$val = process_report_column_name($sheet-> getCellByColumnAndRow(0, $r)->getValue()); 
	    		if( !is_numeric(str_replace(',','',$sheet-> getCellByColumnAndRow($c, $r)->getValue())) )
	    			continue;
	    		$money = $val[2] * str_replace(',','',$sheet-> getCellByColumnAndRow($c, $r)->getValue()); 
	    		 
	    		$table_col_name = return_colname('EPS '.$val[0],$col_name );
	    		//echo $val[0],'-'.$table_col_name.'-'.$money."\n";
	    		if( $table_col_name && is_numeric($money))
	    		{
	    			//echo $val ." ".$money." FOUND\n";
	    			$sql .= "`".$table_col_name."` = ".$money.",";   			
	    		}
			} //EPS growth
			
			for($r = 63; $r <= 108; $r++) 
    		{
	    		//if($r ==80 ||$r==91)
	    			//continue;
	    		$val = process_report_column_name($sheet-> getCellByColumnAndRow(0, $r)->getValue());
	    		if( !is_numeric(str_replace(',','',$sheet-> getCellByColumnAndRow($c, $r)->getValue())) )
	    			continue; 
	    		$money = $val[2] * str_replace(',','',$sheet-> getCellByColumnAndRow($c, $r)->getValue()); 
	    		 
	    		$table_col_name = return_colname($val[0],$col_name );
	    		//echo $val[0],'-'.$table_col_name.'-'.$money."\n";
	    		if( $table_col_name && is_numeric($money))
	    		{
	    			//echo $val ." ".$money." FOUND\n";
	    			$sql .= "`".$table_col_name."` = ".$money.",";   			
	    		}
			} //OTHER
											
			//$sql[strlen($sql)-1] = " ";//�h���̫�@�� ','
			//return;
		  	$sql .= "`Fiscal year end` = '$year[0]"."-"."$year[1]' WHERE `TICKER` = '$row[TICKER]' ;";
			  //echo $sql;
		  	if( mysql_query($sql) == false)
		  	{
		  		echo "ERROR\n$sql\n";
		  		return;
		  	}


		}
		echo "$row[TICKER] processed...\n";	
	}
}
mysql_free_result($result);


function createKeyRatioTable( $type , $year ,$link ,$dbname)
{
	$sql = "SELECT * FROM `$type`;";
	$result = mysql_query($sql,$link);
	$sql = 	"CREATE TABLE `$dbname`.`$year"."_"."keyratio` (`TICKER` VARCHAR( 50 ) NOT NULL ,";
	
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
		//echo $col_name[$i]."\n";
	}
	return $col_name;
}
function return_colname($string,$colname)
{

	for($i=0;$i< count($colname);$i++)
			if(strcmp($string,$colname[$i])==0)
				return $colname[$i];
			
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
		if(strpos(strtolower($string),$MONTH[$i]))
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
function process_report_column_name($str)
{
	
	$rtn = strstr($str, ' Mil', true);
	if ($rtn){
		//$rtn_array[2] = 'Mil';
		$rtn_array[2] = '1000000';
		$str = $rtn;
	}else{
		$rtn_array[2] = 1;
	}
	
	$str_array = explode(' CU$$$$$',$str);
	$rtn = strstr($str_array[0], ' %', true);
	if ($rtn){
		//$str_array[0] = $rtn;
		$str_array[0] = str_replace(' %','',$str_array[0]);
	}
	$rtn_array[0] = $str_array[0];
	if(count($str_array)>1)
		$rtn_array[1] = $str_array[1];
	else
		$rtn_array[1]='';
	ksort($rtn_array);
	return $rtn_array;
}
?>