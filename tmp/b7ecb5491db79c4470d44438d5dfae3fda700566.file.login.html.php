<?php /* Smarty version Smarty-3.0.8, created on 2014-09-08 09:13:37
         compiled from "D:\WebSite\yimiaofengqiang/tpl\admin/login.html" */ ?>
<?php /*%%SmartyHeaderCode:9074540d02c1345b20-41334537%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    'b7ecb5491db79c4470d44438d5dfae3fda700566' => 
    array (
      0 => 'D:\\WebSite\\yimiaofengqiang/tpl\\admin/login.html',
      1 => 1409623040,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '9074540d02c1345b20-41334537',
  'function' => 
  array (
  ),
  'has_nocache_code' => false,
)); /*/%%SmartyHeaderCode%%*/?>
<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=gbk">
<title>后台  - 管理登陆</title>
<link rel="stylesheet" href="/assets/stylesheets/admin/login.css" />
</head>
<body>
	<div class="admin-login-around">
		<div class="admin-login">
			<div class="login-info">
				<div class="login-window">
					<h5>管理后台</h5>
					<div class="login">
						<form  action="<?php echo $_smarty_tpl->smarty->registered_plugins[Smarty::PLUGIN_FUNCTION]['spUrl'][0][0]->__template_spUrl(array('c'=>'admin','a'=>'login'),$_smarty_tpl);?>
" method=POST>
							帐号:<input name="username"/>
							密码:<input name="password" type="password"/>
							<input type="submit" value="登录" />
						</form>
					</div>
				</div>
			</div>
		</div>
	</div>
</body>
</html>