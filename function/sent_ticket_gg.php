<?php
require_once('../kernel/socketmsg.v1.php');
require_once('../socketChat/config.chat.php');
$s=new socketmsg();
if(isset($_POST['type']))
{
	$best_arr = explode(',',$_POST['best']);
	$thing = explode('-',$best_arr[1]);
	for($i = 0; $i<count($thing);$i++)
	{
		$mid_arr = explode('|',$thing[$i]);
		$thing_info_best .= '<a>'.$mid_arr[1]."</a>,";
	}
	$thing_info_best = substr($thing_info_best,0,-1);
	$one_arr = explode(',',$_POST['one']);
	$thing = explode('-',$one_arr[1]);
	for($i = 0; $i<count($thing);$i++)
	{
		$mid_arr = explode('|',$thing[$i]);
		$thing_info_one .= '<a>'.$mid_arr[1]."</a>,";
	}
	$thing_info_one = substr($thing_info_one,0,-1);	
	$two_arr = explode(',',$_POST['two']);
	$thing = explode('-',$two_arr[1]);
	for($i = 0; $i<count($thing);$i++)
	{
		$mid_arr = explode('|',$thing[$i]);
		$thing_info_two .= '<a>'.$mid_arr[1]."</a>,";
	}
	$thing_info_two = substr($thing_info_two,0,-1);
	$gg = 1;
	switch($_POST['type'])
	{
		case 1 :
		{
			$word = 'an|Ŀǰʣ���صȽ�<font color = "black">'.$best_arr[0].'</font>��,������Ʒ:'.$thing_info_best.',һ�Ƚ�<font color = "black">'.$one_arr[0].'</font>��,������Ʒ:'.$thing_info_one.',ʣ��ιο�����:<font color = "black">'.$_POST['null'].'/'.$_POST['all'].'</font>';
			break;
		}
		case 2:
		{
			$word = 'an|��һ�ֳ齱�����,Ŀǰʣ���صȽ�<font color = "black">'.$best_arr[0].'</font>��,������Ʒ:'.$thing_info_best.',һ�Ƚ�<font color = "black">'.$one_arr[0].'</font>��,������Ʒ:'.$thing_info_one.',ʣ��ιο�����:<font color = "black">'.$_POST['null'].'/'.$_POST['all'].'</font>';
			break;
		}
		case 3:
		{
			$word = 'an|���ֻ����,Ŀǰʣ���صȽ�<font color = "black">'.$best_arr[0].'</font>��,������Ʒ:'.$thing_info_best.',һ�Ƚ�<font color = "black">'.$one_arr[0].'</font>��,������Ʒ:'.$thing_info_one.',ʣ��ιο�����:<font color = "black">'.$_POST['null'].'/'.$_POST['all'].'</font>'.'��һ�ֻ���ڰ�Сʱ����';
			break;
		}
	}
}
else
{
	$nickname = $_POST['nickname'];
	$area = $_POST['area'];
	$get_props = $_POST['props'];
	switch($_POST['Award'])
	{
		case 1 :
		{
			$level = '�صȽ�';
			$gg = 1;
			$word = 'an|��ϲ�ٷ�ƽ̨['.$area.']�� ���['.$nickname.'] ͨ�����˹ιο������'.$level.',�õ���Ʒ:'.$get_props;
			break;
		}
		case 2 :
		{
			$level = 'һ�Ƚ�';
			$gg = 1;
			$word = 'an|��ϲ�ٷ�ƽ̨['.$area.']�� ���['.$nickname.'] ͨ�����˹ιο������'.$level.',�õ���Ʒ:'.$get_props;
			break;
		}
		case 3 :
		{
			$level = '���Ƚ�';
			$word = 'an|��ϲ�ٷ�ƽ̨['.$area.']�� ���['.$nickname.'] ͨ�����˹ιο������'.$level.',�õ���Ʒ:'.$get_props;
			$gg = 1;
			break;
		}
		case 4 :
		{
			$level = '���Ƚ�';
			$gg = 0;
			break;
		}
		case 5 :
		{
			$level = '�ĵȽ�';
			$gg = 0;
			break;
		}
	}
}
if($gg == 1)
{
	$word = iconv('gbk','utf-8',$word);
	$r = $s->sendMsg($word);
}
?>
