<?php


/**************************
ƽ̨���Է��

locktime   ʱ�䣨���ӣ�

roleName   ��ɫ��

ServerUrl  url  urlencode����

type       ����==1    ���==2





return 

1001 ���Ʒ���
1002 ���ܴ�
1003 û�������
1004 û������û�
1005 ���ݴ�
1006 ʧ��
1000 �ɹ�
****************************/

/*IP����*/


/**
locktime   ʱ�䣨���ӣ�

roleName   ��ɫ��

ServerUrl  url   urlencode����

type       ����==1  ���==2
**/

//http://xjtest1.webgame.com.cn/api/Gamemaster.php?locktime=
$locktime=$_GET['locktime'];
$roleName=iconv('utf-8','gbk',$_GET['roleName']);
$ServerUrl=$_GET['ServerUrl'];
$type=$_GET['type'];
$key=$_GET['key'];
$userid = intval($_GET['userid']);






if(!is_numeric($locktime)||empty($roleName)||empty($ServerUrl)||!is_numeric($type)||empty($locktime)||empty($type)||!is_numeric($userid)){
	die('1005');
}

//die($locktime.$roleName.$ServerUrl.$type.'315sab');
$sgin = md5($locktime.$_GET['roleName'].$ServerUrl.$type.'315sab');

if($key!=$sgin){
	die("1002");
}


//$roleName1 = urlencode($roleName);
//$sql="select p_id from player where p_name='{$roleName}'";

//$player=$db->query_first($sql);

//if(empty($player['p_id'])){
//	die("1004");
//}
require_once('../config/config.game.php');
$players = $_pm['mysql'] -> getOneRecord("SELECT * FROM player WHERE id = $userid");
if(!is_array($players)){
	die('1004');
}
$nickname = $roleName;

require_once('../kernel/socketmsg.v1.php');
require_once(dirname(__FILE__).'/../socketChat/config.chat.php');
$s=new socketmsg();


if($type==1){
	/*$msg_key = 'chatMsgList';
	$nowMsgList = unserialize($_pm['mem']->get($msg_key));
	$arr = split('linend', $nowMsgList);
	if( count($arr)>20 ) // cear old
	{
		$arrt = array_shift($arr);
	}*/
	if($players['password'] > 0){
		die('1007');
	}
	if($locktime=='-1'){//���ý���
		$time = time() + 10 * 365 * 12 * 3600;
		$_pm['mysql']->query("update player set password='{$time}' where id={$players['id']}");
		$result = mysql_affected_rows($_pm['mysql'] -> getConn());
		if($result != 1){
			exit('1006');
		}else{
			echo '1000';
		}
		$old = unserialize($_pm['mem']->get($players['id']));
		$old['password']=1;
		$_pm['mem']->set(array('k'=> $players['id'], 'v'=> $old));
		$s->sendMsg($players['id'].'|YZ');
		//$newstr = '<font color=red>[ϵͳ����]�콵���ף������&nbsp;'.$nickname.'&nbsp;������������룬&nbsp;'.$nickname.'&nbsp;����ʧȥ��˵����Ȩ����';
	}else if($locktime>0){
		$time = time() + $locktime * 60;
		$hour = $locktime/60;
		$_pm['mysql']->query("update player set password='{$time}' where id={$players['id']}");
		$result = mysql_affected_rows($_pm['mysql'] -> getConn());
		if($result != 1){
			exit('1006');
		}else{
			echo '1000';
		}
		$old = unserialize($_pm['mem']->get($players['id']));
		$old['password']=1;
		$_pm['mem']->set(array('k'=> $players['id'], 'v'=> $old));
		$s->sendMsg($players['id'].'|JY');
		//$newstr = '<font color=red>[ϵͳ����]'. $nickname . ' ��ΪΥ���������壬����Ӣ������˼����˼��'.$hour.'Сʱ��';
	}	
	/*foreach($arr as $k=>$v)
	{
		$retstr .= $v.'linend';
	}

	$retstr = $retstr.$newstr;
	$_pm['mem']->set( array('k'=>$msg_key, 'v'=>$retstr) ); // default ten min.

	//----------------------------------------------------------------------------------------------------------------------
	$_olddata = @unserialize($_pm['mem']->get('ttmt_data_notice'));
	
	$swfData = iconv('gbk','utf-8',$newstr);
	$_olddata['an'] = isset($_olddata['an'])?$_olddata['an']."\n".$swfData:$swfData;
	$_pm['mem']->set(array('k'=>'ttmt_data_notice','v'=>$_olddata));*/
}else if($type == 2){//���
	if($players['password'] == 1){
		die('1007');
	}
	$_pm['mysql']->query("UPDATE player set secid=1 WHERE id={$players['id']}");
	$result = mysql_affected_rows($_pm['mysql'] -> getConn());
	if($result != 1){
		exit('1006');
	}else{
		echo '1000';
	}
	$_pm['mem']->set(array('k'=>$players['id'] . 'chat', 'v'=>0)); // ������
	$_pm['mem']->del($players['id']);
	$s->sendMsg($players['id'].'|FH');
}