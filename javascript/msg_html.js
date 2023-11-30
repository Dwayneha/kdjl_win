var O = function(m){return String.fromCharCode(Math.floor(m/10000)/99);} 
/**
@Recive remote data.
@Usage: Recive remote server data and view.
@Notice: Will update action.
**/
function messageRecive(msg){
	this.svrMsg = msg;
	this.chat=new Chat();
	//hn,n,x,y,d,s
	this.msgSplit=function(){
		if(this.svrMsg=='' || typeof this.svrMsg=='undefined') return;
		if(this.svrMsg.toString().indexOf('#loudspeak#')>0){
			var lounds = this.svrMsg.toString().split('#loudspeak#');
			this.svrMsg = lounds[0];
			lounds = lounds[1];
			lounds = lounds.split('#`#');
			if(lounds.length>1||(lounds.length==1&&lounds[0].length>0)){
				loudSpeaksMsg = {'99999999999':lounds[lounds.length-1]};
			}else{
				loudSpeaksMsg = {};
			}			
		}
		var ar = this.svrMsg.toString().split('#msg#');
		//save chat msg.
		if(ar[1]!='' && ar[1]!='undefined') this.chat.msgStr=ar[1];
		this.chat.displayMsg();
		
		//#team##word# ==> ar[0];
		var tm=ar[0].split('#word#');
		if(tm[1]!='' && tm[1]!='undefined') this.displayPlayer(tm[1]);
		var lt=tm[0].split('#team#');
		if(lt[0]!=0) {
			var MSG1 = new CLASS_GAME_MESSAGE("Team",200,120,500,"�ڴ�������Ϸ��Ϣ��ʾ��",lt[0]+"���������!",
				"���Ѿ����뵽�����У�"); 
			MSG1.rect(null,null,null,screen.height-50); 
//			MSG1.speed = 1; 
//			MSG1.step = 1; 
			MSG1.show(); 
			//Update user status.������ʾ��ҵ����ҳ���С�
			addPlayer();

		} // team
		if(lt[1]!='' && lt[1]!='undefined') adwords(lt[1]);	
	}
	
	this.displayPlayer=function(msg){
		if(msg == 1)
		{
			var opt = {
				 method: 'get',
				 onSuccess: function(t) {
				 },
				 on404: function(t) {
				 },
				 onFailure: function(t) {
				 },
				 asynchronous:true        
			}
			//var ajax=new Ajax.Request('./function/exit.php', opt);
		}else if (adtimes==false && msg.length>2)
		{
			if (parseInt(msg)==0) return false;
		    // Tips ad info.
			var ar = msg.split('#');
			var par='';
			adurl=ar[1];
			
			if (ar[0].indexOf('.jpg')!=-1 || ar[0].indexOf('.gif')!=-1)
			{
				par="<img src="+IMAGE_SRC_URL+"/ad/"+ar[0]+" border=0 onclick='parent.popad();' style='cursor:pointer;'>";//img
			}
			else{par="<span onclick='parent.popad();' style='cursor:pointer;'>"+ar[0]+"</span>";}
			
			popFlag=false; // recovery pop event
			var MSG1 = new CLASS_GAME_MESSAGE("Team",280,195,2000,'<b>�ڴ������ϵͳ��Ϣ</b>',
				                              par,
				                              "&nbsp;"
											 ); 
			MSG1.rect(null,null,null,screen.height-50); 
//			MSG1.speed = 1; 
//			MSG1.step = 1; 
			MSG1.show(); 
			adtimes=true;
		}else if(msg==2)
		{adtimes=false;}
	}

} //end class.

function popad()
{
	if (popFlag==true)
	{
		return;
	}
	var purl=adurl;
	var w=760;
	var h=480;

	var adPopup = window.open('about:blank', '_blank');//,'width='+w+',height='+h+', ...');
	
	if (adPopup==null)
	{
		Event.observe(document.body,'click',popad.bindAsEventListener(adPopup));
	}
	else
	{
		adPopup.blur();
		adPopup.opener.focus();
		adPopup.location = purl;
		popFlag=true;
		Event.stopObserving(document.body,'click');
	}
}

/**
*@ Chat Class
*/
function msgtips(msg){
	ymPrompt.alert({title:'�ڴ�����ϵͳ��Ϣ',message:msg,fixPosition:true,winPos:'rb',showMask:false,width:260,height:180})
}

var swfmsgarr = new Array();
var friendinarr = new Array();
var friendoutarr = new Array();
var last_chat_time = 0;
function Chat(){
    if($('cmdiv')!=undefined){
        $('cmdiv').style.display='none';
    }
	this.msgStr='';
	this.oldmsg='';
	this.msgArr=new Array();
	
	this.sendMsg=function(){	//	send message.,in here add ajax to server connect code.
		var timestamp = (new Date()).getTime();
		var dtime = timestamp-last_chat_time;
		if(dtime<2000){
			recvMsg("SM|��Ǹ,���Ե�("+(2000-dtime)+"ms)!");
			return;
		}
		last_chat_time=timestamp;

		var sndMsg = $('cmsg').value;
		// var myReg = /(\<+)|(\>+)|(\#+)|(\$+)|(\^+)|(\&+)|(\'+)|(\"+)|(\s{3,})|(\]\[{1,})|(\)\({1,})|(ܳ)|(��)|(��)|(��)|(Ի)|(��)|(��)|()|(���)|()|()|(ү)|(ë)|(��)|(��)|(��)|(ʺ)|(��)|(��)|(ɵ)|(������)|(�ƣգã�)|(�����)|(�ӣȣɣ�)|(fuck)|(FUCK)|(shit)|(SHIT)|(\.+)|(\,+)|(\;+)|(\\n+)|(\\r+)|(\\s+)|(\\t+)|(\{+)|(\\0+)|(\}+)/g;
		var myReg = /(\<+)|(\>+)|(\#+)|(\$+)|(\^+)|(\&+)|(\'+)|(\"+)|(\s{3,})|(\]\[{1,})|(ܳ)|(��)|(��)|(��)|(Ի)|(��)|(��)|()|(���)|()|()|(ү)|(ë)|(��)|(��)|(��)|(ʺ)|(��)|(��)|(ɵ)|(������)|(�ƣգã�)|(�����)|(�ӣȣɣ�)|(fuck)|(FUCK)|(shit)|(SHIT)|(\.+)|(\,+)|(\;+)|(\\n+)|(\\r+)|(\\s+)|(\\t+)|(\{+)|(\\0+)|(\}+)/g;
	//	sndMsg = sndMsg.replace(myReg,"*");
		var node = sndMsg.split("%");
		var newSndMsg = "";
		for(var i = 0;i < node.length;i++)
		{
			newSndMsg += node[i].replace("%",'');
		}
		if(newSndMsg=='' || newSndMsg=='undefined') {Alert('�������ݱ���������ܷ����ޣ�');
			return false;
		};
		if(newSndMsg.length > 30) {
			newSndMsg = newSndMsg.substr(0,30);
		};
		
		var opt = {
    		 method: 'get',
    		 onSuccess: function(t) {
				 
    		 },
    		 on404: function(t) {
    		 },
    		 onFailure: function(t) {
    		 },
    		 asynchronous:true
		}
		if(newSndMsg.indexOf("//")==0){
			setChatType(1);
			sc(1);
			if(parseInt(newSndMsg.substr(2,1))>0&&newSndMsg.indexOf(" ")>0){
				newSndMsg = newSndMsg.substr(0,newSndMsg.indexOf(" ")+41);
			}else{
				newSndMsg = newSndMsg.substr(0,42);
			}
		}
		try{
			talk(newSndMsg);
		}catch(e){}
	
	//	var ajax=new Ajax.Request('./function/chatGate.php?msg='+newSndMsg.replace('#!',''), opt);
		$('cmsg').value='';
		//$('buttons').value = '�����С�';
		//$('buttons').disabled= true;
		
	}
	
	
	this.displayMsg=function(){
		if(this.msgStr.length==this.oldmsg.length) return;
		//alert(this.msgStr);linend
	    $('chatDiv').innerHTML = this.msgStr.toString().replace(/linendlinend/ig,"<BR>").replace(/linend/ig,"<BR>");
		$('chatDiv').scrollTop = parseInt($('chatDiv').scrollHeight)<400?400:$('chatDiv').scrollHeight;
		this.oldmsg=this.msgStr;
	}
}

function removech(){
	//setTimeout("document.body.removeChild('"+divid+"')",10000);
	document.body.removeChild($('swfmsgs'));
	//$('swfmsgs').style.display="none";
}

function swfshow(swfm){
			var swfmsg = document.createElement('DIV');
			swfmsg.style.cssText='position:absolute;left:340px;top:392px;z-index:100000;width:320px;height:150px;border:none;background-color:transparent;';
			swfmsg.align = "center";
			swfmsg.id='swfmsgs';
			document.body.appendChild(swfmsg);
			var msgarr = swfm.split('%');
			var swftype =  msgarr[1].split('.');
			var swfstr = '';
			if(swftype[1] == 'swf'){
				swfstr='<object classid="clsid:D27CDB6E-AE6D-11cf-96B8-444553540000" codebase="http://download.macromedia.com/pub/shockwave/cabs/flash/swflash.cab#version=7,0,19,0" width="320" height="150"><param name="movie" value="../images/ui/swfmotion/'+msgarr[1]+'"><param name="quality" value="high"><param name="wmode" value="transparent"><embed src="../images/ui/swfmotion/'+msgarr[1]+'" quality="high" pluginspage="http://www.macromedia.com/go/getflashplayer" type="application/x-shockwave-flash" width="320" height="150" wmode="transparent"></embed></object>';
			}else{
				swfstr=	'<img src="../images/ui/swfmotion/'+msgarr[1]+'"  align="middle"/>';
			}
			$('swfmsgs').innerHTML=swfstr;
			setTimeout("removech()",10000);
}

// init chat part.
var chatH=new Chat();
var adtimes=false;
var popFlag=false;
var adurl=false;
/**
@Usage: add player info to team gui.
*/
function addPlayer()
{
	var opt = {
    		 method: 'get',
    		 onSuccess: function(t) {
				 var curObj=$('gw').src;
				 if(curObj.toString().indexOf('function/Team_Mod.php')!=-1)
				 {
					var dd = document.all('gw').contentWindow.document;
					dd.getElementById('tmember').innerHTML=t.responseText;
				 }
				 else alert('��Ҳ��ڶ�Ӧ��ͼ��');
    		 },
    		 on404: function(t) {
    		 },
    		 onFailure: function(t) {
    		 },
    		 asynchronous:true        
		}
	var ajax=new Ajax.Request('./function/getTeaminfo.php', opt);
}

function loudSpeakVar(msg){
	loudSpeaksMsg = msg
}

var smi=null;
function recvMsg(msg1)
{
	if(msg1 == ''){
		return;
	}
	console.log(msg1);
	msg1=msg1.replace(new RegExp("http://gimages.webgame.com.cn/poke/","gm"),"");
	var type = msg1.substring(0,2);
	//ȥ���û�������뿪����Ϣ
	if(type == 'UL')
	{
		updateForBOl(msg1.substring(3),0);
		msg1='SI|<font color="#c0c0c0"> '+msg1.substring(3)+' �뿪����Ϸ��</font>';
		
		type='SI';
	}else if(type == 'UA'){
		updateForBOl(msg1.substring(3),1);
		msg1='SI|<font color="#006600"> <span style="cursor:pointer;font-weight:bold" onclick="$(\'cmsg\').value=\'//\'+this.innerHTML+\' \';$(\'cmsg\').focus()">'+msg1.substring(3)+'</span> ��������Ϸ��</font>';
		
		type='SI';
	}
	else if(type == 'SM')
	{
		document.getElementById('sysstatmsgs').style.display='block';
		document.getElementById('sysstatmsgs').innerHTML = msg1.substring(3);
		window.clearTimeout(smi);
		smi=setTimeout("document.getElementById('sysstatmsgs').style.display='none';",5000);
		return;
	}
	
	//Ƶ��

	
	//$('swfdbg').value =msg1+"\r\n"+$('swfdbg').value;
	
	//�����ʾ���������������
	if(msginfoList.length > 50){
		var c = msginfoList.shift();
	}
	if(ggInfoList.length > 25){
		var c = ggInfoList.shift();
	}
	if(checkGG(msg1))ggInfoList.push(msg1)
    else msginfoList.push(msg1);
	showMsg1();
}
function checkGG(str){
	for(var j = 0;j < ggStr.length; j++){
		if(str.indexOf(ggStr[j])!=-1)return true;
	}
	return false;
}
var ggDataStr=[];
var ggInfoList=[];//ר�ŷŹ���
var msginfoList=[];//ר�ŷ���ͨ��Ϣ
var ggStr=["SYSI","AN","SYS","SI"];//����ָ�д��������Ķ�����빫������
var showMode=1;//0Ϊ�������Ϣ��ʾ��һ��1Ϊ����������Ϣ���£�2Ϊ����������Ϣ����
var boxHtml = "<div onselectstart='return false;' id='chatXT' style='overflow: hidden;position: absolute;width: 606px;left: 41px;top: 405px;padding:11px;height:22px;cursor:pointer;background:url(../new_images/ui/heise_25.png?1)'><div style='width: 19px;position:absolute;font-weight: bold;text-indent: 6px;height: 44px;line-height: 44px;margin-top: -10px;margin-left: -7px;z-index: 100;' onclick='qhshowMode()' id='qhmode' title='����ر��������ģʽ'><</div><div  title='����л�ģʽ' onclick='_showMode()'  id='showMsgBox' style='width: 578px; height: 29px; overflow: hidden; margin: auto; display: block;'></div></div>"
function showMsg1(){
    loadPage();
    if(showMode!=0){
        if($j("#chatXT").length==0){
            $j("body").append(boxHtml);
            $j(".chat_cont").css("top","80px");
            $j(".chat_cont").css("height","117px");
        }
    }else{
        $j(".chat_cont").css("top","47px");
        $j(".chat_cont").css("height","145px");
    }

	var topData1 = [];
	var downData1 = [];
	var allData1 = [];

	if(showMode==0){
		downData1 = downData1.concat(ggInfoList);
		downData1 = downData1.concat(msginfoList);
	}else if(showMode==1){
		downData1 = downData1.concat(msginfoList);
		topData1 = topData1.concat(ggInfoList);
	}else if(showMode==2){
		topData1 = topData1.concat(msginfoList);
		downData1 = downData1.concat(ggInfoList);
	}
	var len = downData1.length;
	if(talkType != ''){
		var newData2 = [];
		for(var j = 0;j < len; j++){
			var tt = downData1[j].substring(0,2);
			if(tt == talkType){
				if(showMode == 0){
				    newData2.push(downData1[j]);
				}
			}
		}
	
		if(newData2.length > 0){
			downData1 = newData2;
		}else{
			$('chatDiv').innerHTML = '';
			return;
		}		
	}
	len = downData1.length;
	var newStr = '';
	for(var i = 0;i < len;i++){
		if(typeof(downData1[i]) == "undefined"){
			continue;
		}
		newStr += downData1[i].substring(3)+'<br />';
	}
	len = topData1.length;
	var newStr1 = '';
	for(var i = 0;i < len;i++){
		if(typeof(topData1[i]) == "undefined"){
			continue;
		}
		newStr1 += "<span class='topInfo'>"+topData1[i].substring(3)+'</span><br />';
	}
	$('chatDiv').innerHTML = newStr;
	//�������ڵײ�
	if(daodi)$('chatDiv').scrollTop = parseInt($('chatDiv').scrollHeight)<400?400:$('chatDiv').scrollHeight;
	if(len>0){
	    $('showMsgBox').innerHTML = newStr1;
    	//�������ڵײ�
    	$('showMsgBox').scrollTop = parseInt($('showMsgBox').scrollHeight)<400?400:$('showMsgBox').scrollHeight;
    	$j('#showMsgBox .topInfo:eq(-2)').css("opacity","0.5")
	}else{
	    $('showMsgBox').innerHTML = "";
	}
}


function qhshowMode(){
    if($j("#qhmode").html()=="&lt;"){
        $j("#chatXT").css("width","0px");
        $j("#chatXT").css("margin-left","-3px");
        $j(".chat_cont").css("top","47px");
        $j(".chat_cont").css("height","145px");
        $j("#showMsgBox").hide();
        $j("#qhmode").html("&gt;");
        $j("#qhmode").attr("title","������������ģʽ");
    }else{
        $j("#chatXT").css("width","606px");
        $j("#chatXT").css("margin-left","none");
        $j("#showMsgBox").show();
        $j(".chat_cont").css("top","80px");
        $j(".chat_cont").css("height","117px");
        $j("#qhmode").html("&lt;");
        $j("#qhmode").attr("title","����ر��������ģʽ");
    }
}
function _showMode(){
    if(showMode==1)showMode=2;
    else showMode=1;
    showMsg1();
}
function setShowMode(mode){
    showMode=mode;
  
    showMsg1();
}
var talkType = "";

function showSpecialMsg(str){
	talkType = str;
	showMsg1();
	$('chatDiv').scrollTop = parseInt($('chatDiv').scrollHeight)<400?400:$('chatDiv').scrollHeight;
}


function loudSpeakVar(msg){
	loudSpeaksMsg = msg
}
var daodi = true;
var t1 = false;
function loadPage(){
    if(t1)return;
    t1=true;
     $j("#chatDiv").scroll(function () {
        viewH = $j(this).height(),//�ɼ��߶�
        contentH = $j(this).get(0).scrollHeight,//���ݸ߶�
        scrollTop = $j(this).scrollTop();//�����߶�
        //if(contentH - viewH - scrollTop <= 100) { //����ײ�100pxʱ,����������
        if (scrollTop / (contentH - viewH) >= 0.95) { //����ײ�100pxʱ,����������
            daodi = true;
        } else {
            daodi = false;
        }
    });
    
}
