// JavaScript Document
var dtips = document.createElement('DIV');

function sel(obj)
{
	if(selid!=0) selid.style.backgroundColor='#fff';
	selid=obj;
	obj.style.backgroundColor='#DFD496';
	try
	{
		var pro = $(obj.id.replace("t","s")).innerHTML;
		$('num').value= parseInt(pro);
	}
	catch(e)
	{
		$('num').value = "";
	}
}
function buy(){
	//window.parent.Alert('�����̵���ʱδ����!');
    // return;
	//
	var nums = $('num').value;
	if(nums < 1){
		nums = $('limitnum').value;
	}
	if(bid == 0 ){window.parent.window.parent.Alert('����ѡ��Ҫ�������Ʒ��');return;}
	else if(!validInt(nums)){window.parent.Alert('��������������!');return;}
	else if(nums<1 || nums>100){window.parent.Alert('����������Χ��1-100֮���������');return;}
	else{
		var sump = price*parseInt(nums);
		
		if (sump==0) {window.parent.Alert('���ݴ���');return;}
		if(confirm('��ȷ������ѡ�е���Ʒ��? �ܹ��������� '+sump+'Ԫ����'))
		{
			var opt = {
    		 	method: 'get',
				 onSuccess: function(t) {
			 		var n=parseInt(t.responseText);
					if(n==10) {window.parent.Alert('����Ԫ���������ɵ����½ǵ���Ϸ��ֵ����Ԫ����');return;}
					else if(n==4) {window.parent.Alert('���İ����ռ䲻�㣡');return;}
					else if(n==101) {window.parent.Alert('����Ʒ��û�ϼܻ����Ѿ��¼ܣ�');return;}
					else if(n==0) {ajaxfun();window.parent.Alert('������Ʒ�ɹ�!');return;}//refresh bag.
					else {window.parent.Alert(t.responseText);return;}
    		 	},
    		 	on404: function(t) {
    		 	},
    		 	onFailure: function(t) {
    		 	},
    		 	asynchronous:true        
			}
			//window.status='../function/smbuyGate.php?bid='+bid+'&n='+nums;
			var ajax=new Ajax.Request('../function/smbuyGate.php?bid='+bid+'&n='+nums, opt);
		}
		else return;
	}
}

function ajaxfun()
{
	var opt = {
     	method: 'get',
		onSuccess: function(t) {
			 		$('mybag').innerHTML=t.responseText;
    		 	},
     	asynchronous:true        
	}
	var ajax=new Ajax.Request('../function/getBag.php', opt);
}

function sell(){
	var nums = $('num').value; 
	if(bid == 0 ){window.parent.Alert('����ѡ��Ҫ��������Ʒ��');return;}
	else{
	    if(!validInt(nums)) {window.parent.Alert('����ֻ�������֣�');return false;}
		else if(nums<1 || nums>100){window.parent.Alert('����������Χ��1-100֮���������');return;}
		
		if (types==2 && nums!=1)
		{
			window.parent.Alert('���ɵ�����Ʒֻ�ܵ�һ����');return;
		}

		var sump = price*parseInt(nums);
		if(confirm('��ȷ������ѡ�е���Ʒ��? �ܹ������ '+sump+' ��ң�'))
		{
			var opt = {
    		 	method: 'get',
				 onSuccess: function(t) {
			 		var n=parseInt(t.responseText);
					if(n==0) {ajaxfun();window.parent.Alert('������Ʒ�ɹ�!');}
					else if(n==10) window.parent.Alert('���ı�����û���㹻����Ʒ����������');
					else window.parent.Alert('����ʧ��!');
    		 	},
    		 	on404: function(t) {
    		 	},
    		 	onFailure: function(t) {
    		 	},
    		 	asynchronous:true        
			}
			var ajax=new Ajax.Request('../function/sellBag.php?bid='+bid+'&n='+nums, opt);
		}
		else return;
	}
}