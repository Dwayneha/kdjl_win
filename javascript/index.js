document.write("<script language=javascript src='/config/client.js'></script>");
// JavaScript Document
var def1=['zdh05-0.gif','zdh06-0.gif','zdh07-0.gif','zdh08-0.gif'];
var sel1=['zdh05.gif','zdh06.gif','zdh07.gif','zdh08.gif'];
var autoack=false;
var waittime=10;
var usejn=1;
var test=0;
var settime;
var settimeout;
/**task config*/
var help_desc='<br/>���ڴ����顷�Ǹ�����ȡ�ڴ�����ϵ����Ϸ�ľ������иı�ĳ�����������ҳ��Ϸ,��������,��ʹ���ϰ��ʱ��,��ֻҪ����ҳ���ܺ��Լ��İ��ĳ������Ķȹ�һ�죡.<br/>'
			 +'��Ϸ��ɫ��<br/>'
			 +'* ���ٿ�ɰ�����,���㰮�ò�����<br/>'
			 +'* ���˼�ʱ���,����һ��ȥð��<br/>'
			 +'* ��ս����̨����ı���һ������<br/>';
var help_city='<br/>ͨ�����ҳ�����ϵ�<font color=green>���ĳ���</font>����������ڴ�����ĳ���'
              +'�ڳ����У������������̵��ϰ�İ������󣬿��Թ�����Ʒ�ȵȣ�';
var help_shop='<br/>�������ĳ�����и����̵꣬������<br/>'
              +'���µ�<font color=green>����</font>�����Դ桢ȡ������ĳ��<br/>'
			  +'���ϵ�<font color=green>���ߵ�</font>�����Թ����ӻ�(ҩˮ,�鼮,�����).<br/>'
			  +'�м��<font color=green>�ֿ�</font>�����Դ�Ŷ������Ʒ��<br/>'
			  +'<font color=green>������</font>���������а����Ϣ��<br/>'
			  +'<font color=green>������</font>�����Թ��򵽳����װ����<br/>'
			  +'<font color=green>�����̵�</font>��������Ԫ������������Ʒ��<br/>'
			  +'<font color=green>�������</font>���ñ�������,����ĵط���!<br/>'
			  +'<font color=green>�ʹ�</font>����������ĵط���<br/>'
			  +'<font color=green>������</font>�����֮�����Ʒ����'
			  ;
var help_gpc='<br/>ѡ��ҳ�����ϵ� <font color=green>Ұ��̽��</font>,�������ڴ�����ĵ�ͼ��'
			 +'����Ƶ���ͼ�ĵ��ϻ���ʾ��ͼ���ƣ���ɫ��ʾ���δ�����õ�ͼ��'
			 +'��ɫ��ʾ�Ѿ������ĵ�ͼ��ѡ��һ����ͼ�󣬽������ս��ǰ��׼��״̬��'
			 +'���������������������ӣ���鿴������ҵ�������Ϣ�ȡ�ѡ��һ������󣬵�<font color=green>��ʼ</font>�Ϳ��Խ���ս���ˣ�';
var help_skill='<br/>����ս�����棬���Կ���������һ��ͼ�꣬���幦�����£�'
				+'<font color=green>�Զ�����</font>�����Դ򿪻�ر��Զ�ս����<br/>�������ã��ڿ�ʼ�Զ�ս��ǰ������Ҫʹ�õļ��ܡ�'
				+'<font color=green>����</font>����������<br/>���ܣ�ѡ��Ҫʹ�õļ���.<br/>'
				+'<font color=green>����</font>��ҩˮ�ȸ�����Ʒ��<br/>'
				+'<font color=green>��׽</font>���г�������еĲ�׽���߾�����ֻ�ж�Ӧ��������ܲ�׽��������<br/>'
				+'<font color=green>����</font>�������˳�ս��!';
var help_chat='<br/>����ϵͳ����������ҽ����Ĺ��ߣ�ֱ�������������������������ݣ���<font color=green>���Ͱ�ť</font>��<font color=green>�س���</font>'
			 +'���Ϳ��Է���������Ϣ�ˡ���ص��������£�<br/>'
			 +'ͨ���ڷ�����Ϣ����ǰ�棬����Ӣ��״̬��<font color=green>!</font>��<font color=green>!!</font>��<font color=green>#</font>��<font color=green>$</font>�����Է�����ͬ��ɫ�����塣<br/>'
			 +'<font color=green>/����ǳ� ˵������</font>(�ǳ�������֮���пո�)���������˽�ģ������Ļ���<br/>'
			 +'<font color=green>[����]</font>��<font color=green>(����)</font>���Է���ͼƬ����Ŷ��';
var help_task='<br/>������ܣ�<br/>'
			 +'�������ڳ����̵��е�<font color=green>�̵�</font>��<font color=green>������</font>��NPC��������ע�⿴�Ի��������ޡ�<br/>'
			 +'ͨ��ҳ�����Ͻǵ�<font color=green>����</font>ͼ�꣬���Բ�ѯ�����µ�������������';
var help_bag='<br/>�������ܣ�<br/>'
			+'ҳ�����ϵ�<font color=green>����</font>�����Ե���򿪡�ѡ��һЩ��Ʒ����<font color=green>ʹ�ð�ť</font>�����ʹ�á�<br/>'
			+'Ŀǰ�������������װ����ʹ��һЩ���ߡ�';

function helpsys(msg)
{
	$('helptarget').innerHTML=eval('help_'+msg);
}

var bid=0;
//function $(element){return document.getElementById(element)?document.getElementById(element):element;}
//Show_Tools
var sp=Array(0,0,0,0);
var k=0;
function ShowBox(name,cursel,n)
{
	$('baginfo').style.display = 'none';
	clearInterval(settime);
	if(k==1 && sp[cursel]==1)//�ر�
	{		
			$("Box_"+name+"_"+cursel).style.display="none";
			k=0;
			sp[cursel]=0;
	}
	else//��
	{
		if(cursel=='3')
		{
			try{
				getTaskAll();
			}catch(e){
				alert(e)
			}
		}
		else if(cursel=='2')
		{
			msgflag = 0;
			change_type();
			url = 'getInfo.php';		
		}
		else
		{
			url = 'getBag.php?style=1';	
		}
		for(i=1;i<=n;i++)
		{	
			var con=$("Box_"+name+"_"+i);
			if(!con)
			{
				continue;
			}
			con.style.display=i==cursel?"block":"none";
			sp[i]=i==cursel?1:0;
		}
		k=1;
		if (url == '') return;
		if(url.indexOf('getInfo')!=-1)
		{
				var opt = {
					method: 'get',
					onSuccess: function(t) {
								$('infos').innerHTML=t.responseText;
							},
					asynchronous:true        
				}
				var ajax=new Ajax.Request('../function/'+url, opt);
		}
		if(url.indexOf('getBag')!=-1)
		{
				var opt = {
					method: 'get',
					onSuccess: function(t) {
								$('bags').innerHTML=t.responseText;
							},
					asynchronous:true        
				}
				var ajax=new Ajax.Request('../function/'+url, opt);
		}

	}
}


function getTaskAll(){

		var taskall_bot=$('Box_Tools_3');
		var taskall_list=$('task_every_list');
			taskall_bot.style.display='block';
			taskall_list.style.display='block';
			$('task_every_list').innerHTML=''
			
		
		var opt = {
    		 	method: 'get',
				 onSuccess: function(t) {
					//window.parent.Alert(t.responseText);
					var str = t.responseText;
					var arr = str.split('@@@@');
					$('task_title_list').innerHTML= arr[0];
					//$('task_every_list').innerHTML= arr[1];
					$('activity_show').innerHTML= arr[1];
					Getdate();
    		 	},
    		 	on404: function(t) {
    		 	},
    		 	onFailure: function(t) {
    		 	},
    		 	asynchronous:true        
			}
		var ajax=new Ajax.Request('./function/taskshow.php?title_vary=1', opt);
}

var taskcache={}
var getTaskDetailFlag=false;
function getTaskDetail(bid){
	$('con_task_'+bid).innerHTML= '�����С���';
	getTaskDetailFlag=true;
	if(typeof(taskcache[bid])!='undefined')
	{
		$('con_task_'+bid).innerHTML= taskcache[bid];
		return;
	}
	if(bid!=2)
	{
		url = './function/taskshow.php?title_vary=2&bid='+bid+'&rd='+Math.random();
	}
	else
	{
		url = './function/taskshow.php?title_vary=3&bid='+bid+'&rd='+Math.random();
	}
	var opt = {
			method: 'get',
			 onSuccess: function(t) {
				 getTaskDetailFlag=false;
				//window.parent.Alert(t.responseText);
				taskcache[bid]=t.responseText;
				var str = t.responseText;
				//alert(str);
				$('con_task_'+bid).innerHTML= str;
			},
			on404: function(t) {
			},
			onFailure: function(t) {
			},
			asynchronous:true        
		}
	var ajax=new Ajax.Request(url, opt);
}


var title=0;
var times=0;
function showcontent_ac(title,times,id,ev){
		var time=$('activity_show_every');
		if(time.style.display=='none'){
			time.style.display='block';
					
		}else{
			time.style.display='none';
			
		}
	if(ev) Event=ev;
	$('activity_show_every').innerHTML = title+times;
	if(!document.all)//FF����� ie ����absolute ������ڸ�����  ��FF �������Զ����������ڵ�
	{
		x=Event['layerX'] + 380;
		y=Event['layerY'] + 130;
	}
	else
	{
		x=Event['clientX'] +5;
		y=Event['clientY'] +5;
	}
	//recvMsg("CT|"+x+","+y);
	document.getElementById('activity_show_every').style.left=x+"px";
	document.getElementById('activity_show_every').style.top=y+"px";

	
}

function closecontent(){
		var time=$('activity_show_every');
		if(time.style.display=='none'){
			time.style.display='block';
		}else{
			time.style.display='none';
		}
}



function ImgChg(obj1,id1, n1)
{
// n=1: sel 0:def
	var img1;
	if (n1==0) img1=def1[id1];
	else img1=sel1[id1];
	obj1.src=''+IMAGE_SRC_URL+'/ui/main/'+img1;
}
function Load(n)
{	
	var mod = '';
	switch(n)
	{
		case 1: mod='Expore';break;//Ұ��̽��
		case 2: mod='City';break;//city
		case 3: mod='Pets';break;// pets info
		case 4: mod='User';break;// player info.
		default:mod='Welcome';
	}
	try{
		setTimeout('UnTip()',3000);
	}catch(e){}
	document.getElementById('gw').src='./function/'+ mod +'_Mod.php';	
}

var str = "**************��ӭ������ڴ�������������� �ı�������ɫ��!��Ӣ��̾�ţ���ɫ���壬!!��ɫ���塣˽�����/����ǳ� ˵�����ݣ��ǳ�������֮���пո�[����]��(����)���Է������顣**************";
var strlen = str.length;
var inc = 0;
var show = "";

function time()
{
  inc = (inc + 1) % strlen;
  show = str.substr(inc, strlen - inc);
  if (inc > 0)
  {
    show += str.substr(0, inc - 1);
    window.status = show;
  }
}
//window.setInterval("time();", 500);
//
	var s=15;
	var minheight=0;
	var maxheight=355;
	var cur='';
	var bak='';
	var as =0;
	
function HelpMenu(){
	$("helpcmd1").style.display='none';
	$("helpcmd2").style.display='none';
    $("helpcmd3").style.display='none'; //added by Zheng.Ping
	if (bak!='' && bak!=cur) {cur.id='close';as=1;}
	var hd = bak=cur;
	var content=$('helpwin');
    var key = hd.id;
    if(key=="open"){
          //content.style.pixelHeight+=s; // commented by Zheng.Ping
          content.style.pixelHeight = maxheight; /* added by Zheng.Ping */
		  content.style.display='';
          content.style.overflow='visible'; // added by Zheng.Ping
          if (typeof(gamewindow.hiddenContent) == 'function') {	  
              gamewindow.hiddenContent();
          }
         
          if(content.style.pixelHeight<maxheight)
		  {			   
            window.setTimeout("HelpMenu('"+((arguments.length==1)?arguments[0]:'')+"');",1);
           }else {			    
		   		hd.id='close';
				if(arguments.length==1){
					ajaxfun($('helpwincet'), arguments[0]);
				}else{
					ajaxfun($('helpwincet'), hd.name);
				}
		   }
        }else{ 
                //content.style.pixelHeight-=s; //commented by Zheng.Ping
                content.style.pixelHeight=minheight; /* added by Zheng.Ping */
                if (typeof(gamewindow.showContent) == 'function') {
                    gamewindow.showContent();
                }
                
                if(content.style.pixelHeight>minheight){
                        window.setTimeout("HelpMenu('"+((arguments.length==1)?arguments[0]:'')+"');",1);
                }else{
                        hd.id='open';
						content.style.display='none';
						if(arguments.length==1){
							if(as==1){as=0;HelpMenu(arguments[0]);}
						}else{
							if(as==1){as=0;HelpMenu();}
						}
						$('helpwincet').innerHTML='';
                }
        }
}

function ajaxfun(obj,name)
{
	var url = '';
	if (name == 'bag') url = 'getBag.php?style=1';
	else if(name == 'task') url = 'getTaskItem.php';
	else if (name== 'pets'){
		/*$('showmybagusedcells').innerHTML='';
		var nm=$("helpcmd");
		nm.value='�ر�';
		nm.onclick=function(){cur.id='close';HelpMenu();}*/
		url = 'getInfo.php';
	}
	if (url == '') return;
	var opt = {
     	method: 'get',
		onSuccess: function(t) {
			 		obj.innerHTML=t.responseText;
					//alert(t.responseText);
					if(name == 'task')
					{	
						$('showmybagusedcells').innerHTML='';
						var nm=$("helpcmd");
						nm.value='�ر�';
						nm.onclick=function(){cur.id='close';HelpMenu();}
					}else if(name == 'pets'){
						$('showmybagusedcells').innerHTML='';
						var nm=$("helpcmd");
						nm.value='�ر�';
						nm.onclick=function(){cur.id='close';HelpMenu();}
					}
					else if (name== 'bag')
					{
						var nm=$("helpcmd");
						nm.value='ʹ��';
						nm.onclick=Used;
						var nm1=$('helpcmd1');
						nm1.style.display='';
						nm1.onclick=function(){cur.id='close';nm1.style.display='none';HelpMenu();};
						var nm1=$('helpcmd2');
						nm1.style.display='';
						nm1.value='����ֿ�';
						//nm1.onclick=Reset; // commented by Zheng.Ping
                        nm1.onclick=putBagProps2Depot; //added by Zheng.Ping
                        var nm3=$('helpcmd3');
						nm3.style.display='';
                        nm3.onclick = dropBagProps;
						$('showmybagusedcells').innerHTML='��ǰ�����ռ�: ' + $('mybagcelluse').value;
					}
					
    		 	},
     	asynchronous:true        
	}
	var ajax=new Ajax.Request('../function/'+url, opt);
}

/*function ajaxfun(obj,name)
{
	var url = '';
	if (name == 'bag') url = 'getBag.php?style=1';
	else if(name == 'task') url = 'getTaskItem.php';
	else if (name== 'pets')
					{
						$('showmybagusedcells').innerHTML='';
						var nm=$("helpcmd");
						nm.value='�ر�';
						nm.onclick=function(){cur.id='close';HelpMenu();}
					}
	if (url == '') return;
	var opt = {
     	method: 'get',
		onSuccess: function(t) {
			 		obj.innerHTML=t.responseText;
					//alert(t.responseText);
					if(name == 'task')
					{	
						$('showmybagusedcells').innerHTML='';
						var nm=$("helpcmd");
						nm.value='�ر�';
						nm.onclick=function(){cur.id='close';HelpMenu();}
					}
					else if (name== 'bag')
					{
						var nm=$("helpcmd");
						nm.value='ʹ��';
						nm.onclick=Used;
						var nm1=$('helpcmd1');
						nm1.style.display='';
						nm1.onclick=function(){cur.id='close';nm1.style.display='none';HelpMenu();};
						var nm1=$('helpcmd2');
						nm1.style.display='';
						nm1.value='����ֿ�';
						//nm1.onclick=Reset; // commented by Zheng.Ping
                        nm1.onclick=putBagProps2Depot; //added by Zheng.Ping
                        var nm3=$('helpcmd3');
						nm3.style.display='';
                        nm3.onclick = dropBagProps;
						$('showmybagusedcells').innerHTML='��ǰ�����ռ�: ' + $('mybagcelluse').value;
					}
					
    		 	},
     	asynchronous:true        
	}
	var ajax=new Ajax.Request('../function/'+url, opt);
}*/
function empty(){}
var selid=0;
//-----
function vtips(obj,id,bid,sign,type)
{
	showTip(id,bid,sign,type);
	obj.style.border='solid 1px #DFD496';
}
function ctips(obj)
{
	obj.style.border='0px';
	UnTip();
}
function sel(obj)
{
	if(selid!=0) selid.style.backgroundColor='#F1F7DC';
	selid=obj;
	obj.style.backgroundColor='#DFD496';
}
var lastusetime = 0;
function Used()
{
	document.getElementById('incangku').onclick = '';
	if(typeof(bid)=='undefined')
	{
		alert('��ѡ����Ҫʹ�õ���Ʒ');
		return;
	}
	/*now = (new Date()).getTime();
	if(now-lastusetime<1000){		
		return;
	}
	lastusetime = now;*/
	var opt = {
     	method: 'get',
		onSuccess: function(t) {
			 		if(t.responseText!='')
					{
						Alert(t.responseText);
						document.getElementById('incangku').onclick = function()
						{
							putBagProps2Depot();
						}
					}
    		 	},
     	asynchronous:true 
	}
	var ajax=new Ajax.Request('../function/usedProps.php?id='+bid, opt);	
}

function Reset()
{
	if(!confirm('�������������ʾ�����ڰ����е���Ʒ����Ԥ���㹻�Ŀռ䣬��ȷ��������')) return;
	
	var opt = {
     	method: 'get',
		onSuccess: function(t) {
			 		if(t.responseText!='')
					{
						Alert(t.responseText);
					}
    		 	},
     	asynchronous:true        
	}
	var ajax=new Ajax.Request('../function/usedProps.php?op=reset', opt);	
}

function putBagProps2Depot() {
    var opt = {
        method: 'get',
        onSuccess: function(t) {
            var ret = parseInt(t.responseText);

            if (ret == 0) {
                if ($('t' + bid)) {
                    var propsRow   = $('t' + bid).parentNode;
                    var propsTable = propsRow.parentNode;
                    propsTable.removeChild(propsRow);
                    bid = '';
                }
                window.parent.Alert('�Ѿ�����ֿ�!');
            } else if(ret == 2) {
                alert('�ֿ�����������ֿ�ʧ��!');
            } else if (ret == 3) {
                alert('������û�иõ��ߣ�����ֿ�ʧ��!');
            } else if (ret == 4) {
                window.parent.Alert('���Ժ����!');
            }
        },
        asynchronous:true        
    }
    var ajax=new Ajax.Request('./function/props2Depot.php?act=move&id='+bid, opt);
}

function dropBagProps()
{
    if (!parseInt(bid)) {
        return;
    }
    if (!confirm('ɾ����Ӧ���ߺ��ָܻ�����ȷ���Ƿ�Ҫɾ��!')) {
        return;
    }

    var opt = {
        method: 'get',
        onSuccess: function(t) {
            var ret = parseInt(t.responseText);

            if (ret == 0) {
                if ($('t' + bid)) {
                    var propsRow   = $('t' + bid).parentNode;
                    var propsTable = propsRow.parentNode;
                    propsTable.removeChild(propsRow);
                    bid = '';
                }
                window.parent.Alert('�����ɹ�!');
            } else if (ret == 3) {
                alert('������û�иõ��ߣ�����ʧ��!');
            } else if (ret == 4) {
                window.parent.Alert('���Ժ����!');
            }else if (ret == 100) {
                window.parent.Alert('����Ʒ�Ѿ����������ܶ���!');
            }
        },
        asynchronous:true        
    }
    var ajax=new Ajax.Request('./function/props2Depot.php?act=drop&id='+bid, opt);
}

function Alert(msg)
{
	if(msg.length==0) return;
	$('systips').style.display='block';
	$('systips').innerHTML=msg;
	window.setTimeout('unAlert()', 10000);
	/*Tip(msg, BALLOONSTEMOFFSET, true, OFFSETX, -17, BALLOON, true, FADEIN, 400, FADEOUT, 400);
	window.setTimeout('UnTip()',2000);
	*/
}
function unAlert()
{
	$('systips').innerHTML='';
	$('systips').style.display='none';
}
function onlineCount()
{
	var opt = {
     	method: 'get',
		onSuccess: function(t) {
			 		$('onlinec').innerHTML=t.responseText;
					if(t.responseText.indexOf('<!--consumption2exp-->')!=-1)
					{
						try{
							$('onlinec').parentNode.removeChild($('consumption2expdom'));
						}catch(e){}
						$('onlinec').parentNode.innerHTML='<span id="consumption2expdom" style="cursor:pointer;color:#aaaa00;margin-right:12px;font-size:16px" onclick="getconsumption2exp()"><img src="../new_images/qianbianlibao.gif" align="absmiddle"></span>'+$('onlinec').parentNode.innerHTML
						$('online').style.width='240px'
					}else{
						try{
							$('onlinec').parentNode.removeChild($('consumption2expdom'));
							$('online').style.width='140px'
						}catch(e){}	
					}
    		 	},
     	asynchronous:true        
	}
	var ajax=new Ajax.Request('../function/ext_Online.php', opt);
	window.setTimeout('onlineCount()', 300000);	
}
function getconsumption2exp()
{
	var opt = {
		method: 'get',
		onSuccess: function(t) {
					Alert(t.responseText);
				},
		asynchronous:true        
	}
	var ajax=new Ajax.Request('/function/consumption2exp.php', opt);
}
function adwords(msg)
{
	if(msg.length>5) $('adword').innerHTML='<marquee scrollAmount=2 width=400 loop=2>'+msg+'</marquee>';
}
onlineCount();
//adwords();

function showTips(msg,x,y)
{
	msg = '<span style="line-height:16px;over-flow:hidden; width:185px" id="tipsInfoDiv">'+msg+'</span>';
	/*Tip(msg, BGCOLOR,'#000000',
			 BORDERCOLOR,'#cccccc',
		     BORDERSTYLE,'solid',
		     BORDERWIDTH,'',
		     FONTSIZE,'75%',
		     TEXTALIGN,'left',
		     WIDTH,185,
		     FIX,[x,y],
		     OPACITY,80,
		     PADDING,5
		);
*/

	Tip(msg, BGCOLOR,'transparent',
			 BORDERCOLOR,'none',
		     BORDERSTYLE,'none',
		     BORDERWIDTH,'',
		     TEXTALIGN,'left',
		     WIDTH,185,
		     FIX,[x,y],
		     OPACITY,80,
		     PADDING,5
		);

}

//function wait() {alert('ok'):}

// added by Zheng.Ping
	function showTips2(msg,x,y)
{
	
	msg = '<span style="line-height:16px;over-flow:hidden; width:185px" id="tipsInfoDiv">'+msg+'</span>';
    x = x - parseInt($("helpwin").style.left);
    //y=y+20;
	/*Tip(msg, BGCOLOR,'#000000',
			 BORDERCOLOR,'#cccccc',
		     BORDERSTYLE,'solid',
		     BORDERWIDTH,'1',
		     FONTSIZE,'75%',
		     TEXTALIGN,'left',
		     WIDTH,185,
		     FIX,[x,y],
		     OPACITY,80,
		     PADDING,5
		);
	$("WzBoDy").style.height = $("tipsInfoDiv").scrollHeight+6+"px";
    $("WzBoDy").style.overflow = 'visible';*/
    //$('help_win_info').outerHTML = $("WzBoDy").outerHTML;
    $('help_win_info').style.position = 'absolute';
    $('help_win_info').style.borderWidth = '0';
    $('help_win_info').style.borderStyle = 'none';
    $('help_win_info').style.background = 'transparent';
    $('help_win_info').style.borderColor = '';
    $('help_win_info').style.textAlign = 'left';
    $('help_win_info').style.width = '185px';
    $('help_win_info').style.padding = '5px';
    $('help_win_info').style.left = x+'px';
    $('help_win_info').style.top = y+'px';
    $('help_win_info').innerHTML = msg;
    $('help_win_info').style.overflow = 'visible';
    $("help_win_info").style.display = '';
    $("help_win_info").style.zIndex = parseInt($("helpwin").style.zIndex) + 999;
     setTimeout("UnTip2()",20000);
}

// added by Zheng.Ping 
function UnTip2()
{
    $("help_win_info").style.overflow = 'hidden';
    $("help_win_info").style.display = 'none';
    $('help_win_info').innerHTML = '';
}
// added by Du Hao in 2009-04-21 
function showTips3(msg,x,y)
{
	document.getElementById("help_chat_info").style.left = x+"px";
    document.getElementById("help_chat_info").style.top = y+"px";
	msg = '<span style="line-height:16px;over-flow:hidden; width:185px" id="tipsInfoDiv">'+msg+'</span>';
    document.getElementById("help_chat_info").innerHTML = msg;
    document.getElementById("help_chat_info").style.overflow = 'visible';
    document.getElementById("help_chat_info").style.display = 'block';
    settimeout = setTimeout("UnTip3()",20000);
}

// added by Du Hao in 2009-04-21 
function UnTip3()
{
	clearInterval(settimeout);
    $("help_chat_info").style.overflow = 'hidden';
    $("help_chat_info").style.display = 'none';
    $('help_chat_info').innerHTML = '';
}
/**
@��Ϣ��ʾ��
*/
var equipInfos = {};
var LastKey = "";
var Event;
function showm()
{
	return;
	if(!document.getElementById('xxms'))
	{
		var a=document.createElement('div');
		a.style.cssText='position:absolute;top:1px;left:1px;z-index:999999999;border:1px solid #ff0000';
		a.id='xxms';
		document.body.appendChild(a);
	}
	if(!document.all){
		document.getElementById('xxms').innerHTML=Event['layerX']+','+Event['layerY'];
	}else{
		
		document.getElementById('xxms').innerHTML=Event['clientX']+','+Event['clientY'];
	}
}

function showTip(id,bid,sign,type)
{
	if(!document.all){
		var x=Event.x;
		var y=Event.y;
	}else{
		var x=event.x;
		var y=event.y;
	}
	var key = id+"_"+bid+"_"+sign+"_"+type;
	if(typeof(equipInfos[key])!='undefined'){
        showTips(equipInfos[key],x,y);
        return;
	}
	if(LastKey==key){
        return;
	}
	LastKey=key;//alert(LastKey);
	var opt = {
    		 	method: 'get',
				 onSuccess: function(t) {
				 	equipInfos[key] = t.responseText;
					showTips(t.responseText,x,y);
    		 	},
    		 	on404: function(t) {
    		 	},
    		 	onFailure: function(t) {
    		 	},
    		 	asynchronous:true        
			}
	var ajax=new Ajax.Request('../function/getPropsInfo.php?id='+id+'&bid='+bid+'&sign='+sign+'&type='+type, opt);
}

//����չʾ
function showBb(bid)
{
	if(!document.all){
		var x=Event.x;
		var y=Event.y;
	}else{
		var x=event.x;
		var y=event.y;
	}
	
	var opt = {
    		 	method: 'get',
				 onSuccess: function(t) {
                    var reponse = t.responseText;
					showBbTip(reponse,x,y);
    		 	},
    		 	on404: function(t) {
    		 	},
    		 	onFailure: function(t) {
    		 	},
    		 	asynchronous:true        
			}
	var ajax=new Ajax.Request('../function/bbshow.php?id='+bid, opt);
	
}
function showBbTip(msg,x,y)
{	
	msg = '<span style="line-height:16px;over-flow:hidden; width:185px" id="tipsInfoDiv">'+msg+'</span>';
 	x =parseInt($("chatDiv").style.left)+200;
    y =parseInt($("chatDiv").style.top)+30;
	$("bbshow").innerHTML = msg;
    $("bbshow").style.display = 'block';
    $("bbshow").style.zIndex = parseInt($("chatDiv").style.zIndex) + 99999;
   //setTimeout("UnTip3()",5000);
}
function UnTipbb()
{
	//$("bbshow").style.overflow = 'hidden';
    $("bbshow").style.display = 'none';
    //$('bbshow').innerHTML = '';
}

function showTipEquip(id,equip,ev)
{
	
	var x = 500;
	var y = 100;
	var key = id+"_"+equip;
	if(typeof(equipInfos[key])!='undefined'){
        showTips(equipInfos[key],x,y);
        //showTips(equipInfos[key],x,y);
        return;
	}
	if(LastKey==key){
        return;
	}
	
	LastKey=key;//alert(LastKey);
	var opt = {
    		 	method: 'get',
				 onSuccess: function(t) {
				 	equipInfos[key] = t.responseText;
					showTips(t.responseText,x,y);
    		 	},
    		 	on404: function(t) {
    		 	},
    		 	onFailure: function(t) {
    		 	},
    		 	asynchronous:true        
			}
	var ajax=new Ajax.Request('../function/getPropsInfo.php?id='+id+'&equip='+equip, opt);
}


// added by Zheng.Ping 
function showTip2(id,bid,sign,type)
{
	var key = id+"_"+bid+"_"+sign+"_"+type;
	if(typeof(equipInfos[key])!='undefined' && equipInfos[key]!='no'){
       $('baginfo').innerHTML = equipInfos[key];
		$('baginfo').style.display = 'block';
		settime = setTimeout("UnBagTip2()",20000);
        //showTips(equipInfos[key],x,y);
        return;
	}
	if(LastKey==key && equipInfos[key]!='no'){
        return;
	}
	
	LastKey=key;//alert(LastKey);
	var opt = {
    		 	method: 'get',
				 onSuccess: function(t) {
				 	equipInfos[key] = t.responseText;
                    var reponse = t.responseText;
                    //showTips2(t.responseText,x,y);
                    //setTimeout(function() {showTips2(reponse,x,y);}, 1000);
					$('baginfo').innerHTML = reponse;
					$('baginfo').style.display = 'block';
					settime = setTimeout("UnBagTip2()",20000);
    		 	},
    		 	on404: function(t) {
    		 	},
    		 	onFailure: function(t) {
    		 	},
    		 	asynchronous:true        
			}
	var ajax=new Ajax.Request('../function/getPropsInfo.php?id='+id+'&bid='+bid+'&sign='+sign+'&type='+type, opt);
}


function UnBagTip2(){
	clearInterval(settime);
	$('baginfo').style.display = 'none';
}
//added by Du Hao in 2009-04-20
function showTip3(id,bid,sign,type)
{
	var x = 200;
	var y = -50;
	var key = id+"_"+bid+"_"+sign+"_"+type;
	if(typeof(equipInfos[key])!='undefined' && equipInfos[key] != 'no'){
        showTips3(equipInfos[key],x,y);
        return;
	}
	if(LastKey==key &&  equipInfos[key] != 'no'){
        return;
	}
	LastKey=key;
	var opt = {
    		 	method: 'get',
				 onSuccess: function(t) {
				 	equipInfos[key] = t.responseText;
                    var reponse = t.responseText;
                    showTips3(reponse,x,y);
    		 	},
    		 	on404: function(t) {
    		 	},
    		 	onFailure: function(t) {
    		 	},
    		 	asynchronous:true        
			}
	var ajax=new Ajax.Request('../function/getPropsInfo.php?id='+id+'&bid='+bid+'&sign='+sign+'&type='+type, opt);
}
function showchatTip(name,obj)
{	
	var x=event.x;
	var y=event.y;
	var opt = {
    		 	method: 'get',
				 onSuccess: function(t){
					if(t.responseText !=0)
					{
						var opts = {
									method: 'get',
									 onSuccess: function(t) {
												msg = t.responseText;
												msg = '<span style=line-height:1.7>'+msg+'</span>';
												x=x-20;
												Tip(msg, BGCOLOR,'transparent',
														 BORDERCOLOR,'none',
														 BORDERSTYLE,'none',
														 BORDERWIDTH,'0',
														 FONTSIZE,'75%',
														 TEXTALIGN,'left',
														 WIDTH,185,
														 FIX,[x,y],
														 OPACITY,80,
														 PADDING,5,
														 ABOVE,true
													);
									},
									on404: function(t) {
										
									},
									onFailure: function(t) {
									
									},
									asynchronous:true        
								}
						var ajax=new Ajax.Request('../function/getPropsInfo.php?id='+t.responseText, opts);
					}
    		 	},
    		 	on404: function(t) {
    		 	},
    		 	onFailure: function(t) {
    		 	},
    		 	asynchronous:true        
			}
	var ajax=new Ajax.Request('../function/getPropsInfo.php?op='+name, opt);

}

function chatuntip()
{   try{$('dtips').style.display='none';}catch(e){};
}
/*
DEMO:var MSG = new CLASS_GAME_MESSAGE("aa",200,120,"����Ϣ��ʾ��","����1����Ϣ","�������ҳԷ���"); 
	 MSG.show(); 
* ��Ϣ���� 
*/ 
function CLASS_GAME_MESSAGE(id,width,height,timeout,caption,title,message,target,action){ 
this.id = id; 
this.title = title; 
this.caption= caption; 
this.message= message; 
this.target = target; 
this.action = action; 
this.width = width?width:200; 
this.height = height?height:120; 
this.timeout= timeout; 
this.speed = 5; 
this.step = 1; 
this.right = screen.width -1; 
this.bottom = screen.height; 
this.left = this.right - this.width; 
this.top = this.bottom - this.height; 
this.timer = 0; 
this.pause = false; 
this.close = false; 
this.autoHide = true; 
} 

/*
* Hide
*/ 
CLASS_GAME_MESSAGE.prototype.hide = function(){ 
if(this.onunload()){var offset = this.height>this.bottom-this.top?this.height:this.bottom-this.top;var me = this;
if(this.timer>0){window.clearInterval(me.timer);}var fun = function(){if(me.pause==false||me.close){var x = me.left;var y = 0;
var width = me.width;var height = 0;if(me.offset>0){height = me.offset;}y = me.bottom - height; if(y>=me.bottom){ window.clearInterval(me.timer); me.Pop.hide();} else {me.offset = me.offset - me.step;}me.Pop.show(x,y,width,height);}}
this.timer = window.setInterval(fun,this.speed)}} 


// destruct 
CLASS_GAME_MESSAGE.prototype.onunload = function() { 
return true; 
} 
// cmd
CLASS_GAME_MESSAGE.prototype.oncommand = function(){ 
//this.close = true; 
this.hide(); 
} 
//show
CLASS_GAME_MESSAGE.prototype.show = function(){ 
var oPopup = window.createPopup(); //IE5.5+ 
this.Pop = oPopup;
var w = this.width; 
var h = this.height; 
var str = "<DIV style='BORDER-RIGHT: #455690 1px solid; BORDER-TOP: #a6b4cf 1px solid; Z-INDEX: 99999; LEFT: 0px; BORDER-LEFT: #a6b4cf 1px solid; WIDTH: " + w + "px; BORDER-BOTTOM: #455690 1px solid; POSITION: absolute; TOP: 0px; HEIGHT: " + h + "px; BACKGROUND-COLOR: #c9d3f3'>" 
str += "<TABLE style='BORDER-TOP: #ffffff 1px solid; BORDER-LEFT: #ffffff 1px solid' cellSpacing=0 cellPadding=0 width='100%' bgColor=#cfdef4 border=0>" 
str += "<TR>" 
str += "<TD style='FONT-SIZE: 12px;COLOR: #0f2c8c' width=30 height=24></TD>" 
str += "<TD style='PADDING-LEFT: 4px; FONT-WEIGHT: normal; FONT-SIZE: 12px; COLOR: #1f336b; PADDING-TOP: 4px' vAlign=center width='100%'>" + this.caption + "</TD>" 
str += "<TD style='PADDING-RIGHT: 2px; PADDING-TOP: 2px' vAlign=center align=right width=19>" 
str += "<SPAN title=�ر� style='FONT-WEIGHT: bold; FONT-SIZE: 12px; CURSOR: pointer; COLOR: red; MARGIN-RIGHT: 4px' id='btSysClose' >��</SPAN></TD>" 
str += "</TR>" 
str += "<TR>" 
str += "<TD style='PADDING-RIGHT: 1px;PADDING-BOTTOM: 1px' colSpan=3 height=" + (h-28) + ">" 
str += "<DIV style='BORDER-RIGHT: #b9c9ef 1px solid; PADDING-RIGHT: 8px; BORDER-TOP: #728eb8 1px solid; PADDING-LEFT: 8px; FONT-SIZE: 12px; PADDING-BOTTOM: 8px; BORDER-LEFT: #728eb8 1px solid; WIDTH: 100%; COLOR: #1f336b; PADDING-TOP: 8px; BORDER-BOTTOM: #b9c9ef 1px solid; HEIGHT: 100%'>" + this.title + "<BR><BR>" 
str += "<DIV style='WORD-BREAK: break-all' align=left><span href='javascript:void(0)' hidefocus=false id='btCommand'><FONT color=#ff0000>" + this.message + "</FONT></span></DIV>" 
str += "</DIV>" 
str += "</TD>" 
str += "</TR>" 
str += "</TABLE>" 
str += "</DIV>" 
oPopup.document.body.innerHTML = str; 
this.offset = 0; 
var me = this; 
oPopup.document.body.onmouseover = function(){me.pause=true;} 
oPopup.document.body.onmouseout = function(){me.pause=false;} 

var fun = function(){ 
var x = me.left; 
var y = 0; 
var width = me.width; 
var height = me.height; 

if(me.offset>me.height){ 
height = me.height; 
} else { 
height = me.offset; 
} 

y = me.bottom - me.offset; 
if(y<=me.top){ 
me.timeout--; 
if(me.timeout==0){ 
window.clearInterval(me.timer); 
if(me.autoHide){ 
me.hide(); 
} 
} 
} else { 
me.offset = me.offset + me.step; 
} 
me.Pop.show(x,y,width,height); 

} 

this.timer = window.setInterval(fun,this.speed)
var btClose = oPopup.document.getElementById("btSysClose");
btClose.onclick = function(){ 
me.close = true; 
me.hide(); 
} 
var btCommand = oPopup.document.getElementById("btCommand"); 
btCommand.onclick = function(){ 
me.oncommand(); 
} 
//var ommand = oPopup.document.getElementById("ommand"); 
//ommand.onclick = function(){ 
//this.close = true; 
//me.hide(); 
//window.open(ommand.href); 
//} 
} 

//�����ٶȷ��� 
CLASS_GAME_MESSAGE.prototype.speed = function(s){ 
var t = 20; 
try { 
t = praseInt(s); 
} catch(e){} 
this.speed = t; 
} 
//step 
CLASS_GAME_MESSAGE.prototype.step = function(s){ 
var t = 1; 
try { 
t = praseInt(s); 
} catch(e){} 
this.step = t; 
} 

CLASS_GAME_MESSAGE.prototype.rect = function(left,right,top,bottom){ 
try { 
this.left = left !=null?left:this.right-this.width; 
this.right = right !=null?right:this.left +this.width; 
this.bottom = bottom!=null?(bottom>screen.height?screen.height:bottom):screen.height; 
this.top = top !=null?top:this.bottom - this.height; 
} catch(e){} 
} 

//fix tips
//document.onmousedown=function(){UnTip();};
//document.onmousemove=function(){try{UnTip();}catch(e){}};

function filterUserBoardmsg() {
	if($('cmsg').value && $('cmsg').value.indexOf('/') >= 0 && $('cmsg').value.indexOf('<') > 0 && $('cmsg').value.indexOf('>') > 0) {
		var fValue = $('cmsg').value.replace(/<FONT\s+\w+=#?\w+\s+\w+=['"]?\w+['"]?>/, '');
		fValue = fValue.replace(/<FONT\s+\w+=#?\w+\s*>/, '');
		fValue = fValue.replace(/<\/FONT>/, '');
		fValue = fValue.replace(/</, '');
		fValue = fValue.replace(/>/, '');
		fValue = fValue.replace(/\(VIP\)/, '');
		$('cmsg').value = fValue;
		
		return false;
	}
}

window.onload = function() {
	if(navigator.userAgent.indexOf("MSIE")>0){
		$('cmsg').attachEvent("onpropertychange", filterUserBoardmsg);
	} else if (navigator.userAgent.indexOf("Firefox")>0) {
		$('cmsg').addEventListener("oninput", filterUserBoardmsg, false);
	}
}
// Add by DuHao 2009-5-13
function copyWorda(words)
{
	document.getElementById('baike_input').value=words;
}


function ggshow(str){
	str=str.replace(/&lt;/g,'<').replace(/&gt;/g,'>');
	var html = '<table width="250" border="0" align="center" cellpadding="0" cellspacing="0"><tr><td height="19" colspan="2" background="../images/gg/gg_06.jpg"><img src="../images/gg/gg_06.jpg" width="237" height="19"></td><td height="19" background="../images/gg/gg_07.jpg"><img src="../images/gg/gg_07.jpg" style="cursor:pointer" onclick="ggoff()"></td></tr><tr><td align="left" background="../images/gg/gg_02.jpg"><img src="../images/gg/gg_02.jpg" width="8" height="5"></td><td width="236" bgcolor="#F7FEF7"><font color="#0099FF"><div style="margin-left:30px">'+str+'</div></font></td><td background="../images/gg/gg_03.jpg"><img src="../images/gg/gg_03.jpg" width="13" height="5"></td></tr><tr><td colspan="3"><img src="../images/gg/gg_05.jpg" width="250" height="11"></td></tr></table>';
	$('challenge_info').innerHTML = html;
	$('challenge_info').style.display='block';
	setTimeout("ggoff()",5000);
}
function ggoff(){
	$('challenge_info').style.display='none';
}

function lockfun(id){
	if(!confirm('��ȷ�����˵���������')){
		return false;
	}
	var opt = {
     	method: 'get',
		onSuccess: function(t) {
			 		var res = t.responseText;
					if(res == 1){
						window.parent.Alert('û�м������ߣ�');
					}else if(res == 2){
						window.parent.Alert('��������');
					}else if(res == 3){
						window.parent.Alert('�����ɹ���');
					}else if(res == 4){
						window.parent.Alert('�˵����Ѿ�������');
					}
    		 	},
     	asynchronous:true        
	}
	var ajax=new Ajax.Request('../function/lock.php?id='+id, opt);
}

var optt = {
		method: 'get',
		
		asynchronous:true        
	}


function doapplyTeam(id,mapid)
{
	optt.onSuccess= function(n) {
					if(n.responseText!='OK')
					{
						window.parent.Alert(n.responseText);
					}
					else
					{
						window.parent.Alert('����ɹ���');
						$('gw').contentWindow.location='/function/Team_Mod.php?n='+mapid;
					}
				};
	var ajax=new Ajax.Request('/function/team.php?rd='+Math.random()+'&act=apply&id='+id, optt);
}


//Getdate in Task
function Getdate(){
var t=new Date();
var y=t.getYear();
if (navigator.appName=='Netscape'){
var y=t.getYear()+1900;
}
var mo=(t.getMonth()+1)<10?"0"+(t.getMonth()+1):t.getMonth()+1;
var w=t.getDay();
var week=Array("��","һ","��","��","��","��","��");
w=week[t.getDay()];
var d=t.getDate()<10?"0"+t.getDate():t.getDate();
	$("date").innerHTML=y+"��"+mo+"��"+d+"��<br />����"+w;
}

//��������
function Clean()
{
	var opt = {
     	method: 'get',
		onSuccess: function(t) {
			 		$('bags').innerHTML=t.responseText;
    		 	},
     	asynchronous:true 
	}
	var ajax=new Ajax.Request('./function/getBag.php?clean=1&style=1', opt);	
}


function taskASwap(obj)
{
    var objs=obj.parentNode.parentNode.parentNode.parentNode.getElementsByTagName('li');
    for(var i=0;i<objs.length;i++)
    {
		var o=objs[i];
        if(o.className.indexOf('focus'))
        {
            o.className=o.className.replace('focus','');
        }
    }
	
    obj.parentNode.parentNode.className=obj.parentNode.parentNode.className+'focus';
	
}

//Task_Tab
function setTab(name,cursel,n){
	for(i=1;i<=n;i++){
		var menu=document.getElementById(name+i);
		var con=document.getElementById("con_"+name+"_"+i);
		menu.className=i==cursel?"on":"";
		if(i==cursel)
		{
			if(con.style.display!="block"){
				menu.childNodes[0].childNodes[0].style.background='url("../new_images/index/ico_1.gif") no-repeat scroll 10px center transparent';
			}else{
				menu.childNodes[0].childNodes[0].style.background='url("../new_images/index/ico_2.gif") no-repeat scroll 10px center transparent';
			}
		}else{
			if(con.style.display=="block"){
				menu.childNodes[0].childNodes[0].style.background='url("../new_images/index/ico_1.gif") no-repeat scroll 10px center transparent';
			}else{
				menu.childNodes[0].childNodes[0].style.background='url("../new_images/index/ico_2.gif") no-repeat scroll 10px center transparent';
			}
		}
		con.style.display=i==cursel?(con.style.display=="block"?"none":"block"):"none";
 	}

}
function doSacrifice()
{
	if(!$('SacrificeList').value){
		alert('��ѡ�����Ʒ��');
		return;
	}
	var opt = {
			method: 'get',
			onSuccess: function(t) {
						Alert(t.responseText);
			},
		asynchronous:true
	}
	var ajax=new Ajax.Request('/function/usedProps.php?id='+$('SacrificeList').value+'&js', opt);	
}
//
function Sacrifice()
{
	try{
		var obj;
		if(!(obj=document.getElementById('SacrificeDiv')))
		{
			obj=document.createElement('div');
			obj.style.cssText="width:300px;height:200px;position:absolute;left:150px;top:200px;boder:1px solid #606060;background-color:#CCCCCC";
			obj.id='SacrificeDiv';
			obj.innerHTML='ѡ�����Ʒ��<br />\
<select name="select" size="10" id="SacrificeList">\
</select><input name="" type="button" value="����" onclick="doSacrifice()" /><input name="" type="button" value="�ر�" onclick="document.getElementById(\'SacrificeDiv\').style.display=\'none\'"/>';
			document.body.appendChild(obj);
		}else{
			document.getElementById('SacrificeDiv').style.display='block';
		}
		var opt = {
				method: 'get',
				onSuccess: function(t) {
					$('SacrificeList').innerHTML=t.responseText;					
    		 	},
    	 	asynchronous:true
		}
		var ajax=new Ajax.Request('/function/getBagOfVary.php', opt);	
	}catch(e){
		
	}
}
