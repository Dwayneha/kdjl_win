<?php
/**
@Usage: club base class
@Copyright:www.webgame.com.cn
@Version:1.0
@Write date: 2008-09-12
@Write By: GeFei SU
@Club logic descs:
  ���ϵͳ��Ҫ���������ɣ�
   һ���ǰ����Ϣ���洢���а�����Ϣ��=> clublist
   �ڶ��ǰ���Ա��, �洢����ĳ��Ա���ڰ��ĸ�����Ϣ��
   
*/
class club{
	// ���ݿ����
	public $m_db;

	// �ڴ����
	public $m_m;
	
	public function __construct(){
		global $_pm;
		if (!is_array($_pm) || 
			!is_object($_pm['mysql']) || 
			!is_object($_pm['mem']) ||
			!is_object($_pm['user'])
			)
		return false;

		$this-> m_db = $_pm['mysql'];
		$this-> m_m	 = $_pm['mem'];
	}

	/** 
	*@Usage: �������
	*@Param: $name String �������
	*@Return true of false
	*/
	public function create($name)
	{
		// ��ǰ����Ƿ�ӵ�а�ᡣ
		$hadclub = $this->m_db->getOneRecord("SELECT id 
		                                        FROM club_user 
											   WHERE uid={$_SESSION['id']} 
											   LIMIT 0,1");
		if (is_array($hadclub)) return false;

		$length = strlen($name);
		if ($length<6 || $length>20) return false;

		// ��֤�û��Ļ�����Ϣ��

		$exists = $this->m_db->getOneRecord("SELECT id 
											   FROM club
											  WHERE name='{$name}'
											  LIMIT 0,1
											");
		if (!is_array($exists))
		{
			// ��ʼ���������Ϣ��
			$this->m_db->query("INSERT INTO club(name,masters,ctime,levels)
							    VALUES('{$name}','{$_SESSION['id']}',".time().",'����,����Ա')
							   ");

			$self = $this->m_db->getOneRecord("SELECT id
												 FROM club
												WHERE {$_SESSION['id'] in(masters)}");
			if (!is_array($self)) return false;

			// �����û����ϡ�
			$this->m_db->query("INSERT INTO club_user(cid,uid,levelname,ctime)
							    VALUES({$self['id']},{$_SESSION['id']},'����',".time().")
							  ");
			// �۳������Ҫ���������ݡ�

			return true;
		}
		else return false;
	}

    // ��ɢ���
    public function cancel(){}

	// ���ð��
	public function setClub(){}
   
    /**
	*@Usage: ��ӳ�Ա
	*@Param: $name String ����ǳ�
	*@Return: true of false
	**/
	public function addMember($uid)
	{
		if (intval($uid)<0) return false;
		
		$self = $this->m_db->getOneRecord("SELECT id
											 FROM club
											WHERE {$_SESSION['id'] in(masters)}");
		if (!is_array($self)) return false;
		
		$had = $this->m_db->getOneRecord("SELECT id
										    FROM club_user
										   WHERE uid={$uid}
										 ");
		if (!is_array($had))
		{
			$this->m_db->query("INSERT INTO club_user(cid,uid,levelname,ctime)
							    VALUES({$self['id']},{$uid},'����Ա',".time().")
							  ");
			return true;
		}
		else return false;
	}

	// ɾ����Ա
	public function delMember(){}

	/**
	*@Usage:  �޸ĳ�Ա���ڰ��ļ���
	*@Param:  $levelName String ��ҵİ�ἶ���Ρ�
	          $uid Int ��ҵ��û�ID��
	*@Return: true of false
	*/ 
    public function setMember($levelName, $uid)
	{
		$master = $this->m_db->getOneRecord("SELECT id
											   FROM club
											  WHERE $_SESSION['id'] in(masters)
											  LIMIT 0,1
											");
		if (is_array($master))
		{
			// �������Ƿ����ݵ�ǰ��ҵ����ڰ�ᡣ
			$inline = $this->m_db->getOneRecord("SELECT id 
												   FROM club_user 
												  WHERE cid={$master['id']} 
												  LIMIT 0,1");
			if (is_array($inline))
			{
				$this->m_db->query("UPDATE club_user
				                       SET levelname='{$levelName}'
									 WHERE uid={$uid}
								   ");
				return true;
			}
		}
		return false;
	}

	/**
	*@Usage: ��ʾ��ṫ��
	*@Param: $cid int ���ID
	*@Return: String
	*/
	public function getClubInfo($cid)
	{
		if (intval($cid)<1) return false;
		$word = $this->m_db->getOneRecord("SELECT name,aword,king,maxmember,masters,levels,ctime
		                                     FROM club
											WHERE id={$cid}
		                                    LIMIT 0,1
										 ");
		return $word;
	}

	/**
	*@Usage: ���ð����Ϣ
	*@Param: $info array ���ð��Ļ�����Ϣ��
	*@Return: true of false;
	*/
	public function setBasePar($info)
	{
		if (!is_array($info)) return false;
		else
		{
			$updatestr = '';
			foreach ($info as $k => $rs)
			{
				if ($k == 'aword') $updatestr = $updatestr==''?"aword='{$info['aword']}'":",aword='{$info['aword']}'";
				else if ($k == 'masters') 
					$updatestr = $updatestr==''?"masters='{$info['masters']}'":",masters='{$info['masters']}'";
				else if ($k == 'levels')
					$updatestr = $updatestr==''?"levels='{$levels}'":",levels='{$levels}'";
			}
			$this->m_db->query("UPDATE club
								   SET {$updatestr}
							     WHERE {$_SESSION['id']} in (masters)
							   ");	
			return true;
		}
	}

	// Default destruct.
	public function __destruct(){
	
	}
}

class kdclub extends club{

	public $m_maxperson	=	10;	//�������Ա��

	public $m_name		=	'';	//�������


	public function __construct(){parent::__construct();}

	public function task(){}	// �������ϵͳ��

	public function chat(){}	// �������ϵͳ

	public function fb(){}		// ��ḱ��

	public function __destruct(){parent::__destruct();}
}
?>