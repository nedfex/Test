<?php /* Smarty version Smarty-3.1.8, created on 2012-05-05 18:05:41
         compiled from "../templates/index.tpl" */ ?>
<?php /*%%SmartyHeaderCode:21236373424fa4dfa8e03fb7-48080679%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    '16598974be41913e67d6e2462715a26137b17224' => 
    array (
      0 => '../templates/index.tpl',
      1 => 1336212336,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '21236373424fa4dfa8e03fb7-48080679',
  'function' => 
  array (
  ),
  'version' => 'Smarty-3.1.8',
  'unifunc' => 'content_4fa4dfa9119eb4_66194731',
  'variables' => 
  array (
    'name' => 0,
  ),
  'has_nocache_code' => false,
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_4fa4dfa9119eb4_66194731')) {function content_4fa4dfa9119eb4_66194731($_smarty_tpl) {?>

Hello <?php echo $_smarty_tpl->tpl_vars['name']->value;?>
, welcome to Smarty!

<div class="page_container">
	<div class="header">
	a
	</div>
	<div class="body_container">
		<div class="col_left">
			<div class="section">
				<div class="title">
				title
				</div>
			</div>
			<div class="section">
				<div class="title">
				title
				</div>
			</div>
		</div>
		<div class="col_center">
		c
		</div>
		<div class="col_right">
		d
		</div>
	</div>
</div><?php }} ?>