<?php
require_once('./config/config.game.php');
error_reporting('0');
//require_once((dirname(__FILE__)).'/kernel/dbSession.v1.php');
//����IP���ܿ�ʼ

//require('/home/poke/webapps/pmmg1/ipAdmin/ip.php');
/*
$addr=$_SERVER['REMOTE_ADDR'];
$addr_ip = $_pm['mysql'] -> getOneRecord("select count(*) as ips from ip where Ip='{$addr}'");
//var_dump($addr);
//die();
if($addr_ip['ips']>0)
{
  echo "����IP������ע����ߵ�¼������ϵ����Ա!";
  die();
}
//�������ܽ���
*/

if($_SESSION['id'] == "")
{
	die('<script type="text/javascript">window.location="login/login.php"</script>');
}
if(GAME_SERVER_FLAG != $_SESSION['game_server_flag']){
	die('<script type="text/javascript">window.location="passport/login.php"</script>');
}
secStart($_pm['mem']);

define("WEL","db_welcome1");
$cmd = unserialize($_pm['mem'] -> get(WEL));
/*
if($_SESSION['lys_id'] != 'webgame')
{
	$cmd_lys = unserialize($_pm['mem'] -> get('db_T_ly_URL_config'));
	foreach($cmd_lys as $info)
	{
		if($info['F_lys_id'] == $_SESSION['lys_id'])
		{
			$cmd['guanwang'] = $info['F_index_url'];
			$cmd['pay'] = $info['F_pay_url'];
			$cmd['kefu'] = $info['F_gm_url'];
			$cmd['discuss'] = $info['F_bbs_url'];
			break;
		}
	}
}
*/
$giftword = str_replace("\r\n",'!@#$^',$cmd['swfemotion']);

if(isset($_SESSION['ghpstr'])){
	if(!empty($_SESSION['ghpstr'])){
		$user	 = $_pm['user']->getUserById($_SESSION['id']);
		$bag	= $_pm['user']->getUserBagById($_SESSION['id']);
		$numarr = explode("\r\n",$_SESSION['ghpstr']);
		$arr = explode(',',$numarr[0]);
		$task = new task();
		if(is_array($arr)){
			foreach($arr as $v){
				$inarr = explode(':',$v);
				if(count($inarr) != 2){
					continue;
				}
				$givecheck = $task->saveGetPropsMore($inarr[0],$inarr[1]);
				$parr = $_pm['mysql'] -> getOneRecord("SELECT name FROM props WHERE id = {$inarr[0]}");
				$str .= '�����Ʒ��'.$parr['name'].'&nbsp;'.$inarr[1].' ����';
			}
		}
		$pnote = 'card='.$_REQUEST['cardid'].'&pass='.$_REQUEST['pwd'].'&Ӧ�ý�����'.$numarr[0].'----ʵ�ʣ�'.$str.'-----'.$checkflag;
		$_pm['mysql'] -> query("insert into gamelog (ptime,seller,buyer,pnote,vary) values (".time().",{$_SESSION['id']},{$_SESSION['id']},'$pnote',91)");
		$_SESSION['ghpstr'] = '';
		$_SESSION['ghflag'] = '';
	}
}
$configWelcome = unserialize($_pm['mem']->get('db_welcome'));
function setConfiguration(){
	global $configWelcome,$_pm;
	$dt = date("YmdHi");
	$configWel=$configWelcome;
	if(empty($configWel)||!is_array($configWel))
	{
		$configWel = array();
		echo "db welcome not found!\r\n";
	}
	
	foreach($configWel as $row)
	{
		if($row['code']=='admin'){
			//echo $row['contents']."\r\n";
			//$configuration[$row['code']] = explode(',',$row['contents']);
			$_pm['mem']->getHandle()->set('gm_string',$row['contents']);
			break;
		}
	}
	
	//$configuration['admin'] = array_flip($configuration['admin']);	
	
	$configWel = unserialize($_pm['mem']->get('db_gonggao'));
	if(empty($configWel)||!is_array($configWel))
	{
		echo "db gonggao not found!\r\n";
		return;
	}
	$configWel['gonggao']=array();
	foreach($configWel as $row)
	{
		if(!isset($row['endtime']))
		{
			continue;
		}
		if($row['endtime']>$dt)//û�н�����
		{	
			if(!isset($configWel['gonggao'])) $configWel['gonggao']=array();
			$row['msg'] = iconv("gbk",'utf-8',$row['msg']);
			$configWel['gonggao'][] = $row;
		}
	}
	$tmp="";
	foreach($configWel['gonggao'] as $v)
	{
		$tmp.=implode('`|"',$v).chr(1);
	}
	$_pm['mem']->getHandle()->set('gg_string',substr($tmp,0,-5));
}
setConfiguration();


require_once('socketChat/config.chat.php');

if(!isset($_SESSION['nicknamegb']))
{
	$_SESSION['nicknamegb'] = $_SESSION['nickname'];
}

$gjs='';

$arr = $_pm['mysql'] -> getOneRecord("select new_guide_step,last_logintime,onlinetime_today,last_online_day,last_onlinetime,onlinetime from player_ext where uid=".$_SESSION['id']);
if(is_array($arr)&&$arr['new_guide_step']!=-1&&$arr['new_guide_step']<20)
{
	$gjs='
	<script language="javascript">
	new_guide_step_db='.$arr['new_guide_step'].';
	</script>
	<script language="javascript" src="/javascript/newguide.js"></script>
	';
}

$tdStr=date('Ymd');
if($arr['last_online_day']!=$tdStr)
{
	if(date('Ymd',$arr['last_logintime'])!=$tdStr&&$arr['last_logintime']>10000000)
	{
		$sql='update player_ext set exp_got_step=0,last_online_day="'.date('Ymd').'",onlinetime_today="'.(date("H")*3600+date("i")*60+date("s")).'",last_onlinetime=onlinetime where uid='.$_SESSION['id'];
	}else{
		$sql='update player_ext set exp_got_step=0,last_online_day="'.date('Ymd').'",onlinetime_today=0,last_onlinetime=onlinetime where uid='.$_SESSION['id'];
	}
	$_pm['mysql'] -> query($sql);
}else{
	$sql='update player_ext set onlinetime_today=onlinetime_today+onlinetime-last_onlinetime,last_onlinetime=onlinetime where uid='.$_SESSION['id'];
	$_pm['mysql'] -> query($sql);
}

$arrOk = $_pm['mysql'] -> getOneRecord("select password from player where id=".$_SESSION['id']);
$_SESSION['password']=$arrOk['password'];

$callback=false;
foreach($configWelcome as $row)
{
	if($row['code']=='callback'){
		$callback=$row['contents'];
		break;
	}
}

$callbackhtml='';
if($callback){
	$callbackhtml='<div id="CallBackPrize" style="position: absolute; left: 130px; top: 375px; z-index: 10; width: 50px; height: 50px;" onclick="alert(\'�Բ���,�������ϱ������.�������ѯ�����������!\')"><img style="cursor:pointer" src="images/huigui.jpg"></div>';
}

$day=time()-$_SESSION['lastvtime'];
if($day>30*24*3600)
{
	$getM=$_pm['mem']->get('callgeted_'.$_SESSION['id']);
	if($callback&&!$getM)
	{
		$callbackhtml='<div id="CallBackPrize" style="position: absolute; left: 130px; top: 375px; z-index: 10; width: 50px; height: 50px;" onclick="if(confirm(\'����������ɳ��ʽϸߵó���Ϊ��ս��������ܻ�ýϸߵ�����\\n��ȷ��Ҫ������ȡô��\')){getCallBackPrize();}"><img style="cursor:pointer" src="images/huigui.jpg"></div>
		<script language="javascript">
		window.onbeforeunload=function()
		{
			Alert("��û����ȡ����һع齱������ע����ȡ���´��޷���ȡ��");
		}
		setTimeout(\'Alert("��û����ȡ����һع齱������ע����ȡ���´��޷���ȡ��")\',3000);
		</script>
		';
	}
}
session_write_close();

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=gb2312" />
<title>
<?=$cmd['title']?>
</title>
<link href="css/sl_mod.css" rel="stylesheet" type="text/css" />
<script src="javascript/sl_mod.js" type="text/javascript"></script>
<link href="css/global.css" rel="stylesheet" type="text/css" />
<script src="js/global.js" type="text/javascript"></script>
<script language=javascript src='/javascript/check.class.js'></script>
<script language=javascript src='/javascript/prototype.js'></script>
<script src="/javascript/scriptaculous.js" type="text/javascript"></script>
<script language=javascript src='/javascript/index.js'></script>
<script language=javascript src='/javascript/msg_html.js'></script>
<script language=javascript src='/javascript/socket.class.js'></script>
 <script type="text/javascript" src="/javascript/jquery-koudai-1.8.3.js"></script>
<script type="text/javascript" src="/javascript/chat_html5.js"></script>
<style> 
    #rmenu {   
        list-style:none;     
        margin:0;     
        padding:0;      
        border:1px solid #CCCCCC;   
        width:120px;   
        position:absolute;   
        left:0px;   
        top:0px;   
        display:none;   
        z-index:99999;   
        background-color:#F3F3F3   
    }   
    #rmenu li{   
        width:118px;   
        height:16px;   
        font-size:12px;   
        margin-left:1px;   
        margin-right:2px;   
        cursor:pointer;   
        padding-top:3px;   
        padding-left:2px;   
    }   
    #rmenu li:hover{   
        background-color:#c0c0c0;   
        color:#FF0000;   
    }
	#zpdiv
	{
		z-index:2002;
	}
	#lock_zp_div
	{	
		position:absolute;
		width:1000px;
		height:500px;
		background:#000000;
		top:0px;
		left:0px;
		z-index:2001;
		filter:alpha(opacity=0);
		opacity:0;
	
	}
	.saolei_index
	{
		width:500px;
		height:500px;
		position:absolute;
		top:50px;
		left:500px;
		color:#693600;
	}
    .whq_nav{
        margin: 110px 30px;
    }
    .whq_nav li:hover{
        transform: translate(3px, 3px);
    }
</style>

<style type="text/css">
.nav{width:459px; height:294px;color:#630;}
.nav_01{width:459px; height:37px; float:left;}
.nav_02{width:419px; height:279px; background-image:url(new_images/ui/cjjzbg03.gif); float:left; padding:15px 0 0 40px; } 
.tt1 {
	border: 1px solid #960;background-color:#F3E4B2;
}
.tt2 {
	border: 1px solid #960; height:50px; background-color:#F3E4B2; width:300px
}
</style>
</head>
<script language="javascript">
function DRAG(){
	if(!document.all){    
		Event = arguments[0];		
		try{UnTip();showm();}catch(e){}
	}else{
		Event = event;
		
		try{UnTip();showm();}catch(e){}
	}
}
if(!document.all){    
	window.onmousemove = DRAG;
}else{
	document.onmousemove = DRAG;
}


var myUid=<?php echo $_SESSION['id']+0; ?>;
var isIE  = (navigator.appVersion.indexOf("MSIE") != -1) ? true : false;
var isWin = (navigator.appVersion.toLowerCase().indexOf("win") != -1) ? true : false;
var isOpera = (navigator.userAgent.indexOf("Opera") != -1) ? true : false;
var CURSOR_HAND = 'c:pointer';
var friends={};
var blacks={};

var username="<?php echo $_SESSION["username"] ?>";

function   killErrors(){  
	alert(arguments[0]+"\n"+arguments[1])
	//return   true;
}     
function display_close()
{
	document.getElementById('zp_close').style.display = "none";
}
function bright_close()
{
	document.getElementById('zp_close').style.display = "block";
}
function zpgonggao(para)
{
	var opt = {
					method: 'get',
					onSuccess: function(t) 
					{
					},
					asynchronous:true        
				}
	var ajax=new Ajax.Request('/zpmod/gonggao.php?para='+para, opt);
}
window.onerror =  killErrors; 
var loudSpeaksMsg = {};
var displayedMsgId = 0;
function loadads()
{
	var host = window.location.host;
	var hostarr = host.split(".");
	var div = document.getElementById("adbottomleft");
	var div1 = document.getElementById("adbottomright");
	if(hostarr[1] == "webgame")
	{
		var firstword = hostarr[0].substr(2,2);
		if(firstword != "51")//����51��webgame
		{
			div.style.display="block";
			div1.style.display="block";
		}
		else
		{
			div.style.display="none";
			div1.style.display="none";
		}
	}
	else
	{
		div.style.display="none";
		div1.style.display="none";
	}

}
	
	function   killErrors(){  
		return   true;
	}     
	window.onerror =  killErrors; 
	var loudSpeaksMsg = {};
	var displayedMsgId = 0;
	
	

  String.prototype.trim = function() {
    return this.replace(/(^\s*)|(\s*$)/g, "");
  }

  function searchPocketBaike() {
    var searchKey = $("baike_input").value.trim();
    
    if (searchKey) {
        //var url = 'function/search_knol.php?key=' + searchKey;
        //window.open(url, '', 'location=no,status=no');
        //window.open(url, '', 'top=0,left=0,status=no,location=no,toolbar=yes,scrollbars=yes,menubar=no');
    } else {
      alert('��������Ч��Ϣ!');
      $("baike_input").value = searchKey;
      $("baike_input").focus();
    }
  }

  function showBugBox() {
      $("bugReportBox").innerHTML = '&lt;input type="text" id="knol_bug_text" size=50 />&lt;input type="button" value="����" />';
      $("bugReportBox").style.cursor = 'auto';
  }
	function cmotion()
	{
		var imgserver = "";
		try{
			 $('cmdiv').style.display='';
		}catch(e){
			var sp = document.createElement('DIV'); //hp font
				sp.style.cssText='position:absolute;left:380px;top:470px;z-index:100;font-size:0.8em;width:300px;border:1px solid #ccc;padding:5px;line-height:1.5;background-color:#fff;filter:alpha(opacity=85);';
				sp.id='cmdiv';
				document.body.appendChild(sp);
		}
		$('cmdiv').onclick=function(){$('cmdiv').style.display='none';};
		var allm = '';
		for(var i=1;i&lt;=36;i++)
		{
			allm = allm+"&lt;img src='"+imgserver+"images/ui/motion/"+i+".gif' onclick=\"sendm('("+i+")')\"/> ";
		}
			
		$('cmdiv').innerHTML = allm+"&amp;lt;span onclick=$('cmdiv').style.display='none' style=cursor:pointer;>&amp;nbsp;&amp;nbsp;&amp;lt;u>&amp;lt;b>&amp;lt;font color=red>��&amp;lt;/font>&amp;lt;/b>&amp;lt;/u>&amp;lt;/span>";
	}
	function sendm(m)
	{
		$('cmsg').value+=m;
	}
				
</script>
<script language="JavaScript">
function dealUserList(str)
{
	refreshFAndB();
	var strs = str.split(",");
	for(var s=0;s&lt;strs.length;s++)
	{
		updateForBOl(strs[s],1);
	}
	return true;
}
function refreshFAndB()
{
	var fstr=thisMovie("socketChatswf").getFriendList();
	var bstr=thisMovie("socketChatswf").getBlackList();
	var strs = [];
	var str = fstr;	
	
	if(str.length>0)
	{
		strs = str.split("|");
		for(var i=0;i&lt;strs.length;i++)
		{
			if(strs[i].length>0)
			{
				friends[strs[i].replace(/[\$\`]/g,'')] = 0;				
			}
		}
		updateFriendListHTML();		
	}
	
	str = bstr;	
	if(str.length>0)
	{
		strs = str.split("|");
		for(var i=0;i&lt;strs.length;i++)
		{
			if(strs[i].length>0)
			{
				blacks[strs[i].replace(/[\$\`]/g,'')] = 0;
			}
		}
	}
}

function updateFriendListHTML()
{
	var obj = $('frienlistDiv');
	obj.innerHTML='';
	var tmp = '&lt;ul style="list-style:none; line-height:21px">';
	for(name in friends)
	{
		color='#909090';
		if(friends[name]==1)
		{
			color='#009900';
		}
		tmp+='&lt;li style="cursor:pointer; color:'+color+'" onclick="$(\'cmsg\').value=\'//'+name+' \'">'+name+'</span></li>';
	}
	tmp+='</ul>';
	obj.innerHTML=tmp;
}

function updateForBOl(name,sts)
{	
	if(typeof(friends[name])!='undefined')
	{		
		friends[name] = sts;
		updateFriendListHTML();	
	}
	
	if(typeof(blacks[name])!='undefined')
	{
		blacks[name] = sts;
	}
}
var msgflag = 0
function AsCallBack(str)
{
	//alert(str)
	//recvMsg('CT|'+str);
	if(str=='updateYouTeam')
	{
		recvMsg('SM|�����Ա״̬�����ı䣡');
		if(document.getElementById('gw').contentWindow.document.getElementById('creatUTeam'))
		{
			if(document.getElementById('gw').contentWindow.location.href.indexOf('?')>-1){
				document.getElementById('gw').contentWindow.location=document.getElementById('gw').contentWindow.location.href+'&amp;rd='+Math.random();
			}else{
				document.getElementById('gw').contentWindow.location=document.getElementById('gw').contentWindow.location.href+'?rd='+Math.random();
			}
		}else{
			document.getElementById('gw').contentWindow.updateMyTeamInfo();
		}
	}else if(str=='uareKicked'){
		document.getElementById('gw').contentWindow.location='/function/Team_Mod.php';
		recvMsg('SM|�����ӳ��߳����飡');
	}else if(str=='disbandTeam'){
		document.getElementById('gw').contentWindow.location.reload();
		recvMsg('SM|�����ɢ��');
	}else if(str.substr(0,13)=='returnVillege'){
		document.getElementById('gw').contentWindow.location='/function/Team_Mod.php?n='+str.replace('returnVillege','');
	}else if(str=='getTeamFightMod'){
		document.getElementById('gw').contentWindow.getTeamFightMod();
	}else if(str.substr(0,16)=='getTeamFightGate'){
		document.getElementById('gw').contentWindow.getTeamFightGate(str.substr(16));	
	}else if(str=='information-->'){
		msgflag = 1;
		change_type();
	}else if(str.substr(0,4)=='_AL_'){
		Alert(str.substr(4));
	}else if(str=='changhp'){
		document.getElementById('gw').contentWindow.hpshow();
	}else if(str.substr(0,5) == 'tarot'){
		var strarr = str.split('->');
		document.getElementById('gw').contentWindow.tarotshow(strarr[1],strarr[2]);
	}else if(str.substr(0,10) == 'alertTarot'){
		var strarr = str.split('->');
		Alert(strarr[1]);
		document.getElementById('gw').contentWindow.hpshow();
	}else if(str.substr(0,7) == 'goTarot'){
		jumpTfb(1);
		var strarr = str.split('->');
		Alert(strarr[1]);
		document.getElementById('gw').contentWindow.hpshow();
	}else if(str.substr(0,8) == 'outTarot'){
		jumpTfb(1);	
		var strarr = str.split('->');
		Alert(strarr[1]);	
	}else if(str.substr(0,13) == 'fortress_boss'){
		jumpTfb(3);	
		var strarr = str.split('->');
		Alert(strarr[1]);	
	}
	
	if(str.indexOf('ȫ�屻�߳�����')!=-1)
	{
		jumpTfb(2);
	}
	//$('cmsg').value=str;
}
function jumpTfb(a)
{
	setTimeout('jumpTfbgo('+a+')',2000);
}
function jumpTfbgo(a)
{
	if(a==1)
	{		
		document.getElementById('gw').contentWindow.location='/function/Team_Mod.php';		
	}
	else if(a==2)
	{
		document.getElementById('gw').contentWindow.location='/function/Fight_Mod.php';
	}
	else if(a==3)
	{
		document.getElementById('gw').contentWindow.location='/function/Challenge_Mod.php';
	}
}

function change_type1(){
	if($('ab').className=='tools2'){
		$('ab').className='tools';
	}else{
		$('ab').className='tools2';
	}
	if(msgflag == 1){
		window.setTimeout("change_type()",500);
	}else{
		$('ab').className='tools';
	}
}
function change_type(){
	if(msgflag == 1){
		$('ab').className='msg';
	}else $('ab').className='';
}
</script>
<script language="JavaScript">
var jsReady = false;

function isReady() {
	return jsReady;
}

var tmR;
var reloadFlag = true;
function checkOnlinePrize()
{
	var opt = {
					method: 'get',
					onSuccess: function(t) {
								if(t.responseText.substr(0,2)=='OK')
								{
									if(!$('getOnelinePrize'))
									{
										var o=document.createElement('div');
										o.id='getOnelinePrize';
										/////////////
										var left = (parseInt(document.documentElement.clientWidth)-1000)/2;
										var top1 = (parseInt(document.documentElement.clientHeight)-614)/2;
										top1 = top1&lt;0?0:top1;
										o.style.cssText='position:absolute;left: 50px; top: 375px;z-index:10;width:50px;height:50px';
										
										document.body.appendChild(o);
										
										
										
									}
									if(t.responseText.substr(2,12)=='0')
									{
										$('getOnelinePrize').innerHTML='&lt;span style="cursor:pointer" onclick="getOnlinePrize()">&lt;img src="images/getch.gif">&lt;/span>';
										setTimeout('checkOnlinePrize()',600000);
									}else{
										$('getOnelinePrize').innerHTML='&lt;img style="filter: Gray;cursor:pointer" onclick="Alert(\'��û�е���ȡʱ�䣡\')" src="images/getop.jpg">';
										var time=(parseInt(t.responseText.substr(2,12))+5)*1000;
										if(time>0)
											setTimeout('checkOnlinePrize()',time);
										else
											setTimeout('checkOnlinePrize()',600000);
									}
								}
							},
					asynchronous:true        
				}
	var ajax=new Ajax.Request('/function/onlineForPrizeCheck.php', opt);
}
function getOnlinePrize()
{
	var opt = {
					method: 'get',
					onSuccess: function(t) {
								Alert(t.responseText);								
								if(t.responseText.indexOf('<!--OK-->')!=-1)
								{
									checkOnlinePrize();
								}
							},
					asynchronous:true        
				}
	var ajax=new Ajax.Request('/function/onlineForPrize.php', opt);
}
function getCallBackPrize()
{
	var opt = {
					method: 'get',
					onSuccess: function(t) {
									Alert(t.responseText);
									if(t.responseText.indexOf('<!--OK-->')!=-1){
										$('CallBackPrize').style.display='none';
										window.onbeforeunload = function(){ 
										return checkTF();
									}
								}
							},
					asynchronous:true        
				}
	var ajax=new Ajax.Request('/function/getCallBackPrize.php', opt);
}

function pageInit() {
	try{
		window.clearTimeout(tmR);
	}catch(e){}
	jsReady = true;
	tmR = window.setTimeout('doReload()',15000);
	recvMsg("SM|��Ҫ����������......");
	checkOnlinePrize();
}
var reconnectTimes=0;
var reloadT=false;
var recvedMsgFlag=false;

function doReload()
{
	if(recvedMsgFlag)
	{
		setTimeout('recvedMsgFlag=false;',5000);
		return;
	}
	if(!reloadFlag&amp;&amp;arguments.length==0) return;
	reconnectTimes++;
	try{
		if(reconnectTimes>5){
			recvMsg("CT|�����������,��ˢ��ҳ��!");
			return;
		}
		if(reconnectTimes&lt;=3){
			recvMsg("CT|�����������ӷ�����...");
		}else{
			recvMsg("CT|�����������ӷ�����...����ʱ���޷����ӣ�������&lt;a onclick='window.location.reload()' style='color:#ff0000'>ˢ��&lt;/a>ҳ�档����Ͽ�ʱ,����޷�����ս��!");
		}
	}catch(e){
	}
	
	try{
		thisMovie("socketChatswf").reconnectSocket();
	}catch(e){
	}
	reloadFlag = true;
	reloadT = setTimeout("doReload()",30000);
	return true;
}

function whenConnect()
{
	reloadFlag = false;
	window.clearTimeout(tmR);
	reconnectTimes=0;
	if($('chatDiv').innerHTML==''||$('chatDiv').innerHTML.indexOf('��ӭ')==-1)
	{
		$('chatDiv').innerHTML='&lt;font color="#006600">��ӭ����ڴ�����!&lt;/font>&lt;br/>';
	}
}

function whenConnect2()
{
	reloadFlag = false;
	window.clearTimeout(tmR);
	window.clearTimeout(reloadT);
	reconnectTimes=0;
	if($('chatDiv').innerHTML==''||$('chatDiv').innerHTML.indexOf('����')==-1)
	{
		$('chatDiv').innerHTML='&lt;font color="#006600">��ӭ����ڴ�����!&lt;/font>&lt;br/>';
	}
}
<?php 
if(!$usec)
{
	?>
	// - -
	<?php
	$str=$_SESSION['id'].$_SESSION['username'].intval($_SESSION['password']).intval($_SESSION['vip']).iconv('gbk','utf-8',$_SESSION['nickname']);
	?>
	
	function getSetting()
	{
		return "<?php echo (isset($server_ip)?$server_ip:$_SERVER['HTTP_HOST'])
		."|".$_COOKIE[ini_get("session.name")]."|".$socket_port."|30|".
		$_SESSION['id']."|".$_SESSION['username']."|".intval($_SESSION['password'])."|".intval($_SESSION['vip']).'|'.$_SESSION['nickname'].'|'.md5($str);
		?>";
	}
	<?php 
}else{
	?>
	<?php
	$str=$_SESSION['id'].$_SESSION['username'].intval($_SESSION['password']).intval($_SESSION['vip']).iconv('gbk','utf-8',$_SESSION['nickname'].intval($socket_port));
	?>
	
	function getp()
	{
		return '<?php echo intval($socket_port) ?>';
	}
	
	function getserver()
	{
		return '<?php echo (isset($server_ip)?$server_ip:$_SERVER['HTTP_HOST']); ?>';
	}
	function getSetting()
	{
		return "<?php echo 
		$_SESSION['id']."|".$_SESSION['username']."|".intval($_SESSION['password'])."|".intval($_SESSION['vip']).'|'.$_SESSION['nickname'].'|'. intval($socket_port).'|'.md5($str);
		?>";
	}
	<?php
}
?>

function getName()
{
	//<?php echo $_SESSION['nickname']."\r\n"; ?>
	return "<?php echo $_SESSION['nicknamegb']; ?>";
}


function thisMovie(movieName) {
	if (navigator.appName.indexOf("Microsoft") != -1) {
		return window[movieName];
	} else {
		return document[movieName];
	}
}

function sendToActionScript(value) {	
	thisMovie("socketChatswf").sendToActionScript(value);
}

function getTimeStr()
{
	var date = new Date();
	var str = date.getHours();
	str += ":"+date.getMinutes();
	str += ":"+date.getSeconds();
	return str;
}

function AsCallDebug(str) {
	try{
		document.getElementById('cmsg').value='AsCallDebug say: '+str;
	}catch(e)
	{
		alert("AsCallDebug 0 ="+e);
	}
}

function callJS(str)
{
	eval(str);
}

function callGMCommand(str)
{
	var opt = {
    		 method: 'get',
    		 onSuccess: function(t) {
				 if(t.responseText == 'NOBROADCAST'){
					 Alert('��û�����ȵ��ߣ�');
				 }
				 if(t.responseText == 'TOOFAST'){
					 Alert('��˵���ٶ�̫�죡');
				 }
				 if(t.responseText.indexOf('DATATOOLONG')==0){
					 Alert('����̫����');
				 }
				 if(t.responseText.indexOf('nabaweihu')==0){
					 Alert('���ȹ�������ά���У�');
				 }
    		 },
    		 on404: function(t) {
    		 },
    		 onFailure: function(t) {
    		 },
    		 asynchronous:true        
		}

	var ajax=new Ajax.Request('/function/chatGate.php?msg='+decodeURI(str), opt);
	return true;
}

function substrx(str,len)//��ȡ����,Ӣ������
{
    var rtn="";
    var l=0;
    for(var i=0;l&lt;len&amp;&amp;i&lt;str.length;i++)
    {
        if(str.substr(i,1).match(/^\w$/))
        {
            l+=0.5;
        }else{
            l++;
        }
        rtn+=str.substr(i,1);
    }
    return rtn;
}

// JavaScript helper required to detect Flash Player PlugIn version information
function GetSwfVer(){
	// NS/Opera version >= 3 check for Flash plugin in plugin array
	var flashVer = -1;
	
	if (navigator.plugins != null &amp;&amp; navigator.plugins.length > 0) {
		if (navigator.plugins["Shockwave Flash 2.0"] || navigator.plugins["Shockwave Flash"]) {
			var swVer2 = navigator.plugins["Shockwave Flash 2.0"] ? " 2.0" : "";
			var flashDescription = navigator.plugins["Shockwave Flash" + swVer2].description;
			var descArray = flashDescription.split(" ");
			var tempArrayMajor = descArray[2].split(".");			
			var versionMajor = tempArrayMajor[0];
			var versionMinor = tempArrayMajor[1];
			var versionRevision = descArray[3];
			if (versionRevision == "") {
				versionRevision = descArray[4];
			}
			if (versionRevision[0] == "d") {
				versionRevision = versionRevision.substring(1);
			} else if (versionRevision[0] == "r") {
				versionRevision = versionRevision.substring(1);
				if (versionRevision.indexOf("d") > 0) {
					versionRevision = versionRevision.substring(0, versionRevision.indexOf("d"));
				}
			}
			var flashVer = versionMajor + "." + versionMinor + "." + versionRevision;
		}
	}
	// MSN/WebTV 2.6 supports Flash 4
	else if (navigator.userAgent.toLowerCase().indexOf("webtv/2.6") != -1) flashVer = 4;
	// WebTV 2.5 supports Flash 3
	else if (navigator.userAgent.toLowerCase().indexOf("webtv/2.5") != -1) flashVer = 3;
	// older WebTV supports Flash 2
	else if (navigator.userAgent.toLowerCase().indexOf("webtv") != -1) flashVer = 2;
	else if ( isIE &amp;&amp; isWin &amp;&amp; !isOpera ) {
		flashVer = ControlVersion();
	}	
	return flashVer;
}
function ControlVersion()
{
	var version;
	var axo;
	var e;
	// NOTE : new ActiveXObject(strFoo) throws an exception if strFoo isn't in the registry
	try {
		// version will be set for 7.X or greater players
		axo = new ActiveXObject("ShockwaveFlash.ShockwaveFlash.7");
		version = axo.GetVariable("$version");
	} catch (e) {
	}
	if (!version)
	{
		try {
			// version will be set for 6.X players only
			axo = new ActiveXObject("ShockwaveFlash.ShockwaveFlash.6");
			
			// installed player is some revision of 6.0
			// GetVariable("$version") crashes for versions 6.0.22 through 6.0.29,
			// so we have to be careful. 
			
			// default to the first public version
			version = "WIN 6,0,21,0";
			// throws if AllowScripAccess does not exist (introduced in 6.0r47)		
			axo.AllowScriptAccess = "always";
			// safe to call for 6.0r47 or greater
			version = axo.GetVariable("$version");
		} catch (e) {
		}
	}
	if (!version)
	{
		try {
			// version will be set for 4.X or 5.X player
			axo = new ActiveXObject("ShockwaveFlash.ShockwaveFlash.3");
			version = axo.GetVariable("$version");
		} catch (e) {
		}
	}
	if (!version)
	{
		try {
			// version will be set for 3.X player
			axo = new ActiveXObject("ShockwaveFlash.ShockwaveFlash.3");
			version = "WIN 3,0,18,0";
		} catch (e) {
		}
	}
	if (!version)
	{
		try {
			// version will be set for 2.X player
			axo = new ActiveXObject("ShockwaveFlash.ShockwaveFlash");
			version = "WIN 2,0,0,11";
		} catch (e) {
			version = -1;
		}
	}
	
	return version;
}
function DetectFlashVer(reqMajorVer, reqMinorVer, reqRevision)
{
	versionStr = GetSwfVer();
	if (versionStr == -1 ) {
		return false;
	} else if (versionStr != 0) {
		if(isIE &amp;&amp; isWin &amp;&amp; !isOpera) {
			// Given "WIN 2,0,0,11"
			tempArray         = versionStr.split(" "); 	// ["WIN", "2,0,0,11"]
			tempString        = tempArray[1];			// "2,0,0,11"
			versionArray      = tempString.split(",");	// ['2', '0', '0', '11']
		} else {
			versionArray      = versionStr.split(".");
		}
		var versionMajor      = versionArray[0];
		var versionMinor      = versionArray[1];
		var versionRevision   = versionArray[2];
        	// is the major.revision >= requested major.revision AND the minor version >= requested minor
		if (versionMajor > parseFloat(reqMajorVer)) {
			return true;
		} else if (versionMajor == parseFloat(reqMajorVer)) {
			if (versionMinor > parseFloat(reqMinorVer))
				return true;
			else if (versionMinor == parseFloat(reqMinorVer)) {
				if (versionRevision >= parseFloat(reqRevision))
					return true;
			}
		}
		return false;

	}
}
var requiredMajorVersion = 10;
// ���� Flash �Ĵΰ汾��
var requiredMinorVersion = 0;
// ���� Flash �İ汾��
var requiredRevision = 2;


jsReady = true;

window.onbeforeunload = function(){ 
	return checkTF();
}
function checkTF(){
	if(
	typeof($('gw').contentWindow.teamLeader)!='undefined'&amp;&amp;$('gw').contentWindow.teamLeader>0
	){
		if(confirm("���������ս��,�˳�����������������Ա����Ԥ�ϵ�״��,ȷ���뿪��?\n���ֲ��������ʱ�����ɢ����֮��������ӣ�"))
		{
			if(arguments[0])
				return true;
			else	
				return;
		}else{
			return "���������ս��,�˳�����������������Ա����Ԥ�ϵ�״��,ȷ���뿪��?\n���ֲ��������ʱ�����ɢ����֮��������ӣ�";
		}
	}
	if(typeof($('gw').contentWindow.fortressFight)!='undefined')
	{
		return "������Ů��Ҫ������,���ˢ��,���½�����ٴο۳���Ʒ�ͽ��!";
	}
	if(arguments[0])
		return true;
	else	
		return;
}
function blacklistu(str){
	var datas=str.split('&lt;u>');
	var tmp='';
	var con='';
	for(var i=1;i&lt;datas.length;i++){
		tmp+=con+datas[i].substr(0,datas[i].indexOf('&lt;'));
		con=',';
	}
	thisMovie('socketChatswf').setBlackList(tmp);
}
function friendlistu(str){
	var datas=str.split('&lt;u>');
	var tmp='';
	var con='';
	var tmpf={};
	for(var i=1;i&lt;datas.length;i++){
		var name=datas[i].substr(0,datas[i].indexOf('&lt;'));
		tmp+=con+name;
		con=',';
		if(typeof(friends[name])=='undefined')
		{		
			tmpf[name] = 0;
		}else{
			tmpf[name] = friends[name];
		}
	}
	friends=tmpf;
	updateFriendListHTML();	
	thisMovie('socketChatswf').setFriendList(tmp);
	//dealUserList(tmp);
}

</script>
<body onLoad="loadads()" style="color:#693600">
<?php echo $callbackhtml; ?>
<div id="sysstatmsgs" style="position:absolute;width:344px;height:18px;z-index:10002;left: 340px;top: 548px;	opacity: 0.6; filter : progid:DXImageTransform.Microsoft.Alpha(style=0,opacity=60,finishOpacity=100); display:none"> </div>
<iframe id="iframechat" width="600" height="215" scrolling="no" src="/socketChat/chatS.php" style="display:none; z-index:3;left:400px;top:600px; position:absolute;" class="wgframe"></iframe>
<?php
//ʢ��IBW��ʾ��ʼ
$www=explode('.',$_SERVER['HTTP_HOST']);
$website='';
for($i=1;$i<count($www);$i++)
{
	$website.=$www[$i].'.';
}
switch ($website)
{
	case 'game.qidian.com.':
	
?>
<script type="text/javascript" src="http://ibw.sdo.com/flash/js/webwidget.js"> 
</script>
<script type="text/javascript">
ibw.appid=608;
ibw.color="230";//ȦȦƤ��Ĭ����ɫ
ibw.brightness="0.86999";//ȦȦƤ��Ĭ������
ibw.saturation="0.76";//ȦȦƤ��Ĭ�ϱ��Ͷ�
ibw.barMode=1;//ȦȦ����ҳĬ����ʾ��ģʽ(����:1 ����:2) 
ibw.barDisplay="none";//ȦȦ����ҳĬ����ʾ��״̬�����򿪣�"block"���رգ�"none") 
ibw.needLogout=false;// �趨ȦȦ�Ƿ���Ҫע�����ܣ�true(Ĭ��)����Ҫ�� false������Ҫ��
ibw.barTop=30; ibw.barRight=30;//ȦȦ����ҳĬ����ʾ��λ��
</script>
<?php	
break;
}
?>
<script type="text/javascript" src="./javascript/wz_tooltip.js"></script>
<iframe id="iframestat" width="600" height="200" src="/function/onlineStat.php" style="display:none" class="wgframe"></iframe>
<script language=javascript src='./function/onlineGate.php'></script>
<div class="backbg" id="light"></div>
<div id="baginfo" style="margin-left:700px; margin-top:100px;z-index:20; position:absolute; display:none"></div>
<div id="lgn" class='st' style="filter:alpha(opacity=0);"></div>
<div id="page">
  <div id="main" style="left:0px; top:0px">
    <div class="main_t clearfix">
      <!-- ��ർ����ť ��ʼ -->
      <div style="background-image:new_images/index/index_left.jpg" class="side l">
          <ul class="whq_nav">
              <li>
                  <a href="/function/Expore_Mod.php" target="gamewindow"><img src="/images/ui/menu/m_fight.png" alt="Ұ��̽��"></a>
              </li>
              <li>
                  <a href="/function/City_Mod.php" target="gamewindow"><img src="/images/ui/menu/m_city.png" alt="Ұ��̽��"></a>
              </li>
              <li>
                  <a href="/function/Pets_Mod.php" target="gamewindow"><img src="/images/ui/menu/m_pet.png" alt="Ұ��̽��"></a>
              </li>
              <li>
                  <a href="/function/User_Mod.php" target="gamewindow"><img src="/images/ui/menu/m_info.png" alt="Ұ��̽��"></a>
              </li>
          </ul>
      </div>
      <!-- ��ർ����ť ���� -->
      <div class="content r">
        <!-- ���Ͻǹ����� ��ʼ -->
        <div class="tools"></div>
        <div class="tools_btn"><a onclick="ShowBox('Tools','1','3')">����</a><a id="ab" onclick="ShowBox('Tools','2','3')">��Ϣ</a><a onclick="ShowBox('Tools','3','3');">����</a><a class="t4" onClick="addBookmark();">�ղ�</a></div>
        <!-- ���Ͻǹ����� ���� -->
        <!-- ��Ϸ������ ��ʼ -->
		
        <div class="gamebox">
          <iframe id="gw" name="gamewindow" src="function/Welcome_Mod.php" style="width:788px; height:319px;" frameborder="0" scrolling="no" allowTransparency="true"></iframe>
        </div>
        <!-- ��Ϸ������ ���� -->
      </div>
    </div>
    <div class="main_b clearfix">
      <div class="chat l">
        <!-- ������� ��ʼ -->
        <div class="ol">������ң�<span id="onlinec"></span></div>
        <!-- ������� ���� -->
        <div class="chat_box">
          <!-- ����� ��ʼ -->
          <div class="chat_cont" id="chatDiv">�����У����Ժ򡭡� </div>
          <div id="help_chat_info" style="z-index:100;position:absolute;top:100px;display:block; left:250px"></div>
          <div id="bbshow" style="z-index:100; position:absolute; display:block; left:235px; width:300px; top: -27px;"></div>

		  <div id="challenge_info" style="z-index:100; position:absolute; display:none; left:388px; width:300px; top: 46px;"></div>
          <!-- ����� ���� -->
          <!-- ���칤���� ��ʼ -->
          <div class="chat_tool">
            <input id="cmsg" class="inp" value="" type="text">
            <div class="select_lt" id ="select_lt"> <span>����</span>
              <ul class="hidden">
                <li class="i">���͵�</li>
                <li><a href="javascript:;" target="_self" title="����" name="0">����</a></li>
                <li><a href="javascript:;" target="_self" title="˽��" name="1">˽��</a></li>
                <li><a href="javascript:;" target="_self" title="����" name="2">����</a></li>
                <li><a href="javascript:;" target="_self" title="������" name="3">������</a></li>
              </ul>
            </div>
            <div class="select_pd" id ="select_pd"> <span>ȫ��</span>
              <ul class="hidden">
                <li class="i">��ʾƵ��</li>
                <li onclick="showSpecialMsg('')"><a href="javascript:;" target="_self" title="ȫ��" name="">ȫ��</a></li>
                <li onclick="showSpecialMsg('WP')"><a href="javascript:;" target="_self" title="˽��" name="WP">˽��</a></li>
                <li onclick="showSpecialMsg('SG')"><a href="javascript:;" target="_self" title="���" name="SG">���</a></li>
                <li onclick="showSpecialMsg('GC')"><a href="javascript:;" target="_self" title="����" name="GC">����</a></li>
              </ul>
            </div>
            <div class="select_ys" id ="select_ys" style="display:none"> <span>��</span>
              <ul class="hidden">
                <li><a href="javascript:;" target="_self" title="��ɫ" name="">��</a></li>
                <li><a href="javascript:;" target="_self" title="��ɫ" name="!"><font color="#ff3399">��</font></a></li>
                <li><a href="javascript:;" target="_self" title="��ɫ" name="#"><font color="#33cc00">��</font></a></li>
                <li><a href="javascript:;" target="_self" title="��ɫ" name="!!"><font color="#0000ff">��</font></a></li>
              </ul>
            </div>
            <input name="type" type="hidden" id="tknew" />
            <input name="type" type="hidden" id="tklist" />
            <input name="type" type="hidden" id="ccolor" />
            <script type="text/javascript">
							initSelect("select_lt", "tknew");
              initSelect("select_pd", "tklist");
              initSelect("select_ys", "ccolor");
            </script>
            <img style="" onclick="cmotion()" src="images/ui/motion/3.gif">
            <input id="snd" class="but" value=" " onclick="chatH.sendMsg();" type="button">
            <img title="��Ӻ��ѻ��������" style="cursor: pointer;" src="images/friends.gif" onclick="if($('frienlist').style.display=='none'){$('frienlist').style.display='block';}else{$('frienlist').style.display='none';}" class="add"> </div>
          <!-- ���칤���� ���� -->
        </div>
      </div>
      <div class="tip r">
        <!-- �ڴ��ٿ����� ��ʼ -->
        <div class="wiki">
          <form>
            <input type="text" id="baike_input" class="inp" />
            <!--<input type="button" class="btn" value=" "  onClick="searchPocketBaike();return false;" />-->
          </form>
        </div>
        <!-- �ڴ��ٿ����� ���� -->
       <iframe name="gamewindow" src="<?=$cmd['iframe']?>" style="width:253px; height:94px; overflow:hidden;" frameborder="0" scrolling="no" allowTransparency="true"></iframe>

        <!-- ���Ż���� ��ʼ -->
        
        <!-- ���Ż���� ���� -->
        <div class="link">
          <ul>
            <!-- �������� ��ʼ -->
            <li><a  onclick="$('help').style.display='block';void(0)">����</a></li>
           <li><a href="<?=$cmd['guanwang']?>" target="_blank">����</a></li>
              <li><a href=# >�ͷ�</a></li>
              <li><a href=# >�ͷ�</a></li>
              <li><a href=# >�ͷ�</a></li>
          <li><a href="<?=$cmd['exit']?>">�˳�</a></li>
            <!-- �������� ���� -->
          </ul>
        </div>
      </div>
    </div>
    <!-- �ײ����� ��ʼ -->
    <?=$cmd['linkatbottom']?>
    <!--div class="footer"><a href="#">���ְ���</a><a href="#">����ϳ�</a><a href="#">׷������</a><a href="#">����һ��</a><a href="#">���߻��</a><a href="#">17173�ٿ�</a><a href="#">���������ύ</a></div-->
    <!-- �ײ����� ���� -->
  </div>
</div>
<div id="helpwin" style="width:400px;height:0px;overflow:hidden; position:absolute;top:0px;left:420px;z-index:1000; display:none;text-align:left;">
  <table width="326" border="0" cellspacing="0" cellpadding="0">
    <tr>
      <td height="23" background="images/ui/help/kj02.gif" style="display:block"><span id="showmybagusedcells" style="font-size:12px; font-weight:bold; color:#ffffff;"></span></td>
    </tr>
    <tr>
      <td  height="300" valign="top"><div id="helpwincet" style="font-size:12px;scrollbar-face-color:#4EB5B4;scrollbar-highlight-color:#ffffff;scrollbar-3dlight-color:#4EB5B4;scrollbar-shadow-color:#ffffff;scrollbar-darkshadow-color:#4EB5B4;scrollbar-track-color:#4EB5B4;scrollbar-arrow-color:#ffffff; overflow:auto;width:317px;height:290px;padding:5px;line-height:1.7;color:#087f95; display:block; background-color:#F1F8DD;position:absolute;z-index:5;"></div></td>
    </tr>
    <tr>
      <td height="20" background="images/ui/help/kj03.gif" align="center" style="filter:alpha(opacity=90);"><input type="button" value="ʹ��" style="background-image:url(images/ui/help/kj04.gif);border:0px;width:62px;height:20px;color:#2F291D;cursor:pointer;"  onclick="Used();" id="helpcmd"/>
        onclick="Used();" onclick="Reset();"
        <input type="button" value="����" style="background-image:url(images/ui/help/kj04.gif);border:0px;width:62px;height:20px;color:#2F291D;cursor:pointer;"  onclick="Reset();" id="helpcmd2"/>
        <input type="button" value="����" style="background-image:url(images/ui/help/kj04.gif);border:0px;width:62px;height:20px;color:#2F291D;cursor:pointer;display:none;"  onclick="" id="helpcmd3"/>
        <input type="button" value="�ر�" style="background-image:url(images/ui/help/kj04.gif);border:0px;width:62px;height:20px;color:#2F291D;cursor:pointer;display:none;"   id="helpcmd1"/>
      </td>
    </tr>
    <tr>
      <td style="background-image:url(images/ui/help/kj01.gif); height:17px; display:block; filter:alpha(opacity=90);" align="center"></td>
    </tr>
  </table>
  <div id="help_win_info"></div>
</div>
<!--������-->
<div id=adbottomleft style="left:20px;top:635px;width=460px;height=110px;position:absolute;">
  <?=$cmd['adbottomleft']?>
</div>
<div id=adbottomright style="left:522px;top:635px;width=460px;height=110px;position:absolute;">
  <?=$cmd['adbottomright']?>
</div>
<!--�ұ߹��-->
<div id=adright style="left: 1007px; width: 120px; height: 430px; top:0px; position:absolute">
</script>
</div>
<div id=ads style="left:1000px;top:30px;width=120px;height=193px;position:absolute;">
  <?=$cmd['ad_top']?>
</div>
<div id='systips' style="position:absolute;width:246px; z-index:2;left:400px;top:410px;font-size:12px;color:#ffffff;height:142px; border:0px;background:url(new_images/index/boxk.gif);filter:alpha(opacity=80); -moz-opacity:0.8;display:none; padding:10px;z-index:30001"></div>
<!--����-->
<div id="help" style="position:absolute; left:701px; top:348px; width:282px; height:209px; z-index:10;padding-top:3px;display:none">
  <table width="286" border="0" cellpadding="0" cellspacing="0">
    <tr>
      <td><table width="100%" border="0" align="center" cellpadding="0" cellspacing="0">
          <tr>
            <td width="14"><img src="images/help/bbz01.gif" width="14" height="29"></td>
            <td background="images/help/bbz02.gif"><b><font color="green" style="font-size:12px;">�ڴ��������ְ���ϵͳ1.2</font></b></td>
            <td width="31"><img src="images/help/bbz03.gif" width="31" height="29"></td>
          </tr>
        </table></td>
    </tr>
    <tr>
      <td height="174" valign="top" align="left" background="images/help/bbz04.gif" style="font-size:12px;padding:10px;padding-top:0px;line-height:1.7;"><a href="javascript:helpsys('desc');void(0);" style='color:#1c4ec1'>���</a> <a href="javascript:helpsys('city');void(0);" style='color:#1c4ec1'>����</a> <a href="javascript:helpsys('shop');void(0);" style='color:#1c4ec1'>�̵�</a> <a href="javascript:helpsys('gpc');void(0);" style='color:#1c4ec1'>���</a> <a href="javascript:helpsys('skill');void(0);" style='color:#1c4ec1'>����</a> <a href="javascript:helpsys('chat');void(0);" style='color:#1c4ec1'>����</a> <a href="javascript:helpsys('task');void(0);" style='color:#1c4ec1'>����</a> <a href="javascript:helpsys('bag');void(0);" style='color:#1c4ec1'>װ��</a> <a href="javascript:$('help').style.display='none';void(0);" style='color:#1c4ec1'>�ر�</a> <span id='helptarget' style="color:#333333;"><br/>
        ���ڴ�����Ǹ�����ȡ�ڴ�����ϵ����Ϸ�ľ������иı�ĳ�����������ҳ��Ϸ,��������,��ʹ���ϰ��ʱ��,��ֻҪ����ҳ���ܺ��Լ��İ��ĳ������Ķȹ�һ�죡.<br/>
        <!--<a href="<?=$cmd['help']?>" target="_blank">--><img src="images/help/help.gif" 
		border=0></a> <font color=green>�رպ�TAB��(��ĸQ��ߵļ�)�����ٴδ򿪣�</font> </span> </td>
    </tr>
    <tr>
      <td height="12"><img src="images/help/bbz05.gif" width="286" height="12"></td>
    </tr>
  </table>
</div>
<div class="box_task_show_123" id="taskmsg">
  
</div>

<div width="680" id="swfdbgdiv" height="240" style="display:none;left:0px;top:600px; z-index:3; position:absolute" class="wgframe">
  <textarea id="swfdbg" cols="60" rows="36">
</textarea>
</div>
<!-- ���� ��ʼ -->
<div class="box_pack" id="Box_Tools_1">
  <div id="Box_Tools_1_handle" style="position: absolute; left: 20px; top: 6px; border: 0px solid rgb(255, 0, 0); width: 83px; height: 25px; cursor: move;">
  </div>
  <div class="box_cont"  id="bags">
  </div>
</div>
<!-- ���� ���� -->
<!-- ��Ϣ ��ʼ -->
<div class="box_msg" id="Box_Tools_2">
  <div id="Box_Tools_2_handle" style="position: absolute; left: 20px; top: 6px; border: 0px solid rgb(255, 0, 0); width: 83px; height: 25px; cursor: move;">
  </div>
  <div class="box_cont">
    <div class="close_btn" onclick="ShowBox('Tools','2','3')"></div>
    <div class="msg_cont">
      <ul class="list" id='infos'>
      </ul>
    </div>
  </div>
</div>
<!-- ��Ϣ ���� -->
<!-- ���� ��ʼ -->
<div class="box_task" id="Box_Tools_3">
  <div id="Box_Tools_3_handle" style="position: absolute; left: 20px; top: 6px; border: 0px solid rgb(255, 0, 0); width: 83px; height: 25px; cursor: move;">
  </div>
  <div class="box_cont">
    <div class="close_btn" onclick="ShowBox('Tools','3','3');taskcache={};getTaskDetailFlag=false;"></div>
    <div class="i_task" id="activity_show"> </div>
    <div class="task_nav">
      <h2>�����б�</h2>
      <div class="task_list" id="task_title_list"> </div>
    </div>
	
    <div class="task_cont" id="task_every_list">
      
    </div>
  </div>
</div>
<!-- ���� ���� -->
<span id="activity_show_every" style="width:100px; height:50px; position:absolute; left:171px; top:71px;z-Index:12000;display:none;background-color:#e1cea1;border:1px solid #b18033;color:#6c4200;line-height:22px;padding-left:10px;"></span>

<div class="nav" id="create_guild" style="display:none; z-index:100; position:absolute; left: 339px; top: 82px;">
  <div class="nav_01"><img src="new_images/ui/cjjzbg01.gif" width="388" height="37" /><img src="new_images/ui/cjjzbg02.gif" width="71" height="37" style="cursor:pointer" onclick="$('create_guild').style.display='none'" /></div>
  <div class="nav_02">
    <table width="380" border="0" cellspacing="0" cellpadding="0" style="font-size:12px">
      <tr>
        <td width="62" height="24">��������</td>
        <td width="318"><input  name="name" type="text" id="name" class="tt1"/>
          �޺���2-8��</td>
      </tr>
      <tr>
        <td>�������</td>
        <td><textarea name="info" cols="50" rows="4" class="tt2" id="info"></textarea></td>
      </tr>
      <tr>
        <td height="24">&nbsp;</td>
        <td>��200����������</td>
      </tr>
    </table>
    <table width="390" border="0" cellspacing="0" cellpadding="0" style="margin-top:20px; color:#3D5C00; line-height:20px; font-size:12px">
      <tr>
        <td width="84" height="24" valign="top">���崴��������</td>
        <td width="296">1.ӵ�м�������               2.��δ������������<br />
          3.δ�����������������    4.��Ҫ����10��vip����</td>
      </tr>
      <tr>
        <td height="50" valign="top">����ϵͳ˵����</td>
        <td>�����Աÿ�����ȡ����������ȼ�Խ�߻�ø���Խ��<br />
          ������Ա����ͨ����������ս��������㡣<br />
          ���������ͨ�������̵깺��������߻�á�</td>
      </tr>
    </table>
    <table width="390" border="0" cellspacing="0" cellpadding="0" style="margin-top:15px;">
      <tr>
        <td valign="top" style="padding-left:145px;"><img src="new_images/ui/cjjz02.jpg" width="95" height="28" style="cursor:pointer" onclick="$('gw').contentWindow.create_ajax()" /></td>
      </tr>
    </table>
  </div>
</div>

<div id="new_guide_div" style="position:absolute;top:0px;left:0px;z-index:29998;width:1000px;height:620px; display:none; BACKGROUND-COLOR: #ffffff;filter: alpha(opacity=1); opacity:0.01"></div>
<div id="guide_click" style="z-index:30000;position:absolute; left:0px; top:0px; display:none"></div>
<div id="guide_girl" style="z-index:30000;position:absolute;left:356px;top:200px; width:492px; height:308px; background-image:url(new_images/ui/guide_girl.gif); background-repeat:no-repeat; display:none">
  <div id="guide_text" style="display:none;width:200px;left:158px;top:104px;z-index:30000;position:absolute; cursor:pointer;" onclick="$('gw').src='/function/City_Mod.php';doguide();"> <span style="cursor:pointer;" onclick="$('gw').src='/function/City_Mod.php';doguide();">��ӭ�����ڴ��������磬���Ǿ���ʹ�ߡ�С�ܡ������������һ����Ϥһ�¿ڴ��ɣ�����÷��Ľ���Ŷ������������</span>  </div>
  <div id="guide_a" style="width:72px;left:417px; height:66px;top:154px;z-index:30000;position:absolute; cursor:pointer;" onclick="do_over()"></div>
  <div id="guide_next" style="width:58px;left:394px; height:49px;top:227px;z-index:30000;position:absolute; cursor:pointer;" onclick="doclick()"></div>
</div>

<div id="frienlist" style="position:absolute;width:120px; padding:6px; height:165px;z-index:1;left: 1007px;top: 436px; background-repeat:no-repeat; overflow:hidden;display:none; background-image:url(new_images/ui/friend_blacklist_m.gif)">
  <div id="frienlisthandle" style="cursor:move; width:89px; height:21px; float: left; color:#FFFFFF">���Ѻͺ�����<span onclick="if(checkTF(1)){blacklist();}" style="cursor:pointer; font-weight:bold; text-decoration:underline">&gt;&gt;</span></div>
  <div onclick="$('frienlist').style.display='none';" style="cursor: pointer; position: relative; float: right; width: 15px; margin-right: 7px; height: 17px;"></div>
  <div id="frienlistDiv" style="width:100px; overflow:hidden; overflow-y:auto; width:110px; height:132px"></div>
</div>
<!--<img width="18"  id='refbtn' style="position: absolute; top: 345px; z-index: 100; left: 975px; cursor: pointer;" src="new_images/ui/rfrpg2.png" onclick="if(typeof($('gw').contentWindow.teamLeader)!='undefined'&&$('gw').contentWindow.teamLeader>0){Alert('���ս����,������ˢ��!');}else if($('gw').contentWindow.document.title=='������'||$('gw').contentWindow.document.title.indexOf('Ҫ��')!=-1||typeof($('gw').contentWindow.fortressFight)!='undefined'){Alert('��ʱ,������ˢ��!');}else{$('gw').contentWindow.location.reload();}" title="ˢ�¿���ҳ��">-->
<div id="fcm_div" style=" height:20px; width:auto; position:absolute; top:420px; left:230px; display:none;"></div>
<div id="time_box" style=" position:absolute; top:375px; left:200px; color:#FFFFFF;"></div>
<!--<div id="zpdiv" style="width:610px;height:516px; position:absolute; top:30px; left:150px;">
	<embed src="zpmod/roulette.swf" width="500" height="500"  wmode="transparent">
	<div onclick="zpdeal(this)" style="position:relative; left:160px; top:-40px;"><img src="images/zpclose.gif" /></div>
</div>-->
</body>
</html>
<script type="text/javascript">
new Draggable('frienlist',{scroll:window,zindex:1,handle:'frienlisthandle',revert:function(element){return false;}});
new Draggable('Box_Tools_1',{scroll:window,zindex:1,handle:'Box_Tools_1_handle',revert:function(element){return false;}});
new Draggable('Box_Tools_2',{scroll:window,zindex:1,handle:'Box_Tools_2_handle',revert:function(element){return false;}});
new Draggable('Box_Tools_3',{scroll:window,zindex:1,handle:'Box_Tools_3_handle',revert:function(element){return false;}});
</script>
<script type="text/javascript">
pageInit();
function addBookmark() {	
	//window.external.AddFavorite(document.location.href,document.title);
	if (document.all){   
       window.external.addFavorite(document.URL,document.title);   
    }else if (window.sidebar){   
       window.sidebar.addPanel(document.title, document.URL, "");   
    } 
}

function blacklist()
{
	document.getElementById('gw').src='./function/User_Mod.php?type=list';
}
var dBody = null;
function getBody(){
	if(!dBody)dBody=(document.compatMode&amp;&amp;document.compatMode.indexOf('CSS')>-1)?document.documentElement:document.body;return dBody;
}
function getScrollX(){
	return window.pageXOffset||window.scrollX||getBody().scrollLeft||0;
}
function getScrollY(){
	return window.pageYOffset||window.scrollY||getBody().scrollTop||0;
}
function OpenLogin(op,tid,n,ifshow){
	var opt = {
     	method: 'get',
		onSuccess: function(t) {
			 		if(t.responseText!='') $('task_every_list').innerHTML = t.responseText;
    		 	},
     	asynchronous:true        
	}
	var ajax=new Ajax.Request('../function/getTaskinfo.php?op='+op+'&amp;n='+n+'&amp;t='+tid+'&amp;ifshow='+ifshow+'&amp;rd='+Math.random(), opt);
}
function CloseLogin(){
	document.getElementById('light').style.display='none'; 	
	document.getElementById('taskmsg').style.display='none';
	taskcache={};getTaskDetailFlag=false;
}
function fcmdisplay()
{
	var fcm_div = document.getElementById('fcm_div');
	fcm_div.style.display = "none";
	fcm_div.innerHTML = "";
}
function fcmdiv(para)
{
	var fcm_div = document.getElementById('fcm_div');
	fcm_div.style.display = "block";
	switch(para)
	{
		case 1 :
		{
			fcm_div.innerHTML = "&lt;font color='red'>���ۼ�����ʱ������1Сʱ&lt;/font>";

			setTimeout("fcmdisplay()",60000);
			break;
		}
		case 2 :
		{
			fcm_div.innerHTML = "&lt;font color='red'>���ۼ�����ʱ������2Сʱ&lt;/font>";

			setTimeout("fcmdisplay()",60000);
			break;
		}
		case 3 :
		{
			fcm_div.innerHTML = "&lt;font color='red'>���ۼ�����ʱ������3Сʱ,����������Ϣ,���ʵ������˶�&lt;/font>";

			setTimeout("fcmdisplay()",60000);
			break;
		}
		case 4 :
		{
			fcm_div.innerHTML = "&lt;font color='red'>���Ѿ�����ƣ����Ϸʱ�䣬������Ϸ���潫��Ϊ����ֵ��50����&lt;/font>&lt;br/>&lt;font color='red'>Ϊ�����Ľ������뾡��������Ϣ�����ʵ�������������ѧϰ����&lt;/font>";

			setTimeout("fcmdisplay()",30000);
			break;
		}
		case 5:
		{
			fcm_div.innerHTML = "&lt;font color='red'>���ѽ��벻������Ϸʱ�䣬Ϊ�����Ľ�������������������Ϣ&lt;/font>&lt;br/>&lt;font color='red'>�粻���ߣ��������彫�ܵ��𺦣����������ѽ�Ϊ��&lt;/font>&lt;br/>&lt;font color='red'>ֱ�������ۼ�����ʱ����5Сʱ�󣬲��ָܻ�������&lt;/font>";

			setTimeout("fcmdisplay()",15000);
			break;
		}
	}
}
function getLocalTime(nS)
{   
    d =  new Date(parseInt(nS) * 1000);
	thour = d.getHours()>=10?d.getHours()+":":"0"+d.getHours()+":";
 	tmin  = d.getMinutes()>=10?d.getMinutes()+":":"0"+d.getMinutes()+":";
 	tsec  = d.getSeconds()>=10?d.getSeconds():"0"+d.getSeconds();
	return thour+tmin+tsec;      
}
var server_timestamp = 0;	//������ʱ��
var client_timestamp = 0;	//�ͻ���ʱ��
var time_different = 0;	//����������ͻ���ʱ�� 
function time_same(time_different)
{
	client_timestamp = parseInt(String((new Date()).valueOf()).substring(0,10));
	nS = parseInt(client_timestamp)+parseInt(time_different);
	now_time = getLocalTime(nS);
	time_arr = now_time.split(':');	
	if(server_timestamp == 0 || (time_arr[2] == '00' &amp;&amp; time_arr[1].substr(1) == 0))
	{
		var opt = {
				method: 'post',
				onSuccess: function(t) {
						if(t.responseText!='')
						{
							server_timestamp = t.responseText;
							time_different = server_timestamp - client_timestamp;
						}
					},
				asynchronous:false       
				}
			var ajax=new Ajax.Request('api/time.php', opt);
	}
	nS = parseInt(client_timestamp)+parseInt(time_different);
	now_time = getLocalTime(nS);
	document.getElementById('time_box').innerHTML = now_time;
	setTimeout("time_same("+time_different+")",900);
	
}
time_same(0);
function zpdeal()
{
	document.getElementById("zpdiv").parentNode.removeChild(document.getElementById("zpdiv"));
	document.getElementById("lock_zp_div").parentNode.removeChild(document.getElementById("lock_zp_div"));
}
function zp_gonggao(para)
{
	var opt = {
				method: 'get',
				onSuccess: function(t) {
						if(t.responseText!='')
						{
						}
					},
				asynchronous:false       
				}
	var ajax=new Ajax.Request('zpmod/gonggao.php?para='+para, opt);
}
function create_sl_mod()
{
	div = document.createElement('div');
	div.className = "saolei_index";
	div.id = 'saolei_index';
	document.getElementById('time_box').parentNode.appendChild(div);
	var opt = {
				method: 'get',
				onSuccess: function(t) {
						if(t.responseText!='')
						{
							document.getElementById('saolei_index').innerHTML = t.responseText;
						}
					},
				asynchronous:false       
				}
			var ajax=new Ajax.Request('function/saolei_Mod.php', opt);
}
</script>
<script>
if(window.external &amp;&amp; window.external.browserCheck &amp;&amp; window.external.browserCheck()==true)


{



}

else

{

  //      location.href="/error.html"

}


</script>
<?php echo 	$gjs ?>


