<?php
/**
@Usage: ���������Ϣ��
*/

header('Content-Type:text/html;charset=GBK');
require_once('../config/config.game.php');
secStart($_pm['mem']);

$u = $_REQUEST['u'];
$u = iconv('gbk','iso-8859-2',$u);
$u = @mysql_real_escape_string($_REQUEST['u']);
if ($u=='') die('��Ҳ����ڣ�');

$rs = $_pm['mysql']->getOneRecord("SELECT b.username as username,
										  b.name as name,
										  b.level as level,
										  b.czl as czl,
										  b.srchp as srchp,
										  b.srcmp as srcmp,
										  b.imgstand as effectimg
									 FROM userbb as b,player
									WHERE uid=player.id and player.nickname='{$u}' and player.mbid=b.id
								 ");
$rsU = $_pm['mysql']->getOneRecord("SELECT nickname username,id,mbid from player
									WHERE nickname='{$u}'
								 ");
$rs = 	 $_pm['mysql']->getOneRecord("SELECT username as username,
										  name as name,
										  level as level,
										  czl as czl,
										  srchp as srchp,
										  srcmp as srcmp,
										  imgstand as effectimg
									 FROM userbb as b
									WHERE uid=".intval($rsU['id'])." and ".intval($rsU['mbid'])."=id
								 ");							 	 
if (is_array($rs))
{
	echo '
			<table border=0>
			<tr><td><img src="'.IMAGE_SRC_URL.'/bb/'.$rs['effectimg'].'"></td>
			<td valign=middle style="font-size:12px;line-height:1.7;">
			���ˣ�'.$rs['username'].'<br/>
			���'.$rs['name'].'<br/>
			�ȼ���'.$rs['level'].'<br/>
			�ɳ���'.$rs['czl'].'<br/>
			������'.$rs['srchp'].'<br/>
			ħ����'.$rs['srcmp'].'<br/>
			</td></tr>
		  </table>';
}else if($rsU){
	echo '
			<table border=0>
			<tr><td></td>
			<td valign=middle style="font-size:12px;line-height:1.7;">
			��ң�'.$rsU['username'].'<br/>
			�����û��������ս���
			</td></tr>
		  </table>';
}else{
		echo ''.$u .'�����ڣ�';
}
?>