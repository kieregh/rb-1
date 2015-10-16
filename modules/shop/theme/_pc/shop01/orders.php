<form name="orderform" method="post" action="<?php echo $g['s']?>/" target="_action_frame_<?php echo $m?>" onsubmit="return ordercheck(this);">
<input type="hidden" name="r" value="<?php echo $r?>" />
<input type="hidden" name="a" value="order_regis" />
<input type="hidden" name="c" value="<?php echo $c?>" />
<input type="hidden" name="m" value="<?php echo $m?>" />

<div id="cart">
	<table>
		<tr class="sbj">
			<td>&nbsp;</td>
			<td class="t">상품정보</td>
			<td>상품가격</td>
			<td>적립금</td>
			<td>수량</td>
			<td>합계</td>
			<td>할인금액</td>
		</tr>
		
		<?php $i=0?>
		<?php $_TACK=array()?>
		<?php $is_free=$is_cash=false?>
		<?php $sprice=$shprice=$mhprice=$tprice1=$tprice2=$gpoint=0?>
		<?php $cartarr=is_file($g['cart_file'])?file($g['cart_file']):array()?>
		<?php foreach($cartarr as $val):?>
		<?php $C=explode('<s>',$val)?>
		<?php $O=explode('|',$C[2])?>
		<?php $R=getUidData($table[$m.'product'],$C[0])?>
		<?php if(!$R['uid'] || $R['display'] || $R['price_x'] || ($R['stock'] && $buynum > $R['stock_num']))continue?>
		<?php $isPumjeol=getPumjeol($R)?>
		<?php $oprice=getRound($R['price'],'down',$d['shop']['jeolsa'])?>
		<?php $_shprice=getRound(getSHalin($R),'down',$d['shop']['jeolsa'])?>
		<?php $_mhprice=getRound(getMHalin($R),'down',$d['shop']['jeolsa'])?>
		<?php $_smhprice=($_shprice+$_mhprice)*$C[1]?>
		<?php if($R['is_free'])$is_free=true?>
		<?php if($R['is_cash'])$is_cash=true?>

		<tr>
			<td><a href="<?php echo $g['shop_view'].$R['uid']?>"><img src="<?php echo getPic($R,$m,4)?>" width="50" alt="매장보기" /></a></td>
			<td class="nametd">
				<div class="gname">
				<?php if($R['is_free']):?><span class="f1">[무료배송]</span><?php endif?>
				<?php if($R['is_cash']):?><span class="f2">[현금결제]</span><?php endif?>
				<?php echo $R['name']?>
				</div>
				<?php $opprice=$oppoint=0?>
				<?php for($j=0;$j<count($O);$j++):?>
				<?php if(trim($O[$j])=='')continue?>
				<?php $_O=explode('^',$O[$j])?>
				<?php $opprice+=getRound($_O[2],'down',$d['shop']['jeolsa'])?>
				<?php $oppoint+=getRound($_O[4],'down',$d['shop']['jeolsa'])?>
				<div class="option">
					ㄴ<?php echo $_O[0]?> : <?php echo $_O[1]?> 
					<?php if($_O[2]):?><span class="addprice">(<?php echo $_O[2]>0?'+':'-'?><?php echo number_format($_O[2])?>원)</span><?php endif?>
					<?php if($_O[4]):?><span class="addprice">(추가적립 <?php echo number_format($_O[4])?>원)</span><?php endif?>
				</div>
				<input type="hidden" id="qshopOptions_<?php echo $i?><?php echo $j?>" value="<?php echo $_O[1]?>^<?php echo $_O[2]?>^<?php echo $_O[3]?>^<?php echo $_O[4]?>" />
				<input type="hidden" name="pilsuCheck_<?php echo $i?>[]" value="checked|input|<?php echo $_O[0]?>" />
				<?php endfor?>

			</td>
			<td class="pricetd"><?php echo number_format($R['price'])?>원</td>
			<td class="pointtd"><?php echo number_format($R['point'])?>원</td>
			<td class="numtd"><?php echo $C[1]?></td>
			<td class="pricesum"><?php echo number_format(($oprice+$opprice)*$C[1])?>원</td>
			<td class="halintd"><?php echo number_format($_smhprice)?>원</td>
		</tr>

		<?php $i++?>
		<?php $sprice+=($oprice+$opprice)*$C[1]?>
		<?php $gpoint+=($R['point']+$oppoint)*$C[1]?>
		<?php $shalin+=$_shprice*$C[1]?>
		<?php $mhalin+=$_mhprice*$C[1]?>
		<?php if($d['shop']['bundletack']){$_TACK[$R['vendor']]['price']+=($oprice+$opprice-$_smhprice)*$C[1];$_TACK[$R['vendor']]['free']+=$R['is_free'];}?>
		<?php endforeach?>

		<?php if(!$i):?>
		<?php getLink($g['s'].'/?r='.$r.'&m='.$m.'&mod=cart','','','')?>
		<?php endif?>

	</table>

	
	<br />
	<br />
	<br />


<?php
$shprice= $shalin;
$mhprice= $mhalin;
if ($d['shop']['bundletack'])
{
	foreach($_TACK as $_tkey)
	{
		$tprice1= getTprice($_tkey['price'],$d['shop']);
		$tprice2+= $_tkey['free'] || $tprice1 < 0 ? 0 : $tprice1;
	}
}
else {
	$tprice1= getTprice($sprice-($shalin+$mhalin),$d['shop']);
	$tprice2= $is_free || $tprice1 < 0 ? 0 : $tprice1;
}
?>

	<div class="t1"><span>01.</span>주문가격 정보</div>

	<div class="dbox0">
		<div class="sum1box">
			<div class="t"><img src="<?php echo $g['img_module_skin']?>/cart/arr_02.gif" alt="" /> 총 주문금액</div>
			<div class="sprice" id="tacktd0"><?php echo number_format($sprice+$tprice2)?><span>원</span></div>
			<table>
				<tr class="s1">
					<td><img src="<?php echo $g['img_module_skin']?>/cart/arr_01.gif" alt="" /> 상품가격</td>
					<td class="rg"><?php echo number_format($sprice)?>원</td>
				</tr>
				<tr class="s2">
					<td><img src="<?php echo $g['img_module_skin']?>/cart/arr_01.gif" alt="" /> 배송비</td>
					<td class="rg" id="tacktd1">
					<?php if ($d['shop']['bundletack']):?>
						<?php if($tprice2):?>
							<?php echo number_format($tprice2)?>원
						<?php else:?>
							<?php echo $d['shop']['tacktype']==3?$d['shop']['chackbulstr']:'무료배송'?>
						<?php endif?>
					<?php else:?>
						<?php if($is_free || $d['shop']['tacktype']==2):?>
							무료배송
						<?php else:?>
							<?php echo $d['shop']['tacktype']==3?$d['shop']['chackbulstr']:number_format($tprice2).'원'?>
						<?php endif?>
					<?php endif?>
					</td>
				</tr>
			</table>
		</div>
		<div class="halinbox">
			<div class="t"><img src="<?php echo $g['img_module_skin']?>/cart/arr_02.gif" alt="" /> 할인내역</div>
			<table>
				<tr>
					<td><img src="<?php echo $g['img_module_skin']?>/cart/arr_01.gif" alt="" /> 특별세일</td>
					<td>:</td>
					<td><?php echo number_format($shalin)?>원</td>
				</tr>
				<tr>
					<td><img src="<?php echo $g['img_module_skin']?>/cart/arr_01.gif" alt="" /> 회원할인</td>
					<td>:</td>
					<td><?php echo number_format($mhalin)?>원</td>
				</tr>
				<tr class="c">
					<td><img src="<?php echo $g['img_module_skin']?>/cart/arr_01.gif" alt="" /> 쿠폰할인</td>
					<td>:</td>
					<td>
						<input type="text" name="usecoupon" size="6" value="0" readonly="readonly"<?php if(!$my['uid']):?> disabled="disabled"<?php endif?> onkeyup="priceFormat(this);" onkeypress="priceFormat(this);" />원
						<img src="<?php echo $g['img_module_skin']?>/cart/btn_coupon.gif" alt="쿠폰보기/적용" class="upimg" onclick="useCoupon();" />
					</td>
				</tr>
				<tr class="c">
					<td><img src="<?php echo $g['img_module_skin']?>/cart/arr_01.gif" alt="" /> 적립금사용</td>
					<td>:</td>
					<td>
						<input type="text" name="usepoint" size="6" maxlength="7" value="0"<?php if(!$my['uid']):?> disabled="disabled"<?php endif?> onkeyup="priceFormat(this);" onkeypress="priceFormat(this);" />원
						<?php if(!$my['uid']):?>
						<span class="notmember">(비회원 사용불가)</span>
						<?php else:?>
						<img src="<?php echo $g['img_module_skin']?>/cart/btn_usepoint.gif" alt="적용" class="upimg" onclick="usePoint();" />
						<span class="myhavepoint">(<?php echo number_format($my['cash'])?>원보유중)</span>
						<?php endif?>
					</td>
				</tr>
			</table>
			<div class="ptment">
			<?php if($d['shop']['ptype']==1):?>
			적립금은 보유한도내에서 제한없이 사용할 수 있습니다.
			<?php elseif($d['shop']['ptype']==2):?>
			적립금은 <?php echo number_format(getRound($sprice*($d['shop']['point3']/100),'down',$d['shop']['jeolsa']))?>원내에서 사용할 수 있습니다.
			<?php else:?>
			적립금은 <?php echo number_format($d['shop']['point1'])?>~<?php echo number_format($d['shop']['point2'])?>원내에서 사용할 수 있습니다.
			<?php endif?>
			</div>
		</div>
		<div class="sum2box">
			<div class="t"><img src="<?php echo $g['img_module_skin']?>/cart/arr_02.gif" alt="" /> 총 결제금액</div>
			<div class="sprice" id="tprice_1"><?php echo number_format($sprice+$tprice2-($shalin+$mhalin+$chalin))?><span>원</span></div>
			<table>
				<tr class="s1">
					<td><img src="<?php echo $g['img_module_skin']?>/cart/arr_01.gif" alt="" /> 할인금액</td>
					<td class="rg">(-) <span id="uhalin_1"><?php echo number_format($shalin+$mhalin+$chalin)?></span>원</td>
				</tr>
				<tr class="s2">
					<td><img src="<?php echo $g['img_module_skin']?>/cart/arr_01.gif" alt="" /> 적립금사용</td>
					<td class="rg">(-) <span id="upoint_1">0</span>원</td>
				</tr>
			</table>
		</div>
		<div class="clear"></div>
	</div>


<?php
$tel1 = explode('-',$my['tel1']);
$tel2 = explode('-',$my['tel2']);
?>
	<div class="dbox1">
		
		<div class="omanbox">
			<div class="t1"><span>02.</span>주문하시는 분</div>
			<div class="omanbox1">
				<table>
					<tr>
						<td><img src="<?php echo $g['img_module_skin']?>/cart/arr_01.gif" alt="" /> 주문자</td>
						<td class="sx">|</td>
						<td><input type="text" name="o_name" size="15" value="<?php echo $my['name']?>" /></td>
					</tr>
					<tr>
						<td><img src="<?php echo $g['img_module_skin']?>/cart/arr_01.gif" alt="" /> 주소</td>
						<td class="sx">|</td>
						<td>
							<div>
							<input type="text" name="o_zip1" id="o_zip1" size="3" value="<?php echo substr($my['zip'],0,3)?>" readonly="readonly" /> - <input type="text" name="o_zip2" id="o_zip2" size="3" value="<?php echo substr($my['zip'],3,3)?>" readonly="readonly" /> 
							<img src="<?php echo $g['img_module_skin']?>/cart/btn_zip.gif" class="zipbtn" alt="우편번호찾기" onclick="OpenWindow('<?php echo $g['s']?>/?r=<?php echo $r?>&m=zipsearch&zip1=o_zip1&zip2=o_zip2&addr1=o_addr1&focusfield=o_addr2');" />
							</div>
							<div><input type="text" name="o_addr1" id="o_addr1" size="35" value="<?php echo $my['addr0']?> <?php echo $my['addr1']?>" readonly="readonly" /></div>
							<div><input type="text" name="o_addr2" id="o_addr2" size="35" value="<?php echo $my['addr2']?>" /></div>
						</td>
					</tr>
					<tr>
						<td><img src="<?php echo $g['img_module_skin']?>/cart/arr_01.gif" alt="" /> 이메일</td>
						<td class="sx">|</td>
						<td><input type="text" name="o_email" size="35" value="<?php echo $my['email']?>" /></td>
					</tr>
					<tr>
						<td><img src="<?php echo $g['img_module_skin']?>/cart/arr_01.gif" alt="" /> 전화번호</td>
						<td class="sx">|</td>
						<td>
							<input type="text" name="o_tel11" size="4" value="<?php echo $tel1[0]?>" /> - <input type="text" name="o_tel12" size="4" value="<?php echo $tel1[1]?>" /> - <input type="text" name="o_tel13" size="4" value="<?php echo $tel1[2]?>" />
						</td>
					</tr>
					<tr>
						<td><img src="<?php echo $g['img_module_skin']?>/cart/arr_01.gif" alt="" /> 휴대폰번호</td>
						<td class="sx">|</td>
						<td>
							<input type="text" name="o_tel21" size="4" value="<?php echo $tel2[0]?>" /> - <input type="text" name="o_tel22" size="4" value="<?php echo $tel2[1]?>" /> - <input type="text" name="o_tel23" size="4" value="<?php echo $tel2[2]?>" />
						</td>
					</tr>
				</table>
				<?php if($my['uid']):?>
				<div class="infomodify">
					<img src="<?php echo $g['img_module_skin']?>/cart/arr_01.gif" alt="" /> 지금 위의 내용으로 회원정보를 변경합니다.
					<img src="<?php echo $g['img_module_skin']?>/cart/btn_infomodify.gif" class="zipbtn" alt="회원정보반영" onclick="myInfoUpdate();" />
				</div>
				<?php endif?>

			</div>
		</div>
		
		<div class="rmanbox">
			<div class="t1"><span>03.</span>받으실 분</div>
			<div class="rmanbox1">
				<table>
					<?php if($my['uid']):?>
					<tr>
						<td><img src="<?php echo $g['img_module_skin']?>/cart/arr_01.gif" alt="" /> 배송지확인</td>
						<td class="sx">|</td>
						<td>
							<input type="checkbox" name="new_addr" value="Y" class="ck" onclick="newAddr(this);" />새로운 주소
							<img src="<?php echo $g['img_module_skin']?>/cart/btn_myaddr.gif" class="zipbtn" alt="나의주소록" onclick="myAddrBook();" />
						</td>
					</tr>
					<?php else:?>
					<tr>
						<td><img src="<?php echo $g['img_module_skin']?>/cart/arr_01.gif" alt="" /> 배송지확인</td>
						<td class="sx">|</td>
						<td>
							<input type="checkbox" name="same_addr" value="Y" class="ck" onclick="newAddr1(this);" />주문자와 동일
						</td>
					</tr>
					<?php endif?>
					<tr>
						<td><img src="<?php echo $g['img_module_skin']?>/cart/arr_01.gif" alt="" /> 받으실분</td>
						<td class="sx">|</td>
						<td><input type="text" name="r_name" size="15" value="<?php echo $my['name']?>" /></td>
					</tr>
					<tr>
						<td><img src="<?php echo $g['img_module_skin']?>/cart/arr_01.gif" alt="" /> 주소</td>
						<td class="sx">|</td>
						<td>
							<div><input type="text" name="r_zip1" id="r_zip1" size="3" value="<?php echo substr($my['zip'],0,3)?>" readonly="readonly" /> - <input type="text" name="r_zip2" id="r_zip2" size="3" value="<?php echo substr($my['zip'],3,3)?>" readonly="readonly" /> 
							<img src="<?php echo $g['img_module_skin']?>/cart/btn_zip.gif" class="zipbtn" alt="우편번호찾기" onclick="OpenWindow('<?php echo $g['s']?>/?r=<?php echo $r?>&m=zipsearch&zip1=r_zip1&zip2=r_zip2&addr1=r_addr1&focusfield=r_addr2');" />
							</div>
							<div><input type="text" name="r_addr1" id="r_addr1" size="35" value="<?php echo $my['addr0']?> <?php echo $my['addr1']?>" readonly="readonly" onchange="sTackCal();" /></div>
							<div><input type="text" name="r_addr2" id="r_addr2" size="35" value="<?php echo $my['addr2']?>" onblur="sTackCal();" /></div>
						</td>
					</tr>
					<tr>
						<td><img src="<?php echo $g['img_module_skin']?>/cart/arr_01.gif" alt="" /> 전화번호</td>
						<td class="sx">|</td>
						<td>
							<input type="text" name="r_tel11" size="4" value="<?php echo $tel1[0]?>" /> - <input type="text" name="r_tel12" size="4" value="<?php echo $tel1[1]?>" /> - <input type="text" name="r_tel13" size="4" value="<?php echo $tel1[2]?>" />
						</td>
					</tr>
					<tr>
						<td><img src="<?php echo $g['img_module_skin']?>/cart/arr_01.gif" alt="" /> 휴대폰번호</td>
						<td class="sx">|</td>
						<td>
							<input type="text" name="r_tel21" size="4" value="<?php echo $tel2[0]?>" /> - <input type="text" name="r_tel22" size="4" value="<?php echo $tel2[1]?>" /> - <input type="text" name="r_tel23" size="4" value="<?php echo $tel2[2]?>" />
						</td>
					</tr>
					<tr>
						<td><img src="<?php echo $g['img_module_skin']?>/cart/arr_01.gif" alt="" /> 전하는말</td>
						<td class="sx">|</td>
						<td><input type="text" name="msg" size="35" value="" /></td>
					</tr>
				</table>
				<?php if($d['shop']['stack']):?>
				<div class="stackment">* 특수지역(도서/산간)은 배송비가 추가됩니다.</div>
				<?php endif?>

			</div>
		</div>
		<div class="clear"></div>
	</div>




	<div class="dbox1">

		<div class="t1"><span>04.</span>결제정보 입력</div>

		<div class="ckindbox">
			<div class="t2">결제방식 선택</div>
			<div><input type="radio" name="ckind" id="rx1" value="1" checked="checked" onclick="payTypeSelect('bank');" /><label for="rx1">무통장입금</label></div>
			<?php if($d['shop']['card']&&!$is_cash):?><div><input type="radio" name="ckind" id="rx2" value="2" onclick="payTypeSelect('card');" /><label for="rx2">신용카드</label></div><?php endif?>
			<?php if($d['shop']['virt']&&!$is_cash):?><div><input type="radio" name="ckind" id="rx3" value="3" onclick="payTypeSelect('virt');" /><label for="rx3">실시간계좌이체</label></div><?php endif?>
			<?php if($d['shop']['ziro']&&!$is_cash):?><div><input type="radio" name="ckind" id="rx4" value="4" onclick="payTypeSelect('ziro');" /><label for="rx4">가상계좌</label></div><?php endif?>
			<?php if($d['shop']['phone']&&!$is_cash):?><div><input type="radio" name="ckind" id="rx5" value="5" onclick="payTypeSelect('phone');" /><label for="rx5">핸드폰</label></div><?php endif?>

		</div>

		<div class="cinfobox">
			<div class="t2">결제정보</div>

			<div id="paytype_bank">
				<table>
					<tr>
						<td class="g"><img src="<?php echo $g['img_module_skin']?>/cart/arr_01.gif" alt="" /> 결제방식</td>
						<td class="sx">|</td>
						<td>무통장입금</td>
					</tr>
					<tr>
						<td class="g"><img src="<?php echo $g['img_module_skin']?>/cart/arr_01.gif" alt="" /> 입금액</td>
						<td class="sx">|</td>
						<td id="tprice_2"><?php echo number_format($sprice+$tprice2-($shalin+$mhalin+$chalin))?>원</td>
					</tr>
					<?php if($d['shop']['useescr']):?>
					<tr>
						<td class="g"><img src="<?php echo $g['img_module_skin']?>/cart/arr_01.gif" alt="" /> 에스크로</td>
						<td class="sx">|</td>
						<td><input type="checkbox" name="escr" class="r" value="1"<?php if($sprice+$tprice2-($shalin+$mhalin+$chalin) < 100000):?> disabled="disabled"<?php else:?> onclick="escrCheck(this);"<?php endif?> />에스크로(중개거래) 서비스 사용</td>
					</tr>
					<?php endif?>
				</table>
			</div>

			<div id="paytype_card">
				<table>
					<tr>
						<td class="g"><img src="<?php echo $g['img_module_skin']?>/cart/arr_01.gif" alt="" /> 결제방식</td>
						<td class="sx">|</td>
						<td>신용카드</td>
					</tr>
					<tr>
						<td class="g"><img src="<?php echo $g['img_module_skin']?>/cart/arr_01.gif" alt="" /> 결제액</td>
						<td class="sx">|</td>
						<td id="tprice_3"><?php echo number_format($sprice+$tprice2-($shalin+$mhalin+$chalin))?>원</td>
					</tr>
				</table>
			</div>

			<div id="paytype_virt">
				<table>
					<tr>
						<td class="g"><img src="<?php echo $g['img_module_skin']?>/cart/arr_01.gif" alt="" /> 결제방식</td>
						<td class="sx">|</td>
						<td>실시간 계좌이체</td>
					</tr>
					<tr>
						<td class="g"><img src="<?php echo $g['img_module_skin']?>/cart/arr_01.gif" alt="" /> 결제액</td>
						<td class="sx">|</td>
						<td id="tprice_4"><?php echo number_format($sprice+$tprice2-($shalin+$mhalin+$chalin))?>원</td>
					</tr>
				</table>
			</div>

			<div id="paytype_ziro">
				<table>
					<tr>
						<td class="g"><img src="<?php echo $g['img_module_skin']?>/cart/arr_01.gif" alt="" /> 결제방식</td>
						<td class="sx">|</td>
						<td>가상계좌</td>
					</tr>
					<tr>
						<td class="g"><img src="<?php echo $g['img_module_skin']?>/cart/arr_01.gif" alt="" /> 결제액</td>
						<td class="sx">|</td>
						<td id="tprice_4"><?php echo number_format($sprice+$tprice2-($shalin+$mhalin+$chalin))?>원</td>
					</tr>
				</table>
			</div>

			<div id="paytype_phone">
				<table>
					<tr>
						<td class="g"><img src="<?php echo $g['img_module_skin']?>/cart/arr_01.gif" alt="" /> 결제방식</td>
						<td class="sx">|</td>
						<td>핸드폰결제</td>
					</tr>
					<tr>
						<td class="g"><img src="<?php echo $g['img_module_skin']?>/cart/arr_01.gif" alt="" /> 결제액</td>
						<td class="sx">|</td>
						<td id="tprice_4"><?php echo number_format($sprice+$tprice2-($shalin+$mhalin+$chalin))?>원</td>
					</tr>
				</table>
			</div>

					
			<div id="paytype_cx">
				<table>
					<tr id="banktr">
						<td class="g"><img src="<?php echo $g['img_module_skin']?>/cart/arr_01.gif" alt="" /> 입금계좌</td>
						<td class="sx">|</td>
						<td>
							<select name="bank">
							<option value="">- 선택하세요 -</option>
							<?php $banklist = explode('|',trim($d['shop']['bank']))?>
							<?php foreach($banklist as $val):if(!trim($val))continue;$val=str_replace(',',' ',$val)?>
							<option value="<?php echo $val?>"><?php echo $val?></option>
							<?php endforeach?>
							</select>
						</td>
					</tr>
					<tr>
						<td class="g"><img src="<?php echo $g['img_module_skin']?>/cart/arr_01.gif" alt="" /> 입금자명</td>
						<td class="sx">|</td>
						<td><input type="text" name="b_name" size="15" value="<?php echo $my['name']?>" /> <span class="small">실제입금자명</span></td>
					</tr>
				</table>
			</div>

			<div id="paytype_tax">
				<table>
					<?php if($d['shop']['use_cash']||$d['shop']['use_tax']):?>
					<tr>
						<td class="g"><img src="<?php echo $g['img_module_skin']?>/cart/arr_01.gif" alt="" /> 영수증</td>
						<td class="sx">|</td>
						<td class="radiotd">
							<input type="radio" name="taxtype" class="r" value="0" checked="checked" onclick="taxF(0);" />받지않음<br />
							<?php if($d['shop']['use_cash']):?><input type="radio" name="taxtype" class="r" value="1" onclick="taxF(1);" />개인 소득공제용(현금영수증)<br /><?php endif?>
							<?php if($d['shop']['use_tax']):?><input type="radio" name="taxtype" class="r" value="2" onclick="taxF(2);" />사업자 지출 증빙용(세금계산서)<?php endif?>
						</td>
					</tr>
					<tr id="taxT1">
						<td class="g"><img src="<?php echo $g['img_module_skin']?>/cart/arr_01.gif" alt="" /> 식별번호</td>
						<td class="sx">|</td>
						<td><input type="text" name="taxnum1" size="15" value="" /> <span class="small">휴대폰이나 주민번호</span></td>
					</tr>
					<tr id="taxT2">
						<td class="g"><img src="<?php echo $g['img_module_skin']?>/cart/arr_01.gif" alt="" /> 식별번호</td>
						<td class="sx">|</td>
						<td><input type="text" name="taxnum2" id="taxnumcode" size="15" value="" readonly="readonly" onclick="getCompInfo('taxnumcode');" /> <img src="<?php echo $g['img_module_skin']?>/cart/btn_compinfo.gif" alt="사업자정보" class="hand" onclick="getCompInfo('taxnumcode');" /></td>
					</tr>
					<?php endif?>

				</table>
			</div>

	
			<div id="guide_bank" class="guide">
				* 기간내에 입금되지 않으면 주문은 취소처리됩니다.<br />
				<?php if($d['shop']['useescr']):?>
				* 에스크로 서비스는 10만원 이상일 경우 해당됩니다.<br />
				<?php endif?>
			</div>
			<div id="guide_card" class="guide">
				* 결제하기 버튼을 클릭하면 결제창이 뜹니다.<br />
				* 결제창을 중간에 닫으면 주문은 접수되지 않습니다.<br />
			</div>
			<div id="guide_virt" class="guide">
				* 결제하기 버튼을 클릭하면 계좌이체창이 뜹니다.<br />
				* 결제창을 중간에 닫으면 주문은 접수되지 않습니다.<br />
			</div>
			<div id="guide_ziro" class="guide">
				* 결제하기 버튼을 클릭하면 가상계좌창이 뜹니다.<br />
				* 기간내에 입금되지 않으면 주문은 취소처리됩니다.<br />
			</div>
			<div id="guide_phone" class="guide">
				* 결제하기 버튼을 클릭하면 핸드폰 결제창이 뜹니다.<br />
				* 결제창을 중간에 닫으면 주문은 접수되지 않습니다.<br />
			</div>
		</div>

		<div class="finishbox">
			<div class="finishbox1">

				<div class="t2">최종 결제금액</div>

				<table>
					<tr class="s2 b">
						<td><img src="<?php echo $g['img_module_skin']?>/cart/arr_01.gif" alt="" /> 상품가격</td>
						<td class="rg"><b><?php echo number_format($sprice)?>원</b></td>
					</tr>
					<tr class="s2">
						<td><img src="<?php echo $g['img_module_skin']?>/cart/arr_01.gif" alt="" /> 배송비</td>
						<td class="rg" id="tacktd2">(+) <?php echo number_format($tprice2)?>원</td>
					</tr>
					<tr class="s2">
						<td><img src="<?php echo $g['img_module_skin']?>/cart/arr_01.gif" alt="" /> 할인금액</td>
						<td class="rg">(-) <span id="uhalin_2"><?php echo number_format($shalin+$mhalin+$chalin)?></span>원</td>
					</tr>
					<tr class="s2">
						<td><img src="<?php echo $g['img_module_skin']?>/cart/arr_01.gif" alt="" /> 적립금사용</td>
						<td class="rg">(-) <span id="upoint_2">0</span>원</td>
					</tr>
					<tr class="s1">
						<td><img src="<?php echo $g['img_module_skin']?>/cart/arr_01.gif" alt="" /> 최종결제액</td>
						<td class="rg"><span class="red" id="tprice_5"><?php echo number_format($sprice+$tprice2-($shalin+$mhalin+$chalin))?>원</span></td>
					</tr>
					<tr class="s3">
						<td><img src="<?php echo $g['img_module_skin']?>/cart/arr_01.gif" alt="" /> <span class="rg1">적립금지급</span></td>
						<td class="rg"><?php echo number_format($gpoint)?>원</td>
					</tr>
				</table>

			</div>
			<div class="btnbox">
				<a href="<?php echo $g['shop_reset']?>&amp;mod=cart"><img src="<?php echo $g['img_module_skin']?>/cart/btn_cart.gif" alt="장바구니로 가기" /></a>
				<br />
				<?php if($d['shop']['pay_order']==2):?>
		  		   <input type="image" src="<?php echo $g['img_module_skin']?>/cart/btn_order2.gif" alt="주문하기" />			
		       <?php else:?>
                 <input type="image" src="<?php echo $g['img_module_skin']?>/cart/btn_pay.gif" alt="결제하기" />
		       <?php endif?> 
			
			</div>
		</div>
		<div class="clear"></div>
	</div>


</div>



<input type="hidden" name="price_sum" value="<?php echo $sprice?>" />
<input type="hidden" name="price_tack" value="<?php echo $tprice2?>" />
<input type="hidden" name="price_stack" value="0" />
<input type="hidden" name="price_shalin" value="<?php echo $shalin?>" />
<input type="hidden" name="price_mhalin" value="<?php echo $mhalin?>" />
<input type="hidden" name="payType" value="bank" />
<input type="hidden" name="parentcoupon" value="0" />
<input type="hidden" name="uidcoupon" value="0" />
<input type="hidden" name="tid" value="" />
<input type="hidden" name="escrbank" value="" />
<input type="hidden" name="virt_bank" value="" />

<input type="hidden" name="c_name" value="" />
<input type="hidden" name="c_ceo" value="" />
<input type="hidden" name="c_num" value="" />
<input type="hidden" name="c_upte" value="" />
<input type="hidden" name="c_jongmok" value="" />
<input type="hidden" name="c_zip" value="" />
<input type="hidden" name="c_addr1" value="" />
<input type="hidden" name="c_addr2" value="" />

</form>

<script type="text/javascript">
//<![CDATA[
var jeolsaN = parseInt("<?php echo $d['shop']['jeolsa']?>");
var couponN = parseInt("<?php echo $d['shop']['coupontype']?>");
function getCompInfo(code)
{
	OpenWindow(rooturl + '/?r='+raccount+'&m='+moduleid+'&mod=company&layer='+code);
}
function myAddrBook()
{
	if (memberid == '')
	{
		alert('나의 주소록은 회원으로 로그인하셔야 이용하실 수 있습니다.    ');
		return false;
	}
	OpenWindow(rooturl + '/?r='+raccount+'&m='+moduleid+'&mod=address');
}
function useCoupon()
{
	if (memberid == '')
	{
		alert('쿠폰은 회원으로 로그인하셔야 이용하실 수 있습니다.    ');
		return false;
	}
	OpenWindow(rooturl + '/?r='+raccount+'&m='+moduleid+'&mod=coupon');
}
function escrCheck(obj)
{
	if (obj.checked == true)
	{
		getId('paytype_tax').style.display = 'none';
		getId('banktr').style.display = 'none';
		document.orderform.bank.value = '';
	}
	else {
		getId('paytype_tax').style.display = '';
		getId('banktr').style.display = '';
	}
}
function myInfoUpdate()
{
	var f = document.orderform;

	if (memberid == '')
	{
		alert('회원정보반영은 회원으로 로그인하셔야 이용하실 수 있습니다.    ');
		return false;
	}
	if (confirm('위의 정보로 회원정보를 변경하시겠습니까?'))
	{
		f.a.value = 'member_order_update';
		f.submit();
		f.a.value = 'order_regis';
	}
}
function sTackCal()
{
	var usest = parseInt("<?php echo $d['shop']['stack']?1:0?>");
	var chakbul = <?php echo $d['shop']['tacktype']?>;
	var tstr = "<?php echo $d['shop']['usestack']?>";
	var f = document.orderform;
	var i,j;
	var k = false;

	if (usest == 1)
	{
		if (chakbul != 3)
		{
			var tarr = tstr.split('|');
			var val,rg;
			for (i = 0; i < tarr.length; i++)
			{
				if (tarr[i] == '') continue;
				val = tarr[i].split('=');
				rg = val[0].split(',');

				for (j = 0; j < rg.length; j++)
				{
					if (rg[j] == '') continue;

					if (f.r_addr1.value && f.r_addr1.value.indexOf(rg[j]) != -1)
					{
						f.price_stack.value = val[1];
						k = true;
						break;
					}
				}
			}
		}

		if (f.r_addr1.value == '' || k == false) f.price_stack.value = 0;
		var tp = parseInt(f.price_tack.value) + parseInt(f.price_stack.value);

		getId('tacktd0').innerHTML = commaSplit(parseInt(f.price_sum.value) + tp) + '<span>원</span>';
		getId('tacktd1').innerHTML = tp > 0 ? commaSplit(tp) + '원' : (chakbul != 3 ? '무료배송' : '<?php echo $d['shop']['chackbulstr']?>');
		getId('tacktd2').innerHTML = '(+) ' + commaSplit(tp) + '원';

		getOrignBack();
	}
}
function usePoint()
{
	if (memberid == '')
	{
		alert('적립금은 회원으로 로그인하셔야 이용하실 수 있습니다.    ');
		return false;
	}

	var f = document.orderform;
	var tp1 = getId('tprice_1');
	var tp2 = getId('tprice_2');
	var tp3 = getId('tprice_3');
	var tp4 = getId('tprice_4');
	var tp5 = getId('tprice_5');
	var up1 = getId('upoint_1');
	var up2 = getId('upoint_2');
	var uh1 = getId('uhalin_1');
	var uh2 = getId('uhalin_2');

	var total = (parseInt(f.price_sum.value) + parseInt(f.price_tack.value) + parseInt(f.price_stack.value)) - (parseInt(f.price_shalin.value) + parseInt(f.price_mhalin.value));
	var cprice= f.usecoupon.value != '' ? parseInt(filterNum(f.usecoupon.value)) : 0;
	var myUsePt  = f.usepoint.value != '' ? parseInt(filterNum(f.usepoint.value)) : 0;
	var myHavePt = parseInt("<?php echo $my['cash']?>");

	var pType = <?php echo $d['shop']['ptype']?>;
	var myLimitPt1 = parseInt("<?php echo $d['shop']['point1']?>");
	var myLimitPt2 = parseInt("<?php echo $d['shop']['point2']?>");
	var myLimitPt3 = parseInt("<?php echo getRound($sprice*($d['shop']['point3']/100),'down',$d['shop']['jeolsa'])?>");

	if (f.usepoint.value == '' || f.usepoint.value == '0')
	{
		alert('사용할 적립금을 입력해 주세요.    ');
		f.usepoint.value = '0';
		f.usepoint.focus();
		getOrignBack();
		return false;
	}

	if (myUsePt % <?php echo $d['shop']['pjeolsa']?> > 0)
	{
		alert('적립금은 <?php echo $d['shop']['pjeolsa']?>원단위로 사용할 수 있습니다.    ');
		f.usepoint.value = '0';
		f.usepoint.focus();
		getOrignBack();
		return false;
	}

	if (total-myUsePt-cprice < 0)
	{
		alert('적립금은 최대 ' +commaSplit(total-cprice)+ '원까지 사용가능합니다.     ');
		f.usepoint.value = '0';
		f.usepoint.focus();
		getOrignBack();
		return false;
	}

	if (pType == 2)
	{
		if (myUsePt > myLimitPt3)
		{
			alert('적립금은 최대 ' +commaSplit(myLimitPt3)+ '원까지 사용가능합니다.     ');
			f.usepoint.value = '0';
			f.usepoint.focus();
			getOrignBack();
			return false;
		}

	}
	if (pType == 3)
	{
		if (myUsePt < myLimitPt1)
		{
			alert('적립금은 최소 ' +commaSplit(myLimitPt1)+ '원부터 사용가능합니다.     ');
			f.usepoint.value = '0';
			f.usepoint.focus();
			getOrignBack();
			return false;
		}

		if (myUsePt > myLimitPt2)
		{
			alert('적립금은 최대 ' +commaSplit(myLimitPt2)+ '원까지 사용가능합니다.     ');
			f.usepoint.value = '0';
			f.usepoint.focus();
			getOrignBack();
			return false;
		}
	}

	if (myUsePt > myHavePt)
	{
		alert('적립금은 보유한도내에서 사용가능합니다.     ');
		f.usepoint.value = commaSplit(myHavePt);

		up1.innerHTML = commaSplit(myHavePt);
		up2.innerHTML = commaSplit(myHavePt);
		uh1.innerHTML = commaSplit(parseInt(f.price_shalin.value) + parseInt(f.price_mhalin.value)+cprice);
		uh2.innerHTML = commaSplit(parseInt(f.price_shalin.value) + parseInt(f.price_mhalin.value)+cprice);
		tp1.innerHTML = commaSplit(total-myHavePt-cprice) + '<span>원</span>';
		tp2.innerHTML = commaSplit(total-myHavePt-cprice) + '원';
		tp3.innerHTML = commaSplit(total-myHavePt-cprice) + '원';
		tp4.innerHTML = commaSplit(total-myHavePt-cprice) + '원';
		tp5.innerHTML = commaSplit(total-myHavePt-cprice) + '원';
		f.usepoint.focus();
		getOrignBack();
		return false;
	}

	up1.innerHTML = commaSplit(myUsePt);
	up2.innerHTML = commaSplit(myUsePt);
	uh1.innerHTML = commaSplit(parseInt(f.price_shalin.value) + parseInt(f.price_mhalin.value)+cprice);
	uh2.innerHTML = commaSplit(parseInt(f.price_shalin.value) + parseInt(f.price_mhalin.value)+cprice);
	tp1.innerHTML = commaSplit(total-myUsePt-cprice) + '<span>원</span>';
	tp2.innerHTML = commaSplit(total-myUsePt-cprice) + '원';
	tp3.innerHTML = commaSplit(total-myUsePt-cprice) + '원';
	tp4.innerHTML = commaSplit(total-myUsePt-cprice) + '원';
	tp5.innerHTML = commaSplit(total-myUsePt-cprice) + '원';
}
function getOrignBack()
{
	var f = document.orderform;

	var tp1 = getId('tprice_1');
	var tp2 = getId('tprice_2');
	var tp3 = getId('tprice_3');
	var tp4 = getId('tprice_4');
	var tp5 = getId('tprice_5');
	var up1 = getId('upoint_1');
	var up2 = getId('upoint_2');
	var uh1 = getId('uhalin_1');
	var uh2 = getId('uhalin_2');

	var total = (parseInt(f.price_sum.value) + parseInt(f.price_tack.value) + parseInt(f.price_stack.value)) - (parseInt(f.price_shalin.value) + parseInt(f.price_mhalin.value));
	var cprice= f.usecoupon.value != '' ? parseInt(filterNum(f.usecoupon.value)) : 0;
	var myUsePt = f.usepoint.value != '' ? parseInt(filterNum(f.usepoint.value)) : 0;

	up1.innerHTML = commaSplit(myUsePt);
	up2.innerHTML = commaSplit(myUsePt);
	uh1.innerHTML = commaSplit(parseInt(f.price_shalin.value) + parseInt(f.price_mhalin.value)+cprice);
	uh2.innerHTML = commaSplit(parseInt(f.price_shalin.value) + parseInt(f.price_mhalin.value)+cprice);
	tp1.innerHTML = commaSplit(total-myUsePt-cprice) + '<span>원</span>';
	tp2.innerHTML = commaSplit(total-myUsePt-cprice) + '원';
	tp3.innerHTML = commaSplit(total-myUsePt-cprice) + '원';
	tp4.innerHTML = commaSplit(total-myUsePt-cprice) + '원';
	tp5.innerHTML = commaSplit(total-myUsePt-cprice) + '원';
}
function taxF(n)
{
	var f = document.orderform;
	if (n == 0)
	{
		getId('taxT1').style.visibility = 'hidden';
		getId('taxT2').style.visibility = 'hidden';
		getId('taxT1').style.position = 'absolute';
		getId('taxT2').style.position = 'absolute';
		f.taxnum1.value = '';
	}
	if (n == 1)
	{

		var total = (parseInt(f.price_sum.value) + parseInt(f.price_tack.value) + parseInt(f.price_stack.value)) - (parseInt(f.price_shalin.value) + parseInt(f.price_mhalin.value));

		if (total < 5000)
		{
			alert('현금영수증은 결제금액이 5,000원 이상이어야 받으실 수 있습니다.');
			f.taxtype[0].checked = true;
			taxF(0);
		}
		else {
			getId('taxT1').style.visibility = 'visible';
			getId('taxT2').style.visibility = 'hidden';
			getId('taxT1').style.position = 'relative';
			getId('taxT2').style.position = 'absolute';
			if(f.o_tel21.value && f.o_tel22.value && f.o_tel23.value) f.taxnum1.value = f.o_tel21.value +'-' + f.o_tel22.value +'-' + f.o_tel23.value;
		}
	}	
	if (n == 2)
	{
		getId('taxT1').style.visibility = 'hidden';
		getId('taxT2').style.visibility = 'visible';
		getId('taxT1').style.position = 'absolute';
		getId('taxT2').style.position = 'relative';
		f.taxnum1.value = '';
	}
}
function newAddr(obj)
{
	var f = document.orderform;
	if (obj.checked == true)
	{
		f.r_name.value = '';
		f.r_zip1.value = '';
		f.r_zip2.value = '';
		f.r_addr1.value = '';
		f.r_addr2.value = '';
		f.r_tel11.value = '';
		f.r_tel12.value = '';
		f.r_tel13.value = '';
		f.r_tel21.value = '';
		f.r_tel22.value = '';
		f.r_tel23.value = '';
	}
	else {
		f.r_name.value = f.o_name.value;
		f.r_zip1.value = f.o_zip1.value;
		f.r_zip2.value = f.o_zip2.value;
		f.r_addr1.value = f.o_addr1.value;
		f.r_addr2.value = f.o_addr2.value;
		f.r_tel11.value = f.o_tel11.value;
		f.r_tel12.value = f.o_tel12.value;
		f.r_tel13.value = f.o_tel13.value;
		f.r_tel21.value = f.o_tel21.value;
		f.r_tel22.value = f.o_tel22.value;
		f.r_tel23.value = f.o_tel23.value;
	}
	sTackCal();
}
function newAddr1(obj)
{
	var f = document.orderform;
	if (obj.checked == false)
	{
		f.r_name.value = '';
		f.r_zip1.value = '';
		f.r_zip2.value = '';
		f.r_addr1.value = '';
		f.r_addr2.value = '';
		f.r_tel11.value = '';
		f.r_tel12.value = '';
		f.r_tel13.value = '';
		f.r_tel21.value = '';
		f.r_tel22.value = '';
		f.r_tel23.value = '';
	}
	else {
		f.r_name.value = f.o_name.value;
		f.r_zip1.value = f.o_zip1.value;
		f.r_zip2.value = f.o_zip2.value;
		f.r_addr1.value = f.o_addr1.value;
		f.r_addr2.value = f.o_addr2.value;
		f.r_tel11.value = f.o_tel11.value;
		f.r_tel12.value = f.o_tel12.value;
		f.r_tel13.value = f.o_tel13.value;
		f.r_tel21.value = f.o_tel21.value;
		f.r_tel22.value = f.o_tel22.value;
		f.r_tel23.value = f.o_tel23.value;
	}
	sTackCal();
}
function payTypeSelect(type)
{
	var f = document.orderform;

	getId('paytype_bank').style.display = 'none';
	getId('paytype_card').style.display = 'none';
	getId('paytype_virt').style.display = 'none';
	getId('paytype_ziro').style.display = 'none';
	getId('paytype_phone').style.display = 'none';

	getId('guide_bank').style.display = 'none';
	getId('guide_card').style.display = 'none';
	getId('guide_virt').style.display = 'none';
	getId('guide_ziro').style.display = 'none';
	getId('guide_phone').style.display = 'none';

	getId('paytype_cx').style.display = 'none';

	getId('paytype_'+type).style.display = 'block';
	getId('guide_'+type).style.display = 'block';

	f.payType.value = type;

	if (type == 'bank')
	{
		getId('paytype_cx').style.display = 'block';

		if (f.escr && f.escr.checked == true)
		{
			getId('paytype_tax').style.display = 'none';
			getId('banktr').style.display = 'none';
		}
		else {
			getId('paytype_tax').style.display = 'block';
			getId('banktr').style.display = '';
		}

	}
	if (type == 'virt')
	{
		getId('paytype_tax').style.display = 'none';
		getId('paytype_cx').style.display = 'block';
		getId('banktr').style.display = '';
	}
	if (type == 'card'||type == 'ziro'||type == 'phone')
	{
		getId('paytype_tax').style.display = 'none';
		getId('paytype_cx').style.display = 'none';
		getId('banktr').style.display = 'none';
	}
}
function ordercheck(f)
{
	var cprice= f.usecoupon.value != '' ? parseInt(filterNum(f.usecoupon.value)) : 0;
	var cpoint= f.usepoint.value != '' ? parseInt(filterNum(f.usepoint.value)) : 0;
	var myHavePt = parseInt("<?php echo $my['cash']?>");
	if (cpoint > myHavePt)
	{
		alert('보유중인 적립금보다 사용할 적립금이 많습니다.\n적립금사용 적용버튼을 클릭해 주세요.   ');
		f.usepoint.focus();
		return false;
	}
	var total = ((parseInt(f.price_sum.value) + parseInt(f.price_tack.value) + parseInt(f.price_stack.value)) - (parseInt(f.price_shalin.value) + parseInt(f.price_mhalin.value) + cprice + cpoint));
	var checkPrice = getJeolsa(total,jeolsaN);

	if (f.o_name.value == '')
	{
		alert('주문자 성함을 입력해 주세요.');
		f.o_name.focus();
		return false;
	}
	if (f.o_addr1.value == ' ')
	{
		alert('주문자 주소를 입력해 주세요.');
		f.o_addr2.focus();
		return false;
	}
	if (f.o_email.value == '')
	{
		alert('주문자 이메일을 입력해 주세요.');
		f.o_email.focus();
		return false;
	}
	if (f.o_tel11.value == '' || f.o_tel12.value == '' || f.o_tel13.value == '' )
	{
		alert('주문자 전화번호를 입력해 주세요.');
		f.o_tel11.focus();
		return false;
	}
	if (f.o_tel21.value == '' || f.o_tel22.value == '' || f.o_tel23.value == '' )
	{
		alert('주문자 휴대폰번호를 입력해 주세요.');
		f.o_tel21.focus();
		return false;
	}
	if (f.r_name.value == '')
	{
		alert('받으실분 성함을 입력해 주세요.');
		f.r_name.focus();
		return false;
	}
	if (f.r_addr1.value == ' ')
	{
		alert('받으실분 주소를 입력해 주세요.');
		f.r_addr2.focus();
		return false;
	}
	if (f.r_tel11.value == '' || f.r_tel12.value == '' || f.r_tel13.value == '' )
	{
		alert('받으실분 전화번호를 입력해 주세요.');
		f.r_tel11.focus();
		return false;
	}
	if (f.r_tel21.value == '' || f.r_tel22.value == '' || f.r_tel23.value == '' )
	{
		alert('받으실분 휴대폰번호를 입력해 주세요.');
		f.r_tel21.focus();
		return false;
	}

	if (f.payType.value == 'bank' || f.payType.value == 'virt')
	{
		if (!f.escr || (f.escr && f.escr.checked != true))
		{
			if (f.bank.value == '')
			{
				alert('입금계좌를 선택해 주세요.');
				f.bank.focus();
				return false;
			}
		}
		if (f.b_name.value == '')
		{
			alert('입금자 성함을 입력해 주세요.');
			f.b_name.focus();
			return false;
		}
		if (!f.escr || (f.escr && f.escr.checked != true))
		{
			<?php if($d['shop']['use_cash']):?>
			if (f.taxtype[1].checked == true)
			{
				if (f.taxnum1.value == '')
				{
					alert('휴대폰번호나 주민등록번호를 입력해 주세요.   ');
					f.taxnum1.focus();
					return false;
				}
			}
			<?php endif?>
			<?php if($d['shop']['use_tax']):?>
			if (f.taxtype[2].checked == true)
			{
				if (f.taxnum2.value == '')
				{
					alert('사업자 등록번호를 입력해 주세요.   ');
					f.taxnum2.focus();
					return false;
				}
			}
			<?php endif?>
		}
	}

	if (checkPrice > 0)
	{
		if (f.payType.value != 'bank' || (f.payType.value == 'bank' && (f.escr && f.escr.checked == true)))
		{
			<?php if($d['shop']['pay_order']==2):?>
		         return confirm('정말로 주문하시겠습니까?     ');	      
			<?php else:?>
			    <?php include_once $g['dir_module'].'pg/'.$d['shop']['pgcomp'].'/connect.php'?>
			<?php endif?>
			return false;
		}
	}
	
	return confirm('정말로 주문하시겠습니까?     ');
}
sTackCal();
//]]>
</script>

