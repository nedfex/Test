<html> 
<head> 

<title>調查表</title> 

<meta http-equiv="Content-Type" content="text/html; charset=gb2312"> 

</head> 

<body bgcolor="#FFFFFF"> 

<?php 

function display_form() 
{ 

global $PHP_SELF; 

?> 

<FORM action="stupi.php "METHOD=post> 

名字: <INPUT TYPE=TEXT NAME="name"><BR> 

單項選擇: 

<INPUT TYPE=RADIO NAME="first" VALUE="我很笨">我很笨 

<INPUT TYPE=RADIO NAME="first" VALUE="我非常笨">我非常笨 

<INPUT TYPE=RADIO NAME="first" VALUE="我簡直就是個傻冒"> 我簡直就是個傻冒 <br> 

多項選擇: 

<INPUT TYPE=CHECKBOX NAME="second[]" VALUE="我喜歡打藍球">我喜歡打藍球 

<INPUT TYPE=CHECKBOX NAME="second[]" VALUE="我喜歡游泳">我喜歡游泳 

<INPUT TYPE=CHECKBOX NAME="second[]" VALUE="我喜歡跳舞">我喜歡跳舞 

<INPUT TYPE=CHECKBOX NAME="second[]" VALUE="我喜歡爬山">我喜歡爬山 

<INPUT TYPE=HIDDEN NAME="stage" VALUE= "results"><p> 

<INPUT TYPE=SUBMIT VALUE= "謝謝"></p> 

</FORM> 

<?php 

} 

?> 

//程序開始 

<?php 

function process_form() 

{ 

global $name ; 

global $first; 

global $second; 


if ($first == '我很笨') { $first_message = '你不笨。'; } 

elseif ($first == '我非常笨') { $first_message = '你很聰明。'; } 

else { $first_message = '你簡直就像是一個聰明的人了。'; } 

$favorite_second = count($second); 

if ($favorite_second <= 1) 

{$second_message = '但你很快就會在動物園裡死去，懺悔吧！';} 

elseif ($favorite_second > 1 && $favorite_second < 4) 

{$second_message = '你是只愛運動的的猩猩。';} 

else {$second_message = '你運動的太多了，對猩猩來講已經過量，你準備棺材吧，：（';} 

echo "這是一項針對猩猩的測試：<br><br>"; 

echo "你好！ 你的名字叫：$name. <br><br>"; 

echo "你的測驗結果是。。。。。$first_message $second_message"; 

} 

?> 

<?php 

if (empty($stage)) { display_form(); } 

else { process_form(); } 

?> 

</body> 

</html> 