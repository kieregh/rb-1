<?php
if(!defined('__KIMS__')) exit;

getDbUpdate($table[$m.'order'],"tid='".$tid."'","orderid='".$orderid."'");

$O=getDbData($table[$m.'order'],"orderid='".$orderid."'",'*');
$ckind=$O['ckind'];

// 가상계좌 기준 구분처리  
if($ckind=='4'){
	if($bank)  getDbUpdate($table[$m.'order'],"bank='".$bank."'","orderid='".$orderid."'");
}else{
	getDbUpdate($table[$m.'order'],"orderstep='2',d_bank='".$date['totime']."'","orderid='".$orderid."'");	
   getDbUpdate($table[$m.'ordergoods'],"orderstep='2',d_bank='".$date['totime']."'",'parent='.$O['uid']);
}

if ($my['uid'])
{
	$_link=$g['s'].'/?r='.$r.'&m='.$m.'&mod=myorder';
   $msg='';
}
else {	
	$_link=$g['s'].'/?r='.$r.'&m='.$m.'&mod=myorder&_orderid='.$orderid;
	$msg='비회원 주문조회를 위해서는 주문번호를 메모해 두세요.';
}
getLink($_link,'parent.',$msg,'');
?>
