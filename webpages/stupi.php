<html> 
<head> 

<title>秸琩</title> 

<meta http-equiv="Content-Type" content="text/html; charset=gb2312"> 

</head> 

<body bgcolor="#FFFFFF"> 

<?php 

function display_form() 
{ 

global $PHP_SELF; 

?> 

<FORM action="stupi.php "METHOD=post> 

: <INPUT TYPE=TEXT NAME="name"><BR> 

虫兜匡拒: 

<INPUT TYPE=RADIO NAME="first" VALUE="и猜">и猜 

<INPUT TYPE=RADIO NAME="first" VALUE="и獶盽猜">и獶盽猜 

<INPUT TYPE=RADIO NAME="first" VALUE="и虏碞琌短玙"> и虏碞琌短玙 <br> 

兜匡拒: 

<INPUT TYPE=CHECKBOX NAME="second[]" VALUE="и尺舧ゴ屡瞴">и尺舧ゴ屡瞴 

<INPUT TYPE=CHECKBOX NAME="second[]" VALUE="и尺舧村猘">и尺舧村猘 

<INPUT TYPE=CHECKBOX NAME="second[]" VALUE="и尺舧铬籖">и尺舧铬籖 

<INPUT TYPE=CHECKBOX NAME="second[]" VALUE="и尺舧">и尺舧 

<INPUT TYPE=HIDDEN NAME="stage" VALUE= "results"><p> 

<INPUT TYPE=SUBMIT VALUE= "谅谅"></p> 

</FORM> 

<?php 

} 

?> 

//祘秨﹍ 

<?php 

function process_form() 

{ 

global $name ; 

global $first; 

global $second; 


if ($first == 'и猜') { $first_message = 'ぃ猜'; } 

elseif ($first == 'и獶盽猜') { $first_message = '羙'; } 

else { $first_message = '虏碞钩琌羙'; } 

$favorite_second = count($second); 

if ($favorite_second <= 1) 

{$second_message = 'е碞穦笆堕柑腷';} 

elseif ($favorite_second > 1 && $favorite_second < 4) 

{$second_message = '琌稲笲笆礦礦';} 

else {$second_message = '笲笆び癸礦礦ㄓ量竒筁秖非称疵';} 

echo "硂琌兜皐癸礦礦代刚<br><br>"; 

echo " $name. <br><br>"; 

echo "代喷挡狦琌$first_message $second_message"; 

} 

?> 

<?php 

if (empty($stage)) { display_form(); } 

else { process_form(); } 

?> 

</body> 

</html> 