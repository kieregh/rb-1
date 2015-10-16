<?php

$year1	= $year1  ? $year1  : substr($date['today'],0,4);
$month1	= $month1 ? $month1 : substr($date['today'],4,2);
$day1	= $day1   ? $day1   : 1;
$year2	= $year2  ? $year2  : substr($date['today'],0,4);
$month2	= $month2 ? $month2 : substr($date['today'],4,2);
$day2	= $day2   ? $day2   : substr($date['today'],6,2);

$p		= $p ? $p : 1;
$recnum	= $recnum && $recnum < 200 ? $recnum : 10;
$sort	= $sort		? $sort		: 'uid';
$orderby= $orderby	? $orderby	: 'desc';
$d_type = 'd_regis';

$_WHERE = 'mbruid='.$my['uid'];
$_WHERE.= ' and '.$d_type.' > '.$year1.sprintf('%02d',$month1).sprintf('%02d',$day1).'000000 and '.$d_type.' < '.$year2.sprintf('%02d',$month2).sprintf('%02d',$day2).'240000';
if ($where && $keyword)
{
	$_WHERE .= " and ".$where." like '%".trim($keyword)."%'";
}
$NUM = getDbRows($table[$m.'order'],$_WHERE);
$RCD = getDbArray($table[$m.'order'],$_WHERE,'*',$sort,$orderby,$recnum,$p);
$TPG = getTotalPage($NUM,$recnum);
$payType=array('','무통장입금','카드','계좌이체','가상계좌','핸드폰');
?>



<div id="orderpage">

	<div class="order">
		<div class="tt">
			<img src="<?php echo $g['img_module_skin']?>/mypage/arr_01.gif" alt="" /> 주문상품 현황
		</div>


		<div class="term">
			
			<form name="orderTermForm" action="<?php echo $g['s']?>" method="get">
			<input type="hidden" name="r" value="<?php echo $r?>" />
			<input type="hidden" name="m" value="<?php echo $m?>" />
			<input type="hidden" name="mod" value="<?php echo $mod?>" />
			
			<img src="<?php echo $g['img_module_skin']?>/mypage/str_order.gif" alt="조회기간" />
			<img src="<?php echo $g['img_module_skin']?>/mypage/btn_term01.gif" alt="오늘" class="hand" onclick="dropDate('<?php echo $date['today']?>','<?php echo $date['today']?>');" />
			<img src="<?php echo $g['img_module_skin']?>/mypage/btn_term02.gif" alt="15일" class="hand" onclick="dropDate('<?php echo date('Ymd',mktime(0,0,0,substr($date['today'],4,2),substr($date['today'],6,2)-15,substr($date['today'],0,4)))?>','<?php echo $date['today']?>');" />
			<img src="<?php echo $g['img_module_skin']?>/mypage/btn_term03.gif" alt="1개월" class="hand" onclick="dropDate('<?php echo date('Ymd',mktime(0,0,0,substr($date['today'],4,2)-1,substr($date['today'],6,2),substr($date['today'],0,4)))?>','<?php echo $date['today']?>');" />
			<img src="<?php echo $g['img_module_skin']?>/mypage/btn_term04.gif" alt="3개월" class="hand" onclick="dropDate('<?php echo date('Ymd',mktime(0,0,0,substr($date['today'],4,2)-3,substr($date['today'],6,2),substr($date['today'],0,4)))?>','<?php echo $date['today']?>');" />
			<img src="<?php echo $g['img_module_skin']?>/mypage/btn_term05.gif" alt="6개월" class="hand" onclick="dropDate('<?php echo date('Ymd',mktime(0,0,0,substr($date['today'],4,2)-6,substr($date['today'],6,2),substr($date['today'],0,4)))?>','<?php echo $date['today']?>');" />

			<span>
			<select name="year1">
			<?php for($i=2009;$i<substr($date['today'],0,4)+1;$i++):?><option value="<?php echo $i?>"<?php if($year1==$i):?> selected="selected"<?php endif?>><?php echo $i?>년</option><?php endfor?>
			</select>
			<select name="month1">
			<?php for($i=1;$i<13;$i++):?><option value="<?php echo sprintf('%02d',$i)?>"<?php if($month1==$i):?> selected="selected"<?php endif?>><?php echo sprintf('%02d',$i)?>월</option><?php endfor?>
			</select>
			<select name="day1">
			<?php for($i=1;$i<32;$i++):?><option value="<?php echo sprintf('%02d',$i)?>"<?php if($day1==$i):?> selected="selected"<?php endif?>><?php echo sprintf('%02d',$i)?>일(<?php echo getWeekday(date('w',mktime(0,0,0,$month1,$i,$year1)))?>)</option><?php endfor?>
			</select> ~
			<select name="year2">
			<?php for($i=2009;$i<substr($date['today'],0,4)+1;$i++):?><option value="<?php echo $i?>"<?php if($year2==$i):?> selected="selected"<?php endif?>><?php echo $i?>년</option><?php endfor?>
			</select>
			<select name="month2">
			<?php for($i=1;$i<13;$i++):?><option value="<?php echo sprintf('%02d',$i)?>"<?php if($month2==$i):?> selected="selected"<?php endif?>><?php echo sprintf('%02d',$i)?>월</option><?php endfor?>
			</select>
			<select name="day2">
			<?php for($i=1;$i<32;$i++):?><option value="<?php echo sprintf('%02d',$i)?>"<?php if($day2==$i):?> selected="selected"<?php endif?>><?php echo sprintf('%02d',$i)?>일(<?php echo getWeekday(date('w',mktime(0,0,0,$month2,$i,$year2)))?>)</option><?php endfor?>
			</select>
			</span>

			<input type="image" src="<?php echo $g['img_module_skin']?>/mypage/btn_order_search.gif" alt="검색" />
			</form>

		</div>


		<table>
			<tr class="title">
				<td class="date">주문날짜</td>
				<td class="goods">상품명</td>
				<td class="price">실결제액/적립</td>
				<td class="price">결제수단</td>
  			   <td class="price">결제상태</td>
				<td class="flag">상태/정보</td>
				<td class="etc"><?php if($d['shop']['pointgive']==1):?>구매확정/<?php endif?>배송</td>
				<td class="data">증빙자료</td>
			</tr>
			
			
			<?php $sflag=array('','주문접수','입금확인','배송준비','상품발송','배송완료','주문취소','환불요청','환불완료','교환요청','교환완료')?>
			<?php $i=0;while($O=db_fetch_array($RCD)):?>
			<?php $ORDERGOODS=db_query("select * from ".$table[$m.'ordergoods']." where parent='".$O['uid']."'",$DB_CONNECT)?>
			<tr class="loop">
				<td class="date"><script type="text/javascript">getDateFormat('<?php echo $O['d_regis']?>','xxxx.xx.xx');</script></td>
				<td class="goods">
					<?php while($G=db_fetch_array($ORDERGOODS)):?>
					<?php $OP=explode('|',$G['options'])?>
					<div>
					<a href="<?php echo $g['s']?>/?r=<?php echo $r?>&amp;m=<?php echo $m?>&amp;uid=<?php echo $G['goodsuid']?>&amp;review=Y&amp;write=Y" target="_blank"><img src="<?php echo $g['img_module_skin']?>/mypage/btn_product_write.gif" alt="상품평 쓰기" /></a>
					<a href="<?php echo $g['s']?>/?r=<?php echo $r?>&amp;m=<?php echo $m?>&amp;uid=<?php echo $G['goodsuid']?>" target="_blank"><?php echo $G['goodsname']?> (<?php echo $G['buynum']?>개)</a>
					</div>
					<?php for($j=0;$j<count($OP);$j++):?>
					<?php if(trim($OP[$j])=='')continue?>
					<?php $_O=explode('^',$OP[$j])?>
					<div class="option">
						ㄴ<?php echo $_O[0]?> : <?php echo $_O[1]?> 
						<?php if($_O[2]):?><span class="addprice">(<?php echo $_O[2]>0?'+':'-'?><?php echo number_format($_O[2])?>원)</span><?php endif?>
						<?php if($_O[4]):?><span class="addprice">(추가적립:<?php echo number_format($_O[4])?>원)</span><?php endif?>
					</div>
					<?php endfor?>
					<?php endwhile?>
				</td>
				<td class="price"><?php echo number_format(($O['price']+$O['tack'])-($O['shalin']+$O['mhalin']+$O['chalin']+$O['usepoint']))?>원<div>(<?php echo number_format($O['givepoint'])?>원)</div></td>
				<td ><?php echo $payType[$O['ckind']]?></td>
              <td><?php echo getPayState($O,'yes')?></td>
				<td class="flag"><a href="#." onclick="OpenWindowX('<?php echo $g['s']?>/?r=<?php echo $r?>&m=<?php echo $m?>&mod=order&xmod=user&oid=<?php echo $O['orderid']?>');" title="주문번호 : <?php echo $O['orderid']?>"><?php echo $sflag[$O['orderstep']]?></a></td>
				<td class="etc">
					<?php if($d['shop']['pointgive']==1&&$O['orderstep']>1&&$O['orderstep']<6):?>
					<a href="#." onclick="buyFix('<?php echo $O['uid']?>',<?php echo $O['buyfix']?>);"><img src="<?php echo $g['img_module_skin']?>/mypage/btn_buyfix<?php echo $O['buyfix']?>.gif" alt="구매확정" /></a><br />
					<?php endif?>
					<?php if($O['tack_comp']&&$O['tack_number']):?><a href="#." onclick="OpenWindowT('<?php echo $O['tack_comp']?><?php echo $O['tack_number']?>');"><img src="<?php echo $g['img_module_skin']?>/mypage/btn_transport.gif" alt="배송위치 확인" /></a><?php endif?>
				</td>
				<td class="data">
					<a href="#." onclick="OpenWindowX('<?php echo $g['s']?>/?r=<?php echo $r?>&m=<?php echo $m?>&mod=order&xmod=user&print=Y&oid=<?php echo $O['orderid']?>');" title="주문번호 : <?php echo $O['orderid']?>"><img src="<?php echo $g['img_module_skin']?>/mypage/btn_order_print.gif" alt="주문서인쇄" /></a><br />
					<?php if($O['ckind']==2):?>
					<a href="#." onclick="OpenWindowX('<?php echo $g['s']?>/?r=<?php echo $r?>&m=<?php echo $m?>&mod=tax&xmod=card&user=user&oid=<?php echo $O['orderid']?>');" title="승인번호:<?php echo $O['tid']?>"><img src="<?php echo $g['img_module_skin']?>/mypage/btn_card.gif" alt="카드전표" /></a>
					<?php else:?>
					<?php if($O['taxtype']==1):?><a href="#." onclick="OpenWindowX('<?php echo $g['s']?>/?r=<?php echo $r?>&m=<?php echo $m?>&mod=tax&xmod=cash&user=user&oid=<?php echo $O['orderid']?>');" title="<?php echo $O['taxinfo']?>"><img src="<?php echo $g['img_module_skin']?>/mypage/btn_p1.gif" alt="현금영수증" /></a><?php endif?>
					<?php if($O['taxtype']==2):?><a href="#." onclick="OpenWindowX('<?php echo $g['s']?>/?r=<?php echo $r?>&m=<?php echo $m?>&mod=tax&xmod=tax&user=user&oid=<?php echo $O['orderid']?>');" title="<?php echo $O['taxinfo']?>"><img src="<?php echo $g['img_module_skin']?>/mypage/btn_p2.gif" alt="세금계산서" /></a><?php endif?>
					<?php if(!$O['taxtype']):?>
					<a href="#." onclick="OpenWindowX('<?php echo $g['s']?>/?r=<?php echo $r?>&m=<?php echo $m?>&mod=tax&xmod=receipt&user=user&oid=<?php echo $O['orderid']?>');"><img src="<?php echo $g['img_module_skin']?>/mypage/btn_p3.gif" alt="간이영수증" /></a>
					<?php endif?>
					<?php endif?>

				</td>
			</tr>

			<?php endwhile?>
			
			<?php if(!$NUM):?>
			<tr class="none">
				<td colspan="8">주문상품이 없습니다.</td>
			</tr>
			<?php endif?>

			<tr>
				<td colspan="8" class="orderguide">
					<div><img src="<?php echo $g['img_core']?>/_public/ico_notice.gif" alt="" /> 주문/배송 안내</div>
					<ul>
						<li>ㆍ거래상태를 클릭하시면 자세한 주문정보를 확인하실 수 있으며 유효기한시 주문취소/교환요청/환불요청이 가능합니다.</li>
						<?php if($d['shop']['pointgive']==1):?>
						<li>ㆍ<b>구매확정</b> 후에는 적립금이 지급되며 주문취소/교환요청/환불요청은 하실 수 없습니다.</li>
						<!--li>ㆍ구매확정을 하지 않으실 경우 <b>배송일+<?php echo $d['shop']['fixdate']?>일후</b>에 자동으로 확정처리됩니다.</li-->
						<?php endif?>
						<li>ㆍ배송단계에서는 <b>[배송위치확인]</b> 버튼을 이용해서 실시간으로 현재 진행중인 배송상황을 확인하실 수 있습니다.</li> 
						<li>ㆍ결제방식에 따라서 영수증을 요청하신 경우 세금계산서,현금영수증을 다운로드 받을 수 있습니다.</li>
						<li>ㆍ신용카드결제의 경우 현금영수증,세금계산서는 신용카드전표로 대체됩니다.</li>
						<li>ㆍ결제금액은 상품가격이 아닌 할인등이 적용되어 실제로 결제한 총액입니다.</li>
					</ul>				
				
				</td>
			</tr>
		</table>
	</div>

	<div class="pagebox01">
		<?php echo getPageLink(10,$p,$TPG,$g['img_core'].'/page/default')?>
	</div>

</div>

<!-- tid 값 업데이트 폼 -->
<form name="orderTidform" method="post" action="<?php echo $g['s']?>/" target="_action_frame_<?php echo $m?>">
<input type="hidden" name="r" value="<?php echo $r?>" />
<input type="hidden" name="a" value="order_tid_update" />
<input type="hidden" name="c" value="<?php echo $c?>" />
<input type="hidden" name="m" value="<?php echo $m?>" />
<input type="hidden" name="tid" value="" />
<input type="hidden" name="orderid" value="" />
</form>

<script type="text/javascript">
//<![CDATA[
function buyFix(uid,buyfix)
{
	if (buyfix == 1)
	{
		alert('이미 구매확정 하셨거나 확정기한이 경과되어 자동확정되었습니다.     ');
		return false;
	}
	if (confirm('정말로 구매확정하시겠습니까?\n구매확정 후에는 적립금이 지급되며\n주문취소/반품은 하실 수 없습니다.   '))
	{
		eval('frames._action_frame_'+moduleid).location.href = rooturl + '/?r='+raccount+'&m='+moduleid+'&a=buyer_fix&uid='+uid;
	}
}
function dropDate(date1,date2)
{
	var f = document.orderTermForm;
	f.year1.value = date1.substring(0,4);
	f.month1.value = date1.substring(4,6);
	f.day1.value = date1.substring(6,8);
	
	f.year2.value = date2.substring(0,4);
	f.month2.value = date2.substring(4,6);
	f.day2.value = date2.substring(6,8);
	
	f.submit();
}

// 재결제 폼 세팅함수
function do_pay(uid)
{
   var f=document.payform; 
   var ajax=getHttprequest(rooturl+'/?r='+raccount+'&m=<?php echo $m?>&a=get_orderdata&uid='+uid,'');
   var result=getAjaxFilterString(ajax,'RESULT'); 
   var order = JSON.parse(result);
   var payType={2:"card",3:"virt",4:"ziro",5:"phone"}; // 카드,계좌이체,가상계좌,핸드폰
  
   // 결제폼 필드값 세팅
   var orderid=order['orderid']; 
   var payType=payType[order['ckind']];  
   var escr=order['escr'];
   var amount= parseInt(order['price']) - parseInt(order['shalin'])- parseInt(order['mhalin']) - parseInt(order['chalin']) + parseInt(order['tack']);
   var g_name=order['g_name'];
   var g_qty=order['g_qty'];
   var g_name_detail;
   if(g_qty>1) g_name_detail='외 '+g_qty+'건';
   else g_name_detail='';
   var b_name=order['b_name'];
   var o_tel1=order['o_tel1'];
   var o_tel2=order['o_tel2'];
   var o_email=order['o_email'];
   var r_name=order['r_name'];
   var r_tel1=order['r_tel1'];
   var r_tel2=order['r_tel2'];
   var r_zip=order['r_zip'];
   var r_addr1=order['r_addr1'];
   var r_addr2=order['r_addr2'];

   <?php include_once $g['dir_module'].'pg/'.$d['shop']['pgcomp'].'/connect_payonly.php'?>
}
//]]>
</script>

