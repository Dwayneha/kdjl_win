<?php
/**

*/
class guild
{
	private $m_db;	//	Db Handle

	private $m_m;	//	Memory Handle

	private $socket=NULL;
	
	function __construct($_s){
		global $_pm;
		if (!is_array($_pm) || 
			!is_object($_pm['mysql']) || 
			!is_object($_pm['mem'])
			)
		return false;
		$this-> m_db = $_pm['mysql'];
		$this-> m_m	 = $_pm['mem'];
		$this-> socket = $_s;
	}
	//ȡ���ҵļ�����Ϣ
	function getMyGuildInfo()
	{
		$info=$this->m_db->getOneRecord('select g.id,g.name,g.creator_id,g.president_id,g.honor,g.level,g.victory_times,g.failed_times,gm.honor ghonor from guild g,guild_members gm where gm.guild_id=g.id and gm.member_id='.$_SESSION['id']);
		return $info;
	}
	//����վʱ���Ƿ���
	function checkGuildFightTime()
	{
		$week = date("N", time());
		$hourM= date("Hi", time());
		
		$battletimearr = unserialize($this-> m_m->get(MEM_TIME_KEY));
		
		foreach($battletimearr as $bv){
			if($bv['titles'] != "guild_battle")
			{
				continue;
			}
			if($week == $bv['days'] && ($hourM >= $bv['starttime'] && $hourM < $bv['endtime'])){//ս���Ѿ���ʼ
				return true;
			}
		}
		return false;
	}
	
	function getChanllengeGuildInfo($id)
	{
		$info=$this->m_db->getRecords('select challenger_id,defenser_id,challenger_score,defenser_score from guild_challenges where flags=1 and challenger_id='.$id.' or defenser_id='.$id);
		if(!$info)
		{
			return "���ļ����û�н�����ս��Ҳû�м������������ս��";
		}
		
		if(count($info)>1)
		{
			return "���ļ�����ս���ݶ���һ����";
		}
		
		return $info[0];
	}

	//ȡ����ս�ҵĻ��߱���ս�ļ���ĳ�Ա
	function getChanllengeGuildMembers($id)
	{
		$info=$this->m_db->getRecords('select member_id,priv from guild_members where guild_id='.$id);
		if(!$info)
		{
			return "�з������Ա���ݴ���";
		}
		return $info;
	}
	
	//�������ս���session��Ϣ
	function clearGuildFightSession()
	{
		unset($_SESSION['guild_fight_id']);
		unset($_SESSION['guild_fight_time']);
		unset($_SESSION['guild_fight_bid']);
	}	

	//����ս������������ֵ�
	function writeGuildFightScore($winnerId,$loserId)
	{
		$this->m_db->query('BEGIN');
		$winner		=$this->m_db->getOneRecord('select guild_id,honor from guild_members where member_id='.$winnerId.' for update');
		$loser 		=$this->m_db->getOneRecord('select guild_id,honor from guild_members where member_id='.$loserId);

		$winnerGuild=$this->m_db->getOneRecord('select id,honor,level from guild where id='.$winner['guild_id'].' for update');
		$loserGuild =$this->m_db->getOneRecord('select id,honor,level from guild where id='.$loser['guild_id']);

		$challenge	=$this->m_db->getOneRecord('select id,challenger_id from guild_challenges where (challenger_id='.$winner['guild_id'].' and defenser_id='.$loser['guild_id'].') or (challenger_id='.$loser['guild_id'].' and defenser_id='.$winner['guild_id'].') limit 1');
		
		$challenge	=$this->m_db->getOneRecord('select id,challenger_id,defenser_id,challenger_score,defenser_score from guild_challenges where id='.$challenge['id'].' for update');

		if(!$winner||!$loser||!$winnerGuild||!$loserGuild||!$challenge)
		{
			$this->m_db->query('ROLLBACK');
			return "���佱��ʱ��������ս�ܻ���սʤ�������޷���ȡ(-".mysql_error().")��";
		}
		
		$honorGet=(10*(1+($loserGuild['level']-$winnerGuild['level'])*0.1));
		$guildSql='update guild set honor='.($winnerGuild['honor']+$honorGet).' where honor='.intval($winnerGuild['honor']).' and id='.$winner['guild_id'];
		$this->m_db->query($guildSql);
		$memSql='update guild_members set honor='.($winner['honor']+$honorGet).' where honor='.intval($winner['honor']).' and member_id='.$winnerId;
		//echo $memSql;
		$this->m_db->query($memSql);
		
		//$this->m_db->query('update guild_members set honor = honor+ '.$honorGet.' where member_id='.$winnerId);
		if($winner['guild_id']==$challenge['challenger_id'])
		{
			$challengeSql='update guild_challenges set challenger_score='.($challenge['challenger_score']+1).' where id='.$challenge['id'].' and challenger_score='.$challenge['challenger_score'];
		}else{
			$challengeSql='update guild_challenges set defenser_score='.($challenge['defenser_score']+1).' where id='.$challenge['id'].' and defenser_score='.$challenge['defenser_score'];
		}
		$this->m_db->query($challengeSql);
		//wr($challengeSql);
		//echo mysql_error();
		if(!$err=mysql_error()){	
			$this->m_db->query('COMMIT');
			return 'ʤ�������������'.$honorGet.'��';
		}else{
			$this->m_db->query('ROLLBACK');
			return '���佱��ʱ�������󣬸�������ʧ��(-'.mysql_error().')��';
		}
	}

	function wr($somecontent,$line=0){
		$filename = dirname(__FILE__).'/log.txt';
	
		$handle = fopen($filename, 'a+');
	
		if (fwrite($handle, '
			<-----------------------------------------------------------------
			'.$_SERVER['REQUEST_URI'].'
			-----------------------------------------------------------------
				'.$somecontent."
			----------------------------------------------------------------->	
			") === FALSE) {
			//exit;
		}
	
		fclose($handle);
	}
}
?>