<?php
require_once('../config/config.game.php');
secStart($_pm['mem']);
define("ZS", "db_welcome1");
require_once('../sec/dblock_fun.php');
$a = getLock($_SESSION['id']);
if (!is_array($a)) {
    realseLock();
    die('11');
}
$ap = intval($_REQUEST['ap']);  // table userbb->id
$bp = intval($_REQUEST['bp']);  // table userbb->id
$p1 = intval($_REQUEST['p1']);  // table userbag->id
$zs = intval($_REQUEST['zs']);  // table userbag->id
$srctime = 30;
$chouqu_chk_ext = $_pm['mysql']->getRecords("select 1 from userbb where wx=6 and name not like '������%' and czl=1 and uid={$_SESSION['id']} and (id=$ap or id=$bp)");
if (is_array($chouqu_chk_ext) && count($chouqu_chk_ext) > 0) {
    die("ĳ�������Ѿ���ȡ���ɳ�,���ܽ�������!");
}

$cishu = $_pm['mysql']->getOneRecord("select chouqu_chongwu from player_ext where uid={$_SESSION['id']}");
if (strpos($cishu['chouqu_chongwu'], ',' . $ap . ',') !== false || strpos($cishu['chouqu_chongwu'], ',' . $bp . ',') !== false) {
    die("ĳ�������ȡ���ɳ�,���ܽ�������!");
}

if ($ap < 0 || $bp < 0) {
    realseLock();
    die();
}
#################����һ�����ʱ��################
$time = $_SESSION['time' . $_SESSION['id']];
if (empty($time)) {
    $_SESSION['time' . $_SESSION['id']] = time();
} else {
    $nowtime = time();
    $ctime = $nowtime - $time;
    if ($ctime < $srctime && $_GET['type'] != 'do' && $_GET['type1'] != 'check') {
        realseLock();
        die("11");//û�дﵽ���ʱ��
    } else {
        $_SESSION['time' . $_SESSION['id']] = time();
    }
}
exit();

if ($p1 < 0) $p1 = 0;
$p2 = intval($_GET['p2']);
if ($p2 < 0) {
    $p2 = 0;
}


#################�Ƿ�ѡ�˻�����ʯ����############
if ($_GET['type1'] != 'check') //�ж�һ�ξ͹���
{
    $sql_props = 'SELECT pid FROM userbag WHERE (id=' . $p1 . ' or id=' . $p2 . ') and uid=' . $_SESSION['id'];
    $props = $_pm['mysql']->getRecords($sql_props);
    if (is_array($props)) {
        $check_props = 0;
        foreach ($props as $key_props => $key_value)//Array ( [pid] => 771 )
        {
            $a = 'SELECT effect FROM props WHERE varyname=8 and id=' . $key_value['pid'];
            $cmpProps = $_pm['mysql']->getOneRecord($a);
            if (is_array($cmpProps))//Array ( [effect] => npbb:1,npcg:3000%,npcz:15% )
            {
                $key_values = strpos($cmpProps['effect'], 'npbb');
                if ($key_values !== false) {

                    $check_props = $check_props + 1;
                }
            }

        }
        if ($check_props == 0) {
            realseLock();
            die('200');
        }
    } else {
        realseLock();
        die('200');
    }
}

if ($_GET['type'] != 'do') {
    $zbcheck = $_pm['mysql']->getRecords("SELECT id FROM userbag WHERE zbpets = $ap or zbpets = $bp or zbpets = $zs");
    if (count($zbcheck) >= 1) {
        realseLock();
        realseLock();
        die('1000');
    }
}


##################�������������#################
if ($ap < 0 || $bp < 0 || $zs < 0) {
    realseLock();
    die('0');
}

$pp2 = array();

if(lockItem($ap) === false)
{
	die('�Ѿ��ڴ����ˣ�');
}

$user = $_pm['user']->getUserById($_SESSION['id']);
$userbb = $_pm['user']->getUserPetById($_SESSION['id']);
$userbag = $_pm['user']->getUserBagById($_SESSION['id']);
$log = '';
if (is_array($userbb) && is_array($userbag)) {
    $membbid = unserialize($_pm['mem']->get('db_bbname'));
    foreach ($userbb as $key => $rs) {
        if ($rs['id'] == $ap && $rs['level'] >= 60) // From bb base find user current bb.
        {
            $app = $rs;
        } else if ($rs['id'] == $bp && $rs['level'] >= 60) {
            $bpp = $rs;
        } else if ($rs['id'] == $zs && $rs['level'] >= 60 && ($rs['name'] == "�����ޣ�����" || $rs['name'] == "�����ޣ��磩" || $rs['name'] == "�����ޣ�î��" ) && $rs['muchang'] == 0) {
            $zsp = $rs;
        }
    }
    if ($app['wx'] > 6 || $bpp['wx'] > 6 || $zsp['wx'] > 6) {
        realseLock();
        die('�������ڣ���ľ��ˮ����������Ĳſ��Խ��д˲�����');
    }
    unset($rs);
    $ars2 = $membbid[$app['name']];
    $brs2 = $membbid[$bpp['name']];
    $cmprs2 = $_pm['mysql']->getOneRecord("SELECT * 
										FROM zs
									   WHERE aid = {$ars2['id']} and bid={$brs2['id']}
									   LIMIT 0,1
									");
    if (!is_array($cmprs2)) {
        realseLock();
        unLockItem($ap);
        die('2');    //���ܺϳɣ�
    }


    if (!is_array($app) || !is_array($bpp) || ($app['id'] == $bpp['id'])) {
        unLockItem($ap);
        realseLock();
        die('1'); //û�ж�Ӧ�ĳ��
    }

    if (!is_array($zsp)) {
        realseLock();
        unLockItem($ap);
        die("7");//��ѡ��������
    }
  

    // ����Ƿ����㹫ʽ��
    /*$ars = $_pm['mem']->dataGet(array('k' => MEM_BB_KEY,
                                         'v' => "if(\$rs['name'] == '{$app['name']}') \$ret=\$rs;"
                              ));
    $brs = $_pm['mem']->dataGet(array('k' => MEM_BB_KEY,
                                         'v' => "if(\$rs['name'] == '{$bpp['name']}') \$ret=\$rs;"
                              ));*/

    $ars = $membbid[$app['name']];
    $brs = $membbid[$bpp['name']];

    $cmprs = $_pm['mysql']->getOneRecord("SELECT * 
											FROM zs
										   WHERE aid = {$ars['id']} and bid={$brs['id']}
										   LIMIT 0,1
	                                    ");
    if (!is_array($cmprs)) {
        realseLock();
        unLockItem($ap);
        realseLock();
        die('2');    //���ܺϳɣ�
    }


    // �жϽ�����ģ�
    $money = 500000;

    if ($user['money'] < $money) {
        unLockItem($ap);
        die('3');    //	��Ҳ��㡣
    }
    foreach ($userbag as $k => $rs) {
        if ($rs['varyname'] == 19 && $rs['id'] == $p2 && is_array($cmprs2)) {
            $pp2 = $rs;
            $_pm['mysql']->query("UPDATE userbag SET sums = sums - 1 WHERE sums >= 1 and uid = {$_SESSION['id']} and id = $p2");
            $result = mysql_affected_rows($_pm['mysql']->getConn());
            if ($result != 1) {
                realseLock();
                die("��û����Ӧ����Ʒ��");
            }
            continue;
        }
        if ($rs['varyname'] != 8 || $rs['effect'] == '' || empty($rs['effect']) || $rs['sums'] < 1) continue;
        if ($rs['id'] == $p1 && $rs['sums'] >= 1) {
            $pp1 = $rs;
        }
    }

    //$propseff = getEffect($pp1, $pp2);
    //�õ�ʹ����Ʒ��Ч��
    if (is_array($pp1)) {
        $one = explode(",", $pp1['effect']);
        foreach ($one as $b) {
            $arr[] = explode(":", $b);
        }
    }
    $zsflag = 0;
    $psuc = 0;
    $pczl = 0;
    if (is_array($arr)) {
        foreach ($arr as $a) {
            switch ($a[0]) {
                case "npbb":
                    $zsflag = $a[1];
                    break;//�����޲���ʧ
                case "npcg":
                    $psuc = str_replace('%', '', $a[1]) / 100;
                    break;//���ӳɹ�����
                case "npcz":
                    $pczl = str_replace('%', '', $a[1]) / 100;
                    break;//���ӳɳ�
            }
        }
    }


    // �õ��ɹ���.
    //$sus = getSuccess($propseff);
    //�õ��ɳ��ʣ�[(����ȼ�/15+����ȼ�/15)*(100%+�������Ӱٷֱ�)]*100%
    $sus = round(($app['level'] / 30 + $bpp['level'] / 30) * (1 + $psuc), 2);
    $pp2eff = 0;
    if (count($pp2) >= 1) {
        $pp2arr = explode(':', $pp2['effect']);
        if ($pp2arr[0] == 'addcz') {
            $pp2eff = str_replace('%', '', $pp2arr[1]) * 0.01;
        }
    }
    $czl = bbczl($app, $bpp, $pczl, $zsp, $pp2eff);
    if($czl ==0){
        realseLock();
        unLockItem($ap);
        die("10");
    }
    $susnum = rand(1, 10000);
    $a = $sus * 100;
    if ($susnum <= $a) // �ϳɳɹ���a,b������ʧ���õ��µĳ��$cmprs=> �õ���ر�����Ϣ��
    {
        // �ı����Եط�Ϊ:
        $newbid = $cmprs['mid'];

        $brs = $_pm['mysql']->getOneRecord("SELECT * 
											  FROM  bb
											 WHERE id={$newbid}
											 LIMIT 0,1
										  ");

        if (!is_array($brs)) {
            realseLock();
            unLockItem($ap);
            die('10'); // ���ݴ���
        }
        // �ı��������:
        makebb($brs, $czl);
        $cstatus = 2;
    } else // ���û����ص��߽��а󶨣�������ʧ
    {
        $cstatus = 1;
    }

    $user['money'] = $user['money'] - $money;        // �����û����.
    $_pm['mysql']->query("UPDATE player
						     SET money='{$user['money']}'
					 	   WHERE id={$_SESSION['id']}
				  		");
    // ��¼��־��
    $log .= "�ϳɽ����" . ($cstatus == 1 ? "ʧ��" : "�ɹ�") . "\n";
    $log .= "�ϳɵ��ߣ�1:" . $pp1['name'] . '���ϳɵ��ߣ�2:' . $pp2['name'] . ' ����:' . $zsp['id'] . "\n";

    //######### del props Start.##################
    delProps();
    ############# del props end.#####################

    if ($cstatus == 1) //��������ʧ��
    {
        $log .= '�ϳɵ�����ϸ��';
        if (is_array($propseff)) {
            foreach ($propseff as $m => $n) {
                //$log .= $n['shbb']."-";
                if ($n['shbb'] === true) {
                    $del = 0;
                    break;
                }
            }
        }
        if ($zsflag != 1) {
            clearBB($zsp);
            $log .= 'name:' . $zsp['name'] . 'level:' . $zsp['level'] . 'czl:' . $zsp['czl'] . 'hp:' . $zsp['srchp'] . 'hits:' . $zsp['hits'] . 'ac:' . $zsp['ac'];
        }
        $log = addslashes($log);
        // �ϳ�ʧ�ܼ�¼�㣺
        $_pm['mysql']->query("INSERT INTO gamelog(ptime,seller,buyer,pnote,vary)
		                      VALUES(unix_timestamp(),'{$_SESSION['id']}','{$_SESSION['id']}','{$log}',10)
							");
        unLockItem($ap);
        realseLock();
        die('6');
    } else if ($cstatus == 2) // �ɹ���
    {
        $msg_key = 'chatMsgList';
        $nowMsgList = unserialize($_pm['mem']->get($msg_key));
        $arr = split('linend', $nowMsgList);
        if (count($arr) > 20) // cear old
        {
            $arrt = array_shift($arr);
        }
        $newstr = '<font color=red>[ϵͳ����]��ϲ��� ' . $user['nickname'] . ' �ı�������ʥ���ϴ�񣬳ɹ���ת����Ϊ[' . $brs['name'] . ']!</font>';
        $newbbarr = $_pm['mysql']->getOneRecord("SELECT level,czl,ac,hits,srchp FROM userbb WHERE name = '{$brs[name]}' and uid = {$_SESSION['id']} ORDER BY id DESC LIMIT 1");
       $str = '�³������֣�' . $brs['name'] . 'level:' . $newbbarr['level'] . 'czl:' . $newbbarr['czl'] . 'ac:' . $newbbarr['ac'] . 'hits:' . $newbbarr['hits'] . ',ʹ����Ʒ��' . $pp1['name'] . ',�����ޣ�' . $zsp['name'] . 'level:' . $zsp['level'] . 'czl:' . $zsp['czl'] . 'ac:' . $zsp['ac'] . 'hits:' . $zsp['hits'] . ',���' . $app['name'] . 'level:' . $app['level'] . 'czl:' . $app['czl'] . 'ac:' . $app['ac'] . 'hits:' . $app['hits'] . '-' . $bpp['name'] . 'level:' . $bpp['level'] . 'czl:' . $bpp['czl'] . 'ac:' . $bpp['ac'] . 'hits:' . $bpp['hits'];
        $_pm['mysql']->query("INSERT INTO gamelog(ptime,seller,buyer,pnote,vary)
		                      VALUES(unix_timestamp(),'{$_SESSION['id']}','{$_SESSION['id']}','{$str}',11)
							");
        $addczl=$czl-$app["czl"];
    
        foreach ($arr as $k => $v) {
            $retstr .= $v . 'linend';
        }

        $retstr = $retstr . $newstr;
        $_pm['mem']->set(array('k' => $msg_key, 'v' => $retstr)); // default ten min.

        //----------------------------------------------------------------------------------------------------------------------
        //$_olddata = @unserialize($_pm['mem']->get('ttmt_data_notice'));

        $swfData =  '��ϲ��� ' . $user['nickname'] . ' �ı�������ʥ���ϴ�񣬳ɹ���ת����Ϊ[' . $brs['name'] . ']!';
        require_once(dirname(__FILE__) . '/../socketChat/config.chat.php');
        $s = new socketmsg();
        $s->sendMsg('an|' . $swfData);
        realseLock();
        clearBB($app); // del pets master

        clearBB($bpp); // del pets other
        clearBB($zsp);
        unLockItem($ap);
        die('5');
    }
} else {
    unLockItem($ap);
    realseLock();
    die('000');
}
$_pm['mem']->memClose();
// Logic code end.
realseLock();


/**
 * @Usage: �����µĳ��
 * @Param: array -> $bb. �³���
 * @Return: Void(0);
 */
function makebb($bb, $czl)
{
    global $app, $bpp, $user, $_pm, $zsp, $pp2;
    //$czl = bbczl($app,$bpp,$pczl,$zsp);
    $pac = $pmc = $phits = $php = 0;
    if (count($pp2) > 0) {
        $arr = explode(':', $pp2['effect']);
        switch ($arr[0]) {
            case 'addac':
                $pac = str_replace('%', '', $arr[1]) * 0.01;
                break;
            case 'addmc':
                $pmc = str_replace('%', '', $arr[1]) * 0.01;
                break;
            case 'addhits':
                $phits = str_replace('%', '', $arr[1]) * 0.01;
                break;
            case 'addhp':
                $php = str_replace('%', '', $arr[1]) * 0.01;
                break;
        }
    }
    // ac,luck,mc,hit,miss,speed,hp,mp,shbb;
    $bb['ac'] = getPa($bb['ac'], $app['ac'], $bpp['ac'], $pac);  #### ��ʱû�м�����߸������ԡ�
    $bb['mc'] = getPa($bb['mc'], $app['mc'], $bpp['mc'], $pmc);
    $bb['hits'] = getPa($bb['hits'], $app['hits'], $bpp['hits'], $phits);
    $bb['miss'] = getPa($bb['miss'], $app['miss'], $bpp['miss'], 0);
    $bb['speed'] = getPa($bb['speed'], $app['speed'], $bpp['speed'], 0);
    $bb['hp'] = getPa($bb['hp'], $app['hp'], $bpp['hp'], $php);
    $bb['mp'] = getPa($bb['mp'], $app['mp'], $bpp['mp'], 0);
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

        $memskillsysid = unserialize($_pm['mem']->get('db_skillsysid'));
        $jn = $memskillsysid[$arr[0]];
        // #################################################
    //   if ($jn['ackvalue'] == '') continue; // ���Ӹ������ܡ�
        //##################################################

        $ack = split(",", $jn['ackvalue']);
        $plus = split(",", $jn['plus']);
        $uhp = split(",", $jn['uhp']);
        $ump = split(",", $jn['ump']);
        $img = split(",", $jn['imgeft']);

        // Insert userbb jn.
        /*��ȡ�ղ������ID��*/
        $newbb = $_pm['mysql']->getOneRecord("SELECT id 
									  FROM userbb
									 WHERE uid={$_SESSION['id']}
									 ORDER BY stime DESC
									 LIMIT 0,1			                                         
								  ");
        $bbid = $newbb['id'];

        $_pm['mysql']->query("INSERT INTO skill(bid,name,level,vary,wx,value,plus,img,uhp,ump,sid)
					VALUES(
						   '{$bbid}',
						   '{$jn['name']}',
						   '{$arr['1']}',
						   '{$jn['vary']}',
						   '{$jn['wx']}',
						   '{$ack[0]}',
						   '{$plus[0]}',
						   '{$img[0]}',
						   '{$uhp[0]}',
						   '{$ump[0]}',
						   '{$jn['id']}'
						  )
				  ");

        ####################����######################
        /*$wararr1 = $_pm['mysql'] -> getOneRecord("SELECT fighter_id FROM war_fighter WHERE fighter_id = {$app['id']}");
        if(is_array($wararr1)){
            $_pm['mysql'] -> query("UPDATE war_fighter SET fighter_id = {$bbid} WHERE fighter_id = {$app['id']}");
        }
        $wararr2 = $_pm['mysql'] -> getOneRecord("SELECT fighter_id FROM war_fighter_talent WHERE fighter_id = {$app['id']}");
        if(is_array($wararr2)){
            $_pm['mysql'] -> query("UPDATE war_fighter_talent SET fighter_id = {$bbid} WHERE fighter_id = {$app['id']}");
        }*/
        ####################�������������######################

        ##################################�ϳɳɹ������û��赱ǰ����Ϊ��ս����#########################################
        $sql = "UPDATE player
				SET mbid = {$bbid}
				WHERE id = {$_SESSION['id']}";
        $_pm['mysql']->query($sql);
        ###################################���������##################################################################
    }
}

/**
 * @Usage: ɾ��һ������;
 * @Param: Array -> $bb.
 * @Return: Void(0);
 */
function clearBB($bb)
{
    global $_pm, $log;
    $id = $bb['id'];

    foreach ($bb as $k => $v) {
        $log .= $k . '=>' . $v . '-';
    }

    // del sk.
    $_pm['mysql']->query("DELETE FROM skill
				 WHERE bid={$id}
			  ");

    // del zb.
    $_pm['mysql']->query("DELETE FROM userbag
				 WHERE uid={$_SESSION['id']} and zbpets={$id}
			  ");
    // del bb.
    $_pm['mysql']->query("DELETE FROM userbb
				 WHERE uid={$_SESSION['id']} and id={$id}
			  ");

}

unLockItem($ap);
/**
 * @Param: ����a,b�����ԡ�
 * @Return: �������ĳɳ��ʡ�
 * czl:ʵ�ʳɳ���=��Ӧ�����������ݿ�ɳ�������+ȡ1λС��{[ȡһλС����������ɳ�*������ȼ�/120��+ȡһλС����������ɳ�*������ȼ�/240��+rand(������ɳ�/10,������ɳ�/10)]* (100%+���߸�������%)}
 * rand(������ɳ�/10,������ɳ�/10)
 * ��˼��:ȡ������ĳɳ�ֵ/10��������ɳ�ֵ/10�������
 * �縱����ɳ�10,������ɳ�20
 * ��: rand(1,2)
 */
function bbczl($a, $b, $pp1, $zs, $pp2)
{
    global $brs;
    global $_pm;
    $zsarr = unserialize($_pm['mem']->get(ZS));
    // ���Ͽ��г������ԡ�
    if ($zs['name'] == '�����ޣ�î��') {
        $lv = 0.3;
    } else if ($zs['name'] == '�����ޣ��磩') {
        $lv = 0.15;
    } else if ($zs['name'] == '�����ޣ�����') {
        $lv = 0.05;
    }
    //if($a['name'] == 'С�����ūe' || $a['name'] == '��������' || $a['name'] == '�����컢��' || $a['name'] == '�׻�' || $a['name'] == '�����������' || $a['name'] == 'ʥ�޳���¹' || $a['name'] == '����Ӱ�ɪ' || $a['name'] == '�ȼ���' || $a['name'] == 'GM-Ѽ��' || $a['name'] == '����С�ڹ�' || $a['name'] == '������' || $a['name'] == '��������' || $a['name'] == '��Ҷ�ݱ���')
    $zs1 = explode(",", $zsarr['zs1']);
    $zs2 = explode(",", $zsarr['zs2']);
    $zs3 = explode(",", $zsarr['zs3']);
    if (in_array($a['name'], $zs1)) {
        if ($a['czl'] >= 1.0 && $a['czl'] <= 10.9) {
            $num1 = 1;
            $num2 = 200;
        } else if ($a['czl'] > 10.9 && $a['czl'] <= 30.9) {
            $num1 = 1;
            $num2 = 250;
        } else if ($a['czl'] > 30.9 && $a['czl'] <= 49.9) {
            $num1 = 1;
            $num2 = 350;
        } else if ($a['czl'] > 49.9 && $a['czl'] <= 60.9) {
            $num1 = 1;
            $num2 = 480;
        } else if ($a['czl'] > 60.9 && $a['czl'] <= 70.9) {
            $num1 = 1;
            $num2 = 600;
        } else if ($a['czl'] > 70.9 && $a['czl'] <= 80.9) {
            $num1 = 1;
            $num2 = 800;
        } else if ($a['czl'] > 80.9 && $a['czl'] <= 90.9) {
            $num1 = 2;
            $num2 = 1200;
        } else if ($a['czl'] > 90.9) {
            $num1 = 2;
            $num2 = 2200;
        }
    } else if (in_array($a['name'], $zs2)) {//else if($a['name'] == '��èorz����' || $a['name'] == '������' || $a['name'] == 'ѩ����' || $a['name'] == '��Ů����ɯ')
        if ($a['czl'] >= 1.0 && $a['czl'] <= 10.9) {
            $num1 = 1;
            $num2 = 190;
        } else if ($a['czl'] > 10.9 && $a['czl'] <= 30.9) {
            $num1 = 1;
            $num2 = 240;
        } else if ($a['czl'] > 30.9 && $a['czl'] <= 49.9) {
            $num1 = 1;
            $num2 = 340;
        } else if ($a['czl'] > 49.9 && $a['czl'] <= 60.9) {
            $num1 = 1;
            $num2 = 470;
        } else if ($a['czl'] > 60.9 && $a['czl'] <= 70.9) {
            $num1 = 1;
            $num2 = 590;
        } else if ($a['czl'] > 70.9 && $a['czl'] <= 80.9) {
            $num1 = 1;
            $num2 = 780;
        } else if ($a['czl'] > 80.9 && $a['czl'] <= 90.9) {
            $num1 = 2;
            $num2 = 1100;
        } else if ($a['czl'] > 90.9) {
            $num1 = 2;
            $num2 = 1800;
        }
    } else if (in_array($a['name'], $zs3)) {//else if($a['name'] == '�ﺮ��ѩ��' || $a['name'] == '����ѩ����' || $a['name'] == '��ȻŮ��Ӱ' || $a['name'] == '��ҹŮ��Ӱ')
        if ($a['czl'] >= 1.0 && $a['czl'] <= 10.9) {
            $num1 = 1;
            $num2 = 180;
        } else if ($a['czl'] > 10.9 && $a['czl'] <= 30.9) {
            $num1 = 1;
            $num2 = 230;
        } else if ($a['czl'] > 30.9 && $a['czl'] <= 49.9) {
            $num1 = 1;
            $num2 = 330;
        } else if ($a['czl'] > 49.9 && $a['czl'] <= 60.9) {
            $num1 = 1;
            $num2 = 450;
        } else if ($a['czl'] > 60.9 && $a['czl'] <= 70.9) {
            $num1 = 1;
            $num2 = 570;
        } else if ($a['czl'] > 70.9 && $a['czl'] <= 80.9) {
            $num1 = 1;
            $num2 = 760;
        } else if ($a['czl'] > 80.9 && $a['czl'] <= 90.9) {
            $num1 = 2;
            $num2 = 1000;
        } else if ($a['czl'] > 90.9) {
            $num1 = 2;
            $num2 = 1500;
        }
    }
    //������ɳ�+{[(������ȼ�/������ɳ�./2)+(������ȼ�*������ɳ�/1500)]*(100%+�����ްٷֱ�+���߰ٷֱ�)}
	$czl =$a['czl'] + round(((($a['level'] / $a['czl'] / $num1) + ($b['level'] * $b['czl'] / $num2)) * (1 + $lv + $pp1 + $pp2)),1);
	//echo $a['czl'].'+round(((('.$a['level'].'/'.$a['czl'].'/'.$num1.')+('.$b['level'].'*'.$b['czl'].'/'.$num2.'))*(1+'.$lv.'+'.$pp1.'+'.$pp2.')),1)'.'<br />';
	return $czl;
}


/*
*@Usage:����ϳɺ�ĳ��ﵥһ���ԡ�
* a,b,p=> $props attrib.
*@Return: int.
*@Memo ����=[�����������ݿ�����+ȡ��������������*������ȼ�/400��+ȡ��������������*������ȼ�/800��]*(100%+���߸�������%)
*/
function getPa($old, $a, $b, $p)
{    //echo $p.'<br />';
    global $app, $bpp;
    if ($p == '' || $p <= 0) $p = 1;
    else $p = 1 + $p;
    $res = intval(($old + (intval($a * $app['level'] / 400) + intval($b * $bpp['level'] / 800))) * $p);

    return $res;
}


/**
 * @Usage: ɾ����ӵ��ϳ��еĲ��ϡ�
 * @Param:  void(0)
 * @Return: void(0)
 */
function delProps()
{
    global $pp1, $_pm;    // props first,props second, global object array.
    if (is_array($pp1)) {
        $_pm['mysql']->query("UPDATE userbag
								 SET sums=abs(sums-1)
						       WHERE id={$pp1['id']} and uid={$_SESSION['id']} and sums > 0
							");
    }
}

?>
