<?php
require_once('../libs/Smarty.class.php');
$smarty = new Smarty();

$smarty->setTemplateDir('../templates/');
$smarty->setCompileDir('../templates_c/');
$smarty->setConfigDir('../configs/');
$smarty->setCacheDir('../cache/');

$smarty->debugging = true;

?>
<!DOCTYPE HTML>
<html>
	<head>
		<link rel="stylesheet/less" type="text/css" href="../less/styles.less">
		<script src="../libs/less.js" type="text/javascript"></script>
	</head>
	<body>