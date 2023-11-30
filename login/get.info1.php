<?php
session_start();
require_once"../config/config.game.php";
/*===================================================== 
 Copyright (C) Stone、hykwolf、失落的城市<stone@xjwa.net>
 Modify : Stone
 URL : http://www.xjwa.net
===================================================== */
###### Fix PHPSESSID ###########

######平台加密，解密接口函数包
  require_once("lib/passport.php");

######平台接口通用接口函数包
  require_once("lib/nusoap.php");
 
########平台的游戏约定的密钥串
  $key="7sl+kb9adDAc7gLuv31MeEFPBMJZdRZyAx9eEmXSTui4423hgGfXF1pyM"; 

########平台二次验证结果字串######
###### -1:非法访问 -2:通行证被冻结#### 
###### -999:没有通行证 10:验证通过####
  $status=-1;

#########通过GET方式获取字符串######
   if(!isset($_GET["data"])){

	$_GET["data"]="";
   }


//########################################

####解开平台加密码提交过来的用户数据####
   $fcontent=passport_decrypt($_GET["data"], $key); 

########将数据存入数组######
   $carray=explode(",",$fcontent);

########获取通行证用户名######
   $p_name=htmlspecialchars(trim($carray[0]));

########获取通行证licenseId######
   $licenseId=htmlspecialchars(trim($carray[1]));

########获取通行证登录时间,超过30分钟失效，需要重新登录######
  $gdate=time()-$carray[2];
   if($gdate>1800){

       ####登录超时处理流程
   }
	echo '<b>'.__FILE__.'-->'.__LINE__.'</b><br/><pre>=';
	var_dump($p_name,$licenseId	);
	echo '</pre>';

########平台XML二次验证#########
   $status=chekUserlogin($p_name,$licenseId);
	echo '<b>'.__FILE__.'-->'.__LINE__.'</b><br/><pre>=';
	var_dump( $status	);
	echo '</pre>';

 // echo $status;
$objxml = new xml($status);
	echo '<b>'.__FILE__.'-->'.__LINE__.'</b><br/><pre>=';
	var_dump($objxml	);
	echo '</pre>';

$objxml->find_patter('Response', $objxml->dom);
if ($objxml->result == 10) // OK
{
	####正常登录处理流程
	$_SESSION['username'] = $p_name;
	$_SESSION['licenseid']= $licenseId;

	/*$testarr = array('Boss','stone','nic2002','pmtest3','tanwei2008','mayier318','baodou1222','wenfang','kefu04','sam_zeus','leinchu','leinchu2','leinchu3','piwai123','dm511024');

	if( !in_array($p_name,$testarr) ) 
	{
		echo 'alert("游戏维护中！!");';exit();
	}*/
	
	
	//######################################限制同一IP的登陆#######################################################
	/*$ip = $_SERVER['REMOTE_ADDR'];
	$time1 = time() + 3600 * 10;
	$sql = "SELECT uname as nums FROM logins WHERE uIP = '{$ip}' and times < {$time1} order by times";
	$numarr = $_pm['mysql'] -> getRecords($sql);
	$namearr = array();
	if(is_array($numarr))
	{
		foreach($numarr as $v)
		{
			if(!in_array($v['nums'],$namearr))
			{
				$namearr[] = $v['nums'];
			}
		}
	}
	if(is_array($namearr))
	{
		if(count($namearr) >= 5 && !in_array($p_name,$namearr))
		{
			echo 'alert("同一IP10小时内最多只能登陆5个帐号！!");';exit();
		}
		
	}*/
	//######################################在这里结束#######################################################
	
	require_once("../config/config.game.php");
	$min = 5*60; // 5分钟内的玩家数量
	$maxp= 500; // 最大支持600人。
	$onl = $_pm['mysql']->getOneRecord("
						select 
							count(id) olu
					 	from 
							player 
						where lastvtime>unix_timestamp()-{$min}
					 ");
	$onl = $onl['olu'];
	
	if($onl>=$maxp && $p_name!='Boss')
	{
		echo "alert('人数已满，请稍后登录或进入其它口袋精灵二 区服!');";
		$ip = $_SERVER['REMOTE_ADDR'];
			$date = date("Y-m-d");
			$file = getcwd()."/text/".$date.".txt";
			//chmod($file, 0755);
			$fp = fopen($file,"a");
			$dates = date("H:i:s");
			fwrite($fp,"---------------------通行证：".$p_name."，IP:".$ip.",时间：".$dates."\r\n\r\n");
			exit();
		exit();
	}

	$rs = $_pm['mysql']->getOneRecord("SELECT name FROM player WHERE name='{$_SESSION['username']}' limit 0,1");

	if (is_array($rs)) // user had exists,goto game index.
	{
		require_once("D:/phpstudy_pro/WWW/kd.cn/function/loginGate.php");
		echo 'document.getElementById("hh").innerHTML="";top.location="../index.html";';
		
		###########################登陆统计 10.27号谭炜##########################################
		$uIP = $_SERVER['REMOTE_ADDR'];
		$time = time();
		$sql = "INSERT INTO logins (uname,uIP,times) VALUES ('{$_SESSION['username']}','{$uIP}',{$time})";
		$_pm['mysql'] -> query($sql);
		###################################登陆统计在这里显示######################################
	}
	else echo 'document.location.href="reg1.html";';
}else
{
    // error
}

//=================================================================
class xml  {
    private $parser;
    private $pointer;
    public $dom;
	public $result='';
    
    public function __construct($data) {
        $this->pointer =& $this->dom;
        $this->parser = xml_parser_create();
        xml_set_object($this->parser, $this);
        xml_parser_set_option($this->parser, XML_OPTION_CASE_FOLDING, false);
        xml_set_element_handler($this->parser, "tag_open", "tag_close");
        xml_set_character_data_handler($this->parser, "cdata");
        xml_parse($this->parser, $data);
    }
   
    private function tag_open($parser, $tag, $attributes) {
        if (isset($this->pointer[$tag]['@attributes'])) {
            $content = $this->pointer[$tag];
            $this->pointer[$tag] = array(0 => $content);
            $idx = 1;
        } else if (isset($this->pointer[$tag]))
            $idx = count($this->pointer[$tag]);

        if (isset($idx)) {
            $this->pointer[$tag][$idx] = Array(
                '@idx' => $idx,
                '@parent' => $this->pointer);
               $this->pointer =& $this->pointer[$tag][$idx];
        } else {
            $this->pointer[$tag] = Array(
                '@parent' => $this->pointer);
            $this->pointer =& $this->pointer[$tag];
        }
        if (!empty($attributes))
            $this->pointer['@attributes'] = $attributes;
    }

    private function cdata($parser, $cdata) {
           $this->pointer['@data'] = $cdata;
    }

    private function tag_close($parser, $tag) {
        $current = & $this->pointer;
        if (isset($this->pointer['@idx']))
            unset($current['@idx']);
        
        $this->pointer = & $this->pointer['@parent'];
        
          unset($current['@parent']);
           if (isset($current['@data']) && count($current) == 1)
               $current = $current['@data'];
           else if (empty($current['@data'])||$current['@data']==0)
               unset($current['@data']);
    }

	public function find_patter($key, $finddes)
	{
		if (!is_array($finddes)) return;

		foreach ($finddes as $k=>$v)
		{
			if (is_array($v)) $this->find_patter($key, $v);
			else if ($key == $k) 
			{
				$this->result=$v;
				return true;
			}
		}

	}
}
?>