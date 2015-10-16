<?php
include_once $g['path_module'].$module.'/var/var.php';
include_once $g['path_module'].$module.'/_main.php';

$year1	= $year1  ? $year1  : substr($date['today'],0,4);
$month1	= $month1 ? $month1 : substr($date['today'],4,2);
$day1	= $day1   ? $day1   : 1;//substr($date['today'],6,2);
$year2	= $year2  ? $year2  : substr($date['today'],0,4);
$month2	= $month2 ? $month2 : substr($date['today'],4,2);
$day2	= $day2   ? $day2   : substr($date['today'],6,2);


$sort	= $sort ? $sort : 'uid';
$orderby= $orderby ? $orderby : 'desc';
$recnum	= $recnum && $recnum < 200 ? $recnum : 20;

$d_type = $d_type ? $d_type : 'd_regis';
$orderstep = $orderstep != '' ? $orderstep : 1;
$_WHERE = $orderstep ? 'orderstep='.$orderstep.' and ' : '';
$_WHERE.= $d_type.' > '.$year1.sprintf('%02d',$month1).sprintf('%02d',$day1).'000000 and '.$d_type.' < '.$year2.sprintf('%02d',$month2).sprintf('%02d',$day2).'240000';

if ($ckind1||$ckind2||$ckind3||$ckind31||$ckind32)
{
	$_WHERE .= ' and (';
	if($ckind1) $_WHERE .= 'ckind=1 or ';
	if($ckind2) $_WHERE .= 'ckind=2 or ';
	if($ckind3) $_WHERE .= 'ckind=3 or ';
	if($ckind31) $_WHERE .= 'ckind=4 or ';
	if($ckind32) $_WHERE .= 'ckind=5 or ';
	$_WHERE = substr($_WHERE,0,strlen($_WHERE)-4).')';
}
else {
	$_WHERE .= ' and ckind < 4';
	$ckind1 = $ckind2 = $ckind3 = 1;
}
if ($ckind4) $_WHERE .= ' and usepoint>0';
if ($taxtype1) $_WHERE .= ' and taxtype=1';
if ($taxtype2) $_WHERE .= ' and taxtype=2';
if ($escr) $_WHERE .= ' and escr=1';
if ($buyfix) $_WHERE .= ' and buyfix=1';
if ($is_mobile) $_WHERE .= ' and is_mobile=1';
if ($pg_error) $_WHERE .= " and tid=''";

if ($where && $keyw)
{
	$_WHERE .= getSearchSql($where,$keyw,$ikeyword,'or');	
}
$RCD = getDbArray($table[$module.'order'],$_WHERE,'*',$sort,$orderby,$recnum,$p);
$NUM = getDbRows($table[$module.'order'],$_WHERE);
$TPG = getTotalPage($NUM,$recnum);

$cflag  = array('','통장','카드','이체','가상','핸드폰');
$sflag  = array('','주문접수','입금확인','배송준비','상품발송','거래완료','주문취소','환불요청','환불완료','교환요청','교환완료');
?>

<div id="orderlist">



	<div class="sbox">
		<form name="procForm" action="<?php echo $g['s']?>/" method="get">
		<input type="hidden" name="r" value="<?php echo $r?>" />
		<input type="hidden" name="m" value="<?php echo $m?>" />
		<input type="hidden" name="module" value="<?php echo $module?>" />
		<input type="hidden" name="front" value="<?php echo $front?>" />
		<input type="hidden" name="orderstep" value="<?php echo $orderstep?>" />

		<div class="m_menu">
		<ul>
		<li<?php if($orderstep=='0'):?> class="lside selected"<?php else:?> class="lside"<?php endif?> onclick="document.procForm.orderstep.value=0;document.procForm.submit();">전체</li>
		<li<?php if($orderstep=='1'):?> class="selected"<?php endif?> onclick="document.procForm.orderstep.value=1;document.procForm.submit();">주문접수</li>
		<li<?php if($orderstep=='2'):?> class="selected"<?php endif?> onclick="document.procForm.orderstep.value=2;document.procForm.submit();">입금확인</li>
		<li<?php if($orderstep=='3'):?> class="selected"<?php endif?> onclick="document.procForm.orderstep.value=3;document.procForm.submit();">배송준비</li>
		<li<?php if($orderstep=='4'):?> class="selected"<?php endif?> onclick="document.procForm.orderstep.value=4;document.procForm.submit();">상품발송</li>
		<li<?php if($orderstep=='5'):?> class="selected"<?php endif?> onclick="document.procForm.orderstep.value=5;document.procForm.submit();">거래완료</li>
		<li<?php if($orderstep=='6'):?> class="selected"<?php endif?> onclick="document.procForm.orderstep.value=6;document.procForm.submit();">주문취소</li>
		<li<?php if($orderstep=='7'):?> class="selected"<?php endif?> onclick="document.procForm.orderstep.value=7;document.procForm.submit();">환불요청</li>
		<li<?php if($orderstep=='8'):?> class="selected"<?php endif?> onclick="document.procForm.orderstep.value=8;document.procForm.submit();">환불완료</li>
		<li<?php if($orderstep=='9'):?> class="selected"<?php endif?> onclick="document.procForm.orderstep.value=9;document.procForm.submit();">교환요청</li>
		<li<?php if($orderstep=='10'):?> class="selected"<?php endif?> onclick="document.procForm.orderstep.value=10;document.procForm.submit();">교환완료</li>
		</ul>
		<div class="clear"></div>
		</div>

		<div>
		<select name="year1">
		<?php for($i=$date['year'];$i>2009;$i--):?><option value="<?php echo $i?>"<?php if($year1==$i):?> selected="selected"<?php endif?>><?php echo $i?>년</option><?php endfor?>
		</select>
		<select name="month1">
		<?php for($i=1;$i<13;$i++):?><option value="<?php echo sprintf('%02d',$i)?>"<?php if($month1==$i):?> selected="selected"<?php endif?>><?php echo sprintf('%02d',$i)?>월</option><?php endfor?>
		</select>
		<select name="day1">
		<?php for($i=1;$i<32;$i++):?><option value="<?php echo sprintf('%02d',$i)?>"<?php if($day1==$i):?> selected="selected"<?php endif?>><?php echo sprintf('%02d',$i)?>일(<?php echo getWeekday(date('w',mktime(0,0,0,$month1,$i,$year1)))?>)</option><?php endfor?>
		</select> ~
		<select name="year2">
		<?php for($i=$date['year'];$i>2009;$i--):?><option value="<?php echo $i?>"<?php if($year2==$i):?> selected="selected"<?php endif?>><?php echo $i?>년</option><?php endfor?>
		</select>
		<select name="month2">
		<?php for($i=1;$i<13;$i++):?><option value="<?php echo sprintf('%02d',$i)?>"<?php if($month2==$i):?> selected="selected"<?php endif?>><?php echo sprintf('%02d',$i)?>월</option><?php endfor?>
		</select>
		<select name="day2">
		<?php for($i=1;$i<32;$i++):?><option value="<?php echo sprintf('%02d',$i)?>"<?php if($day2==$i):?> selected="selected"<?php endif?>><?php echo sprintf('%02d',$i)?>일(<?php echo getWeekday(date('w',mktime(0,0,0,$month2,$i,$year2)))?>)</option><?php endfor?>
		</select>

		<input type="button" class="btngray" value="기간적용" onclick="this.form.submit();" />
		<input type="button" class="btngray" value="어제" onclick="dropDate('<?php echo date('Ymd',mktime(0,0,0,substr($date['today'],4,2),substr($date['today'],6,2)-1,substr($date['today'],0,4)))?>','<?php echo date('Ymd',mktime(0,0,0,substr($date['today'],4,2),substr($date['today'],6,2)-1,substr($date['today'],0,4)))?>');" />
		<input type="button" class="btngray" value="오늘" onclick="dropDate('<?php echo $date['today']?>','<?php echo $date['today']?>');" />
		<input type="button" class="btngray" value="일주" onclick="dropDate('<?php echo date('Ymd',mktime(0,0,0,substr($date['today'],4,2),substr($date['today'],6,2)-7,substr($date['today'],0,4)))?>','<?php echo $date['today']?>');" />
		<input type="button" class="btngray" value="한달" onclick="dropDate('<?php echo date('Ymd',mktime(0,0,0,substr($date['today'],4,2)-1,substr($date['today'],6,2),substr($date['today'],0,4)))?>','<?php echo $date['today']?>');" />
		<input type="button" class="btngray" value="당월" onclick="dropDate('<?php echo substr($date['today'],0,6)?>01','<?php echo $date['today']?>');" />
		<input type="button" class="btngray" value="전월" onclick="dropDate('<?php echo date('Ym',mktime(0,0,0,substr($date['today'],4,2)-1,substr($date['today'],6,2),substr($date['today'],0,4)))?>01','<?php echo date('Ym',mktime(0,0,0,substr($date['today'],4,2)-1,substr($date['today'],6,2),substr($date['today'],0,4)))?>31');" />
		<input type="button" class="btngray" value="전체" onclick="dropDate('20090101','<?php echo $date['today']?>');" />
		</div>

		<div>
		<select name="recnum" onchange="this.form.submit();">
		<option value="20"<?php if($recnum==20):?> selected="selected"<?php endif?>>20개</option>
		<option value="35"<?php if($recnum==35):?> selected="selected"<?php endif?>>35개</option>
		<option value="50"<?php if($recnum==50):?> selected="selected"<?php endif?>>50개</option>
		<option value="75"<?php if($recnum==75):?> selected="selected"<?php endif?>>75개</option>
		<option value="90"<?php if($recnum==90):?> selected="selected"<?php endif?>>90개</option>
		</select>
		<select name="where">
		<option value="o_name|r_name|b_name" class="all"<?php if($where=='o_name|r_name|b_name'):?> selected="selected"<?php endif?>>전체</option>
		<option value="o_name"<?php if($where=='o_name'):?> selected="selected"<?php endif?>>주문자</option>
		<option value="r_name"<?php if($where=='r_name'):?> selected="selected"<?php endif?>>수령자</option>
		<option value="b_name"<?php if($where=='b_name'):?> selected="selected"<?php endif?>>입금자</option>
		<option value="oid"<?php if($where=='oid'):?> selected="selected"<?php endif?>>주문번호</option>
		</select>
		<input type="text" name="keyw" value="<?php echo stripslashes($keyw)?>" class="input" />
		<input type="submit" value="검색" class="btnblue" />
		<input type="button" value="리셋" class="btngray" onclick="location.href='<?php echo $g['adm_href']?>';" />
		
		&nbsp;&nbsp;
		기준일 : 
		<input type="radio" name="d_type" id="c1" value="d_regis"<?php if($d_type=='d_regis'):?> checked="checked"<?php endif?> onclick="this.form.submit();" /><label for="c1">주문일</label>
		<input type="radio" name="d_type" id="c2" value="d_bank"<?php if($d_type=='d_bank'):?> checked="checked"<?php endif?> onclick="this.form.submit();" /><label for="c2">입금일</label>
		<input type="radio" name="d_type" id="c3" value="d_tack"<?php if($d_type=='d_tack'):?> checked="checked"<?php endif?> onclick="this.form.submit();" /><label for="c3">배송일</label>
		</div>

		<div class="shift">
		<input type="checkbox" name="ckind1" id="p1" value="bank"<?php if($ckind1):?> checked="checked"<?php endif?> onclick="this.form.submit();" /><label for="p1">무통장</label>
		<input type="checkbox" name="ckind2" id="p2" value="card"<?php if($ckind2):?> checked="checked"<?php endif?> onclick="this.form.submit();" /><label for="p2">신용카드</label>
		<input type="checkbox" name="ckind3" id="p3" value="virt"<?php if($ckind3):?> checked="checked"<?php endif?> onclick="this.form.submit();" /><label for="p3">계좌이체</label>
		<input type="checkbox" name="ckind31" id="p31" value="ziro"<?php if($ckind31):?> checked="checked"<?php endif?> onclick="this.form.submit();" /><label for="p31">가상계좌</label>
		<input type="checkbox" name="ckind32" id="p32" value="phone"<?php if($ckind32):?> checked="checked"<?php endif?> onclick="this.form.submit();" /><label for="p32">핸드폰</label>
		<input type="checkbox" name="ckind4" id="p4" value="point"<?php if($ckind4):?> checked="checked"<?php endif?> onclick="this.form.submit();" /><label for="p4">적립금사용</label>
		<input type="checkbox" name="taxtype1" id="p5" value="point"<?php if($taxtype1):?> checked="checked"<?php endif?> onclick="this.form.submit();" /><label for="p5">현금영수증</label>
		<input type="checkbox" name="taxtype2" id="p6" value="point"<?php if($taxtype2):?> checked="checked"<?php endif?> onclick="this.form.submit();" /><label for="p6">세금계산서</label>
		<input type="checkbox" name="escr" id="p7" value="point"<?php if($escr):?> checked="checked"<?php endif?> onclick="this.form.submit();" /><label for="p7">에스크로</label>
		<input type="checkbox" name="buyfix" id="p8" value="point"<?php if($buyfix):?> checked="checked"<?php endif?> onclick="this.form.submit();" /><label for="p8">구매확정</label>
		<input type="checkbox" name="is_mobile" id="p9" value="point"<?php if($is_mobile):?> checked="checked"<?php endif?> onclick="this.form.submit();" /><label for="p9">모바일</label>
		<input type="checkbox" name="pg_error" id="p10" value="point"<?php if($pg_error):?> checked="checked"<?php endif?> onclick="this.form.submit();" /><label for="p10">PG 거래번호 없음</label>
		</div>

		</form>
	</div>


	<div class="info">

		<div class="article">
			<?php echo number_format($NUM)?>건(<?php echo $p?>/<?php echo $TPG?>페이지)
		</div>
		
		<div class="category">

		</div>
		<div class="clear"></div>
	</div>



	<form name="listForm" action="<?php echo $g['s']?>/" method="post" target="_action_frame_<?php echo $m?>">
	<input type="hidden" name="r" value="<?php echo $r?>" />
	<input type="hidden" name="m" value="<?php echo $module?>" />
	<input type="hidden" name="a" value="" />
	<input type="hidden" name="_a" value="" />
	<input type="hidden" name="act" value="" />
	<input type="hidden" name="sql" value="<?php echo $_WHERE?>" />

	<table summary="주문리스트 입니다.">
	<caption>주문리스트</caption> 
	<colgroup> 
	<col width="50"> 
	<col width="70"> 
	<col width="70"> 
	<col width="200"> 
	<col width="70"> 
	<col width="70"> 
	<col width="70"> 
	<col width="70"> 
	<col width="70"> 
	<col width="80">
	<col width="80">
	<col>
	</colgroup> 
	<thead>
	<tr>
	<th scope="col" class="side1"><img src="<?php echo $g['img_core']?>/_public/ico_check_01.gif" class="hand" alt="" onclick="chkFlag('order_members[]');" /><br />번호</th>
	<th scope="col">주문일자<br />입급일자</th>
	<th scope="col">주문자<br />입금자</th>
	<th scope="col">상품정보<br />(옵션내용)</th>
	<th scope="col">상품가격<br />배송비용</th>
	<th scope="col">총할인금액<br />적립금사용</th>
	<th scope="col">최종결제액<br />적립금지급</th>
	<th scope="col">결제방식<br />배송일자</th>
	<th scope="col">운송장번호</th>
	<th scope="col">주문관리</th>
	<th scope="col">PG 거래번호</th>
	<th scope="col" class="side2"></th>
	</tr>
	</thead>
	<tbody>

	<?php while($O=db_fetch_array($RCD)):?>
	<?php $ORDERGOODS=getDbArray($table[$module.'ordergoods'],'parent='.$O['uid'],'*','uid','asc',0,1)?>
	<tr>
	<td>
		<input type="checkbox" name="order_members[]" value="<?php echo $O['uid']?>" />
		<br >
		(<?php echo $NUM-((($p-1)*$recnum)+$_rec++)?>)	
	</td>
	<td>
		<?php if($O['is_mobile']):?><img src="<?php echo $g['img_core']?>/_public/ico_mobile.gif" alt="" title="모바일주문" /><?php endif?>
		<a href="#." onclick="OpenWindowX('<?php echo $g['s']?>/?r=<?php echo $r?>&m=<?php echo $module?>&mod=order&xmod=admin&oid=<?php echo $O['orderid']?>');" title="주문번호 : <?php echo $O['orderid']?>"><?php echo getDateFormat($O['d_regis'],'Y/m/d')?></a><br />
		<?php if($O['d_bank']):?><?php echo getDateFormat($O['d_bank'],'Y/m/d')?><?php else:?>(미확인)<?php endif?>
	</td>
	<td>
		<?php if($O['mbruid']):?><a href="javascript:OpenWindow('<?php echo $g['s']?>/?r=<?php echo $r?>&iframe=Y&m=member&front=manager&page=main&mbruid=<?php echo $O['mbruid']?>');" title="회원메니져"><?php echo $O['o_name']?></a><?php else:?><?php echo $O['o_name']?><?php endif?><br />
		(<?php if($O['b_name']):?><?php echo $O['b_name']?><?php else:?><?php echo $O['o_name']?><?php endif?>)
	</td>
	<td class="product">
		<?php while($G=db_fetch_array($ORDERGOODS)):?>
		<?php $OP=explode('|',$G['options'])?>
		<div>
		<a href="<?php echo $g['s']?>/?r=<?php echo $r?>&amp;m=<?php echo $module?>&amp;uid=<?php echo $G['goodsuid']?>" target="_blank"><?php echo $G['goodsname']?> (<?php if($G['shalin']||$G['mhalin']):?><s><?php echo number_format($G['price'])?></s>-&gt;<?php endif?><?php echo number_format($G['price']-($G['shalin']+$G['mhalin']))?>원/<?php echo $G['buynum']?>개)</a>
		</div>
		<?php for($j=0;$j<count($OP);$j++):?>
		<?php if(trim($OP[$j])=='')continue?>
		<?php $_O=explode('^',$OP[$j])?>
		<div class="option">
			ㄴ<?php echo $_O[0]?> : <?php echo $_O[1]?> 
			<?php if($_O[2]):?><span class="addprice">(<?php echo $_O[2]>0?'+':'-'?><?php echo number_format($_O[2])?>원)</span><?php endif?>
			<?php if($_O[4]):?><span class="addprice">(추가적립 <?php echo number_format($_O[4])?>원)</span><?php endif?>
		</div>
		<?php endfor?>
		<?php endwhile?>
	</td>
	<td>
		<?php echo number_format($O['price'])?><br />(<?php echo $O['tack']?'+'.number_format($O['tack']):($O['tack_after']?'착불':'무료')?>)
	</td>
	<td>
		(-<?php echo number_format($O['shalin']+$O['mhalin']+$O['chalin'])?>)<br />(-<?php echo number_format($O['usepoint'])?>)
	</td>
	<td>
		<span title="구매확정여부">[<?php echo $O['buyfix']?'Y':'N'?>]</span><?php echo number_format(($O['price']+$O['tack'])-($O['shalin']+$O['mhalin']+$O['chalin']+$O['usepoint']))?>원<br /><span title="적립금지급여부">[<?php echo $O['rcvpoint']?'Y':'N'?>]</span><?php echo number_format($O['givepoint'])?>원
	</td>
	<td<?php if($O['ckind']==1):?> title="<?php echo $O['bank']?>"<?php endif?>><?php echo $sflag[$O['orderstep']]?><br />&lt;<?php echo $cflag[$O['ckind']]?>&gt;</td>
	</td>
	<td>
		<input type="text" name="tack_<?php echo $O['uid']?>" value="<?php echo $O['tack_number']?>" class="input" size="10"<?php if($O['tack_comp']&&$O['tack_number']):?> ondblclick="OpenWindowT('<?php echo $O['tack_comp']?><?php echo $O['tack_number']?>');" title="[배송추적]더블클릭"<?php endif?> />
	</td>
	<td>
		<a href="#." onclick="OpenWindowX('<?php echo $g['s']?>/?r=<?php echo $r?>&m=<?php echo $module?>&mod=order&xmod=admin&oid=<?php echo $O['orderid']?>');" title="주문번호 : <?php echo $O['orderid']?>">주문서</a><br />
		<?php if($O['ckind']==2):?>
		<a href="#." onclick="OpenWindowX('<?php echo $g['s']?>/?r=<?php echo $r?>&m=<?php echo $module?>&mod=tax&xmod=card&user=admin&oid=<?php echo $O['orderid']?>');" title="승인번호:<?php echo $O['tid']?>">카드전표</a>
		<?php else:?>
		<?php if($O['taxtype']==1):?><a href="#." onclick="OpenWindowX('<?php echo $g['s']?>/?r=<?php echo $r?>&m=<?php echo $module?>&mod=tax&xmod=cash&user=admin&oid=<?php echo $O['orderid']?>');" title="<?php echo $O['taxinfo']?>">현금영수증</a><?php endif?>
		<?php if($O['taxtype']==2):?><a href="#." onclick="OpenWindowX('<?php echo $g['s']?>/?r=<?php echo $r?>&m=<?php echo $module?>&mod=tax&xmod=tax&user=admin&oid=<?php echo $O['orderid']?>');" title="<?php echo $O['taxinfo']?>">세금계산서</a><?php endif?>
		<?php if(!$O['taxtype']):?><a href="#." onclick="OpenWindowX('<?php echo $g['s']?>/?r=<?php echo $r?>&m=<?php echo $module?>&mod=tax&xmod=receipt&user=admin&oid=<?php echo $O['orderid']?>');">간이영수증</a><?php endif?>
		<?php endif?>	
	</td>
	<td>
		<?php if($O['tid']):?>
		   <span style="color:blue">있음</span>
		<?php else:?>
          <span style="color:red">없음</span>
		<?php endif?> 
		<br/><input type="text" name="tid_<?php echo $O['orderid']?>" value="<?php echo $O['tid']?>" class="input"  />
	</td>
	<td></td>
	</tr> 
	<?php endwhile?> 

	<?php if(!$NUM):?>
	<tr>
	<td><input type="checkbox" disabled="disabled" /></td>
	<td>-</td>
	<td>-</td>
	<td class="sbj1">접수된 주문이 없습니다.</td>
	<td>-</td>
	<td>-</td>
	<td>-</td>
	<td>-</td>
	<td>-</td>
	<td>-</td>
	<td></td>
	</tr> 
	<?php endif?>

	</tbody>
	</table>

	<div class="pagebox01">
	<script type="text/javascript">getPageLink(10,<?php echo $p?>,<?php echo $TPG?>,'<?php echo $g['img_core']?>/page/default');</script>
	</div>


	<select name="tack_comp" class="tackcomp">
	<?php $mc = file($g['path_module'].$module.'/var/data.tack.txt')?>
	<?php foreach($mc as $mv):$mvs=explode('|',$mv)?>
	<option value="<?php echo trim($mvs[1])?>"><?php echo trim($mvs[0])?></option>
	<?php endforeach?>
	</select>
	<input type="button" value="송장등록" class="btnblue" onclick="actCheck('tack');" />
	<input type="button" value="PG 거래번호 등록" class="btngray" onclick="actCheck('tid');" />
	<input type="button" value="엑셀" class="btngray" onclick="layerShowHide('guide_tack','block','none');" />
	&nbsp;&nbsp;&nbsp;&nbsp;

	<input type="button" value="주문삭제" class="btngray" onclick="actCheck('delete');" />

	<?php if($orderstep):?>

	<?php if($orderstep < 6):?>
	<input type="button" class="btngray" value="주문취소" onclick="actCheck('6');" />
	<?php endif?>
	
	<?php if($orderstep > 1 && $orderstep < 6):?>
	<input type="button" class="btngray" value="환불요청" onclick="actCheck('7');" />
	<?php endif?>
	
	<?php if($orderstep == '7'):?>
	<input type="button" class="btnblue" value="환불완료" onclick="actCheck('8');" />
	<?php endif?>
	
	<?php if($orderstep == '5'):?>
	<input type="button" class="btngray" value="교환요청" onclick="actCheck('9');" />
	<?php endif?>
	
	<?php if($orderstep == '9'):?>
	<input type="button" class="btnblue" value="교환완료" onclick="actCheck('10');" />
	<?php endif?>

	<?php if($orderstep == '1'):?>
	<input type="button" class="btnblue" value="입금확인" onclick="actCheck('2');" />
	<?php endif?>

	<?php if($orderstep == '2'):?>
	<input type="button" class="btnblue" value="배송준비" onclick="actCheck('3');" />
	<?php endif?>
	
	<?php if($orderstep == '3'):?>
	<input type="button" class="btnblue" value="상품발송" onclick="actCheck('4');" />
	<?php endif?>
	
	<?php if($orderstep == '4'):?>
	<input type="button" class="btnblue" value="거래완료" onclick="actCheck('5');" />
	<?php endif?>

	<?php endif?>
	</form>


	<div id="guide_tack" class="guide hide">
	<br />
	<form name="upForm" action="<?php echo $g['s']?>/" method="post" target="_action_frame_<?php echo $m?>" enctype="multipart/form-data" onsubmit="return saveCheck(this);">
	<input type="hidden" name="r" value="<?php echo $r?>" />
	<input type="hidden" name="m" value="<?php echo $module?>" />
	<input type="hidden" name="a" value="order_admin" />
	<input type="hidden" name="_a" value="order_tack_regis" />
	<select name="tack_comp" class="tackcomp">
	<?php $mc = file($g['path_module'].$module.'/var/data.tack.txt')?>
	<?php foreach($mc as $mv):$mvs=explode('|',$mv)?>
	<option value="<?php echo trim($mvs[1])?>"><?php echo trim($mvs[0])?></option>
	<?php endforeach?>
	</select>
	<input type="file" name="upfile" class="upfile" />
	<input type="submit" value="CSV송장등록" class="btngray" />
	<input type="button" value="송장데이터 받기" class="btngray" onclick="order_db_down();" />
	</form>
	<br />
	<br />
	<div class="b">CSV파일을 이용한 송장번호 일괄등록 방법 :</div>
	송장데이터를 다운로드 받은 후 엑셀에서 송장번호를 기입합니다.<br />
	송장번호를 등록한 엑셀 파일을 텍스트(탭으로 분리)(*.txt) 로 저장한 후 등록합니다.<br />
	</div>
</div>


<script type="text/javascript">
//<![CDATA[
function saveCheck(f)
{
	if (f.upfile.value == '')
	{
		alert('운송장번호 CSV파일을 선택해 주세요.');
		f.upfile.focus();
		return false;
	}
	var extarr = f.upfile.value.split('.');
	var filext = extarr[extarr.length-1].toLowerCase();
	var permxt = '[txt]';

	if (permxt.indexOf(filext) == -1)
	{
		alert('txt확장자의 CSV 파일만 등록할 수 있습니다.    ');
		f.upfile.focus();
		return false;
	}

	return confirm('정말로 등록하시겠습니까?       ');
}
function order_db_down()
{
	if (confirm('현재 조건에 해당하는 송장용 주문데이터를 받으시겠습니까? '))
	{
		var f = document.listForm;
		f.a.value = 'order_admin'
		f._a.value = 'order_db_down';
		f.submit();
	}
}
function OpenWindowX(url)
{
	window.open(url,'','top=0,left=0,width=100px,height=100px,status=yes,resizable=no,scrollbars=yes');
}
function OpenWindowT(url)
{
	window.open(url,'','top=0,left=0,width=800px,height=700px,status=yes,resizable=no,scrollbars=yes');
}
function dropDate(date1,date2)
{
	var f = document.procForm;
	f.year1.value = date1.substring(0,4);
	f.month1.value = date1.substring(4,6);
	f.day1.value = date1.substring(6,8);
	
	f.year2.value = date2.substring(0,4);
	f.month2.value = date2.substring(4,6);
	f.day2.value = date2.substring(6,8);

	f.submit();
}
function actCheck(flag)
{
	var f = document.listForm;
    var l = document.getElementsByName('order_members[]');
    var n = l.length;
	var j = 0;
    var i;

    for (i = 0; i < n; i++)
	{
		if(l[i].checked == true)
		{
			j++;
		}
	}
	if (!j)
	{
		alert('선택된 주문이 없습니다.      ');
		return false;
	}
	

	if (!confirm('정말로 실행하시겠습니까?        '))	return false;

	f.a.value = 'order_admin';
	f._a.value = 'order_action';
	f.act.value = flag;
	f.submit();
	return false;
}
//]]>
</script>
