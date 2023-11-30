<meta http-equiv="Content-Type" content="text/html; charset=gb2312" />
<style>
body{
font-size:12px}
.STYLE1 {color: #FFFFFF}
td input{
width:50px;
padding:0;
margin:0}
</style>
<?php
require_once(dirname(dirname(__FILE__))."/config/config.game.php");
$managerdb =$_mysql;
require_once(dirname(dirname(__FILE__))."/inc/func.php");
$db = &$_pm['mysql'];
$dbServersA = getConfig($db);
print_r($dbServersA);exit;



	error_reporting(7);
	//$_mysql = $managerdb;
	//$db = new mysql();
	$stime = ($_REQUEST['stime']);
	$etime = ($_REQUEST['etime']);
	if(empty($stime) || empty($etime))
	{
		$where = ' vary=6';
	}
	else{
		$where = ' vary=6 and ptime>='.strtotime($stime).' and ptime<='.strtotime($etime);
		$_SESSION['qlogwhere']  = $where;
	}
if(isset($_SESSION['qlogwhere'])){
	$page = intval($_GET['p']);
	$nnp = 50;
	if($page<0) $page=0;
	$sql = '
			SELECT 
				id,ptime,seller,buyer,
				pnote,vary
			FROM 
				`gamelog`
			where 
			'.$_SESSION['qlogwhere'].'' 
			;
	$data = $_pm['mysql']->getRecords($sql);
	
	if(mysql_error()){
		echo mysql_error().'$sql='.$sql.'<hr>';
	}
}
?>
<?php echo $msg; 
if(!isset($_GET['xls'])){
	choose_date();
}if(!isset($_GET['xls'])){?><br />

<?php
}
?>

<table width="1100" border="1" cellspacing="0" cellpadding="0">
  <tr>
    <td align="center" bgcolor="#003366"><span class="STYLE1">id</span></td>
    <td align="center" bgcolor="#003366"><span class="STYLE1">时间</span></td>
    <td align="center" bgcolor="#003366"><span class="STYLE1">seller</span></td>
    <td align="center" bgcolor="#003366"><span class="STYLE1">buyer</span></td>
    <td align="center" bgcolor="#003366"><span class="STYLE1">说明</span></td>
    <td align="center" bgcolor="#003366"><span class="STYLE1">类型</span></td>
  </tr>
  <?php if ($data){
  $ct=0;
  foreach($data as $rs){
  $ct++;
   ?>
  <tr>
    <td align="center"><?php echo $rs['id']; ?></td>
    <td align="center"><?php echo date("m/d H:i",$rs['ptime']); ?></td>
    <td align="center"><?php echo $rs['seller']; ?></td>
    <td align="center"><?php echo $rs['buyer']; ?></td>
    <td align="center">
	<textarea cols="60" rows="4"><?php echo $rs['pnote']; ?></textarea>
	</td>
    <td align="center"><?php echo $rs['vary']; ?></td>
 </tr>
  <?php }
  ?>
  <tr>
		<td colspan="6">
		<?php if ($page>0){ ?><a href="?cmd=<?php echo $_GET['cmd'] ?>&p=<?php echo $page-1 ?>">上页</a><?php }?>
         <?php echo $page+1 ?> 
		<?php if ($nnp==$ct){ ?><a href="?cmd=<?php echo $_GET['cmd'] ?>&p=<?php echo $page+1 ?>">下页</a><?php }?>
		</td>
	</tr>
  <?php 
 
	 }else{
  	?>
	<tr>
		<td colspan="6">没有记录。<font color="#ffffff"><?php echo $sql; ?></font></td>
	</tr>
<?php 
  }?>
</table>
<script type="text/javascript">
function loads()
{
	window.location=window.location.href+'&xls=yes&stime=<?=$stime?>&etime=<?=$etime?>';
}
</script>
<?php
function choose_date()
{
//$now = time();
global $_REQUEST;

//$yesday=mktime(0,0,0,date("m",time()),date("d",time())-1,date("Y",time()));
//$today=mktime(0,0,0,date("m",time()),date("d",time()),date("Y",time()));
echo '<table width="100%" border="1" cellspacing="0" cellpadding="0">
  <tr>
    <td height="49">
	<form method="post" action="?cmd=vip">
		自由查询<br />
		类型：

			
			vip任务查询
			
		<br />
		<input name="stime" type="text" value="'.(isset($_REQUEST['stime'])?date("Y-m-d H:i:s",strtotime($_REQUEST['stime'])):('2009-05-01 00:00:00')).'" style="width:140px" />-<input name="etime" type="text" value="'.(isset($_REQUEST['etime'])?date("Y-m-d H:i:s",strtotime($_REQUEST['etime'])):'2009-05-31 00:00:00').'" style="width:140px" />
		<input type="hidden" value="yes" name="action" />
		<input type="submit" value="查询" style="width:100px" />
		<span style="cursor:pointer" onclick="loads()"><font color="#FF0000">点击下载</font></span>
	</form>
 	</td>
  </tr>   
</table><b>说明：需要点击“查询”后输入的时间才会生效</b>';
}
?>