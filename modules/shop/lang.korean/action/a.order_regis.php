<?php
if(!defined('__KIMS__')) exit;

include_once $g['dir_module'].'var/var.php';
$g['cart_file'] = $g['dir_module'].'tmp/cart/'.$_SESSION['cartid'].'.txt';

$orderid	= $_SESSION['cartid'];
$orderstep	= $tid &&$ckind!=4?2 : 1;
$mbruid		= $my['uid'];
$o_tel1		= $o_tel11 && $o_tel12 && $o_tel13 ? $o_tel11.'-'.$o_tel12.'-'.$o_tel13 : '';
$o_tel2		= $o_tel21 && $o_tel22 && $o_tel23 ? $o_tel21.'-'.$o_tel22.'-'.$o_tel23 : '';
$o_zip		= $o_zip1.$o_zip2;
$o_addr2	= trim($o_addr2);
$r_tel1		= $r_tel11 && $r_tel12 && $r_tel13 ? $r_tel11.'-'.$r_tel12.'-'.$r_tel13 : '';
$r_tel2		= $r_tel21 && $r_tel22 && $r_tel23 ? $r_tel21.'-'.$r_tel22.'-'.$r_tel23 : '';
$r_zip		= $r_zip1.$r_zip2;
$r_addr2	= trim($r_addr2);
$b_name		= trim($b_name);
$taxtype	= $orderstep == 2 ? 0 : $taxtype;
$taxinfo	= $orderstep == 2 ? 0 : ${'taxnum'.$taxtype};
$d_regis	= $date['totime'];
$d_bank		= $orderstep == 2 ? $d_regis : '';
$d_tack		= '';
$usecoupon	= $uidcoupon && $usecoupon ? filterstr($usecoupon) : 0;
$usepoint	= filterstr($usepoint);
$is_mobile	= $g['mobile'] ? 1 : 0;

if ($orderstep == 2)
{
	$o_name		= getUTFtoUTF($o_name)  == $o_name  ? $o_name  : getKRtoUTF($o_name);
	$o_addr1	= getUTFtoUTF($o_addr1) == $o_addr1 ? $o_addr1 : getKRtoUTF($o_addr1);
	$o_addr2	= getUTFtoUTF($o_addr2) == $o_addr2 ? $o_addr2 : getKRtoUTF($o_addr2);
	$r_name		= getUTFtoUTF($r_name)  == $r_name  ? $r_name  : getKRtoUTF($r_name);
	$r_addr1	= getUTFtoUTF($r_addr1) == $r_addr1 ? $r_addr1 : getKRtoUTF($r_addr1);
	$r_addr2	= getUTFtoUTF($r_addr2) == $r_addr2 ? $r_addr2 : getKRtoUTF($r_addr2);
	$bank		= getUTFtoUTF($bank)    == $bank    ? $bank    : getKRtoUTF($bank);
	$b_name		= getUTFtoUTF($b_name)  == $b_name  ? $b_name  : getKRtoUTF($b_name);
	$msg		= getUTFtoUTF($msg)     == $msg     ? $msg     : getKRtoUTF($msg);
}
if ($escr)
{
	$bank = $escrbank ? $escrbank : $bank;
}

// 후주문, 선결제 가 아니고 && 결제수단이 가상계좌인 경우 가상계좌 데이타를 bank 에 입력한다. 
if($d['shop']['pay_order']!=2 && $ckind==4) $bank=$virt_bank;

$QKEY = 'orderid,orderstep,price,shalin,mhalin,chalin,tack,tack_after,tack_comp,tack_number,givepoint,usepoint,mbruid,';
$QKEY.= 'o_name,o_email,o_tel1,o_tel2,o_zip,o_addr1,o_addr2,';
$QKEY.= 'r_name,r_tel1,r_tel2,r_zip,r_addr1,r_addr2,';
$QKEY.= 'ckind,bank,b_name,escr,tid,buyfix,rcvpoint,memo,msg,taxtype,taxinfo,d_regis,d_bank,d_tack,is_mobile';
$QVAL = "'$orderid','$orderstep','0','0','0','0','0','0','','','0','0','$mbruid',";
$QVAL.= "'$o_name','$o_email','$o_tel1','$o_tel2','$o_zip','$o_addr1','$o_addr2',";
$QVAL.= "'$r_name','$r_tel1','$r_tel2','$r_zip','$r_addr1','$r_addr2',";
$QVAL.= "'$ckind','$bank','$b_name','$escr','$tid','0','0','$memo','$msg','$taxtype','$taxinfo','$d_regis','$d_bank','$d_tack','$is_mobile'";
getDbInsert($table[$m.'order'],$QKEY,$QVAL);
$uidmax = getDbCnt($table[$m.'order'],'max(uid)','');	

$is_free=false;
$_TACK=array();
$sprice=$shalin=$mhalin=$tprice=$gpoint=0;
$i=0;
$cartProductName = '';
$cartarr=is_file($g['cart_file'])?file($g['cart_file']):array();
foreach($cartarr as $val)
{
	$C=explode('<s>',$val);
	$O=explode('|',$C[2]);
	$R=getUidData($table[$m.'product'],$C[0]);
	if(!$R['uid'] || $R['display'] || $R['price_x'] || ($R['stock'] && $C[1] > $R['stock_num']))continue;
	$isPumjeol=getPumjeol($R);
	$oprice=getRound($R['price'],'down',$d['shop']['jeolsa']);
	$_shprice=getRound(getSHalin($R),'down',$d['shop']['jeolsa']);
	$_mhprice=getRound(getMHalin($R),'down',$d['shop']['jeolsa']);
	$_smhprice=($_shprice+$_mhprice)*$C[1];
	if($R['is_free'])$is_free=true;
	$cartProductName = strip_tags($R['name']);

	$opprice=$oppoint=0;
	for($j=0;$j<count($O);$j++)
	{
		if(trim($O[$j])=='')continue;
		$_O=explode('^',$O[$j]);
		$opprice+=getRound($_O[2],'down',$d['shop']['jeolsa']);
		$oppoint+=getRound($_O[4],'down',$d['shop']['jeolsa']);
	}

	$i++;

	$sprice+=($oprice+$opprice)*$C[1];
	$gpoint+=($R['point']+$oppoint)*$C[1];
	$shalin+=$_shprice*$C[1];
	$mhalin+=$_mhprice*$C[1];
	if($d['shop']['bundletack']){$_TACK[$R['vendor']]['price']+=($oprice+$opprice-$_smhprice)*$C[1];$_TACK[$R['vendor']]['free']+=$R['is_free'];}


	$QKEY1 = 'parent,mbruid,orderstep,goodsuid,goodsname,options,';
	$QKEY1.= 'buynum,price,oprice,shalin,mhalin,point,d_regis,d_bank,d_tack';
	$QVAL1 = "'".$uidmax."','".$mbruid."','".$orderstep."','".$R['uid']."','".$R['name']."','".$C[2]."',";
	$QVAL1.= "'".$C[1]."','".$oprice."','".$opprice."','".$_shprice."','".$_mhprice."','".$R['point']."','$d_regis','$d_bank','$d_tack'";
	getDbInsert($table[$m.'ordergoods'],$QKEY1,$QVAL1);

	if (!$R['stock_type1']&&!$R['stock_type2']&&$R['stock_num']>$C[1])
	{
		getDbUpdate($table[$m.'product'],'stock_num=stock_num-'.$C[1],'uid='.$R['uid']);
	}
}

if (!$i)
{
	$_SESSION['cartid'] = '';
	getLink('./?r='.$r.'&m='.$m,'','장바구니 데이터가 손상되었거나 정상적인 접근이 아닙니다.','');
}

$hprice = $shalin+$mhalin;

if ($d['shop']['bundletack'])
{
	foreach($_TACK as $_tkey)
	{
		$tprice1= getTprice($_tkey['price'],$d['shop']);
		$tprice2+= $_tkey['free'] || $tprice1 < 0 ? 0 : $tprice1;
	}
}
else {
	$tprice1= getTprice($sprice-$hprice,$d['shop']);
	$tprice2= $is_free || $tprice1 < 0 ? 0 : $tprice1;
}

if(!$is_free && !$tprice2 && $d['shop']['tacktype']==3)
{
	$tack_after = 1;
}
if (!$mbruid)
{
	$gpoint = 0;
	$usepoint = 0;
	$usecoupon = 0;
	$mhalin = 0;
}

$QUE3 = "update ".$table[$m.'order']." set 
price='".$sprice."',
shalin='".$shalin."',
mhalin='".$mhalin."',
chalin='".$usecoupon."',
tack='".($tprice2+$price_stack)."',
tack_after='".$tack_after."',
givepoint='".$gpoint."',
usepoint='".$usepoint."',
rcvpoint='".($mbruid && $orderinfo['pointgive']==2 && $orderinfo['pointstep']==2 && $orderstep == 2 ? 1 : 0)."' where orderid='".$orderid."'";
db_query($QUE3,$DB_CONNECT);


if ($taxtype)
{
	$d['shop']['price'] = (($sprice + ($d['shop']['tack'] ? $tprice2+$price_stack : 0)) - ($shalin+$mhalin+$chalin+($d['shop']['point'] ? $usepoint : 0)));
	if ($d['shop']['tacktype'] == 1)
	{
		$taxprice = round($d['shop']['price'] / $d['shop']['per']);
		$taxfee = $d['shop']['price'] - $taxprice;
	}
	else {
		$taxprice = $d['shop']['price'];
		$taxfee = 0;
	}
	if ($taxtype == 1)
	{
		$jumin = substr($taxinfo,0,1) == '0' ? '' : $taxinfo;
		$tel = $jumin ? '' : $taxinfo;
		$QUE6 = "insert into ".$table[$m.'ordertax1']." 
		(orderid,name,product,price,fee,jumin,tel,d_regis,d_rcv,auth,rcode)
		values
		('".$orderid."','".$o_name."','".$cartProductName."','".$taxprice."','".$taxfee."','".$jumin."','".$tel."','".$d_regis."','','0','')";
		db_query($QUE6,$DB_CONNECT);
	}
	if ($taxtype == 2)
	{
		$QUE7 = "insert into ".$table[$m.'ordertax2']." 
		(orderid,product,price,fee,name,ceo,num,upte,jongmok,zip,addr1,addr2,d_regis,d_rcv,auth,rcode)
		values
		('".$orderid."','".$cartProductName."','".$taxprice."','".$taxfee."','".$c_name."','".$c_ceo."','".$c_num."','".$c_upte."','".$c_jongmok."','".$c_zip."','".$c_addr1."','".$c_addr2."','".$d_regis."','','0','')";
		db_query($QUE7,$DB_CONNECT);
	}
}


if ($mbruid)
{
	if ($usepoint)
	{
		$QUE4 = "insert into ".$table['s_cash']." 
		(my_mbruid,by_mbruid,price,content,d_regis)
		values
		('$mbruid','0','-$usepoint','상품구입[".$cartProductName."]시 사용하셨습니다.','$d_regis')";
		db_query($QUE4, $DB_CONNECT);
		db_query("update ".$table['s_mbrdata']." set cash=cash-".$usepoint." where memberuid=".$mbruid,$DB_CONNECT);
	}
	if ($d['shop']['pointgive']==2 && $d['shop']['pointstep']==2 && $orderstep==2)
	{
		$QUE5 = "insert into ".$table['s_cash']." 
		(my_mbruid,by_mbruid,price,content,d_regis)
		values
		('$mbruid','0','$gpoint','상품구입[".$cartProductName."]에대한 적립금입니다.','$d_regis')";
		db_query($QUE5, $DB_CONNECT);
		db_query("update ".$table['s_mbrdata']." set cash=cash+".$gpoint." where memberuid=".$mbruid,$DB_CONNECT);
	}
	if ($usecoupon && $parentcoupon)
	{
		$CPD=db_fetch_array(db_query("select * from ".$table[$m.'couponlist']." where uid='".$parentcoupon."'",$DB_CONNECT));
		$upnotmember = str_replace('['.$mbruid.']','',$CPD['notmember']);
		$upusemember = $CPD['usemember'].'['.$mbruid.']';
		db_query("update ".$table[$m.'couponlist']." set usenum=usenum+1,notmember='".$upnotmember."',usemember='".$upusemember."' where uid='".$parentcoupon."'",$DB_CONNECT);
		db_query("update ".$table[$m.'coupondata']." set orderid='".$orderid."',goodsname='".$cartProductName."',halin='".$usecoupon."',d_use='".$d_regis."' where uid='".$uidcoupon."'",$DB_CONNECT);
	}
}


if ($orderstep == 1 && $d['shop']['send_order_1'] && $d['shop']['send_order_email'])
{
	include_once $g['path_core'].'function/email.func.php';

	$content = '상품명 : '.$cartProductName.'<br />';
	$content.= '주문인 : '.$o_name.'<br />';
	$content.= '연락처 : '.$o_tel1.'/'.$o_tel2.'<br />';
	$content.= '결제금액 : '.number_format(($sprice+$tprice2+$price_stack)-($shalin+$mhalin+$chalin+$usepoint)).'원<br />';

	getSendMail($d['shop']['send_order_email'], $o_email.'|'.$o_name, '쇼핑몰에서 주문이 접수되었습니다.', $content, 'HTML');

	if (!$my['uid'])
	{
		getSendMail($o_email.'|'.$o_name,$d['shop']['send_order_email'].'|'.$_HS['name'], '['.$_HS['name'].']비회원 주문번호 안내입니다.', $content.'주문번호 : '.$orderid, 'HTML');
	}
}

unlink($cartfile);
$_SESSION['cartid'] = '';

$tmpfolder = $g['dir_module'].'tmp/cart/';
$opendir = opendir($tmpfolder);
while(false !== ($file = readdir($opendir)))
{	
	if($file == '.' || $file == '..') continue;

	$dtime = date('YmdHis' , mktime(substr($time[0],8,2)+3,substr($time[0],10,2),substr($time[0],12,2),substr($time[0],4,2),substr($time[0],6,2),substr($time[0],0,4)));
	if($date['totime'] > $dtime) 
	{
		unlink($tmpfolder.$file);
	}
}
closedir($opendir);

if($d['shop']['pay_order']==2) $_mod='order_ok&_orderid='.$orderid;
else $_mod='myorder';

if ($my['uid'])
{
	getLink($g['s'].'/?r='.$r.'&m='.$m.'&mod='.$_mod,'parent.','','');
}
else {	
	getLink($g['s'].'/?r='.$r.'&m='.$m.'&mod='.$_mod.'&_orderid='.$orderid,'parent.','비회원 주문조회를 위해서는 주문번호를 메모해 두세요.','');
}

?>
