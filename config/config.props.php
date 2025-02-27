<?php
/**
*@Version: %version%
*@Copyright: %copyright%
*@Author: %author%

*@Write Date: 2008.06.19
*@Update Date:
*@Usage:config of props
*/

// Notice: array key === db field.
global $mem_props;
$_props['vary']	=	array(1	=>	'可叠加',	
						  2	=>	'不可叠加');

$_props['wxs']	=	array(0,1,2,3,4,5,6,7);
$_props['wxd']	=	array('所有','金','木','水','火','土','神','神圣');


$_props['varyname']	=	array(1	=>	'辅助类',	
						  	  2	=>	'增益类',	
						  	  3	=>	'捕捉类',	
						  	  4	=>	'收集类',
							  5 =>	'技能书类',
							  6 =>	'卡片类',
							  7 =>  '进化类',
							  8 =>  '合体类',
							  9 =>	'装备类',
							  10 =>  '精炼类',
							  11 =>  '精炼类',
							  12 =>	 '礼包类',
							  13 =>  '特殊类',
							  14 => '功能类',
							  15 => '宠物卵',
							  16 => '合成类',
							  17 => '水晶类',
							  18 => '特殊回复类',
							  19 => '涅槃加成',
							  22 =>	 '魔法石',
							  23 =>	 '神圣转生道具',
							  24 => '卡片类',
							  25 => '宝石类',
							  26 => '洗练石',
							  27 => '合成保低石类',
							  28 => '刮刮卡类',
							  29 => '奇石类',
							  30=>'扫雷道具类',
							  31=>'扫雷道具类',
							  32=>'扫雷道具类',
							  50  => '魔塔回复类',
								51 => '魔塔复活类',
								52 => '魔塔解密类',
								53 => '魔塔杀伤类',
								54 => '魔塔BUFF',
								55 => '魔塔洗点类',
								56 => '魔塔洗点类',
								57 => '魔塔出战卷',
								58 => ' 魔塔增益类'

						     );
$_props['postion'] = array(   1	=>	'头部',	
						  	  2	=>	'身体',	
						  	  3	=>	'脚部',	
						  	  4	=>	'武器',
							  5 =>	'项链',
							  6 =>	'戒指',
							  7 =>  '翅膀',
							  8 =>  '手镯',
							  9 =>	'宝石',
							  10 =>  '道具'
						  );
$_props['plusflag'] = array(1	=>	'可以强化',
							2	=>	'不可以强化'
						   );
$_props['zb'] = array('openpet'	=> 	'获得一个宠物',
					  'mc'		=>	'防御',
					  'acrate'   =>  '攻击力',
					  'mcrate'   =>  '防御',
					  'openmap' =>	'开启一个地图',
					  'hp'		=>	'生命',
					  'mp'		=>	'魔法',
					  'hits'	=>	'命中',
					  'miss'	=>	'闪避',
					  'kx'		=>	'抗性',
					  'speed'   =>  '速度',
					  'ac'		=>	'攻击'
					 );

						
$_props['fjzb1'] = array('hprate'	=> 	'生命',
					  'mprate'	=>	'魔法',
					  'acrate'	=>	'攻击',
					  'mcrate'  =>	'防御',
					  'hitsrate'=>	'命中',
					  'missrate'=>	'闪避',
					  'speedrate'=>	'速度'
					 );

$_props['fjzb2'] = array('dxsh'	=> 	'伤害抵消',
					  'shjs'	=>	'伤害加深',
					  'shft'  =>	'反弹伤害',
					 );
					 
$_gm['name'] = array('mayier318','tanwei2008','leinchu'
					 );
				
$harden = array("6,100","6,300","6,600","5,1000","5,1500","5,2000","4,3000","4,3500","4,5000","3,7000","3,10000","3,15000","2,20000","2,30000","1,50000");

//离线挂机
$tuoguan = array('10-20' => '10:2:1,11:5:1,209:100:1,210:200:1,25:2:1,26:5:1,40:2:1,41:5:1,55:2:1,56:5:1,70:2:1,71:5:1,870:500:1,871:500:1,873:1000:1',
				 '21-30' => '11:2:1,12:5:1,209:100:1,210:200:1,26:2:1,27:5:1,41:2:1,42:5:1,56:2:1,57:5:1,71:2:1,72:5:1,870:500:1,871:500:1,873:1000:1',
				  '31-40' => '12:2:1,13:5:1,209:100:1,210:200:1,27:2:1,28:5:1,42:2:1,43:5:1,57:2:1,58:5:1,72:2:1,73:5:1,870:500:1,871:500:1,873:1000:1',
				  '41-50' => '13:2:1,14:5:1,209:100:1,210:200:1,28:2:1,29:5:1,43:2:1,44:5:1,58:2:1,59:5:1,73:2:1,74:5:1,870:500:1,871:500:1,873:1000:1',
				  '51-60' => '14:2:1,15:5:1,209:100:1,210:200:1,29:2:1,30:5:1,44:2:1,45:5:1,59:2:1,60:5:1,74:2:1,75:5:1,870:500:1,871:500:1,873:1000:1',
				  '61-70' => '15:2:1,16:5:1,209:100:1,210:200:1,30:2:1,31:5:1,45:2:1,46:5:1,60:2:1,61:5:1,75:2:1,76:5:1,870:500:1,871:500:1,873:1000:1',
				  '71-80' => '16:2:1,17:5:1,209:100:1,210:200:1,31:2:1,32:5:1,46:2:1,47:5:1,61:2:1,62:5:1,76:2:1,77:5:1,870:500:1,871:500:1,873:1000:1',
				  '81-90' => '17:2:1,18:5:1,209:100:1,210:200:1,32:2:1,33:5:1,47:2:1,48:5:1,62:2:1,63:5:1,77:2:1,78:5:1,870:500:1,871:500:1,873:1000:1',
				  '91-100' => '18:2:1,19:5:1,209:100:1,210:200:1,33:2:1,34:5:1,48:2:1,49:5:1,63:2:1,64:5:1,78:2:1,79:5:1,870:500:1,871:500:1,873:1000:1',
				);
				

?>