<?php
/**
*@Version: %version%
*@Copyright: %copyright%
*@Author: %author%

*@Write Date: 2008.05.01
*@Update Date: 2009.01.6
*@Usage: King
*@Note: none
*/
require_once('../config/config.game.php');

secStart($_pm['mem']);


$user	 = $_pm['user']->getUserById($_SESSION['id']);
//Word part.
$taskword= taskcheck($user['task'],6);
$props = unserialize($_pm['mem']->get(MEM_PROPS_KEY));
$_gpc = unserialize($_pm['mem']->get(MEM_GPC_KEY));
$taskitem = $task[$user['task']];
/*$taskitem	= $_pm['mem']->dataGet(array('k'	=>	MEM_TASK_KEY,
										 'v'	=>	"if(\$rs['id']== '{$user['task']}') \$ret=\$rs;"
									));*/




$m = $_pm['mem'];
$taskArr = array();
$rwlidarr = array();



//֪ʶ�ʴ�
$timearr1 = unserialize($_pm['mem']->get(MEM_TIMENEW_KEY));
$timearr = $timearr1['dati'];
foreach($timearr as $k => $v)
{
	$dayarr = explode("-",$v['days']);
}

$taskword= taskcheck($user['task'],6);

$rs = $_pm['mysql']->getOneRecord("SELECT times, result,oksum
									 FROM aoyun_player
									WHERE uid={$_SESSION['id']}
								 ");
if (is_array($rs) && $rs['times']>0 && $rs['result']==1)	//�����콱���
{
	// in here add time limit.
	$active="style='cursor:pointer;'";
}
else $active='';

$welcome = memContent2Arr("db_welcome",'code');

$a = $welcome['dati']['contents'];
if(empty($a))
{
	$rs = $_pm['mysql']->getOneRecord("SELECT contents from welcome where code='dati'");
	$a = $rs['contents'];
}

if(empty($a))
{
	$a	="����ݣ����ٷ���վ֪ͨ��";
}

//�ճ�����   872:1,871:2|872,1;871,2|20100917:1*20,2*30;20101001:5*20,6*30
$uarr = array();
$now = date('Ymd');
$mempropsid = unserialize($_pm['mem']->get('db_propsid'));
$u = $_pm['mysql'] -> getOneRecord('SELECT prize_every_day FROM player_ext WHERE uid = '.$_SESSION['id']);
$uarr = explode('|',$u['prize_every_day']);
$prize_str = $welcome['holiday_prize']['contents'];
$arr = explode('|',$prize_str);
if($arr[0] == 0){//�ճ�����
	$dayprizeflag = 2;//��δ����
}else{
	if($uarr[0] < $now){
		$dayprizeflag = 1;//��δ��ȡ
	}else{
		$dayprizeflag = 0;//�Ѿ���ȡ
	}
	//�õ����õĽ�����Ʒ
	$row = explode(',',$arr[0]);
	foreach($row as $rv){
		$res = explode(':',$rv);
		$dayprizestr .= '<br /><img src="../images/ui/bag/'.$mempropsid[$res[0]]['varyname'].'.gif" border="0" width="20" height="20"/><span class="text02">'.$mempropsid[$res[0]]['name'].'x'.$res[1].'</span>';
	}
	$dayprizestr = substr($dayprizestr,6);
}

if($arr[1] == 0){//��ĩ����
	$weekprizeflag = 2;//��δ����
}else{
	$week = date('w');
	if($week != 0 && $week != 6){
		$weekprizeflag = 3;//������ĩ
	}else{
		if($week == 0){//������
			$yes = date("Ymd", strtotime("1 days ago"));//��Ҫ�ж�����Ҳû����ȡ
			if($uarr[1] < $yes){
				$weekprizeflag = 1;//��δ��ȡ
			}else{
				$weekprizeflag = 0;//�Ѿ���ȡ
			}
		}else{
			if($uarr[1] < $now){
				$weekprizeflag = 1;//��δ��ȡ
			}else{
				$weekprizeflag = 0;//�Ѿ���ȡ
			}
		}
	}
	//�õ����õĽ�����Ʒ
	$row = explode(',',$arr[1]);
	foreach($row as $rv){
		$res = explode(':',$rv);
		$weekprizestr .= '<br /><img src="../images/ui/bag/'.$mempropsid[$res[0]]['varyname'].'.gif" border="0" width="20" height="20"/><span class="text02">'.$mempropsid[$res[0]]['name'].'x'.$res[1].'</span>';
	}
	$weekprizestr = substr($weekprizestr,6);
}

//�ڼ��ս���
$harr = explode(';',$arr[2]);//20100917:1*20,2*30;20101001:5*20,6*30
$holidayprizeflag = 2;
if(is_array($harr)){
	foreach($harr as $hv){
		$row = explode(':',$hv);
		if($now == $row[0]){//�ǽڼ���
			if($uarr[2] == $row[0]){
				$holidayprizeflag = 0;//�Ѿ���ȡ
			}else{
				$holidayprizeflag = 1;//��δ��ȡ
			}
			//�õ����õĽ�����Ʒ
			$rs = explode(',',$row[1]);
			foreach($rs as $rv){
				$res = explode('*',$rv);
				$holidayprizestr .= '<br /><img src="../images/ui/bag/'.$mempropsid[$res[0]]['varyname'].'.gif" border="0" width="20" height="20"/><span class="text02">'.$mempropsid[$res[0]]['name'].'x'.$res[1].'</span>';
			}
			$holidayprizestr = substr($holidayprizestr,6);
			break;
		}
	}
	
}else{
	$holidayprizeflag = 2;
}

$sql = " SELECT userbag.sums,props.name FROM userbag,props WHERE userbag.uid = {$_SESSION[id] } AND props.name IN ('��ȯ','����ȯ','ͭ��ȯ') AND userbag.pid = props.id AND userbag.sums > 0 ";
$res_choose = $_pm['mysql'] -> getRecords($sql);
if( is_array($res_choose) )
{
	foreach( $res_choose as  $info )
	{
		switch( $info['name'] )
		{
			case '��ȯ' :
			{
				$golden_num = $info['sums'];
				break;
			}
			case '����ȯ' :
			{
				$silver_num = $info['sums'];
				break;
			}
			case 'ͭ��ȯ' :
			{
				$copper_num = $info['sums'];
				break;
			}
		}
	}
}
if( !isset($golden_num) )
{
	$golden_num = 0;
}
if( !isset($silver_num) )
{
	$silver_num = 0;
}
if( !isset($copper_num) )
{
	$copper_num = 0;
}
//@Load template.
$tn = $_game['template'] . 'tpl_king.html';
if (file_exists($tn))
{
	$tpl = @file_get_contents($tn);
	
	$src = array(
				 '#word#',
				 '#active#',
				 '#oksum#',
				 '#anounce_msg#',
				 '#prestige#',
				 '#jprestige#',
				 '#dayprizestr#',
				 '#weekprizestr#',
				 '#holidayprizestr#',
				 '#dayprizeflag#',
				 '#weekprizeflag#',
				 '#holidayprizeflag#',
				 '#golden_num#',
				 '#silver_num#',
				 '#copper_num#'
				);
	$des = array(
				 $taskword,
				 $active,
				 $rs['oksum'],
				 $a	,
				 $user['prestige'],
				 $user['jprestige'],
				 $dayprizestr,
				 $weekprizestr,
				 $holidayprizestr,
				 $dayprizeflag,
				 $weekprizeflag,
				 $holidayprizeflag,
				 $golden_num,
				 $silver_num,
				 $copper_num
				);
	$king = str_replace($src, $des, $tpl);
}
// gzip echo. if maybe.
ob_start('ob_gzip');
echo $king;
ob_end_flush();
?>