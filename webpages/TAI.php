<?php
# Initialization
include("LIB_http.php");
include("LIB_parse.php");

$product_array=array();
$Total_Revenue_array = array();
$product_count=0;

# Download the target (store) web page
$Income_Statement_target = "http://beta.moneycentral.msn.com/investor/invsub/results/statemnt.aspx?Symbol=US:ARO";
$Balance_Sheet_target = "http://beta.moneycentral.msn.com/investor/invsub/results/statemnt.aspx?lstStatement=10YearSummary&Symbol=US:ARO";

$web_page = http_get($Income_Statement_target, "");

$table_array = parse_array($web_page['FILE'], "ftable", "</table>");
echo "number of table ".count($table_array)."\n";

for($xx=0; $xx<count($table_array); $xx++) # Income Statement
{
  $product_row_array = parse_array($table_array[$xx], "<tr", "</tr>");
  for($table_row=0; $table_row<count($product_row_array); $table_row++) # 先找出所有table的列
  {
  		#如果找到開頭是 total revenue的<td> 接下來連讀5<td>
  		#echo $product_row_array[$table_row]."\n";
  		if( stristr($product_row_array[$table_row], "Total Revenue" ))
  		{
  			#echo $product_row_array[$table_row];
  			$revenue_array = parse_array($product_row_array[$table_row],"<td" ,"</td");
  			for($xxx = 0 ;$xxx<6;$xxx++)
  			{
  				$Total_Revenue_array[$xxx] = strip_tags(trim($revenue_array[$xxx]));
  				#echo $xxx.$Total_Revenue_array[$xxx-1]."\n";
  			}
  		}
  		else if( stristr($product_row_array[$table_row], "Gross Profit" ))
  		{
  			#echo $product_row_array[$table_row];
  			$row_array = parse_array($product_row_array[$table_row],"<td" ,"</td");
  			for($xxx = 0 ;$xxx<6;$xxx++)
  			{
  				$Gross_Profit_array[$xxx] = strip_tags(trim($row_array[$xxx]));
  				#echo $xxx.$Total_Revenue_array[$xxx-1]."\n";
  			}
  		}
  		else if( stristr($product_row_array[$table_row], "Selling/General/Administrative Expenses, Total" ))
  		{
  			$row_array = parse_array($product_row_array[$table_row],"<td" ,"</td");
  			for($xxx = 0 ;$xxx<6;$xxx++)
  			{
  				$Expenses_array[$xxx] = strip_tags(trim($row_array[$xxx]));
  				#echo $xxx.$Total_Revenue_array[$xxx-1]."\n";
  			}
  		}
  		else if( stristr($product_row_array[$table_row], "Research & Development" ))
  		{
  			$row_array = parse_array($product_row_array[$table_row],"<td" ,"</td");
  			for($xxx = 0 ;$xxx<6;$xxx++)
  			{
  				$RD_array[$xxx] = strip_tags(trim($row_array[$xxx]));
  			}
  		}
  		else if( stristr($product_row_array[$table_row], "Depreciation/Amortization" ))
  		{
  			$row_array = parse_array($product_row_array[$table_row],"<td" ,"</td");
  			for($xxx = 0 ;$xxx<6;$xxx++)
  			{
  				$Depreciation_array[$xxx] = strip_tags(trim($row_array[$xxx]));
  			}
  		}
  		else if( stristr($product_row_array[$table_row], "Interest Expense" ))
  		{
  			$row_array = parse_array($product_row_array[$table_row],"<td" ,"</td");
  			for($xxx = 0 ;$xxx<6;$xxx++)
  			{
  				$Interest_Expense_array[$xxx] = strip_tags(trim($row_array[$xxx]));
  			}
  		}
  		else if( stristr($product_row_array[$table_row], "Operating Income" ))
  		{
  			$row_array = parse_array($product_row_array[$table_row],"<td" ,"</td");
  			for($xxx = 0 ;$xxx<6;$xxx++)
  			{
  				$Operating_Income_array[$xxx] = strip_tags(trim($row_array[$xxx]));
  			}
  		}
  		else if( stristr($product_row_array[$table_row], "Income Before Tax" ))
  		{
  			$row_array = parse_array($product_row_array[$table_row],"<td" ,"</td");
  			for($xxx = 0 ;$xxx<6;$xxx++)
  			{
  				$Income_Before_Tax_array[$xxx] = strip_tags(trim($row_array[$xxx]));
  			}
  		}
  		else if( stristr($product_row_array[$table_row], "Income After Tax" ))
  		{
  			$row_array = parse_array($product_row_array[$table_row],"<td" ,"</td");
  			for($xxx = 0 ;$xxx<6;$xxx++)
  			{
  				$Income_After_Tax_array[$xxx] = strip_tags(trim($row_array[$xxx]));
  			}
  		}
  		else if( stristr($product_row_array[$table_row], "Cost of Revenue, Total" ))
  		{
  			$row_array = parse_array($product_row_array[$table_row],"<td" ,"</td");
  			for($xxx = 0 ;$xxx<6;$xxx++)
  			{
  				$Cost_of_Revenue_array[$xxx] = strip_tags(trim($row_array[$xxx]));
  			}
  		}
  		
  		
  		 		
  }  
}

for($xxx =0 ;$xxx<6;$xxx++)
	echo $Total_Revenue_array[$xxx]." ";
echo "\n";

for($xxx =0 ;$xxx<6;$xxx++)
	echo $Gross_Profit_array[$xxx]." ";
echo "\n";

for($xxx =0 ;$xxx<6;$xxx++)
	echo $Expenses_array[$xxx]." ";
echo "\n";

for($xxx =0 ;$xxx<6;$xxx++)
	echo $RD_array[$xxx]." ";
echo "\n";

for($xxx =0 ;$xxx<6;$xxx++)
	echo $Depreciation_array[$xxx]." ";
echo "\n";

for($xxx =0 ;$xxx<6;$xxx++)
	echo $Interest_Expense_array[$xxx]." ";
echo "\n";

for($xxx =0 ;$xxx<6;$xxx++)
	echo $Operating_Income_array[$xxx]." ";
echo "\n";

for($xxx =0 ;$xxx<6;$xxx++)
	echo $Income_Before_Tax_array[$xxx]." ";
echo "\n";

for($xxx =0 ;$xxx<6;$xxx++)
	echo $Income_After_Tax_array[$xxx]." ";
echo "\n";

for($xxx =0 ;$xxx<6;$xxx++)
	echo $Cost_of_Revenue_array[$xxx]." ";
echo "\n";


?>