document.write("<script language=javascript src='/config/client.js'></script>");
// JavaScript Document
function sel(obj)
{
	for(var i=1;i<4;i++)
	{	
		try{
			$('i'+i).style.filter="alpha(opacity=50)";
		}catch(e){continue;}
	}
	obj.filter="alpha(opacity=100)";
}

function Display(bid,obj)
{
	var objsty = obj.style;
	sel(objsty);
	window.parent.$('gw').src="./function/Pets_Mod.php?pid="+bid;
}

function Setbb(bid,obj,mbid)	// Get pets.
{
	if(bid == mbid)
	{
		return;
	}
	var opt = {
	method: 'get',
	onSuccess: function(t) {
					var tt = t.responseText;
					if(tt == 10){
						if(!confirm("����ǰ���в����л���������������Ҫ�л�����������ʧ����ȷ��Ҫ�л���")){
							return false;
						}
						var opt = {
							method: 'get',
							onSuccess: function(n) {
										if(n.responseText!='') {window.parent.Alert(n.responseText);}
										Display(bid,obj);
									},
							asynchronous:true        
						}
						var ajax=new Ajax.Request('../function/mcGate.php?op=change&id='+bid, opt);
					}else if(tt != "" && tt != 10){
						window.parent.Alert(tt);
						Display(bid,obj);
					}
    		 	},
     	asynchronous:true        
	}
	var ajax=new Ajax.Request('../function/mcGate.php?op=z&id='+bid, opt);
}


function Tag(a,b,c)
{
	if (a == 3) Hide('','none','none');
	else if(b==3) Hide('none','','none');
	else if(c==3) Hide('none','none','');	
	
	$('ta').style.zIndex=a;
	$('tb').style.zIndex=b;
	$('tc').style.zIndex=c;
}
function Hide(a,b,c)
{
	$('cet1').style.display=a;
	$('cet2').style.display=b;
	$('cet3').style.display=c;
}
function sjJn(sid)
{
	if (sid<1 || !validInt(sid))
	{
		window.parent.Alert('�����õļ��ܣ�');return;
	}
	var opt = {
    		 	method: 'get',
				 onSuccess: function(t) {
					if (parseInt(t.responseText)==0) window.parent.Alert('����ʧ�ܣ�');
					else if (parseInt(t.responseText)==2)
					{
						window.parent.Alert('ȱ���������ᣡ');return;
					}
					else if (parseInt(t.responseText)==3)
					{
						window.parent.Alert('�����ĵȼ�̫�ͣ�����������߲㼼�ܣ�');return;
					}
					else if (parseInt(t.responseText)==1)
					{
						window.parent.Alert('��ϲ����������һ���������������飬���������ɹ�!');
						window.location.reload();
					}
    		 	},
    		 	on404: function(t) {
    		 	},
    		 	onFailure: function(t) {
    		 	},
    		 	asynchronous:true        
			}
	//window.status=		'../function/get.sjSkill.php?id='+sid+'&pid='+pid;
	var ajax=new Ajax.Request('../function/get.sjSkill.php?id='+sid+'&pid='+pid, opt);
}

function getJn()
{
	if ($('jlist').value<1 || !validInt($('jlist').value))
	{
		window.parent.Alert('��ѡ��Ҫѧϰ�ļ����飡');return;
	}

	var opt = {
    		 	method: 'get',
				 onSuccess: function(t) {
					if (parseInt(t.responseText)==0) alert('ѧϰʧ��');
					else if (parseInt(t.responseText)==2)
					{
						window.parent.Alert('ȱ�ټ����飡');return;
					}
					else if (parseInt(t.responseText)==3)
					{
						window.parent.Alert('�����ĵȼ�̫�ͣ�����������߲㼼�ܣ�');return;
					}
					else if (parseInt(t.responseText)==4)
					{
						window.parent.Alert('�����뱦�������в�ƥ�䣡');return;
					}
					else if (parseInt(t.responseText)==10)
					{
						window.parent.Alert('���Ѿ�ѧϰ�˸ü��ܣ�');return;
					}
					else if (parseInt(t.responseText)==11)
					{
						window.parent.Alert('�ü�����ר�����ܣ��˳��ﲻ��ѧϰŶ');return;
					}
					else if (parseInt(t.responseText)==1)
					{
						window.parent.Alert('��������һ�������飬ѧϰ���ܳɹ�!');
						window.location.reload();
					}
    		 	},
    		 	on404: function(t) {
    		 	},
    		 	onFailure: function(t) {
    		 	},
    		 	asynchronous:true        
			}
	var ajax=new Ajax.Request('../function/get.Skill.php?id='+$('jlist').value+'&pid='+pid, opt);
}
