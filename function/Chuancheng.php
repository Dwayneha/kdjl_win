<?
header('Content-Type:text/html;charset=GBK');
require_once('../config/config.game.php');
secStart($_pm['mem']);
$type=$_REQUEST['type'];
$bid=$_REQUEST['bid'];
if($bid){
	$cishu=$_pm['mysql']->getOneRecord("select chouqu_chongwu from player_ext where uid={$_SESSION['id']}");
	if(strpos($cishu['chouqu_chongwu'],','.$bid.',')!==false)
	{
		die("�����ȡ���ɳ�,���ܽ��д���!");
	}
}


if($type==1){  //����
	del_bag_expire();
	if($bid<0 or empty($bid)){
		die('1');//���ݴ���
	}
	if($bid==103 || $bid==104 || $bid==105){
		die('77');
	}
	$petsAll  = $_pm['user']->getUserPetById($_SESSION['id']);
	if(!is_array($petsAll)){
		die('3');
	}else{
		foreach($petsAll as $k=>$v){
				if($v['muchang']==3){
					die('4');//�Ѿ�����һ��������
				}elseif($v['muchang']==4){
					die('5');//������봫��
				}elseif($v['muchang']==5){
					die('6');//������Ӧ����
				}elseif($v['muchang']==6){
					die('7');//�������ڴ���
				}elseif($v['muchang']==7){
					die('8');//���ﴫ����ɣ���ȡ�س���
				}
		}
		foreach($petsAll as $k=>$v){
				if($v['id']==$bid && $v['muchang']==1  && $v['tgflag'] == 0){
					if($v['wx']!=6){ //�ĳ�$v['wx']!=6
						die('10');//�������
					}
					if($v['level']<90){
						die('11');//��������
					}
					if($v['czl']<60){
						die('13');//��������
					}
					if(!empty($v['zb'])){
						//die('250');0:
						$astr = explode(',',$v['zb']);
						if(is_array($astr)){
							foreach($astr as $va){
								$bstr = explode(':',$va);
								if(is_array($bstr)){
									$carr = $_pm['mysql'] -> getOneRecord("SELECT pid FROM userbag WHERE id = {$bstr[1]} AND sums > 0");
									if(is_array($carr)){
										$darr = $_pm['mysql'] -> getOneRecord("SELECT varyname FROM props WHERE id = {$carr['pid']}");
										if($darr['varyname'] == 9){
											die('250');
										}
									}
								}
							}
						}
					}
					$k=explode(",",$v['chchengcz']);
					if(is_array($k)){
						if($k[0]>=2 && ($v['czl']-$k[1])<10){
							die('12');//ÿ��10�ɳ�ֻ�ܴ���2��
						}
					}
					if(($v['chchengtime']+86400)>time()){
						die('13');//ÿ24Сʱֻ�ɴ���һ��
					}
					$sql="select chchengbb from player_ext where uid = {$_SESSION['id']}";
					$chchengarr=$_pm['mysql']->getOneRecord($sql);
					if($chchengarr['chchengbb']>0){
						$sql="select muchang,chchengbb,uid from userbb where id={$chchengarr['chchengbb']}";
						$chcbb11 = $_pm['mysql'] ->getOneRecord($sql);
						if($chcbb11['muchang']!=3 && $chcbb11['chchengbb']==0){
							$_pm['mysql']->query("update userbb set muchang=3 where uid = {$_SESSION['id']} and id={$bid}");
							$_pm['mysql']->query("UPDATE player_ext SET chchengbb=0 WHERE uid = {$_SESSION['id']} and chchengbb>0");
							//$sql="update userbb set muchang=3 where uid = {$_SESSION['id']} and id={$bid}";	
							die('78');//����Ѿ�ȡ��
						}elseif($chcbb11['muchang']!=3 && $chcbb11['chchengbb']>0 && $chcbb11['chchengbb']!=$bid){
							$_pm['mysql']->query("update userbb set muchang=3 where uid = {$_SESSION['id']} and id={$bid}");
							$_pm['mysql']->query("UPDATE player_ext SET chchengbb=0 WHERE uid = {$_SESSION['id']} and chchengbb>0");	
							die('79');//��������Ҽ��룬����ѡ���bb
						}
						
						
						//
						//����ĳĳ����ĳĳ���봫��
						$user_nickname		= $_pm['user']->getUserById($_SESSION['id']);
						$t=date('Y-m-d H:i:s',time());
						$_pm['mysql']->query("insert into information(uid,times,content) values({$chcbb11['uid']},'{$t}','��ҡ�{$user_nickname['nickname']}����������Ĵ��г���')");
						require_once(dirname(__FILE__).'/../socketChat/config.chat.php');
						$s=new socketmsg();
						$rs=$s->sendMsg(iconv('gbk','utf-8','SYSN|information-->'),array($chcbb11['uid']));
						$_pm['mysql']->query("update userbb set muchang=4,chchengbb={$chchengarr['chchengbb']} where uid = {$_SESSION['id']} and id={$bid}");
						$_pm['mysql']->query("update userbb set muchang=4,chchengbb={$bid} where  id={$chchengarr['chchengbb']}");
						die('15');//�ɼ���
					}
					
					
					
					
					$sql="update userbb set muchang=3 where uid = {$_SESSION['id']} and id={$bid}";
					if($_pm['mysql']->query($sql)){
						die('2');
					}else{
						die('3');
					}
				}
		}
		die('3');
	}
}elseif($type==2){//ȡ��
	if($bid<0 or empty($bid)){
		die('1');//���ݴ���
	}
		$sql="select muchang,chchengbb,chchengwp from userbb where  id={$bid}";//wx=6 and
		$u=$_pm['mysql'] ->getOneRecord($sql);
		$sql="select uid,muchang,chchengbb from userbb where  id={$u['chchengbb']}";//wx=6 and
		$u1=$_pm['mysql'] ->getOneRecord($sql);
		if($u['muchang']==3 && !empty($u['chchengwp'])){
				die('4');
		}
		if($u['muchang']==7 || $u['muchang']==3 ){
			$sql="update userbb set muchang=1,chchengbb=0,chchengwp='' where id={$bid}";
			$_pm['mysql']->query($sql);	
			$_pm['mysql']->query("UPDATE player_ext SET chchengbb=0 WHERE (uid = {$_SESSION['id']} or uid={$u1['uid']}) and chchengbb>0");		
			die('2');//�ɹ�ȡ��
		}elseif($u['muchang']==6){
			die('3');//���ڴ��У�����ȡ��
		}elseif($u['muchang']==5){
			die('4');
		}elseif($u['muchang']==4){
			if(!empty($u['chchengwp'])){
				die('4');
			}
			
			if($u1['chchengbb']==$bid){
			
				//
				//���� �Ҿܾ��������ĳĳ�ĳ��ﴫ�У��Ѿ�ȡ���˳���
				$user_nickname		= $_pm['user']->getUserById($_SESSION['id']);
				$tt=date('Y-m-d H:i:s',time());
				$_pm['mysql']->query("insert into information(uid,times,content) values({$u1['uid']},'{$tt}','��ҡ�{$user_nickname['nickname']}���Ѿ�ȡ���˳���,�ܾ�����ĳ��ﴫ�У�')");
				require_once(dirname(__FILE__).'/../socketChat/config.chat.php');
				$s=new socketmsg();
				$rs=$s->sendMsg(iconv('gbk','utf-8','SYSN|information-->'),array($u1['uid']));
				$sql="update userbb set chchengbb=0,muchang=3 where id={$u['chchengbb']}";
				$_pm['mysql']->query($sql);	
			}
			
			
			$sql="update userbb set muchang=1,chchengbb=0,chchengwp='' where id={$bid}";
			$_pm['mysql']->query($sql);	
			$_pm['mysql']->query("UPDATE player_ext SET chchengbb=0 WHERE (uid = {$_SESSION['id']} or uid={$u1['uid']}) and chchengbb>0");	
			die('2');//�ɹ�ȡ��
		}
}elseif($type==3){ //������ҵĿɴ��г����б�
	$petslist="";
	$sql="select id,name,level,czl from userbb where wx=6 and muchang=3 and uid <>{$_SESSION['id']}";
	$arr = $_pm['mysql'] ->getRecords($sql);
	if(is_array($arr)){
		foreach($arr as $k=>$rs){
		 $petslist .= '<table style="font-size:12px"><tr>
              			<td width="100px" onmouseout="mcbbdisplay();this.style.border=0;" style="cursor:pointer;text-align:center;" onmouseover="pos=1;mcbbshow('.$rs['id'].');this.style.border=\'solid 1px #DFD496\';"   onclick="sel(this);copyWord(\''.$rs['name'].'\');bid='.$rs['id'].';"><font color="'.$rs['chchengcolor'].'">'.$rs['name'].'</font></td>
              			<td style="text-align:center;" width="60px"> '.$rs['level'].'</td>
						<td style="text-align:center;" width="60px" >'.$rs['czl'].'</td>
						<td width="80px"  style="cursor:pointer;text-align:center;"  onmouseout="this.style.border=0;" style="cursor:pointer;text-align:center;" onmouseover="this.style.border=\'solid 1px #DFD496\';"  onmouseout="this.style.border=0;" onclick="sel(this);jinchuanc('.$rs['id'].');"><img src="../new_images/ui/add05.gif" border="0" /></td>
            		  </tr></table>';
		}
	}
	echo $petslist;
	exit;
}elseif($type==5){ //���������ҵĳ��ﵽ������
	$cwid=$_REQUEST['cwid']; //�ҵĳ���id
	if($bid<=0 || empty($bid)){
		die('1');//���ݴ���
	}
	
	if($cwid<=0 || $cwid==""){
		$sql="select uid from player_ext where uid = {$_SESSION['id']}";
		$arr1=$_pm['mysql']->getOneRecord($sql);
		if(is_array($arr1)){
			$sql = "UPDATE player_ext SET chchengbb={$bid} WHERE uid = {$_SESSION['id']}";
			$_pm['mysql']->query($sql);
		}else{
			$_pm['mysql']->query("insert into player_ext(uid,chchengbb) values({$_SESSION['id']},{$bid})");
		}
		die('2');
	}else{
		$sql = "UPDATE player_ext SET chchengbb=0 WHERE uid = {$_SESSION['id']} and chchengbb>0";
		$_pm['mysql']->query($sql);
	}
	$sql="select muchang,chchengbb from userbb where id={$cwid} ";//and wx=6
	$yes=$_pm['mysql']->getOneRecord($sql);
	$sql="select muchang,chchengbb,uid from userbb where id={$bid} ";//and wx=6
	$yes1=$_pm['mysql']->getOneRecord($sql);
	
	
	
	
	if($yes1['muchang']!=3){
		die('11');//��������Ѿ�ȡ�����Ѻ��������ﴫ����
	}
	
	
	
	if($yes['muchang']==3 && $yes1['muchang']==3){
	
		$sql="update userbb set muchang=4,chchengbb={$cwid} where uid <>{$_SESSION['id']} and id={$bid}  and muchang=3 ";
		$_pm['mysql']->query($sql);
		$sql="update userbb set muchang=4,chchengbb={$bid} where uid ={$_SESSION['id']} and id={$cwid}  and muchang=3 ";
		$_pm['mysql']->query($sql);
			die('2');
	}
	//elseif($yes['muchang']==4 && $yes['chchengbb']==0 &&  $yes1['muchang']==3){
	//	$sql="update userbb set muchang=4,chchengbb={$cwid} where uid <>{$_SESSION['id']} and id={$bid}  and muchang=3 ";
//		$_pm['mysql']->query($sql);
//		$sql="update userbb set chchengbb={$bid} where uid ={$_SESSION['id']} and id={$cwid}";
//		$_pm['mysql']->query($sql);
//			die('2');
//	}elseif($yes['muchang']==5 && $yes['chchengbb']==0 &&  $yes1['muchang']==3){
//		die('10');
//		$sql="update userbb set muchang=4,chchengbb={$cwid} where uid <>{$_SESSION['id']} and id={$bid}  and muchang=3 ";
//		$_pm['mysql']->query($sql);
//		$sql="update userbb set chchengbb={$bid} where uid ={$_SESSION['id']} and id={$cwid}";
//		$_pm['mysql']->query($sql);
//			die('2');
//	}
	elseif($yes['muchang']==6){
		die('3');//���ڴ��У����ܼ���
	}elseif($yes['muchang']==7){
		die('4');//������ɣ���ѡȡ�س��������
	}elseif($yes['muchang']==5){
		die('���Ѿ���Ӧ�������ĳ��ﴫ��');
	}elseif($yes['muchang']==4){
		die('���ж��󴫳���');
	}
	
	// ����  and tgflag=0
	
}elseif($type==6){
$merge_list="";
$sel=$_REQUEST['value'];
$ts=$_REQUEST["ts"];
if($ts=="ts"){
	$sel=$sel."";
}
$sql="select id from player where nickname='{$sel}'";
	$id=$_pm['mysql']->getOneRecord($sql);
	if(is_array($id)){ //�û��Ƿ����
		$sql="select request_merge,merge,request from player_ext where uid={$id['id']}";
		$arr=$_pm['mysql']->getOneRecord($sql);//���ҵ����Ƿ��л���	
		if(is_array($arr)){
			//$sql="select request_merge,merge from player_ext where uid={$_SESSION['id']}";
			//$arr2=$_pm['mysql']->getOneRecord($sql);
				if(is_array($arr) && $arr['request_merge']==0 && $arr['merge']==0){
					$merge_list="<tr id='t".$id['id']."' style='cursor:pointer;text-align:center;' onmouseover='this.style.border=\"solid 1px #DFD496\";'  onmouseout='this.style.border=0;' onclick='sel(this);mergeid=".$id['id'].";xy_qx();'>
							  <td width='100' align='center' >".$sel."</td>
							  <td width='70' align='left'>��</td>
							  <td width='100' align='left'>�����Ͷ�������</td>
							  </tr>";
				}elseif(is_array($arr) && $arr['request_merge']==$_SESSION['id'] && $arr['merge']==0){
					if($arr['request']==2 || $arr['request']==3){
						$merge_list="<tr id='t".$id['id']."' style='cursor:pointer;text-align:center;' onmouseover='this.style.border=\"solid 1px #DFD496\";'  onmouseout='this.style.border=0;' onclick='sel(this);xy=5;xy_qx();'>
							  <td width='100' align='center' >".$sel."</td>
							  <td width='100' align='center'>��</td>
							  <td width='100' align='center'>���Ѿܾ�</td>
							</tr>";
					}else{
						$merge_list="<tr id='t".$id['id']."' style='cursor:pointer;text-align:center;' onmouseover='this.style.border=\"solid 1px #DFD496\";'  onmouseout='this.style.border=0;' onclick='sel(this);merge_id=".$id['id'].";xy=4;xy_qx();'>
							  <td width='100' align='center' >".$sel."</td>
							  <td width='70' align='left'>��</td>
							  <td width='100' align='left'>�������</td>
							</tr>";
					}
					
				}elseif(is_array($arr) && $arr['request_merge']==0 && $arr['merge']>0){
					$usernickname		= $_pm['user']->getUserById($arr['merge']);
					$merge_list="<tr id='t".$id['id']."' style='cursor:pointer;text-align:center;' onmouseover='this.style.border=\"solid 1px #DFD496\";'  onmouseout='this.style.border=0;' onclick='sel(this);xy_qx();'>
							  <td width='100' align='center' >".$sel."</td>
							  <td width='70' align='left'>".$usernickname['nickname']."</td>
							  <td width='100' align='left'>�ѻ�</td>
							</tr>";
				}elseif(is_array($arr) && $arr['request_merge']!=$_SESSION['id'] && $arr['request_merge']>0 && $arr['merge']==0){
					$usernickname		= $_pm['user']->getUserById($arr['merge']);
					$merge_list="<tr id='t".$id['id']."' style='cursor:pointer;' onmouseover='this.style.border=\"solid 1px #DFD496\";'  onmouseout='this.style.border=0;' onclick='sel(this);xy_qx();'>
							  <td width='100' align='center' >".$sel."</td>
							  <td width='70' align='left'>".$usernickname['nickname']."</td>
							  <td width='100' align='left'>����������</td>
							</tr>";
				}
		}else{
		$merge_list="<tr id='t".$id['id']."' style='cursor:pointer;text-align:center;' onmouseover='this.style.border=\"solid 1px #DFD496\";'  onmouseout='this.style.border=0;' onclick='sel(this);mergeid=".$id['id'].";xy_qx();'>
						  <td width='100' align='center' >".$sel."</td>
						  <td width='70' align='left'>��</td>
						  <td width='100' align='left'>�����Ͷ�������</td>
						</tr>";
		}
	}
echo $id['id']."|".$merge_list;
}elseif($type==7){	
	$err=0;
	$cwid=$_REQUEST['cwid']; //�ҵĳ���id
	if($cwid<=0 || $cwid==""){
		die('1');//���ݴ���
	}
	$sql="select muchang,chchengbb,chchengcz,czl,chchengwp,ac,mc,srchp,hits,miss,speed,srcmp,level  from userbb where uid ={$_SESSION['id']} and id={$cwid}";//and wx=6
	$yes=$_pm['mysql']->getOneRecord($sql);
	$sql="select muchang,chchengcz,czl,chchengbb,uid,ac,mc,srchp,hits,miss,speed,srcmp,level  from userbb where uid <>{$_SESSION['id']} and id={$yes['chchengbb']}";//and wx=6
	$yes1=$_pm['mysql']->getOneRecord($sql);
	
	
	
	if(($yes['chchengbb']==0 || $yes1['chchengbb']!=$cwid) &&  $yes['muchang']!=7 && $yes['muchang']!=6){
		die('22');//�Է���ȡ��
	}
	
	if($yes['muchang']==4 && !empty($yes['chchengwp'])){
		$ccwp=explode(',',$yes['chchengwp']);
		if(!is_array($ccwp)){
			$zhu=$_REQUEST['zhu'];
			if(empty($zhu) || $zhu==0){
				die('50');//ѡ�񴫳���
			}
			$jn=$_REQUEST['jn'];
		}else{
			$zhu=$ccwp[0];
			$jn=$ccwp[1];
		}
	}elseif($yes['muchang']!=6 && $yes['muchang']!=5 && $yes['muchang']!=7){
		$zhu=$_REQUEST['zhu'];
		if(empty($zhu) || $zhu==0){
			die('50');//ѡ�񴫳���
		}
		$jn=$_REQUEST['jn'];
	//$jn1=10;//���ܱ�����ʼ����
	}

	if($yes['muchang']==7){
		die('9');//�����
	}elseif($yes['muchang']==5){
		if($yes1['muchang']==1 || $yes1['muchang']==3 || $yes1['chchengbb']!=$cwid){
			die('22');//�Է���ȡ��
		}
		//if(!empty($yes['chchengwp'])){
			//die('4');
		//}
		die('2');//dengdaizhong
	}elseif($yes['muchang']==6){
		die('3');//chuanchengzhong
	}elseif($yes['muchang']==4){
		if($yes1['muchang']==1 || $yes1['muchang']==3 || $yes1['chchengbb']!=$cwid){
			die('22');//�Է���ȡ��
		}
			$userBag	= $_pm['user']->getUserBagById($_SESSION['id']);
			if (is_array($userBag))
			{
				$jnn='';
				$ccc='';
				foreach($userBag as $k => $v1)
				{
					if($v1['varyname'] == 20 && $v1['effect']!='' && $v1['sums']>0 ){     //��Ʒ�������͡���������ת���õ���Ʒ
						$chuancname=explode(':',$v1['effect']);
						 if($chuancname[0]=='chuanc' && $v1['id']==$zhu){
							$ccc=$v1;
						 }elseif($chuancname[0]=='skills' && $v1['id']==$jn){
						 	$jnn=$v1;
						 }
					}
				}
			}
			if(!is_array($ccwp)){
				if(is_array($ccc) && is_array($jnn)){
					if($ccc['vary']==1){
						$_pm['mysql']->query("UPDATE userbag  SET sums=sums-1 WHERE uid={$_SESSION['id']} and id={$ccc['id']} and sums >= 1");
						$result = mysql_affected_rows($_pm['mysql'] -> getConn());
						if($result != 1){
							die("��û����Ӧ����Ʒ��");
						}
					}elseif($ccc['vary']==2){
						$_pm['mysql']->query("DELETE FROM userbag  WHERE uid={$_SESSION['id']} and id={$ccc['id']}");
					}
					if($jnn['vary']==1){
						$_pm['mysql']->query("UPDATE userbag  SET sums=sums-1 WHERE uid={$_SESSION['id']} and id={$jnn['id']} and sums >= 1");
						$result = mysql_affected_rows($_pm['mysql'] -> getConn());
						if($result != 1){
							die("��û����Ӧ����Ʒ��");
						}
					}elseif($jnn['vary']==2){
						$_pm['mysql']->query("DELETE FROM userbag  WHERE uid={$_SESSION['id']} and id={$jnn['id']}");
					}
					$_pm['mysql']->query("UPDATE userbb  SET chchengwp='{$ccc['pid']},{$jnn['pid']}' WHERE id={$cwid}");
				}elseif(is_array($ccc) && !is_array($jnn) ){
					if($ccc['vary']==1){
						$_pm['mysql']->query("UPDATE userbag  SET sums=sums-1 WHERE uid={$_SESSION['id']} and id={$ccc['id']} and sums >= 1");
						$result = mysql_affected_rows($_pm['mysql'] -> getConn());
						if($result != 1){
							die("��û����Ӧ����Ʒ��");
						}
					}elseif($ccc['vary']==2){
						$_pm['mysql']->query("DELETE FROM userbag  WHERE uid={$_SESSION['id']} and id={$ccc['id']}");
					}
					$_pm['mysql']->query("UPDATE userbb  SET chchengwp='{$ccc['pid']},' WHERE id={$cwid}");
				}else{
					die('50');
				}
			}
		//$sql="select muchang,chchengcz,czl  from userbb where uid <>{$_SESSION['id']} and id={$yes['chchengbb']}";//and wx=6
		//$yes1=$_pm['mysql']->getOneRecord($sql);
		if($yes1['muchang']==4 && $yes1['chchengbb']==$cwid){ 

		//
		//����ĳĳ������Ӧĳĳ�Ĵ���
		$user_nickname		= $_pm['user']->getUserById($_SESSION['id']);
		$tt=date('Y-m-d H:i:s',time());
		$_pm['mysql']->query("insert into information(uid,times,content) values({$yes1['uid']},'{$tt}','��ҡ�{$user_nickname['nickname']}�����ڵȴ���ȷ�ϴ��У�')");
		require_once(dirname(__FILE__).'/../socketChat/config.chat.php');
		$s=new socketmsg();
		$rs=$s->sendMsg(iconv('gbk','utf-8','SYSN|information-->'),array($yes1['uid']));
			$sql="update userbb set muchang=5 where uid ={$_SESSION['id']} and id={$cwid} ";	
			$_pm['mysql']->query($sql);
			die('4');//�ȴ��Է���Ӧ
		}elseif($yes1['muchang']==5 && $yes1['chchengbb']==$cwid){
			
		
			$t=time();
			$k1=explode(",",$yes['chchengcz']);
			$k2=explode(",",$yes1['chchengcz']);
			if(is_array($k1) && $k1[0]>0){
				if($k1[0]>=2){
				//die("k1[0]����2{$k1[0]},{$k1[1]}");
					$num1=1;
					$cz1=$yes['czl'];
				}else{
					//die("k1[0]С��2{$k1[0]},{$k1[1]}");
					$num1=$k1[0]+1;
					if(empty($k1[1])){
						$cz1=$yes['czl'];
					}else{
						$cz1=$k1[1];
					}
					//die("{$num1},{$cz1}");
				}
			}else{
			//die("k1���������k1[0]С��0{$k1[0]},{$k1[1]}");
				$num1=1;
				$cz1=$yes['czl'];
			}
			if(is_array($k2) && $k2[0]>0){
				if($k2[0]>=2){
					$num2=1;
					$cz2=$yes1['czl'];
				}else{
					$num2=$k1[0]+1;
					if(empty($k1[1])){
						$cz2=$yes1['czl'];
					}else{
						$cz2=$k1[1];
					}
				
				}
			}else{
				$num2=1;
				$cz2=$yes1['czl'];
			}
		//	die("�Է��ĳɳ��ʹ�������{$yes1['chchengcz']}|�ҵĳɳ��ʹ�������{$yes['chchengcz']}|�ҵĳɳ�{$yes['czl']}|�Է��ĳɳ�{$yes1[czl]}|�Է��Ĵ���{$num2}|�ҵĴ���{$num1}|�Է����һ�ν���Ĵ��гɳ�{$cz2}|�ҵ����һ�ν���Ĵ��гɳ�{$cz1}|�ҵ�{$k1[0]},{$k1[1]}|�Է���{$k2[0]},{$k2[1]}");
			//
			//����ĳĳ������ĳĳ�Ĵ���
			
		$zhu_bb_sx="{$yes['ac']},{$yes['mc']},{$yes['srchp']},{$yes['hits']},{$yes['miss']},{$yes['speed']},{$yes[srcmp]},{$yes[level]}";
		$fu_bb_sx="{$yes1['ac']},{$yes1['mc']},{$yes1['srchp']},{$yes1['hits']},{$yes1['miss']},{$yes1['speed']},{$yes1[srcmp]},{$yes1['level']}";
		$user_nickname		= $_pm['user']->getUserById($_SESSION['id']);
		$tt=date('Y-m-d H:i:s',time());
		$_pm['mysql']->query("insert into information(uid,times,content) values({$yes1['uid']},'{$tt}','��ҡ�{$user_nickname['nickname']}����������Ĵ�����������������Ѿ���ʼ�����ˣ�')");
		require_once(dirname(__FILE__).'/../socketChat/config.chat.php');
		$s=new socketmsg();
		$rs=$s->sendMsg(iconv('gbk','utf-8','SYSN|information-->'),array($yes1['uid']));
		$sql="update userbb set muchang=6,chchengtime={$t},chchengcz='{$num1},{$cz1}',chchengsx='{$fu_bb_sx}' where id={$cwid}";
		$_pm['mysql']->query($sql);
		$sql="update userbb set muchang=6,chchengtime={$t},chchengcz='{$num2},{$cz2}',chchengsx='{$zhu_bb_sx}' where id={$yes['chchengbb']}";
		$_pm['mysql']->query($sql);
		die('5');//���п�ʼ
		}
	}
}elseif($type==8){
	$cwid=intval($_REQUEST['cwid']); //�ҵĳ���id
	if($cwid<=0 || $cwid==""){
		die('1');//���ݴ���
	}
	$sql="select * from userbb where uid ={$_SESSION['id']} and id={$cwid}";//and wx=6
	$yes=$_pm['mysql']->getOneRecord($sql);
	//die("{$yes['muchang']}{$cwid}{$sql}");
	if($yes['muchang']==7){
		die('3');//�����
	}elseif($yes['muchang']==6){
		
		if($yes['level'] < 90 || $yes['czl'] < 60){
			die('��������');
		}
		$petsAlls	= unserialize($_pm['mem']->get(MEM_BB_KEY));
		$rs='';
		//$mainpet='';
		foreach($petsAlls as $k_pet=>$v_pet){
			if($v_pet['name']==$yes['name']){
				$rs=$v_pet;//����ԭʼ����
				break;
			}
		}
		
		
		$ccc='';
		$jnn='';
		
		$cwpp=explode(',',$yes['chchengwp']);//,123,
		/*$userBag	= $_pm['user']->getUserBagById($_SESSION['id']);
		if (is_array($userBag))
		{	
			foreach($userBag as $k => $v1)
			{
				if($v1['varyname'] == 20 && $v1['effect']!='' && $v1['sums']>0 ){    //��Ʒ�������͡���������ת���õ���Ʒ
				
					$chuancname=explode(':',$v1['effect']);
					echo $v1['pid'].'==>'.$cwpp[0].'<br />';
					//die("{$cwpp[0]}|{$_SESSION['id']} ");
					 if($chuancname[0]=='chuanc' &&$v1['pid']==$cwpp[0]){
					 echo __LINE__."----------------------------<br>";
						$ccc=$chuancname[1];//������
						//die("{$ccc}d");
					 }
					 if($chuancname[0]=='skills' && $v1['pid']==$cwpp[1]){echo __LINE__."-------------------------<br>";
						$jnn=$chuancname[1];//���ܱ���
					 }
				}
			}
		}*/
	$carr = $_pm['mysql'] -> getOneRecord("SELECT effect FROM props WHERE id = $cwpp[0]");
	if(is_array($carr)){
		$ef = explode(':',$carr['effect']);
		if($ef[0] == 'chuanc'){
			$ccc = $ef[1];
		}
	}
	$ccarr = $_pm['mysql'] -> getOneRecord("SELECT effect FROM props WHERE id = $cwpp[1]");
	if(is_array($ccarr)){
		$eff = explode(':',$ccarr['effect']);
		if($eff[0] == 'skills'){
			$jnn = $eff[1];
		}
	}
		//echo $yes['chchengsx'];
	$yes2=explode(",",$yes['chchengsx']);//����������
	//var_dump($yes2);
	//$ccc=$ccc/100;
	
	if(empty($ccc) || $ccc == 0 || $ccc == ''){die('û�д�����');}
	if($_REQUEST['t']==1){
		$sj=floor((86400-time()+$yes['chchengtime'])/60);
		if($sj > 0){
			die("������ɽ�������{$sj}ˮ����");//���
		}
	}elseif($_REQUEST['t']==2){
		$sj=floor((86400-time()+$yes['chchengtime'])/60);
		$sql = "UPDATE player_ext SET sj = sj -{$sj}  WHERE uid = {$_SESSION['id']} and sj >= {$sj}";
		$_pm['mysql']->query($sql);
		$effectRow = mysql_affected_rows($_pm['mysql']->getConn());
		if($effectRow!=1){
			die('10');//ˮ������
		}
	}
	/*$new_pet['ac']=floor(($rs['ac']+$yes['ac']*0.08+($yes2[0])*0.05)*($ccc+0.5));
	$new_pet['mc']=floor(($rs['mc']+$yes['mc']*0.08+($yes2[1])*0.05)*($ccc+0.5));
	$new_pet['srchp']=floor(($rs['hp']+$yes['srchp']*0.08+($yes2[2])*0.05)*($ccc+0.5));
	$new_pet['hits']=floor(($rs['hits']+$yes['hits']*0.08+($yes2[3])*0.05)*($ccc+0.5));
	$new_pet['miss']=floor(($rs['miss']+$yes['miss']*0.08+($yes2[4])*0.05)*($ccc+0.5));
	$new_pet['speed']=floor(($rs['speed']+$yes['speed']*0.08+($yes2[5])*0.05)*($ccc+0.5));
	$new_pet['srcmp']=floor(($rs['mp']+$yes['srcmp']*0.08+($yes2[6])*0.05)*($ccc+0.5));*/
	$new_pet['ac']=intval(($rs['ac']+(intval($yes['ac']*$yes['level']/400)+intval($yes2[0]*$yes2[7]/800)))*$ccc);
	$new_pet['mc']=intval(($rs['mc']+(intval($yes['mc']*$yes['level']/400)+intval($yes2[1]*$yes2[7]/800)))*$ccc);
	$new_pet['srchp']=intval(($rs['hp']+(intval($yes['srchp']*$yes['level']/400)+intval($yes2[2]*$yes2[7]/800)))*$ccc);
	$new_pet['hits']=intval(($rs['hits']+(intval($yes['hits']*$yes['level']/400)+intval($yes2[3]*$yes2[7]/800)))*$ccc);
	$new_pet['miss']=intval(($rs['miss']+(intval($yes['miss']*$yes['level']/400)+intval($yes2[4]*$yes2[7]/800)))*$ccc);
	$new_pet['speed']=intval(($rs['speed']+(intval($yes['speed']*$yes['level']/400)+intval($yes2[5]*$yes2[7]/800)))*$ccc);
	$new_pet['srcmp']=intval(($rs['mp']+(intval($yes['srcmp']*$yes['level']/400)+intval($yes2[6]*$yes2[7]/800)))*$ccc);
	
	$add_pet['ac']=$new_pet['ac']-$yes['ac'];
	$add_pet['mc']=$new_pet['mc']-$yes['mc'];
	$add_pet['srchp']=$new_pet['srchp']-$yes['srchp'];
	$add_pet['hits']=$new_pet['hits']-$yes['hits'];
	$add_pet['miss']=$new_pet['miss']-$yes['miss'];
	$add_pet['speed']=$new_pet['speed']-$yes['speed'];
	$add_pet['srcmp']=$new_pet['srcmp']-$yes['srcmp'];
	
	if(!empty($jnn)){
		$save=round(10+100/$jnn);
	}else{
		$save=10;
	}
	$randskill=rand(1,100);
	if($randskill<=$save){
		$sk=$yes['skillist'];
	}else{
		$sk="1:1";
	}
	if(!empty($yes['addsx'])){
		$cchnums=explode(',',$yes['addsx']);
		$cchnums1=$cchnums[7]+1;
	}else{
		$cchnums1=1;
	}
	
	/*if($cchnums1 > 2){
		die('����������������');
	}*/
	
	//������־��¼
	/*$log .= $new_pet['ac'].'=floor(('.$rs['ac'].'+'.$yes['ac'].'*0.08+('.$yes2[0].')*0.05)*('.$ccc.'+0.5));'.
	$new_pet['mc'].'=floor(('.$rs['mc'].'+'.$yes['mc'].'*0.08+('.$yes2[1].')*0.05)*('.$ccc.'+0.6));'.
	$new_pet['srchp'].'=floor(('.$rs['hp'].'+'.$yes['srchp'].'*0.08+('.$yes2[2].')*0.05)*('.$ccc.'+0.5));'.
	$new_pet['hits'].'=floor(('.$rs['hits'].'+'.$yes['hits'].'*0.08+('.$yes2[3].')*0.05)*('.$ccc.'+0.5));'.
	$new_pet['miss'].'=floor(('.$rs['miss'].'+'.$yes['miss'].'*0.08+('.$yes2[4].')*0.05)*('.$ccc.'+0.5));'.
	$new_pet['speed'].'=floor(('.$rs['speed'].'+'.$yes['speed'].'*0.08+('.$yes2[5].')*0.05)*('.$ccc.'+0.5));'.
	$new_pet['srcmp'].'=floor(('.$rs['mp'].'+'.$yes['srcmp'].'*0.08+('.$yes2[6].')*0.05)*('.$ccc.'+0.5));'.date('Y-m-d H:i:s');*/
	$log .= $new_pet['ac'].'=intval(('.$rs['ac'].'+(intval('.$yes['ac'].'*'.$yes['level'].'/400)+intval('.$yes2[0].'*'.$yes2[7].'/800)))*'.$ccc.');'.
	$new_pet['mc'].'=intval(('.$rs['mc'].'+(intval('.$yes['mc'].'*'.$yes['level'].'/400)+intval('.$yes2[1].'*'.$yes2[7].'/800)))*'.$ccc.');'.
	$new_pet['srchp'].'=intval(('.$rs['hp'].'+(intval('.$yes['srchp'].'*'.$yes['level'].'/400)+intval('.$yes2[2].'*'.$yes2[7].'/800)))*'.$ccc.');'.
	$new_pet['hits'].'=intval(('.$rs['hits'].'+(intval('.$yes['hits'].'*'.$yes['level'].'/400)+intval('.$yes2[3].'*'.$yes2[7].'/800)))*'.$ccc.');'.
	$new_pet['miss'].'=intval(('.$rs['miss'].'+(intval('.$yes['miss'].'*'.$yes['level'].'/400)+intval('.$yes2[4].'*'.$yes2[7].'/800)))*'.$ccc.');'.
	$new_pet['speed'].'=intval(('.$rs['speed'].'+(intval('.$yes['speed'].'*'.$yes['level'].'/400)+intval('.$yes2[5].'*'.$yes2[7].'/800)))*'.$ccc.');'.
	$new_pet['srcmp'].'=intval(('.$rs['mp'].'+(intval('.$yes['srcmp'].'*'.$yes['level'].'/400)+intval('.$yes2[6].'*'.$yes2[7].'/800)))*'.$ccc.');'.date('Y-m-d H:i:s');
	$time = time();
	$_pm['mysql'] -> query("INSERT INTO gamelog (ptime,seller,buyer,pnote,vary) VALUES ($time,{$_SESSION['id']},{$_SESSION['id']},'$log',238)");
	
	$sql="update userbb set level = 1,ac='{$new_pet['ac']}',mc='{$new_pet['mc']}',nowexp=0,lexp=170,skillist = '$sk',srchp='{$new_pet['srchp']}',hits='{$new_pet['hits']}',srcmp='{$new_pet['srcmp']}',miss='{$new_pet['miss']}',speed='{$new_pet['speed']}',muchang='7',addsx='{$add_pet['ac']},{$add_pet['mc']},{$add_pet['srchp']},{$add_pet['hits']},{$add_pet['miss']},{$add_pet['speed']},{$add_pet['srcmp']},{$cchnums1}',chchengwp='',chchengwp='',chchengcolor='#FF66CC',chchengsx='' where id={$cwid} and muchang=6";
	
	
	$_pm['mysql']->query($sql);
	
	//��־����
	/*1.����ǰ����ֻ���������
	2.���к��õ��³������������ID��
	3.�����ӵ���ƷID
	4.����ʱ��
	5.�����漰���������ID
	*/
	$str = '�Լ�������Ϣ��'.$yes['id'].'id-'.$yes['name'].'name-'.$yes['level'].'level-'.$yes['czl'].'czl-'.$yes['srchp'].'srchp-'.$yes['srcmp'].'srcmp-'.$yes['ac'].'ac-'.$yes['hits'].'hits-'.$yes['miss'].'miss-'.$yes['speed'].'speed-'.'�Է�������Ϣ��'.$yes['chchengsx'].'ȡ��ʱ�䣺'.date('Y-m-d H:i:s').'����ʱ�䣺'.date('Y-m-d H:i:s',$yes['chchengtime']).'������Ʒ��'.$yes['chchengwp'].'��ӵ����bb��id��'.$yes['chchengbb'];
	$_pm['mysql'] -> query("INSERT INTO gamelog (ptime,seller,buyer,pnote,vary) VALUES ($time,{$_SESSION['id']},{$_SESSION['id']},'$str',235)");
	die('2');
	}
	die('4');
	
}elseif($type==9){
	$ser=$_REQUEST['txt'];
	$petslist="";
	if(trim($ser)==""){
		die('3');
	}
	$sql="select id,name,level,czl from userbb where wx=6 and muchang=3 and uid <>{$_SESSION['id']} and name='{$ser}' ";
	//die("select id,name,level,czl from userbb where wx=6 and muchang=3 and uid <>{$_SESSION['id']} and name='{$ser}'");
	$arr = $_pm['mysql'] ->getRecords($sql);
	if(is_array($arr)){
		foreach($arr as $k=>$rs){
		 $petslist .= '<table style="font-size:12px"><tr>
              			<td width="100px" onmouseout="mcbbdisplay();this.style.border=0;" style="cursor:pointer;text-align:center;" onmouseover="pos=1;mcbbshow('.$rs['id'].');this.style.border=\'solid 1px #DFD496\';"   onclick="sel(this);copyWord(\''.$rs['name'].'\');bid='.$rs['id'].';">'.$rs['name'].'</td>
              			<td style="text-align:center;" width="60px"> '.$rs['level'].'</td>
						<td style="text-align:center;" width="60px" >'.$rs['czl'].'</td>
						<td width="80px"  style="cursor:pointer;text-align:center;"  onmouseout="this.style.border=0;" style="cursor:pointer;text-align:center;" onmouseover="this.style.border=\'solid 1px #DFD496\';"  onmouseout="this.style.border=0;" onclick="sel(this);jinchuanc('.$rs['id'].');"><img src="../new_images/ui/add05.gif" border="0" /></td>
            		  </tr></table>';
		}
	echo $petslist;
	exit;	
	}else{
		die('2');
	}
}elseif($type==10){
		$sql="select chchengwp,chchengbb from userbb where  id={$bid}";//wx=6 and
		$u=$_pm['mysql'] ->getOneRecord($sql);
		
		$sql="select uid,chchengbb from userbb where  id={$u['chchengbb']}";//wx=6 and
		$u1=$_pm['mysql'] ->getOneRecord($sql);
		if($u1['chchengbb']==$bid){
			
			//
			//���� �Ҿܾ��������ĳĳ�ĳ��ﴫ�У��Ѿ�ȡ���˳���
		$user_nickname		= $_pm['user']->getUserById($_SESSION['id']);
		$tt=date('Y-m-d H:i:s',time());
		$_pm['mysql']->query("insert into information(uid,times,content) values({$u1['uid']},'{$tt}','��ҡ�{$user_nickname['nickname']}���Ѿ�ȡ���˳���,�ܾ�����ĳ��ﴫ�У�')");
			require_once(dirname(__FILE__).'/../socketChat/config.chat.php');
			$s=new socketmsg();
			$rs=$s->sendMsg(iconv('gbk','utf-8','SYSN|information-->'),array($u1['uid']));
			$sql="update userbb set chchengbb=0,muchang=3 where id={$u['chchengbb']}";
			$_pm['mysql']->query($sql);	
		}
		$cwp=explode(',',$u['chchengwp']);
		
			$bagnum=0;
			$user		= $_pm['user']->getUserById($_SESSION['id']);
			$userBag	= $_pm['user']->getUserBagById($_SESSION['id']);
			if (is_array($userBag))
			{
					foreach ($userBag as $k => $v)
					{
						if ($v['sums']>0 && $v['zbing']==0) $bagnum++;
					}
					unset($userBag);
			}
			//die("{$bagnum}");
			
			$mempropsid = unserialize($_pm['mem']->get('db_propsid'));
			$wp1 = $mempropsid[$cwp[0]];
			if( ($wp1['vary']==2 && ($n+$bagnum)>$user['maxbag']) || (($bagnum+1) > $user['maxbag']) ) 
				echo "100";
	
			else
			{
				if ($wp1['vary']==2) //���ܵ���
				{
					$_pm['mysql']->query("INSERT INTO userbag(uid,pid,sell,vary,sums,stime) VALUES({$user['id']},{$cwp[0]},{$wp1['sell']},{$wp1['vary']},1,unix_timestamp());");
				}
				else
				{
					$ret = $_pm['mysql']->getOneRecord("SELECT id FROM userbag WHERE uid={$_SESSION['id']} and pid={$cwp[0]} LIMIT 0,1");
					//die("SELECT id FROM userbag WHERE uid={$_SESSION['id']} and pid={$cwp[0]} LIMIT 0,1");
					if(is_array($ret)){
							$_pm['mysql']->query("UPDATE userbag SET sums=sums+1  WHERE uid={$_SESSION['id']} and id={$ret['id']} and sums+1>0");
							
					}else{
						$_pm['mysql']->query("INSERT INTO userbag(uid,pid,sell,vary,sums,stime)
							VALUES({$user['id']},{$cwp[0]},{$wp1['sell']},{$wp1['vary']},1, ".time()." );");
						  
					}
									
				}
			}
		
			if(!empty($cwp[1])){
				$wp2 = $mempropsid[$cwp[1]];
				if( ($wp2['vary']==2 && ($n+$bagnum)>$user['maxbag']) || (($bagnum+1) > $user['maxbag']) ) 
				echo "101";
				else
				{
					if ($wp2['vary']==2) //���ܵ���
					{
						$_pm['mysql']->query("INSERT INTO userbag(uid,pid,sell,vary,sums,stime) VALUES({$user['id']},{$cwp[1]},{$wp2['sell']},{$wp2['vary']},1,unix_timestamp());");
					}
					else
					{
						$ret1 = $_pm['mysql']->getOneRecord("SELECT id FROM userbag WHERE uid={$_SESSION['id']} and pid={$cwp[1]} LIMIT 0,1");
						if(is_array($ret1)){
								$_pm['mysql']->query("UPDATE userbag SET sums=sums+1  WHERE uid={$_SESSION['id']} and id={$ret1['id']} and sums+1>0");
								//die("UPDATE userbag SET sums=sums+1  WHERE uid={$_SESSION['id']} and id={$ret1['id']} and sums+1>0");
						}else{
							$_pm['mysql']->query("INSERT INTO userbag(uid,pid,sell,vary,sums,stime)
								VALUES({$user['id']},{$cwp[1]},{$wp2['sell']},{$wp2['vary']},1, ".time()." );");
							  
						}
										
					}
				}
				
			}
			//$sql="update userbb set muchang=3,chchengbb=0 where id={$u['chchengbb']}";
			//$_pm['mysql']->query($sql); //��ɴ��У�ȡ�س���
			$sql="update userbb set muchang=1,chchengbb=0,chchengwp='' where id={$bid}";
			$_pm['mysql']->query($sql);
			$_pm['mysql']->query("UPDATE player_ext SET chchengbb=0 WHERE (uid = {$_SESSION['id']} or uid={$u1['uid']}) and chchengbb>0");
			die('4');
}	
$_pm['mem']->memClose();
?>
