<?php
if(!defined('__KIMS__')) exit;

checkAdmin(0);
include_once $g['dir_module'].'var/var.php';

if ($_a == 'order_action')
{
	$j = 0;
	foreach($order_members as $val)
	{
		$O = getDbData($table[$m.'order'],"uid='".$val."'",'*');
		$T = db_fetch_array(db_query("select * from ".$table[$m.'ordergoods']." where parent='".$O['uid']."' order by uid asc limit 0,1",$DB_CONNECT));
		$cartProductName = $T['goodsname'];

		//송장번호 등록/수정
		if ($act == 'tack')
		{
			getDbUpdate($table[$m.'order'],"tack_comp='".$tack_comp."',tack_number='".${'tack_'.$O['uid']}."'",'uid='.$O['uid']);
		}
		//주문데이터 삭제
		else if ($act == 'delete')
		{
			$PRODUCT_DATA = db_query("select * from ".$table[$m.'ordergoods']." where parent='".$O['uid']."'",$DB_CONNECT);
			while($G=db_fetch_array($PRODUCT_DATA))
			{
				getDbDelete($table[$m.'ordergoods'],'uid='.$G['uid']);
				
				//재고수량복원
				if ($d['shop']['cancel_stock1']==1)
				{
					$R=getDbData($table[$m.'product'],"uid='".$G['goodsuid']."'",'*');
					if (!$R['stock_type1']&&!$R['stock_type2'])
					{
						getDbUpdate($table[$m.'product'],'stock_num=stock_num+'.$G['buynum'],'uid='.$R['uid']);
					}
				}
			}
			getDbDelete($table[$m.'order'],'uid='.$O['uid']);
			getDbDelete($table[$m.'ordertax1'],'orderid='.$O['orderid']);
			getDbDelete($table[$m.'ordertax2'],'orderid='.$O['orderid']);
		}
		else {

			if ($act > 1 && !$O['d_bank'])
			{
				getDbUpdate($table[$m.'order'],"d_bank='".$date['totime']."'",'uid='.$O['uid']);
				getDbUpdate($table[$m.'ordergoods'],"d_bank='".$date['totime']."'",'parent='.$O['uid']);
			}
			if ($act > 2 && !$O['d_tack'])
			{
				getDbUpdate($table[$m.'order'],"d_tack='".$date['totime']."'",'uid='.$O['uid']);
				getDbUpdate($table[$m.'ordergoods'],"d_tack='".$date['totime']."'",'parent='.$O['uid']);
			}
			getDbUpdate($table[$m.'order'],'orderstep='.$act,'uid='.$O['uid']);
			getDbUpdate($table[$m.'ordergoods'],'orderstep='.$act,'parent='.$O['uid']);
		}

		//재고수량복원
		if (($d['shop']['cancel_stock1']==1 && $act == 6) || ($d['shop']['cancel_stock2']==1 && $act == 8) || ($d['shop']['cancel_stock3']==1 && $act == 10))
		{
			$PRODUCT_DATA = db_query("select * from ".$table[$m.'ordergoods']." where parent='".$O['uid']."'",$DB_CONNECT);
			while($G=db_fetch_array($PRODUCT_DATA))
			{
				$R=getDbData($table[$m.'product'],"uid='".$G['goodsuid']."'",'*');
				if (!$R['stock_type1']&&!$R['stock_type2'])
				{
					getDbUpdate($table[$m.'product'],'stock_num=stock_num+'.$G['buynum'],'uid='.$R['uid']);
				}
			}
		}

		//판매량갱신
		if (($O['orderstep'] != 5 && $O['orderstep'] != 10) && ($act == 5 || $act == 10))
		{
			$PRODUCT_DATA = db_query("select * from ".$table[$m.'ordergoods']." where parent='".$O['uid']."'",$DB_CONNECT);
			while($G=db_fetch_array($PRODUCT_DATA))
			{
				$R=getDbData($table[$m.'product'],"uid='".$G['goodsuid']."'",'*');
				if (!$R['stock_type1']&&!$R['stock_type2'])
				{
					getDbUpdate($table[$m.'product'],'buy=buy+'.$G['buynum'],'uid='.$R['uid']);
				}
			}
		}
		//판매량복원
		if (($O['orderstep'] == 5 || $O['orderstep'] == 10) && ($act != 5 && $act != 10))
		{
			$PRODUCT_DATA = db_query("select * from ".$table[$m.'ordergoods']." where parent='".$O['uid']."'",$DB_CONNECT);
			while($G=db_fetch_array($PRODUCT_DATA))
			{
				$R=getDbData($table[$m.'product'],"uid='".$G['goodsuid']."'",'*');
				if (!$R['stock_type1']&&!$R['stock_type2'])
				{
					getDbUpdate($table[$m.'product'],'buy=buy-'.$G['buynum'],'uid='.$R['uid']);
				}
			}
		}

		//적립금의 지급
		if ($d['shop']['pointgive']==2)
		{
			if ($d['shop']['pointstep'] <= $act && $act < 6 && $act != 'delete')
			{
				if ($O['mbruid'] && !$O['rcvpoint'])
				{
					$QKEY = 'my_mbruid,by_mbruid,price,content,d_regis';
					$QVAL = "'".$O['mbruid']."','0','".$O['givepoint']."','상품구입[".$cartProductName."]에대한 적립금입니다.','".$date['totime']."'";
					getDbInsert($table['s_cash'],$QKEY,$QVAL);

					getDbUpdate($table['s_mbrdata'],'cash=cash+'.$O['givepoint'],'memberuid='.$O['mbruid']);
					getDbUpdate($table[$m.'order'],'rcvpoint=1','uid='.$O['uid']);
				}
			}

		}

		//적립금의 회수(삭제/취소/환불완료)
		if ($d['shop']['cancel_point'] && $O['mbruid'] && $O['rcvpoint'])
		{
			if ($act == 'delete' || $act == 6 || $act == 8)
			{
				$QKEY = 'my_mbruid,by_mbruid,price,content,d_regis';
				$QVAL = "'".$O['mbruid']."','0','-".$O['givepoint']."','주문취소/환불[".$cartProductName."]에 대한 적립금의 회수입니다.','".$date['totime']."'";
				getDbInsert($table['s_cash'],$QKEY,$QVAL);

				getDbUpdate($table['s_mbrdata'],'cash=cash-'.$O['givepoint'],'memberuid='.$O['mbruid']);
				getDbUpdate($table[$m.'order'],'rcvpoint=0','uid='.$O['uid']);

			}
		}
	}

	if ($module == 'Y' && $act == 'delete')
	{
		getLink($refresh_url ,'', '삭제되었습니다.' , 'close');
	}
	else {
		$refresh_url = $referer ? urldecode($referer) : $_SERVER['HTTP_REFERER'];
		getLink($refresh_url ,'parent.', $alert , $history);
	}
}
//송장번호입력
if ($_a == 'tack_number')
{
	getDbUpdate($table[$m.'order'],"tack_comp='".$tack_comp."',tack_number='".$tack_number."'",'uid='.$uid);
	getLink('','','','');
}
//주문데이터다운
if ($_a == 'order_db_down')
{
	$psa = '운송장번호,고유번호,주문번호,주문상품,착불여부,주문일시,수령자명,수령자전화,수령자휴대폰,수령자주소';
	$pfa = 'tack_number,uid,orderid,product,tack_after,d_regis,r_name,r_tel1,r_tel2,r_addr';
	$str_array = explode(',',$psa);
	$fil_array = explode(',',$pfa);
	
	$DATA = db_query("select * from ".$table[$m.'order']." where ".$sql." order by uid desc",$DB_CONNECT);

	header( "Content-type: application/vnd.ms-excel" ); 
	header( "Content-Disposition: attachment; filename=".getUTFtoKR('송장데이터_'.$date['totime']).".xls" ); 
	header( "Content-Description: PHP4 Generated Data" );

	echo '<meta http-equiv="content-type" content="text/html; charset=utf-8" />';
	echo '<table border="1">';
	echo '<tr>';
	$i = 0;
	foreach($str_array as $val)
	{
		if ($val=='')continue;
		echo '<td>'.$val.'('.$fil_array[$i].')</td>';
		$i++;
	}
	echo '</tr>';


	while($R=db_fetch_array($DATA))
	{
		echo '<tr>';
		foreach($fil_array as $val)
		{
			if ($val=='')continue;

			if($val == 'product')
			{
				echo '<td>';
				$PRODUCT_DATA = db_query("select * from ".$table[$m.'ordergoods']." where parent='".$R['uid']."'",$DB_CONNECT);
				while($G=db_fetch_array($PRODUCT_DATA))
				{
					echo $G['goodsname'].'('.$G['buynum'].'개)<br />';
				}
				echo '</td>';
			}
			else if ($val == 'o_addr')
			{
				echo '<td>'.htmlspecialchars('('.$R['o_zip'].') '.$R['o_addr1'].' '.$R['o_addr2']).'</td>';
			}
			else if ($val == 'r_addr')
			{
				echo '<td>'.htmlspecialchars('('.$R['r_zip'].') '.$R['r_addr1'].' '.$R['r_addr2']).'</td>';
			}
			else {
				if (strstr('price,shalin,mhalin,chalin,tack,givepoint,usepoint',$val))
				{
					echo '<td>'.number_format($R[$val]).'</td>';
				}
				else {
					echo '<td>'.htmlspecialchars($R[$val]).'</td>';
				}
			}
		}
		echo '</tr>';
	}

	echo '</table>';

	exit;
}
//송장일괄등록
if ($_a == 'order_tack_regis')
{
	if (is_uploaded_file($_FILES['upfile']['tmp_name']))
	{
		$upFile_A = explode('.' , $_FILES['upfile']['name']);
		$upFile_E = strtolower($upFile_A[count($upFile_A)-1]);

		if ($upFile_E == 'txt')
		{
			$csvdata = file($_FILES['upfile']['tmp_name']);
			foreach($csvdata as $csv)
			{
				$val = explode("\t",getKRtoUTF(str_replace('"','',$csv)));
				if($val[0] && $val[1])
				{
					getDbUpdate($table[$m.'order'],"tack_comp='".$tack_comp."',tack_number='".$val[0]."'",'uid='.$val[1]);
				}
			}
		}
		else {
			getLink('','' , '확장자는 .txt 이어야 합니다.' , '');
		}
	}
	getLink('reload','parent.','' ,'');
}
//주문정보수정
if ($_a == 'order_update')
{

	$o_tel1		= $o_tel11 && $o_tel12 && $o_tel13 ? $o_tel11.'-'.$o_tel12.'-'.$o_tel13 : '';
	$o_tel2		= $o_tel21 && $o_tel22 && $o_tel23 ? $o_tel21.'-'.$o_tel22.'-'.$o_tel23 : '';
	$o_zip		= $o_zip1.$o_zip2;
	$o_addr2	= trim($o_addr2);
	$r_tel1		= $r_tel11 && $r_tel12 && $r_tel13 ? $r_tel11.'-'.$r_tel12.'-'.$r_tel13 : '';
	$r_tel2		= $r_tel21 && $r_tel22 && $r_tel23 ? $r_tel21.'-'.$r_tel22.'-'.$r_tel23 : '';
	$r_zip		= $r_zip1.$r_zip2;
	$r_addr2	= trim($r_addr2);
	$b_name		= trim($b_name);
	$memo		= trim($memo);
	$msg		= trim($msg);

	$QVAL = "tack_comp='".$tack_comp."',
	tack_number='".$tack_number."',
	o_name='".$o_name."',
	o_email='".$o_email."',
	o_tel1='".$o_tel1."',
	o_tel2='".$o_tel2."',
	o_zip='".$o_zip."',
	o_addr1='".$o_addr1."',
	o_addr2='".$o_addr2."',
	r_name='".$r_name."',
	r_tel1='".$r_tel1."',
	r_tel2='".$r_tel2."',
	r_zip='".$r_zip."',
	r_addr1='".$r_addr1."',
	r_addr2='".$r_addr2."',
	b_name='".$b_name."',
	memo='".$memo."',
	msg='".$msg."'";
	getDbUpdate($table[$m.'order'],$QVAL,'uid='.$uid);

	$refresh_url = $referer ? urldecode($referer) : $_SERVER['HTTP_REFERER'];
	getLink($refresh_url,'' , $alert , $history);
}
?>