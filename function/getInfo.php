<?php
header('Content-Type:text/html;charset=GB2312');
require_once('../config/config.game.php');
secStart($_pm['mem']);
$info = $_pm['mysql'] -> getRecords("SELECT id,times,content FROM information WHERE uid = {$_SESSION['id']} ORDER BY id DESC LIMIT 10");
if(is_array($info)){
	if(count($info) == 10){
		$sql = "DELETE FROM information WHERE uid = {$_SESSION['id']} AND id < {$info[3]['id']}";
		//echo $sql;
		$_pm['mysql'] -> query($sql);
	}
	foreach($info as $k => $v){
		$i = $k + 1;
		$len = mb_strlen($v['content']);
		if($len>42){
			$c = substr($v['content'],0,41);
			$c .= '...';
		}else{
			$c = $v['content'];
		}
		$html .= '<li><a title="'.$v['content'].' '.$v['times'].'"><p>'.$i.'. '.$c.'</p></a></li>';
	}
}else{
	$html = '<li><a title="Ŀǰ��û���κ�ϵͳ��Ϣ"><p>
			Ŀǰ��û���κ�ϵͳ��Ϣ
			</p></p></li>';
}
echo $html;
?>