// JavaScript Document
/*usages:ɨ��ǰ��js���ܺ���*/
var gonggao = "<div class='sm'><b>��� <font color=red>?</font> ��������������!</b></div>";
var flash_time = 0;
var move;
var settime1;
var settime2;
var settime3;
var settime4;
var settime5;
var settime6;
function move_status(para)
{
	move = para==1?1:2;
}
function can_move()
{
	if(move == 1)
	{
		var theEvent = window.event || arguments.callee.caller.arguments[0];
		document.getElementById('tishi').style.top = theEvent.clientY-100+"px";
		document.getElementById('tishi').style.left = theEvent.clientX-600+"px";
	}
}
function sl_restart(para)
{
	tishi = document.getElementById('panel').getElementsByTagName('div');
	for(i=0;i<tishi.length;i++)
	{
		if(tishi[i].id == 'tishi')
		{
			display(tishi[i]);
		}
	}
	div = document.createElement('div');
	div.className = "tishi";
	div.style.top = "250px";
	div.style.left = "100px";
	div.onmousedown  = function()
	{
		move_status(1);
	}
	div.onmouseup  = function()
	{
		move_status(2);
	}
	div.onmousemove = function()
	{
		can_move();
	}
	div.id = "tishi";
	div_child = document.createElement('div');
	div_child.className='font';
	img1 = document.createElement('img');
	img1.className='sure btn tishi_btn';
	img1.src='../images/ui/sl_mod/sl11.gif';
	img2 = document.createElement('img');
	img2.className='chanel btn tishi_btn';
	img2.src='../images/ui/sl_mod/sl12.gif';
	switch(para)
	{
		case 'sx' :
		{
			str = "ˢ��һ����Ҫ����<font color='red'>һ��ɨ��ˢ��ȯ</font> �Ƿ����?";
			img1.onclick=function()
			{
				sl_restart_request();
				display('tishi');
			}
			img2.onclick=function()
			{
				display('tishi');
			}
			div_child.innerHTML = str;
			div_child.appendChild(img1);
			div_child.appendChild(img2);
			div.appendChild(div_child);
			break;
		}
		case 'fh' :
		{
			img1.onclick=function()
			{
				die_c('used');
				display('tishi');
			}
			str = "������Ҫ����<font color='red'>һ�Ÿ���ȯ</font> �Ƿ����?";
			img2.onclick=function()
			{
				display('tishi');
				die_c('cancel');
			}
			div_child.innerHTML = str;
			div_child.appendChild(img1);
			div_child.appendChild(img2);
			div.appendChild(div_child);
			
			break;
		}
		case 'getfh':
		{
			img1.className='btn tishi_btn';
			img1.onclick=function()
			{
				display('tishi');
			}
			str = "ϵͳ������һ�Ÿ��<br>";
			div_child.innerHTML = str;
			div_child.appendChild(img1);
			div.appendChild(div_child);
			settime1 = setTimeout("display('tishi')",2000);
			break;
		}
		case 'sl_card':
		{
			img1.onclick=function()
			{
				display('tishi');
				use_sl_card();
			}
			str = "ϵͳ��⵽������ɨ�ױ�������һ��ɨ�״��ؿ�(����ɳ�����65�����ϵ����ÿ����һ����ѽ���Ļ���),�Ƿ�ʹ��һ��ɨ�״��ؿ�?";
			img2.onclick=function()
			{
				display('tishi');
			}
			div_child.innerHTML = str;
			div_child.appendChild(img1);
			div_child.appendChild(img2);
			div.appendChild(div_child);
		}
	}


	document.getElementById('panel').appendChild(div);
}
function sl_restart_request()
{
	var opt = {
		method: 'get',
		onSuccess: function(t)
		{
			var respone =  t.responseText;
			if(respone == 1)
			{
				Alert("����ɨ��ˢ��ȯ��������,����ˢ��");
			}
			else
			{
				document.getElementById('prize').innerHTML = respone;
				document.getElementById('sm').innerHTML += "<br>ˢ�³ɹ�";
				scrollWindow();
			}
		},
		on404: function(t)
		{
		},
		onFailure: function(t)
		{
    	},
    	asynchronous:true        
	}
	var ajax=new Ajax.Request('../function/sl_restart.php', opt);
}
function display(id)
{
	obj = document.getElementById(id);
	obj.parentNode.removeChild(obj);
}
function choose(ob)
{
	var id = ob.id.replace("lq_",'');
	var opt = {
		method: 'get',
		onSuccess: function(t)
		{
			var respone =  t.responseText;
			var responeinfo = respone.split('<Boundaries>');
			ob.innerHTML = responeinfo[1];
			ob.className = (responeinfo[1].indexOf('bob.gif') == -1)?'open':'open_lei';
			document.getElementById('sm').innerHTML += '<br>'+responeinfo[2];
			document.getElementById('fhtime').innerHTML = responeinfo[3];
			if(responeinfo[4]!=0)
			{
				sl_restart('getfh');
			}
			if(responeinfo[1].indexOf('bob.gif') != -1)
			{
				settime2 = setTimeout("bob_die()",1000);
				settime3 = setTimeout("table_view('"+responeinfo[0]+"',1)",8000);
			}
			else
			{
				settime2 = setTimeout("table_view('"+responeinfo[0]+"')",3000);
			}
			scrollWindow();
		},
		on404: function(t)
		{
		},
		onFailure: function(t)
		{
		},
		asynchronous:true        
	}
	var ajax=new Ajax.Request('../function/sl_start.php?id='+id, opt);
}
function flash(id,type)
{
	var ob = document.getElementById(id);
	for(i=1;i<=9;i++)
	{
		document.getElementById("lq_"+i).onclick = '';
	}
	if(typeof(ob.filters) == "undefined")	//fox
	{
		if(type == 1)
		{
			ob.style.opacity -= 0.1;
			if(ob.style.opacity <= 0)
			{
				settime4 = setTimeout("flash('"+ob.id+"',0)",10);
			}
			else
			{
				settime4 = setTimeout("flash('"+ob.id+"',1)",10);
			}
		}		
		else
		{
			ob.style.opacity = parseFloat(ob.style.opacity)+0.1;
			
			if(ob.style.opacity >= 1)
			{
				flash_time++;
				if(2 == flash_time)
				{
					flash_time = 0;
					choose(ob);
				}
				else
				{
					settime4 = setTimeout("flash('"+ob.id+"',1)",10);
				}
			}
			else
			{
				settime4 = setTimeout("flash('"+ob.id+"',0)",10);
			}				

		}		
	}
	else	//ie
	{
		if(type == 1)
		{
			ob.filters.Alpha.Opacity -= 10;
			if(ob.filters.Alpha.Opacity <= 0)
			{
				settime4 = setTimeout("flash('"+ob.id+"',0)",10);
			}
			else
			{
				settime4 = setTimeout("flash('"+ob.id+"',1)",10);
			}
		}		
		else
		{
			ob.filters.Alpha.Opacity = parseFloat(ob.filters.Alpha.Opacity)+10;
			
			if(ob.filters.Alpha.Opacity >= 100)
			{
				flash_time++;
				if(2 == flash_time)
				{
					flash_time = 0;
					choose(ob);
				}
				else
				{
					settime4 = setTimeout("flash('"+ob.id+"',1)",10);
				}
			}
			else
			{
				settime4 = setTimeout("flash('"+ob.id+"',0)",10);
			}				
		}	
	}
}
function table_view(para,type)
{
	document.getElementById('leiqu01').innerHTML = para;
	if(type != 1)
	{
		settime5 = setTimeout("auto()",5000);
	}
	else
	{
		settime5 = setTimeout("sl_restart('fh')",2000);
	}
}
function auto()
{
	var opt = {
		method: 'get',
		onSuccess: function(t)
		{
			var respone =  t.responseText;
			var responeinfo = respone.split('<Boundaries>');
			document.getElementById('gs').innerHTML = "<b>"+responeinfo[0]+"</b>";
			document.getElementById('leiqu01').innerHTML = responeinfo[1];
			document.getElementById('leinum').innerHTML = responeinfo[0]-1;
			document.getElementById('fhtime').innerHTML = responeinfo[2];
			scrollWindow();
		},
		on404: function(t)
		{
		},
		onFailure: function(t)
		{
		},
		asynchronous:true        
	}
	var ajax=new Ajax.Request('../function/auto_start.php', opt);	
}
function bob_die()
{
	divd = document.createElement('div');
	divd.className = "donghua";
	divd.id = "donghua";
	pic = document.createElement('img');
	pic.src = "../images/props/bob_broken.gif";
	divd.appendChild(pic);
	document.getElementById('panel').appendChild(divd);
	settime6 = setTimeout("display('donghua')",5000);
	
}
function die_c(para)
{
	var opt = {
		method: 'get',
		onSuccess: function(t)
		{
			
		},
		on404: function(t)
		{
		},
		onFailure: function(t)
		{
		},
		asynchronous:false      
	}
	var ajax=new Ajax.Request('../function/die_deal.php?cmd='+para, opt);
	auto();
}
function scrollWindow()
{
	var t = document.getElementById('sm');
	t.scrollTop = t.scrollHeight;
}
function close_sl_mod()
{
	obj = document.getElementById('saolei_index');
	obj.parentNode.removeChild(obj);
	clearTimeout(settime1);
	clearTimeout(settime2);
	clearTimeout(settime3); 
	clearTimeout(settime4); 
	clearTimeout(settime5); 
	clearTimeout(settime6); 
}
function canntplay()
{
	sl_restart('sl_card');
}
function use_sl_card()
{
	opt = {
		method: 'get',
		onSuccess: function(t)
		{
			if(t.responseText != 'ok')
			{
				Alert(t.responseText);
			}
			else
			{
				Alert("ɨ�׿�ʹ�óɹ�");
			}
			auto();
		},
		on404: function(t)
		{
		},
		onFailure: function(t)
		{
		},
		asynchronous:false         
	}
	ajax=new Ajax.Request('../function/use_sl_card.php', opt);
}
function open_info()
{
	objarr = document.getElementById('panel').getElementsByTagName('div');
	times = 0;
	for(i=0;i<objarr.length;i++)
	{
		if(objarr[i].id == 'info')
		times++;
	}
	if(times != 0)
	{
		return false;
	}
	div = document.createElement('div');
	divp = document.createElement('div');
	font = document.createElement('font');
	font.innerHTML = "<b>��ɨ�׿�����</b><p>1��ÿ�ճ���ɳ���65����ҿ���ѿ���ɨ�״���<br>һ�Σ�</p><p>2��	��ʹ�������̵귷����ɨ�״��ؿ�������</p><b>��ɨ�׹���</b><p>1��ɨ�״��ع��оŲ�ؿ��������������·���չ<br>ʾ������ÿ�ؿ����صı�� </p><p>2��ÿһ�ط�Ϊ9�����ң�����ѡ������һ������<br>��̽�����</p><p>3��ÿ������=��ǰ�ؿ�-1���ӵڶ����𣩣�</p><p>4�����������йؿ�����ĳһ�ؿ�������������<br>���������ɨ�״��ؽ�����</p><b>��ɨ�׹��ܡ�</b><p>1��	ɨ�״��ؿ��������ٴλ�ý���ɨ�״��ص�<br>Ȩ�ޣ�ÿ����ʹ��һ��;</p><p>2��	ɨ�׸��������������ʹ�ã�ԭ�ظ���;<br>(ͨ��ɨ��3�ء�6�ء�9�غ��м��ʻ��)</p><p>3��ɨ��ˢ��ȯ��������ˢ�¹ؿ�������ÿ����ʹ<br>��һ�Ρ�</p>";
	div.className = "info";
	div.id = "info";
	divp.style.width='290px';
	divp.style.height='360px';
	divp.id = 'divp';

	div.appendChild(divp);
	font.className = "infofont";
	divp.appendChild(font);
	img = document.createElement('img');
	img.src = '../images/ui/sl_mod/sl11.gif';
	img.className = "infosure btn";
	img.onclick = function()
	{
		display("info");
	}
	divp.appendChild(img);
	document.getElementById('panel').appendChild(div);
	
}