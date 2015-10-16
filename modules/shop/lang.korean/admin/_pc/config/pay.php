<?php if(!defined('__KIMS__')) exit?>

<form name="procForm" action="<?php echo $g['s']?>/" method="post" target="_action_frame_<?php echo $m?>" onsubmit="return saveCheck(this);">
<input type="hidden" name="r" value="<?php echo $r?>" />
<input type="hidden" name="m" value="<?php echo $module?>" />
<input type="hidden" name="a" value="config" />
<input type="hidden" name="type" value="<?php echo $type?>" />

<div class="title">
	<?php echo $configset[$type]?>
</div>



<table>
	<tr>
		<td class="td1">허용할 결제방식</td>
		<td class="td2">
			<input type="checkbox" checked="checked" disabled="disabled" />무통장입금 <br />
			<input type="checkbox" name="card" value="1"<?php if($d['shop']['card']):?> checked="checked"<?php endif?> />신용카드 <br />
			<input type="checkbox" name="virt" value="1"<?php if($d['shop']['virt']):?> checked="checked"<?php endif?> />실시간 계좌이체 <br />
			<input type="checkbox" name="ziro" value="1"<?php if($d['shop']['ziro']):?> checked="checked"<?php endif?> />가상계좌 <br />
			<input type="checkbox" name="phone" value="1"<?php if($d['shop']['phone']):?> checked="checked"<?php endif?> />핸드폰 <br />
			<div class="guide">(무통장입금외는 PG서비스를 이용하셔야 합니다.)</div>
			<input type="checkbox" name="card_mobile" value="1"<?php if($d['shop']['card_mobile']):?> checked="checked"<?php endif?> />신용카드(모바일) <br />
			<input type="checkbox" name="virt_mobile" value="1"<?php if($d['shop']['virt_mobile']):?> checked="checked"<?php endif?> />실시간 계좌이체(모바일) <br />
			<input type="checkbox" name="ziro_mobile" value="1"<?php if($d['shop']['ziro_mobile']):?> checked="checked"<?php endif?> />가상계좌(모바일) <br />
			<input type="checkbox" name="phone_mobile" value="1"<?php if($d['shop']['phone_mobile']):?> checked="checked"<?php endif?> />핸드폰(모바일) <br />
		</td>
	</tr>
	<tr>
		<td class="td1">결제액 절사단위</td>
		<td class="td2">
			<select name="jeolsa">
			<option value="1"<?php if($d['shop']['jeolsa']==1):?> selected="selected"<?php endif?>>1원단위 버림</option>
			<option value="10"<?php if($d['shop']['jeolsa']==10):?> selected="selected"<?php endif?>>10원단위 버림</option>
			<option value="100"<?php if($d['shop']['jeolsa']==100):?> selected="selected"<?php endif?>>100원단위 버림</option>
			</select>
			<div class="guide">(4,321원 결제시 10원단위 버릴경우 4,300원 결제)</div>
		</td>
	</tr>
	<tr>
		<td class="td1">적립금 사용설정</td>
		<td class="td2">
			<input type="radio" name="ptype" value="1"<?php if($d['shop']['ptype']==1):?> checked="checked"<?php endif?> />보유 적립금내에서 제한없이 허용합니다.
			<br />

			<input type="radio" name="ptype" value="2"<?php if($d['shop']['ptype']==2):?> checked="checked"<?php endif?> />
			상품총액의 <input type="text" name="point3" size="2" value="<?php echo $d['shop']['point3']?>" class="input" />%이내까지 적립금 사용을 허용합니다.
			<br />
			
			<input type="radio" name="ptype" value="3"<?php if($d['shop']['ptype']==3):?> checked="checked"<?php endif?> />
			<input type="text" name="point1" size="6" value="<?php echo $d['shop']['point1']?>" class="input" onkeydown="numFormat(this);" onkeypress="numFormat(this);" />원부터 
			<input type="text" name="point2" size="6" value="<?php echo $d['shop']['point2']?>" class="input" onkeydown="numFormat(this);" onkeypress="numFormat(this);" />원까지 적립금 사용을 허용합니다.
		</td>
	</tr>
	<tr>
		<td class="td1">적립금 사용단위</td>
		<td class="td2">
			<select name="pjeolsa">
			<option value="10"<?php if($d['shop']['pjeolsa']==10):?> selected="selected"<?php endif?>>10원단위</option>
			<option value="100"<?php if($d['shop']['pjeolsa']==100):?> selected="selected"<?php endif?>>100원단위</option>
			<option value="1000"<?php if($d['shop']['pjeolsa']==1000):?> selected="selected"<?php endif?>>1000원단위</option>
			</select>
			<div class="guide">(결제액 절사단위보다 한단위 높게 설정할 것을 권장합니다)</div>
		</td>
	</tr>
</table>


<div class="submitbox">
	<input type="submit" class="btnblue" value=" 확인 " />
</div>

</form>



<script type="text/javascript">
//<![CDATA[
function saveCheck(f)
{
	return confirm('정말로 실행하시겠습니까?        ');
}
//]]>
</script>
