<?php
function BalanceSheet( $SYMBOL ,$link )
{
	include("/config/config.php");
	
	//$Balance_Sheet_target_Qtr = $MSN_BASE.$MSN_PAGE[0].$MSN_PARAM['balance'].$SYMBOL.$VIEW['quarter'];
	$Balance_Sheet_target_Qtr = "http://investing.money.msn.com/investments/stock-balance-sheet/?symbol=us%3A$SYMBOL&stmtView=Qtr";
	echo $Balance_Sheet_target_Qtr."</br>";
	$web_page = http_get($Balance_Sheet_target_Qtr, "");
	$table_array = parse_array($web_page['FILE'], "<html", "</html>");
		
	$today = getdate(); 
	
	for($xx=0; $xx<count($table_array); $xx++) # Balance Sheet Qtr 
	{
		$product_row_array = parse_array($table_array[$xx], "<tr", "</tr>");
	  $Qtr = SearchTrOld( $product_row_array ,"Q1" );
	  echo count($Qtr);
	  
	  $end_date = SearchTrOld( $product_row_array ,'/20' );
	  for( $i=1 ; $i < count( $Qtr ) ; $i++ )
	  {
	  	$CN = str_replace(' ','_',$Qtr[$i]);
	  	$query = "INSERT INTO `$CN` (`SYMBOL`,`1`) VALUES( '$SYMBOL' , '$end_date[$i]');";

	  	mysql_query($query);
	  	
	  	if($i==1)
	  	{
	  		if(mysql_num_rows(mysql_query("SELECT * FROM `newest_date` WHERE `SYMBOL` = '$SYMBOL';"))>=1)
	  			$query = "UPDATE `newest_date` SET `QUARTER` = '$CN' WHERE `SYMBOL` = '$SYMBOL';" ; 
	  		else
	  			$query = "INSERT INTO `newest_date` (`SYMBOL`,`ACTIVE`,`QUARTER`) VALUES( '$SYMBOL' , '1' ,'$CN' );";
	  		echo $query."</br>";
	  		mysql_query($query);
	  	}
		
	  }
	  	  
	  $counter = 0;
	  for($i=$COLUMN_INDEX['balance']['start'];$i<= $COLUMN_INDEX['balance']['end'];$i++)
	  {
	  	$result[$counter] = SearchTrOld( $product_row_array ,'>'. $COLUMN_NAME[$i].'<' );
	  	$counter++;
	  }
	  
  	
  	for($i=1;$i<count($Qtr);$i++)
	  {
	  	
	  	$CN = str_replace(' ','_',$Qtr[$i]);
	  	//echo $CN;
	  	$query = "UPDATE `finance`.`$CN` SET "; 
	  	$counter = 0;
	  	for($j=$COLUMN_INDEX['balance']['start'];$j <= $COLUMN_INDEX['balance']['end'];$j++)
	  	{
	  		if(count($result[$counter])>=1)
	  			$query.="`".$j."` = ".$result[$counter][$i].",";
	  		$counter++;
	  	}
	  	$query[strlen($query)-1] = " ";//去掉最後一個 ','
	  	$query.=" WHERE `$CN`.`SYMBOL` = '$SYMBOL' ;";
	  	echo $query."</br>";
  	  mysql_query($query);    
  	    
	  }
	}
	
	$Balance_Sheet_target_Ann = $MSN_BASE.$MSN_PAGE[0].$MSN_PARAM['balance'].$SYMBOL.$VIEW['annual'];
	$web_page = http_get($Balance_Sheet_target_Ann, "");
	$table_array = parse_array($web_page['FILE'], "<html", "</html>");

	for($xx=0; $xx<count($table_array); $xx++) # Balance Sheet Ann
	{
	  $product_row_array = parse_array($table_array[$xx], "<tr", "</tr>");

	  $Ann = SearchTrOld( $product_row_array ,$today['year']-1 );
  
	  for( $i=1 ; $i < count( $Ann ) ; $i++ )
	  {
	  	$AN = $Ann[$i];
	  	$end_date = SearchTrOld( $product_row_array ,'/20' );
	  	$query = "INSERT INTO `$AN` (`SYMBOL`,`1`) VALUES( '$SYMBOL' ,'$end_date[$i]');";

	  	mysql_query($query);
	  	
	  	if($i==1)
	  	{
	  		if(mysql_num_rows(mysql_query("SELECT * FROM `newest_date` WHERE `SYMBOL` = '$SYMBOL';"))>=1)
	  			$query = "UPDATE `newest_date` SET `ANNUAL` = '$AN' WHERE `SYMBOL` = '$SYMBOL';" ; 
	  		else
	  			$query = "INSERT INTO `newest_date` (`SYMBOL`,`ACTIVE`,`ANNUAL`) VALUES( '$SYMBOL' , '1' ,'$AN' );";
	  		//echo $query."</br>";
	  		mysql_query($query);
	  	}
	  }
	  
	  for( $i=1 ; $i < count( $Ann ) ; $i++ )
	  {
	  	$AN = $Ann[$i]-5;
	  	$query = "INSERT INTO `$AN` (`SYMBOL`) VALUES( '$SYMBOL');";
	  	//echo $query.'</br>';
	  	mysql_query($query);
	  }
	  
		$counter = 0;
	  for($i=$COLUMN_INDEX['balance']['start'];$i<=$COLUMN_INDEX['balance']['end'];$i++)
	  {
	  	$result[$counter] = SearchTrOld( $product_row_array , '>'. $COLUMN_NAME[$i].'<' );
	  	$counter++;
	  }
	  
  	
  	for($i=1;$i< count($Ann);$i++)
	  {
	  	$AN = $Ann[$i];
	  	$query = "UPDATE `finance`.`$AN` SET "; 
	  	$counter = 0;
	  	for($j=$COLUMN_INDEX['balance']['start'];$j <= $COLUMN_INDEX['balance']['end'];$j++)
	  	{
	  		if(count($result[$counter])>=1)
	  			$query.="`".$j."` = ".$result[$counter][$i].",";
	  		$counter++;
	  	}
	  	$query[strlen($query)-1] = " ";//去掉最後一個 ','
	  	$query.=" WHERE `$AN`.`SYMBOL` = '$SYMBOL' ;";
	  	//echo $query."</br>";
  	  mysql_query($query);    
	  	

	  }

	}
		//mysql_close($link);
}
?>