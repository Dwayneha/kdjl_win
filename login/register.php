<?php
@session_start();
require_once('../config/config.game.php');
@session_start();
ob_start();
if(!isset($_SESSION['manager']) || $_SESSION['manager'] != 1)
{
	//die();
}
/**
*@Version: %version%
*@Copyright: %copyright%
*@Author: %author%

*@Write Date: 2008.05.22
*@Usage:New user register.
*@Note: none
*/
header('Content-Type:text/html;charset=GBK');
require_once("loginCheck.php");
//require_once("../function/cwords.php");
$battletimearr = unserialize($_pm['mem']->get(MEM_TIME_KEY));
foreach($battletimearr as $v)
{
	if($v['titles'] == "login")
	{
		$login = $v['days'];
		break;
	}
}
if($login != "0")
{
	$welcome = memContent2Arr("db_welcome",'code');
	$gm_in_mem = $welcome['admin']['contents'];
	if(!empty($gm_in_mem))
	{
		$_gm['name'] = array_merge($_gm['name'],preg_split("/[,��;��]/",$gm_in_mem));
	}
	/*if(!in_array($_SESSION['username'],$_gm['name']) ) 
	{
		die('ά���У���ͣע�ᣡ');
	}*/
}
//die('ά���У���ͣע�ᣡ');

if($_REQUEST['bname']!='' && $_REQUEST['bc']!='')
{
	require_once("../config/config.game.php");

	$p['bname']=preg_replace("/[\s]/",'',trim($_GET['bname']));
	//echo $p['bname']."  5<br/>\r\n";
	require_once('../socketChat/badWord.php');
	for($i=0;$i<count($badArr);$i++)
	{
		if(!empty($badArr[$i]) && substr($p['bname'],0,strlen($badArr[$i])) == $badArr[$i])
		{
			ob_clean();
			
			ob_end_flush();
			
			die("����Ľ�ɫ����(".$badArr[$i].")Ϊ��ֹʹ�õĴʣ�");
		}
		else if(!empty($badArr[$i]) && strpos($p['bname'],$badArr[$i]) > 0)
		{
			ob_clean();
			
			ob_end_flush();
			
			die("����Ľ�ɫ����(".$badArr[$i].")Ϊ��ֹʹ�õĴʣ�");
		}
		else
		{
			//echo $badArr[$i];
		}
	}
	//echo $p['bname']."  2<br/>\r\n";
	$msg =iconv('gbk','utf-8',$p['bname']);
	//echo $p['bname']."  4<br/>\r\n";
	$_pm['mysql'] = new mysql();
	$tu=$p['bname'];
	//$tu	= mysql_real_escape_string(preg_replace("/[ 	 _\s��	]/",'',trim($p['bname'])));
	//$tu	= preg_replace("/[\s��_]/",'',$p['bname']);
	
	$u	= $_GET['username'];
	$bc	= $p['bc'];
	$rs = $_pm['mysql']->getOneRecord("SELECT id 
							   FROM player 
							  WHERE nickname='{$tu}'");

	if (!is_array($rs))
	{
		$rs = $_pm['mysql']->getOneRecord("SELECT id 
							   FROM player 
							  WHERE name='{$u}' and password <>'".str_repeat('0',32)."'");
	}
	
	$p['sex'] = $p['sex']==1?'˧��':'��Ů';
	
	if (strlen(trim($tu))<4 || strlen(trim($tu))>14){ $err="��ɫ�����Ȳ�����";echo $err;exit();}
	if (is_array($rs))
	{
		die('��ɫ�Ѿ����ڻ������Ѿ���һ����ɫ!');
	}
	else
	{
		$p['head'] = $p['head']==0?1:$p['head'];
		if($p['head']=="undefined") $p['head']=1;
		if($p['bc']=="undefined") $p['bc']=1;
		//die('ע��ɹ�������');
		// insert user data.

		$_pm['mysql']->query("INSERT INTO player(name,secret,nickname,sex,regtime,lastvtime,money,yb,headimg,task)
				    VALUES('{$_GET['username']}','".md5($_GET['pass'])."','{$tu}','{$p['sex']}',".time().",".time().",0,0,'{$p['head']}','')
				  ");
		$_SESSION['username'] = 	$_GET['username'];	
		$_SESSION['id'] = $_pm['mysql']->last_id(); 
		$_SESSION['LoginApiState'] = 1;
		// insert user bb init data.
		switch($bc)
		{
			case 1: $tbc = 1;break;
			case 2: $tbc = 13;break;
			case 3: $tbc = 23;break;
			case 4: $tbc = 32;break;
			case 5: $tbc = 42;break;
			default:$tbc = 1;$bc=1;
		}
		$bb = $_pm['mysql']->getOneRecord("SELECT * FROM bb WHERE id={$tbc} LIMIT 0,1");
		
		if (is_array($bb))
		{
			$czl = getCzl($bb['czl']);
			$uinfo = $_pm['mysql']->getOneRecord("SELECT id,nickname 
										  FROM player 
										 WHERE name='{$u}' 
										 LIMIT 0,1");

			
			$_pm['mysql']->query("INSERT INTO userbb(name,uid,username,level,wx,ac,mc,srchp,hp,srcmp,mp,skillist,stime,nowexp,
									lexp,imgstand,imgack,imgdie,hits,miss,speed,kx,remakelevel,remakeid,remakepid,czl,headimg,cardimg,effectimg)
					    VALUES('{$bb['name']}','{$uinfo['id']}','{$uinfo['nickname']}','1','{$bb['wx']}',
						       '{$bb['ac']}','{$bb['mc']}','{$bb['hp']}','{$bb['hp']}','{$bb['mp']}','{$bb['mp']}','{$bb['skillist']}',unix_timestamp(),
							  '{$bb['nowexp']}','55','{$bb['imgstand']}','{$bb['imgack']}','{$bb['imgdie']}',
							   '{$bb['hits']}','{$bb['miss']}','{$bb['speed']}','{$bb['kx']}','{$bb['remakelevel']}',
							   '{$bb['remakeid']}','{$bb['remakepid']}','{$czl}','t{$tbc}.gif','k{$tbc}.gif','q{$tbc}.gif')
					  ");
			$ids = $_pm['mysql']->getOneRecord("SELECT id 
										FROM userbb 
									   WHERE uid={$uinfo['id']} 
									   ORDER BY stime DESC 
									   LIMIT 0,1"); // get last bb.
			
			$arr = split(":", $bb['skillist']);
			// Get jn info.
			$jn = $_pm['mysql']->getOneRecord("SELECT * 
									   FROM skillsys 
									  WHERE id = {$arr[0]}");
			$ack  = split(",", $jn['ackvalue']);
			$plus = split(",", $jn['plus']);
			$uhp  = split(",", $jn['uhp']);
			$ump  = split(",", $jn['ump']);
			// Insert userbb jn.
						
			$_pm['mysql']->query("INSERT INTO skill(bid,name,level,vary,wx,value,plus,img,uhp,ump)
					  	VALUES('{$ids['id']}', '{$jn['name']}','{$arr['1']}','{$jn['vary']}','{$jn['wx']}','{$ack['0']}','{$plus['0']}','{$jn['img']}','{$uhp['0']}','{$ump['0']}')
					  ");
			$_pm['mysql']->query("UPDATE player SET mbid = {$ids['id']} WHERE nickname='{$uinfo['nickname']}'");
			if($_SESSION['registertype'] == 'prize'){
				$_pm['mysql']->query("INSERT INTO userbag(uid,pid,sell,vary,sums,stime,cantrade)
							VALUES(
								   '{$uinfo['id']}',
								   '2047',
								   '1',
								   '1',
								   1,
								   unix_timestamp(),
								   0
								  );
						  ");
			}
			$_pm['mysql'] -> query("INSERT INTO `lock`(uid,lockvalue) values({$uinfo['id']},0)");

			$_pm['mem']->set(array('k'=>MEM_SYSWORD_KEY, 
					      'v'=>'��ӭ��'.$p['sex'].$tu.'Я������ '.$bb['name'].' ����ڴ��������磡'));
###########################�����û�֪ͨ  2011-3-11 Ѧԭ####################################
			$dom = explode('.',$_SERVER['HTTP_HOST']);	  
			die("1");
		}
		else
		{
			die('ע��ʧ�ܣ�����\n����ϵ�ͷ���');
		}
	}
}
else
{
	die("��ѡ��ͷ��ͱ������ͣ�");
}

?>
