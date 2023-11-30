<?
/*
**登陆接口说明：整合之前各种登陆接口，作为统一的登陆验证。根据不同的请求参数，做出不同方法的验证，并返回统一的状态true or false，以及返回要写入session的用户名、用户id、用户验证的URL.其他文件使用时只需包含该文件，并判断$LoginApiState的状态，若为true则表示成功，false表示失败.成功后则取用户名、用户id、用户验证的URL的值。
**使用方法：把以前各个接口验证过程更换为require('./LoginApi.php');获取下面几个变量的值，以下的值按需要获取。
**author:Huizheng Yu
**Date:2008-04-15
**Ver:1.0
*/
//先定义一个变量$LoginApiState作为返回状态，默认为false；再定义要写入session的变量，用户名、用户id、用户验证的URL
$LoginApiState=false;//登陆状态
//---------------------------------------------------------------------------------------
//****一下的3个变量，按需索取写入session****
$LoginApiUserName='';//用户名，主要取决于其他文件是否需要写入session
$LoginApiUserId='';//用户id，主要取决于其他文件是否需要写入session
$LoginApiQueryString='';//需要再次验证的URL后面的参数字符串，主要取决于其他文件是否需要写入session
$LoginApiLicenseId='';//webgame特有的一个参数，其他文件用时按需要取
//---------------------------------------------------------------------------------------
//定义双方约定的Key和要再次验证的域名的数组集合。{**若后期需要增加合作方的Key和二次验证的域名，需在此修改;不需要二次验证的域名为空**}。
//No：编号
//Key：双方约定的Key
//Domain:需要2次验证的URL，为空则不需要2次验证
//Remark:备注
//--------
//特别说明：如果是很特殊的接口方式，则不需要在此配置，例如51的接口、7K7K的接口、webgame的接口
//--------
extract($_REQUEST);//把提交过来的参数转换为变量形式

require_once('../config/config.game.php');
//---------------------------------------------------------------------------------
//以下为webgame的接口
//---------------------------------------------------------------------------------
//echo $_POST["data"];exit;
/*if(isset($_GET['name']))
{
	$LoginApiState=true;
	$LoginApiUserName=$_GET['name'];
}*/
/*if($data!='')
{
	require_once(dirname(__FILE__).'/lib/passport.php');
	$key="7sl+kb9adDAc7gLuv31MeEFPBMJZdRZyAx9eEmXSTui4423hgGfXF1pyM";
	$status=-1;
	$fcontent=passport_decrypt($data, $key);
	$carray=explode(",",$fcontent);
	$p_name=htmlspecialchars(trim($carray[0]));
	$licenseId=htmlspecialchars(trim($carray[1]));
	$gdate=time()-$carray[2];
	if($gdate>1800)
	{
		$LoginApiState=false;
	}
	else
	{
		if( isset($carray[4]) )
		{
			$_SESSION['lys_id'] = htmlspecialchars(trim($carray[4]));	//混服模式 将带有联营商id
			$_SESSION['LoginApiState'] = 1;	//已登陆
			$_SESSION['username'] = $p_name;
			$_SESSION['licenseid'] = $licenseId;
			require_once(dirname(__FILE__).'/lib/nusoap.php');
			//die($_SESSION['username']);
		}
		else
		{
			require_once(dirname(__FILE__).'/lib/nusoap.php');
			$status=chekUserlogin($p_name,$licenseId);
			$objxml = new xml($status);
			$objxml->find_patter('Response', $objxml->dom);
		}
		if ($objxml->result == 10 || isset($lys_id) )
		{
			$LoginApiState=true;
			$LoginApiUserName=$p_name;
			$LoginApiLicenseId=$licenseId;
		}
		else if($objxml->result == 1104)
		{
			die('<script language="javascript">window.location="http://passport.webgame.com.cn/logout.do?forward='.$urlJump.'";</script>');
		}
		else
		{
			$LoginApiState=false;
		}
	}
}
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
                '@parent' => &$this->pointer);
               $this->pointer =& $this->pointer[$tag][$idx];
        } else {
            $this->pointer[$tag] = Array(
                '@parent' => &$this->pointer);
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
function curl2($url,$port=80){
	$post = 1;
	$returntransfer = 1;
	$header = 0;
	$nobody = 0;
	$followlocation = 1;
	
	$ch = curl_init();
	$options = array(CURLOPT_URL => $url,
						CURLOPT_HEADER => $header,
						CURLOPT_NOBODY => $nobody,
						CURLOPT_PORT => $port,
						CURLOPT_POST => 0,
						CURLOPT_POSTFIELDS => $request,
						CURLOPT_RETURNTRANSFER => $returntransfer,
						CURLOPT_FOLLOWLOCATION => $followlocation,
						CURLOPT_COOKIEJAR => $cookie_jar,
						CURLOPT_COOKIEFILE => $cookie_jar,
						CURLOPT_REFERER => $url
						);
	curl_setopt_array($ch, $options);
	$result = curl_exec($ch);
	curl_close($ch);
	return $result;
}
function msg($msg,$page_to='javascript:history.go(-1)')
{ 
	$newmsg = iconv('utf-8','gbk',$msg);
	if($newmsg == ''){
		$newmsg = $msg;
	}
	echo '<meta http-equiv="content-type" content="text/html;charset=gb2312" />';
	$str = "<script LANGUAGE='javascript'>alert('$newmsg');</script>";
	if ($page_to) $str .= "<meta http-equiv='refresh' content='0;url=$page_to'>";
	echo $str;
	exit;
}*/
?>