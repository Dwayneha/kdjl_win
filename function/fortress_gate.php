<?php
/**
*/
require_once('../config/config.game.php');
require_once('../sec/dblock_fun.php');
header('Content-Type:text/html;charset=GBK');

function msg($m,$js='')
{
	realseLock();
	die('parent.Alert("'.$m.'");'.$js);
}
$a = getLock($_SESSION['id']);
if(!is_array($a)){
	msg('��������æ�����Ժ����ԣ�');
}
secStart($_pm['mem']);

$petsarr	= $_pm['user']->getUserPetById($_SESSION['id']);

$_SESSION['exptype'.$_SESSION['id']] = "";
if($_SESSION['way'.$_SESSION['id']] == "" || $_SESSION['way'.$_SESSION['id']] == "money")
{
	$num = $user['sysautosum'];
}
else if($_SESSION['way'.$_SESSION['id']] == "yb")
{
	$num = $user['maxautofitsum'];
}
$_pm['mysql']->query("UPDATE player
					     SET autofitflag=0
					   WHERE id={$_SESSION['id']}
					");

$kk=0;
$selid=0;
$sk=1;
$mbczl=0;
$bid=abs($_GET['bid']);
$mbid=0;
if (is_array($petsarr))
{
	foreach ($petsarr as $k =>$rs) // Will filter in muchang pets for current user.
	{
		if($rs['muchang'] != 0){
			continue;
		}
		if($rs['id'] == $bid)
		{
			$sel  = 100;
			$selid=$rs['id'];
			$sk   =$kk;
			$mbczl=$rs['czl'];
			$mbid=$bid;
		}
		else
		{
			$sel = 50;
		}
		if($rs['level']==0) $rs['level']=1;
		$pets[$kk++] = "<img src='".IMAGE_SRC_URL."/bb/{$rs['cardimg']}' onClick=\"Setbbs(".$kk.",".$rs['id'].");\" alt=\"{$rs['name']}\" style='cursor:pointer;filter:alpha(opacity={$sel});' id='i{$kk}'> ";
		if ($kk==3) break;
	}
}

if(!$mbid)
{
	msg('��ѡ��һ�����ĳ��');
}
$setting = $_pm['mem']->get('db_welcome1');
if(!is_array($setting)) $setting=unserialize($setting);
if(!is_array($setting))
{
	msg('��̨�������ݶ�ȡʧ��(1)��'.print_r($setting,1));
}
if(!isset($setting['fortress']))
{
	msg('ȱ�ٻ�����趨(fortress)��');
}

if(!isset($setting['fortress_time']))
{
	msg('ȱ�ٻ�����趨(fortress_time)��');
}
$table_name="`fortress_users_".date("Ymd")."`";
$user_in=$_pm['mysql']->getOneRecord('select user_id from '.$table_name.' where user_id='.$_SESSION['id']);

$time_settings=explode("\r\n",$setting['fortress_time']);
$w=date('w');
$hm=date('His');
if($w==0)
{
	$w=7;
}
$time_flag=false;
foreach($time_settings as $s)
{
	$tmp=explode(',',$s);
	//1,210000,210459,212959,213459
	if($w==$tmp[0])
	{
		if(
		($hm>=$tmp[1]&&$hm<=$tmp[4]&&$user_in)
		||
		($hm>=$tmp[1]&&$hm<=$tmp[2])		
		)
		{		
			$time_flag=true;
		}
		if($hm>$tmp[4])
		{
			msg('����ֻ�ܲ鿴���У�<br/><font color=#ff0000>ϵͳû�п۳����ĵ��ߺͽ�ң�</font>','window.location="/function/fortress_stolen_Mod.php";');
		}
		break;
	}
}

if(!$time_flag){
	msg('���ڲ���Ҫ������ʱ�䣡');
}

$set=explode("\r\n",$setting['fortress']);
$sqls_remove_item=array();

foreach($set as $k=>$s)
{
	$tmp=explode(',',$s);
	$tmp0=explode('-',$tmp[0]);//������Ҫ�ĳɳ�
	$tmp1=explode('|',$tmp[1]);//������Ҫ�Ķ���	

	if($mbczl>=$tmp0[0]&&$mbczl<=$tmp0[1])
	{
		$user=$_pm['mysql']->getOneRecord('select money from player where id='.$_SESSION['id']);
		if($user['money']<$tmp[2])
		{
			msg("�����Ϸ�Ҳ��㣬�޷�����Ҫ����");
		}

		$_pm['mysql']->query('update player set money='.($user['money']-$tmp[2]).',mbid='.$mbid.' where id='.$_SESSION['id']);

		foreach($tmp1 as $t)
		{
			$it_need_setting=explode(':',$t);
			$tmp1_str.=$props[$tt[0]]['name'].' '.$tt[1].'��,';

			$row=$_pm['mysql']->getOneRecord('select id,sums from userbag where uid='.$_SESSION['id'].' and sums>='.$it_need_setting[1].' and pid='.$it_need_setting[0]);
			if(!$row)
			{
				$_pm['mysql']->query('rollback');
				msg("��Ҫ����Ʒ���㲻�ܽ���!");
			}
			$_pm['mysql']->query('select * from userbag where id='.$row['id'].' for update');
			$sqls_remove_item[]='update userbag set sums=sums - '.$it_need_setting[1].' where id='.$row['id'].' and sums >='.$it_need_setting[1];
		}

		foreach($sqls_remove_item as $sql)
		{
			$_pm['mysql']->query($sql);
			if(mysql_affected_rows($_pm['mysql'] -> getConn()) != 1)
			{
				$_pm['mysql']->query('rollback');
				msg('ϵͳ��æ�����Ժ������');
			}
		}
		$sql_create_today="CREATE TABLE ".$table_name." (
  `user_id` int(10) NULL DEFAULT NULL,
  `bb_id` int(10) NULL DEFAULT NULL,
  `cur_gpc_id` int(10) NULL DEFAULT NULL,
  `at_section_num` tinyint(2) NULL DEFAULT NULL COMMENT '�ɳ��׶���',
  `nickname` varchar(32) NULL DEFAULT NULL,
  `v_times` smallint(6) NULL DEFAULT 0  COMMENT 'ʤ������',
  `f_times` smallint(6) NULL DEFAULT 0 COMMENT 'ʧ�ܴ���',
  `fv_result` smallint(6) NULL DEFAULT 0 COMMENT '��ǰʤ��ʧ�ܼ������',
  `score` smallint(6) NULL DEFAULT 0 COMMENT '����',
  `score_final` smallint(6) NULL DEFAULT 0 COMMENT '͵ȡ֮��Ļ���',
  PRIMARY KEY (`user_id`)
) ENGINE=InnoDB COMMENT='Ů��Ҫ��';";
		$_pm['mysql']->query($sql_create_today);

		$sql_join='insert into '.$table_name.' set user_id='.$_SESSION['id'].',nickname="'.$_SESSION['nickname'].'",bb_id='.$mbid.',at_section_num='.($k+1).' on duplicate key update nickname="'.$_SESSION['nickname'].'"';
		
		$_pm['mysql']->query($sql_join);
		if(mysql_error()){
			msg($sql_join.'<br />'.mysql_error());
		}
		$_SESSION['fortress_pass']=1;
		msg('����ɹ�,��;�˳�,�ٴν��뽫���ٴο۳����ߺͽ��,<br/>�ٴν��벻������������ı���ȣ����ı���֣�','window.location="/function/fortressCard_Mod.php";');
	}
}
msg('û���ʺ��������Ҫ����������ĳɳ���'.$mbczl.'�������趨�ķ�Χ�ڡ�');
?>