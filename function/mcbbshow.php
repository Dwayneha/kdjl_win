<?php
//�����������
header('Content-Type:text/html;charset=GBK');
require_once "../config/config.game.php";
$bid = intval($_REQUEST['id']);
if($bid < 1)
{
	die("");
}
$arr = $_pm['mysql'] -> getOneRecord("SELECT level,effectimg,name,wx,srchp,srcmp,ac,mc,hits,miss,speed,czl FROM userbb WHERE id = $bid");//../images/ui/petbg.jpg
?>
<div style="z-index:10000; width:40px; height:20px; position:absolute; left:269px; font-size:12px; text-align:center; padding-top:5px; padding-right:5px"><span onclick="mcbbdisplay();" style="cursor:pointer"><font color="#FF0000">�ر�</font></span></div>
<div style=" clear:both;width:300px;height:230; background-image:url(../images/ui/petbg.jpg) ; background-repeat:no-repeat;position:absolute; z-index:9999">
<div style=" clear:both;width:300px;height:230; background-image:url(../images/ui/petbg.jpg) ; background-repeat:no-repeat;position:absolute; z-index:9999">
    <div style="width:177px;height:230px;float:left;"><img src="../images/bb/<?=$arr['effectimg']?>" width="177px" height="230px" /></div>
    <div style="width:123px;height:230px;float:left;position:relative">
         <div style="position:absolute; text-align:center;top:16px;left:9px;width:99px;height:24px; font-size:12px; color:#FFFFFF;font-family:"΢���ź�","����",Arial,Vendana;color:#ffffff;"><?=$arr['name']?></div>
         <div style="font-size:12px;line-height:20px;position:absolute;top:40px;padding:2px;left:5px;height:180px;width:110px;overflow:hidden;">
         ���У�<?=$_pets['wx'][$arr['wx']]?><br />
		 ������<?=$arr['srchp']?><br />
		 ħ����<?=$arr['srcmp']?><br />
		 ������<?=$arr['ac']?><br />
		 ������<?=$arr['mc']?><br />
		 ���У�<?=$arr['hits']?><br />
		 ���ܣ�<?=$arr['miss']?><br />
		 �ɳ���<?=$arr['czl']?><br />
		 �ȼ���<?=$arr['level']?>
         </div>
    </div>
</div>
