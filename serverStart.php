<?php
error_reporting(E_ALL);
set_time_limit(300);

ignore_user_abort(true);//用户关闭浏览器不退出


require_once('config/config.game.php');
global $m;
$m = new memory();	// Init memcache.
$db = new mysql();
$time = time() - 24 * 3600;
$alluser = $db -> getRecords("SELECT id FROM player WHERE lastvtime > $time");
foreach($alluser as $av){
	$lastdotime = unserialize($m -> get('last_do_'.$av['id']));
	$lastvisttime = unserialize($m -> get('last_visit_'.$av['id']));
	$m -> set(array('k' => 'last_visit_'.$av['id'],'v' => "$ntime"));
	$m -> set(array('k' => 'last_do_'.$av['id'],'v' => "$ntime"));
	$ntime = time();
	if($lastdotime <= 0 || $lastvisttime <= 0){
		continue;
	}
	$ot = $lastvisttime - $lastdotime;
	$db -> query("UPDATE player_ext SET onlinetime = onlinetime+$ot WHERE uid = {$av['id']}");
}

$_pm['mem']->clearAll();

function common_process($db_name, $is_need_iterative = false, $assign = false, $return_db_values = false )
{
    global $m, $db;
    if(!$db_name)
        return false;

    $m->del('db_'.$db_name);
    if(!$assign){
		if($db_name == 'props'){
			$res = $db->getRecords("select id,name,requires,usages,effect,sell,prestige,buy,yb,sj,stime,endtime,img,vary,varyname,postion,pluseffect,plusflag,pluspid,plusget,plusnum,propscolor,propslock,series,serieseffect,expire,timelimit,merge,vip,honor,contribution,guild_level,zhekouyb from ".$db_name);
		}else{
			$res = $db->getRecords("select * from ".$db_name);
		}
	}
    if($return_db_values)
        return $res;

    if($is_need_iterative){
        foreach($res as $v)
            $arr[$v['id']] = $v;
        if(is_bool($is_need_iterative))
            $res = $arr;
    }else if($assign && is_array($assign))
        $res = $assign;

    $m->set(array('k' => 'db_'.$db_name, 'v' => $res));

    if(!is_bool($is_need_iterative))
        return $arr;
    return $res;
}
/**
* 内存载入
* 
* @param string $key 要载入的表名
*/
function loadmem($key)
{
    global $m, $db;
    switch($key){
        case 'task':
            common_process('task', true);
            break;
        case 'skillsys':
        //case 'skillsysid':
            $arr = common_process('skillsys', 1);
            common_process('skillsysid', false, $arr);
            break;
        case 'bb':
        //case 'bbname':
        //case 'bbid':
            $ret2 = common_process('bb');
            foreach($ret2 as $k => $v)
            {
                $arr[$v['name']] = $v;
                $arrnew[$v['id']] = $v;
            }
            common_process('bbname', false, $arr);
            common_process('bbid', false, $arrnew);
            break;
        case 'gpc':
        //case 'gpcid':
            $ret2 = common_process('gpc', 1);
            common_process('gpcid', false, $ret2);
            break;
        case 'merge':
            common_process('merge');
            break;
        case 'zs':
            common_process('zs');
            break;
        case 'map':
        //case 'mapid':
            $ret2 = common_process('map', 1);
            common_process('mapid', false, $ret2);
            break;
        case 'wx':
            common_process('wx');
            break;
        case 'welcome':
        //case 'welcome1':
            $ret2 = common_process('welcome');
            foreach($ret2 as $k => $v)
                $arrnew[$v['code']] = $v['contents'];
            common_process('welcome1', false, $arrnew);
            break;
        case 'timeconfig':
        //case 'timeconfignew':
            $ret2 = common_process('timeconfig');
            $arrnew = array();
            foreach($ret2 as $v)
                $arrnew[$v['titles']][] = $v;
            common_process('timeconfignew', false, $arrnew);
            break;
        case 'exptolv':
            common_process('exptolv');
            break;
        case 'aoyun':
            $ret2 = common_process('aoyun', false, false, true);
            foreach($ret2 as $k => $v)
                if(is_array($v))
                    $arr[$v['id']] = $v;
            $m->set(array('k' => 'db_aoyun','v' => $arr));
            break;
        case 'blacklist':
            $ret2 = common_process('blacklist', false, false, true);
            foreach($ret2 as $k => $v)
                $newarr[$v['uid']] = ','.$v['list'].",";
            $m->set(array('k' => 'db_blacklist','v' => $newarr));
            break;
        case 'gonggao':
            common_process('gonggao');
            break;
        case 'props':
        //case 'propsid':
        //case 'propsname':
        //case 'equip': 
            $ret2 = common_process('props');
            foreach($ret2 as $pv)
            {
                $arr[$pv['id']] = $pv;
                $arrnew[$pv['name']] = $pv;
            }
            common_process('propsid', false, $arr);
            common_process('propsname', false, $arrnew);
            break; 
    }
}

foreach(array('task', 'skillsys', 'bb', 'gpc', 'merge', 'zs', 'map', 'wx', 'welcome', 'timeconfig', 'exptolv', 'aoyun', 'blacklist', 'gonggao', 'props') as $v)
        loadmem($v); 



/*$table = array("bb",		// Pets data.

			   "gpc",		// Monster data

			   "map",		// Map data

			   "props",		// Props data

			   "skillsys",	// Skill data

			   "wx",		// WU XING data

			   "exptolv",	// Pets level and exp

			   "memorder",   // Table order,

			   "task",
			   
			   "welcome",		// task.
			   
			   "public",
			   
			   "timeconfig",
			
			'aoyun',
			'gonggao'

			   );	



// Clear memory for share data.

foreach ($table as $k => $v)

{

	$m->del(PFX . $v);

	$ret = $db->getRecords("SELECT * 

	                          FROM {$v}

						     ORDER BY id

						  ");

	$key = PFX . $v;

	$m->add( array('k'=>$key, 'v'=>$ret) );

}



echo "--------------------------------------------------<br/>";

echo "游戏共享数据映射完成，映射时间：" . date("Y-m-d H:i:s",time()) . "<br/>";

echo "--------------------------------------------------<br/><br/>";



print 'The Server info:<br/>

	  ---------------------------------------------------<br/>';

foreach ($m->getStats() as $k => $v)

{

	echo $k . " : \t\t"  . $v . "\n<br/>";

}
require_once('vm1.php');
    foreach(array('task', 'skillsys', 'bb', 'gpc', 'merge', 'zs', 'map', 'wx', 'welcome', 'timeconfig', 'exptolv', 'aoyun', 'blacklist', 'gonggao', 'props') as $v)
        loadmem($v); */
/*
$m->del('db_blacklist');//黑名单
$ret2 = $db->getRecords("select * from blacklist");
foreach($ret2 as $k => $v)
{
	$newarr[$v['uid']] = ','.$v['list'].",";
}
$m->set(array('k'=>'db_blacklist','v'=>$newarr));
unset($ret2,$k,$v,$newarr);

$m->del('db_task');
$ret = $db->getRecords("select * from task");
foreach($ret as $v)
{
	$arr[$v['id']] = $v;
}
$m->set(array('k'=>'db_task','v'=>$arr));
unset($ret,$v,$arr);

$ret = $db->getRecords("select * from timeconfig");
$m->del('db_timeconfignew');
foreach($ret as $v)
{
	$arr[$v['titles']][] = $v;
}
$m->set(array('k'=>'db_timeconfignew','v'=>$arr));
unset($ret,$v,$arr);

$ret = $db->getRecords("select * from aoyun");
$m->del('db_aoyun');
foreach($ret as $v)
{
	$arr[$v['id']] = $v;
}
$m->set(array('k'=>'db_aoyun','v'=>$arr));
unset($ret,$v,$arr);


$ret = $db->getRecords("select * from welcome");
$m->set(array('k'=>'db_welcome','v'=>$ret));
foreach($ret as $kw => $vw)
{
	$newarr[$vw['code']] = $vw['contents'];
}
$m->del('db_welcome1');
$m->set(array('k'=>'db_welcome1','v'=>$newarr));
unset($ret,$v,$newarr);

$ret = $db->getRecords("select * from bb");
$m->del('db_bbname');
$m->del('db_bbid');
foreach($ret as $v)
{
	$arr[$v['name']] = $v;
	$arrnew[$v['id']] = $v;
}
$m->set(array('k'=>'db_bbname','v'=>$arr));
$m->set(array('k'=>'db_bbid','v'=>$arrnew));
unset($ret,$v,$arr,$arrnew);

$ret = $db->getRecords("select * from skillsys");
$m->del('db_skillsysid');
foreach($ret as $v)
{
	$arr[$v['id']] = $v;
}
$m->set(array('k'=>'db_skillsysid','v'=>$arr));
unset($ret,$v,$arr);

$ret = $db->getRecords("select * from map");
$m->del('db_mapid');
foreach($ret as $v)
{
	$arr[$v['id']] = $v;
}
$m->set(array('k'=>'db_mapid','v'=>$arr));
unset($ret,$v,$arr);

$ret = $db->getRecords("select * from gpc");
$m->del('db_gpcid');
foreach($ret as $v)
{
	$arr[$v['id']] = $v;
}
$m->set(array('k'=>'db_gpcid','v'=>$arr));
unset($ret,$v,$arr);

$ret = $db->getRecords("select * from props");
$m->del('db_propsid');
foreach($ret as $v)
{
	$arr[$v['id']] = $v;
	$arrnew[$v['name']] = $v;
}
$m->set(array('k'=>'db_propsid','v'=>$arr));
$m->del('db_propsname');
$m->set(array('k'=>'db_propsname','v'=>$arrnew));
unset($v,$arr);
$equip = new equipment();
$m->del('db_equip');
foreach($ret as $v)
{
	if($v['buy'] > 0 || $v['yb'] > 0 || $v['prestige'] > 0)
	{
		$arr[$v['id']] = $equip -> div($v['id'],0,0,1);
	}
}
$m->set(array('k'=>'db_equip','v'=>$arr));
unset($ret,$v,$arr);
*/
$m->memClose();
?>

OK