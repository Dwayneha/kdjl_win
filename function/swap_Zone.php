<?php 
//ini_set('display_errors','on');
//error_reporting(E_ALL);
ob_start();

$prizeLimitLevel = 40;
$prizeForSwap = array(
	'1444'=>10,
	'1443'=>10
);
$pwdSec = 'sd212228(*';
$pwd = md5($pwdSec);
$block = "~`'`~";
$vbcrlf = "\r\n";
define('ERROR_AUTH','ERR_AUTH');
define('ERROR_SECID_STATUS','ERROR_USER_ID');
define('HTTP_CONTENT_STARTED','<!--~HTTP_CONTENTS_STARTED~-->');

if(isset($_GET['qid'])&&intval($_GET['qid'])>0&&$_GET['p']==$pwd&&isset($_GET['sid'])&&strlen($_GET['sid'])==32)
{
	//Ϊ��ȫ�����׼������֤Ҫת�����û��Ѿ���½
	session_id($_GET['sid']);
}
ob_end_clean();
if(!headers_sent())
	header('Content-Type:text/html;charset=GBK');

require_once('../config/config.game.php');

//INSERT INTO `timeconfig` (`Id`, `titles`, `days`, `starttime`, `endtime`) VALUES (NULL, 'swapzone', 0, '20090501', '20090601');
$prizeEndAt = '';

connect();
$swapZoneSetting = query('select starttime,endtime from timeconfig where titles= "swapzone"');
$swapZoneSetting = $swapZoneSetting[0];
$today = date("Ymd");
$todayhour = date("YmdH");
$swapZoneStarted = false;//��������ת��
$swapZoneEndTime = 0;
$fromZone = array(//ת������				
);

$aimZone = array(//Ŀ����
);
if(is_array($swapZoneSetting))
{
	$swapZoneFrom = query('select titles,endtime from timeconfig where starttime= "SWAP_FROM"');
	$fromZone = getSwapZoneSetting($swapZoneFrom[0]['titles']);

	$swapZoneTo = query('select titles,endtime from timeconfig where starttime= "SWAP_TO"');
	$aimZone = getSwapZoneSetting($swapZoneTo[0]['titles']);

	$swapZonePrize = query('select titles,endtime from timeconfig where starttime= "swapzonePrize"');
	$prizeForSwap = getSwapZoneSetting($swapZonePrize[0]['titles']);
	/*if($_SESSION['username']!="tanwei2008"){
		$swapZoneSetting['starttime'] = '20100603';
	}*/
	if($swapZoneSetting['starttime']<=$today || ( strlen($swapZoneSetting['starttime'])==10&&$swapZoneSetting['starttime']<=$todayhour ) )
	{
		$swapZoneStarted = true;//��������ת��
	}
	$swapZoneEndTime = $swapZoneSetting['endtime'];
}
else
{
	query('alter table timeconfig change titles titles varchar(255) Null default ""');
	query("INSERT INTO `timeconfig` (`Id`, `titles`, `days`, `starttime`, `endtime`) VALUES (NULL, 'swapzone', 0, '21090501', '20190502')");
	query("INSERT INTO `timeconfig` (`Id`, `titles`, `days`, `starttime`, `endtime`) VALUES (NULL, '1308,20;1225,20;1142,5', 0, 'swapzonePrize', 'swapzonePrize')");
	query("
		INSERT INTO `timeconfig` 
		(`Id`, `titles`, `days`, `starttime`, `endtime`) 
		VALUES 
		(NULL, 'pm1.webgame.com.cn,һ��;pmtest.webgame.com.cn,����һ��', 0, 'SWAP_FROM', 'SWAP_FROM')");
	query("
		INSERT INTO `timeconfig` 
		(`Id`, `titles`, `days`, `starttime`, `endtime`) 
		VALUES 
		(NULL, 'pmtest2.webgame.com.cn,���Զ���;pm2.webgame.com.cn,����;pm3.webgame.com.cn,����;pm4.webgame.com.cn,����;pm5.webgame.com.cn,����', 0, 'SWAP_TO', 'SWAP_TO')");
	echo '<!-- swap zone timeconfig not found, it was inited! -->';
}

if(isset($_GET['dbg'])){
	echo '<b>'.__FILE__.'-->'.__LINE__.'</b><br/><pre>=';
	var_dump(	$swapZoneSetting , $today	,$todayhour , $swapZoneSetting['starttime']<=$today);
	echo '</pre>';
}

/********************************************************************************************************************/
/*********************************           �����Ǻ������򲿷�              ****************************************/
/********************************************************************************************************************/
function changeNamePriv(){
	global $changeNamePriv;
	$rs = query('select yb from yblog where nickname="'.$_SESSION['username'].'" limit 1');
	$changeNamePriv = false;
	if(!empty($rs))
	{
		$changeNamePriv = true;
	}
}


function getSwapZoneSetting($str){
	$strs = split(";",$str);
	$tmp = array();
	foreach($strs as $str)
	{
		$t = split(',',$str,2);
		if(count($t)==2)
		{
			$tmp[$t[0]]=$t[1];
		}
	}
	return $tmp;
}





function socketData($host,$url1,$flag=false){
	$port = 80;
	$url = 'http://'.$host.$url1;
	$post = 1;
	$returntransfer = 1;
	$header = 0;
	$nobody = 0;
	$followlocation = 1;

	$ch = curl_init();
	$options = array(CURLOPT_URL => $url,
		CURLOPT_HEADER => $header,
		CURLOPT_NOBODY => $nobody,
		CURLOPT_PORT => $port,
		CURLOPT_POST => $post,
		CURLOPT_POSTFIELDS => $request,
		CURLOPT_RETURNTRANSFER => $returntransfer,
		CURLOPT_FOLLOWLOCATION => $followlocation,
		CURLOPT_COOKIEJAR => $cookie_jar,
		CURLOPT_COOKIEFILE => $cookie_jar,
		CURLOPT_REFERER => $url
	);
	curl_setopt_array($ch, $options);
	$result = curl_exec($ch);
	curl_close($ch);
	return $result;
}
function connect(){
	global $conn,$_mysql;
	$conn = mysql_connect($_mysql['host'],$_mysql['user'],$_mysql['pass']);
	mysql_select_db($_mysql['db'],$conn);
	$row = mysql_fetch_row(mysql_query('show tables',$conn));
	$row = mysql_fetch_row(mysql_query('show create table '.$row[0],$conn));

	if(strpos(strtolower($row[1]),"charset=gbk")!==false){
		mysql_query("SET NAMES GBK;",$conn); 
		mysql_query("SET CHARACTER_SET_CLIENT=GBK;",$conn); 
		mysql_query("SET CHARACTER_SET_RESULTS=GBK;",$conn);
	}else{			
		mysql_query("SET NAMES latin1;",$conn);
	}
}
function query($sql,$flag=false){
	global $conn;
	if($flag)	echo 'query|'.__FILE__.'->'.__LINE__."<hr>\n";
	$result = mysql_query($sql,$conn) or die('sql='.$sql.'<br/>'.mysql_error());
	$rtn = array();
	if(is_resource($result)){
		while ($row = mysql_fetch_assoc($result)) {
			$rtn[]=$row;
		}
	}
	if($flag)	echo __FILE__.'->'.__LINE__."<hr>\n";	
	@mysql_free_result($result);
	if($flag)	echo __FILE__.'->'.__LINE__."<hr>\n";
	return $rtn;
}
function getPrikey($name)
{
	$cols = query('show columns from  `'.$name.'`');
	$primaryKey = array();
	foreach($cols as $col)
	{
		if($col['Key']=='PRI')
		{
			$primaryKey[]=$col['Field'];
		}
	}
	return $primaryKey;	
}
function formatData($data,$name)
{
	$return = $GLOBALS['block'].$name.$GLOBALS['vbcrlf'];
	$primaryKey = getPrikey($name);	

	foreach($data as $v)
	{
		$con = "";
		foreach($v as $k=>$vv)
		{
			if(
				(!in_array($k,$primaryKey) || $name=='userbb')
				||				
				(in_array($k,$primaryKey) && $name=='player_ext' && $k='uid')
			)//userbb����Ҫ��������������ȷ��Ӧ����
			{
				$return .= $con.'`'.$k.'`="'.str_replace('"','\"',$vv).'"';
				$con = ",";
			}
		}
		$return .= $GLOBALS['vbcrlf'];
	}
	return $return;
}

function divideData($data)
{
	$tmp = array();
	$data = split('",`',$data);
	foreach($data as $d)
	{
		$t = split('`="',$d);
		$t[0] = str_replace("`","",$t[0]);
		$tmp[$t[0]] = $t[1];
	}
	return $tmp;
}
function replaceUid($data,$uid,$field='uid')
{
	foreach($data as $k=>$v)
	{
		$data[$k] = preg_replace("/`".$field."`(=\"\d+\")/i","`".$field."`=\"".$uid."\"",$v);
	}
	return $data;
}
function replaceFiled($data,$uid,$field='uid')
{
	foreach($data as $k=>$v)
	{
		$data[$k] = preg_replace("/`".$field."`(=\"[^\"]+\")/i","`".$field."`=\"".$uid."\"",$v);
	}
	return $data;
}
function getBbOldId($data,$field = 'id')
{	
	if(preg_match("/`".$field."`=\"\d+\",/",$data,$out))
	{
		$data = array(substr($out[0],strlen($field)+4,-2),str_replace($out[0],"",$data));
	}	

	return $data;
}
function cancelSwapZone($userid,$cancel_pwd,$host){
	//echo 'http://'.$host.'/function/swap_Zone.php?id='.$userid.'&p='.$cancel_pwd.'&cancelSwap=yes'."<br/>";
	//return socketData($host,'function/swap_Zone.php?id='.$userid.'&p='.$cancel_pwd.'&cancelSwap=yes');
	return curlSS('http://'.$host.'/function/swap_Zone.php?id='.$userid.'&p='.$cancel_pwd.'&cancelSwap=yes');
}
function getWordCharInt($str) 
{
	$stro=$str;
	if(strpos($str,'��') !== false){
		return false;
	}
	$str=preg_replace("/\w/","",$str);
	if(
		preg_match("/[\`~\!@#$%\^&\*\(\)_+\|\=-\{\}\[\];'\:\"<>\?,\.\/]/",$str) || preg_match("/\s/", $str)
	)
	{
		return false;
	}	

	$str = $stro;

	$list = array('{','}','gm','��','�ͷ�','���ֹ�','������','��','��','\?','<','>','����','ϵͳ','����','�佱','Ԫ��','����','��ʾ','kefu','����','������','����','����','5173','����','�Ա�','������','ë��');	

	foreach($list as $v)
	{
		$reg = '/'.$v.'/i';
		if(preg_match($reg,$str)){
			return false;
		}
	}
	return true;
}
function saveData($data,$userid,$server)
{
	global $conn;
	$nUid = NULL;

	$sql = array();
	$cancel_pwd = false;

	foreach($data as $d)
	{
		if(!$cancel_pwd)
		{
			$cancel_pwd=$d;
			continue;
		}
		$tmp = preg_split("/".$GLOBALS['vbcrlf']."/",$d,0,PREG_SPLIT_NO_EMPTY);
		$primaryKey[$tmp[0]] = getPrikey($tmp[0]);	
		for($i=1;$i<count($tmp);$i++)
		{
			$sql[$tmp[0]][] = $tmp[$i];
		}
		if(!isset($sql[$tmp[0]]))
			$sql[$tmp[0]] = array();
	}

	//die();
	if(!isset($sql['player'])||!isset($sql['userbag'])||!isset($sql['userbb'])||!isset($sql['skill'])||!isset($sql['tasklog'])||!isset($sql['player_ext']))
	{
		die("Data lost(02).<!--���յ������ݿⲻ������ȱ�٣�-->");
	}

	//player
	$sqlPlayer = $sql['player'][0];
	query('START TRANSACTION');
	$player = divideData($sqlPlayer);
	$existsUser = false;

	$bbHostNewName = false;
	if(!empty($player['name'])){
		$existsUser = query('select id,name,yb,nickname,openmap,money,maxbag,maxbase,vip from player where name="'.$player['name'].'"');
		$nickLocal = query('select name,nickname from player where nickname like binary "'.$player['nickname'].'"');

		if(!empty($nickLocal)&&$nickLocal[0]['name']!=$player['name'])
		{
			if(!isset($_POST['bname']))
			{
				$GLOBALS['srMsg'] = '��ɫ���Ѿ����ڣ����޸���Ľ�ɫ����';
				$c = cancelSwapZone($userid,$cancel_pwd,$server);
				return false;
			}
			if(!getWordCharInt($_POST['bname'])){
				$GLOBALS['srMsg'] = "��ɫ���н�ֹ���ַ���";
				$c = cancelSwapZone($userid,$cancel_pwd,$server);
				return false;
			}
			if(strlen($_POST['bname'])<3||strlen($_POST['bname'])>13){
				$GLOBALS['srMsg'] = "��ɫ�����ȴ���";
				$c = cancelSwapZone($userid,$cancel_pwd,$server);
				return false;
			}
			$nickLocalNew = query('select name,nickname from player where nickname like binary "'.$_POST['bname'].'"');
			if(!empty($nickLocalNew))
			{
				$GLOBALS['srMsg'] = '��ɫ���Ѿ�������������ʹ�ã����޸���Ľ�ɫ����';
				$c = cancelSwapZone($userid,$cancel_pwd,$server);
				return false;
			}
			$sqlPlayer = str_replace('`nickname`="'.$player['nickname'].'"','`nickname`="'.$_POST['bname'].'"',$sqlPlayer);
			$bbHostNewName = array('`username`="'.$player['nickname'].'"','`username`="'.$_POST['bname'].'"');
		}
	}

	if($existsUser)
	{
		$sqlLog = '
			insert 
			into 
			gamelog
			(ptime,	seller,	buyer,	pnote,	vary) 
			values
			(unix_timestamp(),"'.$existsUser[0]['name'].'","'.$name.'","�û�ת�������޸ı����û���'.print_r($existsUser,1).'",13)
			';

		query($sqlLog);

		$existsUser = $existsUser[0];
		$keyCurUser  = $existsUser['id'] . "chat";
		//query('update player set nickname="ohno" where id="'.$existsUser['id'].'"');
		query('update player set name=md5(name),nickname="SwapZoneRpd" where id="'.$existsUser['id'].'"');
		//mysql_query('delete from player where id="'.$existsUser['id'].'"',$conn);
		//query('delete from tasklog where uid="'.$existsUser['id'].'"');
		$bb = query('select id from userbb where uid="'.$existsUser['id'].'"');
		$_bb = array();
		foreach($bb as $b)
		{
			$_bb[]=$b['id'];
		}
		//query('delete from userbb where id in ("'.implode('","',$_bb).'")');
		//query('delete from userbag where uid="'.$existsUser['id'].'"');
		//query('delete from skill where bid in ("'.implode('","',$_bb).'")');
	}
	//$sqlPlayer=str_replace("name`='","name`='A",$sqlPlayer);
	query('insert into player set '.$sqlPlayer);
	//echo 'insert into player set '.$sqlPlayer." ".__FILE__.'->'.__LINE__."<hr>";

	$nUid = mysql_insert_id($conn);
	if($nUid<1){
		//query('delete from player where name="'.$player['name'].'"');
		//query('ROLLBACK');
		$c = cancelSwapZone($userid,$cancel_pwd,$server);
		die("Create user faild(03).".print_r($c,1).'insert into player set '.$sqlPlayer.mysql_error().$nUid);
	}
	//echo '$nUid='.$nUid."<br/>\n";
	//userbb
	$sqlBb = $sql['userbb'];
	$sqlBb = replaceUid($sqlBb,$nUid);
	$bbId = array();
	//$nBBid = 111;
	//player_ext
	$chouqu = explode('chouqu_chongwu`="',$sql['player_ext'][0]);
	$chouqu = explode('"',$chouqu[1]);
	$sqlplayer_ext = $sql['player_ext'];
	$sqlplayer_ext = replaceUid($sqlplayer_ext,$nUid);

	foreach($sqlplayer_ext as $s)
	{
		if(strlen($s)<3) continue;
		query('insert into player_ext set '.$s.'');
	}
	query(" UPDATE player_ext SET chouqu_chongwu = '' WHERE uid = {$nUid} ");//clear chourqu	
	$prizeLimitLevelFlag = false;//��Ʒ��ȡ�ȼ�����
	foreach($sqlBb as $s)
	{
		if(strlen($s)<5) continue;
		$d = getBbOldId($s);
		$can_chouqu=strpos($chouqu[0],$d[0]);	
		if($bbHostNewName)
		{
			$d[1] = str_replace($bbHostNewName[0],$bbHostNewName[1],$d[1]);
		}
	
		query('insert into userbb set '.$d[1].'');
		$nBBid = mysql_insert_id($conn);//10hour
		if($can_chouqu)
		{
			$bid = mysql_insert_id();
			$ext_type = query(" SELECT * FROM player_ext WHERE uid = {$nUid} ");
			$update_chouqu_data = $ext_type[0]['chouqu_chongwu'];
			$update_chouqu_data .= ','.$bid.',';
			query(" UPDATE player_ext SET chouqu_chongwu = '".$update_chouqu_data."' WHERE uid = {$nUid} ");
			$can_chouqu =false;
		}
		$bbDataCur = divideData($d[1]);

		if(isset($bbDataCur['level'])&&intval($bbDataCur['level'])>=$GLOBALS['prizeLimitLevel'])
		{
			$prizeLimitLevelFlag = true;
		}
		if(isset($_REQUEST['dbg1'])){
			echo "<script>console.log($nBBid);</script>";	
			//die($d[0]);
		}
		$bbId[$d[0]] = $nBBid;
	}
	//skill
	$sqlSkill = $sql['skill'];
	$flag=true;
	$bbIdReplaceSetting = array();
	foreach($sqlSkill as $s)
	{
		if(strlen($s)<5) continue;
		if(preg_match("/`bid`=\"(\d+)\"/",$s,$out))
		{			
			if(isset($bbId[$out[1]]))
			{
				$s=str_replace($out[0],"`bid`=\"".$bbId[$out[1]]."\"",$s);
				$bbIdReplaceSetting[preg_replace("/[^\d]/","",$out[0])] = $bbId[$out[1]];
				query('insert into skill set '.$s.'');
			}
			else
			{
				//echo "A \n";
				$flag=false;
			}
		}else{
			//echo "B \n".strlen($s).'-'.$s."<br/>\n";
			$flag=false;
		}

		if(!$flag)
		{
			query('ROLLBACK');
			$c = cancelSwapZone($userid,$cancel_pwd,$server);
			die('Save skill failed(04).<!--'.mysql_error().$s.'-->'.print_r($c,1));
		}		
	}

	//userbag
	$sqlBag = $sql['userbag'];
	$sqlBag = replaceUid($sqlBag,$nUid);
	foreach($sqlBag as $s)
	{
		if(isset($_REQUEST['dbg1'])){
			echo "<script>console.log(11031)</script>";
			echo "<script>console.log('$s');</script>";	
			echo $s;
		}	
		if(strlen($s)<5) continue;
		//echo $s."<br\>";
		foreach($bbIdReplaceSetting as $k=>$v)
		{
			$f='`zbpets`="'.$k.'"';
			$r='`zbpets`="'.$v.'"';
			$s = str_replace($f,$r,$s);
		}
		//echo $s."<br\>";
		query('insert into userbag set '.$s.'');
		//echo 'insert into userbag set '.$s.''."<br/>\n";
		if(isset($_REQUEST['dbg1'])){
			echo "<script>console.log('$s');</script>";	
			echo $s;
		}		

	}

	//tasklog
	$sqlSkill = $sql['tasklog'];
	$sqlSkill = replaceUid($sqlSkill,$nUid);
	foreach($sqlSkill as $s)
	{
		if(strlen($s)<3) continue;
		query('insert into tasklog set '.$s.'');
	}



	//give prize
	if($GLOBALS['swapZoneEndTime']>=$GLOBALS['today']&&$prizeLimitLevelFlag)
	{		
		foreach($GLOBALS['prizeForSwap'] as $pid=>$v){
			$sql = 'INSERT INTO `userbag` 
				(`id`,`pid`,`uid`, `sell`, `vary`, `sums`, `stime`, `psell`, `pstime`, `petime`, `psum`, `bsum`, `zbing`, `zbpets`, `buycode`, `plus_tms_eft`) VALUES
				(NULL, '.$pid.','.$nUid.',0, 1, '.$v.', unix_timestamp(), 0, NULL, 0, 0, 0, 0, NULL, 0, NULL)';
			query($sql);
		}
	}
	query('COMMIT');
	if(mysql_error())
	{
		query('ROLLBACK');
		$c = cancelSwapZone($userid,$cancel_pwd,$server);
		die(print_r($c,1));
		return false;
	}else{
		if(isset($keyCurUser)) //�ѱ����ʺ�������
			$GLOBALS['_pm']['mem']->del($keyCurUser);
		return true;
	}
}
/********************************************************************************************************************/
/*********************************         ���������̿��Ƴ��򲿷�            ****************************************/
/********************************************************************************************************************/

//�жϹ����Ƿ񿪷�
if(!$swapZoneStarted)
{
	if(strpos($_SERVER['PHP_SELF'],basename(__FILE__))!==false){
		die("δ���Ź��ܣ�");
	}
}
//����
if(isset($_GET['changename'])&&isset($_POST['nName'])&&$swapZoneStarted)
{
	$name = substr(str_replace(array(' ','	','"',"'",';',',',"\\"),"",$_POST['nName']),0,30);
	$msg = "����ʧ��";
	$flag = false;
	if($name==$_SESSION['username'])
	{
		$msg = "�ĵĵ�½����ԭ������ͬ��";
	}else{
		//if(preg_match("/^[a-zA-Z]{1}([a-zA-Z0-9]|[_]){3,19}$/",$_POST['nName'])){
		if(strlen($name)>3&&strlen($name)<15)
		{
			connect();
			$rs = query('select * from player where name="'.$name.'"');
			if(empty($rs)){
				$rs = query('select 
					date_format(from_unixtime(ptime),"%Y/%m/%d %H:%i") ptime 
					from gamelog 
					where 
					(buyer="'.$_SESSION['username'].'" or seller="'.$_SESSION['username'].'" )
					and pnote="ChangeName" and vary=13');
				if(empty($rs)){
					query('START TRANSACTION');
					query('update player set name="'.$name.'" where name="'.$_SESSION['username'].'" and id="'.$_SESSION['id'].'"');
					$sqlLog = '
						insert 
						into 
						gamelog
						(ptime,	seller,	buyer,	pnote,	vary) 
						values
						(unix_timestamp(),"'.$_SESSION['username'].'","'.$name.'","ChangeName",13)
						';

					query($sqlLog);
					if(!mysql_error()){
						$msg = "�����ɹ���\\n��ر���������µ�½��";
						$flag = true;
						query('COMMIT');
						unset($_SESSION['username']);
						unset($_SESSION['id']);
						unset($_SESSION['licenseid']);
						unset($_SESSION);
						$crc = crc32($_COOKIE['PHPSESSID']);
						$_pm['mem']->del($crc);						
					}else{
						query('ROLLBACK');
					}
				}else{
					$msg = "���Ѿ���".$rs[0]['ptime']."�Ĺ��û�����";
				}
			}else{
				$msg = "����ĵ�½�������Ѿ����ڽ�ɫ��";
			}
		}else{
			$msg = "����ĵ�½�����Ϸ���";
		}
	}
	die('
		<script language="javascript">
alert("'.$msg.'");
'.($flag?'parent.changeNameTableDh();top.close();top.location="/login/login.php?rand='.time().'";':'').'
	</script>
	');
}

//�ڵ�½ҳ����ʾת�������б�
else if(isset($displaySwapZone)&&$displaySwapZone===true&&array_key_exists(strtolower($_SERVER['HTTP_HOST']),$fromZone)&&$swapZoneStarted)
{
	changeNamePriv();
	$_box="";
	$echo='	<fieldset style="width:385px">	<legend>�������ʺ�ת������</legend>';
	if($changeNamePriv){
		$echo .= '<table width="100%" border="0" cellspacing="0" cellpadding="0" id="changeNameTable" STYLE="display:none"><tr><td style="padding-left:6px;font-size:12px">	<form style="padding:0px;margin:0px" target="chgNameIfr" method="post" action="/function/swap_Zone.php?changename=1" id="chgNameForm">���뱣����ת�����������еĽ�ɫ���������£�<br/>1.	������ע��һ��<font color=red>ͨ��֤�˺�</font>��ȷ�������κ�������û�н�ɫ<br/>2.	���·��Ŀ�ȱ��������<font color=red>ͨ��֤�˺�</font>������ɫת�Ƶ����˺���<br/>3.	��½���ʺţ�����ת��ҳ����ѡ��Ҫת��ķ�����<br/>����ɫת�Ƶ���<input type="text" size=12 name="nName" id="nName"><input type="button" value=" ȷ�� " onclick="changName()">&nbsp;&nbsp;<input type="button" value=" ȡ�� " onclick="changeNameTableDh()"><br/>ע����ֻ��һ���޸ĵ�½���Ļ��ᣡ���ȷ������д����ȷ�ģ�<br />��½�����ܰ����հס������š�˫���š��ֺš����š�б�ܣ�'.'\\\\'.'����������š�</form><iframe style="display:none" src="about:blank" name="chgNameIfr"></iframe></td></tr></table>';
		$_box="
<style>
/* Z-index of #mask must lower than #boxes .window */
#mask {
  position:absolute;
  z-index:9000;
  background-color:#000;
  display:none;
}
#boxes .window {
  position:absolute;
  width:440px;
  height:200px;
  display:none;
  z-index:9999;
  padding:20px;
  background-color:#eee;
}
/* Customize your modal window here, you can add background image too */
#boxes #dialog {
  width:375px; 
  height:203px;
}
</style>

<script type='text/javascript' src='http://ajax.aspnetcdn.com/ajax/jquery/jquery-1.5.1.min.js'></script>
			<script type='text/javascript'>
$(document).ready(function() {  
    //select all the a tag with name equal to modal
        
    //if close button is clicked
    $('.window .close').click(function (e) {
        //Cancel the link behavior
        e.preventDefault();
        $('#mask, .window').hide();
    });     
     
          
     
});
			</script>
<div id='boxes'>
    <div id='dialog' class='window'>
	<blockquote>
������Ҫ�ڿհ���������һ��<font color='red'>�µ�ͨ��֤�˺�</font>��������ɺ���ȷ������������Ϻ���Զ��ر��������
<br/>Ȼ��ʹ�øղ������<font color='red'>��ͨ��֤�˺�</font>��½������ֱ�ӵ����ת��ָ���������������ת������		
	</blockqupte>
        <a href='#' class='close'>�ر�</a>
    </div>
    <div id='mask'></div>
</div>

			";
	}else{
		$_box="
<style>
/* Z-index of #mask must lower than #boxes .window */
#mask {
  position:absolute;
  z-index:9000;
  background-color:#000;
  display:none;
}
#boxes .window {
  position:absolute;
  width:440px;
  height:200px;
  display:none;
  z-index:9999;
  padding:20px;
  background-color:#eee;
}
/* Customize your modal window here, you can add background image too */
#boxes #dialog {
  width:375px; 
  height:203px;
}
</style>

<script type='text/javascript' src='http://ajax.aspnetcdn.com/ajax/jquery/jquery-1.5.1.min.js'></script>
			<script type='text/javascript'>
$(document).ready(function() {  
    //select all the a tag with name equal to modal
    (function() {
        var id = '#dialog';
        //Get the screen height and width
        var maskHeight = $(document).height();
        var maskWidth = $(window).width();
        //Set height and width to mask to fill up the whole screen
        $('#mask').css({'left':0,'top':0,'width':maskWidth,'height':maskHeight});
        //transition effect     
        $('#mask').fadeIn(1000);    
        $('#mask').fadeTo('slow',0.8);  
        //Get the window height and width
        var winH = $(window).height();
        var winW = $(window).width();
        //Set the popup window to center
        $(id).css('top',  winH/2-$(id).height()/2);
        $(id).css('left', winW/2-$(id).width()/2);
        //transition effect
        $(id).fadeIn(2000); 
    })();
    //if close button is clicked
    $('.window .close').click(function (e) {
        //Cancel the link behavior
        e.preventDefault();
        $('#mask, .window').hide();
    });     
});
			</script>
<div id='boxes'>
    <div id='dialog' class='window'>
	<p>�����ɫ���֡�ת��ָ������������ֱ�ӽ���ת������</p>
	<p>���豣����ɫ�����������<font color='red'>������ɫ</font>�󼴿ɽ��뱣����ɫ����<font color='red'>(</font>�������Ѽ�¼����ҿɼ�<font color='red'>)</font></p>
        <a href='#' class='close'>�ر�</a>
    </div>
    <div id='mask'></div>
</div>

			";

	}
	$echo .= '<table width="100%" border="0" cellspacing="0" cellpadding="0" id="swapZoneTable">';
	if($changeNamePriv){
		$echo .= '<tr><td><input type="button" value=" ������ɫ " onclick="changeNameTableDh()"></td></tr>';
		}
	$i=0;
	foreach($aimZone as $k=>$v)
	{
		if($k==$_SERVER['HTTP_HOST']) continue;
		if($i%2==0) $echo .= '<tr>';
		$echo .= '<td style="padding-left:6px"><a href="javascript:go(\\\'http://'.$k.'/function/swap_Zone.php?id='.$_SESSION['id'].'\\\')" target="_top">'.$v.'</a></td>';
		if($i%2==1) $echo .= '</tr>';
		$i++;
	}//<a href="/"><strong></strong></a>
	$echo .= '</table>	</fieldset><form id="swapForm" method="post"><input type="hidden" name="sid" value="'.session_id().'"></form><a href="/"><strong>������Ϸ</strong></a><br/><div  style="width:385px" id="swapnoticetext">1. ����ҽ�ת������������н�ɫ����ת����ý�ɫ���ݻᱻ�����ֻ����ת��ǰ   ���ڵĽ�ɫ���ݡ������˺���Ϊtest�������һ���н�ɫA�������н�ɫB����ѡ��Ӷ���ת��һ������һ���Ľ�ɫA�ᱻ�����ת�����˺���Ϊtest������ڶ����޽�ɫ����һ���н�ɫB��<br/>2. ת���������ڴ����*10���ڴ�����*10��ֻ������'.substr($swapZoneSetting['starttime'],4,2).'��'.substr($swapZoneSetting['starttime'],6,2).'����'.substr($swapZoneSetting['endtime'],4,2).'��'.substr($swapZoneSetting['endtime'],6,2).'��ת����ң�����ת��ʱ��ҵ���ս������ڻ����40���Ż�õ�ת����������С��40���򲻻�õ��κν���<br/>3. ת��ǰ��ȷ����ɫ���������������ϵĿ�λ����ȷ���������������š�</div>';
	html($urlJump,$urlReg,false,"
<dd/>	$_box	<script language='javascript'>
var dom = document.getElementsByTagName('table')['3'].getElementsByTagName('td')[0];
dom.innerHTML = '".$echo."';
function changName()
{
	var nName = document.getElementById(\"nName\").value;
	if(!confirm('��ȷ������ͨ��֤�˺���д��ȷ�����򽫵����޷���ص���ʧ��\\n��ȷ��Ҫ����ɫת�Ƶ����ʺţ�'+nName+' ��\\n�뱣֤'+nName+'�����Լ��������˺ţ������ڱ��������ڽ�ɫ��\\n��ֻ��һ�ν�ɫת�ƵĻ��ᣡ�����ȷ������д����ȷ�ģ�'))
	{
		return;
}
document.getElementById(\"chgNameForm\").submit();
}
function changeNameTableDh()
{

	var obj = document.getElementById(\"changeNameTable\");
	var obj1 = document.getElementById(\"swapZoneTable\");
	var obj2= document.getElementById(\"swapnoticetext\");
	if(obj.style.display=='none')
	{
		(function() {
			var id = '#dialog';
			//Get the screen height and width
			var maskHeight = $(document).height();
			var maskWidth = $(window).width();
			//Set height and width to mask to fill up the whole screen
			$('#mask').css({'left':0,'top':0,'width':maskWidth,'height':maskHeight});
			//transition effect     
			$('#mask').fadeIn(1000);    
			$('#mask').fadeTo('slow',0.8);  
			//Get the window height and width
			var winH = $(window).height();
			var winW = $(window).width();
			//Set the popup window to center
			$(id).css('top',  winH/2-$(id).height()/2);
			$(id).css('left', winW/2-$(id).width()/2);
			//transition effect
			$(id).fadeIn(2000); 
		})(); 


		obj.style.display='block';
		obj1.style.display='none';
		obj2.style.display='none';
	}
	else
	{
		obj1.style.display='block';
		obj.style.display='none';	
		obj2.style.display='block';
	}
}
function go(url){var f=document.getElementById(\"swapForm\");f.action=url;f.submit();}
</script>
	");
}
//�û�������Ǳ�ҳ������Ǳ�ҳ�汻����
else if(strpos($_SERVER['PHP_SELF'],basename(__FILE__))!==false)
{
//�������ת��������
if(isset($_GET['id'])&&intval($_GET['id'])>0&&isset($_POST['sid'])&&strlen($_POST['sid'])==32)
{
	$userid=intval($_GET['id']);	
	$rfr=parse_url($_SERVER['HTTP_REFERER']);
	$rfr['host'] = strtolower($rfr['host']);
	$html = "";
	if(!isset($rfr['host'])) die("��������");
	if(
		!array_key_exists($rfr['host'],$fromZone)
		&&
		!array_key_exists($_SESSION['swapFrom'],$fromZone)
	){
		die("ת������δ�����������ţ�".$_SESSION['swapFrom']);
	}
	if(!array_key_exists(strtolower($_SERVER['HTTP_HOST']),$aimZone)) die("����������ת����");
	if(!isset($_GET['swapMe'])||!isset($_SESSION['swapMe']))
	{
		$qData = curlSS('http://'.$rfr['host'].'/function/swap_Zone.php?p='.$pwd.'&qid='.$userid.'&sid='.$_POST['sid']);
		//$qData = socketData($rfr['host'],'function/swap_Zone.php?p='.$pwd.'&qid='.$userid.'&sid='.$_POST['sid']);	//��ѯ��������


		if(ERROR_AUTH==$qData) die("δ��Ȩ�ķ���(".'function/swap_Zone.php?p='.$pwd.'&qid='.$userid.'&sid='.$_POST['sid'].")��");
		//$qData=preg_split("/".$GLOBALS['block']."/",$qData,0,PREG_SPLIT_NO_EMPTY);
		$data =preg_split("/".$vbcrlf."/",$qData[1],0,PREG_SPLIT_NO_EMPTY);
		$userInfo = divideData($data[1]);
		connect();
		$userLocal = query('select nickname from player where name like binary "'.$userInfo['name'].'"');

		$nickLocal = query('select name,nickname from player where nickname like binary "'.$userInfo['nickname'].'" and name<>"'.$userInfo['name'].'"');

		$warning = "";
		if(!empty($userLocal))
		{
			$warning = "<font color=#ff0000>��Ҫ��ʾ������������ڱ�����ɫ��Ϊ��".$userLocal[0]['nickname']."�Ľ�ɫ,����ɾ����</font>&nbsp;&nbsp;";
		}
		if($qData)
		{
			$_SESSION['swapMe'] = $userid;
			$_SESSION['swapFrom'] = $rfr['host'];

			$html .= "<div style='width:390px;color:#000000'>ת����Ϣ(����һ���������)��<br/>";
			$html .= "������ȷ��Ҫ������<u>".$rfr['host']."</u>��
					��ɫ��Ϊ��<u>".$userInfo['nickname']."</u>�Ľ�ɫת�뱾��ô��<br/>
					".$warning."
					<form action='?id=".$userid."&swapMe=yes' method=\"post\" id=\"doswapform\">
					";
			if(!empty($nickLocal))
			{
				$html .= "���Ľ�ɫ���Ѿ����ڣ����޸�:".'<input type="text" id="bname" name="bname" onChange="C();" size=4><span id="cname"></span>'."";				
			}
			$html .= "<input type=\"hidden\" name=\"sid\" value=".$_POST['sid'].">
					<input type='button' value='ȷ��ת��' onclick='subit=true;C();' id='sbbtn123'>&nbsp;&nbsp;<input type='button' onclick='history.back()' value='ȡ��'>
					<font color='red'>���<b>ȷ��ת��</b>�������ת������</font>
					</form>					
					</div>
			<script language=\"javascript\" src=\"/javascript/prototype.js\"></script>
<script languge='javascript'>
var subit=false;
function C()
{
	var obj=document.getElementById('bname');
	if(obj){
		var id='cname';
		var op='';
		if(id=='cuser') op='n='+obj.value;
		else op='n='+obj.value;
		var opt = {
			method: 'get',
				onSuccess: function(t) {
					$(id).innerHTML=t.responseText;
					if((t.responseText=='ok' || t.responseText=='OK')&&subit)
					{
						$('sbbtn123').disabled=true;
						$('doswapform').submit();
						subit=false;
		}else if(subit)
		{
			if(t.responseText.replace(/[\w<>=\"'\/]/g,'') != ''){
				alert(t.responseText.replace(/[\w<>=\"'\/]/g,''));
		}
		}
		},
			on404: function(t) {
		},
			onFailure: function(t) {
		},
			asynchronous:true        
		}
		//$(id).innerHTML+='/login/loginCheck.php?'+op;
		var ajax=new Ajax.Request('/login/loginCheck.php?'+op, opt);
		}
		else{
			if(subit)
			{
				$('doswapform').submit();
		}
		}
		}
		</script>
					";
		}
		else
		{
			if($_SESSION['username']=="leinchu"&&isset($_GET['dbg'])){
				//echo file_get_contents('http://'.$rfr['host'].'/'.'function/swap_Zone.php?p='.$pwd.'&qid='.$userid.'&sid='.$_POST['sid']);
				echo curlSS('http://'.$rfr['host'].'/'.'function/swap_Zone.php?p='.$pwd.'&qid='.$userid.'&sid='.$_POST['sid']);
				echo '<b>'.__FILE__.'-->'.__LINE__.'</b><br/><pre>=';
				var_dump($rfr['host'],'function/swap_Zone.php?p='.$pwd.'&qid='.$userid.'&sid='.$_POST['sid']	);
				echo '</pre>';
			}
			die("���ӷ�����ʧ��(00)!");
		}
	}
	else if(isset($_SESSION['swapFrom']))
	{		
		//$qData = socketData($_SESSION['swapFrom'],'function/swap_Zone.php?swapMe=yes&p='.$pwd.'&qid='.$userid.'&sid='.$_POST['sid'].'&swapto='.$_SERVER['HTTP_HOST']);	//��ѯ��������
		$qData = curlSS('http://'.$_SESSION['swapFrom'].'/function/swap_Zone.php?swapMe=yes&p='.$pwd.'&qid='.$userid.'&sid='.$_POST['sid'].'&swapto='.$_SERVER['HTTP_HOST']);
		//var_dump($qData);
		//$qData=preg_split("/".$GLOBALS['block']."/",$qData,0,PREG_SPLIT_NO_EMPTY);

		if(ERROR_AUTH==$qData) die("δ��Ȩ�ķ��ʣ�");
		if(ERROR_SECID_STATUS==$qData) die("���Ѿ�ת�����������ʺű����ᣡ");
		if(count($qData)!=7){
			die("�������ڣ�������һ��������ɣ�<br>��رյ�ǰ��������¿���������ԣ�");
		}
		if($qData)
		{
			connect();
			if(isset($_REQUEST['dbg'])){
				print_r($qData);
				die();
			}
			if(saveData($qData,$userid,$_SESSION['swapFrom'])){				
				unset($_SESSION);
				die(
				'
<script language="javascript">
alert("ת��ɹ�");
window.location="/";
</script>
				');
			}else{
				die(
				'
<script language="javascript">
alert("'.$GLOBALS['srMsg'].'\n�롰���·��͡���");
window.history.back(-1);
</script>
				');
			}
		}
		else
		{
			die("���ӷ�����ʧ��(01)!");
		}
	}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=gb2312" />
<title>�ڴ�����ת��</title>
<style type="text/css">
<!--
body {
	background-color: #2CA67F;
	margin-left: 0px;
	margin-top: 0px;
	margin-right: 0px;
	margin-bottom: 0px;
}
.STYLE1 {
	color: #00553a;
	font-size: 12px;    
	line-height: 20px;
}

#mbox{background-color:#eee; padding:8px; border:2px outset #666;}
#mbm{font-family:sans-serif;font-weight:bold;float:right;padding-bottom:5px;}
.dialog {display:none}

                  

-->
</style>
</head>
	<script type="text/javascript">
	</script>
<body>



<div id="hh">
<table width="890" border="0" align="center" cellpadding="0" cellspacing="0">
  <tr>
	<td><img src="/login/image/zc24.jpg" width="890" height="140"></td>
  </tr>
</table>
<table width="890" border="0" align="center" cellpadding="0" cellspacing="0">
  <tr>
	<td width="246"><img src="/login/image/zc25.jpg" width="246" height="228"></td>
  <td width="166"><img src="/login/image/zc28.jpg" width="166" height="228" border="0" usemap="#Map"></td>
	<td width="144"><img src="/login/image/zc26.jpg" width="152" height="228" border="0" usemap="#Map2"></td>
	<td width="145"><img src="/login/image/zc31.jpg" width="137" height="228"></td>
	<td><img src="/login/image/zc27.jpg" width="189" height="228"></td>
  </tr>
</table>
<table width="890" border="0" align="center" cellpadding="0" cellspacing="0">
  <tr>
	<td width="170" valign="top"><img src="/login/image/zc21.jpg" width="170" height="252"></td>
	<td valign="top"><table width="100%" border="0" cellspacing="0" cellpadding="0">
	  <tr>
		<td height="132" align="left" valign="top" background="/login/image/zc30.jpg" bgcolor="#B5E1D2" style=" line-height:1.7;font-size:12px;color:red;padding-left:60px; font-weight:bold">
			<?php echo $html; ?>
		</td>
	  </tr>

	  <tr>
		<td><img src="/login/image/zc29.jpg" width="531" height="120"></td>
	  </tr>
	</table></td>
	<td width="189" valign="top"><img src="/login/image/zc22.jpg" width="189" height="252"></td>
  </tr>
</table>
</div>
	<script language="javascript">
	document.write('<map name="Map" id="Map">');
	document.write('<area onFocus="this.blur()" shape="circle" coords="79,64,64" href="http://passport.webgame.com.cn/login.do?forward=http://'+window.location.host+'/login/dl.html&gameType=ff" target="_self" />');
	document.write('</map><map name="Map2" id="Map2"><area onFocus="this.blur()" shape="circle" coords="88,114,55" href="http://passport.webgame.com.cn/register.do" target="_self" /></map>');
	</script>
	<script language="javascript">
	document.write('<script language="javascript" src="/login/get.info.php'+window.location.search+'"><\/script>');
	</script>
</body>
</html>
<?php 
	die();
}

//ȡ��ת�����������ת���ķ���������ʧ�ܣ�ͨ������������ȡ��ת��
if(isset($_GET['cancelSwap'])&&isset($_GET['id'])&&intval($_GET['id'])>0&&isset($_GET['p'])&&strlen($_GET['p'])==32)
{
	$mem = new mem($_pm['mem']);
	$pwd = $mem->{'cancel_pwd_'.intval($_GET['id'])};

	if($pwd && $pwd  == $_GET['p'])
	{
		unset($mem->{'cancel_pwd_'.intval($_GET['id'])});
		unset($mem->{'swapMe_'.intval($_GET['id'])});
		connect();
		query('update player set secid=0 where id='.intval($_GET['id']));
		echo "��ԭ���ݳɹ���";
	}
	die("!");
}

//����������������ѯ�������ݺ�ת������һ�β�ѯֻ���û���Ϣ��
if(isset($_GET['qid'])&&intval($_GET['qid'])>0&&$_GET['p']==$pwd&&isset($_GET['sid'])&&strlen($_GET['sid'])==32)
{
	echo HTTP_CONTENT_STARTED;
	$userid=intval($_GET['qid']);
	if(!isset($_SESSION['id'])||$_SESSION['id']!=$userid)
	{
		die(ERROR_AUTH);
	}
	connect();
	$userInfo = query('select * from player where id='.$userid);


	//ͬѧ��ͨ��֤���Ȳ�һ�����⴦��ʼ
	$userInfo[0]['name'] = substr($userInfo[0]['name'],0,18);
	//ͬѧ��ͨ��֤���Ȳ�һ�����⴦�����

	if(empty($userInfo)||$userInfo[0]['secid']>0)
	{
		die(ERROR_SECID_STATUS);
	}
	$memHandle = $_pm['mem']->getHandle();

	$swapMe = $_pm['mem']->get('swapMe_'.$userid);


	if(!isset($_GET['swapMe'])||!$swapMe)
	{
		$memHandle->set('swapMe_'.$userid,1,0,60);
		echo formatData($userInfo,'player');
		die();
	}
	else
	{
		$cancel_pwd = md5(time().$_SERVER['HTTP_HOST']);
		$memHandle->set('cancel_pwd_'.$userid,$cancel_pwd,0,60);
		$_pm['mem']->set(array('k'=>'cancel_pwd1_'.$userid,'v'=>$cancel_pwd));		
		echo $cancel_pwd.formatData($userInfo,'player');
	}

	//��鴦��
	$merge=query("select merge,send,request_merge from player_ext WHERE uid ={$userid}");
	if($merge[0]['merge']>0){
		query("UPDATE player_ext SET request=0,merge=0,request_merge=0,send='' WHERE uid = {$userid} or uid={$merge[0]['merge']}");
		$sum_send=query("SELECT id FROM userbag WHERE uid={$userid} and pid=2381 LIMIT 0,1");
		if(!empty($sum_send[0]['id'])){
			query("UPDATE userbag SET sums=sums+1  WHERE uid={$userid} and id={$sum_send[0]['id']}");
		}else{
			query("INSERT INTO userbag(uid,pid,sell,vary,sums,stime) VALUES({$userid},2381,1,1,1, ".time()." )");
		}
		//$merge1=query("select request from player_ext where uid = {$merge[0]['merge']}");
		$tt=date('Y-m-d H:i:s',time());
		$usernickname0=query("select nickname from player where id={$userid}");
		//if($merge1[0]['request']==1){
		query("insert into information(uid,times,content) values({$merge[0]['merge']},'{$tt}','��ҡ�{$usernickname0[0]['nickname']}����ת����������ǿ����飡')");
		//}else{
		//query("insert into information(uid,times,content) values({$merge[0]['merge']},'{$tt}','��ҡ�{$usernickname0[0]['nickname']}������ǿ����飡')");
		//}
	}elseif($merge[0]['request_merge']>0){
		$send1=explode(',',$merge[0]['send']);
		$bid=$send1[1];
		$n=$send1[0];
		$sum_send=query("SELECT id FROM userbag WHERE uid={$userid} and pid={$bid} LIMIT 0,1");
		if(!empty($sum_send[0]['id'])){
			query("UPDATE userbag SET sums=sums+{$n}  WHERE uid={$userid} and id={$sum_send[0]['id']}");
		}else{
			query("INSERT INTO userbag(uid,pid,sell,vary,sums,stime) VALUES({$userid},{$bid},1,{$n},1, ".time()." )");
		}
		query("UPDATE player_ext SET request=0,merge=0,request_merge=0,send='' WHERE uid = {$userid}");
	}





	$userBag = query('select * from userbag where uid='.$userid);
	echo formatData($userBag,'userbag');

	$userBb = query('select * from userbb where uid='.$userid);
	echo formatData($userBb,'userbb');

	$userBbSkill = query('select skill.* from skill,userbb where userbb.id=skill.bid and userbb.uid='.$userid);
	echo formatData($userBbSkill,'skill');

	$userTask = query('select * from tasklog where uid='.$userid);
	echo formatData($userTask,'tasklog');

	$player_ext = query('select * from player_ext where uid='.$userid);
	echo formatData($player_ext,'player_ext');

	query('update player set secid=40 where id='.$userid);

	$keyCurUser  = $userid . "chat";
	if(isset($keyCurUser)) //�ѱ���(ת��������)�ʺ�������
		$GLOBALS['_pm']['mem']->del($keyCurUser);	
	$swapto = $_GET['swapto'];
	$sqlLog = '
		insert 
		into 
		gamelog
		(ptime,	seller,	buyer,	pnote,	vary) 
		values
		(unix_timestamp(),"'.$userInfo[0]['name'].'","SWAP_ZONE_LOG","'.$swapto.','.print_r($userInfo[0],1).'",13)
		';
	query($sqlLog);
	$sqlPai = 'update userbag 
		set 
		psell=0,
		pstime=0,
		petime=0,
		sums=sums+psum,
		psum=0,
		buycode=""
		where uid='.$userid;

	query($sqlPai);
	die();
}


echo "Ĭ����Ӧ��";

}

function curlSS($url,$port=80){
	$post = 1;
	$returntransfer = 1;
	$header = 0;
	$nobody = 0;
	$followlocation = 1;

	$ch = curl_init();
	$options = array(CURLOPT_URL => $url,
		CURLOPT_HEADER => $header,
		CURLOPT_NOBODY => $nobody,
		CURLOPT_PORT => $port,
		CURLOPT_POST => $post,
		CURLOPT_POSTFIELDS => $request,
		CURLOPT_RETURNTRANSFER => $returntransfer,
		CURLOPT_FOLLOWLOCATION => $followlocation,
		CURLOPT_COOKIEJAR => $cookie_jar,
		CURLOPT_COOKIEFILE => $cookie_jar,
		CURLOPT_REFERER => $url
	);
	curl_setopt_array($ch, $options);
	$result = curl_exec($ch);
	curl_close($ch);
	$result=preg_split("/".$GLOBALS['block']."/",$result,0,PREG_SPLIT_NO_EMPTY);
	return $result;
}
?>
