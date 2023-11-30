<?php

require_once('../config/config.game.php');
require_once('../config/config.alipay.php');

$receipt = $_REQUEST['receipt'];
$product_id = $_REQUEST['product_id'];
$produts = $backObj['apple'];
//$user_id = intval($_REQUEST['user_id']);
$user_id = intval($_SESSION['id']);
if($user_id < 1){
	echo "not_find user_id";
	die();
}

//log_result("user_id={$user_id}");
//log_result("product_id={$product_id}");
//log_result("receipt={$receipt}");

//������֤
purchaseVerification($user_id,$receipt,$product_id,$produts);

//֧����֤
function purchaseVerification($user_id,$receipt,$product_id,$produts){
	global $_pm;
	$productArr = productFromat($produts);
	
	if(!isset($productArr[$product_id])){
		echo "not find proudct";
		die();
	}
	//д�����¼��־
	$buy_time = time();
	$sql = "INSERT INTO t_shop_log(uid,product_id,buy_time,receipt)
			VALUES('{$user_id}','{$product_id}','{$buy_time}','{$receipt}')";
	$_pm['mysql']->query($sql);
	$logId = $_pm['mysql']->last_id();
	$VerifResult = getReceiptData($receipt,false);//true��ʽ��ַ false���Ե�ַ
	if(isset($VerifResult['err'])){
		$VerifResult = getReceiptData($receipt,false);//true��ʽ��ַ false���Ե�ַ
	}
	if(isset($VerifResult['err'])){
		$sql = "UPDATE t_shop_log SET error_detail='{$VerifResult['err']}' where id='{$logId}'";
		$_pm['mysql']->query($sql);
		echo "pay_error:".$VerifResult['err'];
		die();
	}
	if($product_id != $VerifResult['product_id']){//��ֹ�ƽ�����ر�ӵ��ж�
		echo "pay_error,waigua";
		die();
	}
	$transaction_id = $VerifResult['transaction_id'];
	$sql = "SELECT id FROM t_shop_log WHERE transaction_id='{$transaction_id}' and trade=1";
	$rs = $_pm['mysql']->getOneRecord($sql);
	if($rs){
		$sql = "UPDATE t_shop_log SET error_detail='repeat_submitted' WHERE id='{$logId}'";
		$_pm['mysql']->query($sql);
		echo "pay_ok1";
		die();
	}
	
	//����ֵ��Ԫ��
	$price = $productArr[$product_id]['price'];
	$buy_number = $price * 30;
	$out_trade_no = $VerifResult['transaction_id'];
	$subject = "{$productArr[$product_id]['price']}RMB����{$buy_number}Ԫ��";
	$sql = "INSERT INTO yb (payname,paytime,paymoney,getyb,orderid) VALUES ('{$subject}','{$buy_time}','{$price}',{$buy_number},'{$out_trade_no}')";
	$_pm['mysql']->query($sql);
	$sql = "UPDATE player SET yb = yb + {$buy_number} WHERE id = {$user_id}";
	$_pm['mysql']->query($sql);
	//���¹�����־
	$sql = "UPDATE t_shop_log SET trade=1,item_id='{$product_id}',transaction_id='{$out_trade_no}',product_id='{$product_id}',buy_number='{$buy_number}' WHERE id='{$logId}'";
	$_pm['mysql']->query($sql);

	echo "pay_ok";
	
}

/**
 ������֯��Ʒ�������
*/
function productFromat($produts){
	$productArr = array();
	if($produts){
		foreach($produts as $val){
			$productArr[$val['name']] = $val;
		}
	}
	return $productArr;
}

//ƻ��֧����֤
function getReceiptData($receipt, $isSandbox = true){
	
		if($isSandbox) {
			$endpoint = 'https://buy.itunes.apple.com/verifyReceipt';
        } else {   
        	$endpoint = 'https://sandbox.itunes.apple.com/verifyReceipt';
        } 

        $postData = json_encode(array('receipt-data' => $receipt));
        //$ch = curl_init();
        $ch = curl_init($endpoint);   
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);   
        curl_setopt($ch, CURLOPT_POST, true);   
        curl_setopt($ch, CURLOPT_POSTFIELDS, $postData);

        $response = curl_exec($ch);   
        $errno    = curl_errno($ch);   
        $errmsg   = curl_error($ch);   
        curl_close($ch);
        if ($errno != 0) {
        	return array('err'=>'network error');
            
        }
        $data = json_decode($response);   
       
        if (!is_object($data)) {
        	return array('err'=>'Invalid response data');
              
        }   
		
        return array( 
        	'sucess'       	 =>  1,  
            'quantity'       =>  $data->receipt->quantity,//������Ʒ������   
            'product_id'     =>  $data->receipt->product_id, //��Ʒ�ı�ʶ  
            'transaction_id' =>  $data->receipt->transaction_id, //���׵ı�ʶ  
            'purchase_date'  =>  $data->receipt->purchase_date, //���׵�����  
            'app_item_id'    =>  $data->receipt->app_item_id, //Store������ʶ������ַ���  
            'bid'            =>  $data->receipt->bid, //iPhone�����bundle��ʶ   
            'bvrs'           =>  $data->receipt->bvrs // iPhone����İ汾��
        );
}


function log_result($word) {
    $fp = fopen("paylog.txt","a");
    flock($fp, LOCK_EX) ;
    fwrite($fp,"\n".$word."\n");
    flock($fp, LOCK_UN);
    fclose($fp);
}	