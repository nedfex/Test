<?php
#Initialization
include("LIB_http.php");
include("LIB_parse.php");
include("getWithCurl.php");
$product_array=array();
$product_count=0;

#Downloadthetarget (store) web page
$target = "http://www.tellmewhenitchanges.com/buyair";
$web_page = http_get($target, "");

#$content = file_get_contents($target);
#echo $content;

$table_array = parse_array($web_page['FILE'], "<table", "</table>");
echo count($table_array);


# Look for the table that contains the product information
for($xx=0; $xx<count($table_array); $xx++)
    {
    $table_landmark = "Products For Sale";
    if(stristr($table_array[$xx], $table_landmark))        // Process this table
        {
        echo "FOUND: Product table<p>";
        
        # Parse table into an array of table rows    
        $product_row_array = parse_array($table_array[$xx], "<tr", "</tr>");
        
        for($table_row=0; $table_row<count($product_row_array); $table_row++)
            {
            # Detect the beginning of the desired data (Heading row)
            $heading_landmark = "Condition";
            if((stristr($product_row_array[$table_row], $heading_landmark)))
                {
                echo "<p>FOUND: Table heading row";
                
                # Get the postistion of the desired headings
                $table_cell_array = parse_array($product_row_array[$table_row], "<td", "</td>");
                for($heading_cell=0; $heading_cell<count($table_cell_array); $heading_cell++)
                    {
                    if(stristr(strip_tags(trim($table_cell_array[$heading_cell])), "ID#")) 
                        $id_column=$heading_cell;
                    if(stristr(strip_tags(trim($table_cell_array[$heading_cell])), "Product Name")) 
                        $name_column=$heading_cell;
                    if(stristr(strip_tags(trim($table_cell_array[$heading_cell])), "Price")) 
                        $price_column=$heading_cell;
                    }
                echo "FOUND: id_column=$id_column"."<p>";
                echo "FOUND: price_column=$price_column"."<p>";
                echo "FOUND: name_column=$name_column"."<p>";
                
                # Save the heading row for later use
                $heading_row = $table_row;
                }
            
            # Detect the end of the desired data 
            $ending_landmark = "Calculate";
            if((stristr($product_row_array[$table_row], $ending_landmark)))
                {
                echo "PARSING COMPLETE!\n";
                break;
                }
            
            # Parse product & price data
            if(isset($heading_row) && $heading_row<$table_row)
                {
                $table_cell_array = parse_array($product_row_array[$table_row], "<td", "</td>");
                $product_array[$product_count]['ID'] = strip_tags(trim($table_cell_array[$id_column]));
                $product_array[$product_count]['NAME'] = strip_tags(trim($table_cell_array[$name_column]));
                $product_array[$product_count]['PRICE'] = strip_tags(trim($table_cell_array[$price_column]));
                $product_count++;
                echo"PROCESSED: Item #$product_count<p>";
                }
            }
        } 
    }
# Display the collected data
for($xx=0; $xx<count($product_array); $xx++)
    {
    echo "$xx. ";
    echo "ID: ".$product_array[$xx]['ID'].", ";
    echo "NAME: ".$product_array[$xx]['NAME'].", ";
    echo "PRICE: ".$product_array[$xx]['PRICE']."<p>";
    }
?>