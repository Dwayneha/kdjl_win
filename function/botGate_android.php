<?php
/*
require_once('../config/config.game.php');
$user		= $_pm['user']->getUserById($_SESSION['id']);
$time = $user['bot_time'];
$fight_times = $time/20;	//����
$_bb = $_pm['user']->getUserPetByIdS($_SESSION['id'],$user['fightbb']);//ս�����
$id = 1;
$_sk		 = $_pm['user']->getUserPetSkillByIdS($_SESSION['id'],$_bb['id'],$id);
if($user['bot_map_id'] != 0)
{
	$mapData = $_pm['mysql']->getOneRecord("SELECT name,level FROM map WHERE id = '{$user['bot_map_id']}'");
	$levelArr = explode(",",$mapData['level']);
	$level1 = $levelArr[0];
	$level2 = $levelArr[1];
	$gpcData = $_pm['mysql']->getRecords("SELECT * FROM gpc WHERE level >= ".$level1." AND level < ".$level2." AND boss = 1");
	$gs = array();
	$rand_num = rand(0,count($gpcData)-1);
	$gs = $gpcData[$rand_num];
	if (is_array($_bb) && is_array($_sk) )
	{	
		// Componse array .
		$rs = array_merge($_bb,
		array(
				's_name'  => $_sk['name'],
				's_level' => $_sk['level'],
				's_vary'  => $_sk['vary'],
				's_wx'	  => $_sk['wx'],
				's_value' => $_sk['value'],
				's_plus'  => $_sk['plus'],
				's_uhp'   => $_sk['uhp'],
				's_ump'   => $_sk['ump'],
				's_imgeft'   => $_sk['img']
			 )
		);
	}
	//########################
	// ����װ�����Ե�ս���С�
	#############################
	$att = getzbAttrib($rs['id']);	
	$rs['ac']	+= $att['ac'];
	$rs['mc']	+= $att['mc'];
	$rs['hits'] += $att['hits'];
	$rs['speed']+= $att['speed'];
	$rs['miss']	+= $att['miss'];

	$aobj = new Ack($rs, $gs);
	$aobj -> getSkillAck();
	$myHurt = $aobj->skillack;		//�ҷ��˺�
	$aobj1 = new Ack1($gs, $rs);
	$aobj1 -> getSkillAck();
	$otherHurt = $aobj1->skillack;	//�з��˺�
	//���ﰤѪ
	$ghurt = $myHurt;
	//����Ѫ��
	$gall =  $gs['hp'];
	//�ҷ���Ѫ
	$myhurt =  $otherHurt;
	//�ҷ�Ѫ��
	$myall = $rs['srchp'];
	while($gall > 0 && $myall > 0)
	{
		$gall -= $ghurt;
		if($gall <= 0)
		{
			$fight_times = intval($fight_times*$myall/$rs['srchp']);
			break;
		}
		$myall -= $myhurt;
		if($myall <= 0)
		{
			$fight_times = 0;
			$myall = 0;
			break;
		}
	}
}


$time_str = '';


if($time > 24*60*60)
{
	$time_str .= intval($time/(24*60*60))."�� ";
	$time = $time%(24*60*60);
}
if($time > 60*60)
{
	$time_str .= intval($time/(60*60))."Сʱ ";
	$time = $time%(60*60);
}
if($time > 60)
{
	$time_str .= intval($time/(60))."�� ";
	$time = $time%(60);
}
$time_str .= $time%(60)."�� ";


$map_str = $mapData?$mapData['name']:"��";

$get_exp = 0;
$get_money = 0;
$gpcArr = array();
$dropArr = array();
$prpid = array();
$pidArr = array();
$itemDic = array();
$sendPropArr = array();

for($i=0;$i<$fight_times;$i++)
{
	$rand_num = rand(0,count($gpcData)-1);
	$gpcArr[$gpcData[$rand_num]['name']]++;
	$get_exp += $gpcData[$rand_num]['exps'];
	$get_money += $gpcData[$rand_num]['money'];
	$prpid[] = getProps($gpcData[$rand_num]['droplist']);
}
foreach($prpid as $info)
{
	if($info != '')
	{
		$midArr = explode(",",$info);
		for($i=0;$i<count($midArr);$i++)
		{
			if(!in_array($midArr[$i],$pidArr))
			{
				$pidArr[] = $midArr[$i];
			}
			$dropArr[$midArr[$i]]++;
			$sendPropArr[] = $midArr[$i];
		}
	}
}
if(count($pidArr) > 0 )
{
	$sql = "SELECT id,name FROM props WHERE id IN (".implode(",",$pidArr).")";
	$itemData = $_pm['mysql']->getRecords($sql);
	foreach($itemData as $info)
	{
		$itemDic[$info['id']] = $info['name'];
	}
}
$gpc_str = '<br />';
foreach($gpcArr as $key => $info)
{
	$name_length = strlen($key);
	$base_str = "";
	for($base=22;$base>$name_length;$base--)
	{
		$base_str .= " ";
	}
	$gpc_str .= "     <font color='#0099FF'>{$key}</font><font color='#990033'>{$base_str}�� {$info}</font><br />";
}


$str = "<strong>������ս������</strong><br /><br />";
$str .= "<font>�һ�ʱ�䣺</font><font color='#990033'>{$time_str}</font><br />";
$str .= "<font>�һ���ͼ��</font><font color='#990033'>{$map_str}</font><br />";
$str .= "<font>��ɱ���</font><font color='#990033'>{$gpc_str}</font><br />";
$str .= "<font>��þ��飺</font><font color='#990033'>{$get_exp}��</font><br />";
$str .= "<font>��ý�ң�</font><font color='#990033'>{$get_money}��</font><br />";
$str .= "<font>�����Ʒ��</font><br /><br />";
if(count($dropArr) > 0)
{
	foreach($dropArr as $key => $val)
	{
		$name = $itemDic[$key];
		$name_length = strlen($name);
		$base_str = "";
		for($base=22;$base>$name_length;$base--)
		{
			$base_str .= " ";
		}
		$str .= "     <font color='#0099FF'>{$name}</font><font color='#990033'>{$base_str}�� {$val}</font><br />";
	}
	$str .= "<br /><br />";
}
else
{
	$str .= "    ��";
}
$user['money'] += $get_money;
if ($user['money'] >= 1000000000)
{
	$user['money']=1000000000;
}
saveGetOther($rs, $get_exp);
saveGetPropsa(implode(",",$sendPropArr));
$sql = "UPDATE player SET money={$user['money']},bot_time=0,heart_time=".time()." WHERE id={$_SESSION['id']}";
$_pm['mysql']->query($sql);
*/
require_once('../config/config.game.php');
$mapData = $_pm['mysql']->getOneRecord("select contents from welcome where code='public' ");
$str=$mapData['contents'];

//��̬�ı�
$str2='����ݣ�<br>
1�����ڴ�����2��ս�������ֻ���¡��������!<br>
2��ѡ���ͼ�������߹һ����������ű������ϳ��ϵͳ��<br>
3�����ڴ�����2���ٷ�QQȺ��133169475���ٷ�Ψһ�ͷ�QQ��1066087792<br>
4���ڴ��³衾��Ů���������ߣ����Ⱥ�ļ��ٿơ�<br>
5�������-����������ÿ�콱����ԴԴ���ϣ�<br>
<span style="color:red;">���棺���μǲ����ܺ��Լ����ʺš����룬���������ʹ�á����ױ�ƭ����Ϊ��ɲ��ܵ�¼�����ŵ������һ���Լ����𣡵���������Ϸ��������ƭ����Ϊ����ң�һ����ʵ������ɱ���кţ�����˼��</span>';
echo "OK".iconv("gbk","utf-8",$str);
echo "<br><br><br><br>";
?>
