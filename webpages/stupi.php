<html> 
<head> 

<title>�լd��</title> 

<meta http-equiv="Content-Type" content="text/html; charset=gb2312"> 

</head> 

<body bgcolor="#FFFFFF"> 

<?php 

function display_form() 
{ 

global $PHP_SELF; 

?> 

<FORM action="stupi.php "METHOD=post> 

�W�r: <INPUT TYPE=TEXT NAME="name"><BR> 

�涵���: 

<INPUT TYPE=RADIO NAME="first" VALUE="�ګܲ�">�ګܲ� 

<INPUT TYPE=RADIO NAME="first" VALUE="�ګD�`��">�ګD�`�� 

<INPUT TYPE=RADIO NAME="first" VALUE="��²���N�O�Ӷ̫_"> ��²���N�O�Ӷ̫_ <br> 

�h�����: 

<INPUT TYPE=CHECKBOX NAME="second[]" VALUE="�ڳ��w���Ųy">�ڳ��w���Ųy 

<INPUT TYPE=CHECKBOX NAME="second[]" VALUE="�ڳ��w��a">�ڳ��w��a 

<INPUT TYPE=CHECKBOX NAME="second[]" VALUE="�ڳ��w���R">�ڳ��w���R 

<INPUT TYPE=CHECKBOX NAME="second[]" VALUE="�ڳ��w���s">�ڳ��w���s 

<INPUT TYPE=HIDDEN NAME="stage" VALUE= "results"><p> 

<INPUT TYPE=SUBMIT VALUE= "����"></p> 

</FORM> 

<?php 

} 

?> 

//�{�Ƕ}�l 

<?php 

function process_form() 

{ 

global $name ; 

global $first; 

global $second; 


if ($first == '�ګܲ�') { $first_message = '�A���¡C'; } 

elseif ($first == '�ګD�`��') { $first_message = '�A���o���C'; } 

else { $first_message = '�A²���N���O�@���o�����H�F�C'; } 

$favorite_second = count($second); 

if ($favorite_second <= 1) 

{$second_message = '���A�ܧִN�|�b�ʪ���̦��h�A�b���a�I';} 

elseif ($favorite_second > 1 && $favorite_second < 4) 

{$second_message = '�A�O�u�R�B�ʪ����V�V�C';} 

else {$second_message = '�A�B�ʪ��Ӧh�F�A��V�V�����w�g�L�q�A�A�ǳƴç��a�A�G�]';} 

echo "�o�O�@���w��V�V�����աG<br><br>"; 

echo "�A�n�I �A���W�r�s�G$name. <br><br>"; 

echo "�A�����絲�G�O�C�C�C�C�C$first_message $second_message"; 

} 

?> 

<?php 

if (empty($stage)) { display_form(); } 

else { process_form(); } 

?> 

</body> 

</html> 