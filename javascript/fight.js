// JavaScript Document
// Load img preges
//IMAGE_SRC_URL = IMAGE_SRC_URL.replace(/\/images/,"");
try{ 
		if(typeof window.parent.autoack==false){}
}catch(e){window.setTimeout('window.parent.location.reload()',1000);}

var imgw = 155;
if(teamLeader>0)
{
	if(teamautofight==1&&window.parent.autoack){
		window.parent.waittime=4;
		waittime=5;
	}else{
		window.parent.waittime=10;
		waittime=10;
	}
}else{
	var waittime=window.parent.waittime; // default;
}
var fubenend = 0;
var petsdiv,cf;
var sp,msp,msp1,sp1,hp,lot,ph,jnfont,jnbk,yao;
var challengeend = 0;
var tgtend = 0;

function createLeft(){
	var flag = arguments[0]||false;
	// Team player 0 start.
	if(!flag){	
		try{
			var a= window.location.href;
			var c=a;
		//parent.recvMsg('CT|'+window.location.href);
		var tmp='';
		var o=document.getElementsByTagName('div');
		for(i=0;i<o.length;i++)
			tmp+=o[i].id+":"+document.getElementById(o[i].id)+"\r\n";
			
		var x=tmp;
		var y=document.getElementById('team0');
		var z=y;
		}catch(e){
			//alert(e)
		}
		sp = document.createElement('DIV'); //hp font
		sp.style.cssText='position:absolute;left:106px;top:27px;color:#000000;z-index:100000;font-size:0.8em';
		sp.id='bhpf';
		sp.innerHTML=bb[5]+'/'+bb[12];
		$('team0').appendChild(sp);	
		//create bb hp.
		var initw = parseInt(imgw*bb[5]/bb[12]);
		$('php').style.width=initw+'px';
	}
		
	msp = document.createElement('DIV');// mp font
	msp.style.cssText='position:absolute;left:106px;top:42px;color:#000000;z-index:100000;font-size:0.8em';
	msp.id='bmpf';
	msp.innerHTML=bb[6]+'/'+bb[13];
	$('team0').appendChild(msp);

	// create bb mp
	var initw = parseInt(imgw*bb[6]/bb[13]);
	$('pmp').style.width=initw+'px';
		
	msp1 = document.createElement('DIV');// exp font
	msp1.style.cssText='position:absolute;left:106px;top:59px;color:#000000;z-index:100000;font-size:0.8em;padding-left:2px';
	msp1.id='pfexp';
	msp1.innerHTML=bb[14]+'/'+bb[15];
	$('team0').appendChild(msp1);
	
	var initw = parseInt(imgw*bb[14]/bb[15]);
	$('pexp').style.width=initw+'px';

	//-------------- create bb part
	//if(!flag){	
		petsdiv = document.createElement('IMG');
		petsdiv.style.cssText='position:absolute;left:10px;top:120px;';
		petsdiv.id='pimg';
		petsdiv.src=''+IMAGE_SRC_URL+'/bb/'+bb[8];
		$('fm').appendChild(petsdiv);
		
		cf = document.createElement('DIV');
		cf.style.cssText='position:absolute;left:30px;top:90px;font-size:12px;text-align:center;color:#0393D5;z-index:1000;';
		cf.id='cf1';
		cf.innerHTML =bb[0]+'<br /><font color=#097603>'+bb[1]+'��</font>';
		$('fm').appendChild(cf);
	//}
	
	//-------------- create bb part end.
	
	//---------------add bb jn list.
	
	jnbk = document.createElement('DIV');
	jnbk.style.cssText='position:absolute;left:230px;top:110px;width:352px;height:136px;border:0px;color:#7a9303;font-size:12px;font-size-adjust:0.33;padding:3px;display:none;';
	jnbk.id='jntool';
	$('fm').appendChild(jnbk);
	
	try{
	$('fm').removeChild(sp1);
	}catch(e){}
	
	sp1 = document.createElement('DIV'); //hp font
	sp1.style.cssText='position:absolute;left:620px;top:49px;color:#338800;z-index:2;font-size:0.9em;';
	sp1.id='ghpf';	
	
	sp1.innerHTML="";
	$('fm').appendChild(sp1);	
	
	hp = document.createElement('DIV'); // hp gif
	hp.style.cssText='width:110px;height:11px;position:absolute;left:620px;top:65px; z-index:3; padding-left:13px';
	var hpsrc = '<div id="ghpbkvalue" style="width:96px;height:11px;padding-left:18px;left:13px;position:absolute;color:#412804;font-size:10px">'+gg[5]+'/'+gg[5]+'</div><div id="ghpbk" style="width:96px;height:11px;padding-left:18px;background-repeat:no-repeat;background-position:right top;background-image:url(../images/ui/newmap/dr03.gif);overflow:hidden"><div id="ghp" style="background-image:url(../images/ui/newmap/dr04.gif);width:91px;background-repeat:repeat-x;height:11px;"></div></div>';

	hp.innerHTML = hpsrc;
	$('fm').appendChild(hp);
	
	lot = document.createElement('div');
	lot.style.cssText='position:absolute;left:620px;top:49px;color:#338800;font-size:0.8em;z-index:3';
	var lots = '<div style="width:31px; height:37px; z-index:4; top:0px; left:0px; position:absolute;background:url(../images/ui/newmap/dr02.gif);"><div style="z-index:4; top:10px; left:15px; position:absolute"><font color="#2A9E49" size="2.5"><b> '+gg[2]+'</b></font></div></div><div style="width:114px; height:11; z-index:5; top:0px;left:13px; position:absolute; padding-left:6px;padding-top:2px;">&nbsp;&nbsp;<span onclick="copyWord(\'����-'+gg[0].replace("��Ӣ","<font color=blue>��Ӣ</font>")+'\');">'+gg[0].replace("��Ӣ","<font color=blue>��Ӣ</font>")+'</span>&nbsp;LV��'+gg[1]+'</div><div style="width:114px; height:11; z-index:5; top:15px;left:13px; position:absolute"></div>';
	lot.innerHTML=lots;
	$('fm').appendChild(lot);
	
	var xbg = document.createElement('div');
	xbg.style.cssText='position:absolute;left:620px;top:49px;color:#338800;font-size:0.8em;z-index:1';
	xbg.innerHTML='<div style="width:114px; height:28px;  top:0px; left:13px; position:absolute"><img src="../images/ui/newmap/dr01.gif" style="opacity:0.7;filter: progid:DXImageTransform.Microsoft.Alpha(style=0,opacity=70,finishOpacity=100)" /></div>';
	$('fm').appendChild(xbg);
	
	ph = document.createElement('IMG'); // gw gif.
	if(!flag){
		ph.style.cssText='position:absolute;left:530px;top:120px;width:250px;height:180px;';
	}else{
		ph.style.cssText='position:absolute;left:530px;top:120px;width:250px;height:180px;display:none';
	}
	ph.src=''+IMAGE_SRC_URL+'/gpc/'+gg[8];
	ph.id='gyg';
	$('fm').appendChild(ph);
	if(flag){
		ph.style.display='block';
		FadeOrShow('gyg',1,18);
	}
	
	// add right head##########################


	// add jn name.
	jnfont = document.createElement('DIV');
	jnfont.style.cssText='position:absolute;left:547px;top:150px;font-weight:bold;width:250px;height:auto;font-family:������κ;font-size:16px;color:yellow;z-index:1000';
	jnfont.id='pfont';
	$('fm').appendChild(jnfont);
	
	// add yao value.
	yao = document.createElement('DIV');
	yao.style.cssText='position:absolute;z-index:1000;width:130px;display:block;filter:alpha(opacity=0);opacity:0;left:170px;top:120px;font-size:18px;color:#3AE131;font-weight:bold';
	yao.id='yaovid';
	$('fm').appendChild(yao);	
	try{
		if(teamLeader>0)
		{
			$('tcatch').style.display='none';
		}
	}catch(e){}
}
var FadeOrShowT=false;
function FadeOrShow(id,start,step){
	var obj=document.getElementById(id);	
	if(start<0||start>100){
		return;
	}
	if(document.all){
		obj.style.filter='alpha(opacity='+(start+step)+')';
	}else{
		obj.style['opacity']=(start+step)/100;
	}
	FadeOrShowT=setTimeout('FadeOrShow("'+id+'",'+start+'+'+step+','+step+')',100);
}
function Usejn(n){
	var cookie_tax = "pm_skill_"+n;
	var timestamp = Date.parse(new Date());
	var tump=0;
	var cold_skill = new Array(319,320,321,322,323);	//��ȴ���ܣ���Ϊ����id,д�������ܸ��ģ���������fight.js,fbfight.js,fbfightGate.php,FightGate.php������һ�£��������ֲ���Ԥ֪�Ĵ���
	switch (n)
	{
		case 319 :
		{
			var need_time = 300000;	//����id:319��ȴʱ�䣬��λ(����)
			break;
		}
		case 320 :
		{
			var need_time = 300000;	//����id:320��ȴʱ�䣬��λ(����)
			break;
		}
		case 321 :
		{
			var need_time = 180000;	//����id:321��ȴʱ�䣬��λ(����)
			break;
		}
		case 322 :
		{
			var need_time = 180000;	//����id:322��ȴʱ�䣬��λ(����)
			break;
		}
		case 323 :
		{
			var need_time = 120000;	//����id:323��ȴʱ�䣬��λ(����)
			break;
		}
	}

	for( i=0; i < cold_skill.length; i++ )
	{
		if( n == cold_skill[i] )
		{
			var cookie_val = document.cookie;
			var arrcookie=cookie_val.split("; ");
			for(var i=0;i<arrcookie.length;i++)
			{
����			var arr=arrcookie[i].split("=");
				if( cookie_tax == arr[0] )
				{
					var val = arr[1];
					break;
				}
			}
			break;
		}
	}
	if ( typeof(need_time) != 'undefined')
	{
		if( typeof(val) == 'undefined' )	//��һ��ʹ��
		{
			document.cookie = cookie_tax+'='+timestamp;
		}
		else
		{
			var wait_time = timestamp - parseInt(val);
			if(wait_time <= need_time)
			{
				var time = (need_time-wait_time)/1000;
				window.parent.Alert("����ȴ�"+time+"��");
				return;
			}
			else
			{
				document.cookie = cookie_tax+'='+timestamp;
			}
		}
	}
	if(n>1)
	{
		for(var i=0;i<bbjn.lenght;i++)
		{
			var ttarr = bbjn[i];
			if (n==bbjn[9])
			{
				tump=ttarr[8];break;
			}
		}
	}
	n=parseInt(n);
	if ( n!=1 && (tump>bbmcur || bbmcur==0 ))
	{n=1;}//alert('����ħ��ֵ���㣬�޷�ʹ��ħ�����ܣ�');return;}
	
	if(using==true) return;
 	using=true;
		
	window.clearTimeout(readH);
	$('timev').innerHTML='PK';
	
	$('tooldiv').style.display='none';
	if(n!=1) gimg='s';
	else gimg='g';
	
	
	// Get ack by jn.
	getAckOfBB(n);
}

function font(str,fun){
	$('pfont').innerHTML=str;
	window.setTimeout(fun,3000);
}

function fontHide(str){ // Pets return stand position.	
	switch (wx_type)
	{
		case "1" :
		{
			str += "<font color=#CC0033>��</font>";
			break;
		}
		case "2" :
		{
			str += "<font color=#CC99FF>����</font>";
			break;
		}
		case "3" :
		{
			str +="<font color=blue>����</font>";
		}
	}
	$('pfont').innerHTML='';
	$('pimg').style.left='10px';
	$('pimg').src=''+IMAGE_SRC_URL+'/bb/'+bb[8];
	if(fc==-1&&(!mMonsterFighting||mmonsters.length==0))
	{
		fatEnd();return;
	}
	window.setTimeout("gwF('"+str+"');", 1000);
}

function gwF(str){
	var dxarr = str.split('<dx>');
	var gwr = dxarr[0].split(',');
	if( gwr[1] == '0' )
	{
		gwr[1] = 'miss';
		var fatValue='<font color=red><i>'+gwr[1]+'</i> </font>!';
	}
	else
	{
		var fatValue='<font color=red><i>-'+gwr[1]+'</i> </font>!!';
	}
	var ag='g';
	if (gwr[0]!='��ͨ����')
	{
		ag='s';
	}

	$('gyg').style.left='120px';
	$('gyg').style.zIndex='100';
	$('gyg').src=''+IMAGE_SRC_URL+'/gpc/'+gg[8].replace('z',ag);
	
	$('pfont').style.left='50px';

	// view jn.

	var strings = gwr[2]+fatValue;
	if(typeof(dxarr[1]) != "undefined")
	{
		strings += "<br /><font color='#ffffff'>"+dxarr[1]+"</font>"
	}
	font(strings,"fontgHide();");
	hpimg('php');
	//window.setTimeout("fontgHide();",2000);
}

function fontgHide(){
	$('gyg').style.left='547px';
	$('gyg').style.zIndex='10';
	$('gyg').src=''+IMAGE_SRC_URL+'/gpc/'+gg[8];
	
	$('pfont').innerHTML='';
	using=false;
	//һ�غϽ�����
	if(fc<0&&(!mMonsterFighting||mmonsters.length==0)) {		
		fatEnd();return;}
	fc++;
	if(mMonsterFighting){
		loadtime(waittime);	
	}else if(teamfightlock=='NONE'){
		loadtime(waittime);	
	}
}

function displayResult(str){
	if(leaderCheckCloseIeT) window.clearTimeout(leaderCheckCloseIeT);
   // add exp.
   if(str.indexOf('���飺')!=-1)
   {
		var lstr = str.split('���飺');
		var lnum = lstr[1].split('<br/>');
		bb[14]=parseInt(bb[14])+parseInt(lnum[0]);
		if (bb[14]>bb[15])
		{
			bb[14] = bb[14]-parseInt(bb[14]);
		}
	   var initw =155*bb[14]/bb[15];
	   $('pfexp').innerHTML=bb[14]+'/'+bb[15];
	   $('pexp').style.width=initw+'px';
	   //explode_start(2);
	}
	else if (str.indexOf('������')!=-1)
	{
		window.setTimeout("window.parent.$('gw').src='./function/Fight_Mod.php?pz=1&p='+petsid+'&auto=2&rd="+Math.random()+"&team_auto="+(window.parent.autoack?1:0)+"'",3000);
	}
	else if (str.indexOf('�����˺�')!=-1)
	{
		//explode_start(1);
	}
	var canAutoFlag = true;
	//parent.recvMsg('CT|window.parent.autoack='+window.parent.autoack+',challengeend='+challengeend+',tgtend='+tgtend);
	if(challengeend == 1)
	{
		$('result').innerHTML=str+' <BR/>'+
		"<span onclick=\"var mm=event.offsetX*event.offsetY;auto1(mm);\""+' style="cursor:pointer;"></span> ��ϲ������ɴ���ս<br /><span onclick="window.parent.$(\'gw\').src=\'/function/Team_Mod.php?n='+inmap+'\';" style="cursor:pointer;"><b>�˳���ս</b></span>';
		canAutoFlag = false;
	}
	else if(tgtend == 1)
	{
		$('result').innerHTML=str+' <BR/>'+
		"<span onclick=\"var mm=event.offsetX*event.offsetY;auto1(mm);\""+' style="cursor:pointer;"></span> ��ϲ�������ͨ����<br /><span onclick="window.parent.$(\'gw\').src=\'/function/Team_Mod.php?n='+inmap+'\';" style="cursor:pointer;"><b>�˳���ս</b></span>';
		canAutoFlag = false;
	}else{
		if(str.indexOf('<!--teamfbFlag-')!=-1)
		{
			var spos = str.replace(/.*<!--teamfbFlag-(\d+)-->.*/,'$1');	
			if(spos!=3){
				$('result').innerHTML="<font style='color: rgb(153, 0, 102);'>��Ӹ���"+spos+"��ͨ��</font><hr/>"+str;
				$('result').style.display='';setTimeout('window.location="/function/tarot_Mod.php"',5000);
				return;
			}
			else if(str.indexOf('boss')==-1)
			{
				$('result').innerHTML="<font style='color: rgb(153, 0, 102);'>��Ӹ���"+spos+"�ؽ�����</font><hr/>"+str;
				$('result').style.display='';setTimeout('window.location="/function/tarot_Mod.php"',5000);
				return;
			}else{
				$('result').innerHTML="<font style='color: rgb(255, 0, 0);'>��ϲ��,��Ӹ������</font><hr/>"+str;
				$('result').style.display='';setTimeout('window.location="/function/Team_Mod.php"',5000);
				return;
			}
			
		}else if(teamLeader==parent.myUid||teamLeader==0){
			if(typeof(teamautofight)!='undefined'&&!teamautofight) canAutoFlag=false;
			$('result').innerHTML=str+' <BR/>'+
		"<span onclick=\"var mm=event.offsetX*event.offsetY;auto1(mm);\""+' style="cursor:pointer;"><b>����̽��</b></span> <span onclick="window.parent.$(\'gw\').src=\'/function/Team_Mod.php?n='+inmap+'&returnv=1;\'" style="cursor:pointer;"><b>���ش�ׯ</b></span><br/>';						
		}else{
			if(teamLeader!=parent.myUid){//��Ա������Ҳ���˳�����
				setTimeout('keepTeam()',30000);
			}
			$('result').innerHTML=str;
		}
	}
	
	$('result').style.display='';
	$('timev').innerHTML='KO';
	using=true;
	if(window.parent.autoack==true&&canAutoFlag){
		//parent.recvMsg('CT|auto continue!');
		window.setTimeout("auto();",1000);
	}else{
		//parent.recvMsg('CT|no auto !');	
	}
}
function keepTeam()
{
	var opt = {
		 method: 'get',
		 onSuccess: function(t){	
		 	setTimeout('keepTeam()',30000);
		 },
		 on404: function(t) {
		 },
		 onFailure: function(t) {
		 },
		 asynchronous:true        
	}
	var ajax=new Ajax.Request('/function/team.php?a2&checkOnly=1&rd='+Math.random(), opt);
}
var lastusetime = 0;
function auto1(a){
	now = (new Date()).getTime();
	if(now-lastusetime<2000){		
		return;
	}
	lastusetime = now;
	//window.setTimeout("window.parent.$('gw').src='function/Fight_Mod.php?p="+petsid+"&bid="+a,1000);
	window.setTimeout("window.parent.$('gw').src='function/Fight_Mod.php?p="+petsid+"&bid="+a+"&rd="+Math.random()+"'",1000);
	//window.parent.$('gw').src='/function/Fight_Mod.php?p="+petsid+"&bid='+mm;
}
function auto()
{
	now = (new Date()).getTime();
	if(now-lastusetime<1000){	
		setTimeout('auto()',1000);
		return;
	}
	lastusetime = now;
	window.setTimeout("window.parent.$('gw').src='./function/Fight_Mod.php?pz=2&p='+petsid+'&auto=2&rd="+Math.random()+"&team_auto="+(window.parent.autoack?1:0)+"'",1000);
	//window.parent.$('gw').src='./function/Fight_Mod.php?p='+petsid+'&auto=2';
}
var readH=false;
function loadtime(m){
	try{
		window.clearTimeout(FadeOrShowT);
		if(document.all){
			$('gyg').style.filter='alpha(opacity=100)';
		}else{
			$('gyg').style['opacity']=100;
		}
	}catch(e){
		alert(e)
	}
	try{window.clearTimeout(readH);}catch(e){}
	if(using==true){
		window.clearTimeout(readH);
		return;
	}
	if(teamfightlock=='NONE'||!teamfightlock)
		$('tooldiv').style.display='';
	else
		$('tooldiv').style.display='none';
	$('timev').innerHTML = m--;
	if(m==-1) 
	{	
		window.clearTimeout(readH);
		if(teamfightlock=='NONE'||!teamfightlock){
			Usejn(window.parent.usejn);
		}
		else if(teamfightlock==true)
		{
			//getAckOfBB(0);
		}
		return;
	}
	else{
		readH=window.setTimeout("loadtime("+m+");", 1000);
	}
}
createLeft();
loadtime(waittime);
//parent.recvMsg('CT|teamfightlock='+teamfightlock+',teamLeader='+teamLeader);
// Server part.###########################
// @Get bb ack.
// @Now, for demo, simple test in localhost.
function getAckOfBB(id){
	var opt = {
    		 method: 'get',
    		 onSuccess: function(t){
				 if(t.responseText == 'SKILLCOLD')
				 {
					parent.Alert("��ʹ�õļ���������ȴ��");window.location="/function/Team_Mod.php";return;
				 }
				if(t.responseText=='TEAMERROR')
				{
					parent.Alert("�ж�Ա�˳����ߵ��߻������룡");window.location="/function/Team_Mod.php";return;
				}
			 	if(t.responseText == 0 || t.responseText=='') return;
				else if(t.responseText > 0){
					var pets = t.responseText;
					eval('setTimeout("window.location=\'/function/Fight_Mod.php?p='+pets+'&auto=1000\'",1000)');
				}
			 	else {splits(t.responseText);}
    		 },
    		 on404: function(t) {
    		 },
    		 onFailure: function(t) {
    		 },
    		 asynchronous:true        
		}
	var ajax=new Ajax.Request('../function/FightGate.php?id='+id+'&g='+gg[11]+'&checkwg=checked&rd='+Math.random(), opt);
}
var continueMonsterHitBBJs;
//var mmonsters = [];
var bbhpmp = [];
var monsterattack = [];
var curGG = [];
var gwdeadFlag=false;
var wx_type = "";
// Split server info.
function splits(str)
{
		var crit = str.split('*');
		str = crit[0];
		wx_type = crit[2];
	try{		
		window.clearTimeout(FadeOrShowT);
		if(document.all){
			$('gyg').style.filter='alpha(opacity=100)';
		}else{
			$('gyg').style['opacity']=100;
		}
		leaderCheckCloseIe();
	}catch(e){
		alert("splits Line 471:"+e)
	}
	var ackstr = "";
	if(str.indexOf("#<tgtend>") != -1){
		tgtend = 1;
		if(str.indexOf("<ack>") != -1)
		{
			var ackstr2 = str.split("#<tgtend>");
			ackstr = ackstr2[0].split("<ack>");
		}
	}
	else if(str.indexOf("#challengeend") != -1)
	{
		challengeend = 1;
		if(str.indexOf("<ack>") != -1)
		{
			var ackstr1 = str.split("#challengeend");
			ackstr = ackstr1[0].split("<ack>");
		}
	}else{
		if(str.indexOf("<ack>") != -1)
		{
			ackstr = str.split("<ack>");
		}
	}
	
	mMonsterFighting = false;
	//alert(str)
	if(str == 'autoend') {autoFitStart(2);return;}
	var tt = str.split('#');
	if(str.indexOf("�����ˣ�����")!=-1){
		displayResult(tt[2]);
	}
	var bbr = tt[0].split(',');
	if(bbr[2] == '0')
	{
		bbr[2] = 'miss';
	}
	var useInfo = tt[4].split(',');
	//alert(bbr[4]);
	
	//�õ���Ѫ����ħ������
	var hpinfo = "";
	var mpinfo = "";
	if(bbr[4] != null)
	{
		if(bbr[4].indexOf('��Ѫ') != -1)
		{
			var infos = bbr[4].split("==");
			hpinfo = infos[0];
			mpinfo = infos[1];
		}
		else if(bbr[4].indexOf('��ħ')!= -1)
		{
			mpinfo = bbr[4];	
		}
	}
	
    //����
	var gwr = tt[1].split(',');
	if(gwr[1] == '0')
	{
		gwr[1] = 'miss';
	}
	bbcur = bbr[0];
	bbmcur=bbr[1];
	gwcur = gwr[0];
	//alert(gwcur)
    endtips = tt[2];
	//alert(tt[5]);
	if(useprops == 0)
	{
		//alert('a1')
		var iw = parseInt((imgw/bbmpmax)*bbr[1]);	
		
		$('pmp').style.width=iw+'px';
		$('bmpf').innerHTML=bbr[1]+'/'+bb[13];
		var fontValue='';
	
		//����
		if(useInfo[0]!=undefined&&useInfo[0]=="3"){//959658,8925,0,��Ѫ��,3,-20,25#91,15,��ͨ����##
			/*if(endtips.length>0){
				
			}else{*/
				if(useInfo[1]<=0){//�����ı�
					fontValue+=bbr[3]+'<font color="#00AF00"><i>'+(useInfo[1]>0?"":"+")+(-1*useInfo[1])+'</i> </font>';	
				}
				if(useInfo[2]<0){//ħ���ı�
					fontValue+='<br/>'+bbr[3]+'<font color="#0000AF"><i>'+(useInfo[2]>0?"":"+")+(-1*useInfo[2])+'</i> </font>';	
				}
				setTimeout("hpimg('php')",3000);
				$('pfont').style.top='200px';
				$('pfont').style.left='80px';
				$('pfont').style.zIndex='1000';
				font(fontValue,"fontHide('"+tt[1]+tt[3]+"');");
				fontValue="";
				window.setTimeout("using=false;loadtime(waittime);",7500);
			//}
		}else{
			
			//Move pets and view jn name.
			$('pimg').style.left='470px';
			$('pimg').style.zIndex='100';
			$('pimg').src=''+IMAGE_SRC_URL+'/bb/'+bb[8].replace('z',gimg);
			if( bbr[2] == 'miss')
			{
				var fontValue='<font color=red><i>'+bbr[2]+'</i> </font>!';
			}
			else
			{
				var fontValue='<font color=red><i>-'+bbr[2]+'</i> </font>!!';
			}
			$('pfont').style.left='620px';
			
			//�ж���Ѫ����ħ��ֵ�Ƿ�Ϊ�պ��Ƿ���ʾ
			var strings;
			if(hpinfo != null && mpinfo != null)
			{
				strings = bbr[3]+'! '+fontValue+"<font color='#14FD10'>"+hpinfo+"</font><font color='#0067CB'>"+mpinfo+"</font>";
			}
			else if(hpinfo != null && mpinfo == null)
			{
				strings = bbr[3]+'! '+fontValue+"<font color='#14FD10'>"+hpinfo+"</font>";
			}
			else if(hpinfo == null && mpinfo != null)
			{
				strings = bbr[3]+'! '+fontValue+"<font color='#0067CB'>"+mpinfo+"</font>";
			}
			else
			{
				strings = bbr[3]+'! '+fontValue;
			}
			switch (crit[2])
			{
				case "1" :
				{
					strings = "<font color=red>��ǿ</font>"+strings;
					break;
				}
				case "2" :
				{
					strings = "<font color=red>����</font>"+strings;
					break;
				}
				case "3" :
				{
					strings = "<font color=red>����</font>"+strings;
				}
			}
			if(crit[1] ==  '1')	//����
			{
				strings = "<p><font color=red><b>����  </b></font></p>" + strings;
			}
			if(ackstr[1] != null)
			{
				var ack = "<font color='#9900FF'>"+ackstr[1]+"</font>";
				if(strings != null)
				{
					strings += "<br />"+ack;
				}
				else
				{
					strings = ack;
				}
			}
	
			//�����ж�
			//��������
			if(tt[5]=='MULTI_MONSTRTER_CONTINUE'){
				mMonsterFighting = true;				
				hpimg('ghp');
				font(strings,"fontHide('"+tt[1]+tt[3]+"');");//$('tooldiv').style.display='block';};
				setTimeout("if(teamfightlock=='NONE'||!teamfightlock){using=false;loadtime(waittime);}",7500);
			}else{
				hpimg('ghp');
				font(strings,"fontHide('"+tt[1]+tt[3]+"');");
			}
		}
	}
	else
	{
		useprops = 0;
		//gwF(tt[1]);
		window.setTimeout("gwF('"+tt[1]+tt[3]+"');", 1000);
	}
	if (tt[3]!='') word(tt[3]);
}
var mMonsterFighting = false;
//var toolBackTimeout=false;
function multiMomsterContinue(){
	FadeOrShow('gyg',90,-18);
	setTimeout('mmonsterMoreMovie(0)',1500);
}

function createGW(flag)
{
	window.clearTimeout(FadeOrShowT);
	try{$('fm').removeChild(hp);}catch(e){}
	try{$('fm').removeChild(lot);}catch(e){}
	try{$('fm').removeChild(jnfont);}catch(e){}
	try{$('fm').removeChild(yao);}catch(e){}
	//try{$('fm').removeChild(jnbk);}catch(e){}
	try{$('fm').removeChild(ph);}catch(e){}
	
	sp1 = document.createElement('DIV'); //hp font
	sp1.style.cssText='position:absolute;left:620px;top:49px;color:#338800;z-index:2;font-size:0.9em;';
	sp1.id='ghpf';	
	
	sp1.innerHTML="";
	$('fm').appendChild(sp1);	
	
	hp = document.createElement('DIV'); // hp gif
	hp.style.cssText='width:110px;height:11px;position:absolute;left:620px;top:65px; z-index:3; padding-left:13px';
	var hpsrc = '<div id="ghpbkvalue" style="width:96px;height:11px;padding-left:18px;left:13px;position:absolute;color:#412804;font-size:10px">'+gg[5]+'/'+gg[5]+'</div><div id="ghpbk" style="width:96px;height:11px;padding-left:18px;background-repeat:no-repeat;background-position:right top;background-image:url(../images/ui/newmap/dr03.gif);overflow:hidden"><div id="ghp" style="background-image:url(../images/ui/newmap/dr04.gif);width:91px;background-repeat:repeat-x;height:11px;"></div></div>';

	hp.innerHTML = hpsrc;
	$('fm').appendChild(hp);
	
	lot = document.createElement('div');
	lot.style.cssText='position:absolute;left:620px;top:49px;color:#338800;font-size:0.8em;z-index:3';
	var lots = '<div style="width:31px; height:37px; z-index:4; top:0px; left:0px; position:absolute;background:url(../images/ui/newmap/dr02.gif);"><div style="z-index:4; top:10px; left:15px; position:absolute"><font color="#2A9E49" size="2.5"><b> '+gg[2]+'</b></font></div></div><div style="width:114px; height:11; z-index:5; top:0px;left:13px; position:absolute; padding-left:6px;padding-top:2px;">&nbsp;&nbsp;<span onclick="copyWord(\'����-'+gg[0].replace("��Ӣ","<font color=blue>��Ӣ</font>")+'\');">'+gg[0].replace("��Ӣ","<font color=blue>��Ӣ</font>")+'</span>&nbsp;LV��'+gg[1]+'</div><div style="width:114px; height:11; z-index:5; top:15px;left:13px; position:absolute"></div>';
	lot.innerHTML=lots;
	$('fm').appendChild(lot);
	
	var xbg = document.createElement('div');
	xbg.style.cssText='position:absolute;left:620px;top:49px;color:#338800;font-size:0.8em;z-index:1';
	xbg.innerHTML='<div style="width:114px; height:28px;  top:0px; left:13px; position:absolute"><img src="../images/ui/newmap/dr01.gif" style="opacity:0.7;filter: progid:DXImageTransform.Microsoft.Alpha(style=0,opacity=70,finishOpacity=100)" /></div>';
	$('fm').appendChild(xbg);
	
	ph = document.createElement('IMG'); // gw gif.
	if(!flag){
		ph.style.cssText='position:absolute;left:530px;top:120px;width:250px;height:180px;';
	}else{
		ph.style.cssText='position:absolute;left:530px;top:120px;width:250px;height:180px;display:none';
	}
	ph.src=''+IMAGE_SRC_URL+'/gpc/'+gg[8];
	ph.id='gyg';
	
	if(document.all){
		ph.style.filter='alpha(opacity=100)';
	}else{
		ph.style['opacity']=100;
	}
	$('fm').appendChild(ph);
	//if(flag){
	ph.style.display='block';
	//	FadeOrShow('gyg',1,18);
	//}
	
	// add right head##########################


	// add jn name.
	jnfont = document.createElement('DIV');
	jnfont.style.cssText='position:absolute;left:547px;top:150px;font-weight:bold;width:250px;height:auto;font-family:������κ;font-size:16px;color:yellow;z-index:1000';
	jnfont.id='pfont';
	$('fm').appendChild(jnfont);
	
	// add yao value.
	yao = document.createElement('DIV');
	yao.style.cssText='position:absolute;z-index:1000;width:130px;display:block;filter:alpha(opacity=0);opacity:0;left:170px;top:120px;font-size:18px;color:#3AE131;font-weight:bold';
	yao.id='yaovid';
	$('fm').appendChild(yao);		
}
var order=0;
//var dbgstr="";
function mmonsterMoreMovie(){
	window.clearTimeout(FadeOrShowT);
	var movieend=false;
	order++;
	
	if(order==mmonsters.length){
		movieend = true;
	}
	if(!movieend){
		mmonsters[order][2]=getWx(mmonsters[order][2]);
		gg=mmonsters[order];
	}
	var tmp=""
	gwhpmax = gg[5];
	gwmpmax = gg[6];

	createGW(false);
		
	var tablelist = $('showmmonsterlistdetails');
	using=false;

	var gwdeadFlag=true;
	
	if(gwdeadFlag){
		try{
			tablelist.deleteRow(0);
			if(tablelist.rows.length>0){
				tablelist.rows[0].cells[0].style.color = "#ff0000";
			}
		}catch(e){
			alert("new problem:"+tablelist.rows.length+"|"+order);
		}
	}	
	fc=1;
	setTimeout('loadtime(waittime)',1500);	
		
}
function checkbbdie(order){
	if(typeof(bbhpmp[order])!='undefined'&&bbhpmp[order][0]<=0){
		//$('result').style.display='block';
		$('timev').innerHTML='KO';
		FadeOrShow('pimg',90,-10);
		FadeOrShow('cf1',90,-10);
		//$('result').style.display='���� '+bb[0]+' �ܵ��������˺����Ѿ�����ս��������';
		displayResult('���� '+bb[0]+' �ܵ��������˺����Ѿ�����ս��������');
		try{
			window.clearTimeout(readH);
		}catch(e){}
	}else{
		setTimeout('mmonsterMoreMovie('+(order+1)+')',2500);
	}
}
/**
* @Make random number.
*/
function rand(under, over){ 
        switch(arguments.length){ 
            case 1: return parseInt(Math.random()*under+1); 
            case 2: return parseInt(Math.random()*(over-under+1) + under);  
            default: return 0; 
        } 
    }  // shawl.qiu script 
// get jn
function jnstr(str){
	return eval(str);
}

/**
* hp img view
*/

function hpimg(imgid)
{

	  var hpmax;
	  var cur;
	  var imgw;
		if(imgid == 'php') // gw ack, view bb.
		{
			imgw   = 155;
			$('bhpf').innerHTML=bbcur+'/'+bb[12];
			
			hpmax=bb[12];
			if (bbcur<=0) {fc=-2;bbcur=0;}
			cur = bbcur;
		}
		else 
		{		    
			imgw = 91;
			$('ghpbkvalue').innerHTML=gwcur+'/'+gg[5];
			hpmax=gg[5];
			if (gwcur<=0) {fc=-1;gwcur=0;}
			cur = gwcur;
			$('ghpbk').style.width=(parseInt((imgw/hpmax)*cur)+5)+'px';
			//alert(parseInt((imgw/hpmax)*cur))
		}

		var iw = parseInt((imgw/hpmax)*cur);
		$(imgid).style.width=iw+'px';
		$(imgid).border='0px';
}

function fatEnd(){
	FadeOrShow('gyg',90,-18);
	//'TeamFightNextMonster';TeamStillAlive
	//alert(endtips)
	if(endtips=='TeamFightNextMonster')
	{
		multiMomsterContinue();
	}else if(endtips=='TeamStillAlive'){
		window.location='/function/Fight_Mod.php?TeamStillAlive='+Math.random();
	}else{
		displayResult(endtips); // Only test.
		FadeOrShow('gyg',90,-18);
	}
}
// Create yao window.

function yaoWin(n){
	var d = document.createElement("DIV");
	if(n == 1) // hp.
	{
		d.style.cssText = "position:absolute;border:1px solid #ccc;left:515px;top:190px; background-color:red;display:none;";
		d.id = 'ywin';
	}
	$('fm').appendChild(d);

}
function viewyw(n)
{
	if(n==1) $('ywin').style.top = '249px';
	else $('ywin').style.top = '249px';
	loadYW(n);
	$('ywin').style.display = '';
}

function UseYao(n)
{
	if( $('timev').innerHTML <=1 )
	{
		window.parent.Alert('���ڲ��ܳ�ҩ');return;
	}
	if(using == true) {window.parent.Alert('ս��״̬�����ܳ�ҩ��');return;}
	var opt = {
    		 method: 'get',
    		 onSuccess: function(t){
			 	if(t.responseText != '0')
				{	
					if(t.responseText != 'hasusemedbuff')
					{
						var arr = t.responseText.split(',');
						arr[0] = parseInt(arr[0]);
						arr[1] = parseInt(arr[1]);
						arr[2] = arr[2];
						arr[3] = arr[3];
						if (arr[0]!=0)
						{
							bbcur= parseInt(bbcur)+parseInt(arr[0]);
							if (bbcur>bb[12]) bbcur = bb[12];
							var initw =  parseInt((imgw/bb[12])*bbcur);	; // init img width.
							$('php').style.width=initw+'px';
							$('bhpf').innerHTML=bbcur+'/'+bb[12];
						}
						if (arr[1]!=0)
						{
							bbmcur= parseInt(bbmcur)+parseInt(arr[1]);			
							if (bbmcur>bb[13]) bbmcur = bb[13];
							
							var initw =  parseInt((imgw/bb[13])*bbmcur);	; // init img width.
							$('pmp').style.width=initw+'px';
							$('bmpf').innerHTML=bbmcur+'/'+bb[13];
						}
						window.clearTimeout(readH);
						useprops = 1;
						using = true;
						var tip='';
						if (arr[0]!=0) tip=arr[0]+'hp ';
						if (arr[1]!=0) tip+=arr[1]+'mp';
						if ( arr[2]!=0 )
						{	
							tip+=arr[2]+'����';
							buff_div('����',arr[2]);
						}
						if (arr[3]!=0)
						{
							tip+=arr[3]+'����';
							buff_div('����',arr[3]);
						}
						yaoValue(tip,0);
						//getAckOfBB(1);
					}
					else
					{
						window.parent.Alert("���Ѿ�ʹ�ù����Ƶ���ʱ״̬ҩ��Ŷ");
					}
				}
    		 },
    		 on404: function(t) {
    		 },
    		 onFailure: function(t) {
    		 },
    		 asynchronous:true        
		}
	var ajax=new Ajax.Request('../function/getProps.php?id='+n, opt);
}

//yaoWin(1);

/**left:80,top:200*/

function yaoValue(n,c)
{
	if (c==0)
	{$('yaovid').innerHTML = '+'+n;
	}
	var obj=$('yaovid').style;
	if( typeof(obj.filter) != 'undefined' )
	{
		var str=obj.filter.replace("\"","");
		var c=parseInt(str.replace("alpha(opacity=","").replace(")",""));
		c+=10;

    	if(c>=100)
		{ 
			window.setTimeout("flashYao('"+n+"',100);", 1000);
			c=0;
			return;
		}
		else
		{ 
			obj.filter="alpha(opacity="+c+")";
			window.setTimeout("yaoValue('"+n+"','"+c+"');",5);
		}
	}
	else
	{
		var str = obj.opacity;
		c = parseFloat(str);
		c += 0.1;
		if(c >= 1)
		{
			c = 1;
			window.setTimeout("flashYao('"+n+"',100);", 1000);
			c = 0;
			return;
		}
		else
		{
			obj.opacity = c;
			window.setTimeout("yaoValue('"+n+"','"+c+"');",5);
		}
	}
}
function buff_div(para,val)
{
	var med_buff = $('med_buff');
	switch (para)
	{
		case '����' :
		{
			med_buff.innerHTML += '<img src="../new_images/ui/buff_ac.jpg" title = "��������:'+val+'" />';
			break;
		}
		case '����' :
		{
			med_buff.innerHTML += '<img src="../new_images/ui/buff_mc.jpg" title = "��������:'+val+'" />';
			break;
		}
	}
}
function flashYao(n,c)
{
	var obj=$('yaovid').style;
	if( typeof(obj.filter) != 'undefined' )
	{
		var str=obj.filter.replace("\"","");
		var c=parseInt(str.replace("alpha(opacity=","").replace(")",""));
		c-=10;

    	if (c<=0)
		{	obj.filter="alpha(opacity=0)";
			$('yaovid').innerHTML = '';
			window.setTimeout("getAckOfBB(1);", 1000);
		}
		else{
			obj.filter="alpha(opacity="+c+")";
			window.setTimeout("flashYao('"+n+"','"+c+"');",5);
		}
	}
	else
	{
		var str = obj.opacity;
		c = parseFloat(str);
		c -= 0.1;
		if(c <= 0 )
		{
			c = 0;
			obj.opacity = 0;
			$('yaovid').innerHTML = '';
			window.setTimeout("getAckOfBB(1);", 1000);
		}
		else
		{
			obj.opacity = c;
			window.setTimeout("flashYao('"+n+"','"+c+"');",5);
		}
	}
}

// Catch part.
function viewCatch(p)
{
//	window.parent.Alert('��δ���ţ�');
//	return;
	if(teamLeader>0)
	{
		parent.Alert('���״̬������׽!');
		return;
	}
	if(catchs==1){window.parent.Alert('�ȴ���һ�λ���ɣ�');return;}
	var opt = {
    		 method: 'get',
    		 onSuccess: function(t){
			 	var n = parseInt(t.responseText);
				if (n==2){window.parent.Alert('��׽�����벶׽�������в�ͬ��');}
				else if(n==3){window.parent.Alert('�˱������ܲ�׽��');}
				else if(n==4){window.parent.Alert('����������ĵ���̫����!');}
				else if(n==6){window.parent.Alert('Я���ĳ�����࣬����õ��������ٲ�׽!');}
				else if(n==7){window.parent.Alert('���������������׽��������~');}
				else if(n==10){
					window.parent.Alert('������ϲ������׽��һ��������');
					window.setTimeout('window.location.reload()',1000);
				}
				else if(n==12){
					window.parent.Alert('ѡ����߶Ե�ǰ������Ч��');
				}
				else if(n==13)
				{
					window.parent.Alert('ʧ�ܣ�û�ܻ���κε��ߣ�');
				}
				else if(n==15)
				{
					window.parent.Alert('������ϲ������õ��ߣ�');
					window.setTimeout('window.location.reload()',1000);
				}
				else if(n==20){
					window.parent.Alert('��������û����Ӧ�ĵ��ߣ�');				
				}else if(n==30){
					window.parent.Alert('��õ��߳ɹ���');
				}else {window.parent.Alert('��׽ʧ��!');}
				catchs=1;
				//if(n!=10) getAckOfBB(1);
    		 },
    		 on404: function(t) {
    		 },
    		 onFailure: function(t) {
    		 },
    		 asynchronous:true        
		}
	var ajax=new Ajax.Request('../function/get.Catch.php?pid='+p, opt);
}

function word(str)
{
	var oldstr = $('word').innerHTML;
	if (oldstr == str) return;
	$('word').style.display='';
	$('word').innerHTML = str;
	window.setTimeout("hideword();",5000);
}
function hideword()
{
	$('word').style.display='none';
}

// Load tools
function tbs(){
	var str = '<table border="0" cellspacing="0" cellpadding="0"><tr><td width="17"><img src="../images/ui/newmap/bk01.gif" width="17" height="123" /></td><td background="../images/ui/newmap/bk04.gif">';
	return str;
}
function tbe(){
	return '</td><td width="31"><table width="100%" border="0" cellspacing="0"cellpadding="0"><tr><td width="31"><img src="../images/ui/newmap/bk02.gif" width="31" height="31" onclick="$(\'jntool\').style.display=\'none\';" style="cursor:pointer;" /></td></tr><tr><td><img src="../images/ui/newmap/bk03.gif" width="31" height="92" /></td></tr></table></td></tr></table><table width="100" border="0" align="left" cellpadding="0" cellspacing="0" style="margin-left:10px"><tr><td><img src="../images/ui/newmap/jiantou.gif" width="17" height="16" /></td></tr></table>';
}
function tbs1(){
	return '<table width="100%" border="0" cellspacing="0" cellpadding="0"><tr>';
}
function tbe1(){
	return '</tr></table>';
}
function foreach(str){
	return '<td height="28px"><table border="0" cellspacing="0" cellpadding="0"><tr><td width="10"><img src="../images/ui/newmap/bk05.gif" width="3" height="22" /></td><td align="center" background="../images/ui/newmap/bk.jpg">'+str+'</td><td width="4"><img src="../images/ui/newmap/bk06.gif" width="4" height="22" /></td></tr></table></td>';
}
function loadtool(n)
{
	var toolshd = $('jntool');
	toolshd.style.display='';
	toolshd.innerHTML='';
	if (n == 1) // auto
	{
		var tl = tbs()+tbs1()+foreach('<span onclick="autoFitStart(2);" style="cursor:pointer;">��ʼ��Ұ��Զ�����(1.2������)</span>')+foreach('<span onclick="autoFitStart(4);" style="cursor:pointer;">�ر��Զ�ս��</span>')+'</tr><tr>'+foreach('<span onclick="autoFitStart(1);" style="cursor:pointer;">��ʼԪ�����Զ�����(1.5������)</span>')+foreach('<span onclick="autoFitStart(3);" style="cursor:pointer;">�ر��Զ�ս��</span>')+tbe1()+tbe();
		toolshd.innerHTML = tl;
	}
	else if(n==2)	// ack set.
	{
		var toolshda = tbs()+tbs1();
		var j = 1;
		var checked = 2;
		for(var i=0;i<=7; i++)
		{
			try{
				var tt = bbjn[i];
				if ( typeof bbjn[i] == 'undefined') continue;
				if(j % 3 == 0){
					toolshda += '<tr>';
				}
				toolshda += foreach("<span onclick=\"window.parent.usejn='"+tt[9]+"';closetl();\" style='cursor:pointer;'>"+ tt[0] +"</span>");
				j++;
				if(j % 3 == 0){
					toolshda += '<tr>';
				}
			}catch(e){continue;}
		}
		toolshda += tbe1()+tbe();
		toolshd.innerHTML = toolshda;
	}
	else if(n==3)	// common ack
	{
		closetl();Usejn(1);
	}
	else if(n==4) // skill
	{
		var toolshda = tbs()+tbs1();
		var j = 1;
		for(var i=0;i<=7; i++)
		{
			try{
				var tt = bbjn[i];
				if ( typeof bbjn[i] == 'undefined') continue;
				if(j % 3 == 0){
					toolshda += '<tr>';
				}
				toolshda += foreach('<span onclick="Usejn('+tt[9]+');closetl();" style="cursor:pointer;">'+tt[0]+'</span>');
				j++;
				if(j % 3 == 0){
					toolshda += '<tr>';
				}
			}catch(e){continue;}
		}
		toolshda += tbe1()+tbe();
		toolshd.innerHTML = toolshda;
	}
	else if(n==5) // help
	{
		var toolshda = tbs()+tbs1();
		var j = 1;
		var checked = 2;
		if(bbfzp[0]==0) {toolshd.innerHTML='';return;}
		for(var i=0;i<=bbfzp.length; i++)
		{
			try{
				var tt = bbfzp[i];
				if ( typeof bbfzp[i] == 'undefined') continue;
				if(j % 3 == 0){
					toolshda += '<tr>';
				}
				
				toolshda += foreach('<span onclick="closetl();UseYao('+tt[2]+');" style="cursor:pointer;">'+tt[0]+'</span>');
				j++;
				if(j % 3 == 0){
					toolshda += '<tr>';
				}
				checked = 1;
				//toolshd.innerHTML+="<span onclick=\"UseYao("+tt[2]+");closetl();\" style='border:1px solid #ccc;cursor:pointer;'>"+ tt[0] +"</span>";
				
			}catch(e){continue;}
		}
		toolshda += tbe1()+tbe();
		if(checked == 1){
			toolshd.innerHTML = toolshda;
		}else{
			toolshd.innerHTML = tbs()+tbs1()+foreach('��ʱû�и�������')+tbe1()+tbe();
		}
	}
	else if(n==6) // catch
	{
		var toolshda = tbs()+tbs1();
		var j = 0;
		var checked = 2;
		if(catcharr[0]==0) {toolshd.innerHTML='';return;}
		for(var i=0;i<=catcharr.length; i++)
		{
			try{
				var tt = catcharr[i];
				if ( typeof catcharr[i] == 'undefined') continue;
				if(j % 3 == 0){
					toolshda += '<tr>';
				}
				toolshda += foreach('<span onclick="viewCatch('+tt[2]+');closetl();" style="cursor:pointer;">'+tt[0]+'</span>');
				j++;
				if(j % 3 == 0){
					toolshda += '</tr>';
				}
				
				checked = 1;
			}catch(e){continue;}
		}
		toolshda += tbe1()+tbe();
		if(checked == 1){
			toolshd.innerHTML = toolshda;
		}else{
			toolshd.innerHTML = tbs()+tbs1()+foreach('��ʱû�о�����')+tbe1()+tbe();
		}
		
		//viewCatch();
		
	}
	else if(n==7) // props.
	{
		//toolshd.innerHTML = tbs()+tbs1()+foreach('ʹ�õ���')+tbe1()+tbe();
		var opt = {
    		 method: 'get',
    		 onSuccess: function(t){
			 	var text = t.responseText;
			 	if(text.indexOf('û�д�����ߣ�') != -1)
				{
					toolshd.innerHTML = tbs()+tbs1()+foreach('��û�д�����ߣ�')+tbe1()+tbe();
				}else{
					var arr = text.split(',');
					var len = arr.length - 1;//���һ���ǿյ�
					var newArr = new Array();
					var toolshda = tbs()+tbs1();
					var j = 0;
					var checked = 2;
					for(var i = 0;i < len;i++){
						newArr = '';
						if(arr[i] == ''){
							continue;
						}
						newArr = arr[i].split(':');
						if(j % 3 == 0){
							toolshda += '<tr>';
						}
						toolshda += foreach('<span onclick="challengeProps('+newArr[1]+');closetl();" style="cursor:pointer;">'+newArr[0]+'</span>');
						j++;
						if(j % 3 == 0){
							toolshda += '</tr>';
						}
						checked = 1;
					}
					toolshda += tbe1()+tbe();
					if(checked == 1){
						toolshd.innerHTML = toolshda;
					}
				}
    		 },
    		 on404: function(t) {
    		 },
    		 onFailure: function(t) {
    		 },
    		 asynchronous:true        
		}
		var ajax=new Ajax.Request('../function/challenge_props.php?op=propslist', opt);
	}
	else if(n==8)
	{
		if(teamLeader==0){
			window.parent.$('gw').src='./function/Expore_Mod.php';
		}else{
			parent.Alert('�����ܶ���һ������!');
		}
	}
	else {toolshd.style.display='none';return false;}
}

function challengeProps(id){
	if(id == ''){
		return;
	}
	var opt = {
    		 method: 'get',
    		 onSuccess: function(t){
			 	var n = t.responseText;
				if(n == '1'){
					window.parent.Alert('��������');
				}else if(n == '2'){
					window.parent.Alert('��ս���ﲻ��Ϊ�գ�');
				}else if(n == '3'){
					window.parent.Alert('��������ս��ͼ����ʹ�ô�����Ʒ��');
				}else if(n == '4'){
					window.parent.Alert('��û�д���Ʒ��');
				}else if(n == '5'){
					window.parent.Alert('���Ǵ�����ߣ�');
				}else if(n == '100'){
					bbcur = bb[12];
					var initw =  parseInt((imgw/bb[12])*bbcur);	; // init img width.
					$('php').style.width=initw+'px';
					$('bhpf').innerHTML=bbcur+'/'+bb[12];
					window.parent.Alert('��ϲ����Ѫ�����ˣ�');
				}
    		 },
    		 on404: function(t) {
    		 },
    		 onFailure: function(t) {
    		 },
    		 asynchronous:true        
		}
	var ajax=new Ajax.Request('../function/challenge_props.php?op=usedprops&id='+id, opt);
}

function closetl()
{
	$('jntool').style.display='none';
	$('tooldiv').style.display='none';
	
}
function autoFitStart(ns)
{
	if(teamLeader>0)
	{
		if(teamLeader!=parent.myUid)
		{
			parent.Alert('�ӳ����������Զ�ս����');
			return;
		}
		if(teamautofight==1){
			if(ns==1||ns==2){
				window.parent.autoack=true;
				window.location='/function/Fight_Mod.php?pz=3&team_auto=1&setteamauto&rd='+Math.random();
			}else{
				wt=10;
				window.parent.autoack=false;
				window.parent.waittime=wt;
				waittime=wt;
				closetl();
				window.location='/function/Fight_Mod.php?pz=4&team_auto=0&setteamauto&rd='+Math.random();				
			}
		}else{
			parent.Alert('�Զ�ս���빺������Զ�ս�����ߣ������Զ�ս��������');
		}
		return;
	}
	var af = "";
	var wt = "";
	var mt = "";
	var tip = "";
	if(ns == 1)//����Ԫ�����Զ�ս��
	{
		af = true;
		mt = "open";
		tip = '����';
	}
	else if(ns == 2)//������Ұ��Զ�ս��
	{
		af = true;		
		mt = "open";		
		tip = '����';	
	}
	else if(ns == 3)//�ر��Զ�ս��
	{
		af = false;
		mt = "close";
		tip = '�ر�';
	}
	else if(ns == 4)//�ر�Ԫ��ս��	
	{		
		af = false;	
		mt = "close";
		tip = '�ر�';
	}	
	/*var af = ns==1?true:false;
	var wt = ns==1?3:10;
	var mt = ns==1?'open':'close';
	var tip= ns==1?'����':'�ر�';*/
	var opt = {
    		 method: 'get',
    		 onSuccess: function(t){
				if(t.responseText == 'exit'){
					window.parent.autoack=false;
					window.parent.Alert('��ս��ʱ�������Զ���');
					$('jntool').style.display='none';
					return false;
				}
				if(parseInt(t.responseText)>0)
				{ 	
					window.parent.autoack=af;
					window.parent.waittime=wt;
					closetl();
					Usejn(window.parent.usejn);
					alert(tip+'�Զ�ս���ɹ���ʣ���Զ�ս��������'+t.responseText+' �Σ�');
					//window.location.reload();
					window.setTimeout('window.location.reload()',1000);
					//window.parent.waittime=fttime;
				}
				else {
					if(ns==1) {closetl();window.parent.Alert('�����Զ�ս��ʧ��,ʣ�����Ϊ��0');}
					else if(ns==2) {closetl();window.parent.Alert('ʣ�����Ϊ��0, �ر��Զ�ս��!');}
					window.parent.autoack=false;
					//window.location.reload();
					window.setTimeout('window.location.reload()',1000);
					//window.parent.waittime=fttime;
				}
				 $('jntool').style.display='none';
    		 },
    		 on404: function(t) {
    		 },
    		 onFailure: function(t) {
    		 },
    		 asynchronous:true        
		}
	var ajax=new Ajax.Request('../function/ext_Fight.php?op='+ns, opt);
}

/*function getTeamFightGate()
{
	getAckOfBB(0);
}*/

function getWx($n)
{
	$n=$n+0;
	var $str="";
	switch($n){
		case 1: $str = '��';break;
		case 2: $str = 'ľ';break;
		case 3: $str = 'ˮ';break;
		case 4: $str = '��';break;
		case 5: $str = '��';break;
		case 6: $str = '��';break;
	}
	return $str;
}


function getTeamFightMod()//team.js����Ҳ��һ��
{
	if(teamLeader==parent.myUid){
		window.location='/function/Fight_Mod.php?';
	}else{
		window.location='/function/Fight_Mod.php?pz=5&team_auto='+teamautofight;	
	}
}

function getTeamFightGate(data)
{
	//parent.Alert(data)
	$('timev').innerHTML='PK';
	try{window.clearTimeout(readH);}catch(e){}
	if(parent.$('gw').contentWindow.window.location.href.indexOf('Fight_Mod.php')<0)
	{		
		eval('parent.window.setTimeout("$(\'gw\').splits(\''+data+'\');",3000);');
		window.location="/function/Fight_Mod.php?rd="+Math.random();
	}else{
		splits(data);
		$('timev').innerHTML='PK';
	}
}

var leaderCheckCloseIeT=false;
function leaderCheckCloseIe()
{
	if(leaderCheckCloseIeT) window.clearTimeout(leaderCheckCloseIeT);
	if(typeof(intfbFlag)=='undefined'||!intfbFlag){
		var waittimeout=20000;
	}else{
		var waittimeout=50000;
	}
	if(
	   	teamLeader==parent.myUid		
	)
	{
		leaderCheckCloseIeT=setTimeout('refight();window.parent.recvMsg("CT|<font color=\'669933\'>�ж�Ա������Ӧʱ�����,ϵͳΪ�����¿�ʼս��!</font>")',waittimeout);	
	}
}

if(teamLeader==parent.myUid)
{
	leaderCheckCloseIe();
}

function refight(){
	window.location='/function/Fight_Mod.php?pz=5&p='+petsid+'&auto=2&rd='+Math.random()+'&team_auto='+(window.parent.autoack?1:0)
}