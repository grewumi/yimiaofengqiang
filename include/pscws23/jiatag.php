<?php
/* ----------------------------------------------------------------------- *\
   PHP-�������ķִ� (SCWS) ver 3.1/2.1 (ʵ����ʾ)
   
   (v2) ���ڴ�Ƶ�ʵ�����������,
   (v3) ˫����ݴ�Ƶȡ�ϸ�֮�ַ�

   �������汾���÷��� API һ��.
   $Id: demo.php,v 1.2 2008/12/20 12:18:15 hightman Exp $

   -----------------------------------------------------------------------
   ����: ������(hightman) (MSN: MingL_Mar@msn.com) (php-QQȺ: 17708754)
   ��վ: http://www.ftphp.com/scws
   ʱ��: 2006/03/05
   �޶�: 2008/12/20
   Ŀ��: ѧϰ�о�������, ϣ���кõĽ��鼰��;ϣ���ܽ�һ������.
   -----------------------------------------------------------------------
   ����: PHP 4.1.0�����߰汾�� PHP5 (���뽨�� --enable-dba --with-[cdb|gdbm])
\* ----------------------------------------------------------------------- */

/**
 * �鿴Դ��Ĳ��� <*.php?source>
 */

$stag = 'source';
$slen = strlen($stag);
if (isset($_SERVER['QUERY_STRING']) 
	&& !strncmp($_SERVER['QUERY_STRING'], $stag, $slen))
{
	$qlen = strlen($_SERVER['QUERY_STRING']);	
	$files = array('pscws2', 'pscws3', 'dict', 'xdb_r');
	$file = ($qlen > $slen && $qlen < ($slen + count($files))) ? $files[$qlen-$slen] . '.class.php' : __FILE__;
	highlight_file($file);
	exit(0);
}

/**
 * ʵ����ʼ
 */

// ���Լ���ʵ������ʱ��
function get_microtime()
{
	list($usec, $sec) = explode(' ', microtime()); 
	return ((float)$usec + (float)$sec); 
} 

$time_start = get_microtime();

// ʵ����ǰ�Ĳ���ָ�����ȡ
$dict = 'dict/dict.xdb';	// Ĭ�ϲ��� xdb (���������κ�����)
$mydata  = NULL;	// ��������
$version = 3;		// ���ð汾
$autodis = false;	// �Ƿ�ʶ������
$ignore  = false;	// �Ƿ���Ա��
$debug   = false;	// �Ƿ�Ϊ����ģʽ
$stats	 = false;	// �Ƿ�鿴ͳ�ƽ��
$is_cli  = (php_sapi_name() == 'cli');	// �Ƿ�Ϊ cli ���л���
$sample_text = <<<__EOF__
�¿��貢���ǡ��޼�����Ψһ����Ȩ�ˣ�һ����Ӱ�������Ȩ���Ӱ��Ƭ�����С�

һ����Ӱ�����߰������ݡ���Ӱ�����ȴ�����Ա����Щ������Ա�����ǵĴ������а�Ȩ�ġ���������Ƭ����Ȩ�������˲��ܶԵ�Ӱ�����������С���ӳ������ͨ���������������Ȳ��ܰѵ�Ӱ�ı��С˵��������������������ʽ����Ҳ���ܰ�һ������Сʱ���ܷ���ĵ�Ӱ�ı�ɰ��Сʱ���ܷ���Ķ�Ƭ��

����Ȩ�Ͱ�Ȩ���ҹ���ͬһ������Ƿ��ɸ�����Ʒ�����ߵ�ר��Ȩ������νר��Ȩ������û�о���Ȩ��������ֲ��Ƿ��ɹ涨�����⣬Ҫʹ�������Ʒ���ͱ��뾭��������Ȩ��û����Ȩ������Ȩ��
__EOF__;

// ���ݲ�ͬ�汾�Ļ�����ȡ��������
if ($is_cli)
{       
	$argc = $_SERVER['argc'];
	for ($i = 1; $i < $argc; $i++)
	{
		$optarg = $_SERVER['argv'][$i];
		if (!strncmp($optarg, "--", 2))
		{
			$cmp = substr($optarg, 2);
			if (!strcasecmp($cmp, "help"))
			{
				$mydata = NULL;
				break;
			}
			else if (!strcasecmp($cmp, "autodis"))
				$autodis = true;
			else if (!strcasecmp($cmp, "ignore"))
				$ignore = true;
			else if (!strcasecmp($cmp, "v2"))
				$version = 2;
			else if (!strcasecmp($cmp, "debug"))
				$debug = true;
			else if (!strcasecmp($cmp, "stats"))
				$stats = true;
			else if (!strcasecmp($cmp, "dict"))
			{
				$i++;
				$dict = $_SERVER['argv'][$i];
			}
		}
		else if (is_null($mydata))
		{
			if (is_file($optarg)) $mydata = @file_get_contents($optarg);
			else $mydata = trim($optarg);
		}
	}
}
else
{       
	// ���ֲ���ѡ��
	$checked_ignore = $checked_autodis = $checked_v2 = '';
	
	// �Ƿ�ָ���е� 2 ��
	if (isset($_REQUEST['version']) && $_REQUEST['version'] == 2)
	{
		$version = 2;
		$checked_v2 = ' selected';
	}
        
	// �Ƿ�ָ��һ���ʵ��ʽ
	$selected_gdbm = $selected_text = $selected_sqlite = '';
	if (isset($_REQUEST['dict']))
	{
		if ($_REQUEST['dict'] == 'gdbm')
		{
			$dict = 'dict/dict.gdbm';
			$selected_gdbm = ' selected';
		}
		else if ($_REQUEST['dict'] == 'text')
		{
			$dict = 'dict/dict.txt';
			$selected_text = ' selected';
		}
		else if ($_REQUEST['dict'] == 'sqlite')
		{
			$dict = 'dict/dict.sqlite';
			$selected_sqlite = ' selected';
		}
		else if ($_REQUEST['dict'] == 'cdb')
		{
			$dict = 'dict/dict.cdb';
			$selected_cdb = ' selected';
		}
		else
		{
			$_REQUEST['dict'] = 'xdb';
		}
	}

	// �Ƿ�������ʶ�� (ȱʡ�ر�)
	if (isset($_REQUEST['autodis']) && !strcmp($_REQUEST['autodis'], 'yes'))
	{
		$autodis = true;
		$checked_autodis = ' checked';
	}

	// �Ƿ����������
	if (isset($_REQUEST['ignore']) && !strcmp($_REQUEST['ignore'], 'yes'))
	{
		$ignore = true;
		$checked_ignore = ' checked';
	}

	// �Ƿ���debug
	if (isset($_REQUEST['debug']) && !strcmp($_REQUEST['debug'], 'yes'))
	{
		$debug = true;
		$checked_debug = ' checked';
	}

	// �Ƿ�ͳ�Ʊ�
	if (isset($_REQUEST['stats']) && !strcmp($_REQUEST['stats'], 'yes'))
	{
		$stats = true;
		$checked_stats = ' checked';
	}

	// �з�����
	if (!isset($_REQUEST['mydata']) || empty($_REQUEST['mydata']))
	{
		$mydata = $sample_text;
	}
	else
	{
		$mydata = & $_REQUEST['mydata'];
		if (get_magic_quotes_gpc())
			$mydata = stripslashes($mydata);
	}
}
header("Content-type: text/html; charset=gbk");
$mydata = $_POST['title'];
if(!$mydata){
    $mydata = urldecode($_GET['title']);
}
$encode = mb_detect_encoding($mydata, array("ASCII","UTF-8","GB2312","GBK","BIG5")); 
if($encode == 'UTF-8'){
    $mydata = iconv('utf-8','gb2312',$mydata);
}
//echo $mydata;
//js��������
$js = $_POST['js'];
//$mydata = "Ӥ������� ��ˮ����͸��������ϴ�¾��洿����������Ʒ ��������";
//$js = 1;

if($mydata){
    // ������� \r\n\t
    if (!is_null($mydata)) 
            $mydata = trim($mydata);

    // ʵ�����ִʶ���(mydata�ǿ�)
    $object = 'PSCWS' . $version;
    require (strtolower($object) . '.class.php');

    $cws = new $object($dict);
    $cws->set_ignore_mark($ignore);
    $cws->set_autodis($autodis);
    $cws->set_debug($debug);
    // hightman.060330: ǿ�п���ͳ��
    $cws->set_statistics($stats);
    // ִ���з�, �ִʽ������ִ�� words_cb()
    $tags = array_unique($cws->segment($mydata));
    
}
if($js){
    foreach($tags as $v){
        $tag_js .= $v.' ';
    }
    echo '{"tags":"'.trim($tag_js).'"}';
}else{
    foreach($tags as &$v){
        $v = iconv('gb2312','utf-8',$v);
    }
    echo json_encode($tags);
}

?>

