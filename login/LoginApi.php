<?
/*
**��½�ӿ�˵��������֮ǰ���ֵ�½�ӿڣ���Ϊͳһ�ĵ�½��֤�����ݲ�ͬ�����������������ͬ��������֤��������ͳһ��״̬true or false���Լ�����Ҫд��session���û������û�id���û���֤��URL.�����ļ�ʹ��ʱֻ��������ļ������ж�$LoginApiState��״̬����Ϊtrue���ʾ�ɹ���false��ʾʧ��.�ɹ�����ȡ�û������û�id���û���֤��URL��ֵ��
**ʹ�÷���������ǰ�����ӿ���֤���̸���Ϊrequire('./LoginApi.php');��ȡ���漸��������ֵ�����µ�ֵ����Ҫ��ȡ��
**author:Huizheng Yu
**Date:2008-04-15
**Ver:1.0
*/
//�ȶ���һ������$LoginApiState��Ϊ����״̬��Ĭ��Ϊfalse���ٶ���Ҫд��session�ı������û������û�id���û���֤��URL
$LoginApiState=false;//��½״̬
//---------------------------------------------------------------------------------------
//****һ�µ�3��������������ȡд��session****
$LoginApiUserName='';//�û�������Ҫȡ���������ļ��Ƿ���Ҫд��session
$LoginApiUserId='';//�û�id����Ҫȡ���������ļ��Ƿ���Ҫд��session
$LoginApiQueryString='';//��Ҫ�ٴ���֤��URL����Ĳ����ַ�������Ҫȡ���������ļ��Ƿ���Ҫд��session
$LoginApiLicenseId='';//webgame���е�һ�������������ļ���ʱ����Ҫȡ
//---------------------------------------------------------------------------------------
//����˫��Լ����Key��Ҫ�ٴ���֤�����������鼯�ϡ�{**��������Ҫ���Ӻ�������Key�Ͷ�����֤�����������ڴ��޸�;����Ҫ������֤������Ϊ��**}��
//No�����
//Key��˫��Լ����Key
//Domain:��Ҫ2����֤��URL��Ϊ������Ҫ2����֤
//Remark:��ע
//--------
//�ر�˵��������Ǻ�����Ľӿڷ�ʽ������Ҫ�ڴ����ã�����51�Ľӿڡ�7K7K�Ľӿڡ�webgame�Ľӿ�
//--------
extract($_REQUEST);//���ύ�����Ĳ���ת��Ϊ������ʽ

require_once('../config/config.game.php');
//---------------------------------------------------------------------------------
//����Ϊwebgame�Ľӿ�
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
			$_SESSION['lys_id'] = htmlspecialchars(trim($carray[4]));	//���ģʽ ��������Ӫ��id
			$_SESSION['LoginApiState'] = 1;	//�ѵ�½
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