<?php
session_start();
//避免出现乱码
header('Content-Type:text/html;charset=GBK');
require_once "../config/config.game.php";
$userBag = $_pm['user'] -> getUserBagById($_SESSION['id']);
$user = $_pm['user'] -> getUserById($_SESSION['id']);
secStart($_pm['mem']);
if($_REQUEST['action'] == "to")
{
	$id = intval($_REQUEST['id']);
	if($id == $_SESSION['pid'.$_SESSION['id']])
	{
		$_SESSION['pid'.$_SESSION['id']] = "";
		$_SESSION['bid'.$_SESSION['id']] = "";
		$err = 1;
	}
	else if($id == $_SESSION['pids'.$_SESSION['id']])
	{
		$_SESSION['pids'.$_SESSION['id']] = "";
		$err = 1;
	}
	echo $err;
	//$_SESSION['dbg_equip_attr2'] .= "Why here 1?<br>";
}
else
{
	$err = 0;
	$id = intval($_REQUEST['id']);
	if($id < 1)
	{
		die("1");
	}
	$bid = intval($_REQUEST['bid']);
	if($bid < 0)
	{
		die("3");
	}
	
	//判断用户包裹是否已满
	$bagNum=0;
	if(is_array($userBag))
	{
		foreach($userBag as $x => $y)
		{
			if($y['sums']>0 and $y['zbing'] == 0) 
			{
				$bagNum++;		
			}
		}
	}
	
	if($bagNum >= $user['maxbag'])
	{
		die('5');
	}
	//查循包裹ID
	$sql = "SELECT id
			FROM userbag
			WHERE zbing = 1 and zbpets = {$bid} and uid = {$_SESSION['id']} and pid = {$id}";
	$row = $_pm['mysql'] -> getOneRecord($sql);
	//判断包裹ID是否有效（为空），当用户非法操作的时候可能出现此情况
	if($row['id'] == "")
	{
		die("4");
	}
	$sql = "SELECT zb 
			FROM userbb
			WHERE id = {$bid}";
	$rs = $_pm['mysql'] -> getOneRecord($sql);
	if(is_array($rs))
	{
		$zb = explode(",",$rs['zb']);
		if(is_array($zb))
		{
			foreach($zb as $k => $v)
			{
				$zbs = explode(":",$zb[$k]);
				if(is_array($zbs))
				{
					if($zbs[1] == $row['id'])
					{
						continue;
					}
					else
					{
						$str .= $zbs[0].":".$zbs[1].",";
						//$str后多了一个","
					}
				}
			}
		}
	}
	//去$str 后的多的那个","
	if(!empty($str))
	{
		$newStr = substr($str,0,-1);
	}
	$sql = "UPDATE userbag SET zbing = 0,zbpets = 0 WHERE id = $row[id]";
	$_pm['mysql'] -> query($sql);
	$sql = "UPDATE userbb 
			SET zb = '{$newStr}'
			WHERE id = {$bid}";
	$_pm['mysql'] -> query($sql);
	$err = 2;
	
	
	

	$_pm['mysql']->query("UPDATE userbb,player
						 SET addmp = 0,addhp = 0
					   WHERE fightbb=userbb.id and player.id={$_SESSION['id']}
					");
	//设定装备变化标志
	formatMsgEffect($bid);
	$_pm['mem']->set(array("k"=>"User_bb_equip_changed_".$bid.'_'.$_SESSION['id'],"v"=>1));
	
	
	$_pm['mem']->get("User_bb_equip_info_a_".$user['mbid'].'_'.$_SESSION['id']);
	$_pm['mem']->get("User_bb_equip_info_b_".$user['mbid'].'_'.$_SESSION['id']);
	
	//$_SESSION['dbg_equip_attr2'] .= "<strong>Right here 1!</strong><br>";
	echo $err;
}
$_pm['mem']->memClose();
?>