<?php
/**
@Usage: װ����
@Copyright:www.webgame.com.cn
@Version:1.1
@Write: 2008.08.12
@Modify Date: 2008.09.16 
       Note: ����CLASS�е�һЩ���������з��룬���������ݿ����ܡ�
@Memo:
*/
class equipment
{
	// װ����ʾͷ����ɫ,����Ĭ�ϡ�
	public $ep_title = '#ED9037';

	// װ����ʾ����������ɫ
	public $ep_base = '#FEFDFA';

	// װ����ʾ����������ɫ
	public $ep_plus = '#0067CB';

	// װ����ʾ����������ɫ
	public $ep_special = '#14FD10';
	
	//��ɫ
	public $ep_glod = '#FED625';
	//��ɫ
	public $ep_green = '#A8A7A4';
	// ���������������ɫ�������塣
	
	// ���ݿ����
	public $m_db;

	// �ڴ����
	public $m_m;

    // expiration time
    public $expiration; // added by Zheng.Ping

    // equipment html content part one
    public $tooltip_html_one; // added by Zheng.Ping

    // equipment html content part two
    public $tooltip_html_two; // added by Zheng.Ping
	
	/**
	*@Usage:��Ĭ�ϵĹ��캯������ʼ���ڴ漰���ݿ����
	*@Param:��
	*@Return:void(0)
	*/
	public function __construct()
	{
		global $_pm;
		if (!is_array($_pm) || 
			!is_object($_pm['mysql']) || 
			!is_object($_pm['mem'])
			)
		return false;

		$this-> m_db = $_pm['mysql'];
		$this-> m_m	 = $_pm['mem'];
        /* added by Zheng.Ping */
        $this->tooltip_html_one = null;
        $this->tooltip_html_two = null;
        /* added by Zheng.Ping */
	}
	
	/**
	*@Usage:
	*@Param:
	*@Return: false or array of props.
	*/
	public function getProps($id,$type)
	{
		global $_pm;
		if($id == "" || $id < 0)
		{
			return false;
		}
		else
		{
			if ($type == 1)
			{
				$arr = unserialize($_pm['mem']->get('db_propsid'));
				$rs = $arr[$id];
				//��ѯ�û�������
				$sql = "SELECT cantrade FROM userbag WHERE id = $id";
				$rsb = $this -> m_db -> getOneRecord($sql);
				$rs['cantrade'] = $rsb['cantrade'];
			}
			else
			{
				//��ѯ�û�������
				$sql = "SELECT props.*,userbag.id as pid,userbag.cantrade,userbag.F_item_hole_info FROM props,userbag WHERE userbag.id = $id and userbag.pid = props.id";
				$rs = $this -> m_db -> getOneRecord($sql);
			}
			return $rs;
		}
	}
	
	/**
	*@Usage:
	*@Param:
	*@Return:
	*/
	public function setColor($title, $base, $plus, $special,$glod,$green)
	{
		if($title == "")
		{
			$this -> ep_title = '#ED9037';
		}
		if($base == "")
		{
			$this -> ep_base = '#FEFDFA';
		}
		if($plus == "")
		{
			$this -> ep_plus = '#9833DC';
		}
		if($special == "")
		{
			$this -> ep_special = '#14FD10';
		}
		if($glod == "")
		{
			$this -> ep_glod = '#FED625';
		}
		if($green == "")
		{
			$this -> ep_green = '#A8A7A4';
		}
	}
	
	/**
	*@Usage:�жϵ����ĸ�����
	*@Param: 
	      $id    ����ID
		  $bid   ����ID
		  $type  ���ͣ�
	*@Return:
	*/
	public function div($id,$bid = "",$sign = 0,$type=1)
	{
		$result = "";
		global $_props;
		$rs = $this->getProps($id,$type);
		if (!is_array($rs)) return false;

		if ($rs['varyname'] == 9)
		{
			$result = $this -> zhuangbei($id,$bid,$sign,$type);
		}
		else if ($rs['varyname'] == 5)
		{
			$result = $this -> jineng($id,$type);
		}
		else if ($rs['varyname'] == 25)
		{
			$result = $this -> gam($id,$type);
		}
		else if ($rs['varyname'] == 22 && !isset($_GET['frzb']))
		{
			$result = '<font color="#ffffff">���ص�ħ��ʯ��<span style="cursor:pointer" onclick="$(\'gw\').contentWindow.location=\'/function/zhanbuwu.php\'">ħ���ݵ�ܽ���</span>���԰���ʹ����Ŷ��</font>';
		}
		else
		{
			//����
			$result = $this -> daoju($id,$type);
		}

        $this->set_expiration($rs['expire']); // added by Zheng.Ping

		$results = '<table style="font-size:12px;" width=185 cellpadding=0 cellspacing=0 border=0>
					<tr> <td background=../images/ui/tips/border4_tl.gif width=5 height=5></td>
					<td background=../images/ui/tips/border4_t.gif></td>
					<td background=../images/ui/tips/border4_tr.gif></td>
					</tr>
					<tr><td width=5 background=../images/ui/tips/border4_l.gif></td>
					<td   style="background:#1F1F30;filter:Alpha(opacity=90);" align=center></td>
					<td width=5 background=../images/ui/tips/border4_r.gif></td></tr><tr><td width=5 background=../images/ui/tips/border4_l.gif></td>
					<td style="background:#1F1F30;filter:Alpha(opacity=90);">'.$result.'</td><td width=5 background=../images/ui/tips/border4_r.gif></td>
					</tr><tr><td background=../images/ui/tips/border4_bl.gif width=5 height=5></td><td background=../images/ui/tips/border4_b.gif></td>
					<td background=../images/ui/tips/border4_br.gif></td>
					</tr>
					</table>';

        /* added by Zheng.Ping */
        if ($this->tooltip_html_one) {
            $this->tooltip_html_one = '<table style="font-size:12px;" width=185 cellpadding=0 cellspacing=0 border=0>
					<tr> <td background=../images/ui/tips/border4_tl.gif width=5 height=5></td>
					<td background=../images/ui/tips/border4_t.gif></td>
					<td background=../images/ui/tips/border4_tr.gif></td>
					</tr>
					<tr><td width=5 background=../images/ui/tips/border4_l.gif></td>
					<td   style="background:#1F1F30;filter:Alpha(opacity=90);" align=center></td>
					<td width=5 background=../images/ui/tips/border4_r.gif></td></tr><tr><td width=5 background=../images/ui/tips/border4_l.gif></td>
					<td style="background:#1F1F30;filter:Alpha(opacity=90);">' . $this->tooltip_html_one;
        }
        if ($this->tooltip_html_two) {
            $this->tooltip_html_two .= '</td><td width=5 background=../images/ui/tips/border4_r.gif></td>
					</tr><tr><td background=../images/ui/tips/border4_bl.gif width=5 height=5></td><td background=../images/ui/tips/border4_b.gif></td>
					<td background=../images/ui/tips/border4_br.gif></td>
					</tr>
					</table>';
        }
        /* added by Zheng.Ping */

		return $results;
	}

	/**
	*@Usage:װ����ʾ
	*@Param:$id-> ����id, $bid-> ����id��
	*@Return: String
	*/
	public function zhuangbei($id,$bid = "",$sign = 0,$type = 1)
	{
		global $_props;
		$rs = $this->getProps($id,$type);
		if (!is_array($rs)) return false;
		
		$er[0]= "";
		if($sign != 0)
		{
			$arr = $this -> tms($id);
			if($arr['tms'] == 1)
			{
				$er = explode(",",$arr['plus_tms_eft']);
				$er[0] = $er[0] + 1;
				$str = "+".$er[0];
			}
		}
		switch($rs['propscolor'])
		{
			case 1:
				$div .= '<font color="#FEFDFA"><b>'.$rs['name'].'&nbsp;'.$str.'</b></font><br/>';
				break;
			case 2:
				$div .= '<font color="#0067CB"><b>'.$rs['name'].'&nbsp;'.$str.'</b></font><br/>';
				break;
			case 3:
				$div .= '<font color="#9833DC"><b>'.$rs['name'].'&nbsp;'.$str.'</b></font><br/>';
				break;
			case 4:
				$div .= '<font color="#14FD10"><b>'.$rs['name'].'&nbsp;'.$str.'</b></font><br/>';
				break;
			case 5:
				$div .= '<font color="#FED625"><b>'.$rs['name'].'&nbsp;'.$str.'</b></font><br/>';
				break;	
			case 6:
				$div .= '<font color="#ED9037"><b>'.$rs['name'].'&nbsp;'.$str.'</b></font><br/>';
				break;
		}
		//�Ժ���ǿ���ȼ�
					
		//�Ƿ�ɽ���
		if($rs['cantrade'] == 0){
			if($rs['propslock'] == 0){
				$cantradestr = '���ɽ���';
			}else{
				$cantradestr = '�ɽ���';
			}
		}else if($rs['cantrade'] == 1){
			$cantradestr = '�ɽ���';
		}else{
			$cantradestr = '���ɽ���';
		}
		$div .= '<font color='.$this -> ep_green.'>'.$cantradestr. '</font><br/>';

        $this->tooltip_html_one = $div; /* this line is added by Zheng.Ping, for showing the expiration time */

		/*$div .= '<tr>
				 <td height="20" align="left"><img src="images/props/'.$rs['img'].'"></td>
			</tr>';*/
			
		//�õ�װ��λ��
		/*$postion = str_replace(array(1,2,3,4,5,6,7,8,9),
		                       $_props['postion'],
							   $rs['postion']);*/
							   
		$postion = $_props['postion'][$rs['postion']];
		
		//�Ƿ��ǿ��
		$plusflag = str_replace(array(1,0),
		                        array("��ǿ��","����ǿ��"),
								$rs['plusflag']);

		$div .= '<font color='.$this -> ep_base.'>'.$postion.'װ��&nbsp('.$plusflag.')</font><br/>';
        $this->tooltip_html_two .= '<font color='.$this -> ep_base.'>'.$postion.'װ��&nbsp('.$plusflag.')</font><br/>'; // added by Zheng.Ping

		//��������
		$div .= $this->getZbBaseAttrib($rs,$sign);
        $this->tooltip_html_two .= $this->getZbBaseAttrib($rs,$sign); // added by Zheng.Ping

		//�õ����к���Ҫ�ĵȼ�
		if (!empty($rs['requires']))
		{
			$requires = explode(",", $rs['requires']);
			$lv = explode(":", $requires[0]); 
			$a = explode(":", $requires[1]);
			$wx = str_replace(array(0,1,2,3,4,5,6,7),array('����','��','ľ','ˮ','��','��','��','��ʥ'),$a[1]);
			$div .= '<font color='.$this -> ep_base.'>��������'.$wx.'ϵ</font><br/>';
			$div .= '<font color='.$this -> ep_base.'>����ȼ���'.$lv[1].'��</font><br/>';
			$this->tooltip_html_two .= '<font color='.$this -> ep_base.'>��������'.$wx.'ϵ</font><br/>'; // added by Zheng.Ping
			$this->tooltip_html_two .= '<font color='.$this -> ep_base.'>����ȼ���'.$lv[1].'��</font><br/>'; // added by Zheng.Ping
		}
		
		// ��ȡװ����Ʒ����		
		$div .=  $this->getZbPlusAttrib($rs);
        $this->tooltip_html_two .= $this->getZbPlusAttrib($rs); // added by Zheng.Ping
		
		//��ȡװ����������
		$div .= $this->getZbCardAttrib($rs);
        $this->tooltip_html_two .= $this->getZbCardAttrib($rs); // added by Zheng.Ping
		
		// ��ȡװ����װ�������ԡ�
		$mid_para = $this->getZbSeriesAttrib($rs, $bid);
		$div .= $mid_para;
        $this->tooltip_html_two .= $mid_para; // added by Losttempler


		
	   //˵��
		$div .= '<font color='.$this -> ep_base.'>'.$rs['usages'].'</font><br/>
					';

        $this->tooltip_html_two .= '<font color='.$this -> ep_base.'>'.$rs['usages'].'</font><br/>'; // added by Zheng.Ping


		return $div;
	}
	
	/**
	*@Usage:������ʾ
	*@Param:
	*@Return:
	*/
	function daoju($id,$type = 1)
	{
		global $_props;
		$rs = $this->getProps($id,$type);
		if (is_array($rs))
		{
			switch($rs['propscolor'])
			{
				case 1:
					$div .= '<font color="#FEFDFA"><b>'.$rs['name'].'</b></font><br/>';
					break;
				case 2:
					$div .= '<font color="#0067CB"><b>'.$rs['name'].'</b></font><br/>';
					break;
				case 3:
					$div .= '<font color="#9833DC"><b>'.$rs['name'].'</b></font><br/>';
						break;
				case 4:
					$div .= '<font color="#14FD10"><b>'.$rs['name'].'</b></font><br/>';
						break;
				case 5:
					$div .= '<font color="#FED625"><b>'.$rs['name'].'</b></font><br/>';
					break;	
				case 6:
					$div .= '<font color="#ED9037"><b>'.$rs['name'].'</b></font><br/>';
					break;
			}//�Ժ���ǿ���ȼ�
				
			//�Ƿ�ɽ���
			if($rs['cantrade'] == 0){
				if($rs['propslock'] == 0){
					$cantradestr = '���ɽ���';
				}else{
					$cantradestr = '�ɽ���';
				}
			}else if($rs['cantrade'] == 1){
				$cantradestr = '�ɽ���';
			}else{
				$cantradestr = '���ɽ���';
			}
			$div .= '<font color='.$this -> ep_green.'>'.$cantradestr. '</font><br/>';
			/*$div .= '<tr>
				 <td height="20" align="left"><img src="images/props/'.$rs['img'].'"></td>
			</tr>';*/
            $this->tooltip_html_one = $div; /* this line is added by Zheng.Ping, for showing the expiration time */
		}
		
		//˵��
		$div .= '<font color='.$this -> ep_base.'>'.$rs['usages'].'</font><br/>';
        $this->tooltip_html_two = '<font color='.$this -> ep_base.'>' . $rs['usages'] . '</font><br/>'; // added by Zheng.Ping

		return $div;
	}
	/**
	*@Usage:��ʯ��ʾ
	*@Param:
	*@Return:
	*/	
	function gam($id,$type = 1)
		{
		global $_props;
		$rs = $this->getProps($id,$type);
		if (is_array($rs))
		{
			switch($rs['propscolor'])
			{
				case 1:
					$div .= '<font color="#FEFDFA"><b>'.$rs['name'].'</b></font><br/>';
					break;
				case 2:
					$div .= '<font color="#0067CB"><b>'.$rs['name'].'</b></font><br/>';
					break;
				case 3:
					$div .= '<font color="#9833DC"><b>'.$rs['name'].'</b></font><br/>';
						break;
				case 4:
					$div .= '<font color="#14FD10"><b>'.$rs['name'].'</b></font><br/>';
						break;
				case 5:
					$div .= '<font color="#FED625"><b>'.$rs['name'].'</b></font><br/>';
					break;	
				case 6:
					$div .= '<font color="#ED9037"><b>'.$rs['name'].'</b></font><br/>';
					break;
			}//�Ժ���ǿ���ȼ�
				
			//�Ƿ�ɽ���
			if($rs['cantrade'] == 0){
				if($rs['propslock'] == 0){
					$cantradestr = '���ɽ���';
				}else{
					$cantradestr = '�ɽ���';
				}
			}else if($rs['cantrade'] == 1){
				$cantradestr = '�ɽ���';
			}else{
				$cantradestr = '���ɽ���';
			}
			$div .= '<font color='.$this -> ep_green.'>'.$cantradestr. '</font><br/>';
			$div .= "<font color='red'>";
			if( isset($rs['requires']) && !empty($rs['requires']) )
			{
				$div .= "��Ƕ��λ:";
				$mid_arr = explode(',',$rs['requires']);
				foreach( $mid_arr as $requires_info )
				{
					$mid_arr_info = explode(':',$requires_info);
					if($mid_arr_info[0] == "postion" )
					{
						$div .= "";
						$mid_arr_info_end = explode('|',$mid_arr_info[1]);
						foreach( $mid_arr_info_end as $postion_infomation )
						{
							switch($postion_infomation)
							{
								case "1" :
								{
									$div .= "ͷ�� ";
									break;
								}
								case "2" :
								{
									$div .= "���� ";
									break;
								}
								case "3" :
								{
									$div .= "ѥ�� ";
									break;
								}
								case "4" :
								{
									$div .= "���� ";
									break;
								}
								case "5" :
								{
									$div .= "���� ";
									break;
								}
								case "6" :
								{
									$div .= "��ָ ";
									break;
								}
								case "7" :
								{
									$div .= "��� ";
									break;
								}
								case "8" :
								{
									$div .= "���� ";
									break;
								}
								case "9" :
								{
									$div .= "��ʯ ";
									break;
								}
								case "10" :
								{
									$div .= "���� ";
									break;
								}
							}
						}
						
					}
					if( $mid_arr_info[0] == "color" )
					{
						switch($mid_arr_info[1])
						{
							case "2" :
							{
								$color = "��ɫװ��";
								break;
							}
							case "3" :
							{
								$color = "��ɫװ��";
								break;
							}
							case "4" :
							{
								$color = "��ɫװ��";
								break;
							}
							case "5" :
							{
								$color = "��ɫװ��";
								break;
							}
							case "6" :
							{
								$color = "��ɫװ��";
								break;
							}
						}
						$div .= "ֻ����Ƕ".$color;
					}
				}
			}
			else
			{
				$div .= "������";
			}
			$div .= "</font><br>";
            $this->tooltip_html_one = $div; 
		}
		
		//˵��
		$div .= '<font color='.$this -> ep_base.'>'.$rs['usages'].'</font><br/>';
        $this->tooltip_html_two = '<font color='.$this -> ep_base.'>' . $rs['usages'] . '</font><br/>'; // added by Zheng.Ping

		return $div;
	}
	
		/**
	*@Usage:��������ʾ
	*@Param:
	*@Return:
	*/
	function jineng($id,$type = 1)
	{
		global $_props;
		$rs = $this->getProps($id,$type);
		if (!is_array($rs)) return '';

		switch($rs['propscolor'])
		{
			case 1:
				$div .= '<font color="#FEFDFA"><b>'.$rs['name'].'</b></font><br/>';
				break;
			case 2:
				$div .= '<font color="#0067CB"><b>'.$rs['name'].'</b></font><br/>';
				break;
			case 3:
				$div .= '<font color="#9833DC"><b>'.$rs['name'].'</b></font><br/>';
				break;
			case 4:
				$div .= '<font color="#14FD10"><b>'.$rs['name'].'</b></font><br/>';
				break;
			case 5:
				$div .= '<font color="#FED625"><b>'.$rs['name'].'</b></font><br/>';
				break;	
			case 6:
				$div .= '<font color="#ED9037"><b>'.$rs['name'].'</b></font><br/>';
				break;
		}
			//�Ժ���ǿ���ȼ�
		/*$div .= '<tr>
				 <td height="20" align="left"><img src="images/props/'.$rs['img'].'"></td>
			</tr>';*/
		if($rs['cantrade'] == 0){
			if($rs['propslock'] == 0){
				$cantradestr = '���ɽ���';
			}else{
				$cantradestr = '�ɽ���';
			}
		}else if($rs['cantrade'] == 1){
			$cantradestr = '�ɽ���';
		}else{
			$cantradestr = '���ɽ���';
		}
		$div .= '<font color='.$this -> ep_green.'>'.$cantradestr. '</font><br/>';
		$this->tooltip_html_one = $div; /* this line is added by Zheng.Ping, for showing the expiration time */
		
		//�õ����к���Ҫ�ĵȼ�
		if (!empty($rs['effect']))
		{
			$str = explode(":", $rs['effect']);
			if ($str[0] == "kx")
			{
				$num = explode(",", $str[1]);
				foreach($num as $n => $ar)
				{
					if(!empty($num[1]) && $num[1] == $num[2] && $num[2] ==$num[3] && $num[3] == $num[4] && $num[4] == $num[5])
					{
						$div .= '<font color='.$this -> ep_base.'>+'.$num[1].'&nbsp;ȫ��</font><br/>';
						$this->tooltip_html_two .= '<font color='.$this -> ep_base.'>+'.$num[1].'&nbsp;ȫ��</font><br/>'; /* this line is added by Zheng.Ping, for showing the expiration time */
						break;
					}
					if($ar != 0)
					{
						$div .= '<font color='.$this -> ep_base.'>+&nbsp'.$ar.'&nbsp;'.$_props['wxd'][$n].'��</font><br/>';
						$this->tooltip_html_two .= '<font color='.$this -> ep_base.'>+&nbsp'.$ar.'&nbsp;'.$_props['wxd'][$n].'��</font><br/>'; /* this line is added by Zheng.Ping, for showing the expiration time */
					}
					/*$effect = "+".$str[1].str_replace($_props['zb'],array("openpet","mc","ac","openmap","hp","mp","hits","miss","kx"),$str[0]);
					$div .= '<tr>
						<td height="20" align="left"><font color='.$this -> ep_plus.'>+'.$effect.'</font></td>
						</tr>';*/
				}
			}
			else
			{
				/*$effect = str_replace(array("openpet","mc","ac","openmap","hp","mp","hits","miss","kx","speed"),
				                      $_props['zb'],
									  $str[0]);*/
				 $effect = $_props['zb'][''.$str[0].''];
				$div .= '<font color='.$this -> ep_base.' class="line">+'.$str[1].'&nbsp;'.$effect.'</font><br/>';
				$this->tooltip_html_two .= '<font color='.$this -> ep_base.' class="line">+'.$str[1].' '.$effect.'</font><br/>'; /* this line is added by Zheng.Ping, for showing the expiration time */
			}
		}
	
				
		//��Ʒ����
		if(!empty($rs['pluseffect']))
		{
			$str = explode(",",$rs['pluseffect']);
			for($i = 0;$i < count($str);$i++)
			{
				$sx = explode(":",$str[$i]);
				if($sx[0] != "kx")
				{
					/*$effect = $sx[1]." ".str_replace(array("openpet","mc","ac","openmap","hp","mp","hits","miss","kx","speed"),
														$_props['zb'],
														$sx[0]);*/
					$effect = $sx[1]." ".$_props['zb'][''.$sx[0].''];
					$div .= '<font color='.$this -> ep_plus.'>+'.$effect.'</font><br/>';
					$this->tooltip_html_two .=  '<font color='.$this -> ep_plus.'>+'.$effect.'</font><br/>'; /* this line is added by Zheng.Ping, for showing the expiration time */
				}
				else
				{
					$nums = explode(":", $str[$i]);
					foreach($nums as $ns => $ars)
					{
						if(!empty($nums[1]) && $nums[1] == $nums[2] && $nums[2] ==$nums[3] && $nums[3] == $nums[4] && $nums[4] == $nums[5])
						{
							$div .= '<font color='.$this -> ep_plus.'>+'.$nums[1].'&nbsp;ȫ��</font><br/>';
							$this->tooltip_html_two .= '<font color='.$this -> ep_plus.'>+'.$nums[1].'&nbsp;ȫ��</font><br/>'; /* this line is added by Zheng.Ping, for showing the expiration time */
							break;
						}
						if($ars == 0 || !is_numeric($ars))
						{
							continue;
						}
						else
						{
							$a = $ns - 1;
							$div .= '<font color='.$this -> ep_plus.'>+&nbsp'.$ars.'&nbsp;'.$_props['wxd'][$a].'��</font><br/>';
							$this->tooltip_html_two .= '<font color='.$this -> ep_plus.'>+&nbsp'.$ars.'&nbsp;'.$_props['wxd'][$a].'��</font><br/>'; /* this line is added by Zheng.Ping, for showing the expiration time */
						}
					}
				}
			} 
		} // end if ��Ʒ����
		
	
		//˵��
		$div .= '<font color='.$this -> ep_base.'>'.$rs['usages'].'</font><br/>';
		$this->tooltip_html_two .= '<font color='.$this -> ep_base.'>'.$rs['usages'].'</font><br/>'; /* this line is added by Zheng.Ping, for showing the expiration time */
		return $div;
	}
	
	/**
	*@Usage: ��ȡװ����Ʒ���ԡ�
	*@Param: $rs => װ����������
	*@Return: String
	*/
	public function getZbPlusAttrib($rs)
	{
		if (!is_array($rs) || empty($rs['pluseffect'])) return '';
		$div = '';
		global $_props;
		
		$str = explode(",", $rs['pluseffect']);
		
		for($i = 0; $i < count($str); $i++)
		{
			$sx = explode(":", $str[$i]);
			
			if (array_key_exists($sx[0], $_props['zb']) && $sx[0] != "kx")
			{
				/*$effect = $sx[1]." ".str_replace(array("openpet","mc","ac","openmap","hp","mp","hits","miss","kx","speed"),
				                                     $_props['zb'],
													 $sx[0]
													 );*/
													 
				$effect = $sx[1]." ".$_props['zb'][''.$sx[0].''];
				$div .= '<font color='.$this -> ep_plus. '>+' . $effect. '</font><br/>';
			}
			else if($sx[0] == "kx")
			{
				$nums = explode(":", $str[$i]);
				foreach($nums as $ns => $ars)
				{
					if(!empty($nums[1]) && $nums[1] == $nums[2] && $nums[2] ==$nums[3] && $nums[3] == $nums[4] && $nums[4] == $nums[5])
					{
						$div .= '<font color='.$this -> ep_plus.'>+'.$nums[1].'&nbsp;ȫ��</font><br/>';
						break;
					}
					else if($ars == 0 || !is_numeric($ars))
					{
						continue;
					}
					else
					{
						$a = $ns - 1;
						$div .= '<font color='.$this -> ep_plus.'>+&nbsp'.$ars.'&nbsp;'.$_props['wxd'][$a].'��</font><br/>';
					}
				}
			}
			else if (array_key_exists($sx[0], $_props['fjzb1']))
			{
				/*$effect = $sx[1]." ".str_replace(array("hprate","mprate","acrate","mcrate","hitsrate","missrate","speedrate"),
				                                     $_props['fjzb1'],
													 $sx[0]
													 );*/
													 
				$effect = $sx[1]." ". $_props['fjzb1'][''.$sx[0].''];
				$div .= '<font color='.$this -> ep_plus.'>+' . $effect . '</font><br/>';
			}
			else if (array_key_exists($sx[0], $_props['fjzb2']))
			{
				$effect = str_replace(array("dxsh","shjs","shft"), $_props['fjzb2'], $sx[0]);
				$div .= '<font color='.$this -> ep_plus.'>' . $effect . ' ' . $sx[1] . '</font><br/>';
			}
			else if ($sx[0] == "szmp")
			{
				$div .= '<font color='.$this -> ep_plus.'>�˺���'.$sx[1].'ת��ΪMP</font><br/>';
			}
			else if ($sx[0] == "sdmp")
			{
				$div .= '<font color='.$this -> ep_plus.'>�˺���'.$sx[1].'��MP����</font><br/>';
			}
			else if ($sx[0] == "addmoney")
			{
				$div .= '<font color='.$this -> ep_plus.'>ս��ʤ����ý������'.$sx[1].'��</font><br/>';
			}
			else if ($sx[0] == "hitshp")
			{
				$div .= '<font color='.$this -> ep_plus.'>͵ȡ�˺���'.$sx[1].'ת��Ϊ����</font><br/>';
			}
			else if ($sx[0] == "hitsmp")
			{
				$div .= '<font color='.$this -> ep_plus.'>͵ȡ�˺���'.$sx[1].'ת��Ϊħ��</font><br/>';
			}
			else if ($sx[0] == "time")
			{
				$div .= '<font color='.$this -> ep_plus.'>ս���ȴ�ʱ�����'.$sx[1].'��</font><br/>';
			}
			else if ($sx[0] == "skill")
			{
				$eff = explode(":", $str[$i]);
				$div .= '<font color='.$this -> ep_plus.'>ѧ�Ἴ��'.$eff[1].'LV'.$eff[2].'</font><br/>';
			}
			else if ($sx[0] == "killitem")
			{
				$eff = explode(":",$str[$i]);
				$div .= '<font color='.$this -> ep_plus.'>ɱ��������'.$eff[2].'�ļ��ʻ����Ʒ:'.$eff[1].'</font><br/>';
			}
		} // end foreach
		return $div;
	} // end function
	
	/**
	*@Usage: ��ȡװ����װ����Ч����
	*@Param: $rs => װ������Ϣ����.
	         $bid => 
	*@Return: String
	*/
	public function getZbSeriesAttrib($rs, $bid)
	{
		if (!is_array($rs) || empty($rs['series'])) return '';
	    $div = '';
		global $_props;
		$_props['zb'] = array('openpet'	=> 	'���һ������',
											  'mc'		=>	'����',
											  'openmap' =>	'����һ����ͼ',
											  'hp'		=>	'����',
											  'mp'		=>	'ħ��',
											  'hits'	=>	'����',
											  'miss'	=>	'����',
											  'kx'		=>	'����',
											  'speed'   =>  '�ٶ�',
											  'ac'		=>	'����'
											 );
		$series = explode(":", $rs['series']);
		$tz = explode("|", $series[1]);
		$num = count($tz);//����������װ���м���
		
		$list =str_replace('|', ',', $series[1]);
		if (is_array($tz) && $bid>0)
		{
			$names = $this -> m_db -> getRecords("SELECT pid
												    FROM userbag
												   WHERE pid in(". $list .") and uid = {$_SESSION['id']} and zbpets = {$bid}");
		}
		else $names = array();

		$num1=0;
		$petslist=array();
		if(is_array($names))
		{
			foreach ($names as $x => $y)
			{
				$petslist[]=$y['pid'];
				$num1++;
			}
		}

		if (!empty($num1))
		{
			$div .= '<font color='.$this -> ep_glod.'>'.$series[0].'('.$num1.'/'.$num.')</font><br/>';
		}
		else
		{
			$div .= '<font color='.$this -> ep_glod.'>'.$series[0].'(0/'.$num.')</font><br/>';
		}
		$memarr = unserialize($this->m_m->get('db_propsid'));
		foreach($tz as $k => $v)
		{
			$name = $memarr[$id];
			if (!is_array($name)) continue;
			
			if (in_array($v, $petslist))
			{
				$div .= '<font color='.$this -> ep_special.'>'.$name['name'].'</font><br/>';
			}
			else
			{
				$div .= '<font color='.$this -> ep_green.'>'.$name['name'].'</font><br/>';
			}
			unset($name);
		}
		
		if (!empty($rs['serieseffect']))
		{
			$serieseffect = explode(",",$rs['serieseffect']);
			for($i = 0;$i < count($serieseffect);$i++)
			{
				$j = $i + 1;
				if(!empty($serieseffect[$i]))
				{
					$effect = explode(":",$serieseffect[$i]);
					if(array_key_exists($effect[0],$_props['zb']))
					{
						$str = $_props['zb'][$effect[0]];
						if($j <= $num1)
						{
							$div .='<font color='.$this -> ep_special.'>('.$j.')��װ��+'.$effect[1]." ".$str.'</font><br/>';
						}
						else
						{
							$div .='<font color='.$this -> ep_green.'>('.$j.')��װ��+'.$effect[1]." ".$str.'</font><br/>';
						}

					}
					if(array_key_exists($effect[0],$_props['fjzb1']))
					{
						$str = str_replace(array("hprate","mprate","acrate","mcrate","hitsrate","missrate","speedrate"),$_props['fjzb1'],$effect[0]);
						if($j <= $num1)
						{
							$div .='<font color='.$this -> ep_special.'>('.$j.')��װ��+'.$effect[1]." ".$str.'</font><br/>';
						}
						else
						{
							$div .='<font color='.$this -> ep_green.'>('.$j.')��װ��+'.$effect[1]." ".$str.'</font><br/>';
						}
					}
					
					if(array_key_exists($effect[0],$_props['fjzb2']))
					{
						$str = str_replace(array("dxsh","shjs","shft"),$_props['fjzb2'],$effect[0]);

						if($j <= $num1)
						{
							$div .='<font color='.$this -> ep_special.'>('.$j.')��װ��'.$str." ".$effect[1].'</font><br/>';
						}
						else
						{
							$div .='<font color='.$this -> ep_green.'>('.$j.')��װ��'.$str." ".$effect[1].'</font><br/>';
						}

					}
					else if($effect[0] == "szmp")
					{

						if($j <= $num1)
						{
							$div .='<font color='.$this -> ep_special.'>('.$j.')��װ���˺���'.$effect[1].'ת��ΪMP</font><br/>';
						}
						else
						{
							$div .='<font color='.$this -> ep_green.'>('.$j.')��װ���˺���'.$effect[1].'ת��ΪMP</font><br/>';
						}

					}
					else if($effect[0] == "sdmp")
					{

						if($j <= $num1)
						{
							$div .='<font color='.$this -> ep_special.'>('.$j.')��װ���˺���'.$effect[1].'��MP����</font><br/>';
						}
						else
						{
							$div .='<font color='.$this -> ep_green.'>('.$j.')��װ���˺���'.$effect[1].'��MP����</font><br/>';
						}

					}
					else if($effect[0] == "addmoney")
					{

						if($j <= $num1)
						{
							$div .='<font color='.$this -> ep_special.'>('.$j.')��װ��ս��ʤ����ý������'.$effect[1].'��</font><br/>';
						}
						else
						{
							$div .='<font color='.$this -> ep_green.'>('.$j.')��װ��ս��ʤ����ý������'.$effect[1].'��</font><br/>';
						}

					}
					else if($effect[0] == "hitshp")
					{

						if($j <= $num1)
						{
							$div .='<font color='.$this -> ep_special.'>('.$j.')��װ��͵ȡ�˺���'.$effect[1].'ת��Ϊ����</font><br/>';
						}
						else
						{
							$div .='<font color='.$this -> ep_green.'>('.$j.')��װ��͵ȡ�˺���'.$effect[1].'ת��Ϊ����</font><br/>';
						}

					}
					else if($effect[0] == "hitsmp")
					{

						if($j <= $num1)
						{
							$div .='<font color='.$this -> ep_special.'>('.$j.')��װ��͵ȡ�˺���'.$effect[1].'ת��Ϊħ��</font><br/>';
						}
						else
						{
							$div .='<font color='.$this -> ep_green.'>('.$j.')��װ��͵ȡ�˺���'.$effect[1].'ת��Ϊħ��</font><br/>';
						}

					}
					else if($effect[0] == "time")
					{

						if($j <= $num1)
						{
							$div .='<font color='.$this -> ep_special.'>('.$j.')��װ��ս���ȴ�ʱ�����'.$effect[1].'��</font><br/>';
						}
						else
						{
							$div .='<font color='.$this -> ep_green.'>('.$j.')��װ��ս���ȴ�ʱ�����'.$effect[1].'��</font><br/>';
						}

					}
					else if($effect[0] == "skill")
					{	
						$eff = explode(":",$serieseffect[$i]);

						if($j <= $num1)
						{
							$div .='<font color='.$this -> ep_special.'>('.$j.')��װ��ѧ�Ἴ��'.$eff[1].'LV'.$eff[2].'</font><br/>';
						}
						else
						{
							$div .='<font color='.$this -> ep_green.'>('.$j.')��װ��ѧ�Ἴ��'.$eff[1].'LV'.$eff[2].'</font><br/>';
						}

					}
					else if($effect[0] == "killitem")
					{
						$eff = explode(":",$serieseffect[$i]);

						if($j <= $num1)
						{
							$div .='<font color='.$this -> ep_special.'>('.$j.')��װ��ɱ��������'.$eff[2].'�ļ��ʻ����Ʒ:'.$eff[1].'</font><br/>';
						}
						else
						{
							$div .='<font color='.$this -> ep_green.'>('.$j.')��װ��ɱ��������'.$eff[2].'�ļ��ʻ����Ʒ:'.$eff[1].'</font><br/>';
						}

					}
				}
			}	//	end for
		}
		return $div;
	} // end function
	
	/**
	*@Usage: ��ȡװ����������
	*@Param: $rs => ������������
	*@Return: String
	*/
	public function getZbCardAttrib($rs)
	{
		$div = '';
		if (empty($rs['plusnum']))
		{
			$div .= '<font color='.$this -> ep_special.'>�޿���</font><br/>';
		}
		else
		{
			if( isset($rs['F_item_hole_info']) && !empty($rs['F_item_hole_info']) )
			{
				$hole_info = explode(',',$rs['F_item_hole_info']);
				$hole_has_used = count($hole_info);
				$div .= '<font color='.$this -> ep_special.'>��������'.$hole_has_used.'/'.$rs['plusnum'].'</font><br/>';
				foreach( $hole_info as $info )
				{
					$mid_arr = explode(':',$info);
					switch ($mid_arr[0])
					{
						case 'ac' :
						{
							$div .= '<font color="red">��ʯЧ�������ӹ���'.$mid_arr[1].'</font><br/>';
							break;
						}
						case 'mc' :
						{
							$div .= '<font color="red">��ʯЧ�������ӷ���'.$mid_arr[1].'</font><br/>';
							break;
						}
						case 'hits' :
						{
							$div .= '<font color="red">��ʯЧ������������'.$mid_arr[1].'</font><br/>';
							break;
						}
						case 'miss' :
						{
							$div .= '<font color="red">��ʯЧ������������'.$mid_arr[1].'</font><br/>';
							break;
						}
						case 'hp' :
						{
							$div .= '<font color="red">��ʯЧ��������HP����'.$mid_arr[1].'</font><br/>';	
							break;
						}
						case 'mp' :
						{
							$div .= '<font color="red">��ʯЧ��������MP����'.$mid_arr[1].'</font><br/>';	
							break;
						}
						case 'speed' :
						{
							$div .= '<font color="red">��ʯЧ���������ٶ�'.$mid_arr[1].'</font><br/>';	
							break;
						}
						case 'sdmp' :
						{
							$div .= '<font color="red">��ʯЧ�������ܵ��˺���'.$mid_arr[1].'��MP����</font><br/>';	
							break;
						}
						case 'szmp' :
						{
							$div .= '<font color="red">��ʯЧ�������ܵ��˺���'.$mid_arr[1].'ת��ΪMP</font><br/>';	
							break;
						}
						case 'hitshp' :
						{
							$div .= '<font color="red">��ʯЧ����������ȡ�˺���'.$mid_arr[1].'ת��Ϊ����HP</font><br/>';	
							break;
						}
						case 'hitsmp' :
						{
							$div .= '<font color="red">��ʯЧ����������ȡ�˺���'.$mid_arr[1].'ת��Ϊ����MP</font><br/>';
							break;
						}
						case 'dxsh' :
						{
							$div .= '<font color="red">��ʯЧ�����˺�����'.$mid_arr[1].'</font><br/>';	
							break;
						}
						case 'shjs':
						{
							$div .= '<font color="red">��ʯЧ�����Ե�����ɵ��˺�����'.$mid_arr[1].'</font><br/>';	
							break;
						}
						case 'crit':
						{
							$div .= '<font color="red">��ʯЧ��������һ��������'.$mid_arr[1].'</font><br/>';	
							break;
						}
					}
				}
			}
			else
			{
				$div .= '<font color='.$this -> ep_special.'>��������0/'.$rs['plusnum'].'</font><br/>';
			}
		}
		return $div;
	}	
	
	/**
	*@Usage: ��ȡװ����������
	*@Param: $rs => ������������
	*@Return: String
	*/
	public function getZbBaseAttrib($rs,$sign)
	{
	    $div = '';
		global $_props;
		if (!empty($rs['effect']))
		{
			$str = explode(":", $rs['effect']);
			if ($str[0] == "kx")
			{
				$num = explode(",", $str[1]);
				foreach($num as $n => $ar)
				{
					if(!empty($num[1]) && $num[1] == $num[2] && $num[2] ==$num[3] && $num[3] == $num[4] && $num[4] == $num[5])
					{
						$div .= '<font color='.$this -> ep_base.'>+&nbsp'.$num[1].'&nbsp;ȫ��</font><br/>';
						break;
					}
					else if($ar != 0)
					{
						$div .= '<font color='.$this -> ep_base.'>+&nbsp'.$ar.'&nbsp;'.$_props['wxd'][$n].'��</font><br/>';
					}
				}
			}
			else
			{
				/*$effect = str_replace(array("openpet","mc","ac","openmap","hp","mp","hits","miss","kx","speed"),
				                      $_props['zb'],
									  $str[0]);*/
				$effect = $_props['zb'][$str[0]];
				$ef = "";
				if($sign != 0)
				{
					$arr = $this -> tms($rs['pid']);
					if($arr['tms'] == 1)
					{
						$b = explode(",",$arr['plus_tms_eft']);
						$ef = '<font color= red>+'.$b[1].'</font>';
					}
				}
				$div .= '<font color='.$this -> ep_base.' class="line">+'.$str[1].' '.$effect." ".$ef.'</font><br/>';
			}
		}
		return $div;
	} // end function



/**
	*@Usage: ��ȡװ��ǿ���������
	*@Param: $rs => ����
	*@Return: String
	*/
	public function tms($id)
	{
		$sql = "SELECT plus_tms_eft FROM userbag WHERE id=$id";
		$rs = $this-> m_db -> getOneRecord($sql);
		if(is_array($rs))
		{
			if(!empty($rs['plus_tms_eft']))
			{
				$rs['tms'] = 1;
			}
			else
			{
				$rs['tms'] = 0;
			}
		}
		else
		{
			$rs['tms'] = 0;
		}
		return $rs;
	}//end tms

    /**
     * set the expiration period
     *
     * @param integer $expiration
     * @return none
     * @author Zheng.Ping
     */
    public function set_expiration($expiration)
    {
        $this->expiration = intval($expiration);
    }

	
}// end class
?>
