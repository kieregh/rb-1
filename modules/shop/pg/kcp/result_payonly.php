<?php
extract($_GET);
extract($_POST);
extract($_SERVER);
//주문번호 : $ordr_idxx

// 가상계좌 정보
if($bankname){
   $bankname=iconv('EUC-KR','UTF-8',$bankname);
	$depositor=iconv('EUC-KR','UTF-8',$depositor);
	$year='(유효기간 : '.substr($va_date, 0,4).'년 ';
	$month=substr($va_date, 4,2).'월 ';
	$day=substr($va_date, 6,2).'일)';
	$va_date_arr=$year.$month.$day;
	$bank=$bankname.' '.$account.' '.$depositor.' '.$va_date_arr;	
}
?>

<meta http-equiv="content-type" content="text/html; charset=utf-8" />

<?php if($res_cd == "0000"):?>

<script type="text/javascript">
//parent.document.orderform.tid.value = "<?php echo $tno?>";
//parent.document.orderform.submit();
parent.document.orderTidform.tid.value = "<?php echo $trade_no?>";
parent.document.orderTidform.orderid.value = "<?php echo $ordr_idxx?>";
parent.document.orderTidform.bank.value = "<?php echo $bank?>";
parent.document.orderTidform.submit();
</script>

<?endif?>
