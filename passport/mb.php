<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=gb2312"  />
	<meta name="viewport" content="user-scalable=no, width=device-width,  initial-scale=0.3/" /> 
	
	<title>�ڴ�����</title>
	<script src="../js/jquery-1.2.4a.js"></script>
</head>
<body>
<input type="button" onclick="javascript:window.location='login.php'" value="����"><br /><br />
<font>�����û�����</font><input type="text" name="passport" /><br />
<input type="button" id="pass" value="�ύ" />
</body>
</html>
<script>
$("#pass").click(function()
{
	if($("[name='passport']").val() != '')
	{
		$.ajax( {    
	    url:'mbGate.php', 
	    data:{"passport":encodeURIComponent($("[name='passport']").val())},    
	    type:'post',    
	    cache:false, 
	    dataType:'html',    
	    success:function(data) {
			if(data.substring(0,2) != "OK")
			{    
	        	alert(data);
			}
			else if(data.substring(0,3) == "OK1")
			{
				$("#pass").remove();
				$("[name='passport']").attr("readonly",true);
				alert("�㻹û�������ܱ���������");
				var input = $("<br /><font>�������룺</font><input type='text' name='password' /><br /><br /><font color='red'>���ܱ�����һ�����ò����޸�</font><br /><div><font>�������ܱ����⣺</font><input type='text' name='qu' /><br /><br /><font>�������ܱ��𰸣�</font><input type='text' name='an' /><br /><br /><input type='button' onclick='setmb()' id='setmb' value= '����'></div>");
				$("body").append(input);
				
			}
			else if(data.substring(0,3) == "OK2")
			{
				$("#pass").remove();
				$("[name='passport']").attr("readonly",true);
				var input = $("<br /><div><font>�ܱ����⣺</font>"+data.substring(4)+"<br /><br /><font>�������ܱ��𰸣�</font><input type='text' name='anS' /><br /><br /><input type='button' id='ref' onclick='setan()' value= '��֤'></div>");
				$("body").append(input);
			}
	    },    
	    error : function() {    
	          alert("�쳣��");    
	     }    
	});
	}
});
function setmb()
{
	if($("[name='qu']").val() != '' && $("[name='an']").val() != ''  )
	{
		$.ajax({    
	    url:'mbGate.php', 
	    data:{"passport":encodeURIComponent($("[name='passport']").val()),"an":encodeURIComponent($("[name='an']").val()),"qu":encodeURIComponent($("[name='qu']").val()),"pass":encodeURIComponent($("[name='password']").val())},    
	    type:'post',    
	    cache:false, 
	    dataType:'html',    
	    success:function(data) {
			if(data.substring(0,2) == "OK")
			{
				alert("���óɹ�");
				window.location = "login.php";
			}
			else
			{
				alert(data);
			}
	    },    
	    error : function() {    
	          alert("�쳣��");    
	     }   
		 }); 
	}
	else
	{
		alert("������𰸲���Ϊ��");
	}
}
function setan()
{
	if($("[name='anS']").val() != '' )
	{
		$.ajax({    
	    url:'mbGate.php', 
	    data:{"passport":encodeURIComponent($("[name='passport']").val()),"anS":encodeURIComponent($("[name='anS']").val())},    
	    type:'post',    
	    cache:false, 
	    dataType:'html',    
	    success:function(data) {
		
			if(data.substring(0,2) == "OK")
			{
				$("#ref").remove();
				$("[name='anS']").attr("readonly",true);
				var input = $("<br /><div><font>�����������룺</font><input type='text' name='newPass' /><br /><br /><input type='button' onclick='setpass()' value= '����'></div>");
				$("body").append(input);
			}
			else
			{
				alert(data);
			}
	    },    
	    error : function() {    
	          alert("�쳣��");    
	     }   
		 }); 
	}
	else
	{
		alert("������𰸲���Ϊ��");
	}
}
function setpass()
{
	if($("[name='newPass']").val() != '' )
	{
		$.ajax({    
	    url:'mbGate.php', 
	    data:{"passport":encodeURIComponent($("[name='passport']").val()),"anS":encodeURIComponent($("[name='anS']").val()),"newPass":encodeURIComponent($("[name='newPass']").val())},    
	    type:'post',    
	    cache:false, 
	    dataType:'html',    
	    success:function(data) {
			if(data.substring(0,2) == "OK")
			{
				alert("���óɹ�");
				window.location = "login.php";
			}
			else
			{
				alert(data);
			}
	    },    
	    error : function() {    
	          alert("�쳣��");    
	     }   
		 }); 
	}
}


</script>
