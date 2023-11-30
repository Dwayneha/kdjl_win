<?php
/**
*@Version: %version%
*@Copyright: %copyright%
*@Author: %author%

@Usage: FamilyUpgrade buy function
@Write date: 2010.04.02
@##############################################
*/
require_once('../config/config.game.php');

secStart($_pm['mem']);
$count = 0;
$check = 0;

$srctime = 15;
$time = $_SESSION['time'.$_SESSION['id']];
if(empty($time))
{	
	$_SESSION['time'.$_SESSION['id']] = time();
}
else
{
	$nowtime = time();
	$ctime = $nowtime - $time;
	if($ctime < $srctime)
	{
		die("6");//û�дﵽ���ʱ��
	}
	else
	{
		$_SESSION['time'.$_SESSION['id']] = time();
	}
}


$user		= $_pm['user']->getUserById($_SESSION['id']);
$bags		= $_pm['user']->getUserBagById($_SESSION['id']);

$exit = $_pm['mysql'] -> getOneRecord("select * from guild_members where member_id = ".$_SESSION['id']." and (priv = 2 or priv = 3)");
if(!is_array($exit))
{
	die('1');//�����峤���������̵�
}

$level = $_pm['mysql'] -> getOneRecord("select id,level,shop_level from guild where id = ".$exit['guild_id']);
if(is_array($level))
{
	if($level['shop_level'] >= $level['level'])
	{
		die('2');//�����̵�ȼ����ڼ���ȼ���������
	}
}

//��������������Ʒ
$needprops = $_pm['mysql'] -> getOneRecord("select * from guild_settings where level = ".$level['level']);
if(is_array($needprops))
{
	if(strpos($needprops['need_items_for_shop'],',') == true)
	{
		$props = explode(',',$needprops['need_items_for_shop']);
		foreach($props as $key => $value)//������д��ʽ��A:B,C:D,E:F
		{
			$pro = explode(':',$value);
			$needid[] = $pro[0];
			$needsum[$pro[0]] = $pro[1];
		}
		$count = count($props);
	}
	else
	{
			$pro = explode(':',$needprops['need_items_for_shop']); 
			$needid[] = $pro[0];
			$needsum[$pro[0]] = $pro[1];
			$count = 1;
	}	
}

//��������Ʒ
if(is_array($bags))
{
	foreach($bags as $b_key => $b_value)
	{
		if($b_value['vary'] == 2)//�����۵�
		{
			$nonefold = $_pm['mysql'] -> getRecords("select * from userbag where pid = ".$b_value['pid']." and uid = ".$_SESSION['id']." and vary = 2 and zbing != 1 and sums > 0");
				if(is_array($nonefold[0]))
				{
					if(!in_array($b_value['pid'],$bagsid))
					{
						$bagsid[] = $nonefold[0]['pid'];
						$bagssum[$b_value['pid']] = count($nonefold);//ֻ�б����� �ֿ�Ĳ���	
					}
				
				}	
		}
		else
		{
			if($b_value['sums'] > 0)
			{
				$bagsid[] = $b_value['pid'];
				$bagssum[$b_value['pid']] = $b_value['sums'];
			}
		}
	
	}
}
			
//������������Ʒ�ıȽ�
if(is_array($needsum))
{
	foreach($needsum as $key => $value)
	{
		if(!in_array($key,$bagsid))
		{
			die('3');//��Ʒû��
		}
		else
		{
			if(is_array($bagssum))
			{
				foreach($bagssum as $bagkey => $bagvalue)
				{
					if($key == $bagkey)//ID �е�����¿�����
					{
						if($bagvalue >= $value)//�����������ڱ�������
						{
							$check++;
						}
						else
						{
							die('3');//��Ʒ����
						}
					}
				}
			}
		}
	}	
}

//������
if($check == $count && $count != 0)//������Ʒ�㹻
{
	//guild����Ȼ�����Ӧ�ĵ���
	$upgrade = $_pm['mysql'] -> query("update guild set shop_level=shop_level+1 where id=".$level['id']);
	if(mysql_affected_rows($_pm['mysql'] -> getConn()) != 1)
	{
		die('4');//����ʧ�ܣ����ݿ�ԭ��
	}
	//������
	foreach($needsum as $key_sum => $value_sum)
	{
		$vary = $_pm['mysql'] -> getRecords("select vary from userbag where pid={$key_sum} and uid={$_SESSION['id']} and sums > 0");
		if($vary[0]['vary'] == 1)
		{
			$deprops = $_pm['mysql'] -> query("update userbag set sums=sums-{$value_sum} where pid={$key_sum} and sums>={$value_sum} and uid={$_SESSION['id']}");	
		}
		else
		{
			for($i=1;$i<=$value_sum;$i++)
			{
				$deprops = $_pm['mysql'] -> query("delete from userbag where pid={$key_sum} and sums>={$value_sum} and uid={$_SESSION['id']} limit 1");
			}
		}
	}
	die('5');//�����ɹ�
}
?>