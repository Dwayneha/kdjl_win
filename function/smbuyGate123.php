<?php
/*
*˵�������ϸ����������̣��е���Ҫ��ƽ̨������Ԫ�������е�ֱ������Ϸ�е�����˻��Ͽ۳�Ԫ�����й���
*By Huizheng Yu
*2009-04-17
*/
header('Content-Type:text/html;charset=GBK');
//die('ά���С���');
//exit();
require_once('../config/config.game.php');

$m	= $_pm['mem'];
$db = &$_pm['mysql'];
$u	= $_pm['user'];
secStart($m);
$err = 0;
define('HTTP_CONTENT_STARTED',"\r\n\r\n");
//---------------------------
//ͨ���ж�������ȷ���Ƿ���Ҫ��ƽ̨����Ԫ��
$Domain=explode('.',$_SERVER['HTTP_HOST']);
$DomainName1=$Domain[1];
$DomainName2=$Domain[2];
//---------------------------
$user	= $u->getUserById($_SESSION['id']);
$bags    = $u->getUserBagById($_SESSION['id']);
if($user===FALSE) {$err=1;}

$bid = intval($_REQUEST['bid']); // table: props => id
$n	 = intval($_REQUEST['n']); 

if(lockItem($bid) === false)
{
	die('�Ѿ��ڴ����ˣ�');
}

if($n <= 0)
{
	unLockItem($bid);
	die('2');
}

if( !is_int($bid) || $bid<1 || $n<1) $err = 2;

/*$wp= $m->dataGet(array('k' => MEM_PROPS_KEY, 
					   'v' => "if(\$rs['id'] == '{$bid}' && \$rs['yb']>0) \$ret=\$rs;"
				 ));*/
$wp = $_pm['mysql'] -> getOneRecord("SELECT * FROM props WHERE id = $bid and yb > 0");
//�����Զ����¼ܵĹ���
if(!empty($wp['timelimit'])){
	$limitarr = explode('|',$wp['timelimit']);
	$nowtime = date('YmdHi');
	if(!empty($limitarr[0]) && $nowtime < $limitarr[0]){
		unLockItem($bid);
		die('101');
	}
	if(!empty($limitarr[1]) && $nowtime > $limitarr[1]){
		unLockItem($bid);
		die('101');
	}
}
//�����Զ����¼ܵĹ������������
// Get current bag props num.
$bagnum = 0;
if (is_array($bags))
{
	foreach ($bags as $k => $v)
	{
		if ($v['sums']>0 && $v['zbing']==0) $bagnum++;
	}
}

if (!is_array($wp)) $err=3;
else if( ($wp['vary']==2 && ($n+$bagnum)>$user['maxbag']) || (($bagnum+1)>$user['maxbag']) ) $err=4;
else
{
	$price = $wp['yb']*$n;
	if(empty($price))
	{
		unLockItem($bid);
		die("3");
	}
	$nowCoin = $user['yb'];


	//--------------------------
	//���Ϊwebgame������
	//--------------------------
	if($DomainName1=='webgame'&&!preg_match('/pm51\d/is',$_SERVER['HTTP_HOST'])&&!preg_match('/kdjl\d/is',$_SERVER['HTTP_HOST']))
	{
		######ƽ̨���ܣ����ܽӿں�����
		require_once("../login/lib/passport.php");
		
		######ƽ̨�ӿ�ͨ�ýӿں�����
		require_once("../login/lib/nusoap.php");
		// ��ȡ���ʣ��Ԫ����
		$coinXml = queryCoin($_SESSION['username'],$_SESSION['licenseid']);
		//echo $coinXml;exit();
		$xmlarr = explode('Response10/Response', str_replace(array("<",">"),"",$coinXml));
		$nowCoin = 0;
		if (count($xmlarr)>1)
		{	
			$endpart = explode('coin_valid![CDATA[',$xmlarr[1]);
			if(count($endpart)>1)
			{
				$coinarr = explode('/coin_valid', $endpart[1]);
				$nowCoin=intval($coinarr[0]);
			} 
		}
		else{
			$nowCoin = 0;
		}
	}
	//--------------------------
	if ($price > $nowCoin)
	{
		$err='10'; // Money too less.
	}
	else
	{   
		//--------------------------
		//���Ϊwebgame������
		//--------------------------
		if($DomainName1=='webgame'&&!preg_match('/pm51\d/is',$_SERVER['HTTP_HOST'])&&!preg_match('/kdjl\d/is',$_SERVER['HTTP_HOST']))
		{
			######ƽ̨���ܣ����ܽӿں�����
			require_once("../login/lib/passport.php");
			
			######ƽ̨�ӿ�ͨ�ýӿں�����
			require_once("../login/lib/nusoap.php");
			// ����##################################################
			// $pay is xml document.
			
			$pay=payment($_SESSION['username'],
						$_SESSION['licenseid'],
						time().'_'.$wp['id'],
						$price,
						$_SESSION['username']."����ڴ������[1��]���� ".$wp['name']." {$n}����");
			if (strpos($pay,'<Response>10</Response>')===false)
			{
				//header('Content-Type:text/html;charset=GBK');
				 unLockItem($bid);
				 die("Ԫ��֧��ʧ��!");
			}
		}
		//-----------------------------
		
		//----------------------------
		//���Ϊ4399������
		//----------------------------
		if($DomainName2=='cn'&&($DomainName1=='youjia'||$DomainName1=='qq496'))
		{
			$host = str_replace("KD","",strtoupper(substr($_SERVER['HTTP_HOST'],0,strpos($_SERVER['HTTP_HOST'],'.'))));
			$ordid=$host.substr("000000000000".$_SESSION['id'],-9).substr(time(),-9);
			
			$now = time();
			$api_code = '4399_Pm_Gold_WCQmhS7FDvnv533b';
			$rflag = md5($_SESSION['userid'].'|'.urlencode($_SESSION['username']).'|'.'S1'.'|'.$price.'|'.$now.'|'.$api_code);
			$utype = urlencode(base64_encode(iconv("GBK","UTF-8",$wp['name'])));
			$number = $n;
			$desc = urlencode(base64_encode(iconv("GBK","UTF-8",$_SESSION['username']."����ڴ������[1��]���� ".$wp['name']." {$n}����")));
			$par = 'UserId='.$_SESSION['userid'].'&UserName='.$_SESSION['username'].'&ServerId=S1&UseGold='.$price.'&UseTime='.$now.'&flag='.$rflag.'&UseType='.$utype.'&Number='.$n.'&Desc='.$desc.'&orderid='.$ordid;
	
			//echo $par;
			//$rets = @file_get_contents("http://web.4399.com/api/kdjl/use_gold.php?".$par);
				
			//$rets = socketData("web.4399.com","/api/kdjl/use_gold.php?".$par);
			/*if (substr($rets,0,5) != 'true|' )
			{
				//ob_start();
				//header('Content-Type:text/html;charset=GBK');
				die('Ԫ��֧��ʧ�ܣ���');
				//ob_end_flush();
			}*/
			
			$requestNum = 0;
			for($i = 0; $i <= 4; $i++)
			{
				
				$a = time();
				$rets = socketData("web.4399.com","/api/kdjl/use_gold_order.php?".$par);
				//$rets = http_get_result("http://web.4399.com/api/kdjl/use_gold_order.php?".$par);
				$b = time();
				$cha = $b-$a;
				echo $cha;
				if($requestNum == 4 && strpos($rets,'true|') ===false)
				{	unLockItem($bid);
					if(strpos($rets,'false|') !==false)
					{
						die('�۷�ʧ�ܣ����Ժ����ԣ���');
					}else
					{
						die('������ϣ����Ժ��򣡣�');
					}
				}
				/* �ж϶������ظ��������Ѿ���Ǯ���ͼӵ��ߣ��������ѭ��,����ֵ��order_exist|true|1257309440|*/
				$array = explode("|",$rets);
				$time_exit = $array[2]+8;
				if(strpos($rets,'order_exist|') !==false && strpos($rets,'true|') !==false && time()<$time_exit)
				{
					break;	
				}
				/* �ɹ����շ�����Ϣ���ӵ���*/
				if(strpos($rets,'true|') !==false)
				{
					unLockItem($bid);
					break;		
				}
				else
				{
					$requestNum++;
					sleep(2);		
				}
				
			}

		}
		//----------------------------
		$now = time();
		$number = $n;
			
		$db->query("insert into yblog(title,nickname,yb,buytime,pname,nums)
				    values('{$ordid}����ڴ������[7��]����{$wp['name']} {$n} ��.','{$_SESSION['username']}','{$price}',unix_timestamp(),'{$wp['name']}',{$n})
				  "); // save buy log.
		
		######################################���������ӻ��� ̷� 11.10###########################################
		//���Ż��֣�����ۼ�����100Ԫ����1�֣�
		//��player�����������֣�score���ֶΣ������û�������useyb�ֶΣ������û�û�л�ȡ���ֵ�Ԫ��
		$useryb = $user['useyb'] + $price;//�ܵ����ѵ�Ԫ����
		$score = intval($useryb / 100);
		$useyb = intval($useryb % 100);
		#######################################�������������#######################################3

		######################################���������ӻ���� ̷� 1.20###########################################
		//���Ż��֣�����ۼ�����100Ԫ����1�֣�
		//��player�����������֣�score���ֶΣ������û����֣�����useyb�ֶΣ������û�û�л�ȡ���ֵ�Ԫ��
		$active_useybs = $user['active_useyb'] + $price;//�ܵ����ѵ�Ԫ����
		$active_score = intval($active_useybs / 100);
		$active_useyb = intval($active_useybs % 100);
		#######################################��������������#######################################3

		######################################����������vip ̷� 1.20###########################################
		//���Ż��֣�����ۼ�����100Ԫ����1�֣�
		//��player�����������֣�score���ֶΣ������û����֣�����useyb�ֶΣ������û�û�л�ȡ���ֵ�Ԫ��
		$vipybs = $user['vipyb'] + $price;//�ܵ����ѵ�Ԫ����
		$vip = intval($vipybs / 100);
		$vipyb = intval($vipybs % 100);
		#######################################��������������#######################################3

		$user['yb'] = $nowCoin-$price;
		
		#########################################################

		if ($wp['vary']==2) //���ܵ���
		{ 
			for ($i=0; $i<$n; $i++)
			{
			    $db->query("INSERT INTO userbag(uid,pid,sell,vary,sums,stime)
							VALUES(
								   {$user['id']},
								   {$bid},
								   {$wp['sell']},
									2,
								   1,
								   unix_timestamp()
								  );
						  ");
			}
		}
		else
		{		
			$arrobj = new arrays();
			$ret = false;
			if(is_array($bags))
			foreach($bags as $k=>$v)
			{
				if($v['uid']==$_SESSION['id'] && $v['pid']==$bid) $ret=$v;
			}
			
			if (is_array($ret))
			{

				$db->query("UPDATE userbag
							   SET sums=sums+{$n},stime=".time()."
							 WHERE id={$ret['id']}
						  ");
						  
			}
			else //create new data
			{
				$db->query("INSERT INTO userbag(uid,pid,sell,vary,sums,stime)
							VALUES(
								   {$user['id']},
								   {$bid},
								   {$wp['sell']},
									1,
									{$n},
								   unix_timestamp());
						  ");
						
			}
		}
		/*$db->query("update player set yb={$user['yb']},useyb={$useyb},score=score + {$score},active_useyb={$active_useyb},active_score=active_score+{$active_score} where id={$_SESSION['id']}");*/
		$db->query("update player set yb={$user['yb']},useyb={$useyb},score=score + {$score},vip = vip + {$vip},vipyb = {$vipyb},active_useyb={$active_useyb},active_score=active_score+{$active_score} where id={$_SESSION['id']}");
	}	// end inner else
}
unset($user,$wp);
$m->memClose();
echo $err;
unLockItem($bid);

function socketData($host,$url,$flag=false){
	$fp = @fsockopen($host, 80, $errno, $errstr, 30);
	if (!$fp) {
		return false;
	} else {
		$out = "GET /".$url." HTTP/1.1\r\n";
		$out .= "Host: ".$host."\r\n";
		$out .= "Connection: Close\r\n\r\n";
	
		fwrite($fp, $out);
		$rtn = "";
		while (!feof($fp)) {
			$rtn.= fgets($fp, 128);
		}
		fclose($fp);
	}	
	if($flag)
	{
		echo "\n\n\n".$rtn."\n\n\n";
	}
	$rtn = split(HTTP_CONTENT_STARTED,$rtn,2);
	$rtn = $rtn[1];
	return $rtn;
}
?>