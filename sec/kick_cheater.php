<?php
function microtime_float()
{
    list($usec, $sec) = explode(" ", microtime());
    return ((float)$usec + (float)$sec);
}
function signWaiGua($wgUser,$type,$Time)
{
	if($type=='mustvisit'){
		if(
				!in_array($_SESSION['id'],$wgUser['wgList'])
				||$wgUser['wgList'][$_SESSION['id']][$type][1]!=date("YmdH")
		)
		{			
			$wgUser['wgList'][$_SESSION['id']][$type] = array($_SESSION['username'],date("YmdH"));
		}
	}else{
		if($wgUser['wgList'][$_SESSION['id']][$type][1]!=date("YmdH"))
		{
			$wgUser['wgList'][$_SESSION['id']][$type] = array($_SESSION['username'],date("YmdH"),1);		
		}else{
			$wgUser['wgList'][$_SESSION['id']][$type][2]++;
		}		
	}
}

if(isset($_SESSION['id'])){
	//��ʼ��
	$Time = time();
	$userIsWaigua = false;
	$wgUser = unserialize($_pm['mem']->get("wgUser"));
	if(!isset($_SESSION['login_time']))
	{
		$_SESSION['login_time'] = $Time;
	}	
	if(empty($wgUser)) $wgUser=array(
									'userList'=>array(),		//�û���½ʱ������ʱ��
									'visitList'=>array(),		//���Ⱥ���ʵ��ļ��ķ��ʼ�¼��Ҫ�������������ȵ��̵�
									'configMVVED'=>array(),		//ÿ���û����ʱ���Ҫ���ʵ��ļ������
									'wgList'=>array()			//����Ϊ��ʹ����ҵ��û�����Ϣ
									);
	if($wgUser['userList'][$_SESSION['id']]['login_time']!=$_SESSION['login_time'])
	{
		$wgUser['visitList'][$_SESSION['id']]=array();
		$wgUser['configMVVED'][$_SESSION['id']]=array();
		$wgUser['wgList']=array();
		$wgUser['userList'][$_SESSION['id']]['login_time'] = $_SESSION['login_time'];
	}
	
	//$wgUser['userList'][$_SESSION['id']]['login_time'] = $Time;
	
	$wgUser['userList'][$_SESSION['id']]['last_active_time'] = $Time;

	//����˳��
	$configVORD= array(
					'/function/getTaskinfo.php'=>'/function/getTask.php',
					'/function/Base_Mod.php'=>'/function/baseGate.php',
					'/function/getBag.php'=>'/function/props2Depot.php',
					'/function/Pai_Mod.php'=>'/function/paisellGate.php',
					'/function/Props_Mod.php'=>'/function/PropsGate.php'
					);
	$configVORD1= array(					
					'/function/Props_Mod.php'=>'/function/sellBag.php'
					);
	if(array_key_exists($_SERVER['PHP_SELF'],$configVORD))
	{
		$wgUser['visitList'][$_SESSION['id']][$_SERVER['PHP_SELF']]=$Time;
	}
	if(
		in_array($_SERVER['PHP_SELF'],$configVORD)||
		in_array($_SERVER['PHP_SELF'],$configVORD1))
	{
		$configVORDReverse = array_merge(array_flip($configVORD),array_flip($configVORD1));
		//$configVORDReverse = array_flip($configVORD);
		$visitTime = $wgUser['visitList'][$_SESSION['id']][$configVORDReverse[$_SERVER['PHP_SELF']]];
		if(empty($visitTime)||$visitTime+1800<$Time)
		{	
			signWaiGua($wgUser,'visitorder',$Time);
		}
	}
	
	//������ʵ��ļ���Ҳ�����ʵ��ļ�,���ֿ�������ң���һ�������ʱ����������
	$configMV = array(
						'/function/onlineStat.php',
						'/function/ext_Online.php',
						'/function/exit.php',
						'/function/Welcome_Mod.php'
					);
	
	if(
		in_array(
			$_SERVER['PHP_SELF'],
			$configMV	
		)		
	)
	{
		$wgUser['configMVVED'][$_SESSION['id']][$_SERVER['PHP_SELF']] = 1;
	}else{
	}
	
	if(
		$Time-$wgUser['userList'][$_SESSION['id']]['login_time']>40 &&
		(
			empty($wgUser['configMVVED'][$_SESSION['id']]) ||
			count($wgUser['configMVVED'][$_SESSION['id']])+1 < count($configMV)
		)
	)
	{
		if(
			$Time-$wgUser['userList'][$_SESSION['id']]['login_time'] > 40+6*rand(1,10)+1.2*rand(1,100)
		)
		{		
			signWaiGua($wgUser,
				'mustvisit',$Time
			);		
		}
	}
	
	$_pm['mem']->set(array('k'=>"wgUser",'v'=>$wgUser));
}
?>