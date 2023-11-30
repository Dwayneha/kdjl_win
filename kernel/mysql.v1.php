<?php
/**
@Usage: mysql database driver for php.
@Copyright:www.webgame.com.cn
*/
register_shutdown_function("shutdown");
function shutdown(){	
	if(isset($GLOBALS['_pm'])){
		if(isset($GLOBALS['_pm']['mysql'])) $GLOBALS['_pm']['mysql']->close();				
		if(isset($GLOBALS['_pm']['mem'])) $GLOBALS['_pm']['mem']->memClose();
		$GLOBALS['_pm'] = NULL;
	}
}
class mysql{

	private static $linkHandle	=	0;

	private $errMsg		=	'';

	private $effectRows	=	'';

	// db connection initial.
	function __construct(){
		if(!is_resource(self::$linkHandle))
			$this->mysqlConnect();
	}
    
	private function mysqlConnect(){
		global $_mysql;

		if ($_mysql['contype'] == 0)
		{
			self::$linkHandle = @mysql_connect($_mysql['host'], $_mysql['user'], $_mysql['pass']);
			if (!self::$linkHandle) {
				$this->err='Connect error: ' . @mysql_error();
			}
		}
		else if ($_mysql['contype'] == 1)
		{
			self::$linkHandle = @mysql_pconnect($_mysql['host'], $_mysql['user'], $_mysql['pass']);
			if (!self::$linkHandle) {
				$this->err='Connect error: ' . @mysql_error();
			}
		}
		@mysql_select_db($_mysql['db']);
		
		$this->query("SET NAMES GBK;"); 
		$this->query("SET CHARACTER_SET_CLIENT=GBK;"); 
		$this->query("SET CHARACTER_SET_RESULTS=GBK;");		
	}
	
	public function last_id()
	{
		return mysql_insert_id(self::$linkHandle);
	}
	
	// get all record.
	public function getRecords($sql, $type=0){
		/*
		global $_pm;
		$memKey = "_getRecords_";
		$timeMem=unserialize($_pm['mem']->get($memKey));
		if(!is_array($timeMem)){
			$timeMem=array();
		}
		if(!array_key_exists($sql,$timeMem)){
			$timeMem[$sql]=1;
		}else{
			$timeMem[$sql]++;
		}
		$_pm['mem']->set(array("k"=>$memKey,"v"=>$timeMem));
		*/
		$this->safeConn();
		$qd	=	$this->query($sql);
		$i	=	0;
		if ($qd !== FALSE)
		{
			while($rs=@mysql_fetch_assoc($qd))
			{
				$ret[$i++] = $rs;
			}

			if ($type==1) $this->effectRows = @mysql_num_rows();
			else if ($type==2) $this->effectRows = @mysql_affected_rows();
			else $this->effectRows = FALSE;

			@mysql_free_result($qd);
			return $ret;
		}
		else return FALSE;
	} 
	
	// Get query effect rows.
	public function getEffectRows(){
		return $this->effectRows;
	}

	// Get one record.
	// type:1->select,2->insert,update,replace,delete.0 is default none
	public function getOneRecord($sql, $type=0){
		/*
		global $_pm;
		$memKey = "_getRecord_";
		$timeMem=unserialize($_pm['mem']->get($memKey));
		if(!is_array($timeMem)){
			$timeMem=array();
		}
		if(!array_key_exists($sql,$timeMem)){
			$timeMem[$sql]=1;
		}else{
			$timeMem[$sql]++;
		}
		$_pm['mem']->set(array("k"=>$memKey,"v"=>$timeMem));
		*/
		$this->safeConn();
		$qd	=	$this->query($sql);
		if ($qd !== FALSE)
		{
			$ret = @mysql_fetch_assoc($qd);
			if ($type==1) $this->effectRows = @mysql_num_rows();
			else if ($type==2) $this->effectRows = @mysql_affected_rows();
			else $this->effectRows = FALSE;

			@mysql_free_result($qd);
			return $ret;
		}
		else return FALSE;
	}

	// Database Query.
	public function query($sql){
		$this->safeConn();
		/*if($_SESSION['id'] == '110')
		{
			if((strpos(strtolower($sql),'update') !== false || strpos(strtolower($sql),'insert') !== false) && strpos(strtolower($sql),'tasklog') !== false)
			{
				
				$dates = date("Y-m-d H:i:s");
				$str = $_SERVER['REQUEST_URI']."------>".$dates.'----------->'.$sql.'=========='.__LINE__;
				mysql_query("INSERT INTO gamelog(ptime,seller,buyer,pnote,vary) VALUES (".time().",111,111,'".$str."',111)");
			}
		}*/

		
		if ( ($hd=mysql_query($sql)) === FALSE)
		{
			$this->errMsg = 'Query error:' . @mysql_error();
			return FALSE;
		}
		else
		{
			return $hd;
		}
	}

	public function getError(){
		return $this->errMsg;
	}

	public function safeConn()
	{
		
		if (!is_resource(self::$linkHandle))
		{
			$this->mysqlConnect();
			if(!is_resource(self::$linkHandle)) 
				die('<script>window.location.reload();</script>');
		}
	}
   	public function close(){
		@mysql_close(self::$linkHandle);
		self::$linkHandle = NULL;
	}
	public function getConn(){		
		return self::$linkHandle;
	}
    // class destruct.
    function __destruct(){
		@mysql_close(self::$linkHandle);
	}
}
?>