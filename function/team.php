<?php 
require_once('../config/config.game.php');
secStart($_pm['mem']);
header('Content-Type:text/html;charset=GBK');
require_once(dirname(__FILE__).'/../socketChat/config.chat.php');
$s=new socketmsg();
$team=new team($_SESSION['team_id'],$s);
$team->checkMyTeam();
if(isset($_GET['checkOnly']))
{
//header("refresh:27;url=".$_SERVER['REQUEST_URI']);
	die('<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="refresh" content="26" />
<meta http-equiv="Content-Type" content="text/html; charset=gb2312" />
<title>��ǰ��ͼ�Ķ���</title>
<script language="javascript">
setTimeout("window.location=\'/function/team.php?checkOnly=1&rd=\'+Math.random();",24000);
setTimeout("window.location.reload();",25000);
</script></head>
<body>
</body>
</html>');
}
/*********************************************************************************/
/*����������ļ���iframeʹ��ʱ����ش���*/
/*********************************************************************************/
if(isset($_GET['showAllTeamsTime']))
{
	if(!isset($_SESSION['team_id'])){
		$echo = $team->getTeamList(intval($_GET['showAllTeamsTime']));
		if(isset($_GET['check']))
		{
			if($echo!='latest')
			{
				echo 'window.location="/function/team.php?showAllTeamsTime=0";';
			}else{
				echo '//'.$echo;
			}
			die();
		}
	}else{
		$echo = $team->getMyTeamInfo();
		$isleader=$team->isTeamLeader($_SESSION['id'],$_SESSION['team_id']);
	}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=gb2312" />
<title>��ǰ��ͼ�Ķ���</title>
</head>
<script language="javascript" src="/javascript/prototype.js"></script>
<style>
body,td{font-size:12px}
ul { margin: 0px; padding: 0px; }
li { display: inline; list-style-type: none; }
.title { width:95px; height:22px; float:left;line-height:22px; overflow:hidden; }
.title2 { width:50px; height:22px; float:left;line-height:22px;}
.title3 { width:80px; height:17px; float:left;line-height:22px; text-align:center; padding-top:5px}

</style>
<body leftmargin="0" topmargin="0" rightmargin="0">
<div style="position:absolute; left:230px; z-index:10; cursor:pointer" onclick="window.parent.location.reload();"><img src="../new_images/ui/add08.gif" border="0" /></div>

<script language="javascript">
var inteam=false;
curtime=<?php echo time(); ?>;
data='<?php echo $echo; ?>';
function activeButtons()
{
	var btn=document.getElmentsByTagName('input');
	for(i=0;i<btn.length;i++)
	{
		if(btn[i].type=='button')
		{
			btn[i].disabled=false;
		}
	}
}
</script>
<?php if (!isset($_SESSION['team_id'])){ ?>
<table border="0" style="border-bottom:1px solid #CCCCCC; color:#005500" cellpadding="0" cellspacing="0">
  <tr>
    <td align="center" height="21" width="95" style="border-bottom:1px solid #005500; ">�ӳ�</td>
    <td align="center" width="50" style="border-bottom:1px solid #005500; ">��Ա����</td>
    <td align="center" width="80" style="border-bottom:1px solid #005500; ">�������</td>
  </tr>
</table>
<script language="javascript">
datas=data.split('`');

document.write('<ul>');
for(i=0;i<datas.length;i++)
{
	tmp=datas[i].split('|');
	if(tmp.length==3){
		document.write('\
		<li>\
            <div class="title" align="center">\
            '+tmp[1]+'</div>\
            <div class="title2" align="center">\
            '+tmp[2]+'</div>\
            <div class="title3" align="center">\
            <img src="../images/ui/team/anniu1.gif" style="cursor:pointer" width="69" height="17" onclick="parent.applyTeam('+tmp[0]+');this.disabled=true;" /></div>\
            </li>\
			');
 	 }
}
document.write('</ul>');

function cnew()
{
	var o = document.createElement('script');
	o.src = '/function/team.php?check=1&showAllTeamsTime='+curtime+'&rd='+Math.random();
	document.body.appendChild(o);
	setTimeout('cnew();',5000);
}
setTimeout('cnew();',5000);
</script>
<!--/table-->
<?php } else { ?>
<script language="javascript">
var inteam=true;
var isleader=<?php echo $isleader+0; ?>;
var myuid=<?php echo $_SESSION['id']+0; ?>;
var data=data.split('@');

document.write('<strong>'+data[0]+'</strong> �Ķ���');

</script>
<table width="100%" border="0" cellpadding="0" cellspacing="0" style="; color:#005500">
  <tr style="border-bottom:1px solid #005500; color:#005500">
    <td align="center" height="21" style="border-bottom:1px solid #005500; ">��Ա</td>
    <td align="center" style="border-bottom:1px solid #005500; ">״̬</td>
    <td align="center" style="border-bottom:1px solid #005500; ">����</td>
  </tr>

<script language="javascript">
var datas=data[1].split('`');
for(i=0;i<datas.length;i++)
{
	tmp=datas[i].split('|');
	
	if(tmp.length==3){
		if(tmp[2]=='-1')
		{
			str='������';
		}
		else if(tmp[2]=='1')
		{
			str='�ѹ��';
		}
		else if(tmp[2]=='-2')
		{
			continue;
		}else{
			str='����';
		}
		document.write('  <tr>\
		<td align="center" height="21">'+tmp[1]+'</td>\
		<td align="center">'+str+'</td>\
		<td align="center">'+(
			isleader&&myuid!=tmp[0]?
			(tmp[2]!='-1'?'<input type="image" src="../new_images/ui/team_kick.png" name="Submit" value="�߳�" onclick="parent.kickMember('+tmp[0]+');this.disabled=true;" style="cursor:pointer"/>':'<input  type="image" src="../new_images/ui/team_accept.png" style="cursor:pointer" name="Submit" value="��׼" onclick="parent.permitTeam('+tmp[0]+');this.disabled=true;" /><input  type="image" style="cursor:pointer" src="../new_images/ui/team_refuse.png" name="Submit" value="�ܾ�" onclick="parent.unPermitTeam('+tmp[0]+');this.disabled=true;" />')
			:
			'-')+'</td>\
	  </tr>');
 	 }
}
</script>
</table>
<?php } ?>
</body>
</html>
<?php 
	die();
}


/*********************************************************************************/
/*����������ļ���ajax����ĺ�̨ʹ��ʱ����ش��������û��������飬�뿪����ȵ� */
/*********************************************************************************/

//$rs=$s->sendMsg('SYSN|'.iconv('gb2312','utf-8','�����Ϣ���͸�idΪ97��620���û�,�����������������յ�').'.',array(97,620));
header("Content-type: text/css; charset=gb2312"); 

$act=$_GET['act'];
switch($act)
{
	case 'create':
		$rs=$team->createTeam();
		if($rs!==true)
		{
			die($rs);
		}else{
			die("OK");
		}
		break;
	case 'permit':
		$rs=$team->permitTeam(intval($_GET['id']));
		if($rs!==true)
		{
			die($rs);
		}else{
			die("OK");
		}
		break;
	case 'unpermit':
		$rs=$team->unpermitTeam(intval($_GET['id']));
		if($rs!==true)
		{
			die($rs);
		}else{
			die("OK");
		}
		break;
	case 'invite':
		$rs=$team->inviteTeam(intval($_GET['id']));
		if($rs!==true)
		{
			die($rs);
		}else{
			die("OK");
		}
		break;
	case 'apply':
		$rs=$team->applyTeam(intval($_GET['id']));
		if($rs!==true)
		{
			die($rs);
		}else{
			die("OK");
		}
		break;
	case 'leave':
		$rs=$team->leaveTeam(intval($_GET['id']));
		if($rs!==true)
		{
			die($rs);
		}else{
			die("OK");
		}
		break;
	case 'disbandTeam':
		$rs=$team->disbandTeam();
		if($rs!==true)
		{
			die($rs);
		}else{
			die("OK");
		}
		break;
	case 'kickMember':
		$rs=$team->kickMember(intval($_GET['id']));
		if($rs!==true)
		{
			die($rs);
		}else{
			die("OK");
		}
		break;	
	case 'swapState':
		$rs=$team->swapTeamState();
		if($rs!==true)
		{
			die($rs);
		}else{
			die("OK");
		}
		break;
	case 'teamInfo':
		$rs=$team->getMyTeamInfo();
		if($rs!==true)
		{
			die($rs);
		}else{
			die("OK");
		}
		break;
	default:
		echo  '��������'.$act.'����';
		break;
}
?>