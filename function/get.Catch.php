<?php
/**
*@Version: %version%
*@Copyright: %copyright%
*@Author: %author%

*@Write Date: 2008.05.19
*@Update Date: 2008.10.28
*@Usage: study skill of user bb.
*@Memo:
  0: ���ݴ���
  ��׽���ܷ����޸ġ�
*/

header('Content-Type:text/html;charset=GBK');
require_once('../config/config.game.php');
define(MEM_FIGHT_KEY, $_SESSION['id'] . 'fight');

$arrobj = new arrays();
secStart($_pm['mem']);

$bid =intval($_REQUEST['pid']); // bag props id.table:userbag.ʹ�õ���ID��������ID��

if( !is_int($bid) || $bid<1 ) die('0');

$user	 = $_pm['user']->getUserById($_SESSION['id']);//�û���Ϣ
$sp	     = $_pm['user']->getUserItemById($_SESSION['id'],$bid);//�û�������Ϣ
$allbb   = $_pm['user']->getUserPetById($_SESSION['id']);//�û�������Ϣ
$memgpcid = unserialize($_pm['mem']->get('db_gpcid'));
$mempropsid = unserialize($_pm['mem']->get('db_propsid'));

$all = 0;
if (is_array($allbb))
{
	foreach($allbb as $x => $y)
	{
		if($y['muchang']==1) continue;
		$all++;
	}
	if ($all>=3) die("6");//��Я���ı��������Ѵ����
}

$test = $_SESSION['fight'.$_SESSION['id']];


if(isset($_SESSION['catch_gw_info'])&&$_SESSION['catch_gw_info']==$_SESSION['fight'.$_SESSION['id']]['gid'])
{
	stopUser2(52);//,true
	die('0');
}

$gs = $memgpcid[$test['gid']];
/*$gs = $_pm['mem']->dataGet(array('k'	=>	MEM_GPC_KEY,
			 		    'v'	=>  "if(\$rs['id'] == '{$test['gid']}') \$ret=\$rs;"
				 ));*/
				 //��ǰ����Ĺ�������
$bb = $test;
if (!is_array($bb) || !is_array($gs)) die('-1');
else
{
	$bbrs = $arrobj->dataGet(array('k'	=>	MEM_BB_KEY,
			 		    	  'v'	=>  "if(\$rs['uid'] == '{$_SESSION['id']}' && \$rs['id']=='{$bb['bid']}') \$ret=\$rs;"
					 			),//��ǰ����ֵĳ�������
							$allbb
						   );
	if (!is_array($bbrs)) $bbrs['level']=0;
}

if (is_array($sp))
{
	
	$_SESSION['catch_gw_info'] = $test['gid'];
	
	$prs = $sp;//������Ϣ��
	
	// ��׽���� ��Ҫ����׽�Ĺ�����Ϣ����ȷ����ʼ���㡣
	if (is_array($prs) && is_array($gs))
	{
	
		if($prs['sums'] < 1)
		{
			die("20");
		}
		if($bid != $prs['id'])
		{
			die("20");
		}
		// Start count...
		// ʵ�ʲ�׽��=[���ﲶ׽ֵ/��100����ҳ��������ȼ�֮�]*��1�����ﵱǰHPֵ/�������HPֵ��*100%+��׽���߸��Ӳ�׽��
		
		//ʵ�ʲ�׽�ʣ������ﲶ׽ֵ/100��*��1�����ﵱǰHPֵ/�������HPֵ��*100%+��׽���߸��Ӳ�׽�� 
		
		// �����ʽ��

		$pv = explode(':', $prs['effect']);
		
		if(strtolower($pv[0])=='getitems')//��ȡװ��
		{
			$params = explode(",",$pv[1]);
			$theGPCs = explode("|",$params[0]);
			/*if(!in_array($_SESSION['fight'.$_SESSION['id']]['gid'],$theGPCs))
			{
				die("12");
			}*/
			
			
			
			
			$pzl = ($gs['catchv']/100)*(1-$bb['hp']/$gs['hp']);
			
			$randNum = $pzl*100;
			$a = $randNum==0?10000:intval(100/$randNum);
			$nvl = rand(1,$a);
			if($nvl == 1) 
			{
				$msg = "";
				$strarr = explode(",",$prs['effect']);
				$items = explode("|",$strarr[1]);
				foreach($items as $v)
				{
					$proparr = explode(":",$v);
					$randnum = rand(1,$proparr[1]);
					if($randnum == 1)
					{
						
						$prs = $mempropsid[$proparr[0]];
						/*$prs = $_pm['mem']->dataGet(array('k' => MEM_PROPS_KEY, 
													 'v' => "if(\$rs['id'] == '{$proparr[0]}') \$ret=\$rs;"
										  ));*/
										 
						
						
						$task = new task();
						$task->saveGetPropsMore($proparr[0],$proparr[2]);
						if($proparr[3] == "2")
						{
							
							$gpc = $memgpcid[$proparr[0]];
							/*$gpc = $_pm['mem']->dataGet(array('k' => MEM_GPC_KEY, 
													 'v' => "if(\$rs['id'] == '{$proparr[0]}') \$ret=\$rs;"
										  ));*/
							$task->saveGword("�� {$gpc['name']} ���ϳɹ��ķ����� {$prs['name']} {$proparr[2]} ����");
						}
						$newstr = "��ϲ���õ� {$prs['name']} {$proparr[2]} ����";
						$_pm['mysql']->query("UPDATE userbag
						SET sums=abs(sums-1)
						WHERE id=$bid and sums > 0
						");
						die($newstr);
						break;
					}
				}
			}
			else{
			$_pm['mysql']->query("UPDATE userbag
					SET sums=abs(sums-1)
					WHERE id=$bid and sums > 0
					");
			}
		}
		else if(strtolower($pv[0])=='get')//��ȡװ��
		{
			$theGPCs = explode("|",$pv[1]);			
			
			if(!in_array($_SESSION['fight'.$_SESSION['id']]['gid'],$theGPCs))
			{
				die("12");
			}
			
			$pvv = str_replace('%','',$pv[2])/100;
			
			$pzl = ($gs['catchv']/100)*(1-$bb['hp']/$gs['hp'])+$pvv;
			
			$randNum = $pzl*100;
			$a = intval(100/$randNum);
			$nvl = rand(1,$a);
			if($nvl == 1) // Catch ok.
			{
				//������Ʒ��ȡ����ʽ������ID�����ʷ�Χ��
				$prpid = intval($pv[4]);
				$okidlist = $drop = "";
				if ($prpid === false || $prpid == 0 || $prpid == '') $drop = '��';
				else
				{
					$rarr = array($prpid);
					foreach ($rarr as $k => $v)
					{
						
						/*$prs = $_pm['mem']->dataGet(array('k' => MEM_PROPS_KEY, 
												 'v' => "if(\$rs['id'] == '{$v}') \$ret=\$rs;"
									  ));*/

						$prs = $mempropsid[$v];
						if( is_array($prs) )
						{
							$drop .= $prs['name'].',';
							$okidlist .= $v.',';
						} 
					}// end foreach.
					$drop = substr($drop, 0, -1);
					$okidlist = substr($okidlist, 0, -1);
					$_bag		 = $_pm['user']->getUserBagById($_SESSION['id']);
					saveGetProps($okidlist);
				}
				
				//������			
				if($pv['3'] == 2)
				{
					$task = new task();
					$task->saveGword("�ɹ��Ļ�ȡ��: ".$drop."��̫ˬ�ˣ�");
				}
				
				$_pm['mysql']->query("UPDATE userbag
				SET sums=abs(sums-1)
				WHERE id=$bid and sums > 0
				");
				die('15');
			}else{
				$_pm['mysql']->query("UPDATE userbag
				SET sums=abs(sums-1)
				WHERE id=$bid and sums > 0
				");
				die('13');
			}
		}
		else if(strtolower($pv[0])=='catch')
		{
			if ($gs['catchid'] == 0) die('3'); // �˹ֲ��ܲ�׽
			$pvv = str_replace('%','',$pv[2])/100;
			$gwidarr = explode("|",$pv[1]);
			if(!in_array($gs['id'],$gwidarr))
			{
				die("7");//���ܲ�׽�˱���
			}
			
			
			
			$pzl = ($gs['catchv']/100)*(1-$bb['hp']/$gs['hp'])+$pvv;
			
			$randNum = $pzl*100;
			$nvl = rand(1, intval(100/$randNum));
			
			
			
			//$nvl = 1;
			
			if($nvl == 1) // Catch ok.
			{
				$newpetsid = $gs['catchid'];
				// Get new bb info.
						$membbid = unserialize($_pm['mem']->get('db_bbid'));
						$bb = $membbid[$newpetsid];
						/*$bb = $_pm['mem']->dataGet(array('k'	=>	MEM_BB_KEY,
						'v'	=>  "if(\$rs['id'] == '{$newpetsid}') \$ret=\$rs;"
						),
						$allbb
				 );*/
				if ($gs['wx'] != $bb['wx']) die('2');
				$czl = getCzl($bb['czl']);
				
				// insert into userbb.
				//$bbid= $newid = mem_get_autoid($m, MEM_ORDER_KEY, 'userbb');
				
				$uinfo = $user;
				$_pm['mysql']->query("INSERT INTO userbb(name,uid,username,level,wx,ac,mc,srchp,hp,srcmp,mp,skillist,stime,nowexp,
						lexp,imgstand,imgack,imgdie,hits,miss,speed,kx,remakelevel,remakeid,remakepid,czl,headimg,cardimg,effectimg)
				VALUES('{$bb['name']}','{$uinfo['id']}','{$uinfo['nickname']}','1','{$bb['wx']}',
				   '{$bb['ac']}','{$bb['mc']}','{$bb['hp']}','{$bb['hp']}','{$bb['mp']}','{$bb['mp']}','{$bb['skillist']}',unix_timestamp(),
				  '{$bb['nowexp']}','100','{$bb['imgstand']}','{$bb['imgack']}','{$bb['imgdie']}',
				   '{$bb['hits']}','{$bb['miss']}','{$bb['speed']}','{$bb['kx']}','{$bb['remakelevel']}',
				   '{$bb['remakeid']}','{$bb['remakepid']}','{$czl}','{$bb['headimg']}','{$bb['cardimg']}','{$bb['effectimg']}')
				");
				//������			
				if($pv['3'] == 2)
				{
					$task = new task();
					$task->saveGword("�ɹ��Ĳ�׽���� {$bb['name']} ��̫�в��ˣ�");
				}
				/*��ȡ�ղ������ID��*/
				$newbb = $_pm['mysql']->getOneRecord("SELECT id 
							  FROM userbb
							 WHERE uid={$_SESSION['id']}
							 ORDER BY stime DESC
							 LIMIT 0,1			                                         
						  ");
				$bbid = $newbb['id'];
				
				//�޸�ֻ����һ�ּ��ܵ�bug���ܣ�����Ѫ����
				$arr = split(",", $bb['skillist']);
				foreach($arr as $av)
				{
					if(empty($av))
					{
						continue;
					}
					$newarr = explode(":",$av);
					if(empty($newarr[0]))
					{
						continue;
					}
					$memskillsysid = unserialize($_pm['mem']->get('db_skillsysid'));
					$jn = $memskillsysid[$newarr[0]];
					/*$jn = $_pm['mem']->dataGet(array('k'	=>	MEM_SKILLSYS_KEY,
						'v'	=>  "if(\$rs['id'] == '{$newarr[0]}') \$ret=\$rs;"
					));*/
					$ack  = split(",", $jn['ackvalue']);
					$plus = split(",", $jn['plus']);
					$uhp  = split(",", $jn['uhp']);
					$ump  = split(",", $jn['ump']);
					$img = split(",",$jn['imgeft']);
					$_pm['mysql']->query("INSERT INTO skill(bid,name,level,vary,wx,value,plus,img,uhp,ump,sid)
					VALUES({$bbid}, '{$jn['name']}','{$newarr['1']}','{$jn['vary']}','{$jn['wx']}','{$ack['0']}','{$plus['0']}','{$img['0']}',{$uhp['0']},{$ump['0']},{$jn['id']})
					");
				}
				// Get jn info.
				/*$jn = $_pm['mem']->dataGet(array('k'	=>	MEM_SKILLSYS_KEY,
						'v'	=>  "if(\$rs['id'] == '{$arr[0]}') \$ret=\$rs;"
				));
				$ack  = split(",", $jn['ackvalue']);
				$plus = split(",", $jn['plus']);
				$uhp  = split(",", $jn['uhp']);
				$ump  = split(",", $jn['ump']);
				��ȡ�ղ������ID��
				$newbb = $_pm['mysql']->getOneRecord("SELECT id 
							  FROM userbb
							 WHERE uid={$_SESSION['id']}
							 ORDER BY stime DESC
							 LIMIT 0,1			                                         
						  ");
				$bbid = $newbb['id'];
				
				// Insert userbb jn.	
				//$newid = mem_get_autoid($m, MEM_ORDER_KEY,'skill');
				echo "INSERT INTO skill(bid,name,level,vary,wx,value,plus,img,uhp,ump,sid)
				VALUES({$bbid}, '{$jn['name']}','{$arr['1']}','{$jn['vary']}','{$jn['wx']}','{$ack['0']}','{$plus['0']}','{$jn['img']}',{$uhp['0']},{$ump['0']},{$jn['id']})
				";exit;
				$_pm['mysql']->query("INSERT INTO skill(bid,name,level,vary,wx,value,plus,img,uhp,ump,sid)
				VALUES({$bbid}, '{$jn['name']}','{$arr['1']}','{$jn['vary']}','{$jn['wx']}','{$ack['0']}','{$plus['0']}','{$jn['img']}',{$uhp['0']},{$ump['0']},{$jn['id']})
				");*/
				//��ȥ������
				$_pm['mysql']->query("UPDATE userbag
				SET sums=abs(sums-1)
				WHERE id={$prs['id']} and sums > 0
				");
				//$_pm['user']->updateMemUserbb($_SESSION['id']);
				//$_pm['user']->updateMemUsersk($_SESSION['id']);
				die('10');
			}
			else
			{ // Clear props.
				$_pm['mysql']->query("UPDATE userbag
				   SET sums=abs(sums-1)
				 WHERE id={$prs['id']} and sums > 0
				");
				//$_pm['user']->updateMemUserbag($_SESSION['id']);
			} // ��׽����̫�͡�	 
		}
	}
}
$_pm['mem']->memClose();
echo "0";


/**
* @Usage: �洢�û��õ��ĵ��ߵ��û�����.
* @Param: String, format: 1,2,3
* @Logic: 
  ����û������д���Ʒ����������۵���ֱ���ۼӣ���������¼�¼��
  >>������Ʒ˵���ֶ�
*/
function saveGetProps($idlist)
{
	if ($idlist == '' or $idlist == 0) return false;
	global $_pm,$_bag,$user;
	$arrobj = new arrays();

	$l=0;
	if (is_array($_bag))
	{
		foreach ($_bag as $x => $y)
		{
			if ($y['sums']>0 && $y['zbing']==0) $l++;
		}
	}
	if ($l >= $user['maxbag']) return false;	
	
	$arr = split(',', $idlist);
	foreach ($arr as $k => $v)
	{
		$rs = $arrobj->dataGet(array('k' => MEM_USERBAG_KEY, 
									 'v' => "if(\$rs['uid']=='{$_SESSION['id']}' && \$rs['pid']=='{$v}') \$ret=\$rs;"
									 ),
								   $_bag
							  ); 
		
		//$rs = $_pm['mysql']->getOneRecord("SELECT * FROM userbag WHERE uid={$_SESSION['id']} and pid={$v}");
		if (is_array($rs))
		{
			if ($rs['vary'] == 1) // ���۵�����.
			{
				$_pm['mysql']->query("UPDATE userbag
							   SET sums=sums+1
							 WHERE id={$rs['id']}
						  ");
			}
			else
			{
				//$newid = mem_get_autoid($m, MEM_ORDER_KEY, 'userbag');
				$_pm['mysql']->query("INSERT INTO userbag(uid,pid,sell,vary,sums,stime)
							VALUES(
								   {$user['id']},
								   {$v},
								   {$rs['sell']},
								   2,
								   1,
								   unix_timestamp()
								  );
						  ");
				 $l++;
			}
		}
		else{
			$mempropsid = unserialize($_pm['mem']->get('db_propsid'));
			$rs = $mempropsid[$v];
			/*$rs = $_pm['mem']->dataGet(array('k' => MEM_PROPS_KEY, 
								    'v' => "if(\$rs['id'] == '{$v}') \$ret=\$rs;"
								  ));*/
			if (is_array($rs))
			{
				//$newid = mem_get_autoid($m, MEM_ORDER_KEY, 'userbag');
				$_pm['mysql']->query("INSERT INTO userbag(uid,pid,sell,vary,sums,stime)
							VALUES(
								   {$user['id']},
								   {$v},
								   {$rs['sell']},
								   {$rs['vary']},
								   1,
								   unix_timestamp()
								  );
						  ");
				 $l++;
			}	
		}		
		unset($rs);
		// ����Ƿ񳬳�������
		if ($l >= $user['maxbag']) return false;
	}	
}
?>
