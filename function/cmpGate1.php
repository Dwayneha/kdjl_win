<?php
/**
*@Version: %version%
*@Copyright: %copyright%
*@Author: %author%

*@Write Date: 2008.07.30
*@Update Date: 
*@Usage: ����ϳ�ϵͳ��
*@Memo: ����ϳ�ϵͳ��
:����=[�����������ݿ�����+ȡ��������������5%��+ȡ��������������1%��]*(100%+���߸�������%)
:ʵ�ʳɳ���=ȡ1λС��{[ȡһλС����������ɳ�*90%��+ȡһλС����������ɳ�*10%��]* (100%+���߸�������%)}
*/
require_once('../config/config.game.php');
secStart($_pm['mem']);

$ap	    = intval($_REQUEST['ap']);  // table userbb->id
$bp 	= intval($_REQUEST['bp']);  // table userbb->id
$p1 	= intval($_REQUEST['p1']);  // table userbag->id
$p2 	= intval($_REQUEST['p2']);  // table userbag->id
$srctime = 15;
#################����һ�����ʱ��################
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
		die("11");//û�дﵽ���ʱ��
	}
	else
	{
		$_SESSION['time'.$_SESSION['id']] = time();
	}
}
##################�������������#################
if ($ap<0 || $bp<0) die('0');
if ($p1<0) $p1 = 0;
if ($p2<0) $p2 = 0;



$user		= $_pm['user']->getUserById($_SESSION['id']);
$userbb		= $_pm['user']->getUserPetById($_SESSION['id']);
if(!empty($p1))
{
	$pp1 = $_pm['user']->getUserItemById($_SESSION['id'],$p1);
}
if(!empty($p2))
{
	$pp2 = $_pm['user']->getUserItemById($_SESSION['id'],$p2);
}
$log = '';

if ( is_array($userbb))
{
	foreach ($userbb as $key => $rs)
	{
		if ($rs['id']==$ap && $rs['level']>=40) // From bb base find user current bb.
		{
			$app = $rs;
		}
		if ($rs['id']== $bp && $rs['level']>=40)
		{
			$bpp = $rs;
		}
	}
    unset($rs);
	
	
	//mysql5BUG   ̷� 2009.2.24
	if($p1 == $p2 && $p1 != 0)
	{
		if($pp1['sums'] < 2)
		{
			die("100");
		}
	}

	if (!is_array($app) || !is_array($bpp) || ($app['id'] == $bpp['id'])) die('1'); //û�ж�Ӧ�ĳ��
	
	// ����Ƿ����㹫ʽ��
	$ars = $_pm['mem']->dataGet(array('k' => MEM_BB_KEY, 
										 'v' => "if(\$rs['name'] == '{$app['name']}') \$ret=\$rs;"
							  ));
	$brs = $_pm['mem']->dataGet(array('k' => MEM_BB_KEY, 
										 'v' => "if(\$rs['name'] == '{$bpp['name']}') \$ret=\$rs;"
							  ));
	
	$cmprs = $_pm['mysql']->getOneRecord("SELECT * 
											FROM merge
										   WHERE aid = {$ars['id']} and bid={$brs['id']}
										   LIMIT 0,1
	                                    ");
    if (!is_array($cmprs)) die('2');	//���ܺϳɣ�								
					  
	
	// �жϽ�����ģ�
	$money=0;
	if (is_array($pp1))
	{
		$one = explode(',', $pp1['effect']);
		foreach ($one as $a => $b)
		{
			$arr = explode(':', $b);
			$money+=$arr[count($arr)-1];
		}
	}
	if (is_array($pp2))
	{
		$one = explode(',', $pp2['effect']);
		foreach ($one as $a => $b)
		{
			$arr = explode(':', $b);
			$money+=$arr[count($arr)-1];
		}
	}
	
	$money = ($app['level']+$bpp['level'])*1000+$money;
	if ($user['money'] < $money) die('3');	//	��Ҳ��㡣
		
	$propseff = getEffect($pp1, $pp2);
	
	
	// �õ��ɹ���.
	$sus = getSuccess($propseff,$app['czl'],$pp1['pid'],$pp2['pid']);

	$czl = bbczl($app,$bpp,$pp1,$pp2);

	if ($sus) // �ϳɳɹ���a,b������ʧ���õ��µĳ��$cmprs=> �õ���ر�����Ϣ��
	{
		// �ı����Եط�Ϊ:
		if ($sus == 1) $newbid = $cmprs['maid'];
		if ($sus == 2) $newbid = $cmprs['mbid'];
		
		$brs = $_pm['mysql']->getOneRecord("SELECT * 
											  FROM  bb
											 WHERE id={$newbid}
											 LIMIT 0,1
										  ");
										  
		if (!is_array($brs))
		{
			die('10'); // ���ݴ���
		}
		// �ı��������:
		makebb($brs);
		$cstatus = 2;
	}
	else // ���û����ص��߽��а󶨣�������ʧ
	{
		$cstatus = 1;
	}
	
	$user['money'] = $user['money']-$money;		// �����û����.
	$_pm['mysql']->query("UPDATE player
						     SET money='{$user['money']}'
					 	   WHERE id={$_SESSION['id']}
				  		");
	// ��¼��־��
	$log .= "�ϳɽ����".($cstatus==1?"ʧ��":"�ɹ�")."\n";
	$log .= "�ϳɵ��ߣ�1:".$pp1['pid'].' 2:'.$pp2['pid']."\n";

	//######### del props Start.##################
	delProps();
	############# del props end.#####################

	if ($cstatus == 1) //������ʧ��
	{
		$del = 1;
		$log .= '�ϳɵ�����ϸ��';
		if(is_array($propseff))
		{
			foreach ($propseff as $m => $n)
			{
				$log .= $n['shbb']."-";
				if ($n['shbb'] === true)
				{
					$del = 0;
					break;
				}
			}
		}
		
		if ($del == 1)
		{
			clearBB($bpp);
		}
		$log = addslashes($log);
		// �ϳ�ʧ�ܼ�¼�㣺
		$_pm['mysql']->query("INSERT INTO gamelog(ptime,seller,buyer,pnote,vary)
		                      VALUES(unix_timestamp(),'{$_SESSION['id']}','{$_SESSION['id']}','{$log}',2)
							");
		die('6');
	}
	else if($cstatus == 2) // �ɹ���
	{
		/*
		$_pm['mem']->set(array('k'=>MEM_SYSWORD_KEY, 
							   'v'=>'[ϵͳ����]��ϲ��� '.$user['nickname'].'�ɹ��ĺϳ���һֻ['.$cmprs['name'].'],����̫������!'));
		*/
		$msg_key = 'chatMsgList';
		$nowMsgList = unserialize($_pm['mem']->get($msg_key));
		$arr = split('linend', $nowMsgList);
		if( count($arr)>20 ) // cear old
		{
			$arrt = array_shift($arr);
		}
		$newstr = '<font color=red>[ϵͳ����]��ϲ��� '.$user['nickname'].' �ɹ��ĺϳ���һֻ['.$brs['name'].'],����̫������!</font>';
		$str = '�³������֣�'.$brs['name'].',ʹ����Ʒ1��'.$pp1['name'].',ʹ����Ʒ2��'.$pp2['name'].',���'.$app['name'].'-'.$bpp['name'].'';
		$_pm['mysql']->query("INSERT INTO gamelog(ptime,seller,buyer,pnote,vary)
		                      VALUES(unix_timestamp(),'{$_SESSION['id']}','{$_SESSION['id']}','{$str}',4)
							");
		
		foreach($arr as $k=>$v)
		{
			$retstr .= $v.'linend';
		}

		$retstr = $retstr.$newstr;
		$_pm['mem']->set( array('k'=>$msg_key, 'v'=>$retstr) ); // default ten min.

		
		clearBB($app); // del pets master
		clearBB($bpp); // del pets other
		die('5');
	}
}
else die('000');
$_pm['mem']->memClose();
// Logic code end.



/**
* @Usage: �����µĳ��
* @Param: array -> $bb.
* @Return: Void(0);
*/
function makebb($bb)
{
	global $app,$bpp,$pp1,$pp2,$user,$_pm,$propseff;
	$czl = bbczl($app,$bpp,$pp1,$pp2);

	// ac,luck,mc,hit,miss,speed,hp,mp,shbb; 
	$bb['ac']	= getPa($bb['ac'], $app['ac'], $bpp['ac'] ,getPlus($propseff, 'ac'));  #### ��ʱû�м�����߸������ԡ�
    $bb['mc']	= getPa($bb['mc'], $app['mc'], $bpp['mc'] ,getPlus($propseff, 'mc'));
	$bb['hits']	= getPa($bb['hits'], $app['hits'], $bpp['hits'] ,getPlus($propseff, 'hits'));
    $bb['miss']	= getPa($bb['miss'], $app['miss'], $bpp['miss'] ,getPlus($propseff, 'miss'));
	$bb['speed']= getPa($bb['speed'], $app['speed'], $bpp['speed'] ,getPlus($propseff, 'speed'));
	$bb['hp']	= getPa($bb['hp'], $app['hp'], $bpp['hp'] ,getPlus($propseff, 'hp'));
	$bb['mp']	= getPa($bb['mp'], $app['mp'], $bpp['mp'] ,getPlus($propseff, 'mp'));


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
				  
		$_pm['mysql'] -> query("UPDATE player SET mbid = {$bbid} WHERE id = {$_SESSION['id']}");
				  
   }
}

/**
* @Usage: ɾ��һ������;
* @Param: Array -> $bb.
* @Return: Void(0);
*/
function clearBB($bb)
{
	global $_pm,$log;
	$id = $bb['id'];
	
	foreach ($bb as $k => $v)
	{
		$log .= $k.'=>'.$v.'-';
	}
	
	// del sk. 
	$_pm['mysql']->query("DELETE FROM skill
				 WHERE bid={$id}
			  ");
			  
	// del zb.
	$_pm['mysql']->query("DELETE FROM userbag
				 WHERE uid={$_SESSION['id']} and zbpets={$id}
			  ");
	// del bb.
	$_pm['mysql']->query("DELETE FROM userbb
				 WHERE uid={$_SESSION['id']} and id={$id}
			  ");
}

/**
* @Param: ����a,b�����ԡ�
* @Return: �������ĳɳ��ʡ�
  czl:ʵ�ʳɳ���=��Ӧ�����������ݿ�ɳ�������+ȡ1λС��{[ȡһλС����������ɳ�*������ȼ�/120��+ȡһλС����������ɳ�*������ȼ�/240��+rand(������ɳ�/10,������ɳ�/10)]* (100%+���߸�������%)}
  rand(������ɳ�/10,������ɳ�/10)
  ��˼��:ȡ������ĳɳ�ֵ/10��������ɳ�ֵ/10�������
  �縱����ɳ�10,������ɳ�20
  ��: rand(1,2)
*/
function bbczl($a, $b, $pp1, $pp2)
{
	global $brs; // ���Ͽ��г������ԡ�
	$czlplus = '';
	// ��һ�����ߡ�
	if (is_array($pp1))
	{
		$one = explode(',', $pp1['effect']);
		foreach ($one as $x => $y)
		{
			$arr = explode(':', $y);
			if ($arr[0] == 'addczl')
			{
				$czlplus = str_replace('%','',$arr[1])/100;
			}
		}
	}
    // �ڶ������ߡ�
	if (is_array($pp2))
	{
		$one = explode(',', $pp2['effect']);
		foreach ($one as $x => $y)
		{
			$arr = explode(':', $y);
			if ($arr[0] == 'addczl')
			{
				$czlplus += str_replace('%','',$arr[1])/100;
			}
		}
	}
	
	$czl = getCzl($brs['czl'])+round( (round(( ($a['czl']*$a['level'])/120),1) + round(( ($b['czl']*$b['level'])/240),1) + 
		          rand(round($b['czl']/10,1)*10, round($a['czl']/10,1)*10)/10 )*(1+$czlplus),1);
	if($czl > 50)
	{
		$czl = 50.0;
	}
	return $czl;
}

/**
*@Usage: ��ȡ�ϳ�����ӵ��ߵ�����Ч��
*@Param: array -> $pp1, array -> $pp2.
*@Return: array.
*/
function getEffect($pp1, $pp2)
{
	$i = 0;
	if (is_array($pp1))
	{
		$one = explode(',', $pp1['effect']);
		foreach ($one as $a => $b)
		{
			$arr = explode(':', $b);
			$ret[$i++] = getvary(trim($arr[0]), $arr);
		}
	}
	if (is_array($pp2))
	{
		$one = explode(',', $pp2['effect']);
		foreach ($one as $a => $b)
		{
			$arr = explode(':', $b);
			$ret[$i++] = getvary(trim($arr[0]), $arr);
		}
	}
	// ���Ч����
	return $ret;
}

/**
* @Usage: ���ص�һЧ����
* @Param: string->$vary, array->$value.
* @Return: array.
*/
function getvary($vary, $value)
{
	switch($vary)
	{   // ac,luck,mc,hit,miss,speed,hp,mp,shbb;
		case "addac": $ret['ac'] = str_replace('%','',$value[1])/100;break;
		case "luck": $ret['luck'] = $value['1'].':'.(str_replace('%','',$value[2])/100);break;
		case "addmc": $ret['mc'] = str_replace('%','',$value[1])/100;break;
		case "addhit": $ret['hit'] = str_replace('%','',$value[1])/100;break;
		case "addmiss": $ret['miss'] = str_replace('%','',$value[1])/100;break;
		case "addspeed": $ret['speed'] = str_replace('%','',$value[1])/100;break;
		case "addhp": $ret['hp'] = str_replace('%','',$value[1])/100;break;
		case "addmp": $ret['mp'] = str_replace('%','',$value[1])/100;break;
		case "shbb": $ret['shbb'] = true;break;
	}
	return $ret;
}

/*
������+������=���(����A������B,��Cʧ��)
��������ļ���:
����A��25%
����B��5%
ʧ�ܣ�70%
@Param: ���߸������ԡ�
@Return: 1=>a 2=>b 0->fail
�ɳ�����>=1��<5ʱ �ϳɳɹ���Ϊ100%
�ɳ�����>=5��<10ʱ �ϳɳɹ���Ϊ�̶�ֵ 50%
�ɳ�����>=10��<15ʱ �ϳɳɹ���Ϊ�̶�ֵ20%
�ɳ�����>=15ʱ �ϳɳɹ���ʹ����ǰ�ļ��ʡ�

*/
function getSuccess($props,$czl,$id1,$id2)
{
	if(!empty($id1) && $id1 == 872)
	{
		return 2;
		exit;
	}
	if(!empty($id2) && $id2 == 872)
	{
		return 2;
		exit;
	}
	if($czl >= 1 && $czl < 5)
	{
		$num = rand(1,2);
		return $num;
	}
	else if($czl >= 5 && $czl < 10)
	{
		$sus = rand(1,2);
		if($sus == 1)
		{
			$num = rand(1,2);
		}
		else
		{
			$num = 0;
		}
		return $num;
	}
	else if($czl >= 10 && $czl < 15)
	{
		$sus = rand(1,5);
		if($sus == 1)
		{
			$num = rand(1,2);
		}
		else
		{
			$num = 0;
		}
		return $num;
	}
	else
	{
		$lucka = 0;
		$luckb = 0;
		if (is_array($props))
		{
			foreach ($props as $k => $v)
			{
				if ($v['luck']!='')
				{
					$arr = explode(':', $v['luck']);
					if (strtolower($arr[0]) == 'b') $luckb += $arr[1];
					else if(strtolower($arr[0]) == 'a') $lucka += $arr[1];
				}
			}
		}
		
		$arand = round(($lucka+0.80),2)*100;
		$brand = round(($luckb+0.60),2)*100;
		$af = $bf = 0;
		$arand = $arand>100?100:$arand;
		$brand = $brand>100?100:$brand;
	
	
		$okarand = rand($arand,100);
		$okbrand = rand($brand,100);
		
		if ($okarand == $arand ) $af = 1;
		if ($okbrand == $brand ) $bf = 1;
	
		if ($af == 1) return 1;
		else if($bf == 1) return 2;
		else return 0;
	}
}
/*
*@Usage:����ϳɺ�ĳ��ﵥһ���ԡ�
* a,b,p=> $props attrib.
*@Return: int.
*@Memo ����=[�����������ݿ�����+ȡ��������������*������ȼ�/400��+ȡ��������������*������ȼ�/800��]*(100%+���߸�������%)
*/
function getPa($old, $a, $b ,$p)
{	
	global $app,$bpp;
	if ($p == '' || $p<=0) $p=1;
	else $p = 1+$p;

	return intval(($old+(intval($a*$app['level']/400)+intval($b*$bpp['level']/800)))*$p);
}

/**
*@Usage: ��úϳɼ�����ߵĸ�������ֵ��
*@ Return: float.
*/
function getPlus($parr, $name)
{
	$ret = 0;
	if (!is_array($parr)) return 0;
	foreach ($parr as $k => $rs)
	{
		if ($rs[$name]!='' && $rs[$name]>0)
		{
			$ret +=$rs[$name];
		}
	}
	return $ret;
}

/**
*@Usage: ɾ����ӵ��ϳ��еĲ��ϡ�
*@Param:  void(0)
*@Return: void(0)
*/
function delProps()
{
	global $pp1, $pp2, $_pm;	// props first,props second, global object array.
	if (is_array($pp1))
	{
		$_pm['mysql']->query("UPDATE userbag
								 SET sums=abs(sums-1)
						       WHERE id={$pp1['id']} and uid={$_SESSION['id']} and sums > 0
							");
	}
	if (is_array($pp2))
	{
		$_pm['mysql']->query("UPDATE userbag
								 SET sums=abs(sums-1)
						       WHERE id={$pp2['id']} and uid={$_SESSION['id']} and sums > 0
							");
	}
}
?>