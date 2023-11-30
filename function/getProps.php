<?php
/**
*@Version: %version%
*@Copyright: %copyright%
*@Author: %author%

*@Write Date: 2008.05.20
*@Update Date: 2008.05.30
*@Usage:Get User props.
*@Note: 
*/
require_once('../config/config.game.php');
secStart($_pm['mem']);

$err = 0;
$id = intval($_REQUEST['id']);

if ($id<1) die('Error Item Id!');
$user		= $_pm['user']->getUserById($_SESSION['id']);
$BAG		= $_pm['user']->getUserBagById($_SESSION['id']);
$wp			= false;
foreach ($BAG as $k => $v)
{
	if ($v['uid'] == $_SESSION['id'] && $v['id'] == $id)
	{
		$wp = $v;
		break;
	}
}
/*if($_SESSION['username']=="leinchu"){
	$lein_dbg = true;
}
*/
if (!is_array($wp) || $wp['sums']<1) die('Error item!');
else
{
	$_pm['mysql']->query("UPDATE userbag
							 SET sums=abs(sums-1)
						   WHERE uid={$_SESSION['id']} and id={$id} and sums > 0
						");
	$err = getValue($id, $wp['effect']);
	//$_pm['user']->updateMemUserbag($_SESSION['id']);
}
$_pm['mem']->memClose();
echo $err;

// Get effect value
// Ŀǰ���ż�MP,HP��
function getValue($n,$effect)
{
	global $_pm,$BAG;
	$hp = $mp = 0;
	$buff['addac'] = $buff['addmc'] = 0;
	$arr = explode(',', $effect);
	foreach ($arr as $k => $v)
	{
		$tarr = explode(':', $v);
		switch ($tarr[0])
		{
			case "hp": $hp = $tarr[1];break;
			case "mp": $mp = $tarr[1];break;
			case "addac": 
			{
				$buff['addac'] = $tarr[1];
				break;
			}
			case "addmc": 
			{
				$buff['addmc'] = $tarr[1];
				break;
			}

			default:;
		}
		unset($tarr);
	}
	if( $buff['addac'] > 0 || $buff['addmc'] > 0 )
	{
		$med_buff_info = $_pm['mysql']->getOneRecord(" SELECT F_Medicine_Buff FROM player_ext WHERE uid = '".$_SESSION['id']."'");
		
		if(  $med_buff_info['F_Medicine_Buff'] == '' )	//��δʹ�ù�
		{
			$_pm['mysql'] -> query(" UPDATE player_ext SET F_Medicine_Buff = '".$effect."' WHERE uid = '".$_SESSION['id']."'");	
			$_SESSION['first_in'] = 1;
		}
		else
		{	//addac:10000,addmc:10000
			foreach( $buff as $key => $val )
			{
				if( $buff[$key] != 0 )
				{
					if( strstr($med_buff_info['F_Medicine_Buff'],$key) )
					{
						die('hasusemedbuff');	//������������
					}
				}
			}
			//������û����������
			$buff_set = $med_buff_info['F_Medicine_Buff'].','.$effect;
			$_pm['mysql'] -> query(" UPDATE player_ext SET F_Medicine_Buff = '".$buff_set."' WHERE uid = '".$_SESSION['id']."'");	
			$_SESSION['first_in'] = 1;
			unset($med_buff_info);
		}
		
	}
	$bb	= $_pm['user']->getUserPetById($_SESSION['id']);
	$fit= $_SESSION['fight'.$_SESSION['id']];

	if (!is_array($fit) || !is_array($bb)) return false;
	$addHPSql="";
	$addMPSql="";
	foreach ($bb as $x => $y)
	{
		if (
			 $y['id'] == $fit['bid'] && 
			 $y['uid'] == $_SESSION['id'])
		{
			$sumhp = $y['hp'] + $hp;
			$summp = $y['mp'] + $mp;
			if($sumhp>$y['srchp']){//�����Ѫ֮����ʣ�࣬����Ѫ��������ĵ���
				$newhp= $y['srchp'];
				if($hp>$y['srchp']-$y['hp']){//������߼�Ѫ��������ʧѪ��
					$arr = getzbAttrib($y['id']);
					if($arr['hp']>0){//���װ����Ѫ������0
						$leftAavialeHP=$hp-($y['srchp']-$y['hp']);
						if($leftAavialeHP>$arr['hp']){
							$addHPSql=",addhp=".$arr['hp'];
						}else{
							$addHPSql=",addhp=addhp+".intval($leftAavialeHP);
						}
					}
				}
			}else $newhp = $sumhp;			

			if($summp>$y['srcmp']){//�����ħ֮����ʣ�࣬����ħ��������ĵ���
				$newmp= $y['srcmp'];
				if($mp>$y['srcmp']-$y['mp']){//������߼�ħ��������ʧħ��
					if(!isset($arr)){
						$arr = getzbAttrib($y['id']);
					}
					if($arr['mp']>0){//���װ����ħ������0
						$leftAavialeMP=$hp-($y['srcmp']-$y['mp']);
						if($leftAavialeMP>$arr['mp']){
							$addMPSql=",addmp=".$arr['mp'];
						}else{
							$addMPSql=",addmp=addmp+".intval($leftAavialeMP);
						}
					}
				}
			}else $newmp = $summp;
			
			break;
		}
	}
	
	$sql = "UPDATE userbb
				   SET hp={$newhp},
					   mp={$newmp}".$addMPSql.$addHPSql."
				 WHERE uid={$_SESSION['id']} and id={$fit['bid']}
			  ";
			  /*if($_SESSION['id'] == '261619'){
			  	echo $sql;
			  }*/
	// Update bb info.
	$_pm['mysql']->query($sql);
	$fit['fuzu']=1;
	$_SESSION['fight'.$_SESSION['id']]=$fit;
	return $hp.','.$mp.','.$buff['addac'].','.$buff['addmc'];
}
?>