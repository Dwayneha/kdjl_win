<?
header('Content-Type:text/html;charset=GBK');
require_once('../config/config.game.php');
secStart($_pm['mem']);
$type=$_REQUEST['type'];
$mergeid=$_REQUEST['mergeid'];
if($type==1){ //�������
	$sql="select request,merge from player_ext where uid = {$_SESSION['id']}";
	$arr1=$_pm['mysql']->getOneRecord($sql);
	if($arr1['merge']>0){
		$sql="select request from player_ext where uid ={$arr1['merge']}";
		$arr2=$_pm['mysql']->getOneRecord($sql);
		if($arr2['request']==1){
			die('14');//�Է��Ѿ�����������
		}
	}else{
		die('11');//�Է��������޻�����ϵ
	}
	$nomergetime=time();
	$sql = "UPDATE player_ext SET sj = sj - 2000,request=1,nomergetime={$nomergetime} WHERE uid = {$_SESSION['id']} and sj >= 2000 and request=0";
	$_pm['mysql']->query($sql);
	$effectRow = mysql_affected_rows($_pm['mysql']->getConn());
	if($effectRow==1){
		//
		//���������������������
		$user_nickname		= $_pm['user']->getUserById($_SESSION['id']);
		$tt=date('Y-m-d H:m:s',time());
		$_pm['mysql']->query("insert into information(uid,times,content) values({$arr1['merge']},'{$tt}','��ҡ�{$user_nickname['nickname']}������������!')");
		require_once(dirname(__FILE__).'/../socketChat/config.chat.php');
		$s=new socketmsg();
		$rs=$s->sendMsg(iconv('gbk','utf-8','SYSN|information-->'),array($arr1['uid']));
		die('1');
	}else{
		$sql="select request from player_ext where request=1 and uid = {$_SESSION['id']}";
		$arr=$_pm['mysql']->getOneRecord($sql);
		if(is_array($arr)){
			die('3');
		}else{
			die('2');
		}	
	}
}elseif($type==2){ //ȡ�����
	$sql="select request,merge from player_ext where uid = {$_SESSION['id']}";
	$arr1=$_pm['mysql']->getOneRecord($sql);
	if($arr1['merge']>0){
		$sql="select merge from player_ext where uid ={$arr1['merge']}";
		$arr2=$_pm['mysql']->getOneRecord($sql);
		if($arr2['merge']==0 || $arr2['merge']!=$_SESSION['id']){
			die('15');//�Է��Ѿ��������
		}
	}
	$sql = "UPDATE player_ext SET sj = sj + 2000,request=0 WHERE uid = {$_SESSION['id']} and request=1";
	$_pm['mysql']->query($sql);
	$effectRow = mysql_affected_rows($_pm['mysql']->getConn());
	if($effectRow==1){
		//
		//�����������ȡ����������������
		$user_nickname		= $_pm['user']->getUserById($_SESSION['id']);
		$tt=date('Y-m-d H:m:s',time());
		$_pm['mysql']->query("insert into information(uid,times,content) values({$arr1['merge']},'{$tt}','��ҡ�{$user_nickname['nickname']}��ȡ������������������')");
		require_once(dirname(__FILE__).'/../socketChat/config.chat.php');
		$s=new socketmsg();
		$rs=$s->sendMsg(iconv('gbk','utf-8','SYSN|information-->'),array($arr1['uid']));
		die('4');
	}else{
			die('5');
	}
}elseif($type==3){ //���ܻ���
	$user		= $_pm['user']->getUserById($_SESSION['id']);
	if($mergeid<0 || empty($mergeid)){
		die('1');
	}
	$sql = "select request,request_merge,merge from player_ext WHERE uid = {$_SESSION['id']} ";
	$arrmerge=$_pm['mysql']->getOneRecord($sql);
	if(is_array($arrmerge)){
		if($arrmerge['request']==1){
				die('2');//��û����ʽ��飬���ȴ��Է�ͬ�����
			}
		if($arrmerge['request']==2){
				die('4');//������������ҷ����˽�����󣬱���ȡ���ſɽ���
			}
		if($arrmerge['merge']>0){
			die('3');//���
		}
		if($arrmerge['request_merge']>0){
			die('4');//������������ҷ����˽�����󣬱���ȡ���ſɽ���
		}
		
	}
		$sql = "select  send from player_ext WHERE uid = {$mergeid} and request_merge={$_SESSION['id']}";
		$send=$_pm['mysql']->getOneRecord($sql);
		if(is_array($send)){
			$send1=explode(',',$send['send']);
			$bid=$send1[1];
			$n=$send1[0];
			//echo "bid:".$bid."|n:".$n;
			$err = 0;
			$mempropsid = unserialize($_pm['mem']->get('db_propsid'));
			$wp1 = $mempropsid[$bid];
			$bid=$wp1['endtime'];
			//echo "|bid2:".$bid;
			
			//die('sss');
			$wp=$mempropsid[$bid];
				//var_dump($wp1);
		  	//var_dump($wp);
			//die('ss');
				if ($wp['vary']==2) //���ܵ���
				{
					$_pm['mysql']->query("INSERT INTO userbag(uid,pid,sell,vary,sums,stime)
								VALUES(
								{$user['id']},
								{$bid},
								{$wp['sell']},
								{$wp['vary']},
								1,
								unix_timestamp()
								);
							");
				}
				else
				{
					$ret = $_pm['mysql']->getOneRecord("SELECT id 
												FROM userbag
											   WHERE uid={$_SESSION['id']} and pid={$bid}
											   LIMIT 0,1
											");
					if(is_array($ret)){
							$_pm['mysql']->query("UPDATE userbag 
							   SET sums=sums+{$n} 
							 WHERE uid={$_SESSION['id']} and id={$ret['id']} and sums+{$n}>0
						  ");
					}else{
						$_pm['mysql']->query("INSERT INTO userbag(uid,pid,sell,vary,sums,stime)
							VALUES(
								   {$user['id']},
								   {$bid},
								   {$wp['sell']},
								   {$wp['vary']},
								   {$n},
								   ".time()."
								  );
						  ");	
					}
				}
		}else{
			die("6");
		}	
	if(!is_array($arrmerge)){
		$_pm['mysql']->query("insert into player_ext(uid,request_merge,merge,request) values({$_SESSION['id']},0,{$mergeid},0)");
	}else{
		$sql = "UPDATE player_ext SET request=0,merge={$mergeid},request_merge=0,send='0' WHERE uid = {$_SESSION['id']}";
		$_pm['mysql']->query($sql);
		}
		$_pm['mysql']->query("UPDATE player_ext SET request=0,merge={$_SESSION['id']},request_merge=0,send='0' WHERE uid = {$mergeid}");
		
		//
		//�����ҽ�����ĳ��ҵĻ���
			/*$user2		= $_pm['user']->getUserById($mergeid);
			$msg_key = 'chatMsgList';
			$nowMsgList = unserialize($_pm['mem']->get($msg_key));
			$arr = split('linend', $nowMsgList);
			if( count($arr)>20 ) // cear old
			{
				$arrt = array_shift($arr);
			}
			$newstr = '<font color=red>��ϵͳ���桿��ϲ���  '.$user['nickname'].'  �����  '.$user2['nickname'].'  ��ɷ���!</font>';
			foreach($arr as $k=>$v)
			{
				$retstr .= $v.'linend';
			}
			$retstr = $retstr.$newstr;
			$_pm['mem']->set( array('k'=>$msg_key, 'v'=>$retstr) );*/
		
		
		
		
		$user2		= $_pm['user']->getUserById($mergeid);
		$tt=date('Y-m-d H:m:s',time());
		$_pm['mysql']->query("insert into information(uid,times,content) values({$arrmerge['merge']},'{$tt}','��ҡ�{$user['nickname']}�����������������Ļ�������')");
		require_once(dirname(__FILE__).'/../socketChat/config.chat.php');
		$s=new socketmsg();
		$rs=$s->sendMsg(iconv('gbk','utf-8','SYSN|information-->'),array($arrmerge['uid']));
		$str = '��ϲ��'.$user['nickname'].'���͡�'.$user2['nickname'].'����Ϊ���ޣ�';
		$rs=$s->sendMsg(iconv('gbk','utf-8','an|'.$str));
		die("5");//chenggong
}elseif($type==4){ //����
	$err = 0;
	$user		= $_pm['user']->getUserById($_SESSION['id']);
	$bags		= $_pm['user']->getUserBagById($_SESSION['id']);
	del_bag_expire();
	$bid = intval($_REQUEST['pid']); // table: userbag -> id
	$n	 = intval($_REQUEST['n']);
	if($n>10){
		die('110');
	}
	$arr=$_pm['mysql']->getOneRecord("select * from player_ext WHERE uid = {$_SESSION['id']}"); 
	if($_SESSION['id']==$mergeid){
		die('17');
	}
	if($arr['merge']>0){
		die('4');//δ���
	}
	if($arr['request_merge']>0){
		die('5');//������Ѿ�����һ�������Ϣ
	}
	$arr1=$_pm['mysql']->getOneRecord("select request_merge from player_ext WHERE uid = {$mergeid}");
	if($arr1['request_merge']==$_SESSION['id']){
		die('18');//������Ѿ�����һ�������Ϣ
	}
	if($mergeid==0 || empty($mergeid)){
		die('6');
	}
	if($n <= 0)
	{
		unLockItem($bid);
		die('2');
	}
	
	if ($_pm['user']->check(array('int' => $bid, 'int' => $n)) === FALSE) {
		unLockItem($bid);
		die('2');
	}
	
	$wp = false;
	foreach ($bags as $k => $v)
	{
		if ($v['uid'] == $_SESSION['id'] && $v['id'] == $bid) 
		{
			$wp = $v; 
			break;
		}
	}
	if (!is_array($wp))
	{
		unLockItem($bid);
		die('3');
	}
	else if(!empty($wp['zbing']))
	{
		unLockItem($bid);
		die("10");//װ�������ϵĲ������͡�
	}
	else
	{
		if ($n > $wp['sums']) {
			unLockItem($bid);
			die('10');
		}
		if ($wp['vary'] == 2)	//	Can't repeat!
		{
			$_pm['mysql']->query("DELETE FROM userbag
						 WHERE uid={$_SESSION['id']} and id={$bid}
					  ");
		}
		else
		{	
			$_pm['mysql']->query("UPDATE userbag
						   SET sums=sums-{$n}
						 WHERE uid={$_SESSION['id']} and id={$bid} and sums>={$n}
					  ");
		}
		
		$send=$n.','.$wp['pid'];
		if(is_array($arr)){
			$sql = "UPDATE player_ext SET request=0,merge=0,request_merge={$mergeid},send='{$send}' WHERE uid = {$_SESSION['id']}";
			$_pm['mysql']->query($sql);
		}else{
			$_pm['mysql']->query("insert into player_ext(uid,request_merge,merge,request,send) values({$_SESSION['id']},{$mergeid},0,0,'{$send}')");
		}
	}
	
	
	
	
	//
	//��������ĳ������
	$tt=date('Y-m-d H:m:s',time());
	$_pm['mysql']->query("insert into information(uid,times,content) values({$mergeid},'{$tt}','��ҡ�{$user['nickname']}��������飡')");
	require_once(dirname(__FILE__).'/../socketChat/config.chat.php');
	$s=new socketmsg();
	$rs=$s->sendMsg(iconv('gbk','utf-8','SYSN|information-->'),array($mergeid));
	echo $err;
	unLockItem($bid);
}elseif($type==5){//ͬ���������
	$rvs=$_pm['mysql']->getOneRecord("select * from player_ext WHERE uid ={$mergeid} and merge={$_SESSION['id']} and request=1");
	if(is_array($rvs)){
		$sql = "UPDATE player_ext SET request=0,merge=0,request_merge=0,send='0' WHERE uid = {$_SESSION['id']} or uid={$mergeid}";
		if($_pm['mysql']->query($sql)){
		//
		//������ͬ��ĳ��ҵ��������
		
		$user_nickname		= $_pm['user']->getUserById($_SESSION['id']);
		$tt=date('Y-m-d H:m:s',time());
		$_pm['mysql']->query("insert into information(uid,times,content) values({$mergeid},'{$tt}','��ҡ�{$user_nickname['nickname']}��ͬ������������������')");
		require_once(dirname(__FILE__).'/../socketChat/config.chat.php');
		$s=new socketmsg();
		$rs=$s->sendMsg(iconv('gbk','utf-8','SYSN|information-->'),array($mergeid));
			die('1');
		}else{
			die('2');
		}
	}else{
		die('2');
	}
	
}elseif($type==6){ //ȡ������Ļ���
	$user		= $_pm['user']->getUserById($_SESSION['id']);
	$bags		= $_pm['user']->getUserBagById($_SESSION['id']);
	
		$sql = "select  send from player_ext WHERE uid = {$_SESSION['id']} and request_merge>0";
		$send=$_pm['mysql']->getOneRecord($sql);
		if(is_array($send)){
			$send1=explode(',',$send['send']);
			$bid=$send1[1];
			$n=$send1[0];
			$err = 0;
			$bagnum = 0;
			$mempropsid = unserialize($_pm['mem']->get('db_propsid'));
			$wp = $mempropsid[$bid];
			if ($wp['vary']==2) //���ܵ���
			{
				$_pm['mysql']->query("INSERT INTO userbag(uid,pid,sell,vary,sums,stime) VALUES({$user['id']},{$bid},{$wp['sell']},{$wp['vary']},1,unix_timestamp());");
			}
			else
			{
				$ret = $_pm['mysql']->getOneRecord("SELECT id FROM userbag WHERE uid={$_SESSION['id']} and pid={$bid} LIMIT 0,1");
				if(is_array($ret)){
						$_pm['mysql']->query("UPDATE userbag SET sums=sums+{$n}  WHERE uid={$_SESSION['id']} and id={$ret['id']} and sums+{$n}>0");
				}else{
					$_pm['mysql']->query("INSERT INTO userbag(uid,pid,sell,vary,sums,stime)
						VALUES({$user['id']},{$bid},{$wp['sell']},{$wp['vary']},{$n}, ".time()." );");
					  
				}
								
			}
		}else{ die("6");}
		$sql = "UPDATE player_ext SET request=0,merge=0,request_merge=0,send='0' WHERE uid = {$_SESSION['id']}";
		$_pm['mysql']->query($sql);
		$effectRow = mysql_affected_rows($_pm['mysql']->getConn());
		if($effectRow==1){
		//
		//������ȡ���˶�ĳ��ҵ����
		//
			die("1");//chenggong
		}else{
			die("6");//shibai
		}
}elseif($type==7){//�ܾ��Է��Ļ�������
	$sql="select request_merge,uid from player_ext where uid ={$mergeid} and request=0";
	$arr=$_pm['mysql']->getOneRecord($sql);
	if($arr['request_merge']==$_SESSION['id']){
			
			$user1		= $_pm['user']->getUserById($_SESSION['id']);
			$user2		= $_pm['user']->getUserById($arr['uid']);
			
			
		$sql = "UPDATE player_ext SET request=2 WHERE uid ={$mergeid} and request=0";
		$_pm['mysql']->query($sql);
		//
		//�����Ҿܾ��˶�ĳ��ҵ����
		$tt=date('Y-m-d H:m:s',time());
		$_pm['mysql']->query("insert into information(uid,times,content) values({$mergeid},'{$tt}','��ҡ�{$user1['nickname']}���ܾ��������飡')");
		require_once(dirname(__FILE__).'/../socketChat/config.chat.php');
		$s=new socketmsg();
		$rs=$s->sendMsg(iconv('gbk','utf-8','SYSN|information-->'),array($mergeid));
		die('1');
	}
}elseif($type==8){//����Ҿܾ�����Ӧ����ܾ���ȡ����Ʒ��ȡ����������
	$user		= $_pm['user']->getUserById($_SESSION['id']);
	$sql="select send from player_ext where uid ={$_SESSION['id']} and request=2";
	$send=$_pm['mysql']->getOneRecord($sql);
	if(is_array($send)){
		$send1=explode(',',$send['send']);
			$bid=$send1[1];
			$n=$send1[0];
			$err = 0;
			$bagnum = 0;
			$mempropsid = unserialize($_pm['mem']->get('db_propsid'));
			$wp = $mempropsid[$bid];
			if ($wp['vary']==2) //���ܵ���
			{
				$_pm['mysql']->query("INSERT INTO userbag(uid,pid,sell,vary,sums,stime) VALUES(
							{$user['id']},
							{$bid},
							{$wp['sell']},
							{$wp['vary']},
							1,
							unix_timestamp()
							);
						");
			}
			else
			{
				$ret = $_pm['mysql']->getOneRecord("SELECT id 
											FROM userbag
										   WHERE uid={$_SESSION['id']} and pid={$bid}
										   LIMIT 0,1
										");
				if(is_array($ret)){
						$_pm['mysql']->query("UPDATE userbag 
						   SET sums=sums+{$n} 
						 WHERE uid={$_SESSION['id']} and id={$ret['id']} and sums+{$n}>0
					  ");
				}else{
					$_pm['mysql']->query("INSERT INTO userbag(uid,pid,sell,vary,sums,stime)
						VALUES(
							   {$user['id']},
							   {$bid},
							   {$wp['sell']},
							   {$wp['vary']},
							   {$n},
							   ".time()."
							  );
					  ");
				}
								
			}
		$sql = "UPDATE player_ext SET request=0,merge=0,request_merge=0,send='0' WHERE uid = {$_SESSION['id']}";
		$_pm['mysql']->query($sql);
		//
		//������ȡ���˶���ҵĻ�������
		//
		
		die('1');
	}else{
		die('2');//�Է��Ѿ�ȡ���˶���ľܾ�
	}
}elseif($type==9){
	$sql="select request_merge from player_ext where uid ={$mergeid} and request=2";
	$arr=$_pm['mysql']->getOneRecord($sql);
	if($arr['request_merge']==$_SESSION['id']){
		$sql = "UPDATE player_ext SET request=0 WHERE uid ={$mergeid} and request=2";
		$_pm['mysql']->query($sql);
		//
		//������ȡ���˾ܾ�ĳ��ҵĻ�������
		//
		die('1');
	}
}elseif($type==10){
	$sql = "select merge,request from player_ext WHERE uid = {$_SESSION['id']} ";
	$mer=$_pm['mysql']->getOneRecord($sql);
	if($mer['merge']>0){
		if($mer['request']==1){
			die('4');//�Է��Ѿ�������������
		}
		$sql = "select request from player_ext WHERE uid = {$mer['merge']} ";
		$mer2=$_pm['mysql']->getOneRecord($sql);
		if($mer2['request']==1){
			die('14');
		}
		
		$sql = "UPDATE player_ext SET sj = sj - 5000,request=0,merge=0,request_merge=0 WHERE uid = {$_SESSION['id']} and sj >= 5000 ";
		$_pm['mysql']->query($sql);
		$effectRow = mysql_affected_rows($_pm['mysql']->getConn());
		if($effectRow==1){
			$sql = "UPDATE player_ext SET request=0,merge=0,request_merge=0 WHERE uid = {$mer['merge']}";
			$_pm['mysql']->query($sql);
		//
		//������ǿ����������
		$user		= $_pm['user']->getUserById($_SESSION['id']);
		$tt=date('Y-m-d H:m:s',time());
		$_pm['mysql']->query("insert into information(uid,times,content) values({$mer['merge']},'{$tt}','��ҡ�{$user['nickname']}��ǿ��������飡')");
		require_once(dirname(__FILE__).'/../socketChat/config.chat.php');
		$s=new socketmsg();
		$rs=$s->sendMsg(iconv('gbk','utf-8','SYSN|information-->'),array($mer['merge']));
			die('1');//���ɹ�
		}else{
			die('2');//ˮ������
		}	
	}else{
		die('3');//�Է��������޻�����ϵ
	}
}elseif($type==11){//��Ҿܾ��Է�������������
	$sql = "select merge,request from player_ext WHERE uid = {$_SESSION['id']} ";
	$mer=$_pm['mysql']->getOneRecord($sql);
	if($mer['merge']>0){
	
		$sql = "select merge,request from player_ext WHERE uid = {$mer['merge']} ";
		$mer1=$_pm['mysql']->getOneRecord($sql);
	
		if($mer1['request']==1){
			$sql = "UPDATE player_ext SET sj = sj + 2000,request=0 WHERE uid = {$mer['merge']} ";
			$_pm['mysql']->query($sql);
		
			//
			//������Ҿܾ�������
			$user		= $_pm['user']->getUserById($_SESSION['id']);
			$tt=date('Y-m-d H:m:s',time());
			$_pm['mysql']->query("insert into information(uid,times,content) values({$mer['merge']},'{$tt}','��ҡ�{$user['nickname']}���ܾ��������������2000ˮ�����ջأ�������ظ�������')");
			die('1');//��ܾ��˶Է����������
		}else{
			die('2');//��������������þܾ�
		}	
	}else{
		die('3');//�Է��������޻�����ϵ
	}
}

$_pm['mem']->memClose();



?>