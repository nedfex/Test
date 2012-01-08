<?php
function Income_Statement($SYMBOL)
{
	include("/config/config.php");

	$link = ConnectDB($SQL);
	$selectresult=mysql_select_db("finance",$link);

	//$Income_Statement_target_Qtr= "http://beta.moneycentral.msn.com/investor/invsub/results/statemnt.aspx?Symbol=US:ARO&stmtView=Qtr";
	$Income_Statement_target_Qtr = $MSN_BASE.$MSN_PAGE[0].$MSN_PARAM['income'].$SYMBOL.$VIEW['quarter'];
	
	$web_page = http_get($Income_Statement_target_Qtr, "");
	$table_array = parse_array($web_page['FILE'], "<html", "</html>");
	
	$today = getdate(); 
	
	for($xx=0; $xx<count($table_array); $xx++) # Qtr 
	{
	  $product_row_array = parse_array($table_array[$xx], "<tr", "</tr>");
		$Qtr = SearchTrOld( $product_row_array ,'Q1');
		
		$counter = 0;
	  for($i=$COLUMN_INDEX['income']['start'];$i<=$COLUMN_INDEX['income']['end'];$i++)
	  {
	  	$result[$counter] = SearchTrOld( $product_row_array , '>'. $COLUMN_NAME[$i].'<' );
	  	$counter++;
	  }
	    
	  for($i=1;$i<count($Qtr);$i++)
	  {
	  	$CN = str_replace(' ','_',$Qtr[$i]);
	  	
	  	$query = "UPDATE `finance`.`$CN` SET "; 
	  	$counter = 0;
	  	for($j=$COLUMN_INDEX['income']['start'];$j <= $COLUMN_INDEX['income']['end'];$j++)
	  	{
	  		if(count($result[$counter])>=1)
	  			$query.="`".$j."` = ".$result[$counter][$i].",";
	  		$counter++;
	  	}
	  	$query[strlen($query)-1] = " ";//去掉最後一個 ','
	  	$query.=" WHERE `$CN`.`SYMBOL` = '$SYMBOL' ;";
	  	//echo $query."</br>";
  	  mysql_query($query);    
	  	 
	  }
	}
	
	//$Balance_Sheet_target_Ann = "http://beta.moneycentral.msn.com/investor/invsub/results/statemnt.aspx?Symbol=US:ARO&stmtView=Ann";
	$Income_Statement_target_Ann = $MSN_BASE.$MSN_PAGE[0].$MSN_PARAM['income'].$SYMBOL.$VIEW['annual'];
	$web_page = http_get($Income_Statement_target_Ann, "");
	
	$table_array = parse_array($web_page['FILE'], "<html", "</html>");
	
	for($xx=0; $xx<count($table_array); $xx++) # Balance Sheet Qtr 
	{
	  $product_row_array = parse_array($table_array[$xx], "<tr", "</tr>");
	  $Ann = SearchTrOld( $product_row_array ,$today['year']-1 );
	  
	  $counter = 0;
	  for($i=$COLUMN_INDEX['income']['start'];$i<=$COLUMN_INDEX['income']['end'];$i++)
	  {
	  	$result[$counter] = SearchTrOld( $product_row_array , '>'. $COLUMN_NAME[$i].'<' );
	  	$counter++;
	  }
	  
	 	for($i=1;$i<count($Ann);$i++)
	  {
	  	$AN = $Ann[$i];
	  	
	  	$query = "UPDATE `finance`.`$AN` SET "; 
	  	$counter = 0;
	  	for($j=$COLUMN_INDEX['income']['start'];$j <= $COLUMN_INDEX['income']['end'];$j++)
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
	mysql_close($link);
}
?>