<?php
$_WHERE = "mbruid='".$my['uid']."' and orderid='".$_orderid."'";
$O=getDbData($table[$m.'order'],$_WHERE,'*');
?>


<div id="orderpage">

	<div class="order">
		<div class="orderguide">
			<div><img src="<?php echo $g['img_core']?>/_public/ico_notice.gif" alt="" /> 아래 '주문내역'과 같이 주문접수가 완료되었습니다. </div>
			<ul>
				<li>ㆍ현재 주문접수만 완료된 상태이며 <span style="color:red;">결제를 진행하기 위해서는 <span class="b">'결제하기'</span></span> 버튼을 눌러주세요. </li>
				<li>ㆍ상세정보를 클릭하시면 자세한 주문정보를 확인하실 수 있습니다. </li>
				<li>ㆍ결제금액은 상품가격이 아닌 할인등이 적용되어 실제로 결제한 총액입니다.</li>
			</ul>
		</div>
		<br/>		
		<div class="tt">
			<img src="<?php echo $g['img_module_skin']?>/mypage/arr_01.gif" alt="" /> 주문내역
			<span style="display:inline-block;float:right;"><a href="<?php echo $g['s']?>/?r=<?php echo $r?>&amp;m=<?php echo $m?>&amp;mod=myorder" style="color:#999;">[주문내역 전체보기]</a></span>
		</div>
		<table>
			<tr class="title">
				<td class="date">주문날짜</td>
				<td class="goods">상품명</td>
				<td class="price">실결제액/적립</td>
				<td class="flag">상세정보</td>
				<td class="etc">결제방식</td>
				<td class="data">결제진행</td>
			</tr>
			
			<?php $pflag=array('','무통장입금','카드','계좌이체','가상계좌','핸드폰')?>
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
				<td class="flag">
					<a href="#." onclick="OpenWindowX('<?php echo $g['s']?>/?r=<?php echo $r?>&m=<?php echo $m?>&mod=order&xmod=user&oid=<?php echo $O['orderid']?>');" title="주문번호 : <?php echo $O['orderid']?>">상세정보</a>
            	</td>
				<td class="etc">
			       <?php echo $pflag[$O['ckind']]?>
				</td>
				<td class="data">
					<?php echo getPayState($O,'yes')?>
				</td>
			</tr>			
		</table>
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
<input type="hidden" name="bank" value="" />
</form>

<script type="text/javascript">
//<![CDATA[

// 결제 진행 함수 
function do_pay(uid)
{
   var f=document.payform; 
   var ajax=getHttprequest(rooturl+'/?r='+raccount+'&m=<?php echo $m?>&a=get_orderdata&uid='+uid,'');
   var result=getAjaxFilterString(ajax,'RESULT'); 
   var order = JSON.parse(result);
   var payType={2:"card",3:"virt",4:"ziro",5:"phone"};
  
   // 주문 데이타 세팅
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

