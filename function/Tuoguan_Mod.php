<?php
/**
*@Version: %version%
*@Copyright: %copyright%
*@Author: %author%

*@Write Date: 2008.09.25
*@Update Date: 
*@Usage: �����й�
*@Note: none
*/
require_once('../config/config.game.php');
secStart($_pm['mem']);

$user	 = $_pm['user']->getUserById($_SESSION['id']);
$petsAll  = $_pm['user']->getUserPetById($_SESSION['id']);
//�����йܵ������Ŀ
$tgnum = $user['tgmax'];
//�õ��������õĳ���
$utime = $user['tgtime'];
$petsoption .= '<option value="">��ѡ�����</option>';
foreach($petsAll as $pall)
{
	if($pall['level'] <= 10 || $pall['muchang'] != 1)
	{
		continue;
	}
	$petsoption .= '<option value='.$pall["id"].'>'.$pall['name'].'</option>';
}
$option1 = '<select name="select5" id="pets1" onchange="getflag(this)">'.$petsoption.'</select>';
$option2 = '<select name="select4" id="pets2" onchange="getflag(this)">'.$petsoption.'</select>';
$option3 = '<select name="select"  id="pets3" onchange="getflag(this)">'.$petsoption.'</select>';
$flag1 = "";
$flag2 = "";
$flag3 = "";

//�й�ʱ��
$timestr = "1,2,4,8,10";
$timearr = explode(",",$timestr);
foreach($timearr as $t)
{
	$timeoption .= '<option value='.$t.'>'.$t.'Сʱ</option>';
}
$time1 = '<select name="select2" id="time1" >'.$timeoption.'</select>';
$time2 = '<select name="select20" id="time2">'.$timeoption.'</select>';
$time3 = ' <select name="select2" id="time3">'.$timeoption.'</select>';

//�й�����
$messtr = "1,2,3";
$mesarr = explode(",",$messtr);
$messtr1 = "��Ϣ,��������,ð������";
$mesarr1 = explode(",",$messtr1);
foreach($mesarr as $k => $v)
{
	$mesoption .= '<option value='.$v.'>'.$mesarr1[$k].'</option>';
}
$mes1 = '<select name="select3" id="mes1">'.$mesoption.'</select>';
$mes2 = '<select name="select3" id="mes2">'.$mesoption.'</select>';
$mes3 = '<select name="select3" id="mes3">'.$mesoption.'</select>';
foreach($petsAll as $pet)
{
	if(!empty($pet['tgflag']))
	{
		$petarr[] = $pet; 
	}
}
if(is_array($petarr))
{
	$num = count($petarr);
	if($num == 1)
	{
		$option1 ='<select name="select5" id="pets1" onchange="getflag(this)" disabled="disabled">'.petoption($petarr[0]['id'],$petarr[0]['name'],$petsAll).'</select>';
		$flag1 = flag($petarr[0]['tgstime'],$petarr[0]['tgtime']);
		$time1 = '<select name="select2" id="time1" disabled=disabled >'.pettime($petarr[0]['tgtime'],$timearr).'</select>';
		$mes1 = '<select name="select3" id="mes1" disabled=disabled>'.tgmes($petarr[0]['tgmes'],$mesarr,$mesarr1).'</select>';
	}
	if($num == 2)
	{
		$option1 ='<select name="select5" id="pets1" onchange="getflag(this)" disabled="disabled">'.petoption($petarr[0]['id'],$petarr[0]['name'],$petsAll).'</select>';
		
		$option2 ='<select name="select4" id="pets2" onchange="getflag(this)" disabled="disabled"> disabled="disabled">'.petoption($petarr[1]['id'],$petarr[1]['name'],$petsAll).'</select>';
		
		$time1 = '<select name="select2" id="time1" disabled=disabled >'.pettime($petarr[0]['tgtime'],$timearr).'</select>';
		$time2 = '<select name="select20" id="time2" disabled=disabled >'.pettime($petarr[1]['tgtime'],$timearr).'</select>';
		
		$mes1 = '<select name="select3" id="mes1" disabled = disabled>'.tgmes($petarr[0]['tgmes'],$mesarr,$mesarr1).'</select>';
		$mes2 = '<select name="select3" id="mes2" disabled = disabled>'.tgmes($petarr[1]['tgmes'],$mesarr,$mesarr1).'</select>';
		
		$flag1 = flag($petarr[0]['tgstime'],$petarr[0]['tgtime']);
		$flag2 = flag($petarr[1]['tgstime'],$petarr[1]['tgtime']);
	}
	if($num == 3)
	{
		$option1 ='<select name="select5" id="pets1" onchange="getflag(this)" disabled="disabled">'.petoption($petarr[0]['id'],$petarr[0]['name'],$petsAll).'</select>';
		
		$option2 ='<select name="select4" id="pets2" onchange="getflag(this)" disabled="disabled"> disabled="disabled">'.petoption($petarr[1]['id'],$petarr[1]['name'],$petsAll).'</select>';
		
		$option3 ='<select name="select"  id="pets3" onchange="getflag(this)" disabled=disabled> disabled="disabled">'.petoption($petarr[2]['id'],$petarr[2]['name'],$petsAll).'</select>';
		
		$time1 = '<select name="select2" id="time1" disabled=disabled >'.pettime($petarr[0]['tgtime'],$timearr).'</select>';
		$time2 = '<select name="select20" id="time2" disabled=disabled >'.pettime($petarr[1]['tgtime'],$timearr).'</select>';
		$time3 = ' <select name="select2" id="time3"disabled=disabled >'.pettime($petarr[2]['tgtime'],$timearr).'</select>';
		
		$mes1 = '<select name="select3" id="mes1" disabled = disabled>'.tgmes($petarr[0]['tgmes'],$mesarr,$mesarr1).'</select>';
		$mes2 = '<select name="select3" id="mes2" disabled = disabled>'.tgmes($petarr[1]['tgmes'],$mesarr,$mesarr1).'</select>';
		$mes3 = '<select name="select3" id="mes3" disabled = disabled>'.tgmes($petarr[2]['tgmes'],$mesarr,$mesarr1).'</select>';
		
		$flag1 = flag($petarr[0]['tgstime'],$petarr[0]['tgtime']);
		$flag2 = flag($petarr[1]['tgstime'],$petarr[1]['tgtime']);
		$flag3 = flag($petarr[2]['tgstime'],$petarr[2]['tgtime']);
	}
}

//Word part.

//�õ���ǰ������й�ʱ��

$taskword= taskcheck($user['task'],4);

$_pm['mem']->memClose();
//@Load template.
$tn = $_game['template'] . 'tpl_tuoguan.html';
if (file_exists($tn))
{
	$tpl = @file_get_contents($tn);
	
	$src = array('#word#',
				 '#petsoption1#',
				 '#petsoption2#',
				 '#petsoption3#',
				 '#flag1#',
				 '#flag2#',
				 '#flag3#',
				 '#time1#',
				 '#time2#',
				 '#time3#',
				 '#mes1#',
				 '#mes2#',
				 '#mes3#',
				 '#time#',
				 '#num#'
				);
	$des = array($taskword,
				 $option1,
				 $option2,
				 $option3,
				 $flag1,
				 $flag2,
				 $flag3,
				 $time1,
				 $time2,
				 $time3,
				 $mes1,
				 $mes2,
				 $mes3,
				 $utime,
				 $tgnum
				);
	$mc = str_replace($src, $des, $tpl);
}

// gzip echo. if maybe.
ob_start('ob_gzip');
echo $mc;
ob_end_flush();


//�й�״̬
//$stime  ��ʼ�й�ʱ��
//$time �йܵ�ʱ��
function flag($stime,$time)
{
	$now = time();
	if($now < $stime)
	{
		$str = "�ȴ���";
	}
	else
	{
		$times = $now - $stime;
		if($times > $time)
		{
			$str = "�й����";
		}
		else
		{
			$str = "�й���";
		}
	}
	return $str;
}

//�йܳ���
//$id �йܳ����ID
//$name �йܳ��������
//$pets array ���еĳ���
function petoption($id,$name,$pets)
{
	$str ='<option value='.$id.'>'.$name.'</option>';
	foreach($pets as $pall)
	{
		if($pall['level'] <= 10 || $pall['id'] == $id || $pall['muchang'] != 1)
		{
			continue;
		}
		$str .= '<option value='.$pall["id"].'>'.$pall['name'].'</option>';
	}
	return $str;
}

//�й�ʱ��
//$arr ����
//$time �й�ʱ��
function pettime($time,$arr)
{
	$time1 = $time / 3600;
	$str = '<option value='.$time1.'>'.$time1.'Сʱ</option>';
	foreach($arr as $v)
	{
		if($v == $time1)
		{
			break;
		}
		$str .= '<option value='.$v.'>'.$v.'Сʱ</option>';
	}
	return $str;
}

//�й�����
//$mes �й�����
//$arr1 ���� ʱ��
//$arr2 ���� ����
function tgmes($mes,$arr1,$arr2)
{
	foreach($arr1 as $k => $v)
	{
		if($v == $mes)
		{
			$str .= '<option value='.$v.' selected=selected>'.$arr2[$k].'</option>';
		}
		else
		{
			$str .= '<option value='.$v.'>'.$arr2[$k].'</option>';
		}
	}
	return $str;
}
?>