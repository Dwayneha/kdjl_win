<?php
require_once('../config/config.game.php');
secStart($_pm['mem']);
header('Content-Type:text/html;charset=gbk');
$csForbiden = array(3160, 3121, 3120, 3119, 3118, 2766, 2763, 2714, 2713, 2712, 2711, 2710, 2709, 2708, 2707, 2706, 2705, 2704, 2703, 2702, 2701, 2700, 2699, 2698, 2697, 2696, 2695, 2694, 2693, 2692, 2691, 2690, 2689, 2688, 2687, 2686, 2685, 2684, 2683, 2682, 2628, 2624, 2623, 2622, 2621, 2620, 2614, 2613, 2612, 2611, 2610, 2609, 2572, 2571, 2570, 2569, 2568, 2567, 2566, 2565, 2564, 2563, 2562, 2560, 2481, 2456, 2413, 2408, 2407, 2406, 2389, 2388, 2387, 2386, 2385, 2313, 2235, 2213, 2207, 2206, 2205, 2204, 2179, 2162, 2147, 2146, 2145, 2144, 2143, 2142, 1972, 1963, 1962, 1961, 1719, 1697, 1696, 1653, 1647, 1574, 1573, 1572, 1571, 1438, 1437, 1424, 1423, 1414, 1326, 1324, 1217, 1163, 1142, 1141, 1137, 1136, 1105, 1104, 914, 913, 912);
$user = $_pm['user']->getUserById($_SESSION['id']);
$bags = $bag = $_pm['user']->getUserBagById($_SESSION['id']);
$id = intval($_REQUEST['id']); // userbag id

if (isset($_GET['js']) && isset($_GET['pid'])) {
	$__pid = intval($_GET['pid']);
	$sidrow = $_pm['mysql']->getOneRecord('select id from userbag where uid=' . $_SESSION['id'] . ' and pid=' . $__pid . ' and sums>0');
	if (!$sidrow) {
		die('�����ħ��ʯ���޷������ͷ�ħ����Ҫ��ħ��T_T�´������ɡ�');
	}
	$id = $sidrow['id'];
}

if ($id < 1 || !is_array($bags)) die('��Ʒ������!');
del_bag_expire();
if (lockItem($id) === false) {
	unLockItem($id);
	die("�Ѿ��ڴ�����");
}


// �������
//if ($_REQUEST['op'] == 'reset') {
//    echo '������ɣ�';
//    unLockItem($id);
//    exit();
//}


foreach ($bags as $k => $v) {
	if ($v['id'] == $id && $v['uid'] == $_SESSION['id'] && $v['sums'] > 0 && $v['zbing'] == 0) {
		$rs = $v;
		break;
	}
}
// main bb for user.
$bb = $_pm['mysql']->getOneRecord("SELECT * FROM userbb
						  WHERE id={$user['mbid']} and uid={$_SESSION['id']} 
						  LIMIT 0,1
						");

if (!is_array($rs)) {
	unLockItem($id);
	die("û�з��������Ʒ��");
}

// if is zb,used it!
// if is zb,used it!
if ($rs['varyname'] == 9)    //װ��ϵͳ��
{
	if (is_array($bb)) {
		// Check �Ƿ���ϱ���Ҫ��
		if ($rs['requires'] != '') {
			$arr = explode(',', $rs['requires']);
			if (is_array($arr)) {
				foreach ($arr as $v) {
					if (!empty($v)) {
						$newarr = explode(":", $v);
						if ($newarr[0] == 'lv') {
							$tlv = $newarr[1];
						} else if ($newarr[0] == 'wx' && !empty($newarr[1])) {
							$twx = $newarr[1];
						}
					}
				}
			}
			if (!empty($twx) && $twx != $bb['wx']) {
				unLockItem($id);
				die('�������в�ƥ��!');
			} else if (!empty($tlv) && $tlv > $bb['level']) {
				unLockItem($id);
				die('�����ȼ�����!');
			}
			/*$lv  = explode(':', $arr[0]);
            if ($lv[0] == "lv") $tlv = $lv[1];
            else if($lv[0] == "wx") $twx = $lv[1];

            if($arr[1] != '')
            {
                $wx = explode(':', $arr[1]);
                if ($wx[0] == "lv") $tlv = $wx[1];
                else if($wx[0] == "wx") $twx = $wx[1];
            }

            if ($twx!= $bb['wx'] || $tlv>$bb['level']) die('�����ȼ����������в�ƥ��!');*/
		}

		// Ensure props attrib is ok
		if (!isset($rs['postion']) || $rs['postion'] == '') {
			/*$prs = $_pm['mem']->dataGet(array('k'	=>	MEM_PROPS_KEY,
                                     'v'	=>	"if(\$rs['id'] == '{$rs['pid']}') \$ret=\$rs;"
                    ));*/
			$mempropsid = unserialize($_pm['mem']->get('db_propsid'));
			$grs = $mempropsid[$rs['pid']];
			$rs['postion'] = $prs['postion']; // Fix postion.
			unset($prs);
		}

		if (strlen($bb['zb']) < 2) {
			$bb['zb'] = $rs['postion'] . ':' . $rs['id'];
		} else {
			if (strstr($bb['zb'], ",")) {
				$zb = split(',', $bb['zb']); // format: p:id,p:id
				$new = '';
				$rpl = 0;
				foreach ($zb as $k => $v) {
					$arr = explode(':', $v);
					if ($arr[0] == $rs['postion']) // �滻��Ӧװ����
					{
						$new .= ',' . $arr[0] . ':' . $id;
						$oldid = $arr[1];
						$rpl = 1;
					} else $new .= ',' . $v;
				}
				$bb['zb'] = substr($new, 1);
				if (!$rpl) $bb['zb'] .= ',' . $rs['postion'] . ':' . $rs['id'];
			} else {
				$arr = explode(':', $bb['zb']);
				if ($arr[0] == $rs['postion']) // �滻��Ӧװ����
				{
					$bb['zb'] = $arr[0] . ':' . $rs['id'];
					$oldid = $arr[1];
				} else $bb['zb'] = $bb['zb'] . ',' . $rs['postion'] . ':' . $rs['id'];
			}
		}

		/**Find current postion zb clear zb tag.*/
		$clearlist = '';
		foreach ($bags as $k => $v) {
			if ($v['postion'] == $rs['postion'] and $v['zbing'] != 0 and $v['zbpets'] != 0 and $v['zbpets'] == $bb['id']) {
				$clearlist .= $clearlist ? ',' . $v['id'] : $v['id'];
			}
		}


		$_pm['mysql']->query("UPDATE userbb 
					   SET zb='{$bb['zb']}'
					 WHERE id={$user['mbid']}
				  ");
		if (!empty($clearlist) && $clearlist != '') {
			$_pm['mysql']->query("UPDATE userbag 
						   SET zbing=0,zbpets=0 
						 WHERE id in ({$clearlist})
					 ");
		}
		//echo $user['mbid'];exit;

		$_pm['mysql']->query("UPDATE userbag 
					   SET zbing=1,zbpets={$user['mbid']}
					 WHERE id={$id}
				  ");
		formatMsgEffect($user['mbid']);
		$memeffect = unserialize($_pm['mem']->get('format_user_zhuangbei_' . $user['mbid']));
		//echo $memeffect.'----->'.__LINE__.'<br />';
		//�趨װ���仯��־
		$_pm['mem']->set(array("k" => "User_bb_equip_changed_" . $user['mbid'] . '_' . $_SESSION['id'], "v" => 1));
		//$_SESSION['dbg_equip_attr2'] .= "Right here 2!<br>";
		unLockItem($id);
		die('��ϲ����װ���ɹ���');
	} else {
		unLockItem($id);
		die('����û��������ս���������ܽ���װ����');
	}
}
else if ($rs['varyname'] == 28)    //�齱����
{
	//session��
	$key = 'user_chou_' . $_SESSION['id'];
	if (!isset($_SESSION[$key])) {
		$_SESSION[$key] = time();
	} else {
		// sleep(3);
		realseLock();
		unset($_SESSION[$key]);
		die('��������æ�����Ժ����ԣ�');
	}
	$r = $_pm['mysql']->getOneRecord("SELECT sums FROM userbag WHERE uid = " . $_SESSION['id'] . " AND pid = 3965 ");
	if ($r['sums'] < 1) {
		//sleep(3);
		realseLock();
		unset($_SESSION[$key]);
		die('��������æ�����Ժ����ԣ�');
	}
	require_once('../sec/dblock_fun.php');
	$a = getLock($_SESSION['id']);
	if (!is_array($a)) {
		realseLock();
		die('��������æ�����Ժ����ԣ�');
	}//echo __LINE__."<br>";
	require_once('../api/curl.php');
	$url = "http://pmmg1.webgame.com.cn/interface/use_ticket.php";
	$area = explode('.', $_SERVER['HTTP_HOST']);
	$data['area'] = $area[0];
	$data['username'] = $_SESSION['username'];
	$data['nickname'] = $_SESSION['nickname'];
	$luck_return = curl_post($url, $data);
	if ($luck_return == "no inter") {
		die("��ƽ̨δ����");
	} elseif ($luck_return == 'today_end') {
		die("���ճ齱�Ѿ�����");
	} elseif ($luck_return == 'end') {
		die("���ճ���Ѿ����");
	}
	$return_info = explode('|', $luck_return);
	if ($return_info[0] != 'ok') {
		die("�齱����");
	}
	switch ($return_info[1]) {
		case 1 :
		{
			$level = '�صȽ�';
			break;
		}
		case 2 :
		{
			$level = 'һ�Ƚ�';
			break;
		}
		case 3 :
		{
			$level = '���Ƚ�';
			break;
		}
		case 4 :
		{
			$level = '���Ƚ�';
			break;
		}
		case 5 :
		{
			$level = '���뽱';
			break;
		}
	}
	$user = $_pm['user']->getUserById($_SESSION['id']);
	$bag = $_pm['user']->getUserBagById($_SESSION['id']);
	$lucky_draw = new task;
	$lucky_draw->saveGetProps($return_info[3]);
	$sql = "UPDATE userbag SET sums = sums - 1 WHERE uid = {$_SESSION['id']} and pid = {$rs['pid']} and sums>0";
	$_pm['mysql']->query($sql);
	$sql = "DELETE from userbag WHERE sums = 0 AND bsum = 0 AND psum = 0 AND uid = {$_SESSION['id']} and pid = {$rs['pid']}";
	$_pm['mysql']->query($sql);
	unLockItem($id);
	realseLock();
	unset($_SESSION[$key]);
	die("�齱�ɹ�,���" . $level . ",�õ���Ʒ:" . $return_info[2]);
}
else if ($rs['varyname'] == 13) // �������͡���չ�������ֿ⣬��������
{
	//�йܿռ������
	if ($rs['pid'] == 1203) {
		if ($user['tgmax'] >= 2) {
			unLockItem($id);
			die("��ֻ��ʹ�ô˾�����һ���й�����");
		} else if ($user['tgmax'] == 1) {
			$sql = "UPDATE player SET tgmax = 2 WHERE id = {$_SESSION['id']}";
			$_pm['mysql']->query($sql);
			$sql = "UPDATE userbag SET sums = sums - 1 WHERE uid = {$_SESSION['id']} and pid = 1203 and sums>0";
			$_pm['mysql']->query($sql);
			unLockItem($id);
			die("ʹ���й���������ᣨһ���ɹ�!");
		}
	}
	if ($rs['pid'] == 1204) {
		if ($user['tgmax'] >= 3) {
			unLockItem($id);
			die("��ֻ��ʹ�ô˾�����һ���й�����");
		} else if ($user['tgmax'] == 1) {
			unLockItem($id);
			die("����ʹ���й��������һ�����������й���!");
		} else if ($user['tgmax'] == 2) {
			$sql = "UPDATE player SET tgmax = 3 WHERE id = {$_SESSION['id']}";
			$_pm['mysql']->query($sql);
			$sql = "UPDATE userbag SET sums = sums - 1 WHERE uid = {$_SESSION['id']} and pid = 1204 and sums>0";
			$_pm['mysql']->query($sql);
			unLockItem($id);
			die("ʹ���й���������ᣨ�����ɹ�!");
		}
	}
	$eff = explode(":", $rs['effect']);
	if ($eff[0] == 'zhanshi') {
		$arr = "";
		$arr = $_pm['mysql']->getOneRecord("SELECT bbshow FROM player_ext WHERE uid = {$_SESSION['id']}");
		if (!is_array($arr)) {
			unLockItem($id);
			die("����ʱ����ʹ�ó���չʾ��");
		}
		$_pm['mysql']->query("UPDATE player_ext SET bbshow = bbshow + {$eff[1]} WHERE uid = {$_SESSION['id']}");
		$_pm['mysql']->query("UPDATE userbag SET sums = sums - 1 WHERE pid = {$rs['pid']} and uid = {$_SESSION['id']} and sums>0");
		unLockItem($id);
		die("��ϲ��ʹ�ó���չʾ��ɹ�����" . $eff[1] . "��չʾ���ᣡ");
	} else if ($eff[0] == 'addsj') {
		$_pm['mysql']->query("UPDATE userbag
								  SET sums=sums-1
							 WHERE id={$id} and uid={$_SESSION['id']} and sums>0
							 ");
		$result = mysql_affected_rows($_pm['mysql']->getConn());
		if ($result != 1) {
			unLockItem($id);
			die("��û����Ӧ����Ʒ��");
		}
		$arr = "";
		$arr = $_pm['mysql']->getOneRecord("SELECT sj FROM player_ext WHERE uid = {$_SESSION['id']}");
		$numarr = explode(',', $eff[1]);
		$num = rand($numarr[0], $numarr[1]);
		if (!is_array($arr)) {
			$_pm['mysql']->getOneRecord("INSERT INTO player_ext (uid,sj,bbshow) VALUES ({$_SESSION['id']},$num,5)");
		} else {
			$_pm['mysql']->query("UPDATE player_ext SET sj = sj + $num WHERE uid = {$_SESSION['id']}");
		}
		unLockItem($id);
		die("��ϲ���õ���" . $num . "��ˮ����");
	} else if ($eff[0] == 'addyb') {
		$_pm['mysql']->query("UPDATE userbag
								  SET sums=sums-1
							 WHERE id={$id} and uid={$_SESSION['id']} and sums>0
							 ");
		$result = mysql_affected_rows($_pm['mysql']->getConn());
		if ($result != 1) {
			unLockItem($id);
			die("��û����Ӧ����Ʒ��");
		}
		$numarr = explode(',', $eff[1]);
		$num = rand($numarr[0], $numarr[1]);
		$_pm['mysql']->query("UPDATE player SET yb = yb+" . $num . " WHERE id = {$_SESSION['id']}");
		unLockItem($id);
		die("��ϲ���õ���" . $num . "Ԫ����");
	} else if ($eff[0] == 'addbag') {
		if ($user['maxbag'] < 150) {
			unLockItem($id);
			die("���ı���û�дﵽ150������ʹ�ô˵�����չ��");
		}
		if ($user['maxbag'] >= 200) {
			unLockItem($id);
			die("���ı����Ѿ���200���ˣ�������ʹ�ô˵�����չ��");
		}
		$maxbag = $user['maxbag'] + $eff[1];
		if ($maxbag > 200) $maxbag = 200;
		$_pm['mysql']->query("UPDATE userbag
								  SET sums=sums-1
							 WHERE id={$id} and uid={$_SESSION['id']} and sums>0
							 ");
		$result = mysql_affected_rows($_pm['mysql']->getConn());
		if ($result != 1) {
			unLockItem($id);
			die("��û����Ӧ����Ʒ��");
		}
		$_pm['mysql']->query("UPDATE player SET maxbag = $maxbag WHERE id = {$_SESSION['id']}");
		unLockItem($id);
		die("��ϲ����������������{$eff[1]}��");
	} else if ($eff[0] == 'addck') {
		if ($user['maxbase'] < 150) {
			unLockItem($id);
			die("���Ĳֿ�û�дﵽ150������ʹ�ô˵�����չ��");
		}
		if ($user['maxbase'] >= 200) {
			unLockItem($id);
			die("���ı����Ѿ���200���ˣ�������ʹ�ô˵�����չ��");
		}
		$maxbase = $user['maxbase'] + $eff[1];
		if ($maxbase > 200) $maxbase = 200;
		$_pm['mysql']->query("UPDATE userbag
								  SET sums=sums-1
							 WHERE id={$id} and uid={$_SESSION['id']} and sums>0
							 ");
		$result = mysql_affected_rows($_pm['mysql']->getConn());
		if ($result != 1) {
			unLockItem($id);
			die("��û����Ӧ����Ʒ��");
		}
		$_pm['mysql']->query("UPDATE player SET maxbase = $maxbase WHERE id = {$_SESSION['id']}");
		unLockItem($id);
		die("��ϲ���ֿ����������{$eff[1]}��");
	} else if ($eff[0] == 'addbag1') {
		if ($user['maxbag'] < 200) {
			unLockItem($id);
			die("���ı���û�дﵽ200������ʹ�ô˵�����չ��");
		}
		if ($user['maxbag'] >= 300) {
			unLockItem($id);
			die("���ı����Ѿ���300���ˣ�������ʹ�ô˵�����չ��");
		}
		$maxbag = $user['maxbag'] + $eff[1];
		if ($maxbag > 300) $maxbag = 300;
		$_pm['mysql']->query("UPDATE userbag
								  SET sums=sums-1
							 WHERE id={$id} and uid={$_SESSION['id']} and sums>0
							 ");
		$result = mysql_affected_rows($_pm['mysql']->getConn());
		if ($result != 1) {
			unLockItem($id);
			die("��û����Ӧ����Ʒ��");
		}
		$_pm['mysql']->query("UPDATE player SET maxbag = $maxbag WHERE id = {$_SESSION['id']}");
		unLockItem($id);
		die("��ϲ����������������{$eff[1]}��");
	} else if ($eff[0] == 'addck1') {
		if ($user['maxbase'] < 200) {
			unLockItem($id);
			die("���Ĳֿ�û�дﵽ200������ʹ�ô˵�����չ��");
		}
		if ($user['maxbase'] >= 300) {
			unLockItem($id);
			die("���ı����Ѿ���300���ˣ�������ʹ�ô˵�����չ��");
		}
		$maxbase = $user['maxbase'] + $eff[1];
		if ($maxbase > 300) $maxbase = 300;
		$_pm['mysql']->query("UPDATE userbag
								  SET sums=sums-1
							 WHERE id={$id} and uid={$_SESSION['id']} and sums>0
							 ");
		$result = mysql_affected_rows($_pm['mysql']->getConn());
		if ($result != 1) {
			unLockItem($id);
			die("��û����Ӧ����Ʒ��");
		}
		$_pm['mysql']->query("UPDATE player SET maxbase = $maxbase WHERE id = {$_SESSION['id']}");
		unLockItem($id);
		die("��ϲ���ֿ����������{$eff[1]}��");
	}
	if (is_array($eff)) {
		if ($eff[0] == "tuoguan") {
			$_pm['mysql']->query("UPDATE userbag
								  SET sums=sums-1
							 WHERE id={$id} and uid={$_SESSION['id']} and sums>0
							 ");
			$result = mysql_affected_rows($_pm['mysql']->getConn());
			if ($result != 1) {
				unLockItem($id);
				die("��û����Ӧ����Ʒ��");
			}
			unset($result);
			$sql = "UPDATE player SET tgtime = tgtime + $eff[1] WHERE id = {$_SESSION['id']}";
			$_pm['mysql']->query($sql);
			unLockItem($id);
			die("ʹ��{$eff[1]}Сʱ�йܾ�ɹ�!");
		}
	}
	$keys = explode(':', $rs['effect']);
	if ($rs['pid'] >= 85 && $rs['pid'] <= 93) {
		$keys = explode(':', $rs['effect']);
		$item = split(',', $user['openmap']);
		if (in_array($keys[1], $item)) {
			unLockItem($id);
			die($rs['name'] . '��Ӧ�ĵ�ͼ�Ѿ�����!');
		}

		$valid = false;
		foreach ($bags as $k => $v) {
			if ($v['id'] == $id) {
				$valid = true;
				$rs = $v;
				break;
			}
		}
		if (is_array($rs)) {
			// del a props for current map.
			$_pm['mysql']->query("UPDATE userbag SET sums = sums -1 WHERE uid = {$_SESSION['id']} and id = {$id} and sums>0");
			$user['openmap'] .= ',' . $keys[1];

			$_pm['mysql']->query("UPDATE player 
						   SET openmap='{$user['openmap']}' 
						 WHERE id={$_SESSION['id']}");

			unLockItem($id);
			die("{$rs['name']} ��Ӧ��ͼ�򿪳ɹ�!");
		} else {
			unLockItem($id);
			die("��ͼ��ʧ�ܣ���ȷ�ϰ������д򿪸õ�ͼ��Ӧ��Կ��!");
		}
	} else if (($rs['pid'] >= 200 && $rs['pid'] <= 202) || $rs['pid'] == 1344) {
		$full = 0;
		if ($rs['name'] == "�ֿ���������") {
			if ($user['maxbase'] >= 96) $full = 1;
			if ($user['maxbase'] + 6 > 96) $user['maxbase'] = 96;
			else $user['maxbase'] += 6;
		} else if ($rs['name'] == "������������") {
			if ($user['maxbag'] >= 96) $full = 1;
			if ($user['maxbag'] + 6 > 96) $user['maxbag'] = 96;
			else $user['maxbag'] += 6;
		} else if ($rs['name'] == "������������") {
			if ($user['maxmc'] >= 40) $full = 1;
			if ($user['maxmc'] + 6 > 40) $user['maxmc'] = 40;
			else $user['maxmc'] += 6;
		} else if ($rs['name'] == "�߼�������������") {
			if ($user['maxmc'] < 40) {
				unLockItem($id);
				die("�����������ӻ�û��չ��40������������������չ��40��������ô˵�����չ!");
			}
			if ($user['maxmc'] >= 80) $full = 1;
			if ($user['maxmc'] + 1 > 80) $user['maxmc'] = 80;
			else $user['maxmc'] += 1;
		}
		if ($full == 1) {
			unLockItem($id);
			die("�Ѿ���չ�����ޣ���������չ������������!");
		}

		// del props. and save result.
		$_pm['mysql']->query("UPDATE player 
					   SET maxbase={$user['maxbase']},
					       maxbag={$user['maxbag']},
						   maxmc={$user['maxmc']}
					 WHERE id={$_SESSION['id']}");

		$_pm['mysql']->query("UPDATE userbag
					   SET sums=sums-1
					 WHERE id={$id} and uid={$_SESSION['id']} and sums>0
				  ");
		unLockItem($id);
		die("ʹ�õ��� {$rs['name']} �ɹ�!");
	} else if ($rs['pid'] == 1342) {
		$full = 0;
		if ($user['maxbag'] >= 150) $full = 1;
		if ($user['maxbag'] < 96) $full = 2;
		if ($user['maxbag'] + 6 > 150) $user['maxbag'] = 150;
		else $user['maxbag'] += 6;
		if ($full == 1) {
			unLockItem($id);
			die("�Ѿ���չ�����ޣ������ټ�����չ��!");
		}
		if ($full == 2) {
			unLockItem($id);
			die("������û��չ��96�������ñ�������������չ��96��!");
		}
		$_pm['mysql']->query("UPDATE player 
					   SET maxbag={$user['maxbag']}
					 WHERE id={$_SESSION['id']}");

		$_pm['mysql']->query("UPDATE userbag
					   SET sums=sums-1
					 WHERE id={$id} and uid={$_SESSION['id']} and sums>0
				  ");
		unLockItem($id);
		die("ʹ�õ��� {$rs['name']} �ɹ�!");
	} else if ($rs['pid'] == 1343) {
		$full = 0;
		if ($user['maxbase'] >= 150) $full = 1;
		if ($user['maxbase'] < 96) $full = 2;
		if ($user['maxbase'] + 6 > 150) $user['maxbase'] = 150;
		else $user['maxbase'] += 6;
		if ($full == 1) {
			unLockItem($id);
			die("�Ѿ���չ�����ޣ������ټ�����չ��!");
		}
		if ($full == 2) {
			unLockItem($id);
			die("�ֿ⻹û��չ��96�������òֿ�����������չ��96��!");
		}
		$_pm['mysql']->query("UPDATE player 
					   SET maxbase={$user['maxbase']}
					 WHERE id={$_SESSION['id']}");

		$_pm['mysql']->query("UPDATE userbag
					   SET sums=sums-1
					 WHERE id={$id} and uid={$_SESSION['id']} and sums>0
				  ");
		unLockItem($id);
		die("ʹ�õ��� {$rs['name']} �ɹ�!");
	} else if (($rs['pid'] >= 742 && $rs['pid'] <= 746) || $rs['pid'] == 1247 || $rs['pid'] == 1225 || $rs['pid'] == 2055) // ������Զ�ս����. format:
	{
		if ($keys[0] == 'exp') // ʹ�þ����
		{
			$dbl = 0;
			switch ($keys[1]) {
				case 1.5:
					$dbl = 2;
					break;
				case 2:
					$dbl = 3;
					break;
				case 2.5:
					$dbl = 4;
					break;
				case 3:
					$dbl = 5;
					break;
			}

			if (is_array($rs)) {
				// del a props for current
				$_pm['mysql']->query("UPDATE userbag
								  SET sums=sums-1
							 WHERE id={$id} and uid={$_SESSION['id']} and sums>0
							 ");
				$result = mysql_affected_rows($_pm['mysql']->getConn());
				if ($result != 1) {
					unLockItem($id);
					die("��û����Ӧ����Ʒ��");
				}
				unset($result);
				// ��ȡ��ǰ��ʣ��˫��ʱ�䲢�ۼơ�
				if ($user['dblexpflag'] > 1 && $dbl == $user['dblexpflag']) {
					$other = $user['dblstime'] + $user['maxdblexptime'] - time();
					if ($other <= 0) $other = 0;
					$user['maxdblexptime'] = 3600 + $other;
				} else $user['maxdblexptime'] = 3600;

				$user['dblexpflag'] = $dbl;
				$user['dblstime'] = time();

				// Update user data to database.
				$_pm['mysql']->query("UPDATE player
							   SET maxdblexptime={$user['maxdblexptime']},
								   dblexpflag={$user['dblexpflag']},
								   dblstime={$user['dblstime']}
							 WHERE id={$_SESSION['id']}
						  ");
				unLockItem($id);
				die("ʹ��{$keys[1]} �������ɹ�!");
			} else {
				unLockItem($id);
				die("û���ڰ����з�����Ӧ����Ʒ!");
			}
		}
	} // end ˫����
	####################�����Զ�ս������Ϊ��Ұ��Ԫ����9.24̷�###################

	if ($keys[0] == 'autofree') // ʹ�ý�Ұ��Զ�ս����
	{
		if (is_array($rs)) {
			// del a props for current
			$_pm['mysql']->query("UPDATE userbag
								  SET sums=sums-1
							 WHERE id={$id} and uid={$_SESSION['id']} and sums>0
							 ");
			$result = mysql_affected_rows($_pm['mysql']->getConn());
			if ($result != 1) {
				unLockItem($id);
				die("��û����Ӧ����Ʒ��");
			}
			unset($result);
			$user['sysautosum'] += intval($keys[1]);
			$_pm['mysql']->query("UPDATE player
								 SET sysautosum={$user['sysautosum']}
							 WHERE id={$_SESSION['id']}
							  ");
			unLockItem($id);
			die("ʹ�� {$keys[1]} �ν�Ұ��Զ�ս����ɹ�!");
		}
	} else if ($keys[0] == "auto" || $keys[0] == "autoteam") {
		$_pm['mysql']->query("UPDATE userbag
								  SET sums=sums-1
							 WHERE id={$id} and uid={$_SESSION['id']} and sums>0
							 ");
		$result = mysql_affected_rows($_pm['mysql']->getConn());
		if ($result != 1) {
			unLockItem($id);
			die("��û����Ӧ����Ʒ��");
		}
		unset($result);
		if ($keys[0] == "auto") {
			$user['maxautofitsum'] += intval($keys[1]);
			$_pm['mysql']->query("UPDATE player
									  SET maxautofitsum={$user['maxautofitsum']}
								 WHERE id={$_SESSION['id']}
								 ");
			$msg = "ʹ�� {$keys[1]} ��Ԫ�����Զ�ս����ɹ�!";
		} else {
			$_pm['mysql']->query("UPDATE player_ext
									  SET team_auto_times = team_auto_times+" . intval($keys[1]) . " WHERE uid=" . $_SESSION['id']);
			$msg = "ʹ������Զ�ս����ɹ�,���� {$keys[1]} ��!";
		}
		unLockItem($id);
		die($msg);
	}
	####################���������###################
}
else if ($rs['varyname'] == 4) {
	$config = $_pm['mysql']->getOneRecord("SELECT value2,contents FROM welcome WHERE code = 'ticket'");
	if (!is_array($config)) {
		$str = '';
	} else {
		$timearr = explode(':', $config['value2']);
		if ($timearr['0'] != 1) {
			$str = '';
		} else {
			if (date('H') >= $timearr['1']) {
				unLockItem($id);
				die('�����ѿ�������������ɣ�');
			}
		}
	}
	if ($rs['effect'] != 'ticket') {
		unLockItem($id);
		die();
	}
	$_pm['mysql']->query("UPDATE userbag
					  SET sums=sums-1
				 WHERE id={$id} and uid={$_SESSION['id']} and sums>0
				 ");
	$result = mysql_affected_rows($_pm['mysql']->getConn());
	if ($result != 1) {
		unLockItem($id);
		die("��û����Ӧ����Ʒ��");
	}
	$sql = 'CREATE TABLE IF not exists ticket_' . date('Ymd') . ' (
		  `id` int(11) NOT NULL AUTO_INCREMENT,
		  `uid` int(11) unsigned DEFAULT "0",
		  `ticket_num` varchar(8) DEFAULT "0" COMMENT "����",
		  PRIMARY KEY (`id`),
		  UNIQUE KEY `tn` (`ticket_num`) 
		) ENGINE=MyISAM';
	$_pm['mysql']->query($sql);
	$welcome = memContent2Arr("db_welcome", 'code');
	$config = $welcome['ticket_num']['contents'];
	if (empty($config)) {
		$_pm['mysql']->query("UPDATE userbag
					  SET sums=sums+1
				 WHERE id={$id} and uid={$_SESSION['id']}
				 ");
		unLockItem($id);
		die("û���ڴ����ݣ�");
	}
	$res = rand_num($config);
	echo 'ʹ�óɹ�����ú���Ϊ ' . $res . ' �����뵽�����Ʋ鿴';
	unLockItem($id);
}
else if ($rs['varyname'] == 12) // �������͡�
{
	/**
	 * Format: randitem:1308:1:80:2|1055:1:70:2|1141:1:80:2|744:1:30:2|211:1:40:1|213:1:40:1|871:1:40:1|870:1:20:1|1207:1:20:1|9:1:5:1|912:1:1:1
	 * @Memo: 1��ʾ��øõ��ߵ�ʱ��,�ᷢϵͳ����(2��ʾ���ᷢ����)
	 * ��[�������]��һö����,�����ǲȵ��˹�ʺ��,��Ȼ�����E(��Ӧ����)��D(��Ӧ�ĵ�������)��
	 */
	//�ж��û������Ƿ�����
	$bagNum = 0;

	if (is_array($bags)) {
		foreach ($bags as $x => $y) {
			if ($y['sums'] > 0 and $y['zbing'] == 0) {
				$bagNum++;
			}
		}
	}
	$snum = $user['maxbag'] - $bagNum;
	if ($snum < 3) {
		unLockItem($id);
		die('�������������ո��ӣ�');
	}
	if ($bagNum >= $user['maxbag']) {
		unLockItem($id);
		die('���İ����������������������');
	}

	if (!empty($rs['requires'])) {
		$requires = explode(":", $rs['requires']);
		if ($requires[0] == 'lv') {
			if ($bb['level'] < $requires[1]) {
				unLockItem($id);
				die("��û�дﵽ��Ӧ�ĵȼ������ܿ����ñ��䣡");
			}
		}
	}
	$propsPatter = $rs['effect'];
	$arr = explode(",", $propsPatter);
	$task = new task();
	foreach ($arr as $v) {
		$newarr = explode(":", $v);
		if ($newarr[0] == "needkey") {
			if (is_array($bags)) {
				foreach ($bags as $y) {
					if ($y['pid'] == $newarr[1] && $y['sums'] > 0) {
						$_pm['mysql']->query("UPDATE userbag
										     SET sums=sums-1
										   WHERE pid={$newarr[1]} and uid={$_SESSION['id']} and sums>0
										");
						$sign = 1;
					}
				}
				if ($sign != 1) {
					unLockItem($id);
					die("��û�п��������Կ��!");
				}
			} else {
				unLockItem($id);
				die("��û�п��������Կ��!");
			}
		} else if ($newarr[0] == 'giveitems') {

			unset($result);
			$patter = str_replace('giveitems:', '', $rs['effect']);
			$propslist = explode(',', $patter);

			$retstr = '';
			if (is_array($propslist)) {
				if ($snum < count($propslist)) {
					die('�����ռ䲻�㣡');
				}
				$_pm['mysql']->query("UPDATE userbag
								  SET sums=sums-1
							 WHERE id={$id} and uid={$_SESSION['id']} and sums>0
							 ");
				$result = mysql_affected_rows($_pm['mysql']->getConn());
				if ($result != 1) {
					unLockItem($id);
					die("��û����Ӧ����Ʒ��");
				}
				foreach ($propslist as $k => $v) {
					$inarr = explode(':', $v);        //	0=> ID, 2=> rand number, 1=> sum props


					if (is_array($inarr)) {
						//foreach($inarr as $inarrs)
						//{
						$prs =getBasePropsInfoById($inarr[0]);
						$task->saveGetPropsMore($inarr[0], $inarr[1], $rs['pid'],0,$prs);

						if (empty($retstr)) {
							$retstr = '��õ��� ' . $prs['name'] . '&nbsp;' . $inarr[1] . ' ��';
						} else {
							$retstr .= "," . $prs['name'] . '&nbsp;' . $inarr[1] . ' ��';
						}
						//}
					}
				} // end foreach
				// del props current bag.
				echo $retstr;
			}
		} elseif ($newarr[0] == "randitem") {
			$patter = str_replace('randitem:', '', $v);
			$propslist = explode('|', $patter);
			$retstr = '';
			if (is_array($propslist)) {
				foreach ($propslist as $k => $v) {
					$inarr = explode(':', $v);        //	0=> ID, 2=> rand number, 1=> sum props
					if (rand(1, intval($inarr[2])) == 1)    //  rand hits
					{// del props current bag.
						$_pm['mysql']->query("UPDATE userbag
								  SET sums=sums-1
							 WHERE id={$id} and uid={$_SESSION['id']} and sums>0
							 ");
						$result = mysql_affected_rows($_pm['mysql']->getConn());
						if ($result != 1) {
							unLockItem($id);
							die("��û����Ӧ����Ʒ��");
						}
						unset($result);
						$prs=getBasePropsInfoById($inarr[0]);
						$task->saveGetPropsMore($inarr[0], $inarr[1], $rs['pid'],0,$prs);
						$retstr = '��õ��� ' . $prs['name'] . ' ' . $inarr[1] . ' ��';
						if ($inarr[3] == 2) {
							$word = " ,ʹ��{$rs['name']},���˵صõ���ȻŮ���ף��,����� {$inarr[1]} ��{$prs['name']}";
							$task->saveGword($word);
						}

						echo $retstr;
						break;
					}
				} // end foreach
			}
		}
	}
}
else if ($rs['varyname'] == 22) // �������͡�
{

	if (!isset($_GET['js'])) {
		unLockItem($id);
		die('ռ��ʯ,����ռ������ռ��ʱʹ��!<br/><span style="cursor:pointer;color:#ff0000;font-size:14px;font-weight:bold" onclick="$(\'gw\').contentWindow.location=\'/function/zhanbuwu.php\'"><strong>�������ǰ����ռ���ݡ���</strong></span>');
	}
	/**
	 * Format: randitem:1308:1:80:2|1055:1:70:2|1141:1:80:2|744:1:30:2|211:1:40:1|213:1:40:1|871:1:40:1|870:1:20:1|1207:1:20:1|9:1:5:1|912:1:1:1
	 * @Memo: 1��ʾ��øõ��ߵ�ʱ��,�ᷢϵͳ����(2��ʾ���ᷢ����)
	 * ��[�������]��һö����,�����ǲȵ��˹�ʺ��,��Ȼ�����E(��Ӧ����)��D(��Ӧ�ĵ�������)��
	 */
	//�ж��û������Ƿ�����
	$bagNum = 0;

	if (is_array($bags)) {
		foreach ($bags as $x => $y) {
			if ($y['sums'] > 0 and $y['zbing'] == 0) {
				$bagNum++;
			}
		}
	}
	$snum = $user['maxbag'] - $bagNum;
	if ($snum < 3) {
		die('�������������ո��ӣ�');
	}
	if ($bagNum >= $user['maxbag']) {
		unLockItem($id);
		die('���İ����������������������');
	}

	if (!empty($rs['requires'])) {
		$requires = explode(":", $rs['requires']);
		if ($requires[0] == 'lv') {
			if ($bb['level'] < $requires[1]) {
				unLockItem($id);
				die("��û�дﵽ��Ӧ�ĵȼ������ܽ���ռ����");
			}
		}
	}
	$propsPatter = $rs['effect'];
	$arr = explode(",", $propsPatter);

	foreach ($arr as $v) {
		$newarr = explode(":", $v);
		if ($newarr[0] == "needkey") {
			if (is_array($bags)) {
				foreach ($bags as $y) {
					if ($y['pid'] == $newarr[1] && $y['sums'] > 0) {
						$_pm['mysql']->query("UPDATE userbag
										     SET sums=sums-1
										   WHERE pid={$newarr[1]} and uid={$_SESSION['id']} and sums>0
										");
						$sign = 1;
					}
				}
				if ($sign != 1) {
					unLockItem($id);
					die("��û��ռ����Կ��!");
				}
			} else {
				unLockItem($id);
				die("��û��ռ����Կ��!");
			}
		} else if ($newarr[0] == 'giveitems') {

			unset($result);
			$patter = str_replace('giveitems:', '', $rs['effect']);
			$propslist = explode(',', $patter);

			$retstr = '';
			if (is_array($propslist)) {
				if ($snum < count($propslist)) {
					die('�����ռ䲻�㣡');
				}
				$_pm['mysql']->query("UPDATE userbag
								  SET sums=sums-1
							 WHERE id={$id} and uid={$_SESSION['id']} and sums>0
							 ");
				$result = mysql_affected_rows($_pm['mysql']->getConn());
				if ($result != 1) {
					unLockItem($id);
					die("�����ռ��ʯ���޷�����ռ����Ҫ��ħ��T_T�´������ɡ�");
				}
				foreach ($propslist as $k => $v) {
					$inarr = explode(':', $v);        //	0=> ID, 2=> rand number, 1=> sum props


					if (is_array($inarr)) {
						//foreach($inarr as $inarrs)
						//{
						$task = new task();
						$task->saveGetPropsMore($inarr[0], $inarr[1], $rs['pid']);
						$prs = $_pm['mysql']->getOneRecord("SELECT name FROM props WHERE id={$inarr[0]}");
						if (empty($retstr)) {
							$retstr = '��õ��� ' . $prs['name'] . '&nbsp;' . $inarr[1] . ' ��';
						} else {
							$retstr .= "," . $prs['name'] . '&nbsp;' . $inarr[1] . ' ��';
						}
						//}
					}
				} // end foreach
				// del props current bag.
				echo $retstr;
			}
		} elseif ($newarr[0] == "randitem") {
			$patter = str_replace('randitem:', '', $v);
			$propslist = explode('|', $patter);
			$retstr = '';
			$task = new task();
			if (is_array($propslist)) {
				foreach ($propslist as $k => $v) {
					$inarr = explode(':', $v);        //	0=> ID, 2=> rand number, 1=> sum props
					if (rand(1, intval($inarr[2])) == 1)    //  rand hits
					{// del props current bag.
						$_pm['mysql']->query("UPDATE userbag
								  SET sums=sums-1
							 WHERE id={$id} and uid={$_SESSION['id']} and sums>0
							 ");
						$result = mysql_affected_rows($_pm['mysql']->getConn());
						if ($result != 1) {
							unLockItem($id);
							die("��û����Ӧ����Ʒ��");
						}
						unset($result);
						$task->saveGetPropsMore($inarr[0], $inarr[1], $rs['pid']);
						$prs = $_pm['mysql']->getOneRecord("SELECT name FROM props WHERE id={$inarr[0]}");
						$retstr = '��õ��� ' . $prs['name'] . ' ' . $inarr[1] . ' ��';

						if ($inarr[3] == 2) {
							$word = " ,ʹ��{$rs['name']},�ϵ�ռ���ж�����ȻŮ��,����� {$inarr[1]} ��{$prs['name']}";
							//$word = "�������������ϵ�ռ���ж�����ȻŮ��Ů���İ���{$prs['name']}*{$inarr[1]}������������������";
							$task->saveGword($word);
						}

						echo $retstr;
						break;
					}
				} // end foreach
			}
		}
	}
}
else if ($rs['varyname'] == 2) // ������
{
	require_once('../sec/dblock_fun.php');
	$a = getLock($_SESSION['id']);
	if (!is_array($a)) {
		realseLock();
		unLockItem($id);
		die('��������æ�����Ժ����ԣ�');
	}
	$_pm['mysql']->query("UPDATE userbag
								  SET sums=sums-1
							 WHERE id={$id} and uid={$_SESSION['id']} and sums>0
							 ");
	$result = mysql_affected_rows($_pm['mysql']->getConn());
	if ($result != 1) {
		unLockItem($id);
		realseLock();
		die("��û����Ӧ����Ʒ��");
	}
	unset($result);
	$tid = $id == 0 ? $user['mbid'] : $id;
	$bb = $_pm['mysql']->getOneRecord("SELECT wx,name,czl,id
										 FROM userbb
										WHERE id={$user['mbid']} and uid=" . $_SESSION['id']);
	if ($bb['wx'] == 7 && $rs['requires'] != '__SS__') {
		unLockItem($id);
		$_pm['mysql']->query("rollback");
		realseLock();
		die("��ʥ�����޷�ʹ�ô�����Ʒ��");
	}

	if ($bb['wx'] != 7 && $rs['requires'] == '__SS__') {
		unLockItem($id);
		$_pm['mysql']->query("rollback");
		realseLock();
		die("����ʥ�����޷�ʹ�ô�����Ʒ��");
	}
	$arr = explode(':', $rs['effect']);
	if (!is_array($arr)) return false;
	if ($arr[0] == 'addexp') // ���Ӿ���
	{
		$eval = "\$exp=rand{$arr[1]};";
		eval($eval);
		$t = new task();
		$rtn = $t->saveExps($exp);
		if ($rtn === false) {
			$_pm['mysql']->query('rollback');
			realseLock();
			die("�����Ѿ������������ˣ�");
		}
		$tips .= '��þ���' . $exp;
	} else if ($arr[0] == "addczl") // ��ӳɳ�
	{
		$cishu = $_pm['mysql']->getOneRecord("select chouqu_chongwu from player_ext where uid={$_SESSION['id']}");
		if (strpos($cishu['chouqu_chongwu'], ',' . $bb['id'] . ',') !== false) {
			unLockItem($id);
			$_pm['mysql']->query("rollback");
			die("��������ȡ���ɳ�,������ʹ���������!");
		}

		if ($bb['wx'] == 7) {
			$bb_settings = $_pm['mysql']->getOneRecord("SELECT max_czl FROM bb,super_jh	WHERE bb.id=super_jh.pet_id and bb.name='" . $bb['name'] . "' limit 1");
			if (!$bb_settings) {
				unLockItem($id);
				$_pm['mysql']->query("rollback");
				realseLock();
				die("ȡ����ʥ�����趨ʧ�ܣ�");
			}

			if ($bb_settings['max_czl'] < $bb['czl'] + $arr[1]) {
				$arr[1] = $bb_settings['max_czl'] - $bb['czl'];
			}
		}

		if ($user['mbid'] != '' && $user['mbid'] > 0) {
			$_pm['mysql']->query("UPDATE userbb
				                         SET czl=czl+{$arr[1]}
									   WHERE id={$user['mbid']}
									");
			$tips .= '��������������' . $arr[1] . '�ɳ���';
		}
	} else if ($arr[0] == "addac") // ���ӹ�����
	{
		if ($user['mbid'] != '' && $user['mbid'] > 0) {
			$_pm['mysql']->query("UPDATE userbb
				                         SET ac=ac+{$arr[1]}
									   WHERE id={$user['mbid']}
									");
			$tips .= '��������������' . $arr[1] . '������';
		}
	} else if ($arr[0] == "addmc") // ���ӷ���
	{
		if ($user['mbid'] != '' && $user['mbid'] > 0) {
			$_pm['mysql']->query("UPDATE userbb
				                         SET mc=mc+{$arr[1]}
									   WHERE id={$user['mbid']}
									");
			$tips .= '��������������' . $arr[1] . '������';
		}
	} else if ($arr[0] == "addhits") // ��������
	{
		if ($user['mbid'] != '' && $user['mbid'] > 0) {
			$_pm['mysql']->query("UPDATE userbb
				                         SET hits=hits+{$arr[1]}
									   WHERE id={$user['mbid']}
									");
			$tips .= '��������������' . $arr[1] . '���У�';
		}
	} else if ($arr[0] == "addmiss") // ��������
	{
		if ($user['mbid'] != '' && $user['mbid'] > 0) {
			$_pm['mysql']->query("UPDATE userbb
				                         SET miss=miss+{$arr[1]}
									   WHERE id={$user['mbid']}
									");
			$tips .= '��������������' . $arr[1] . '���ܣ�';
		}
	} else if ($arr[0] == "addhp") // ����������
	{
		if ($user['mbid'] != '' && $user['mbid'] > 0) {
			$_pm['mysql']->query("UPDATE userbb
				                         SET srchp=srchp+{$arr[1]}
									   WHERE id={$user['mbid']}
									");
			$tips .= '��������������' . $arr[1] . '������';
		}
	} else if ($arr[0] == "addspeed") // ����������
	{
		if ($user['mbid'] != '' && $user['mbid'] > 0) {
			$_pm['mysql']->query("UPDATE userbb
				                         SET speed=speed+{$arr[1]}
									   WHERE id={$user['mbid']}
									");
			$tips .= '��������������' . $arr[1] . '�ٶȣ�';
		}
	} else if ($arr[0] == "addmp") // ����ħ��
	{
		if ($user['mbid'] != '' && $user['mbid'] > 0) {
			$_pm['mysql']->query("UPDATE userbb
				                         SET srcmp=srcmp+{$arr[1]}
									   WHERE id={$user['mbid']}
									");
			$tips .= '��������������' . $arr[1] . 'ħ����';
		}
	} else if ($arr[0] == "weiwang") // ��������
	{
		$_pm['mysql']->query("UPDATE player
				                         SET prestige=prestige+{$arr[1]}
									   WHERE id={$_SESSION['id']}
									");
		$tips .= '��������' . $arr[1] . '�㣡';
	} else if ($arr[0] == "add_cq_czl") // ���ӳ�ȡ�ĳɳ�����2010-11-3
	{
		$sql = 'update player_ext set czl_ss=czl_ss+' . abs($arr[1]) . ' where uid=' . $_SESSION['id'];
		$_pm['mysql']->query($sql);
		$tips .= '��óɳ�' . $arr[1] . '�㣡';
	} else if ($arr[0] == "add_zc_jifen") // ������ս����Ů��Ҫ������ʤ���ֱ���2010-11-3
	{
		$row = $_pm['mysql']->getOneRecord('select buff_status from player_ext where uid=' . $_SESSION['id']);
		$buff = preg_replace("/add_zc_jifen:[^;]+;?/", '', $row['buff_status']) . 'add_zc_jifen:' . date("Ymd") . ',' . $arr[1] . ';';

		$sql = 'update player_ext set buff_status="' . $buff . '" where uid=' . $_SESSION['id'];
		$_pm['mysql']->query($sql);
		$tips .= '�����ɹ���';
	}
	echo $tips;
	realseLock();
	unLockItem($id);
}
else if ($rs['varyname'] == 24)    //��Ƭ��
{
	$sql = " SELECT F_User_Card_Info FROM player_ext WHERE uid = '" . $rs['uid'] . "'";
	$result_card = $_pm['mysql']->getOneRecord($sql);
	unset($sql);
	if (empty($result_card['F_User_Card_Info'])) {
		$sql = " UPDATE player_ext SET  F_User_Card_Info = '" . $rs['name'] . ":1' WHERE uid = '" . $rs['uid'] . "'";
		$result_card = $_pm['mysql']->query($sql);
		if ($result_card) {
			unset($result_card);
			$result_card = $result_card = $_pm['mysql']->query("UPDATE userbag
							  SET sums=sums-1
							   WHERE id={$rs['id']} and uid={$_SESSION['id']} and sums>0
							");
			if ($result_card) {
				$sql = " SELECT * FROM t_card_to_title ";
				$result = $_pm['mysql']->getRecords($sql);

				echo "��ϲ����һ��ʹ�ÿ�Ƭ�ɹ�";
			} else {
				die("��һ��ʹ�ÿ�Ƭ�쳣");
			}
		} else {
			die("��һ��ʹ�ÿ�Ƭ�쳣");
		}
	} else {
		$card_arr = explode(',', $result_card['F_User_Card_Info']);
		for ($i = 0; $i < count($card_arr); $i++) {
			$arr_card_info = explode(':', $card_arr[$i]);
			$arr_card_name[$i] = $arr_card_info[0];
			if ($arr_card_name[$i] == $rs['name']) {
				$num = 0;
				$num = $arr_card_info[1] + 1;
				$arr_card_num[$i] = $num;
			} else {
				$arr_card_num[$i] = intval($arr_card_info[1]);
			}
		}
		if (in_array($rs['name'], $arr_card_name)) {
			$the_card = 1;
		} else {
			$the_card = 0;
		}
		switch ($the_card) {
			case 0 :    //û�������Ƭ
			{
				$sql = " UPDATE player_ext SET F_User_Card_Info ='" . $result_card['F_User_Card_Info'] . "," . $rs['name'] . ":1' WHERE uid = '" . $rs['uid'] . "'";
				$result = $_pm['mysql']->query($sql);
				if ($result) {
					unset($result);
					$result = $_pm['mysql']->query("UPDATE userbag
							  SET sums=sums-1
							   WHERE id={$rs['id']} and uid={$_SESSION['id']} and sums>0
							");
					if ($result) {
						unset($result);
						unset($arr_card_name);
						unset($arr_card_num);
						echo "��ϲ��ʹ���¿�Ƭ�ɹ�";


					} else {
						unset($arr_card_name);
						unset($arr_card_num);
						unset($result);
						die("ʹ���¿�Ƭ�쳣");
					}

				} else {
					unset($arr_card_name);
					unset($arr_card_num);
					die("ʹ���¿�Ƭ�쳣");
				}
				break;
			}
			case 1 :    //�����Ƭʹ�ù���
			{
				$set_arr = array_combine($arr_card_name, $arr_card_num);
				foreach ($set_arr as $key => $val) {
					$set .= $key . ":" . $val . ",";
				}
				$set = substr($set, 0, -1);
				$sql = " UPDATE player_ext SET F_User_Card_Info = '" . $set . "' WHERE uid = '" . $rs['uid'] . "'";
				unset($set);
				unset($arr_card_name);
				unset($arr_card_num);
				unset($set_arr);
				unset($result);
				$result = $_pm['mysql']->query($sql);
				if ($result) {
					echo "ʹ�ù��Ŀ�Ƭʹ�óɹ�";
					$result_card = $_pm['mysql']->query("UPDATE userbag
							  SET sums=sums-1
							   WHERE id={$rs['id']} and uid={$_SESSION['id']} and sums>0
							");
				} else {
					die("ʹ�ù��Ŀ�Ƭʹ��ʧ��");
				}
				break;
			}
		}
	}
	$sql = " SELECT * FROM t_card_to_title ";
	$result = $_pm['mysql']->getRecords($sql);

	$sql = " SELECT F_User_Card_Info FROM player_ext WHERE uid = '" . $_SESSION['id'] . "'";
	$result_user_card = $_pm['mysql']->getOneRecord($sql);

	$arr_types = explode(',', $result_user_card['F_User_Card_Info']);
	for ($i = 0; $i < count($arr_types); $i++) {
		$result_user_card_arr = explode(':', $arr_types[$i]);
		$result_user_card_name[$i] = $result_user_card_arr[0];
	}
	unset($result_user_card);
	$result_user_card_has = array();
	$result_user_card_has = $result_user_card_name;
	$is_in_arr = array();
	foreach ($result as $info) {
		$arr_the_title_need_card = explode(',', $info['F_title_must_card']);
		for ($i = 0; $i < count($arr_the_title_need_card); $i++) {
			if (in_array($arr_the_title_need_card[$i], $result_user_card_has) || $arr_the_title_need_card[$i] == $result_user_card_has[0]) {
				array_push($is_in_arr, 1);
			} else {
				array_push($is_in_arr, 0);
			}
		}
		if (!in_array(0, $is_in_arr)) {
			$sql = " SELECT F_Has_Title FROM player_ext WHERE uid = '" . $_SESSION['id'] . "'";
			$title_result = $_pm['mysql']->getOneRecord($sql);
			if (empty($title_result['F_Has_Title'])) {
				$sql = " UPDATE player_ext SET  F_Has_Title = '" . $info['id'] . "' WHERE uid = '" . $_SESSION['id'] . "'";
				$_pm['mysql']->query($sql);
				$task = new task();
				$word .= "������µĳƺ�-----" . $info['F_title_Chinese'];
				$task->saveGword($word);
			} else {
				$title_has = explode(',', $title_result['F_Has_Title']);
				if (!in_array($info['id'], $title_has)) {
					$set = $title_result['F_Has_Title'] . "," . $info['id'];
					$sql = " UPDATE player_ext SET F_Has_Title =  '" . $set . "' WHERE uid = '" . $_SESSION['id'] . "'";
					$_pm['mysql']->query($sql);
					$task = new task();
					$word .= "������µĳƺ�-----" . $info['F_title_Chinese'];
					$task->saveGword($word);
				}
			}
		}
		unset($is_in_arr);
		$is_in_arr = array();
	}
}
else if ($rs['varyname'] == 16) // ͼֽ�ϳ���
{

	//�ж��û������Ƿ�����
	$bagNum = 0;

	if (is_array($bags)) {
		foreach ($bags as $x => $y) {
			if ($y['sums'] > 0 and $y['zbing'] == 0) {
				$bagNum++;
			}
		}
	}

	if ($bagNum >= $user['maxbag']) {
		unLockItem($id);
		die('���İ����������������������');
	}

	$arr = explode(':', $rs['effect'], 2);
	if ($arr[0] == 'hecheng') // ͼֽ�ϳ� ��ʽ��hecheng:(956:10|957:10|958:10|1025:1):1012:1|1013:1
	{
		require_once('../sec/dblock_fun.php');
		$a = getLock($_SESSION['id']);
		if (!is_array($a)) {
			realseLock();
			die('��������æ�����Ժ����ԣ�');
		}
		$sysl = $_pm['mysql']->getOneRecord(" SELECT sums FROM userbag WHERE sums > 0 AND uid = {$_SESSION['id']} AND  id={$rs['id']}");
		if (!isset($sysl['sums']) || empty($sysl['sums'])) {
			unLockItem($id);
			die('��Ĳ��ϲ��㣬�޷�����!');
		}
		$rarr = explode('):', $arr[1]);
		$require = str_replace('(', '', $rarr[0]);
		$getProps = explode('|', $rarr[1]);

		// Check props is exists?
		$need = explode('|', $require);
		foreach ($need as $k => $v) {
			$t = explode(':', $v);
			$ex = $_pm['mysql']->getOneRecord("SELECT sum(sums) as cnt 
												  FROM userbag 
												 WHERE pid ={$t['0']} and uid={$_SESSION['id']}");
			if ($ex['cnt'] < $t['1']) {
				unLockItem($id);
				die('��Ĳ��ϲ��㣬�޷�������');
			}
		}

		// ok, then get props.
		$idlist = '';
		foreach ($getProps as $v) {
			$gets = explode(':', $v);
			for ($i = 0; $i < $gets['1']; $i++) {
				$idlist .= $idlist == '' ? $gets[0] : ',' . $gets[0];
			}
		}

		// clear props
		$delcount = 0;
		foreach ($need as $k => $v) {
			$t = explode(':', $v);
			$ret = $_pm['mysql']->getRecords("SELECT id,sums
											  FROM userbag 
											 WHERE pid ={$t['0']} and uid={$_SESSION['id']}
											 ORDER by sums
										  ");
			//Del props and count num
			if (is_array($ret)) {
				foreach ($ret as $k => $v) {
					if ($v['sums'] < 1) continue;
					if ($delcount < $t[1]) $del = $t[1] - $delcount;
					else break;
					if ($v['sums'] == $del) {
						// del record
						$_pm['mysql']->query("UPDATE userbag 
											   SET sums=0
											 WHERE id={$v['id']}
										   ");
						break;
					} else if ($v['sums'] < $del) {
						// del record. $v['sums']
						$delcount += $v['sums'];
						$_pm['mysql']->query("UPDATE userbag 
											   SET sums=0
											 WHERE id={$v['id']}
										   ");
					} else // ��ȥʣ����ֵ��update.
					{
						$v['sums'] = $v['sums'] - $del;
						// update record.
						$_pm['mysql']->query("UPDATE userbag 
											   SET sums={$v['sums']}
											 WHERE id={$v['id']}
										  ");
						break;
					}
				}
			} // end if
		} // end foreach
		// clear end
		$_pm['mysql']->query("UPDATE userbag
							     SET sums=sums-1
							   WHERE id={$rs['id']} and uid={$_SESSION['id']} and sums>0
							");
		// save props;
		$tsk = new task();
		$tsk->saveGetProps($idlist);
		unLockItem($id);
		realseLock();
		die('��ϲ��,�����ɹ�!�����һ����Ʒ!');
	} else if ($arr[0] == 'chongzhu') // �����ϳ� ��ʽ��chongzhu:(956|957|958|1025):1012:10|1013:50
	{
		require_once('../sec/dblock_fun.php');
		$a = getLock($_SESSION['id']);
		if (!is_array($a)) {
			realseLock();
			unLockItem($id);
			die('��������æ�����Ժ����ԣ�');
		}
		$arr = explode('):', $arr[1]);
		$arr[0] = str_replace('(', '', $arr[0]);
		$where = 'pid in(' . $arr[0] . ')';
		$sql = " SELECT id,pid FROM userbag WHERE  uid = '" . $_SESSION['id'] . "' AND sums = 1 AND zbing = 0  AND " . $where;
		//echo $sql;
		$res = $_pm['mysql']->getRecords($sql);
		if (count($res) < 1 || empty($res) || !is_array($res)) {
			unLockItem($id);
			die("������û�д���������ƷŶ��");
		}
		shuffle($res);
		$sql = " SELECT name FROM props WHERE id = {$res[0][pid]}";
		$del_name = $_pm['mysql']->getOneRecord($sql);
		$get_things_arr = explode('|', $arr[1]);
		for ($i = 0; $i < count($get_things_arr); $i++) {
			$arr_probability = explode('-', $get_things_arr[$i]);
			$new_arr[$arr_probability[0]] = $arr_probability[1];
		}
		$lucky_num = rand(0, 100);    //Ϊ�˱��ڼ�����ʣ���û���ȡ100
		//echo $lucky_num."<br>";
		asort($new_arr);
		//print_r($new_arr);
		foreach ($new_arr as $key => $val) {
			if ($lucky_num >= $val) {
				$get_pid = $key;
			}
		}
		$sub_p = $_pm['mysql']->query("UPDATE userbag SET sums=sums-1 WHERE id={$rs['id']} and uid={$_SESSION['id']} and sums>0");
		if ($sub_p) {
			echo "��Ʒʹ�óɹ�";
			$_pm['mysql']->query("DELETE FROM  userbag WHERE id={$res[0][id]} and uid={$_SESSION['id']} ");
			if (isset($get_pid)) {

				$sql = " SELECT name,propscolor FROM props WHERE id = {$get_pid}";
				$get_name = $_pm['mysql']->getOneRecord($sql);
				$user = $_pm['user']->getUserById($_SESSION['id']);
				$bag = $_pm['user']->getUserBagById($_SESSION['id']);
				$card_task = new task;
				$card_task->saveGetProps($get_pid);
				if ($get_name['propscolor'] == 6) {
					$word .= "ʹ��" . $rs['name'] . "�����õ���" . $get_name['name'];
					$card_task->saveGword($word);
				}
				echo "<br>�����õ�" . $get_name['name'];
				$str = '�����ɹ�,��ʧuserbag��id:' . $res[0]['id'] . ',props��id:' . $get_pid . '�����Ʒ:' . $get_name['name'];
			} else {
				$card_task = new task;
				$sql = " SELECT nickname FROM player WHERE id = '" . $_SESSION['id'] . "'";
				$user_nickname = $_pm['mysql']->getOneRecord($sql);
				//$word ="ľ��������,���õ�".$del_name['name']."�����".$user_nickname['nickname']."��������,����ôľ����";
				//$card_task->saveGword($word,1);
				$str = '����ʧ����ʧuserbag��id:' . $res[0]['id'] . ',props��id:' . $get_pid;
			}
			//��Ҫ��־
			$_pm['mysql']->query("INSERT INTO gamelog (seller,buyer,ptime,pnote,vary) VALUES({$_SESSION['id']},{$_SESSION['id']}," . time() . ",'$str',177)");
			realseLock();
			unLockItem($id);
		} else {
			unLockItem($id);
			die("��������,����ʧ��");
		}
	} else if ($arr[0] == 'random_combine') {
		require_once('../sec/dblock_fun.php');
		$a = getLock($_SESSION['id']);
		if (!is_array($a)) {
			realseLock();
			unLockItem($id);
			die('��������æ�����Ժ����ԣ�');
		}
		$sysl = $_pm['mysql']->getOneRecord(" SELECT sums FROM userbag WHERE sums > 0 AND uid = {$_SESSION['id']} AND  id={$rs['id']}");
		if (!isset($sysl['sums']) || empty($sysl['sums'])) {
			unLockItem($id);
			die('��Ĳ��ϲ��㣬�޷�����!');
		}
		$settings_of_gain = explode(';', $arr[1]);
		$items_need = explode('|', $settings_of_gain[0]);
		$items_gain = explode('|', $settings_of_gain[1]);

		$sqls_remove_item = array();
		foreach ($items_need as $idx => $it_need) {
			$it_need_setting = explode(',', $it_need);
			if (count($it_need_setting) != 2 || $it_need_setting[1] < 1 || $it_need_setting[0] < 1) {
				unLockItem($id);
				die("��Ҫ��Ʒ�趨��" . $idx . "������!");
			}
			$row = $_pm['mysql']->getOneRecord('select id,sums from userbag where uid=' . $_SESSION['id'] . ' and sums>=' . $it_need_setting[1] . ' and pid=' . $it_need_setting[0]);
			if (!$row) {
				unLockItem($id);
				die("��Ҫ����Ʒ��������!");
			}
			$_pm['mysql']->query('select * from userbag where id=' . $row['id'] . ' for update');
			//$sqls_remove_item[]='update userbag set sums='.($row['sums']-$it_need_setting[1]>-1?$row['sums']-$it_need_setting[1]:0).' where id='.$row['id'];
			$sqls_remove_item[] = 'update userbag set sums=sums - ' . $it_need_setting[1] . ' where id=' . $row['id'] . ' and sums >=' . $it_need_setting[1];
		}

		$gainFlag = false;
		$msg = "";
		foreach ($items_gain as $idx => $it_gain) {
			$it_gain_setting = explode(',', $it_gain);
			$rand = rand(0, 100);
			if ($rand <= $it_gain_setting[1]) {
				$tsk = new task();
				if (!isset($mempropsid)) $mempropsid = unserialize($_pm['mem']->get('db_propsid'));
				if (!isset($mempropsid[$it_gain_setting[0]])) {
					unLockItem($id);
					die("�����Ʒ�趨��" . $idx . "������,��Ʒ" . $it_gain_setting[0] . "������!");
				}
				$tsk->saveGetPropsMore($it_gain_setting[0], $it_gain_setting[2]);
				$msg .= '�ɹ��ϳ�:' . $mempropsid[$it_gain_setting[0]]['name'] . " " . $it_gain_setting[2] . '��!<br/>';
				if ($it_gain_setting[3] > 0) {
					$tsk->saveGword('�ɹ��ϳ�:' . $mempropsid[$it_gain_setting[0]]['name'] . " " . $it_gain_setting[2] . '��!');
				}
				$gainFlag = true;
				break;
			}
		}
		if ($msg == "") {
			$msg = "���ź�,�ϳ�ʧ��,û�л���κ���Ʒ!";
		}
		//ֻҪ������,��Ҫ�۳���Ʒ
		foreach ($sqls_remove_item as $sql) {
			$_pm['mysql']->query($sql);
			if (mysql_affected_rows($_pm['mysql']->getConn()) != 1) {
				unLockItem($id);
				die('ϵͳ��æ�����Ժ������');
			}
		}

		if ($error = mysql_error()) {
			unLockItem($id);
			die("���ִ���:" . $error);
		}
		$_pm['mysql']->query("UPDATE userbag
							     SET sums=sums-1
							   WHERE id={$rs['id']} and uid={$_SESSION['id']} and sums>0
							");

		realseLock();
		unLockItem($id);
		die($msg);
	}
}
else if ($rs['varyname'] == 15) // ������
{
	require_once('../sec/dblock_fun.php');
	$a = getLock($_SESSION['id']);
	if (!is_array($a)) {
		realseLock();
		unLockItem($id);
		die('��������æ�����Ժ����ԣ�');
	}
	$allbb = $_pm['user']->getUserPetById($_SESSION['id']);
	$all = 0;
	if (is_array($allbb)) {
		foreach ($allbb as $x => $y) {
			if ($y['muchang'] != 0) continue;
			$all++;
		}
		if ($all >= 3) {
			unLockItem($id);
			die("��ֻ��Я��3������,ʹ�õ���ʧ�ܣ�<br/>[ϵͳ�Ƽ�]�������԰�����Я���ı������뵽������");
		}
	}

	//$_pm['mysql']->query("START TRANSACTION;");
	$_pm['mysql']->query("UPDATE userbag
								  SET sums=sums-1
							 WHERE id={$id} and uid={$_SESSION['id']} and sums>0
							 ");
	$result = mysql_affected_rows($_pm['mysql']->getConn());
	if ($result != 1) {
		unLockItem($id);
		//$_pm['mysql']->query("ROLLBACK;");
		die("��û����Ӧ����Ʒ��");
	}
	unset($result);
	$arr = explode(':', $rs['effect']);
	if ($arr[0] == "openpet") $newpetsid = $arr[1];

	// ���ݱ���ID�����ɱ������Բ��������ݸ���������ݰ���
	#########################################################################################
	// Get new bb info.
	$bb = $_pm['mem']->dataGet(array('k' => MEM_BB_KEY,
		'v' => "if(\$rs['id'] == '{$newpetsid}') \$ret=\$rs;"
	));

	$czl = getCzl($bb['czl']);

	// insert into userbb.
	//$bbid= $newid = mem_get_autoid($m, MEM_ORDER_KEY, 'userbb');

	$uinfo = $user;

	$_pm['mysql']->query("INSERT INTO userbb(
									   name,
									   uid,
									   username,
									   level,
									   wx,
									   ac,
									   mc,
									   srchp,
									   hp,
									   srcmp,
									   mp,
									   skillist,
									   stime,
									   nowexp,
									   lexp,
									   imgstand,
									   imgack,
									   imgdie,
									   hits,
									   miss,
									   speed,
									   kx,
									   remakelevel,
									   remakeid,
									   remakepid,
									   muchang,
									   czl,
									   headimg,
									   cardimg,
									   effectimg
									  )
					VALUES(
						   '{$bb['name']}',
						   '{$uinfo['id']}',
						   '{$uinfo['nickname']}',
						   '1',
						   '{$bb['wx']}',
						   '{$bb['ac']}',
						   '{$bb['mc']}',
						   '{$bb['hp']}',
						   '{$bb['hp']}',
						   '{$bb['mp']}',
						   '{$bb['mp']}',
						   '{$bb['skillist']}',
						   unix_timestamp(),
						   '{$bb['nowexp']}',
						   '100',
						   '{$bb['imgstand']}',
						   '{$bb['imgack']}',
						   '{$bb['imgdie']}',
						   '{$bb['hits']}',
						   '{$bb['miss']}',
						   '{$bb['speed']}',
						   '{$bb['kx']}',
						   '{$bb['remakelevel']}',
						   '{$bb['remakeid']}',
						   '{$bb['remakepid']}',
						   '0',
						   '{$czl}',
						   't{$bb['id']}.gif',
						   'k{$bb['id']}.gif',
						   'q{$bb['id']}.gif'
						   )
				  ");

	$jnall = split(",", $bb['skillist']);
	foreach ($jnall as $a => $b) {
		$arr = split(":", $b);
		// Get jn info.
		$jn = $_pm['mem']->dataGet(array('k' => MEM_SKILLSYS_KEY,
			'v' => "if(\$rs['id'] == '{$arr[0]}') \$ret=\$rs;"
		));
		// #################################################
		// if ($jn['ackvalue'] == '') continue; // ���Ӹ������ܡ�
		//##################################################

		$ack = split(",", $jn['ackvalue']);
		$plus = split(",", $jn['plus']);
		$uhp = split(",", $jn['uhp']);
		$ump = split(",", $jn['ump']);
		$img = split(",", $jn['imgeft']);

		// Insert userbb jn.
		//$newid = mem_get_autoid($m, MEM_ORDER_KEY,'skill');
		/*��ȡ�ղ������ID��*/
		$newbb = $_pm['mysql']->getOneRecord("SELECT id 
										  FROM userbb
										 WHERE uid={$_SESSION['id']}
										 ORDER BY stime DESC
										 LIMIT 0,1			                                         
									  ");
		$bbid = $newbb['id'];
		$szid=$arr['1']-1;
		$_pm['mysql']->query("INSERT INTO skill(bid,name,level,vary,wx,value,plus,img,uhp,ump,sid)
						VALUES(
							   '{$bbid}',
							   '{$jn['name']}',
							   '{$arr['1']}',
							   '{$jn['vary']}',
							   '{$jn['wx']}',
							   '{$ack[$szid]}',
							   '{$plus[$szid]}',
							   '{$img[$szid]}',
							   '{$uhp[$szid]}',
							   '{$ump[$szid]}',
							   '{$jn['id']}'
							  )
					  ");
	}

	// sub props sum.
	realseLock();
	echo "ʹ�õ��߳ɹ�!";
	// $_pm['mysql']->query("COMMIT;");
	//#######################################################################################
}
else if ($rs['varyname'] == 14) // �������ȡ����
{
	require_once('../sec/dblock_fun.php');
	$a = getLock($_SESSION['id']);
	if (!is_array($a)) {
		realseLock();
		unLockItem($id);
		die('��������æ�����Ժ����ԣ�');
	}
	$arr = explode(':', $rs['effect']);
	if ($arr[0] == "jg") {
		$sql = "SELECT jgvalue FROM battlefield_user WHERE uid = {$_SESSION['id']}";
		$row = $_pm['mysql']->getOneRecord($sql);
		if (!is_array($row)) {
			unLockItem($id);
			die("��Ŀǰû�вμ�ս���������ʹ�ô˵��ߣ�");
		}
		$_pm['mysql']->query("UPDATE battlefield_user
		                         SET jgvalue=jgvalue+{$arr[1]}
							   WHERE uid={$_SESSION['id']}
							");
		// sub props sum.
		$_pm['mysql']->query("UPDATE userbag
						   SET sums=sums-1
						   WHERE id={$id} and uid={$_SESSION['id']} and sums>0
						  ");
		realseLock();
		echo "��ϲ����ʹ�õ��߳ɹ���������� {$arr[1]} �������";
	} else {
		echo '����ʹ��ʧ�ܣ�';
	}

}
else if ($rs['varyname'] == 55) // �츳��
{
	$arr = explode(':', $rs['effect']);
	if ($arr[0] == "xidian") {
		$sql = "SELECT id FROM war_player WHERE id = {$_SESSION['id']}";
		$row = $_pm['mysql']->getOneRecord($sql);
		if (!is_array($row))
			$_pm['mysql']->query("INSERT INTO war_player (`id`, wash_talent_count) VALUES 
                                    ({$_SESSION['id']}, {$arr[1]})
                            ");
		else
			$_pm['mysql']->query("UPDATE war_player
                                 SET wash_talent_count=wash_talent_count+{$arr[1]}
                               WHERE id={$_SESSION['id']}
                            ");

		// sub props sum.
		$_pm['mysql']->query("UPDATE userbag
                           SET sums=sums-1
                           WHERE id={$id} and uid={$_SESSION['id']} and sums>0
                          ");
		echo "��ϲ����ʹ�õ��߳ɹ���������� {$arr[1]} ��ϴ�������";
	} else {
		echo '����ʹ��ʧ�ܣ�';
	}
}
else if ($rs['varyname'] == 57) // ����Я������������
{
	$arr = explode(':', $rs['effect']);

	if (
		in_array($arr[0], array('xiedaibb21', 'xiedaibb31', 'xiedaibb20', 'xiedaibb30'))

	) {
		$xdnum = 1;
		$xdtime = 0;
		switch ($arr[0]) {
			case 'xiedaibb21':
				$xdnum = 2;
				$xdtime = time() + 3600 * 24 * 30;
				break;
			case 'xiedaibb31':
				$xdnum = 3;
				$xdtime = time() + 3600 * 24 * 30;
				break;
			case 'xiedaibb20':
				$xdnum = 2;
				$xdtime = 0;
				break;
			case 'xiedaibb30':
				$xdnum = 3;
				$xdtime = 0;
				break;
		}

		$xdtimestr = $xdtime == 0 ? "����" : date("Y/m/d H:i", $xdtime);
		$sql = "SELECT max_take_pet_num_save,max_take_pet_num, take_pet_limit_time FROM war_player WHERE id = {$_SESSION['id']}";
		$row = $_pm['mysql']->getOneRecord($sql);

		if ($row['take_pet_limit_time'] < time() && $row['take_pet_limit_time'] > 0)//�������
		{
			$_pm['mysql']->query("
							   UPDATE war_player
                                 SET max_take_pet_num=max_take_pet_num_save,take_pet_limit_time=0
                               WHERE id={$_SESSION['id']}
                            ");
			$row['take_pet_limit_time'] = 0;
			$row['max_take_pet_num'] = $row['max_take_pet_num_save'];
		}

		if ($row['max_take_pet_num'] > 2 && $row['take_pet_limit_time'] == 0) {
			die("������Я���ĳ��������Ѿ������ֵ��");
		}

		if ($row['take_pet_limit_time'] > time() && !isset($_GET['cofxiedaibb'])) {
			die('��Ŀǰ����Я��' . $row['max_take_pet_num'] . 'ֻ����������ʱ��Ϊ��' . date("Y/m/d H:i", $row['take_pet_limit_time']) . '��<br/><font color="#f00">����������״̬�������ǣ�</font><br/>ȷ�����<a href="javascript:bid=\'' . $id . '&cofxiedaibb=1\';Used();setTimeout(\'bid=' . $id . '\',500);this.style.display=\'none\';void(0);"><strong>����</strong></a>��');
		}

		$_pm['mysql']->query("START TRANSACTION");

		if (!is_array($row)) {
			//echo "********************************************";
			$row_czl = $_pm['mysql']->getOneRecord("select czl,wx from userbb where uid=" . $_SESSION['id'] . " order by czl desc");
			$_pm['mysql']->query("INSERT INTO war_player (`id`,name, max_take_pet_num, take_pet_limit_time,grow_up,wuxing) VALUES 
                                    ({$_SESSION['id']}, '" . $_SESSION['username'] . "'," . $xdnum . "," . $xdtime . "," . $row_czl['czl'] . "," . $row_czl['wx'] . ")
                            ");
			$_pm['mysql']->query("UPDATE userbag
                               SET sums=sums-1
                               WHERE id={$id} and uid={$_SESSION['id']} and sums>0
                              ");
		} else {
			//echo "-------------------------------------------";
			$_pm['mysql']->query("UPDATE war_player
                                 SET max_take_pet_num_save='" . ($row['take_pet_limit_time'] > time() ? $row['max_take_pet_num_save'] : $row['max_take_pet_num']) . "',max_take_pet_num='" . $xdnum . "',take_pet_limit_time='" . $xdtime . "'
                               WHERE id={$_SESSION['id']}
                            ");
			$_pm['mysql']->query("UPDATE userbag
                               SET sums=sums-1
                               WHERE id={$id} and uid={$_SESSION['id']} and sums>0
                              ");
		}

		if (!mysql_error()) {
			$_pm['mysql']->query("COMMIT");
			echo "��ϲ����ʹ�õ��߳ɹ������ĳ���Я���������Ϊ:" . $xdnum . "����Чʱ����:" . $xdtimestr . "��";
		} else {
			$_pm['mysql']->query("ROLLBACK");
			echo "ʹ�õ���ʧ��,��������δ���٣�";
		}
		echo time();
		// sub props sum.

	} else {
		echo '����ʹ��ʧ�ܣ�';
	}
}
else if ($rs['varyname'] == 58) // ����Я������������
{
	if (!is_array($bb)) {
		unLockItem($id);
		die('����û��������ս����������ʹ��������ߣ�');
	}

	$arr = explode(':', $rs['effect']);
	if (
		in_array($arr[0], array('tianfuexp')) && count($arr) == 2

	) {
		$exp = explode(',', $arr[1]);
		if (count($exp) == 2) {
			if ($exp[0] < 1 || $exp[0] > $exp[1]) {
				unLockItem($id);
				die("��������������ݴ���{$arr[1]}����");
			}
			$expGet = (int)rand($exp[0], $exp[1]);
		} else {
			$expGet = (int)$exp[0];
		}

		$sql = 'select
					id,current_experience
				from
					war_fighter_talent
				where
					fighter_id=' . $bb['id'] . '';

		$ts = $_pm['mysql']->getRecords($sql);


		if (empty($ts) || !is_array($ts)) {
			unLockItem($id);
			die("��û�н����ħ����û��ħ�����ݣ�");
		}

		if ($err = mysql_error()) {
			unLockItem($id);
			die("��ѯ����" . $err);
		}

		$expGetAver = ceil($expGet / count($ts));

		$_pm['mysql']->query("START TRANSACTION");
		foreach ($ts as $row) {
			$sql = 'update war_fighter_talent set current_experience=' . ($row['current_experience'] + $expGetAver) . ' where id=' . $row['id'];
			$_pm['mysql']->query($sql);
		}

		if (!$err = mysql_error()) {
			$_pm['mysql']->query("COMMIT");
			echo "ʹ�õ��߳ɹ�����ս�����Ѿ����츳ƽ�־��飺" . $expGet . "��";
		} else {
			$_pm['mysql']->query("ROLLBACK");
			echo "ʹ�õ���ʧ��,��������δ����($err)��";
		}
	} else {
		echo '����ʹ��ʧ�ܣ��������ݴ���';
	}
}

function rand_num($config)
{
	global $_pm;
	$rand = rand(10000, 99999);
	$num = $config . $rand;
	$_pm['mysql']->query('INSERT INTO ticket_' . date('Ymd') . ' SET uid=' . $_SESSION['id'] . ',ticket_num="' . $num . '"');
	$result = mysql_affected_rows($_pm['mysql']->getConn());
	if ($result != 1) {
		return rand_num($config);
	} else {
		return $num;
	}
}

unLockItem($id);
$_pm['mem']->memClose();
unset($m, $u, $db, $user, $bags, $rs);
?>
