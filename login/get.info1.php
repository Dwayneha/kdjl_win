<?php
session_start();
require_once"../config/config.game.php";
/*===================================================== 
 Copyright (C) Stone��hykwolf��ʧ��ĳ���<stone@xjwa.net>
 Modify : Stone
 URL : http://www.xjwa.net
===================================================== */
###### Fix PHPSESSID ###########

######ƽ̨���ܣ����ܽӿں�����
  require_once("lib/passport.php");

######ƽ̨�ӿ�ͨ�ýӿں�����
  require_once("lib/nusoap.php");
 
########ƽ̨����ϷԼ������Կ��
  $key="7sl+kb9adDAc7gLuv31MeEFPBMJZdRZyAx9eEmXSTui4423hgGfXF1pyM"; 

########ƽ̨������֤����ִ�######
###### -1:�Ƿ����� -2:ͨ��֤������#### 
###### -999:û��ͨ��֤ 10:��֤ͨ��####
  $status=-1;

#########ͨ��GET��ʽ��ȡ�ַ���######
   if(!isset($_GET["data"])){

	$_GET["data"]="";
   }


//########################################

####�⿪ƽ̨�������ύ�������û�����####
   $fcontent=passport_decrypt($_GET["data"], $key); 

########�����ݴ�������######
   $carray=explode(",",$fcontent);

########��ȡͨ��֤�û���######
   $p_name=htmlspecialchars(trim($carray[0]));

########��ȡͨ��֤licenseId######
   $licenseId=htmlspecialchars(trim($carray[1]));

########��ȡͨ��֤��¼ʱ��,����30����ʧЧ����Ҫ���µ�¼######
  $gdate=time()-$carray[2];
   if($gdate>1800){

       ####��¼��ʱ��������
   }
	echo '<b>'.__FILE__.'-->'.__LINE__.'</b><br/><pre>=';
	var_dump($p_name,$licenseId	);
	echo '</pre>';

########ƽ̨XML������֤#########
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
	####������¼��������
	$_SESSION['username'] = $p_name;
	$_SESSION['licenseid']= $licenseId;

	/*$testarr = array('Boss','stone','nic2002','pmtest3','tanwei2008','mayier318','baodou1222','wenfang','kefu04','sam_zeus','leinchu','leinchu2','leinchu3','piwai123','dm511024');

	if( !in_array($p_name,$testarr) ) 
	{
		echo 'alert("��Ϸά���У�!");';exit();
	}*/
	
	
	//######################################����ͬһIP�ĵ�½#######################################################
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
			echo 'alert("ͬһIP10Сʱ�����ֻ�ܵ�½5���ʺţ�!");';exit();
		}
		
	}*/
	//######################################���������#######################################################
	
	require_once("../config/config.game.php");
	$min = 5*60; // 5�����ڵ��������
	$maxp= 500; // ���֧��600�ˡ�
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
		echo "alert('�������������Ժ��¼����������ڴ������ ����!');";
		$ip = $_SERVER['REMOTE_ADDR'];
			$date = date("Y-m-d");
			$file = getcwd()."/text/".$date.".txt";
			//chmod($file, 0755);
			$fp = fopen($file,"a");
			$dates = date("H:i:s");
			fwrite($fp,"---------------------ͨ��֤��".$p_name."��IP:".$ip.",ʱ�䣺".$dates."\r\n\r\n");
			exit();
		exit();
	}

	$rs = $_pm['mysql']->getOneRecord("SELECT name FROM player WHERE name='{$_SESSION['username']}' limit 0,1");

	if (is_array($rs)) // user had exists,goto game index.
	{
		require_once("D:/phpstudy_pro/WWW/kd.cn/function/loginGate.php");
		echo 'document.getElementById("hh").innerHTML="";top.location="../index.html";';
		
		###########################��½ͳ�� 10.27��̷�##########################################
		$uIP = $_SERVER['REMOTE_ADDR'];
		$time = time();
		$sql = "INSERT INTO logins (uname,uIP,times) VALUES ('{$_SESSION['username']}','{$uIP}',{$time})";
		$_pm['mysql'] -> query($sql);
		###################################��½ͳ����������ʾ######################################
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