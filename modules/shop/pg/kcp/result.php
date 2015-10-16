<?php
extract($_GET);
extract($_POST);
extract($_SERVER);
//주문번호 : $ordr_idxx
?>

<meta http-equiv="content-type" content="text/html; charset=utf-8" />

<?php if($res_cd == "0000"):?>

<script type="text/javascript">
alert('\n결제가 정상적으로 처리되었습니다.		\n');
parent.document.orderform.tid.value = "<?php echo $tno?>";
parent.document.orderform.submit();
</script>

<?else:?>

<script type="text/javascript">
alert('\n결제가 정상적으로 처리 되지 않았습니다.\n\n[ errCode : <?php echo strip_tags($res_msg)?>(<?php echo $res_cd?>) ]	 \n\n상점으로 문의 바랍니다.		\n');
</script>

<?endif?>
