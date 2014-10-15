<!DOCTYPE html>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=gbk" />
</head>
<body>
    <?php
        $result = file_get_contents("http://d.qumai.org/tool/action.php?action=autologin&type=getall&iid=".$_GET['item_id']);
        $ptn = '/commissionRate(.+?)(\d+),/i';
	preg_match_all($ptn,$result,$arr);
        $commissionRate = $arr[2][0];
    ?>
    佣金：<span class="commission" style="color:red;font-size:22px;width:30px;"><?php if($commissionRate){ echo $commissionRate; } ?></span> %<br/>
</body>
</html>