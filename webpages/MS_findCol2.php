<?php
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

$d = dir("C:/xampp/htdocs/Finance/webpages/datas/Annual/IncomeStatement/");
$base = "C:/xampp/htdocs/Finance/webpages/datas/Annual/IncomeStatement/";

while (false !== ($entry = $d->read())) 
{
   echo $entry."\n";
   if(strpos($entry,'.csv'))
	 {
	  	$reader = PHPExcel_IOFactory::createReader('CSV'); // Ū���ɮ� 
			$PHPExcel = $reader->load( $base.$entry ); // �ɮצW�� 
			$sheet = $PHPExcel->getSheet(0); // Ū���Ĥ@�Ӥu�@��(�s���q 0 �}�l) 
			$highestRow = $sheet->getHighestRow(); // ���o�`�C�� 
			echo "==".$highestRow."<br />\n"; 
			// �@��Ū���@�C 
			for ($row = 2; $row <= $highestRow; $row++) 
			{
				$val = $sheet->getCellByColumnAndRow(0, $row)->getValue(); 
		    //echo $val .' '; 
		    if(strlen($val)==0)
		    	continue;
		    $sql = "INSERT INTO `is` (`1`) VALUES( '$val');";
		    mysql_query($sql);
		  } 
	 		//echo "<br />\n";
	 		//break;
		} 
}


$d->close();



?>