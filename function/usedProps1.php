<?php
/**
*@Version: %version%
*@Copyright: %copyright%
*@Author: %author%

*@Write Date: 2008.05.01
*@Update Date: 2008.09.06
*@Usage: Used props.
*@Note: none
 Fix: zb level and wx limit for zb bb
*/
session_start();
header('Content-Type:text/html;charset=GBK');
require_once('../config/config.game.php');

secStart($_pm['mem']);

$user		= $_pm['user']->getUserById($_SESSION['id']);
$bags=$bag	= $_pm['user']->getUserBagById($_SESSION['id']);
$id = intval($_REQUEST['id']); // userbag id
if ($id<1 || !is_array($bags)) die('��Ʒ������!');
if(lockItem($id) === false)
{
	die('�Ѿ��ڴ����ˣ�');
}




// �������
if($_REQUEST['op'] == 'reset')
{
	echo '������ɣ�';
	unLockItem($id);
	exit();
}



foreach ($bags as $k => $v)
{
	if ($v['id'] == $id && $v['uid'] == $_SESSION['id'] && $v['sums']>0 && $v['zbing']==0)
	{
		$rs = $v; 
		break;
	}
}
$_pm['mysql'] -> query('START TRANSACTION;');
// main bb for user.
$bb = $_pm['mysql']->getOneRecord("SELECT * FROM userbb
						  WHERE id={$user['mbid']} and uid={$_SESSION['id']} 
						  LIMIT 0,1
						");
if (!is_array($rs)){
	unLockItem($id);
	die("û�з��������Ʒ��");
}

// if is zb,used it!
// if is zb,used it!
if ($rs['varyname'] == 9)	//װ��ϵͳ��
{
	if (is_array($bb))
	{
		// Check �Ƿ���ϱ���Ҫ��
		if ($rs['requires']!='')
		{
			$arr = explode(',', $rs['requires']);
			if(is_array($arr))
			{
				foreach($arr as $v){
					if(!empty($v))
					{
						$newarr = explode(":",$v);
						if($newarr[0] == 'lv'){
							$tlv = $newarr[1];
						}else if($newarr[0] == 'wx' && !empty($newarr[1])){
							$twx = $newarr[1];
						}
					}
				}
			}
			if(!empty($twx) && $twx != $bb['wx'])
			{
				unLockItem($id);
	$_pm['mysql'] -> query('ROLLBACK;');					
				die('�������в�ƥ��!');
			}
			else if(!empty($tlv) && $tlv > $bb['level'])
			{
				unLockItem($id);
	$_pm['mysql'] -> query('ROLLBACK;');					
				die('�����ȼ�����!');
			}
			/*$lv  = explode(':', $arr[0]);
			if ($lv[0] == "lv") $tlv = $lv[1];
			else if($lv[0] == "wx") $twx = $lv[1];
			
			if($arr[1] != '')
			{
				$wx = explode(':', $arr[1]);
				if ($wx[0] == "lv") $tlv = $wx[1];
				else if($wx[0] == "wx") $twx = $wx[1];
			}
			
			if ($twx!= $bb['wx'] || $tlv>$bb['level']) die('�����ȼ����������в�ƥ��!');*/
		}
		
		// Ensure props attrib is ok
		if (!isset($rs['postion']) || $rs['postion'] == '')
		{
			$prs = $_pm['mem']->dataGet(array('k'	=>	MEM_PROPS_KEY,
									 'v'	=>	"if(\$rs['id'] == '{$rs['pid']}') \$ret=\$rs;"
					));
			$rs['postion'] = $prs['postion']; // Fix postion.
			unset($prs);
		}
		
		if (strlen($bb['zb'])<2) 
		{
			$bb['zb'] = $rs['postion'] . ':' . $rs['id'];
		}
		else
		{
			if (strstr($bb['zb'], ","))
			{
				$zb  = split(',', $bb['zb']); // format: p:id,p:id
				$new = '';
				$rpl = 0;
				foreach($zb as $k => $v)
				{
					$arr = explode(':',$v);
					if ($arr[0] == $rs['postion']) // �滻��Ӧװ����
					{
						$new   .= ','.$arr[0] . ':' . $id;
						$oldid	= $arr[1];
						$rpl	= 1;
					}else $new .= ',' . $v;
				}
				$bb['zb'] = substr($new,1);
				if(!$rpl) $bb['zb'] .= ',' . $rs['postion'] . ':' . $rs['id'];
			}
			else 
			{
				$arr = explode(':', $bb['zb']);
				if ($arr[0] == $rs['postion']) // �滻��Ӧװ����
				{
					$bb['zb'] = $arr[0] . ':' . $rs['id'];
					$oldid = $arr[1];
				}else $bb['zb'] = $bb['zb'] . ',' . $rs['postion'] . ':' . $rs['id'];
			}
		}
		
		/**Find current postion zb clear zb tag.*/
		$clearlist = '';
		foreach ($bags as $k => $v)
		{
			if ($v['postion'] == $rs['postion'] and $v['zbing']!=0 and $v['zbpets']!=0 and $v['zbpets']==$bb['id'])
			{
				$clearlist .= $clearlist?','.$v['id']:$v['id'];
			}
		}

		

		$_pm['mysql']->query("UPDATE userbb 
					   SET zb='{$bb['zb']}'
					 WHERE id={$user['mbid']}
				  ");
		if (!empty($clearlist) && $clearlist!='')
		{
			$_pm['mysql']->query("UPDATE userbag 
						   SET zbing=0,zbpets=0 
						 WHERE id in ({$clearlist})
					 ");
		}
		$_pm['mysql']->query("UPDATE userbag 
					   SET zbing=1,zbpets={$user['mbid']}
					 WHERE id={$id}
				  ");

		//�趨װ���仯��־
		$_pm['mem']->set(array("k"=>"User_bb_equip_changed_".$user['mbid'].'_'.$_SESSION['id'],"v"=>1));
		//$_SESSION['dbg_equip_attr2'] .= "Right here 2!<br>";
		unLockItem($id);
	$_pm['mysql'] -> query('COMMIT;');
		die('��ϲ����װ���ɹ���');
	}
	else{
		unLockItem($id);
	$_pm['mysql'] -> query('ROLLBACK;');					
		die('����û��������ս���������ܽ���װ����');
	}
}
else if($rs['varyname'] == 13) // �������͡���չ�������ֿ⣬��������
{
	//�йܿռ������
	if($rs['pid'] == 1203)
	{
		if($user['tgmax'] >= 2)
		{
			unLockItem($id);
	$_pm['mysql'] -> query('ROLLBACK;');					
			die("��ֻ��ʹ�ô˾�����һ���й�����");
		}
		else if($user['tgmax'] == 1) 
		{
			$sql = "UPDATE player SET tgmax = 2 WHERE id = {$_SESSION['id']}";
			$_pm['mysql'] -> query($sql);
			$sql = "UPDATE userbag SET sums = sums - 1 WHERE uid = {$_SESSION['id']} and pid = 1203 and sums>0";
			$_pm['mysql'] -> query($sql);
			unLockItem($id);
	$_pm['mysql'] -> query('COMMIT;');
			die("ʹ���й���������ᣨһ���ɹ�!");
		}
	}
	if($rs['pid'] == 1204)
	{
		if($user['tgmax'] >= 3)
		{
			unLockItem($id);
	$_pm['mysql'] -> query('ROLLBACK;');					
			die("��ֻ��ʹ�ô˾�����һ���й�����");
		}
		else if($user['tgmax'] == 1)
		{
			unLockItem($id);
	$_pm['mysql'] -> query('ROLLBACK;');					
			die("����ʹ���й��������һ�����������й���!");
		}
		else if($user['tgmax'] == 2) 
		{
			$sql = "UPDATE player SET tgmax = 3 WHERE id = {$_SESSION['id']}";
			$_pm['mysql'] -> query($sql);
			$sql = "UPDATE userbag SET sums = sums - 1 WHERE uid = {$_SESSION['id']} and pid = 1204 and sums>0";
			$_pm['mysql'] -> query($sql);
			unLockItem($id);
	$_pm['mysql'] -> query('COMMIT;');
			die("ʹ���й���������ᣨ�����ɹ�!");
		}
	}
	$eff = explode(":",$rs['effect']);
	if($eff[0] == 'zhanshi')
	{
		$arr = "";
		$arr = $_pm['mysql'] -> getOneRecord("SELECT bbshow FROM player_ext WHERE uid = {$_SESSION['id']}");
		if(!is_array($arr))
		{
				unLockItem($id);
	$_pm['mysql'] -> query('ROLLBACK;');					
			die("����ʱ����ʹ�ó���չʾ��");
		}
		$_pm['mysql'] -> query("UPDATE player_ext SET bbshow = bbshow + {$eff[1]} WHERE uid = {$_SESSION['id']}");
		$_pm['mysql'] -> query("UPDATE userbag SET sums = sums - 1 WHERE pid = {$rs['pid']} and uid = {$_SESSION['id']} and sums>0");
			unLockItem($id);
	$_pm['mysql'] -> query('COMMIT;');
		die("��ϲ��ʹ�ó���չʾ��ɹ�����".$eff[1]."��չʾ���ᣡ");
	}
	if(is_array($eff))
	{
		if($eff[0] == "tuoguan")
		{
			$sql = "UPDATE player SET tgtime = tgtime + $eff[1] WHERE id = {$_SESSION['id']}";
			$_pm['mysql'] -> query($sql);
			$_pm['mysql'] -> query("UPDATE userbag SET sums = sums - 1 WHERE uid = {$_SESSION['id']} and pid = {$rs['pid']} and sums>0");
				unLockItem($id);
	$_pm['mysql'] -> query('COMMIT;');
			die("ʹ��{$eff[1]}Сʱ�йܾ�ɹ�!");
		}
	}
	$keys = explode(':', $rs['effect']);
	if ($rs['pid'] >=85 && $rs['pid']<=93)
	{
		$keys = explode(':', $rs['effect']);
		$item = split(',',$user['openmap']);
		if (in_array($keys[1], $item)){
			unLockItem($id);
	$_pm['mysql'] -> query('ROLLBACK;');					
			die($rs['name'].'��Ӧ�ĵ�ͼ�Ѿ�����!');
		}
		
		$valid = false;
		foreach ($bags as $k => $v)
		{
			if ($v['id'] == $id)
			{
				$valid	= true;
				$rs = $v;
				break;
			}
		}
		if (is_array($rs))
		{
			// del a props for current map.
			$_pm['mysql']->query("UPDATE userbag SET sums = sums -1 WHERE uid = {$_SESSION['id']} and id = {$id} and sums>0");
			$user['openmap'] .= ','.$keys[1];
		
			$_pm['mysql']->query("UPDATE player 
						   SET openmap='{$user['openmap']}' 
						 WHERE id={$_SESSION['id']}");

			unLockItem($id);
	$_pm['mysql'] -> query('COMMIT;');
			die("{$rs['name']} ��Ӧ��ͼ�򿪳ɹ�!");
		}
		else{
			unLockItem($id);
	$_pm['mysql'] -> query('ROLLBACK;');					
			die("��ͼ��ʧ�ܣ���ȷ�ϰ������д򿪸õ�ͼ��Ӧ��Կ��!");
		}
	}
	else if($rs['pid'] >=200 && $rs['pid']<=202)
	{
		$full = 0;
		if($rs['name'] == "�ֿ���������")
		{
			if($user['maxbase'] >= 96) $full=1;
			if($user['maxbase']+6>96) $user['maxbase']=96;
			else $user['maxbase']+=6;
		}
		else if($rs['name'] == "������������")
		{
			if($user['maxbag'] >= 96) $full=1;
			if($user['maxbag']+6>96) $user['maxbag']=96;
			else $user['maxbag']+=6;
		}
		else if($rs['name'] == "������������")
		{
			if($user['maxmc'] >= 40) $full=1;
			if($user['maxmc']+6>40) $user['maxmc']=40;
			else $user['maxmc']+=6;
		}
		if ($full==1){
			unLockItem($id);
	$_pm['mysql'] -> query('ROLLBACK;');					
			die("�Ѿ���չ�����ޣ���������չ������������!");
		}

		// del props. and save result.
		$_pm['mysql']->query("UPDATE player 
					   SET maxbase={$user['maxbase']},
					       maxbag={$user['maxbag']},
						   maxmc={$user['maxmc']}
					 WHERE id={$_SESSION['id']}");
		
		$_pm['mysql']->query("UPDATE userbag
					   SET sums=sums-1
					 WHERE id={$id} and uid={$_SESSION['id']} and sums>0
				  ");
		unLockItem($id);
	$_pm['mysql'] -> query('COMMIT;');
		die("ʹ�õ��� {$rs['name']} �ɹ�!");
	}
	else if ($rs['pid'] == 1342){
		$full = 0;
		if($user['maxbag'] == 150) $full=1;
		if($user['maxbag'] < 96) $full=2;
		if($user['maxbag']+6>150) $user['maxbag']=150;
		else $user['maxbag']+=6;
		if ($full==1){
			unLockItem($id);
	$_pm['mysql'] -> query('ROLLBACK;');					
			die("�Ѿ���չ�����ޣ������ټ�����չ��!");
		}
		if ($full==2){
			unLockItem($id);
	$_pm['mysql'] -> query('ROLLBACK;');					
			die("������û��չ��96�������ñ�������������չ��96��!");
		}
		$_pm['mysql']->query("UPDATE player 
					   SET maxbag={$user['maxbag']}
					 WHERE id={$_SESSION['id']}");
		
		$_pm['mysql']->query("UPDATE userbag
					   SET sums=sums-1
					 WHERE id={$id} and uid={$_SESSION['id']} and sums>0
				  ");
		unLockItem($id);
	$_pm['mysql'] -> query('COMMIT;');
		die("ʹ�õ��� {$rs['name']} �ɹ�!");
	}
	else if ($rs['pid'] == 1343){
		$full = 0;
		if($user['maxbase'] == 150) $full=1;
		if($user['maxbase'] < 96) $full=2;
		if($user['maxbase']+6>150) $user['maxbase']=150;
		else $user['maxbase']+=6;
		if ($full==1){
			unLockItem($id);
	$_pm['mysql'] -> query('ROLLBACK;');					
		die("�Ѿ���չ�����ޣ������ټ�����չ��!");
		}
		if ($full==2){	
			unLockItem($id);
	$_pm['mysql'] -> query('ROLLBACK;');					
			die("�ֿ⻹û��չ��96�������òֿ�����������չ��96��!");
		}
		$_pm['mysql']->query("UPDATE player 
					   SET maxbase={$user['maxbase']}
					 WHERE id={$_SESSION['id']}");
		
		$_pm['mysql']->query("UPDATE userbag
					   SET sums=sums-1
					 WHERE id={$id} and uid={$_SESSION['id']} and sums>0
				  ");
		unLockItem($id);
	$_pm['mysql'] -> query('COMMIT;');
		die("ʹ�õ��� {$rs['name']} �ɹ�!");
	}
	else if(($rs['pid'] >=742 && $rs['pid']<=746) || $rs['pid'] == 1247 || $rs['pid'] == 1225) // ������Զ�ս����. format: 
	{
		if ($keys[0] == 'exp') // ʹ�þ����
		{
			$dbl=0;
			switch($keys[1])
			{
				case 1.5: $dbl = 2;break;
				case 2:   $dbl = 3;break;
				case 2.5: $dbl = 4;break;
				case 3: $dbl = 5;break;
			}
		
			if(is_array($rs))
			{
				// del a props for current 
				$_pm['mysql']->query("UPDATE userbag
							   SET sums=sums-1
							 WHERE id={$id} and uid={$_SESSION['id']} and sums>0
						  ");
				// ��ȡ��ǰ��ʣ��˫��ʱ�䲢�ۼơ�
				if ($user['dblexpflag']>1 && $dbl==$user['dblexpflag']) 
				{
					$other=$user['dblstime']+$user['maxdblexptime']-time();
					if ($other<=0) $other=0;
					$user['maxdblexptime']=3600+$other;
				}
				else $user['maxdblexptime']=3600;
				
				$user['dblexpflag']=$dbl;
				$user['dblstime']=time();

				// Update user data to database.
				$_pm['mysql']->query("UPDATE player
							   SET maxdblexptime={$user['maxdblexptime']},
								   dblexpflag={$user['dblexpflag']},
								   dblstime={$user['dblstime']}
							 WHERE id={$_SESSION['id']}
						  ");
				unLockItem($id);
	$_pm['mysql'] -> query('COMMIT;');
				die("ʹ��{$keys[1]} �������ɹ�!");
			}	
			else{
				unLockItem($id);
	$_pm['mysql'] -> query('ROLLBACK;');					
			die("û���ڰ����з�����Ӧ����Ʒ!");	
			}
		}
	} // end ˫����
	####################�����Զ�ս������Ϊ��Ǯ���Ԫ����9.24̷�###################
		
		if($keys[0] == 'autofree') // ʹ�ý�Ǯ���Զ�ս����
		{
			if(is_array($rs))
			{
				// del a props for current 
				$_pm['mysql']->query("UPDATE userbag
								  SET sums=sums-1
							 WHERE id={$id} and uid={$_SESSION['id']} and sums>0
							 ");
				$user['sysautosum']+= intval($keys[1]);
				$_pm['mysql']->query("UPDATE player
								 SET sysautosum={$user['sysautosum']}
							 WHERE id={$_SESSION['id']}
							  ");
				unLockItem($id);
		$_pm['mysql'] -> query('COMMIT;');
			die("ʹ�� {$keys[1]} �ν�Ǯ���Զ�ս����ɹ�!");
			}
		}
		else if($keys[0] == "auto")
		{
			$_pm['mysql']->query("UPDATE userbag
								  SET sums=sums-1
							 WHERE id={$id} and uid={$_SESSION['id']} and sums>0
							 ");
			$user['maxautofitsum']+= intval($keys[1]);
			$_pm['mysql']->query("UPDATE player
								  SET maxautofitsum={$user['maxautofitsum']}
							 WHERE id={$_SESSION['id']}
							 ");
			unLockItem($id);
		$_pm['mysql'] -> query('COMMIT;');
		die("ʹ�� {$keys[1]} ��Ԫ�����Զ�ս����ɹ�!");
		}
				####################���������###################
}
else if($rs['varyname'] == 12) // �������͡�
{
	/**
	* Format: randitem:1308:1:80:2|1055:1:70:2|1141:1:80:2|744:1:30:2|211:1:40:1|213:1:40:1|871:1:40:1|870:1:20:1|1207:1:20:1|9:1:5:1|912:1:1:1
	@Memo: 1��ʾ��øõ��ߵ�ʱ��,�ᷢϵͳ����(2��ʾ���ᷢ����)
			��[�������]��һö����,�����ǲȵ��˹�ʺ��,��Ȼ�����E(��Ӧ����)��D(��Ӧ�ĵ�������)��
	*/
	//�ж��û������Ƿ�����
	$bagNum=0;
	
	if(is_array($bags))
	{
		foreach($bags as $x => $y)
		{
			if($y['sums']>0 and $y['zbing'] == 0) 
			{
				$bagNum++;		
			}
		}
	}

	if($bagNum >= $user['maxbag'])
	{
		unLockItem($id);
	$_pm['mysql'] -> query('ROLLBACK;');					
	die('���İ����������������������');
	}
	
	if(!empty($rs['requires']))
	{
		$requires = explode(":",$rs['requires']);
		if($requires[0] == 'lv')
		{
			if($bb['level'] < $requires[1])
			{
				unLockItem($id);
	$_pm['mysql'] -> query('ROLLBACK;');					
				die("��û�дﵽ��Ӧ�ĵȼ������ܿ����ñ��䣡");
			}
		}
	}
    $propsPatter = $rs['effect'];
	$arr = explode(",",$propsPatter);

	foreach($arr as $v)
	{
		$newarr = explode(":",$v);
		if($newarr[0] == "needkey")
		{
			if(is_array($bags))
			{
				foreach($bags as $y)
				{
					if($y['pid'] == $newarr[1] && $y['sums'] > 0)
					{
						$_pm['mysql']->query("UPDATE userbag
										     SET sums=sums-1
										   WHERE pid={$newarr[1]} and uid={$_SESSION['id']} and sums>0
										");
						$sign = 1;
					}
				}
				if($sign != 1)
				{
					unLockItem($id);
	$_pm['mysql'] -> query('ROLLBACK;');					
				die("��û�п��������Կ��!");
				}
			}
			else
			{
				unLockItem($id);
	$_pm['mysql'] -> query('ROLLBACK;');					
			die("��û�п��������Կ��!");
			}
		}
		else if($newarr[0] == 'giveitems')
		{
			$patter = str_replace('giveitems:', '', $rs['effect']);
			$propslist = explode(',', $patter);
			
			$retstr = '';
			if (is_array($propslist))
			{
				foreach ($propslist as $k => $v)
				{
					$inarr = explode(':', $v);		//	0=> ID, 2=> rand number, 1=> sum props
					
					
					if(is_array($inarr))
					{
						//foreach($inarr as $inarrs)
						//{
							$task = new task();
							$task->saveGetPropsMore($inarr[0],$inarr[1],$rs['pid']);
							$prs = $_pm['mysql']->getOneRecord("SELECT name FROM props WHERE id={$inarr[0]}");
							if(empty($retstr))
							{
								$retstr = '��õ��� '.$prs['name'].'&nbsp;'.$inarr[1].' ��';
							}
							else
							{
								$retstr .= ",".$prs['name'].'&nbsp;'.$inarr[1].' ��';
							}
						//}
					}
				} // end foreach
				// del props current bag.
				$_pm['mysql']->query("UPDATE userbag
										 SET sums=sums-1
									   WHERE id={$rs['id']} and uid={$_SESSION['id']} and sums>0
									");
				echo $retstr;
			}
		}
		elseif($newarr[0] == "randitem")
		{
			$patter = str_replace('randitem:', '', $v);
			$propslist = explode('|', $patter);
			$retstr = '';
			$task = new task();
			if (is_array($propslist))
			{
				foreach ($propslist as $k => $v)
				{
					$inarr = explode(':', $v);		//	0=> ID, 2=> rand number, 1=> sum props
					if (rand(1, intval($inarr[2])) == 1)	//  rand hits
					{
						$task = new task();
						$task->saveGetPropsMore($inarr[0],$inarr[1],$rs['pid']);
						$prs = $_pm['mysql']->getOneRecord("SELECT name FROM props WHERE id={$inarr[0]}");
						$retstr = '��õ��� '.$prs['name'].' '.$inarr[1].' ��';
		
						// del props current bag.
						$_pm['mysql']->query("UPDATE userbag
												 SET sums=sums-1
											   WHERE id={$rs['id']} and uid={$_SESSION['id']} and sums>0
											");
						
						if ($inarr[3] == 2)
						{
							$word = " ,ʹ��{$rs['name']},���˵صõ���ȻŮ���ף��,����� {$inarr[1]} ��{$prs['name']}";
							$task->saveGword($word);
						}
	
						echo $retstr;
						break;	
					}
				} // end foreach
			}
		}
	}
}

else if($rs['varyname'] == 2) // ������ 
{
		$arr = explode(':', $rs['effect']);
		if (!is_array($arr)) return false;
		if ($arr[0] == 'addexp') // ���Ӿ���
		{
			$eval = "\$exp=rand{$arr[1]};";
			eval($eval);
			$t = new task();
			$t->saveExps($exp);
			$tips .= '��þ���'.$exp;
		}
		else if($arr[0] == "addczl") // ��ӳɳ�
		{
			if ($user['mbid']!='' && $user['mbid']>0)
			{   
				$_pm['mysql']->query("UPDATE userbb
				                         SET czl=czl+{$arr[1]}
									   WHERE id={$user['mbid']}
									");
				$tips .= '��������������'.$arr[1].'�ɳ���';
			}
		}
		else if($arr[0] == "addac") // ���ӹ�����
		{
			if ($user['mbid']!='' && $user['mbid']>0)
			{   
				$_pm['mysql']->query("UPDATE userbb
				                         SET ac=ac+{$arr[1]}
									   WHERE id={$user['mbid']}
									");
				$tips .= '��������������'.$arr[1].'������';
			}
		}
		else if($arr[0] == "addmc") // ���ӷ���
		{
			if ($user['mbid']!='' && $user['mbid']>0)
			{   
				$_pm['mysql']->query("UPDATE userbb
				                         SET mc=mc+{$arr[1]}
									   WHERE id={$user['mbid']}
									");
				$tips .= '��������������'.$arr[1].'������';
			}
		}
		else if($arr[0] == "addhits") // ��������
		{
			if ($user['mbid']!='' && $user['mbid']>0)
			{   
				$_pm['mysql']->query("UPDATE userbb
				                         SET hits=hits+{$arr[1]}
									   WHERE id={$user['mbid']}
									");
				$tips .= '��������������'.$arr[1].'���У�';
			}
		}
		else if($arr[0] == "addmiss") // ��������
		{
			if ($user['mbid']!='' && $user['mbid']>0)
			{   
				$_pm['mysql']->query("UPDATE userbb
				                         SET miss=miss+{$arr[1]}
									   WHERE id={$user['mbid']}
									");
				$tips .= '��������������'.$arr[1].'���ܣ�';
			}
		}
		else if($arr[0] == "addhp") // ����������
		{
			if ($user['mbid']!='' && $user['mbid']>0)
			{   
				$_pm['mysql']->query("UPDATE userbb
				                         SET srchp=srchp+{$arr[1]}
									   WHERE id={$user['mbid']}
									");
				$tips .= '��������������'.$arr[1].'������';
			}
		}
		else if($arr[0] == "addmp") // ����ħ��
		{
			if ($user['mbid']!='' && $user['mbid']>0)
			{   
				$_pm['mysql']->query("UPDATE userbb
				                         SET srcmp=srcmp+{$arr[1]}
									   WHERE id={$user['mbid']}
									");
				$tips .= '��������������'.$arr[1].'ħ����';
			}
		}
		else if($arr[0] == "weiwang") // ��������
		{
			$_pm['mysql']->query("UPDATE player
				                         SET prestige=prestige+{$arr[1]}
									   WHERE id={$_SESSION['id']}
									");
			$tips .= '��������'.$arr[1].'�㣡';
		}
		$_pm['mysql']->query("UPDATE userbag
							     SET sums=sums-1
							   WHERE id={$rs['id']} and uid={$_SESSION['id']} and sums>0
							");
		echo $tips;
}

else if($rs['varyname'] == 16) // ͼֽ�ϳ���
{
	
	//�ж��û������Ƿ�����
	$bagNum=0;
	
	if(is_array($bags))
	{
		foreach($bags as $x => $y)
		{
			if($y['sums']>0 and $y['zbing'] == 0) 
			{
				$bagNum++;		
			}
		}
	}

	if($bagNum >= $user['maxbag'])
	{
		unLockItem($id);
	$_pm['mysql'] -> query('ROLLBACK;');					
	die('���İ����������������������');
	}

	$arr = explode(':', $rs['effect'],2);
	if ($arr[0] == 'hecheng') // ͼֽ�ϳ� ��ʽ��hecheng:(956:10|957:10|958:10|1025:1):1012:1
	{
		$rarr = explode('):', $arr[1]);
		$require = str_replace('(', '',$rarr[0]);
		$gets = explode(':', $rarr[1]);

		// Check props is exists?
		$need = explode('|', $require);
		foreach ($need as $k => $v)
		{   $t = explode(':', $v);
			$ex  = $_pm['mysql']->getOneRecord("SELECT sum(sums) as cnt 
												  FROM userbag 
												 WHERE pid ={$t['0']} and uid={$_SESSION['id']}");
            if ($ex['cnt'] < $t['1']){
				unLockItem($id);
	$_pm['mysql'] -> query('ROLLBACK;');					
		die('��Ĳ��ϲ��㣬�޷�������');
			}
		}

		// ok, then get props.
		$idlist='';
		for($i=0; $i<$gets['1'];$i++)
		{
			$idlist .= $idlist==''?	$gets[0]:','.$gets[0];
		}
 
		// clear props
		$delcount = 0;
		foreach ($need as $k => $v)
		{
			$t = explode(':', $v);
			$ret =$_pm['mysql']->getRecords("SELECT id,sums
											  FROM userbag 
											 WHERE pid ={$t['0']} and uid={$_SESSION['id']}
											 ORDER by sums
										  ");
			//Del props and count num
			if (is_array($ret))
			{
				foreach ($ret as $k => $v)
				{
					if ($v['sums']<1) continue;
					if ($delcount < $t[1]) $del = $t[1]-$delcount;
					else break;
					if ($v['sums']==$del)
					{
						// del record
						$_pm['mysql']->query("UPDATE userbag 
											   SET sums=0
											 WHERE id={$v['id']}
										   ");
						break;
					}
					else if ($v['sums']<$del)
					{
						// del record. $v['sums']
						$delcount+=$v['sums'];
						$_pm['mysql']->query("UPDATE userbag 
											   SET sums=0
											 WHERE id={$v['id']}
										   ");
					}
					else // ��ȥʣ����ֵ��update.
					{
						$v['sums'] = $v['sums']-$del;
						// update record.
						$_pm['mysql']->query("UPDATE userbag 
											   SET sums={$v['sums']}
											 WHERE id={$v['id']}
										  ");
						break;	
					}
				}
			} // end if
		} // end foreach
        // clear end
		$_pm['mysql']->query("UPDATE userbag
							     SET sums=sums-1
							   WHERE id={$rs['id']} and uid={$_SESSION['id']} and sums>0
							");
		// save props;
		$tsk = new task();
		$tsk->saveGetProps($idlist);
		unLockItem($id);
	$_pm['mysql'] -> query('COMMIT;');
		die('��ϲ��,�����ɹ�!�����һ����Ʒ!');
	}
}
else if ($rs['varyname'] == 15) // ������
{
	$allbb = $_pm['user']->getUserPetById($_SESSION['id']);
	$all = 0;
	if (is_array($allbb))
	{
		foreach($allbb as $x => $y)
		{
			if($y['muchang']==1) continue;
			$all++;
		}
		if ($all>=3){
			unLockItem($id);
	$_pm['mysql'] -> query('ROLLBACK;');					
			die("��ֻ��Я��3������,ʹ�õ���ʧ�ܣ�<br/>[ϵͳ�Ƽ�]�������԰�����Я���ı������뵽������");
		}
	}

	$arr = explode(':', $rs['effect']);
	if($arr[0] == "openpet") $newpetsid = $arr[1];
	
	// ���ݱ���ID�����ɱ������Բ��������ݸ���������ݰ���
	#########################################################################################
		// Get new bb info.
		$bb = $_pm['mem']->dataGet(array('k'	=>	MEM_BB_KEY,
								'v'	=>  "if(\$rs['id'] == '{$newpetsid}') \$ret=\$rs;"
						));

		$czl = getCzl($bb['czl']);

		// insert into userbb.
		//$bbid= $newid = mem_get_autoid($m, MEM_ORDER_KEY, 'userbb');
		
		$uinfo = $user;
		
		$_pm['mysql']->query("INSERT INTO userbb(
									   name,
									   uid,
									   username,
									   level,
									   wx,
									   ac,
									   mc,
									   srchp,
									   hp,
									   srcmp,
									   mp,
									   skillist,
									   stime,
									   nowexp,
									   lexp,
									   imgstand,
									   imgack,
									   imgdie,
									   hits,
									   miss,
									   speed,
									   kx,
									   remakelevel,
									   remakeid,
									   remakepid,
									   muchang,
									   czl,
									   headimg,
									   cardimg,
									   effectimg
									  )
					VALUES(
						   '{$bb['name']}',
						   '{$uinfo['id']}',
						   '{$uinfo['nickname']}',
						   '1',
						   '{$bb['wx']}',
						   '{$bb['ac']}',
						   '{$bb['mc']}',
						   '{$bb['hp']}',
						   '{$bb['hp']}',
						   '{$bb['mp']}',
						   '{$bb['mp']}',
						   '{$bb['skillist']}',
						   unix_timestamp(),
						   '{$bb['nowexp']}',
						   '100',
						   '{$bb['imgstand']}',
						   '{$bb['imgack']}',
						   '{$bb['imgdie']}',
						   '{$bb['hits']}',
						   '{$bb['miss']}',
						   '{$bb['speed']}',
						   '{$bb['kx']}',
						   '{$bb['remakelevel']}',
						   '{$bb['remakeid']}',
						   '{$bb['remakepid']}',
						   '0',
						   '{$czl}',
						   't{$bb['id']}.gif',
						   'k{$bb['id']}.gif',
						   'q{$bb['id']}.gif'
						   )
				  ");
		
		$jnall = split(",", $bb['skillist']);
		foreach($jnall as $a => $b)
		{
			$arr = split(":", $b);
			// Get jn info.
			$jn = $_pm['mem']->dataGet(array('k'	=>	MEM_SKILLSYS_KEY,
									'v'	=>  "if(\$rs['id'] == '{$arr[0]}') \$ret=\$rs;"
							));
			// #################################################				
			if ($jn['ackvalue']=='') continue; // ���Ӹ������ܡ�
			//##################################################
			
			$ack  = split(",", $jn['ackvalue']);
			$plus = split(",", $jn['plus']);
			$uhp  = split(",", $jn['uhp']);
			$ump  = split(",", $jn['ump']);
			$img  = split(",", $jn['imgeft']);

			// Insert userbb jn.	
			//$newid = mem_get_autoid($m, MEM_ORDER_KEY,'skill');
			/*��ȡ�ղ������ID��*/
			$newbb = $_pm['mysql']->getOneRecord("SELECT id 
										  FROM userbb
										 WHERE uid={$_SESSION['id']}
										 ORDER BY stime DESC
										 LIMIT 0,1			                                         
									  ");
			$bbid = $newbb['id'];

			$_pm['mysql']->query("INSERT INTO skill(bid,name,level,vary,wx,value,plus,img,uhp,ump,sid)
						VALUES(
							   '{$bbid}',
							   '{$jn['name']}',
							   '{$arr['1']}',
							   '{$jn['vary']}',
							   '{$jn['wx']}',
							   '{$ack[0]}',
							   '{$plus[0]}',
							   '{$img[0]}',
							   '{$uhp[0]}',
							   '{$ump[0]}',
							   '{$jn['id']}'
							  )
					  ");
	  }
	 
	  // sub props sum.
	  $_pm['mysql']->query("UPDATE userbag
					 SET sums=sums-1
				   WHERE id={$id} and uid={$_SESSION['id']} and sums>0
				");
	  echo "ʹ�õ��߳ɹ�!";
	//#######################################################################################
}
else if ($rs['varyname'] == 14) // �������ȡ����
{
	$arr = explode(':', $rs['effect']);
	if($arr[0] == "jg")
	{
		$sql = "SELECT jgvalue FROM battlefield_user WHERE uid = {$_SESSION['id']}";
		$row = $_pm['mysql'] -> getOneRecord($sql);
		if(!is_array($row))
		{
			unLockItem($id);
	$_pm['mysql'] -> query('ROLLBACK;');					
			die("��Ŀǰû�вμ�ս���������ʹ�ô˵��ߣ�");
		}
		$_pm['mysql']->query("UPDATE battlefield_user
		                         SET jgvalue=jgvalue+{$arr[1]}
							   WHERE uid={$_SESSION['id']}
							");
		 // sub props sum.
	  $_pm['mysql']->query("UPDATE userbag
						   SET sums=sums-1
						   WHERE id={$id} and uid={$_SESSION['id']} and sums>0
						  ");
		echo "��ϲ����ʹ�õ��߳ɹ���������� {$arr[1]} �������";
	}
	else {echo '����ʹ��ʧ�ܣ�';}
	
}
$_pm['mysql'] -> query('COMMIT;');

unLockItem($id);
$_pm['mem']->memClose();
unset($m, $u, $db, $user, $bags, $rs);
?>