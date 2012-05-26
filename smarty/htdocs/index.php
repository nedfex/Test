<?php
require_once('./header.php');

$smarty->assign('name','Ned');

//** un-comment the following line to show the debug console
//$smarty->debugging = true;

$smarty->display('index.tpl');

require_once('./footer.php');
?>