<?php
/**
 * This script is only used to some additional operation 
 * such as sorting paihang ranking
 * It is a tool only used by GM, other users are denied.
 *
 * @date 2009-06-11 15:16
 * @author Zheng.Ping
 */


session_start();
require_once('../config/config.game.php');
secStart($_pm['mem']);
header('Content-Type:text/html;charset=GBK');


define('CHECK_HOSTS', false);
define('ALLOWED_HOSTS', '125.69.81.43');
define('CHECK_USER', false);
define('PAIHANG_USER', 'piwai123,donasky');

define('ACTION_PAIHANG', 'paihang');
define("SORT_PAIHANG_TIME","20090626 11:00");


$allowed_hosts = explode(',', ALLOWED_HOSTS);
$paihang_users = explode(',', PAIHANG_USER);
$goto_game     = true;

//var_dump($_SESSION);
if (!CHECK_HOSTS || in_array($_SERVER['REMOTE_ADDR'], $allowed_hosts)) {
    // only the special user from the allowed IP address can access the service
    // if the user is not allowed, redirect to the game entrance.
    //if (in_array($_SESSION['username'], $paihang_users)) { 
    if (isset($_REQUEST['action']) && $_REQUEST['action'] == ACTION_PAIHANG) {
        if (CHECK_USER) {
            if (in_array($_SESSION['username'], $paihang_users)) { 
                // user can do operation of paihang
                $action = ACTION_PAIHANG;
            }
        } else {
            $action = ACTION_PAIHANG;
        }
    }
    //}

    $goto_game = false;
}


// handle for ajax request
if (isset($_SERVER['HTTP_X_REQUESTED_WITH'])
    && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') {
    if (isset($action)) {
        switch ($action) {
        case ACTION_PAIHANG:
            if (isset($_REQUEST['ope']) ) {
                if ($_REQUEST['ope'] == 'check') {
                    if (is_sorted_paihang()) {
                        echo "{code:1}";
                    } else {
                        echo "{code:2}";
                    }
                } elseif ($_REQUEST['ope'] == 'show') {
                    echo get_player_paihang_list(); 
                }  elseif (isset($_POST['ope']) && $_POST['ope'] == 'sort') {
                    if (sort_player_paihang()) {
                        echo "{code:2}";
                    } else {
                        echo "{code:1}";
                    }
                } elseif (isset($_POST['ope']) && $_POST['ope'] == 'fsort') {
                    clear_player_paihang();
                    if (sort_player_paihang()) {
                        echo "{code:2}";
                    } else {
                        echo "{code:1}";
                    }
                }
            }
            break;
        default:
            break;
        }
        exit(0);
    } else {
        echo "{code:202}";
        exit(1);
    }
}

if (CHECK_HOSTS && $goto_game) {
    header("location:../");
    exit(1);
}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=gbk" />
<title>�������</title>
<script type="text/javascript" src="../javascript/lib/jquery.js"></script>
<style>
<!--
body
{
	margin:0; padding:0; font-size:12px; font-family:Arial, Helvetica, sans-serif; background: #FFF;background-repeat: repeat-x;
}
.clear{clear:both; font-size:0; line-height:0}
.box{ width:100%;}
.font1{ color:#003; font-size:24px; font-weight:bold;}
-->
</style>
<script type="text/javascript">
<!--
function trigger_paihang_action() {
    $('#content_area').hide();
    $.get('<?php echo $_SERVER['PHP_SELF']; ?>', {action:'paihang', ope:'check', t:Math.random()}, function(data) {check_paihang(data);});
}

function check_paihang(data) {
    try {
        var ret = eval('(' + data + ')');
        
        if (ret['code']) {
            if (ret['code'] == 1) {
                alert('�Ѿ�����������ֵ��');
            } else if (ret['code'] == 2) {
                $.post('<?php echo $_SERVER['PHP_SELF']; ?>', {action:'paihang', ope:'sort'}, function(data) {sort_paihang(data);});
            }
        } else {
            alert('���ش���!');
        }
    } catch(e) {
    }
}

function sort_paihang() {
    $.post('<?php echo $_SERVER['PHP_SELF']; ?>', {action:'paihang', ope:'sort'}, function(data) {response_sorting(data);});
}

function force_paihang_action() {
    $('#content_area').hide();
    if (confirm('��ȷ��Ҫ���г弶���в��������������еĳ�����а�!')) {
        $.post('<?php echo $_SERVER['PHP_SELF']; ?>', {action:'paihang', ope:'fsort'}, function(data) {response_sorting(data);});
    }
}

function response_sorting(data) {
    try {
        var ret = eval('(' + data + ')');
        
        if (ret['code']) {
            if (ret['code'] == 1) {
                alert('���и�ֵʧ��!');
            } else if (ret['code'] == 2) {
                alert('���и�ֵ�ɹ�!');
            }
        } else {
            alert('���ش���!');
        }
    } catch(e) {
        alert('�����쳣!');
    }
}

function show_paihang_action() {
    $.get('<?php echo $_SERVER['PHP_SELF']; ?>', {action:'paihang', ope:'show', t:Math.random()}, function(data) {show_paihang(data);});
}

function show_paihang(data) {
    try {
        var ret = eval('(' + data + ')');
        
        if (ret['code']) {
            if (ret['code'] == 1) {
                alert('û�м�¼!');
            } else if (ret['code'] == 2) {
                //alert(ret['html']);
                $('#content_area').html(ret['html']);
                $('#content_area').show();
            }
        } else {
            alert('���ش���!');
        }
    } catch(e) {
        alert('�����쳣!');
    }
}

<?php if (CHECK_HOSTS && !isset($action)): ?>
location = "../";
<?php endif; ?>
-->
</script>
</head>
</head>
<body>
<div class="box" style="width:100%;">
<?php if (isset($action) && $action == ACTION_PAIHANG): ?>
    <div style="float:left">
        <input type="button" value="�弶����" onclick="trigger_paihang_action(); return false;" />
        <input type="button" value="�鿴����" onclick="show_paihang_action(); return false;" />
    </div>
    <div style="float:right">
    <?php
        $now = mktime();
        if (get_paihang_ope_expiration() > $now):
    ?>
        <input type="button" value="��������" onclick="force_paihang_action(); return false;" />
    <?php endif; ?>
    </div>
<?php endif; ?>
</div>
<div id="content_area" class="box" style="width:100%;display:none;">
</div>
</body>
</html>

<?php
/**
 * check if the paihang had been sorted before
 *
 * @return boolean
 */
function is_sorted_paihang()
{
    $ret = false;

    $check = $GLOBALS['_pm']['mysql']->getOneRecord("SELECT count(*) AS sum FROM player WHERE paihang > 0");
    if (!empty($check) && isset($check['sum']) && $check['sum'] > 0) {
        $ret = true;
    };

    return $ret;
}

/**
 * clean the paihang information of the player
 *
 * @return boolean
 */
function clear_player_paihang()
{
    $ret = true;

    $sql = sprintf("UPDATE player SET paihang=0");
    if (false === $GLOBALS['_pm']['mysql']->query($sql)) {
        $ret = false;
    }

    return $ret;
}

/**
 * sort the paihang of the player
 *
 * @return boolean
 */
function sort_player_paihang()
{
    $ret = true;

    /*if (is_sorted_paihang()) {
        // if sorting had occurred before, return at here
        //echo 'sorting had occurred!<br />';
        return $ret;
    }*/

    $sort_nums = 10;
    $query_nums = 100;
    $toprs = $GLOBALS['_pm']['mysql']->getRecords("SELECT b.uid
								FROM userbb as b,player as u
							   WHERE u.id = b.uid and (u.secid is null or u.secid=0)
							   ORDER BY level DESC,nowexp DESC
							   LIMIT 0, $query_nums
							");
    if (is_array($toprs)) {
        $pid_list = array();
        foreach ($toprs as $v) {
            $pid_list[] = $v['uid'];
        }
        $pid_list = array_unique($pid_list);

        $i = 0;
        $first_paihang = array();
        $second_paihang = array();
        $third_paihang = array();
        $fourth_paihang = array();
        foreach ($pid_list as $pid) {
            $i++;
            if ($i == 1) {
                $first_paihang[] = $pid;
            } elseif (in_array($i, array(2, 3, 4))) {
                $second_paihang[] = $pid;
            } elseif (in_array($i, array(5, 6, 7))) {
                $third_paihang[] = $pid;
            } elseif (in_array($i, array(8, 9, 10))) {
                $fourth_paihang[] = $pid;
            }
        }

        $paihang_level = array(1 => $first_paihang, 
            2 => $second_paihang, 
            3 => $third_paihang, 
            4 => $fourth_paihang);
        foreach ($paihang_level as $k => $v) {
            if (!empty($v)) {
                $sql = sprintf("UPDATE player SET paihang=%d WHERE id IN (%s)", $k, implode(',', $v));
                if (false === $GLOBALS['_pm']['mysql']->query($sql)) {
                    $ret = false;
                }
            }
        }
	}

    return $ret;
}

/**
 * get the player's paihang rank list
 *
 * @return string json
 */
function get_player_paihang_list() 
{
    $ret   = "{code:1}";
    $toprs = $GLOBALS['_pm']['mysql']->getRecords("SELECT name, nickname, paihang FROM player WHERE paihang > 0 ORDER BY paihang
							");
    if (is_array($toprs) && count($toprs) > 0) {
        $html = get_paihang_html_tag($toprs);
        if ($html != "") {
            $ret = "{code:2, html:'{$html}'}";
        }
    }

    return $ret;
}

/**
 * get the expiration of the sorting paihang rank
 *
 * @return integer
 */
function get_paihang_ope_expiration() {
    $ret = strtotime(SORT_PAIHANG_TIME);

    $res = $GLOBALS['_pm']['mysql']->getOneRecord("SELECT endtime FROM timeconfig WHERE titles='paihang'");

    if (is_array($res) && isset($res['endtime'])) {
        if (strtotime($res['endtime']) > 0) {
            $ret = strtotime($res['endtime']);
        }
    } else {
        // insert the paihang config items into table timeconfig
        insert_paihang_config_param(); 
    }

    return $ret;
}

/**
 * insert a record of paihang into the table timeconfig
 *
 * @return boolean
 */
function insert_paihang_config_param() {
    $ret = true;

    // the default time is SORT_PAIHANG_TIME
    $sql = sprintf("INSERT INTO timeconfig (titles, endtime) VALUES ('paihang', '%s')", SORT_PAIHANG_TIME);
    $res = $GLOBALS['_pm']['mysql']->query($sql);
    if ($res === false) {
        $ret = false;
    }


    return $ret;
}

/**
 * get the HTML code of the paihang list
 *
 * @param array $paihang_list
 * @return string
 */
function get_paihang_html_tag($paihang_list)
{
    $ret = "";

    if (count($paihang_list) > 0) {
        $ret = "<table style=\"border-collapse:collapse;border:solid 1px red;\"><thead style=\"text-align:center;\"><td>����</td><td>ͨ��֤</td><td>���</td><td>����</td></thead><tbody>";
        foreach ($paihang_list as $i => $row) {
            $j = $i + 1;
            $ret .= "<tr ><td style=\"border:solid 1px black;\">{$j}</td><td style=\"border:solid 1px black;\">{$row['name']}</td><td style=\"border:solid 1px black;\">{$row['nickname']}</td><td style=\"border:solid 1px black;\">{$row['paihang']}</td></tr>";
        }
        $ret .= "</tbody></table>";
    }

    return $ret;
}
?>
