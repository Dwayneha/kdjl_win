<?php
$mem = NULL;
function getMemcacheSetting()
{
	$data = file_get_contents(dirname(dirname(__FILE__)).'/config/config.game.php');
	preg_match("/_mem\[['\"]host['\"]\][^'\"]+['\"]([^\"']+)['\"]/",$data,$out);
	preg_match("/_mem\[['\"]port['\"]\][^'\"]+['\"]([^\"']+)['\"]/",$data,$out1);
	return array('host'=>$out[1],'port'=>$out1[1]);
}

function memConnect($_mem)
{
	global $mem;
	$mem = new Memcache;
	if ($mem->connect($_mem['host'], $_mem['port']) === FALSE)
	{
		$mem = false;
		wr("Memcache connet failed server:".print_r($_mem,1),1);
	}
}

function memGet($key)
{
	global $mem;
	if(!$ver=$mem->getVersion())
	{
		memConnect(getMemcacheSetting());
	}
	//wr('ver = '.$ver);
	if($mem === false) return NULL;
	$val = $mem->get($key);
	if(!$val)
	{
		//wr($mem->get('db_map'));
	}
	if(is_string($val))
		return unserialize($val);
	else
		return $val;
}
memConnect(getMemcacheSetting());
$domainPrefix = 'pokeelf';
$map  = memGet("pokeelf_online_user_list");
print_r($map);
echo count($map)>1&&is_array($map)?"OK<font color='#009900'>正常</font>-".date("Y-m-d H:i:s"):'Memcache error(内存服务器错误)';
?>