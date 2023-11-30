<?php
$mem = NULL;
function getMemcacheSetting()
{
	return array('host'=>'127.0.0.1','port'=>'11211');
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
	{
		return unserialize($val);
	}
	else
	{
		$val[0] = '1328527870';
		$mem->set($key,$val);
		return $val;
	}
}
$_mem=getMemcacheSetting();
echo 'ַ'.$_mem['host'].':'.$_mem['port'].'<br/>';
memConnect(getMemcacheSetting());
$key = '84zhaomu_refresh_time_____';
$val  = memGet($key);
print_r($val);
?>
