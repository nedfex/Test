<?php
function SearchTr( $product_row_array ,$row_name ) //return elements <td>
{	
	$ans = array();
	//echo "<tr>";
	for($table_row=0; $table_row<count($product_row_array); $table_row++) 
	{
		$temp_row = $product_row_array[$table_row];
		$pattern = array('/\s+/i','/> /i','/ </i');
		$replacement = array( ' ' , '>' , '<' );
		$temp_row = preg_replace($pattern, $replacement,$temp_row);
  		if( stristr( $temp_row, '>'.$row_name.'<' ))
  		{
  			$temp_array = parse_array($product_row_array[$table_row],"<td" ,"</td");
  			//echo "<td>".strip_tags(trim($temp_array[0]))."</td>";
  			$ans[0] = strip_tags(trim($temp_array[0]));
  			for($xxx = 1 ;$xxx<count($temp_array);$xxx++)
  			{
  				$ans[$xxx] = str_replace(",","",strip_tags(trim($temp_array[$xxx])));
  				//echo "<td>".$ans[$xxx]."</td>";
  			}
  			//echo "\n";
  			break;
  		}		
  	}  
  //echo "</tr>";
	return $ans;
}
function SearchTrOld( $product_row_array ,$row_name ) //return elements <td>
{
	$ans = array();
	//echo "<tr>";
	for($table_row=0; $table_row<count($product_row_array); $table_row++) 
	{
  		if( stristr( $product_row_array[$table_row], "$row_name" ))
  		{
  			$temp_array = parse_array($product_row_array[$table_row],"<td" ,"</td");
  			//echo "<td>".strip_tags(trim($temp_array[0]))."</td>";
  			$ans[0] = strip_tags(trim($temp_array[0]));
  			for($xxx = 1 ;$xxx<count($temp_array);$xxx++)
  			{
  				$ans[$xxx] = str_replace(",","",strip_tags(trim($temp_array[$xxx])));
  				//echo "<td>".$ans[$xxx]."</td>";
  			}
  			//echo "\n";
  			break;
  		}		
  	}  
  //echo "</tr>";
	return $ans;
}

?>