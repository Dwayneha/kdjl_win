<?php //props:734:85:86:87:733|3,money:20000
exit();
require_once('../config/config.game.php');
define(MEM_PROPS_KEY, "db_props");
define(MEM_TASK_KEY, "db_task");
define(MEM_WX_KEY, "db_wx");
define(MEM_EXP_KEY, "db_exptolv");
define(MEM_BB_KEY, $_SESSION['id']."bb");
define(MEM_BAG_KEY, $_SESSION['id']."bag");

$m = new memory();
$db = new mysql();
secStart($m);

$user = unserialize($m->get($_SESSION['id']));
$_bag = unserialize($m->get(MEM_BAG_KEY));
header('Content-Type:text/html;charset=GBK');

$drs = $db->getOneRecord("select cet from libao where pname='{$_SESSION['username']}'");
if (!is_array($drs))
{
	die('��û�ʸ���ȡ�ɣ�');
}
$wps = explode(',',$drs['cet']);

$wp = $wps[0];
$money = $wps[1];


// Check 
$hd = $db->getOneRecord("select pname from libao where pname='{$user['name']}' and flag=0");
if(!is_array($hd)) die('���Ѿ���ȡ�������û���ʸ���ȡ!');
else $db->query("update libao set flag=1 where pname='{$user['name']}'");

saveGetProps($wp);

$user['money']+=$money;
$m->set(array('k'=>$_SESSION['id'] ,'v'=>$user));

$m->memClose();
die('��ϲ�����ɹ���ȡ���!');

function saveGetProps($idlist)
{
	if ($idlist == '' or $idlist == 0) return false;
	global $m,$user,$_bag;
	//	������飺
	$l=0;
	if(is_array($_bag))
	{
		foreach ($_bag as $x => $y)
		{
			if ($y['sums']>0) $l++;
		}
	}
	if ($l >= $user['maxbag']) return false;	
	
	$arr = split(',', $idlist);
	
	foreach ($arr as $k => $v)
	{
		$rs = $m->dataGet(array('k' => MEM_BAG_KEY, 
								'v' => "if(\$rs['uid']=='{$_SESSION['id']}' && \$rs['pid']=='{$v}') \$ret=\$rs;"
							  )); 

		if (is_array($rs))
		{
			if ($rs['vary'] == 1) // ���۵�����.
			{
				$tt = time();
				$m->updateArray(array('k'	=>	MEM_BAG_KEY,
									  'v'	=> "if(\$rs['id']=='{$rs['id']}')
												{ \$rs['sums']=\$rs['sums']+1;
												  \$rs['stime']={$tt};}"
							));
			}
			else
			{
				$newid = mem_get_autoid($m, MEM_ORDER_KEY, 'userbag');
				$m->addArray(array(
				              'k' => MEM_BAG_KEY,
					 	      'v' => array('id' 	=> $newid,
											'pid'   => $v,
							                'uid' 	=> $_SESSION['id'],
											'sell'  => $rs['sell'],
											'vary'	=> 2,
											'sums'	=> 1,
											'name'  => $rs['name'],
											'img'	=> $rs['img'],
											'stime'	=> time(),
											'effect'=> $rs['effect'],
											'usages'=> $rs['usages'],
											'postion' => $rs['postion'],
											'zbing' => 0,
											'varyname'=>$rs['varyname'],
											'requires'=> $rs['requires']
											)
							 ));
			   $l++;
		   }	   
		}
		else{
			$rs = $m->dataGet(array('k' => MEM_PROPS_KEY, 
								    'v' => "if(\$rs['id'] == '{$v}') \$ret=\$rs;"
								  ));
			if (is_array($rs))
			{
				$newid = mem_get_autoid($m, MEM_ORDER_KEY, 'userbag');
				$m->addArray(array(
				              'k' => MEM_BAG_KEY,
					 	      'v' => array('id' 	=> $newid,
											'pid'   => $v,
							                'uid' 	=> $_SESSION['id'],
											'sell'  => $rs['sell'],
											'vary'	=> $rs['vary'],
											'sums'	=> 1,
											'img'	=> $rs['img'],
											'name'  => $rs['name'],
											'stime'	=> time(),
											'effect'=> $rs['effect'],
											'usages'=> $rs['usages'],
											'postion' => $rs['postion'],
											'zbing' => 0,
											'varyname'=>$rs['varyname'],
											'requires'=> $rs['requires']
											)
							 ));
				$l++;
			}	
		}		
		unset($rs);
		// ����Ƿ񳬳�������
		if ($l >= $user['maxbag']) return false;
	}	
	unset($db,$arr);
}
?>