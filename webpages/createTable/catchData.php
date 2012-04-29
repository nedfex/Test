<html>
<link href="htc.css" rel="stylesheet" type="text/css">
	<head>
    	<script type="text/javascript" src="https://ajax.googleapis.com/ajax/libs/jquery/1.7.1/jquery.min.js"></script>
    </head>
	<body>
		<form action="catchData.php" method="get"> 
          <input type="text" name="SYMBOL_NAME" placeholder="ARO" value=<? echo (array_key_exists('SYMBOL_NAME',$_GET))?$_GET['SYMBOL_NAME']:"ARO"; ?> >
		  <input type="submit" value="Extrat Data">
		</form>
        <script language="javascript">

		function updateOnPEChange(pe)
		{
			var future_eps = $('.future_eps_value').html();
			var new_future_price = future_eps * pe;
			var new_now_safty_price = new_future_price / 8;
			$('.future_price_value').html(new_future_price.toFixed(2));
			$('.now_safty_price_value').html(new_now_safty_price.toFixed(2));
		}
        $(document).ready(function(){
			var min_pe = 10000;
			var min_pe_radio;
			$('.pe_ratio_value').each(function(){
				if (parseFloat($(this).html()) < min_pe){
					min_pe = parseFloat($(this).html());
					min_pe_radio = $(this).prev().find('input');
				}
			});
			min_pe_radio.attr('checked', true);
			$(".pe_ratio_title").click(function(event){
				var selected_pe = $(this).parent().next('.pe_ratio_value').html();
				updateOnPEChange(selected_pe);
			});
			$('.pe_ratio_input').blur(function(){
				if ($('.pe_ratio_title.user_input').is(':checked')){
					var input_pe = $(this).val();
					updateOnPEChange(input_pe);
				}
			});
			$('.pe_ratio_input').click(function(){
				$('.pe_ratio_title.user_input').attr('checked', true);
			});
		});
        </script>
	</body>
</html>

<?php
#Initialization

include("..\..\W3C_lib\LIB_http.php");
include("..\..\W3C_lib\LIB_parse.php");
include("..\..\W3C_lib\LIB_SearchTr.php");
include("..\config\config.php");

include("DisplayCompanyData.php");
include("estimatePrice.php");
include("getCurrentPrice.php");
include("..\utility.php");

set_time_limit(3600);

$link = ConnectDB($SQL);
$selectresult = mysql_select_db("finance_ms",$link);
$today = getdate(); 

if(array_key_exists('SYMBOL_NAME',$_GET))
{
	$row['TICKER'] = strtoupper($_GET['SYMBOL_NAME']).":US";
	//echo $row['SYMBOL'];
}
else
{
	$row['SYMBOL'] = "GOOG:US";
}

$query= "SELECT * FROM `company3` WHERE `TICKER` =  '$row[TICKER]:US';";
$result =mysql_query($query,$link);
if( $result && mysql_num_rows($result)==0 )
{
	echo "SYMBOL <b>\" $row[TICKER]:US \"</b> is not found.</br>";
	//return;
	
	$query= "SELECT * FROM `company` WHERE `CompanyName` LIKE '%$row[TICKER]%';";
	$result = mysql_query($query);
	
	if(mysql_num_rows($result)==0)
	{
		echo "Company  <b>\" $row[TICKER]:US \"</b> is not found</br>";
		return;
	}
	
	for($i=0;$i < mysql_num_rows($result) ; $i++)
	{
		$row = mysql_fetch_array($result);
		echo "<a href = \"catchData.php?SYMBOL_NAME=$row[TICKER]:US\">Do you mean : <b>$row[CompanyName]($row[TICKER])</b>?</a></br>";
	}
	return;
}

DisplayCompanyData($row['TICKER'],$link);

mysql_close($link);


?>