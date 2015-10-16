
var UriOfsendToPg = "<?php echo $g['url_root']?>/modules/<?php echo $m?>/pg/<?php echo $d['shop']['pgcomp']?>/_mobile/sample/common/pp_ax_hub.php?";

UriOfsendToPg += "req_tx=pay&";	//요청구분
UriOfsendToPg += "ordr_idxx=<?php echo $_SESSION['cartid']?>&";		//주문번호
UriOfsendToPg += "good_mny=" + checkPrice + "&";										//결제금액
UriOfsendToPg += "good_name=<?php echo strip_tags($R['name']).($i>1?'외 '.$i.'건':'')?>&";		//주문상품
UriOfsendToPg += "buyr_name="+ f.b_name.value +"&";										//주문자명
UriOfsendToPg += "buyr_tel1=" + f.o_tel11.value + "-" + f.o_tel12.value + "-" + f.o_tel13.value + "&";	//전화번호
UriOfsendToPg += "buyr_tel2=" + f.o_tel21.value + "-" + f.o_tel22.value + "-" + f.o_tel23.value + "&";	//휴대폰
UriOfsendToPg += "buyr_mail=" + f.o_email.value + "&";													//이메일

UriOfsendToPg += "rcvr_name=" + f.r_name.value + "&";													//수취인 이름
UriOfsendToPg += "rcvr_tel1=" + f.r_tel11.value + "-" + f.r_tel12.value + "-" + f.r_tel13.value + "&";	//수취인 전화번호
UriOfsendToPg += "rcvr_tel2=" + f.r_tel21.value + "-" + f.r_tel22.value + "-" + f.r_tel23.value + "&";	//수취인 휴대전화
UriOfsendToPg += "rcvr_mail=&";																			//수취인 메일
UriOfsendToPg += "rcvr_zipx=" + f.r_zip1.value + f.r_zip2.value + "&";									//수취인 우편번호
UriOfsendToPg += "rcvr_add1=" + f.r_addr1.value + "&";													//수취인 주소1
UriOfsendToPg += "rcvr_add2=" + f.r_addr2.value + "&";													//수취인 주소2										

if (f.payType.value == 'card')
{
	UriOfsendToPg += "use_pay_method=100000000000&"; //신용카드
}
if (f.payType.value == 'virt')
{
	UriOfsendToPg += "use_pay_method=010000000000&"; //계좌이체
}
if (f.payType.value == 'ziro')
{
	UriOfsendToPg += "use_pay_method=001000000000&"; //가상계좌
}
if (f.payType.value == 'phone')
{
	UriOfsendToPg += "use_pay_method=000010000000&"; //핸드폰
}

eval('frames._action_frame_'+moduleid).location.href = UriOfsendToPg;

