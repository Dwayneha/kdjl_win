<?php
//[--> ��������д����������js
require_once('../config/config.game.php');

$someOneTalkMemFlagKey = "someOneTalkMemFlag";
$flag = unserialize($_pm['mem']->get($someOneTalkMemFlagKey));
if(!$flag){
	sleep(0.5);//������д���Ȱ��룬������ǲ��У���ôӦ���������ȵ��߳����ڱ��߳�ִ���˸���
	$flag = unserialize($_pm['mem']->get($someOneTalkMemFlagKey));
}
if(!$flag){
	$_pm['mem']->set(array('k'=>$someOneTalkMemFlagKey,'v'=>1));

	//$m = $_pm['mem'];	// Init memcache.
	/*
	$crc = crc32($_REQUEST[PHPSESSID]);
	$user = unserialize($_pm['mem']->get($crc));
	$key = crc32($user);
	$key = $key<1?1-$key-1:$key;

	$time = time();

	if($time+60>time())
	{   $h = date("i",$time);
	//�޸�����ʱע���޸�serverGate.php
	if($h==50)//$h==10||$h==30 ||$h==50 || $h==40
	{
	$trs = $_pm['mysql']->getOneRecord("select id,msg,url
	from gamead
	order by id desc
	limit 0,1");
	if (is_array($trs))
	{
	$newid = rand(1,$trs['id']);
	$lrs=$_pm['mysql']->getOneRecord("select id,msg,url
	from gamead
	where id={$newid}");
	$cmdresult = $lrs['msg'].'#'.$lrs['url'];
	//$time=$time+70;
	}else $cmdresult=0;
	}
	else if ($h==48) $cmdresult=2;
	else $cmdresult=0;
	}
	else {$cmdresult=1;$time=time();}

	$cm =  stripslashes(unserialize($_pm['mem']->get('chatMsgList'))); // get every player information from memcache.

	$cm =  ($cm==false?'':str_replace(chr(13),'',$cm));
	$cm =  formatMsg($cm);

	$word	= unserialize($_pm['mem']->get(MEM_SYSWORD_KEY));
	if (strlen($word)>5)
	{
	$_pm['mem']->set(array('k'=>MEM_SYSWORD_KEY, 'v'=>0));
	}else $word=1;

	// team
	$team = unserialize($_pm['mem']->get($key));
	if (is_array($team) && $team[1]==$user)
	{
	$tword=$team[0];
	$_pm['mem']->set(array('k'=>$key, 'v'=>0));
	}else $tword=0;
	*/
	function get_http_mdate()
	{
		return gmdate("D, d M Y H:i:s",time())." GMT";
	}
	function etag(){
		$str=md5(microtime());
		//73019c-673-64b74fc0
		return substr($str,0,6).'-'.substr($str,7,3).'-'.substr($str,11,8);
	}
	$msg_key = 'chatMsgListLoundSpeaker';//С����
	$loudspeak	= unserialize($_pm['mem']->get($msg_key));

	$setcookie="";

	if(is_array($loudspeak)){
		$keys = array_keys($loudspeak);
		arsort($keys);
		$setcookie="setcookie('displayedMsgId',".intval($keys[0]).",365*v*3600+time(),'/',\$_SERVER['HTTP_HOST']);";
	}else{
		$setcookie="//".count($loudspeak);
	}

	$retstr = $tword."#team#".$word.'#word#'.$cmdresult.'#msg#'.$cm;
	$somecontent  = '<?php
	header("Content-type: text/javascript; charset=GBK"); 
	header("Last-Modified: '.get_http_mdate().'");
	//header("Cache-Control: max-age=3600");
	header("ETag: '.etag().'");
	$displayedId=isset($_COOKIE["displayedMsgId"])?$_COOKIE["displayedMsgId"]:0;
	'.$setcookie.'
	?>
	setTimeout("re();",3000);	
	if(typeof(loudSpeaksMsg)=="undefined"){var loudSpeaksMsg={};}
	loudSpeaksMsg={};//����
	try{
';

	if(is_array($loudspeak)){
		$somecontent .= '
';
		foreach($loudspeak as $k=>$v){
			$k = intval($k);
			//< ?php if($displayedId<'.$k.'){ ? >< ?php } ? >
			$somecontent .= '
			loudSpeaksMsg["'.$k.'"]="'.str_replace(array("\r","\n",'"'),array('','','\"'),$v).'";			
';
		}
	}

	$somecontent .= '
	}catch(e){}
';
	$filename = dirname(__FILE__)."/messageData.php";

	if (!$handle = fopen($filename, 'w+')) {
		echo "���ܴ��ļ� $filename";
		exit;
	}

	if (fwrite($handle, $somecontent) === FALSE) {
		echo "����д�뵽�ļ� $filename";
		exit;
	}

	//echo $filename;

	fclose($handle);

	unset($retstr, $cmdresult);
	//д����� <--]
	$_pm['mem']->set(array('k'=>$someOneTalkMemFlagKey,'v'=>0));
}else{
	$_pm['mem']->set(array('k'=>$someOneTalkMemFlagKey,'v'=>0));
	//echo 'Update later('.$flag.')!';
}


/**
Chat function
$msg: example: altc: С����linendm����mС����: ��������```linend�L�: ��ǰ��15��������һ���ɳ�4linend����: 30����4.9linend      ��į..: - -��ȫ��ɫ��
*/
function formatMsg($msg)
{
	global $user;
	if ($msg == '') return $msg;
	$arr = explode('linend', $msg);
	$patterdes = 'm'.$user.':';
	$pattersrc = 'm'.$user.'m';
	$retmsg = '';
	foreach ($arr as $k => $mg)
	{
		if (substr($mg,0,1)=='m' && strpos($mg, $patterdes)!==false) // recive user
		{
			// split the result.
			$try = explode($patterdes, $mg,2);
			$fromuser = substr($try[0],1);
			$mg = '<font color=#B64ABA><u>{<span>}'.$fromuser.' </span></u> => '.$try[1].'</font>';
		}
		else if(substr($mg,0,1)=='m' && strpos($mg, $pattersrc)!==false) // send user
		{
			// split the result.
			$try = explode(':', $mg,2);
			$fromuser  = str_replace($pattersrc, "", $try[0]);

			$mg = '<font color=#B64ABA>/'.$fromuser.' '.$try[1].'</font>';
		}

		// for gm.
		if (substr($mg,0,1)=='m' && ($user=='GM' || $user == 'tanwei' || $user == 'С��ˮ��' || $user == '��ˮ�ĺ�Ѽ')) // recive user
		{
			$mg = substr($mg, 1);
			$mg = str_replace('m', ' => ',$mg);
			$mg = '<font color=#B64ABA>'.$mg.'</font>';
		}

		if (substr($mg,0,1) == 'm') continue;
		else $retmsg .= $mg.'linend';
	}
	for($i=1;$i<=36;$i++)
	{	$src[$i] = "(".$i.")";
	$src1[$i] = "[".$i."]";
	if($i<=26)
	{
		$des1[$i] = '<img src=images/ui/motion1/'.$i.'.gif>';
	}
	$des[$i] = '<img src=images/ui/motion/'.$i.'.gif>';
	}
	$okret=str_replace($src,$des,substr($retmsg,0,-6));
	$okret=str_replace($src1,$des1,$okret);
	$okret=str_replace("{<span>}","<span style=cursor:pointer onclick=\$('cmsg').value='/'+this.innerHTML;>",$okret);
	//$okret=str_replace(array("{","}"),array("<span style=color:green>��<a href='#' onclick=showchatTip(this.innerHTML,this) onmouseout=chatuntip() style=cursor:pointer;color:green;>","</a>��</span>"),$okret);

	unset($src,$des);
	return $okret;
}
?>