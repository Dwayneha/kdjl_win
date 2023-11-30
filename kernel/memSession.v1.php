<?php 
//�趨 SESSION ��Чʱ�䣬��λ�� ��
if(!isset($_game['version'])){
	// Game Name
	$_game['name']	=	'�ڴ�����';
	
	// Game Version
	$_game['version']	=	'v1';
	
	// Game Keyword
	$_game['keyword']   =	'';
	
	// Game Desc
	$_game['keydesc']	=	'';
	
	$_game['kernel']	=	$_SERVER['DOCUMENT_ROOT'].'/kernel/';
	$_game['config']	=	$_SERVER['DOCUMENT_ROOT'].'/config/';
	$_game['template']  =	$_SERVER['DOCUMENT_ROOT'].'/template/';
	$_game['sec']  		=	$_SERVER['DOCUMENT_ROOT'].'/sec/';
	
	$_mem['host']		=	'192.168.0.109';
	$_mem['port']		=	'11214';
}

define('SESS_LIFTTIME', 3600);
//����memcache������Ϣ
define('MEMCACHE_HOST', $_mem['host']);
define('MEMCACHE_PORT', $_mem['port']);

if (!class_exists('memSession'))
{
	class memSession{
		static  $mSessSavePath;
		static  $mSessName;
		static  $mMemcacheObj;
		public function __construct()
		{
			if (!class_exists('Memcache') || !function_exists('memcache_connect'))
			{
				die('Memcache extension not exists!');
			}
	
			if (!empty(self::$mMemcacheObj) && is_object(self::$mMemcacheObj))
			{
				return false;
			}
			self::$mMemcacheObj = new Memcache;
			if (!self::$mMemcacheObj->connect(MEMCACHE_HOST , MEMCACHE_PORT))
			{
				die('Fatal Error: Can not connect to memcache host '. MEMCACHE_HOST .':'. MEMCACHE_PORT);
			}
			return TRUE;
		}

		public static function sessOpen($pSavePath = '', $pSessName = '')
		{
			self::$mSessSavePath    = $pSavePath;
			self::$mSessName        = $pSessName;
			return TRUE;
		}
		
		public static function sessClose()
		{
			return TRUE;
		}
		
		public static function sessRead($wSessId = '')
		{
			if(strlen($wSessId) == 0) return;

			$wData = self::$mMemcacheObj->get($wSessId);
			//�ȶ����ݣ����û�У��ͳ�ʼ��һ��
			if (!empty($wData))
			{
				return $wData;
			}
			else
			{
				//��ʼ��һ���ռ�¼
				$ret = self::$mMemcacheObj->set($wSessId, '', 0, SESS_LIFTTIME);
	
				if (TRUE != $ret)
				{
					die("Session error(".__LINE__.")!");
	
					return FALSE;
				}
	
				return TRUE;
			}
		}
		
		public static function sessWrite($wSessId = '', $wData = '')
		{
			$ret = self::$mMemcacheObj->replace($wSessId, $wData, 0, SESS_LIFTTIME);
			if (TRUE != $ret)
			{
				die("Fatal Error: SessionID $wSessId Save data failed!");
	
				return FALSE;
			}
			return TRUE;
		}
		
		public static function sessDestroy($wSessId = '')
		{
			self::sessWrite($wSessId);
	
			return FALSE;
		}
		
		public static function sessGc()
		{
			//����������,memcache���Լ��Ĺ��ڻ��ջ���
	
			return TRUE;
		}
		
		public function initSess()
		{
			//$domain = '.imysql.cn';
			//��ʹ�� GET/POST ������ʽ
			//ini_set('session.use_trans_sid',    0);
	
			//�������������������ʱ��
			//ini_set('session.gc_maxlifetime',   SESS_LIFTTIME);
	
			//ʹ�� COOKIE ���� SESSION ID �ķ�ʽ
			//ini_set('session.use_cookies',      1);
			//ini_set('session.cookie_path',      '/');
	
			//������������ SESSION ID �� COOKIE
			//ini_set('session.cookie_domain',    $domain);
	
			//�� session.save_handler ����Ϊ user��������Ĭ�ϵ� files
			//session_module_name('memcache');
			session_write_close();
			session_module_name('memcache');
			//���� SESSION �����������Ӧ�ķ�������
			session_set_save_handler(
					array('memSession', 'sessOpen'),   //��Ӧ�ھ�̬���� My_Sess::open()����ͬ��
					array('memSession', 'sessClose'),
					array('memSession', 'sessRead'),
					array('memSession', 'sessWrite'),
					array('memSession', 'sessDestroy'),
					array('memSession', 'sessGc')
					);
	
			session_start();			
			return TRUE;
		}
		// }}}
	
	}//end class
	$memSess    = new memSession;
	$memSess->initSess();

}//end define

?>