<?php /* Smarty version Smarty-3.0.8, created on 2016-05-05 14:15:53
         compiled from "D:\WebSite\yimiaofengqiang/tpl\admin/pro.html" */ ?>
<?php /*%%SmartyHeaderCode:9224572ae5193abc19-63158932%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    'ae59bb689617c282ee92d95874b684f517c6cb1e' => 
    array (
      0 => 'D:\\WebSite\\yimiaofengqiang/tpl\\admin/pro.html',
      1 => 1456295466,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '9224572ae5193abc19-63158932',
  'function' => 
  array (
  ),
  'has_nocache_code' => false,
)); /*/%%SmartyHeaderCode%%*/?>
<?php if (!is_callable('smarty_modifier_escape')) include 'D:\WebSite\yimiaofengqiang\SpeedPHP\Drivers\Smarty\plugins\modifier.escape.php';
?><!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=gbk">
<title>后台  - 商品管理</title>
<link rel="stylesheet" href="/assets/stylesheets/admin/head.css" />
<link rel="stylesheet" href="/assets/stylesheets/admin/pro.css" />
<link rel="stylesheet" href="/assets/stylesheets/admin/page.css" />
<script type="text/javascript">
    function delfun(){
        if(confirm("请仔细核对无误，删除本数据后不能恢复。")){
            return true;
        }else{
            return false;
        }
    }
</script>
</head>
<body>
        <?php $_template = new Smarty_Internal_Template("admin/head.html", $_smarty_tpl->smarty, $_smarty_tpl, $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, null, null);
 echo $_template->getRenderedTemplate(); $_template->rendered_content = null;?><?php unset($_template);?>
	<div class="pro-ctrl">
                <a href="<?php echo $_smarty_tpl->smarty->registered_plugins[Smarty::PLUGIN_FUNCTION]['spUrl'][0][0]->__template_spUrl(array('c'=>'admin','a'=>'pro','mode'=>$_smarty_tpl->getVariable('mode')->value,'classification'=>1),$_smarty_tpl);?>
">一秒疯抢</a>
                <a href="<?php echo $_smarty_tpl->smarty->registered_plugins[Smarty::PLUGIN_FUNCTION]['spUrl'][0][0]->__template_spUrl(array('c'=>'admin','a'=>'pro','mode'=>$_smarty_tpl->getVariable('mode')->value,'classification'=>2),$_smarty_tpl);?>
">一秒疯抢独家</a>
                <a href="<?php echo $_smarty_tpl->smarty->registered_plugins[Smarty::PLUGIN_FUNCTION]['spUrl'][0][0]->__template_spUrl(array('c'=>'admin','a'=>'pro','mode'=>$_smarty_tpl->getVariable('mode')->value,'classification'=>3),$_smarty_tpl);?>
">一秒疯抢特价</a>
                <a href="<?php echo $_smarty_tpl->smarty->registered_plugins[Smarty::PLUGIN_FUNCTION]['spUrl'][0][0]->__template_spUrl(array('c'=>'admin','a'=>'pro','mode'=>$_smarty_tpl->getVariable('mode')->value,'type'=>87),$_smarty_tpl);?>
">明日预告</a>
		<a href="<?php echo $_smarty_tpl->smarty->registered_plugins[Smarty::PLUGIN_FUNCTION]['spUrl'][0][0]->__template_spUrl(array('c'=>'admin','a'=>'pro','mode'=>$_smarty_tpl->getVariable('mode')->value,'status'=>'ygq'),$_smarty_tpl);?>
">过期数据</a>
		<a href="<?php echo $_smarty_tpl->smarty->registered_plugins[Smarty::PLUGIN_FUNCTION]['spUrl'][0][0]->__template_spUrl(array('c'=>'admin','a'=>'pro','mode'=>$_smarty_tpl->getVariable('mode')->value,'type'=>85),$_smarty_tpl);?>
">广告位</a>
		<a href="<?php echo $_smarty_tpl->smarty->registered_plugins[Smarty::PLUGIN_FUNCTION]['spUrl'][0][0]->__template_spUrl(array('c'=>'admin','a'=>'pro','mode'=>$_smarty_tpl->getVariable('mode')->value,'sh'=>'no'),$_smarty_tpl);?>
">未审核</a>
		<a href="<?php echo $_smarty_tpl->smarty->registered_plugins[Smarty::PLUGIN_FUNCTION]['spUrl'][0][0]->__template_spUrl(array('c'=>'admin','a'=>'pro','mode'=>$_smarty_tpl->getVariable('mode')->value,'sh'=>'ck2'),$_smarty_tpl);?>
">未通过</a>
		<a target="_blank" href="<?php echo $_smarty_tpl->smarty->registered_plugins[Smarty::PLUGIN_FUNCTION]['spUrl'][0][0]->__template_spUrl(array('c'=>'admin','a'=>'addpro','mode'=>$_smarty_tpl->getVariable('mode')->value),$_smarty_tpl);?>
">添加商品</a>
		<form action="" method="post">
			<input name="q" /><!--onpaste="value=value.replace(/[^\d]/gi,'');" onKeyUp="value=value.replace(/[^\d]/gi,'');"-->
			<input type="submit" value="搜索"/>
		</form>
	</div>
	<div class="admin-pro-around">
		<div class="admin-pro">
			<div class="pro-info">
				<ul>
					<li class="hd">
                                                <span class="short"><input type="checkbox" /></span>
						<span class="short">排序</span>
						<span class="pic">图片</span>
                                                <span class="title" style="width:80px;">分类</span>
						<span class="title">标题</span>
						<?php if ($_smarty_tpl->getVariable('mode')->value=='pro'){?>
						<span class="short">原价</span>
						<span class="short">促销价</span>
						<span class="short">销量</span>
						<?php }elseif($_smarty_tpl->getVariable('mode')->value=='try'){?>
						<span class="short">价值</span>
						<span class="short">概率</span>
						<span class="short">数量</span>
						<?php }?>
                                                <span class="short">佣金</span>
						<span class="title">操作</span>
                                                <span class="title" style="width:80px;">旺旺</span>
                                                <span class="title" style="width:80px;">推广</span>
                                                <span class="title" style="width:80px;">来源</span>
					</li>	
				<?php  $_smarty_tpl->tpl_vars['v'] = new Smarty_Variable;
 $_smarty_tpl->tpl_vars['k'] = new Smarty_Variable;
 $_from = $_smarty_tpl->getVariable('items')->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
if ($_smarty_tpl->_count($_from) > 0){
    foreach ($_from as $_smarty_tpl->tpl_vars['v']->key => $_smarty_tpl->tpl_vars['v']->value){
 $_smarty_tpl->tpl_vars['k']->value = $_smarty_tpl->tpl_vars['v']->key;
?>
					<li>
                                                <span class="short"><input type="checkbox" /></span>
						<span class="short"><?php echo $_smarty_tpl->tpl_vars['v']->value['rank'];?>
</span>
						<span class="pic"><img src="<?php echo $_smarty_tpl->tpl_vars['v']->value['pic'];?>
" /></span>
                                                <span class="title" style="width:80px;"><?php  $_smarty_tpl->tpl_vars['iv'] = new Smarty_Variable;
 $_smarty_tpl->tpl_vars['k'] = new Smarty_Variable;
 $_from = $_smarty_tpl->getVariable('procats')->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
if ($_smarty_tpl->_count($_from) > 0){
    foreach ($_from as $_smarty_tpl->tpl_vars['iv']->key => $_smarty_tpl->tpl_vars['iv']->value){
 $_smarty_tpl->tpl_vars['k']->value = $_smarty_tpl->tpl_vars['iv']->key;
?><?php if ($_smarty_tpl->tpl_vars['v']->value['cat']==$_smarty_tpl->tpl_vars['iv']->value['type']){?><?php echo $_smarty_tpl->tpl_vars['iv']->value['name'];?>
<?php }?><?php }} ?></span>
						<a title="<?php echo $_smarty_tpl->tpl_vars['v']->value['title'];?>
" target="_blank" href="<?php echo $_smarty_tpl->smarty->registered_plugins[Smarty::PLUGIN_FUNCTION]['spUrl'][0][0]->__template_spUrl(array('c'=>'main','a'=>'deal','id'=>$_smarty_tpl->tpl_vars['v']->value['id']),$_smarty_tpl);?>
">
							<span class="title"><?php echo $_smarty_tpl->tpl_vars['v']->value['title'];?>
</span>
						</a>
						<span class="short"><?php echo $_smarty_tpl->tpl_vars['v']->value['oprice'];?>
</span>
						<?php if ($_smarty_tpl->getVariable('mode')->value=='pro'){?>
						<span class="short"><?php echo $_smarty_tpl->tpl_vars['v']->value['nprice'];?>
</span>
						<?php }elseif($_smarty_tpl->getVariable('mode')->value=='try'){?>
						<span class="short"><?php echo $_smarty_tpl->tpl_vars['v']->value['gailv'];?>
</span>
						<?php }?>
						
						<span class="short"><?php echo $_smarty_tpl->tpl_vars['v']->value['volume'];?>
</span>
                                                <span class="short"><?php echo $_smarty_tpl->tpl_vars['v']->value['commission_rate'];?>
%</span>
						<span class="title">
                                                    <a target="_blank" href="<?php echo $_smarty_tpl->smarty->registered_plugins[Smarty::PLUGIN_FUNCTION]['spUrl'][0][0]->__template_spUrl(array('c'=>'admin','mode'=>$_smarty_tpl->getVariable('mode')->value,'a'=>'modpro','id'=>$_smarty_tpl->tpl_vars['v']->value['id']),$_smarty_tpl);?>
">修改</a>
                                                    <a target="_blank" href="<?php echo $_smarty_tpl->smarty->registered_plugins[Smarty::PLUGIN_FUNCTION]['spUrl'][0][0]->__template_spUrl(array('c'=>'admin','mode'=>$_smarty_tpl->getVariable('mode')->value,'a'=>'checkpro','id'=>$_smarty_tpl->tpl_vars['v']->value['id']),$_smarty_tpl);?>
">审核</a>
                                                    <a target="_blank" href="<?php echo $_smarty_tpl->smarty->registered_plugins[Smarty::PLUGIN_FUNCTION]['spUrl'][0][0]->__template_spUrl(array('c'=>'admin','mode'=>$_smarty_tpl->getVariable('mode')->value,'a'=>'xuqi','id'=>$_smarty_tpl->tpl_vars['v']->value['id']),$_smarty_tpl);?>
">续期</a>
                                                    <a target="_blank" href="<?php echo $_smarty_tpl->smarty->registered_plugins[Smarty::PLUGIN_FUNCTION]['spUrl'][0][0]->__template_spUrl(array('c'=>'admin','a'=>'delpro','mode'=>$_smarty_tpl->getVariable('mode')->value,'id'=>$_smarty_tpl->tpl_vars['v']->value['id']),$_smarty_tpl);?>
" onclick="return delfun()">删除</a>
						</span>
						
                                                <a title="<?php echo $_smarty_tpl->tpl_vars['v']->value['nick'];?>
" target="_blank" href="http://www.taobao.com/webww/ww.php?ver=3&touid=<?php echo smarty_modifier_escape($_smarty_tpl->tpl_vars['v']->value['nick'],'url');?>
&siteid=cntaobao&status=1&charset=utf-8" >
                                                    <span class="title" style="width:80px;"><?php echo $_smarty_tpl->tpl_vars['v']->value['nick'];?>
</span>
                                                </a>
                                                <span class="title" style="width:80px;">普通推广</span>
                                                <span class="title" style="width:80px;"><?php if ($_smarty_tpl->tpl_vars['v']->value['channel']==1){?>网站添加<?php }elseif($_smarty_tpl->tpl_vars['v']->value['channel']==2){?>商家报名<?php }?></span>
                                                
						
                                                
					</li>
					<?php if ($_smarty_tpl->tpl_vars['v']->value['ischeck']==2){?><div class="reason"><?php echo $_smarty_tpl->tpl_vars['v']->value['reason'];?>
</div><?php }?>
				<?php }} ?>
				</ul>
			</div>
			<div class="admin-page">
		    <?php if ($_smarty_tpl->getVariable('pager')->value){?>
			    共有商品<?php echo $_smarty_tpl->getVariable('pager')->value['total_count'];?>
个，共有<?php echo $_smarty_tpl->getVariable('pager')->value['total_page'];?>
页（每页<?php echo $_smarty_tpl->getVariable('pager')->value['page_size'];?>
个商品）：
			    <!--在当前页不是第一页的时候，显示前页和上一页-->
			    <?php if ($_smarty_tpl->getVariable('pager')->value['current_page']!=$_smarty_tpl->getVariable('pager')->value['first_page']){?>
				    <a href="<?php echo $_smarty_tpl->smarty->registered_plugins[Smarty::PLUGIN_FUNCTION]['spUrl'][0][0]->__template_spUrl(array('c'=>'admin','a'=>'pro','classification'=>$_smarty_tpl->getVariable('classification')->value,'type'=>$_smarty_tpl->getVariable('type')->value,'mode'=>$_smarty_tpl->getVariable('mode')->value,'sh'=>$_smarty_tpl->getVariable('sh')->value,'page'=>$_smarty_tpl->getVariable('pager')->value['first_page']),$_smarty_tpl);?>
">第一页</a> |
				    <a href="<?php echo $_smarty_tpl->smarty->registered_plugins[Smarty::PLUGIN_FUNCTION]['spUrl'][0][0]->__template_spUrl(array('c'=>'admin','a'=>'pro','classification'=>$_smarty_tpl->getVariable('classification')->value,'type'=>$_smarty_tpl->getVariable('type')->value,'mode'=>$_smarty_tpl->getVariable('mode')->value,'sh'=>$_smarty_tpl->getVariable('sh')->value,'page'=>$_smarty_tpl->getVariable('pager')->value['prev_page']),$_smarty_tpl);?>
">上一页</a> |
			    <?php }?>
			    <!--开始循环页码，同时如果循环到当前页则不显示链接-->
			    <?php  $_smarty_tpl->tpl_vars['thepage'] = new Smarty_Variable;
 $_from = $_smarty_tpl->getVariable('pager')->value['all_pages']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
if ($_smarty_tpl->_count($_from) > 0){
    foreach ($_from as $_smarty_tpl->tpl_vars['thepage']->key => $_smarty_tpl->tpl_vars['thepage']->value){
?>
			            <?php if ($_smarty_tpl->tpl_vars['thepage']->value!=$_smarty_tpl->getVariable('pager')->value['current_page']){?>
			            	<?php if ($_smarty_tpl->tpl_vars['thepage']->value<=$_smarty_tpl->getVariable('pager')->value['current_page']+8&&$_smarty_tpl->tpl_vars['thepage']->value>=$_smarty_tpl->getVariable('pager')->value['current_page']-2){?>
			                	<a href="<?php echo $_smarty_tpl->smarty->registered_plugins[Smarty::PLUGIN_FUNCTION]['spUrl'][0][0]->__template_spUrl(array('c'=>'admin','a'=>'pro','classification'=>$_smarty_tpl->getVariable('classification')->value,'type'=>$_smarty_tpl->getVariable('type')->value,'mode'=>$_smarty_tpl->getVariable('mode')->value,'sh'=>$_smarty_tpl->getVariable('sh')->value,'page'=>$_smarty_tpl->tpl_vars['thepage']->value),$_smarty_tpl);?>
"><?php echo $_smarty_tpl->tpl_vars['thepage']->value;?>
</a>
			            	<?php }?>									
			            <?php }else{ ?>
			            	<b><?php echo $_smarty_tpl->tpl_vars['thepage']->value;?>
</b>
			            <?php }?>
			    <?php }} ?>
			    <!--在当前页不是最后一页的时候，显示下一页和后页-->
			    <?php if ($_smarty_tpl->getVariable('pager')->value['current_page']!=$_smarty_tpl->getVariable('pager')->value['last_page']){?>
			    	|
				    <a href="<?php echo $_smarty_tpl->smarty->registered_plugins[Smarty::PLUGIN_FUNCTION]['spUrl'][0][0]->__template_spUrl(array('c'=>'admin','a'=>'pro','classification'=>$_smarty_tpl->getVariable('classification')->value,'type'=>$_smarty_tpl->getVariable('type')->value,'mode'=>$_smarty_tpl->getVariable('mode')->value,'sh'=>$_smarty_tpl->getVariable('sh')->value,'page'=>$_smarty_tpl->getVariable('pager')->value['next_page']),$_smarty_tpl);?>
">下一页</a> |
				    <a href="<?php echo $_smarty_tpl->smarty->registered_plugins[Smarty::PLUGIN_FUNCTION]['spUrl'][0][0]->__template_spUrl(array('c'=>'admin','a'=>'pro','classification'=>$_smarty_tpl->getVariable('classification')->value,'type'=>$_smarty_tpl->getVariable('type')->value,'mode'=>$_smarty_tpl->getVariable('mode')->value,'sh'=>$_smarty_tpl->getVariable('sh')->value,'page'=>$_smarty_tpl->getVariable('pager')->value['last_page']),$_smarty_tpl);?>
">尾页</a>
			    <?php }?>
			<?php }?>
		</div>
		</div>
	</div>
</body>
</html>