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
session_start();
require_once('../config/config.game.php');
header('Content-Type:text/html;charset=GBK');
secStart($_pm['mem']);
require_once('../sec/dblock_fun.php');
$a = getLock($_SESSION['id']);
if(!is_array($a)){
	realseLock();
	die('11');
}
$ap	    = intval($_REQUEST['ap']);  // table userbb->id
$bp 	= intval($_REQUEST['bp']);  // table userbb->id
$p1 	= intval($_REQUEST['p1']);  // table userbag->id
$p2 	= intval($_REQUEST['p2']);  // table userbag->id
$srctime = 15;
if ($p1<0) $p1 = 0;
if ($p2<0) $p2 = 0;
if($ap < 0 || $bp < 0){
	realseLock();
	die();
}
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
	if($ctime < $srctime && $_GET['type'] != 'do' && $_GET['type1'] != 'check')
	{
		die("11");//û�дﵽ���ʱ��
	}
	else
	{
		$_SESSION['time'.$_SESSION['id']] = time();
	}
}




#################�Ƿ�ѡ�˻�����ʯ����############
if($_GET['type1'] != 'check') //�ж�һ�ξ͹���
{
	$sql_props = 'SELECT pid FROM userbag WHERE (id='.$p1.' or id='.$p2.') and uid='.$_SESSION['id'];
	$props = $_pm['mysql'] -> getRecords($sql_props);
	if(is_array($props))
	{
		$check_props = 0;
		foreach ($props as $key_props => $key_value)//Array ( [pid] => 771 )
		{
			$a = 'SELECT effect FROM props WHERE varyname=8 and id='.$key_value['pid'];
			$cmpProps = $_pm['mysql'] -> getOneRecord($a);
			if(is_array($cmpProps))//Array ( [effect] => hecheng:A:10%,B:4%|addczl:8%|1 ) 
			{
				$key_values = substr($cmpProps['effect'],-1,1);
				if($key_values == '1')
				{
					$check_props = $check_props+1;
				}
			}

		}
		if($check_props == 0)
		{
			die('200');
		}
	}
	else
	{
		die('200');
	}
}

##################�������������#################
if($_GET['type'] != 'do'){
	$zbcheck = $_pm['mysql'] -> getRecords("SELECT id FROM userbag WHERE zbpets = $ap or zbpets = $bp");//echo "SELECT id FROM userbag WHERE zbpets = $ap or zbpets = $bp";
	if(count($zbcheck) >= 1){//echo __LINE__."<br>";
		realseLock();
		die('1000');
	}//echo __LINE__."<br>";
}


/*if(lockItem($ap) === false)
{
	die('�Ѿ��ڴ����ˣ�');
}*/
if ($ap<0 || $bp<0) {
	realseLock();
	die('0');
}




$user		= $_pm['user']->getUserById($_SESSION['id']);//һ���û���������Ϣplayer
$userbb		= $_pm['user']->getUserPetById($_SESSION['id']);//һ���û����еĳ�����Ϣuserbb
if(!empty($p1))
{
	$pp1 = $_pm['user']->getUserItemById($_SESSION['id'],$p1);//����һ����userbag
	if($pp1['sums'] < 1){
		realseLock();
		die('20');
	}
}
if(!empty($p2))
{
	$pp2 = $_pm['user']->getUserItemById($_SESSION['id'],$p2);//���߶�����userbag
	if($pp2['sums'] < 1){
		realseLock();
		die('20');
	}
}

$log = '';

if ( is_array($userbb))
{
	foreach ($userbb as $key => $rs)
	{
		if ($rs['id']==$ap && $rs['level']>=40) // From bb base find user current bb.
		{
			$app = $rs;//������Ϣ�����飩userbb
		}
		if ($rs['id']== $bp && $rs['level']>=40)
		{
			$bpp = $rs;//������Ϣ�����飩userbb
		}
	}
    unset($rs);
	
	$cishu=$_pm['mysql']->getOneRecord("select hecheng_nums,chouqu_chongwu from player_ext where uid={$_SESSION['id']}");
	if(strpos($cishu['chouqu_chongwu'],','.$app['id'].',')!==false||strpos($cishu['chouqu_chongwu'],','.$bpp['id'].',')!==false)
	{
		die("ĳ�������ȡ���ɳ�,���ܽ��кϳ�!");
	}

	if($p1 == $p2 && $p1 != 0)
	{
		if($pp1['sums'] < 2)
		{
			realseLock();
			die("100");
		}
	}

	if (!is_array($app) || !is_array($bpp) || ($app['id'] == $bpp['id'])) {
		realseLock();
		die('1'); //û�ж�Ӧ�ĳ��
	}
	
	// ����Ƿ����㹫ʽ��
	//$ars = $_pm['mem']->dataGet(array('k' => MEM_BB_KEY, 
	//									 'v' => "if(\$rs['name'] == '{$app['name']}') \$ret=\$rs;"//bb
	//						  ));
	//$brs = $_pm['mem']->dataGet(array('k' => MEM_BB_KEY, 
	//									 'v' => "if(\$rs['name'] == '{$bpp['name']}') \$ret=\$rs;"//bb
	//						  ));
	$membbname = unserialize($_pm['mem']->get('db_bbname'));
	$ars = $membbname[$app['name']];
	//print_r($ars);exit;
	$brs = $membbname[$bpp['name']];

	$cmprs = $_pm['mysql']->getOneRecord("SELECT * 
											FROM merge
										   WHERE aid = {$ars['id']} and bid={$brs['id']}
										   LIMIT 0,1
	                                    ");
    if (!is_array($cmprs)) {
		realseLock();
		die('2');	//���ܺϳɣ�
	}
	//����Ƿ��гɳ�����
	$max_czl = 0;
	if(!empty($cmprs['limits']))
	{
		$limitsarr = explode('|',$cmprs['limits']);
		if(!empty($limitsarr[0]) && $app['czl'] < $limitsarr[0])//����ɳ�����
		{
			realseLock();
			die('15');
		}
		if(!empty($limitsarr[1]) && $bpp['czl'] < $limitsarr[1])//����ɳ�����
		{
			realseLock();
			die('15');
		}
		if(!empty($limitsarr[1]) && count($limitsarr) == 3 )
		{
			$max_czl = $limitsarr[2];
		}
	}								
					  
	$money=0;
	$money=$user['money'];
	if($user['money'] < 50000)
	{
		realseLock();
		die('3');//	��Ҳ���
	}
		
	$propseff = getEffect($pp1, $pp2);//Array ( [0] => hecheng:A:1%,B:0% [1] => addczl:0% [2] => 1 [3] => hecheng:A:15%,B:3% [4] => addczl:20% [5] => 2 ) 

	$sus = getSuccess($app,$bpp,$pp1,$pp2);//�ɹ��ʹ�ʽ����һ������2->B�� 1->A��
	//echo 'sus:'.$sus.'<br />';
	$czl = bbczl($app,$bpp,$pp1,$pp2);///��óɳ���->һ���ٷ�С��23.2
	if($czl > $max_czl && $max_czl != 0)
	{
		$czl = $max_czl;
	}
//$sus = 1;

	if ($sus) // �ϳɳɹ���a,b������ʧ���õ��µĳ��$cmprs=> �õ���ر�����Ϣ��
	{

			// �ı����Եط�Ϊ:
		if ($sus == 1) $newbid = $cmprs['maid'];
		if ($sus == 2) $newbid = $cmprs['mbid'];
		//echo 'newbb:'.$newbid.'<br />';exit;
		$brs = $_pm['mysql']->getOneRecord("SELECT * 
											  FROM  bb
											 WHERE id={$newbid}
											 LIMIT 0,1
										  ");
										  
		if (!is_array($brs))
		{
			realseLock();
			die('10'); // ���ݴ���
		}
		// �ı��������:
		makebb($brs,$max_czl);
		$cstatus = 2;
	}
	else // ���û����ص��߽��а󶨣�������ʧ
	{


		$cstatus = 1;
	}

	$user['money'] = $user['money']-50000;		// �����û����.
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
//$cstatus=1;
	if ($cstatus == 1) //������ʧ���ϳ�ʧ��
	{
		//�ڴ�д��һ�ű�
		if(!isset($cishu)) $cishu=$_pm['mysql']->getOneRecord("select hecheng_nums from player_ext where uid={$_SESSION['id']}");
		$nums2=$_pm['mysql']->query("update player_ext set hecheng_nums=".($cishu[hecheng_nums]+1)." where uid={$_SESSION['id']}");
	
		$del = 1;
		$log .= '�ϳɵ�����ϸ��';
		if(is_array($propseff))
		{
			if(!empty($pp1))
			{
				$log .= $pp1['name'].'-';
			}
			if(!empty($pp2))
			{
				$log .= $pp2['name'].'-';
			}
			//$pp1['name']$pp1['effect']
			
			//$log .= $n['shbb']."-";
			if ($propseff[2] == 1 || $propseff[5] == 1)
			{
				$del = 0;
			}
		}
	
		if ($del == 1)//������ʧ������
		{
			clearBB($bpp);
			$log .= 'name:'.$bpp['name'].'level:'.$bpp['level'].'czl:'.$bpp['czl'].'ac:'.$bpp['ac'].'srchp:'.$bpp['srchp'].'hits:'.$bpp['hits'];
		}
		$log = addslashes($log);
		// �ϳ�ʧ�ܼ�¼�㣺
		$_pm['mysql']->query("INSERT INTO gamelog(ptime,seller,buyer,pnote,vary)
		                      VALUES(unix_timestamp(),'{$_SESSION['id']}','{$_SESSION['id']}','{$log}',2)
							");
		realseLock();
		die('6');
	}
	else if($cstatus == 2) // �ɹ���
	{
		$nums3=$_pm['mysql']->query("update player_ext set hecheng_nums=0 where uid={$_SESSION['id']}");
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
		$newbbarr = $_pm['mysql'] -> getOneRecord("SELECT level,czl,ac,hits,srchp FROM userbb WHERE name = '{$brs[name]}' and uid = {$_SESSION['id']} order by id desc");
		
		$str = '�³������֣�'.$brs['name'].'level:'.$newbbarr['level'].'czl:'.$newbbarr['czl'].'ac:'.$newbbarr['ac'].'hits:'.$newbbarr['hits'].',ʹ����Ʒ1��'.$pp1['name'].',ʹ����Ʒ2��'.$pp2['name'].',���'.$app['name'].'level:'.$app['level'].'czl:'.$app['czl'].'ac:'.$app['ac'].'hits:'.$app['hits'].'-'.$bpp['name'].'level:'.$bpp['level'].'czl:'.$bpp['czl'].'ac:'.$bpp['ac'].'hits:'.$bpp['hits'];
		$_pm['mysql']->query("INSERT INTO gamelog(ptime,seller,buyer,pnote,vary)
		                      VALUES(unix_timestamp(),'{$_SESSION['id']}','{$_SESSION['id']}','{$str}',4)
							");
		
		foreach($arr as $k=>$v)
		{
			$retstr .= $v.'linend';
		}

		$retstr = $retstr.$newstr;
		$_pm['mem']->set( array('k'=>$msg_key, 'v'=>$retstr) ); // default ten min.
		
		// �ϳɳɹ�����socket֮ǰ����¼
		$_pm['mysql']->query("INSERT INTO gamelog(ptime,seller,buyer,pnote,vary)
		                      VALUES(unix_timestamp(),'{$_SESSION['id']}','{$_SESSION['id']}','�ϳɳɹ�����socket֮ǰ����¼',173)
							");
		clearBB($app); // del pets master
		clearBB($bpp); // del pets other
		
		//----------------------------------------------------------------------------------------------------------------------
		//$_olddata = @unserialize($_pm['mem']->get('ttmt_data_notice'));		
		$swfData = iconv('gbk','utf-8','��ϲ��� '.$user['nickname'].' �ɹ��ĺϳ���һֻ['.$brs['name'].'],����̫������!');
		require_once(dirname(__FILE__).'/../socketChat/config.chat.php');
		$s=new socketmsg();
		$s->sendMsg('an|'.$swfData);
		
		// �ϳɳɹ�����socket֮������¼
		$_pm['mysql']->query("INSERT INTO gamelog(ptime,seller,buyer,pnote,vary)
		                      VALUES(unix_timestamp(),'{$_SESSION['id']}','{$_SESSION['id']}','�ϳɳɹ�����socket֮������¼',173)
							");
		//$_olddata['an'] = isset($_olddata['an'])?$_olddata['an']."<br/>[ϵͳ����]��".$swfData:$swfData;
		//$_pm['mem']->set(array('k'=>'ttmt_data_notice','v'=>$_olddata));
		//----------------------------------------------------------------------------------------------------------------------
		
		
		realseLock();
		die('5');
	}
}
else {
	realseLock();
	die('000');
}
realseLock();
$_pm['mem']->memClose();
// Logic code end.



/**
* @Usage: �����µĳ��
* @Param: array -> $bb.
* @Return: Void(0);
*/
function makebb($bb,$max_czl)
{$czl=0;
//echo "\r\n";
	global $app,$bpp,$pp1,$pp2,$user,$_pm,$propseff;
	$czl = bbczl($app,$bpp,$pp1,$pp2);
	$ac=getPlus($propseff,'ac');
	$mc=getPlus($propseff,'mc');
	$hit=getPlus($propseff,'hit');
	$miss=getPlus($propseff,'miss');
	$speed=getPlus($propseff,'speed');
	$hp=getPlus($propseff,'hp');
	$mp=getPlus($propseff,'mp');
	
	// ac,luck,mc,hit,miss,speed,hp,mp,shbb,czl; 
	$bb['ac']	= getPa($bb['ac'], $app['ac'], $bpp['ac'] ,getPlus($propseff,'ac'));#### ��ʱû�м�����߸������ԡ�
    $bb['mc']	= getPa($bb['mc'], $app['mc'], $bpp['mc'] ,getPlus($propseff,'mc'));
	$bb['hits']	= getPa($bb['hits'], $app['hits'], $bpp['hits'] ,getPlus($propseff,'hit'));
    $bb['miss']	= getPa($bb['miss'], $app['miss'], $bpp['miss'] ,getPlus($propseff,'miss'));
	$bb['speed']= getPa($bb['speed'], $app['speed'], $bpp['speed'] ,getPlus($propseff,'speed'));
	$bb['hp']	= getPa($bb['hp'], $app['hp'], $bpp['hp'] ,getPlus($propseff,'hp'));
	$bb['mp']	= getPa($bb['mp'], $app['mp'], $bpp['mp'] ,getPlus($propseff,'mp'));
	
	$uinfo = $user;
	if($bb['wx']==6 && $czl>60)
	{
		$czl=60;
	}
	else if($bb['wx']!=6 && $czl>150)
	{
		$czl=150;
	}
	if($max_czl != 0 && $czl > $max_czl)
	{
		$czl = $max_czl;
	}
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
	
	$jnall = split(",", $bb['skillist']);//1:1,60:1
	
	$membbname = unserialize($_pm['mem']->get('db_skillsysid'));
	
	foreach($jnall as $a => $b)
	{
		$arr = split(":", $b);
		// Get jn info.
		
		//$memjnid = $this->m_m->unserialize(get('db_skillsysid'));
		$jn = $membbname[$arr[0]];
		/*$jn = $_pm['mem']->dataGet(array('k'	=>	MEM_SKILLSYS_KEY,
								'v'	=>  "if(\$rs['id'] == '{$arr[0]}') \$ret=\$rs;"
						));*/
		// #################################################				
		if ($jn['ackvalue']=='')
		{
			$_pm['mysql']->query("INSERT INTO gamelog(ptime,seller,buyer,pnote,vary)
		                      VALUES(unix_timestamp(),'{$_SESSION['id']}','{$_SESSION['id']}','".$arr[0]."���ܹ���Ϊ0',173)
							");
			continue; // ���Ӹ������ܡ�
		}
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
	//return;
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
	/*$_pm['mysql']->query("DELETE FROM userbag
				 WHERE uid={$_SESSION['id']} and zbpets={$id}
			  ");*/
	$arr = $_pm['mysql'] -> getRecords("SELECT id,pid FROM userbag WHERE uid = {$_SESSION['id']} and zbpets = {$id}");
	if(is_array($arr)){
		foreach($arr as $v){
			if(!empty($v)){
				$_pm['mysql']->query("DELETE FROM userbag
				 WHERE uid={$_SESSION['id']} and pid = {$v['pid']} and zbpets = {$id}
			  ");
			}
		}
	}
	// del bb.
	$_pm['mysql']->query("DELETE FROM userbb
				 WHERE uid={$_SESSION['id']} and id={$id}
			  ");
}

/**
* @Param: ����a,b�����ԡ�
* @Return: ������Ϻ�ĳɳ��ʡ�
  
�ɳ���		��Ӧ��ʽ							
51.0(������51.0)����	������ɳ�+{[(������ȼ�/(������ɳ�+10))+(������ȼ�*������ɳ�/200)]*(100%+���߰ٷֱ�)}								
51.0(����51.0)��70.0	������ɳ�+{[(������ȼ�/������ɳ�)+(������ȼ�*������ɳ�/350)]*(100%+���߰ٷֱ�)}								
70.0(����70.0)��90.0	������ɳ�+{[(������ȼ�/������ɳ�)+(������ȼ�*������ɳ�/500)]*(100%+���߰ٷֱ�)}	
90.0(����90.0)��100.0	������ɳ�+{[(������ȼ�/������ɳ�)+(������ȼ�*������ɳ�/700)]*(100%+���߰ٷֱ�)}								
100(����100.0)����	������ɳ�+{[(������ȼ�/������ɳ�)+(������ȼ�*������ɳ�/900)]*(100%+���߰ٷֱ�)}								

*/
function bbczl($a, $b, $pp1, $pp2)
{
	global $brs; // ���Ͽ��г������ԡ�
	
	if (is_array($pp1))
	{
		$one = explode('|', $pp1['effect']);
		
		$arr_11 = explode(':', $one[1]);
		if($arr_11[0]=='addczl')
		{
		$arr_1 = str_replace('%','',$arr_11[1]);
		$arr = $arr_1/100; 
		}
		
		
		
	}
	unset($one,$arr_11,$arr_1);
	if (is_array($pp2))
	{
		$one = explode('|', $pp2['effect']);
		$arr_11 = explode(':', $one[1]);
		if($arr_11[0]=='addczl')
		{
		$arr_1 = str_replace('%','',$arr_11[1]);
		$arr += $arr_1/100; 
		}
		
	}
	unset($one,$arr_11,$arr_1);
	
	if($a['czl']<51.0)
	{
	$czl=round($a['czl']+($a['level']/($a['czl']+10)+$b['level']*$b['czl']/200)*(1+$arr),1);//23.2
	return $czl;
	}
	if($a['czl']<70.0 || $a['czl']>=51.0)
	{
	$czl=round($a['czl']+($a['level']/$a['czl']+$b['level']*$b['czl']/350)*(1+$arr),1);
	return $czl;
	}
	if($a['czl']<90.0 || $a['czl']>=70.0)
	{
	$czl=round($a['czl']+($a['level']/$a['czl']+$b['level']*$b['czl']/500)*(1+$arr),1);
	return $czl;
	}
	if($a['czl']<100.0 || $a['czl']>=90.0)
	{
	$czl=round($a['czl']+($a['level']/$a['czl']+$b['level']*$b['czl']/700)*(1+$arr),1);
	return $czl;
	}
	if($a['czl']>=100.0)
	{
	$czl=round($a['czl']+($a['level']/$a['czl']+$b['level']*$b['czl']/900)*(1+$arr),1);
	return $czl;
	}
	//return $czl;
}

/**
*@Usage: ��ȡ�ϳ�����ӵ��ߵ�����Ч��=��Ϊһ��6��Ԫ������
*@Return: array.
*/
function getEffect($pp1, $pp2)
{

	if (is_array($pp1))
	{
		$one = explode('|', $pp1['effect']);
		foreach ($one as $a => $b)
		{
			$one1[] = $b;
		}
		unset($one);
	}
	if (is_array($pp2))
	{
		$one = explode('|', $pp2['effect']);
		foreach ($one as $a => $b)
		{
			$one1[] = $b;
		}
		unset($one);
	}
	// ���Ч����
	return $one1;

}


/**
* @Usage: ���ص�һЧ����
* @Param: string->$vary, array->$value.
* @Return: array.
*/
function getvary($vary, $value)//hecheng:A:15%|B:3%|addspeed:15%|2
{
	switch($vary)
	{   // ac,luck,mc,hit,miss,speed,hp,mp,shbb,czl;  hecheng:A:15%,B:3%|addspeed:15%|2
	//$value[1]   0.15 $ret['ac'] = 0.15   $ret['hp'] = 0.15  $ret=array();
		case "addac": $ret['ac'] = str_replace('%','',$value[1])/100;break;
		case "luck": $ret['luck'] = $value['1'].':'.(str_replace('%','',$value[2])/100);break;
		case "addmc": $ret['mc'] = str_replace('%','',$value[1])/100;break;
		case "addhit": $ret['hit'] = str_replace('%','',$value[1])/100;break;
		case "addmiss": $ret['miss'] = str_replace('%','',$value[1])/100;break;
		case "addspeed": $ret['speed'] = str_replace('%','',$value[1])/100;break;
		case "addhp": $ret['hp'] = str_replace('%','',$value[1])/100;break;
		case "addmp": $ret['mp'] = str_replace('%','',$value[1])/100;break;
		case "addczl": $ret['czl'] = str_replace('%','',$value[1])/100;break;
		case "B": $ret['B'] = str_replace('%','',$value[1])/100;break;
		case "shbb": $ret['shbb'] = true;break;
	}
	return $ret;
}

/*
��ʽ��												
�ºϳɳɹ���ʽΪ(ȡ1λС��)��		[�ϳɴ���/(����ɳ�*2)]+[(����ȼ�+����ȼ�)/15]*0.01+(���߰ٷֱ�)+[(���1~5)*0.01]											
�ϳ��жϳɹ����������B�׳ɹ��ٷֱȣ�Ĭ��5%��+��B�׵��߰ٷֱȣ� �ɹ���ϳ�Ϊϡ�г裨B��			ʧ�ܺ�ϳ�Ϊ��ͨ��(A)		
*/
function getSuccess($app,$bpp,$pp1,$pp2)
{
	global $_pm;
	if (is_array($pp1))
	{
		$one = explode('|', $pp1['effect']);
		$arr = explode(',', $one[0]);
		$arr_2 = explode(':',$arr[0]);		
		$arr_21 = str_replace('%','',$arr_2[2]);
		$arr2 = $arr_21/100;
		
		$arr_3 = explode(':',$arr[1]);
		$arr_31 = str_replace('%','',$arr_3[1]);
		$arr4 = $arr_31/100;		
		unset($arr_2,$arr_21,$one);
	}
	
	if (is_array($pp2))
	{

		$one = explode('|', $pp2['effect']);
		$arr = explode(',', $one[0]);       
		$arr_2 = explode(':',$arr[0]);		
		$arr_21 = str_replace('%','',$arr_2[2]);
		$arr2 += $arr_21/100;
		
		$arr_3 = explode(':',$arr[1]);
		$arr_31 = str_replace('%','',$arr_3[1]);
		$arr4 += $arr_31/100;
	}
	
	$nums="select hecheng_nums from player_ext where uid={$_SESSION['id']}";
	$cishu = $_pm['mysql'] -> getOneRecord($nums);
	$chenggonglv=($cishu['hecheng_nums']/($app['czl']*2))+(($app['level']+$bpp['level'])/15)*0.01+$arr2+(rand(1,5)*0.01);
	//echo "<br />".$arr2."<br />";
	$success=round($chenggonglv,1);

	if($cishu['hecheng_nums']==10 || $app['czl']<=5)//�������Ǵﵽ10��ʱ���ϳ���Ϊ100%
	{
		$success=1.0;
	}

	$a=rand(1,100)/100;
	if($a<=$success)//�ɹ��Ĵ���
	{
		//echo 'B:'.$arr4.'<br />';exit;
		$chance=0.05+$arr4;
		$chance_rand=rand(1,100)/100;
		if($chance_rand<=$chance)
		{
			//�ϳ�B����
			unset($success);
			return 2;
			
		}
		else
		{
			//�ϳ�A����
			unset($success);
			return 1;
			
		}
		
	}
	else//ʧ�ܵĴ���
	{
	unset($success);
		return false;
	}
}
/*
*@Usage:����ϳɺ�ĳ��ﵥһ���ԡ�
* a,b,p=> $props attrib.
*@Return: int.
*@Memo ����=ȡ��{[�����������ݿ�����+ȡ��������������*������ȼ�/400��+ȡ��������������*������ȼ�/800��]*(100%+���߸�������%)}
*/
function getPa($old, $a, $b ,$p)
{	
	global $app,$bpp;
	if ($p == '' || $p<=0) $p=1;
	else $p = 1+$p;

	return intval(($old+(intval($a*$app['level']/400)+intval($b*$bpp['level']/800)))*$p);
}


/**
*@Usage: ��úϳɼ����
�ߵĸ�������ֵ��
*@ Return: float.
*/
function getPlus($parr,$a)//Array([0] => hecheng:A:15%,B:3% [1] => addczl:20% [2] => 2 [3] => hecheng:A:15%,B:3% [4] => addczl:20% [5] => 2)  
{
	$czl1 = 0;
	$czl2 = 0;
	$czl = 0;
	
	if (!is_array($parr)) return 0;
	$arr = explode(':',$parr[1]);//$arr[0]=addczl $arr[1]=15%
	$arr2 = substr($arr[0], 3); //czl mp cp  
	if(count($parr)==6)
	{
	$arr1 = explode(':',$parr[4]);
	$arr3 = substr($arr1[0], 3); //czl mp cp 
	}
	switch ($arr2)
			{
				case "czl":
					if($a=='czl')
					{
						$czl1 = str_replace('%','',$arr[1])/100;//$arr[1]=0.15����Ҫ�����������
						if(count($parr)==3)
						{
						return $czl1;
						}
					}
					else
					{
						$czl1=0;
						if(count($parr)==3)
						{
						return $czl1;
						}
					}
					break;
				case "ac":
					if($a=='ac')
					{
						$czl1 = str_replace('%','',$arr[1])/100;
						if(count($parr)==3)
						{
						return $czl1;
						}
					}
					else
					{
						$czl1=0;
						if(count($parr)==3)
						{
						return $czl1;
						}
					}
					break;
				case "mc":
					if($a=='mc')
					{
						$czl1 = str_replace('%','',$arr[1])/100;
						if(count($parr)==3)
						{
						return $czl1;
						}
					}
					else
					{
						$czl1=0;
						if(count($parr)==3)
						{
						return $czl1;
						}
					}
					break;
				case "hit":
					if($a=='hits')
					{
						$czl1 = str_replace('%','',$arr[1])/100;
						if(count($parr)==3)
						{
						return $czl1;
						}
					}
					else
					{
						$czl1=0;
						if(count($parr)==3)
						{
						return $czl1;
						}
					}
					break;
				case "miss":
					if($a=='miss')
					{
						$czl1 = str_replace('%','',$arr[1])/100;
						if(count($parr)==3)
						{
						return $czl1;
						}
					}
					else
					{
						$czl1=0;
						if(count($parr)==3)
						{
						return $czl1;
						}
					}
					break;
				case "speed":
					if($a=='speed')
					{
						$czl1 = str_replace('%','',$arr[1])/100;
						if(count($parr)==3)
						{
						return $czl1;
						}
					}
					else
					{
						$czl1=0;
						if(count($parr)==3)
						{
						return $czl1;
						}
					}
					break;
				case "hp":
					if($a=='hp')
					{
						$czl1 = str_replace('%','',$arr[1])/100;
						if(count($parr)==3)
						{
						return $czl1;
						}
					}
					else
					{
						$czl1=0;
						if(count($parr)==3)
						{
						return $czl1;
						}
					}
					break;
				case "mp":
					if($a=='mp')
					{
						$czl1 = str_replace('%','',$arr[1])/100;
						if(count($parr)==3)
						{
						return $czl1;
						}
					}
					else
					{
						$czl1=0;
						if(count($parr)==3)
						{
						return $czl1;
						}
					}
					break;
			}
			switch ($arr3)
			{
				case "czl":
					if($a=='czl')
					{
							$czl2 = str_replace('%','',$arr1[1])/100;
							$czl = $czl1+$czl2;
							return $czl;
							
					}
					else
					{
						return $czl1;
					}
					break;
				case "ac":
					if($a=='ac')
					{
						 	$czl2 = str_replace('%','',$arr1[1])/100;
							return $czl = $czl1+$czl2;
					}
					else
					{
						return $czl1;
					}
					break;
				case "mc":
					if($a=='mc')
					{
							$czl2 = str_replace('%','',$arr1[1])/100;
							return $czl = $czl1+$czl2;
					}
					else
					{
						return $czl1;
					}
					break;
				case "hit":
					if($a=='hits')
					{
							$czl2 = str_replace('%','',$arr1[1])/100;
							return $czl = $czl1+$czl2;
					}
					else
					{
						return $czl1;
					}
					break;
				case "miss":
					if($a=='miss')
					{
							$czl2 = str_replace('%','',$arr1[1])/100;
							return $czl = $czl1+$czl2;
					}
					else
					{
						return $czl1;
					}
					break;
				case "speed":
					if($a=='speed')
					{
							$czl2 = str_replace('%','',$arr1[1])/100;
							return $czl = $czl1+$czl2;
					}
					else
					{
						return $czl1;
					}
					break;
				case "hp":
					if($a=='hp')
					{
							$czl2 = str_replace('%','',$arr1[1])/100;
							return $czl = $czl1+$czl2;
					}
					else
					{
						return $czl1;
					}
					break;
				case "mp":
					if($a=='mp')
					{
							$czl2 = str_replace('%','',$arr1[1])/100;
							return $czl = $czl1+$czl2;
					}
					else
					{
						return $czl1;
					}
					break;
			}


}

/**
*@Usage: ɾ����ӵ��ϳ��еĲ��ϡ�
*@Param:  void(0)
*@Return: void(0)
*/
function delProps()
{
//return;
	global $pp1, $pp2, $_pm;	// props first,props second, global object array.
	if (is_array($pp1))
	{
		$_pm['mysql']->query("UPDATE userbag
								 SET sums=abs(sums-1)
						       WHERE id={$pp1['id']} and uid={$_SESSION['id']} and sums > 0
							");
		//echo mysql_affected_rows($_pm['mysql'] -> getConn()).'<br />';
		if(mysql_affected_rows($_pm['mysql'] -> getConn()) != 1){
			realseLock();
			die('20');
		}
	}
	if (is_array($pp2))
	{
		$_pm['mysql']->query("UPDATE userbag
								 SET sums=abs(sums-1)
						       WHERE id={$pp2['id']} and uid={$_SESSION['id']} and sums > 0
							");
		if(mysql_affected_rows($_pm['mysql'] -> getConn()) != 1){
			realseLock();
			die('20');
		}
	}
}
?>