<?php
/**
*@Version: %version%
*@Copyright: %copyright%
*@Author: %author%

*@Write Date: 2008.05.01
*@Update Date: 2008.05.22
*@Usage: ����ϳ�ϵͳ
         �������̣����Я���ĳ����У�����ѡ����������ѡ����Ҫ��Ӱ��ĵ��ߺ󣬼��ɿ�ʼ�ϳɡ�
*@Note: none
*/
require_once('../config/config.game.php');

secStart($_pm['mem']);

$user		= $_pm['user']->getUserById($_SESSION['id']);
$petsAll	= $_pm['user']->getUserPetById($_SESSION['id']);
$bag		= $_pm['user']->getUserBagById($_SESSION['id']);
/*if($user['name'] != 'tanwei2008' && $user['name'] != 'boss')
{
die("ά���У���ع�����һ����");
}*/
//die("ά���У���ع�����һ����");
if (is_array($petsAll))
{  
	$zskk=0;
	foreach ($petsAll as $k => $rs)
	{
		if($rs['level'] >= 60 && ($rs['name'] == "�����ޣ�����" || $rs['name'] == "�����ޣ��磩" || $rs['name'] == "�����ޣ�î��") && $rs['muchang'] == 0)
		{
			$zsoption .= "<option value='{$rs['id']}'>{$rs['name']}-{$rs['level']}</option>\n";
		}
		if ($rs['muchang'] == 1 || $rs['level']<60 || $rs['wx'] != 6 || $rs['name'] == "�����ޣ�����" || $rs['name'] == "�����ޣ��磩" || $rs['name'] == "�����ޣ�î��") continue;
		$zspets[$zskk++] = "<img src=''.IMAGE_SRC_URL.'/bb/{$rs['cardimg']}' onclick='Display({$rs['id']});' style='cursor:pointer;display:none;' id='cp{$zskk}'>";
		$zsapetslist .= "<option value='{$rs['id']}'>{$rs['name']}-{$rs['level']}</option>\n";
		$zsbblistid .= $bblistid?",'{$rs['id']}-{$rs['cardimg']}'":"'{$rs['id']}-{$rs['cardimg']}'";
		if ($zskk == 3) break;
	}
}

/**
*@Get Bag.
*/
if (is_array($bag))
{
	$i = 0;
	foreach($bag as $k => $v)
	{
		$effarr = explode(":",$v['usages']);
		if($effarr[0] != '����')
		{
			continue;
		}
		// Get money;
		// effect format: luck:B:10%:5000, shbb:5000
		if(!empty($v['sums']))
		{
			$zsplist .= "<option value='{$v['id']}'>{$v['name']}-{$v['sums']}��</option>\n";
		}
	}
}


//Word part.
$taskword= taskcheck($user['task'], 2);
$_pm['mem']->memClose();


//@Load template.
$tn = $_game['template'] . 'tpl_zs.html';
if (file_exists($tn))
{
	$tpl = @file_get_contents($tn);
	
	$src = array("#word#",
				 "#zsone#",
				 "#zstwo#",
				 "#zsapetslist#",
				 "#zsbpetslist#",
				 "#zswupinone#",
				 "#zsbballid#",
				 "#zsoptions#"
				);
	$des = array($taskword,
				 $zspets[0],
				 $zspets[1],
				 $zsapetslist,
				 $zsapetslist,
				 $zsplist,
				 $zsbblistid,
				 $zsoption
				);
	$shop = str_replace($src, $des, $tpl);
}
// gzip echo. if maybe.
ob_start('ob_gzip');
echo $shop;
ob_end_flush();

// Get props name for pid.
// @return: false or String.
function getPropsName($pid)
{
	global $_pm;
	/*$rs = $_pm['mem']->dataGet(array('k' => MEM_PROPS_KEY,
							'v' => "if(\$rs['id'] == {$pid}) \$ret=\$rs;"
						));*/
	$mempropsid = unserialize($_pm['mem']->get('db_propsid'));
	$rs = $mempropsid[$pid];
	if (is_array($rs)) return $rs['name'];
	else return false;	
}
?>