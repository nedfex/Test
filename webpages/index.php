<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Awesome Stock</title>
<script src="http://ajax.googleapis.com/ajax/libs/jquery/1.5/jquery.min.js"></script>
</head>

<body>
<div>
<?php
$app_id = "102263869865721";
$app_secret = "26ba8b24f5d144c779a6211a000d7b0f";
$my_url = "http://127.0.0.1/Finance/webpages/";
$code = @$_REQUEST["code"];
if(empty($code)) {
	$dialog_url = "https://www.facebook.com/dialog/oauth?client_id=". $app_id ."&scope=email&redirect_uri=" . urlencode($my_url);
	echo("<script> top.location.href='" . $dialog_url . "'</script>");
}
$token_url = "https://graph.facebook.com/oauth/access_token?" . "client_id=" . $app_id . "&redirect_uri=" . urlencode($my_url) . "&client_secret=" . $app_secret . "&code=" . $code;
$response = file_get_contents($token_url);
$params = null;
parse_str($response, $params);
$graph_url = "https://graph.facebook.com/me?access_token=".$params['access_token'];
$user = json_decode(file_get_contents($graph_url));
echo("Hello " . $user->name);
echo("<br>Your Mail is: " . $user->email);

?>
</div>
<style type="text/css">
div.item_holder{
	border:1px solid gray;
	width: 300px;
	float:left;
	padding:5px;
	font-family:Helvetica, Arial, sans-serif;
}
div#sector_holder{
	width:130px;
}
div#industry_holder{
	width:250px;
}
div#summary_holder{
	width:600px;
	text-align:center;
}
table{
	border-collapse:collapse;
	border:1px solid gray;
	margin:1px auto;
}
td{
	border:1px solid gray;
	padding:5px;
}
span{
	cursor:pointer;
}
</style>
<form>
<input type="text" />
<button>Search</button>
</form>
<hr />
<div id="sector_holder" class="item_holder">

</div>
<div id="industry_holder" class="item_holder">

</div>
<div id="company_holder" class="item_holder">

</div>
<div id="summary_holder" class="item_holder">

</div>
<script type="text/javascript">
$(document).ready(function() {
	$.ajax({
		url: "ajax.php",
		type: "GET",
		data: ({service : 'a'}),
		dataType: "json",
		success: function(data){
			html='';
			for (i=0; i<data.length; i++){
				html = html + '<span class="sector_item">'+data[i]+'</span><br>';
			}
			$('#sector_holder').html(html);
			$('.sector_item').click(function(){
				$('#industry_holder').html('<img src="./images/loading.gif">');
				sector = $(this).html();
				sector_handler(sector);
			});
		}
	});
	
	function sector_handler(sectorName){
		$.ajax({
			url: "ajax.php",
			type: "GET",
			data: ({service : 'b', sector: sectorName}),
			dataType: "json",
			success: function(data){
				html='';
				for (i=0; i<data.length; i++){
					html = html + '<span class="industry_item">'+data[i]+'</span><br>';
				}
				$('#industry_holder').hide();
				$('#industry_holder').html(html);
				$('#industry_holder').slideDown();
				$('.industry_item').click(function(){
					$('#company_holder').html('<img src="./images/loading.gif">');
					industry = $(this).html();
					industry_handler(sectorName, industry);
				});
			}
		});
	}
	
	function industry_handler(sectorName, industryName){
		$.ajax({
			url: "ajax.php",
			type: "GET",
			data: ({service : 'c', sector: sectorName, industry: industryName}),
			dataType: "json",
			success: function(data){
				html='';
				for (i=0; i<data.length; i++){
					html = html + '<span class="company_item" id='+data[1][i]+'>'+data[0][i]+'('+data[1][i]+')'+'</span><br>';
				}
				$('#company_holder').hide()
				$('#company_holder').html(html);
				$('#company_holder').slideDown();
				$('.company_item').click(function(){
					$('#summary_holder').html('<img src="./images/loading.gif">');
					summary_handler($(this).attr('id'));
				});
			}
		});
	}
	
	function summary_handler(symbolName){
		$.ajax({
			url: "ajax.php",
			type: "GET",
			data: ({service : 'd', symbol: symbolName}),
			dataType: "html",
			success: function(data){
				html=data;
				$('#summary_holder').hide()
				$('#summary_holder').html(html);
				$('#summary_holder').fadeIn();
			}
		});
	}
});
</script>
</body>
</html>
