<?php
/**
*@Version: %version%
*@Copyright: %copyright%
*@Author: %author%

*@Write Date: 2008.05.01
*@Update Date: 2008.05.22
*@Usage: MuChang
*@Note: none
*/ 
require_once('../config/config.game.php');
secStart($_pm['mem']);

$user	 = $_pm['user']->getUserById($_SESSION['id']);
$petsAll  = $_pm['user']->getUserPetById($_SESSION['id']);
$td_num = 0;
$pall = 0;
$mainbb =''; //1:26;2:123 ;3:235;
if (!is_array($petsAll)) $petslist='��ȡ��������ʧ��!';
else 
{
	$kk = 0;
	foreach ($petsAll as $k => $rs)
	{
		if ($rs['name'] == '') continue;
		if ($rs['muchang']==1 && $rs['tgflag'] == 0)
		{//title="<img src='.IMAGE_SRC_URL.'/bb/'.$rs['imgdie'].' style=\'margin:0px;float:left;\'>'.$rs['name'].'<br/>'.getWx($rs['wx']).'ϵ<br/>'.'" 
			$pall++;
			$petslist .= '<tr>
              			<td width="130px" onmouseover="mcbbshow('.$rs['id'].');" style="cursor:pointer;text-align:left;" onmouseover="vtips(this)" onmouseout="ctips(this)" onclick="sel(this);copyWord(\''.$rs[name].'\');Display('.$rs['id'].');"><img src="'.IMAGE_SRC_URL.'/ui/muchang/mc05.gif" />'.$rs['name'].'</td>
              			<td width="70px" style="text-align:left;">'.getWx($rs['wx']).'</td>
              			<td style="text-align:left;" >LV '.$rs['level'].'</td>
            		  </tr>';
			$imgcontent = '<img src='.IMAGE_SRC_URL.'/bb/'.$rs['imgdie'].' style=\'margin:0px;float:left;\'>'.
					$rs['name'].'<br/>'.getWx($rs['wx']).'ϵ<br/>'
					.'" style="cursor:pointer;text-align:left;" onmouseover="vtips(this)" onmouseout="ctips(this)" onclick="sel(this);copyWord(\''.$rs[name].'\');Display('.$rs['id'].');';
		}
		else if($rs['muchang'] == 0 && $rs['tgflag'] != 1)
		{
			//----------------------------------
			if ($rs['id'] == $user['mbid'])
			{
				$pets[$kk++] = "<img src='".IMAGE_SRC_URL."/bb/{$rs['cardimg']}' onclick='Display({$rs['id']});zhixiang(this,".$td_num.")' style='cursor:pointer;'>";
			}
			else
			{
				$pets[$kk++] = "<img class='ch02' src='".IMAGE_SRC_URL."/bb/{$rs['cardimg']}' onclick='Display({$rs['id']});zhixiang(this,".$td_num.")' style='cursor:pointer;'>";
			}
			$td_num++;
			if ($rs['id'] == $user['mbid'])
			{
				if ($kk == 1) $mainbb='<td class="ch01">&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td>';
				else if($kk==2) $mainbb='<td>&nbsp;</td><td class="ch01">&nbsp;</td><td>&nbsp;</td>';
				else if($kk==3) $mainbb='<td>&nbsp;</td><td>&nbsp;</td><td class="ch01">&nbsp;</td>';
			}
			//----------------------------------
		}
	}
	if ($petslist == '') $petslist = '�������滹û�б�����';
}
$pnum = $pall;
$pall = '';
/* added by Zheng.Ping */
$login = '���룺<input name="login" type="password" id="login"  /><br /><br />
&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<input type="button" name="Submit" value="ȷ��" onclick="login()" hidefocus />&nbsp;&nbsp;&nbsp;&nbsp;
<input type="button" name="Submit2" value="�޸�����" onclick="update()" hidefocus />';
$encryptAction = 'pwd()';
$encryptSubmit = 'jiami();';
$oldPwdInput   = '';
$discardStep   = 'delbb()';
$discardAction = "Ajax.Request('../function/mcGate.php?op=d&id='+bid, opt)";
$getDiscardPwd = '';
$discardCheck  = "if(bid==0){window.parent.Alert('����Ҫ��ѡ��һ��������!');return;}
    else if(!confirm('Ϊ�˿ڴ���������Ľ�����չ��������Ҫ������10000��Ҵ����!\\nע�⣬��һ����������������Ҳ�Ҳ������ˣ����ұ���������װ��\\nҲ��һ����ʧ����Ȼ���ڶ���ǰҲ������ȡ��������ȷ��Ҫ�����ñ�����?')) return;";

if(!empty($user['fieldpwd'])) {
    //$encryptAction = 'pwd3()';
    $encryptSubmit = 'resetPwd();';
    $oldPwdInput   = 'ԭ���룺<input type="password" name="old_pwd" id="old_pwd" /><br /><br />��';
    $discardStep   = 'discardBb();';
    $discardAction = "Ajax.Request('../function/mcGate.php?op=d&pwd='+pwd+'&id='+bid, opt)";
    $getDiscardPwd = "var pwd = $('pwd2').value;";
    $discardCheck  = '';
}

if(!empty($user['fieldpwd']) && empty($_SESSION['loginField' . $_SESSION['id']]))
{
    $petslist = $login;
    $encryptAction = "alert('�������Ѿ�����!');return false;";
}




//�õ��������õĳ���
$i = 0;
$utime = $user['tgtime'];
foreach($petsAll as $pall)
{
	if($pall['level'] <= 10 || $pall['muchang'] != 1)
	{
		continue;
	}
	if($pall['tgflag'] > 0){
		$i++;
		$mesoption .= '<option value='.$pall["id"].'>'.$pall['name'].'</option>';
	}else{
		$petsoption .= '<option value='.$pall["id"].'>'.$pall['name'].'</option>';
	}
}
//�����йܵ������Ŀ
$tgnum = $user['tgmax'] - $i;
/* added by Zheng.Ping */

//Word part.
$taskword= taskcheck($user['task'],4);

$_pm['mem']->memClose();
$style = $_GET['style'];
$sjarr = $_pm['mysql'] -> getOneRecord("SELECT sj FROM player_ext WHERE uid = {$_SESSION['id']}");
//@Load template.
$tn = $_game['template'] . 'tpl_muchang.html';
if (file_exists($tn))
{
	$tpl = @file_get_contents($tn);
	
	$src = array('#money#',
				 '#yb#',
				 '#mclimit#',
				 //right attrib.
				 '#bblist#',
	             '#encryptAction#',
                 '#encryptSubmit#',
                 '#oldPwdInput#',
                 '#discardStep#',
                 '#discardAction#',
                 '#getDiscardPwd#',
                 '#discardCheck#',
				 '#word#',
				 '#one#',
				 '#two#',
				 '#three#',
				 '#mainbb#',
				 '#bb#',
				 '#time#',
				 '#num#',
				 '#mesoption#',
				 '#style#',
				 '#sj#'
				);
	$des = array($user['money'],
				 $user['yb'],
				 $pnum.'/'.$user['maxmc'],
				 //right part
				 $petslist,
				 $encryptAction,
                 $encryptSubmit,
                 $oldPwdInput,
                 $discardStep,
                 $discardAction,
                 $getDiscardPwd,
                 $discardCheck,
				 $taskword,
				 $pets[0],
				 $pets[1],
				 $pets[2],
				 $mainbb,
				 $petsoption,
				 $utime,
				 $tgnum,
				 $mesoption,
				 $style,
				 $sjarr['sj']
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
