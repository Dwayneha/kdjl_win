<?php
error_reporting(0);
set_time_limit(300);

ignore_user_abort(true);//�û��ر���������˳�


require_once('./config/config.game.php');
global $m;
$m = new memory();	// Init memcache.
$db = new mysql();

/**
* ͨ�ô�����
* 
* @param string $db_name ����, bool/int $is_need_iterative �Ƿ���Ҫforeach�������1������set���󷵻�foreach��ֵ, bool/string $assign �Ƿ�ֱ�Ӹ�ֵ, bool $return_db_values �Ƿ�ֱ�ӷ������ݿ��ѯ��ֵ
*/
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
	//print_r(unserialize($m->get('db_'.$db_name)));
	//echo __LINE__."<br>";
    if(!is_bool($is_need_iterative))
        return $arr;
    return $res;
}
/**
* �ڴ�����
* 
* @param string $key Ҫ����ı���
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
            if(is_array($ret2)){
                foreach($ret2 as $k => $v)
                    $newarr[$v['uid']] = ','.$v['list'].",";
                $m->set(array('k' => 'db_blacklist','v' => $newarr));
            }

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
		case 'T_ly_URL_config':
		{
			common_process('T_ly_URL_config');
			break;
		}
    }
}

//��memcache�е�ĳ��һ�����������������ֶ�ά����ת�����ַ�����������
function memArr2Str1($data,$key,$spFiled="`_`",$spLine="$+$",$suffix='str')
{
	global $_pm;
	//$data=$_pm['mem']->get($key);
	//if(!is_array($data)&&strlen($data)>3) $data=unserialize($data);
	$str='';
	$con='';
	
	if(count($data)>0){
		foreach($data as $v)
		{
			if(count($v)>0)
			{
				$str.=$con.implode($spFiled,$v);
				$con=$spLine;
			}
		}
	}
	$key.=$suffix;
	$_pm['mem']->setnsnc($key,$str);
	
}

function guild_update_mem(){
	global $_pm;
	$guild = $_pm['mysql'] -> getRecords("SELECT member_id,guild_id FROM guild_members");
	$arr = array();
	if (!is_array($guild)) {
		$_pm['mem'] -> setns('MEM_GUILD_LIST',$arr);
		memArr2Str($arr,'MEM_GUILD_LIST');
		return false;
	}
	foreach($guild as $v){
		$arr[$v['guild_id']][] = $v['member_id'];
	}
	$_pm['mem'] -> setns('MEM_GUILD_LIST',$arr);
	memArr2Str1($arr,'MEM_GUILD_LIST');
}
	
if(count($_GET) == 1 && $_GET['db'] == "")
{
    ///*
    foreach(array('task', 'skillsys', 'bb', 'gpc', 'merge', 'zs', 'map', 'wx', 'welcome', 'timeconfig', 'exptolv', 'aoyun', 'blacklist', 'gonggao', 'props','T_ly_URL_config') as $v)
	loadmem($v);  
	guild_update_mem();
	
	
	
	
	echo 'done';
}
else
{
	
	foreach($_GET as $k => $v)
	{
		$ret = $ret1 = $ret2 = $arr = $newarr = NULL;
		$m->del($v);
		if($v == "db_task")
		{
/*			$ret = $db->getRecords("select * from task");
			foreach($ret as $va)
			{
				$arr[$va['id']] = $va;
			}
			$m->set(array('k' => 'db_task', 'v' => $arr));*/
            loadmem('task');
			echo $v."<br />";
		}
		else if($v == "db_bb")
		{
/*			$ret = $db -> getRecords('SELECT * FROM bb');
			$m -> set(array('k'=>'db_bb','v'=>$ret));
			foreach($ret as $bk => $bv)
			{
				$arr[$bv['name']] = $bv;
				$arrnew[$bv['id']] = $bv;
			}
			$m->del('db_bbname');
			$m -> set(array('k'=>'db_bbname','v'=>$arr));
			$m->del('db_bbid');
			$m -> set(array('k'=>'db_bbid','v'=>$arrnew));
			unset($bk,$bv,$arr,$arrnew);*/
            loadmem('bb');
			echo $v."<br />";
		}
		else if($v == "db_skillsys")
		{
/*			$ret = $db -> getRecords('SELECT * FROM skillsys');
			$m -> set(array('k'=>'db_skillsys','v'=>$ret));
			foreach($ret as $bk => $bv)
			{
				$arr[$bv['id']] = $bv;
			}
			$m->del('db_skillsysid');
			$m -> set(array('k'=>'db_skillsysid','v'=>$arr));
			unset($bk,$bv,$arr);*/
            loadmem('skillsys');
			echo $v."<br />";
		}
		else if($v == "db_gpc")
		{
/*			$ret = $db -> getRecords('SELECT * FROM gpc');
			$m -> set(array('k'=>'db_gpc','v'=>$ret));
			foreach($ret as $bk => $bv)
			{
				$arr[$bv['id']] = $bv;
			}
			$m->del('db_gpcid');
			$m -> set(array('k'=>'db_gpcid','v'=>$arr));
			unset($bk,$bv,$arr);*/
            loadmem('gpc');         
			echo $v."<br />";
		}
		else if($v == "db_map")
		{
/*			$ret = $db -> getRecords('SELECT * FROM map');
			$m -> set(array('k'=>'db_map','v'=>$ret));
			foreach($ret as $bk => $bv)
			{
				$arr[$bv['id']] = $bv;
			}
			$m->del('db_mapid');
			$m -> set(array('k'=>'db_mapid','v'=>$arr));
			echo $v;
			unset($bk,$bv,$arr);*/
            loadmem('map');
			echo $v."<br />";
		}
		else if($v == "db_props")
		{
/*			$ret = $db->getRecords("select * from props");
			$m->set(array('k'=>'db_props','v'=>$ret));
			foreach($ret as $pv)
			{
				$arr[$pv['id']] = $pv;
				$arrnew[$pv['name']] = $pv;
			}
			$m->del('db_propsid');
			$m->set(array('k'=>'db_propsid','v'=>$arr));
			$m->del('db_propsname');
			$m->set(array('k'=>'db_propsname','v'=>$arrnew));
			unset($pv,$arr);
			$equip = new equipment();
			$m->del('db_equip');
			foreach($ret as $vs)
			{
				if($vs['buy'] > 0 || $vs['yb'] > 0 || $vs['prestige'] > 0)
				{
					$arr[$vs['id']] = $equip -> div($vs['id'],0,0,1);
				}
			}
			$m->set(array('k'=>'db_equip','v'=>$arr));*/
            loadmem('props');
			echo $v."<br />";
		}
		else if($v == "db_timeconfig")
		{
/*			$ret = $db->getRecords("select * from timeconfig");
			$m->set(array('k'=>'db_timeconfig','v'=>$ret));
			$m->del('db_timeconfignew');
			foreach($ret as $vs)
			{
				$arr[$vs['titles']][] = $vs;
			}
			$m->set(array('k'=>'db_timeconfignew','v'=>$arr));*/
            loadmem('timeconfig');
			echo $v."<br />";
		}
		else if($v == "db_T_ly_URL_config")
		{
			loadmem('T_ly_URL_config');
			echo $v."<br />";
		}
		else if($v == "db_aoyun")
		{
/*			$ret = $db->getRecords("select * from aoyun");
			foreach($ret as $vs)
			{
				$arr[$vs['id']] = $vs;
			}
			$m->set(array('k'=>'db_aoyun','v'=>$arr));*/
            loadmem('aoyun');
			echo $v."<br />";
		}
		else if($v == "db_welcome")
		{
/*			$ret = $db->getRecords("select * from welcome");
			$m->set(array('k'=>'db_welcome','v'=>$ret));
			foreach($ret as $kw => $vw)
			{
				$newarr[$vw['code']] = $vw['contents'];
			}
			$m->del('db_welcome1');
			$m->set(array('k'=>'db_welcome1','v'=>$newarr));*/
            loadmem('welcome');
			echo $v."<br />";
		}
		else if($k != "Submit" && $v != "checkbox")
		{
			$table = explode("_",$v);
			if($table[1] != 'props'){
				$ret2 = $db -> getRecords("select id,name,requires,usages,effect,sell,prestige,buy,yb,sj,stime,endtime,img,vary,varyname,postion,pluseffect,plusflag,pluspid,plusget,plusnum,propscolor,propslock,series,serieseffect,expire,timelimit,merge,vip,honor,contribution,guild_level,zhekouyb from ".$table[1]);
			}else{
				$ret2 = $db->getRecords("select * from {$table[1]}");
			}
			$m->set(array('k'=>$v,'v'=>$ret2));
			echo $v."<br />";
		}
	}
}
//foreach(array('task', 'skillsys', 'bb', 'gpc', 'merge', 'zs', 'map', 'wx', 'welcome', 'timeconfig', 'exptolv', 'aoyun', 'blacklist', 'gonggao', 'props', 'skillsysid', 'bbname', 'bbid', 'gpcid', 'mapid', 'welcome1', 'timeconfignew', 'propsid', 'propsname', 'equip') as $v) if(!$m->get('db_'.$v)) $a[]=$v;
//var_dump(unserialize( $m->get('db_skillsys')));
$sql = 'SELECT value2,contents FROM welcome WHERE code = "timelimitbuy"';
$tm = $_pm["mysql"] -> getOneRecord($sql);
if(is_array($tm)){
	$time = date('Y-m-d H:i:s');
	$tarr = explode('|',$tm['value2']);
	if($time > $tarr[0] && $time < $tarr[1]){
		$p = explode(',',$tm['contents']);//20100915120000
		$v = '';
		foreach($p as $v){
			$va = explode(':',$v);
			$s = 0;
			$sql = 'SELECT id FROM props WHERE zhekouyb > 0 AND id = '.$va[0];
			$res = $_pm['mysql'] -> getOneRecord($sql);
			$sql = 'SELECT sum(nums) as nums FROM yblog WHERE title ="'.$va[0].'" AND DATE_FORMAT(from_unixtime(buytime),"%Y-%m-%d %H:%i:%s") > "'.$tarr[0].'" AND DATE_FORMAT(from_unixtime(buytime),"%Y-%m-%d %H:%i:%s") < "'.$tarr[1].'"';
			//echo $sql;
			$ybarr = $_pm['mysql'] -> getOneRecord($sql);
			
			if(is_array($ybarr)){
				$s = $ybarr['nums'];
			}
			$m -> set(array('k' =>'zhekou_'.$res['id'].'_num', 'v' => $s));
		}
	}
}
$m->memClose();
if(isset($_GET['auto']))
{
	echo  
	'
		<script language="javascript">
			alert("��������ʼ���ɹ�!\n��ر���������µ�½!");
			setTimeout("window.top.goToIndex();",500);
		</script>
	';
	die();
}
if(stripos($_SERVER['PHP_SELF'], 'vm') !== false)
{
	
?>

<style type="text/css">
<!--
body,td,th {
	font-size: 12px;
}
body {
	margin-left: 0px;
	margin-top: 0px;
	margin-right: 0px;
	margin-bottom: 0px;
}
.STYLE1 {color: #FF0000}
-->
</style>
<center>
<form id="form1" name="form1" method="get" action="">
<table width="778" border="0" cellspacing="0" cellpadding="0">
  <tr>
    <td width="72" height="25" align="right">�������ݣ�</td>
    <td width="706" height="25"><p>
      <input name="db_task" type="checkbox" id="db_task" value="db_task" />
      ����
      <input type="checkbox" name="skillsys" value="skillsys" />
      
      ����
      <input name="db_bb" type="checkbox" id="db_bb" value="db_bb" />
      ����
      <input name="db_gpc" type="checkbox" id="db_gpc" value="db_gpc" />
      ����
      <input name="db_merge" type="checkbox" id="db_merge" value="db_merge" />
      �ϳ�
      <input name="db_zs" type="checkbox" id="db_zs" value="db_zs" />
      ת��
      <input name="db_map" type="checkbox" id="db_map" value="db_map" />
      ��ͼ
      <input name="db_wx" type="checkbox" id="db_wx" value="db_wx" />
      ����
      <input name="db_welcome" type="checkbox" id="db_welcome" value="db_welcome" />
      �����
      <input name="db_timeconfig" type="checkbox" id="db_timeconfig" value="db_timeconfig" />
      ʱ������
      <input name="db_exptolv" type="checkbox" id="db_exptolv" value="db_exptolv" />
      ��������
      <input name="db_aoyun" type="checkbox" id="db_aoyun" value="db_aoyun" />
      ����
      <input name="db_blacklist" type="checkbox" id="db_blacklist" value="db_blacklist" />
      ������
      <input name="db_gonggao" type="checkbox" id="db_gonggao" value="db_gonggao" />
      ����
      <input name="db_props" type="checkbox" id="db_props" value="db_props" />
      ����
	  <input name="db_T_ly_URL_config" type="checkbox" id="db_T_ly_URL_config" value="db_T_ly_URL_config" />
      ������URL����<span class="STYLE1">(Ĭ���Ǹ�������)</span>
      <input type="submit" name="Submit" value="�ύ" />
    </p>
      </td>
  </tr>
</table>
</form>
</center>
<?
}?>

