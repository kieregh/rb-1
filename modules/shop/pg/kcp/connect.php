
var UriOfsendToPg = "<?php echo $g['url_root']?>/modules/<?php echo $m?>/pg/<?php echo $d['shop']['pgcomp']?>/kcp_cross_pay.php?";

UriOfsendToPg += "site_name=<?php echo $d['shop']['g_shopname']?>&";	//site 이름
UriOfsendToPg += "site_cd=<?php echo $d['shop']['pgid']?>&";			//상점아이디
UriOfsendToPg += "ordr_idxx=<?php echo $_SESSION['cartid']?>&";		//주문번호


if (f.payType.value == 'bank')
{
	if (f.escr.checked == true)
	{
		UriOfsendToPg += "shop_service_type=001000000000&"; //에스크로
	}
}
if (f.payType.value == 'card')
{
	UriOfsendToPg += "shop_service_type=100000000000&"; //신용카드
}
if (f.payType.value == 'virt')
{
	UriOfsendToPg += "shop_service_type=010000000000&"; //계좌이체
}
if (f.payType.value == 'ziro')
{
	UriOfsendToPg += "shop_service_type=001000000000&"; //가상계좌
}
if (f.payType.value == 'phone')
{
	UriOfsendToPg += "shop_service_type=000010000000&"; //핸드폰
}

UriOfsendToPg += "good_name=<?php echo strip_tags($R['name']).($i>1?'외 '.$i.'건':'')?>&";		//주문상품
UriOfsendToPg += "good_mny=" + checkPrice + "&";										//결제금액
UriOfsendToPg += "quotaopt=&";															//할부개월수
UriOfsendToPg += "good_cnt=1&";															//구매수량
UriOfsendToPg += "buyr_name="+ f.b_name.value +"&";										//주문자명
UriOfsendToPg += "buyr_tel1=" + f.o_tel11.value + "-" + f.o_tel12.value + "-" + f.o_tel13.value + "&";	//전화번호
UriOfsendToPg += "buyr_tel2=" + f.o_tel21.value + "-" + f.o_tel22.value + "-" + f.o_tel23.value + "&";	//휴대폰
UriOfsendToPg += "buyr_mail=" + f.o_email.value + "&";													//이메일
UriOfsendToPg += "soc_no=&";																			//주민번호

if (f.escr && f.escr.checked == true)
{
	UriOfsendToPg += "escw_used=Y&"; //에스크로사용여부 = 카드는 디폴트 N 
	UriOfsendToPg += "pay_mod=Y&"; //에스크로결제처리모드 에스크로: Y, 일반: N, KCP 설정 조건: O
}
else {
	UriOfsendToPg += "escw_used=N&"; //에스크로사용여부 = 카드는 디폴트 N 
	UriOfsendToPg += "pay_mod=O&"; //에스크로결제처리모드 에스크로: Y, 일반: N, KCP 설정 조건: O
}

UriOfsendToPg += "rcvr_name=" + f.r_name.value + "&";													//수취인 이름
UriOfsendToPg += "rcvr_tel1=" + f.r_tel11.value + "-" + f.r_tel12.value + "-" + f.r_tel13.value + "&";	//수취인 전화번호
UriOfsendToPg += "rcvr_tel2=" + f.r_tel21.value + "-" + f.r_tel22.value + "-" + f.r_tel23.value + "&";	//수취인 휴대전화
UriOfsendToPg += "rcvr_mail=&";																			//수취인 메일
UriOfsendToPg += "rcvr_zipx=" + f.r_zip1.value + f.r_zip2.value + "&";									//수취인 우편번호
UriOfsendToPg += "rcvr_add1=" + f.r_addr1.value + "&";													//수취인 주소1
UriOfsendToPg += "rcvr_add2=" + f.r_addr2.value + "&";													//수취인 주소2										
UriOfsendToPg += "pg_type=PGNW&";		//PG구분(PGNW로 셋팅)   
UriOfsendToPg += "currency=410&";		//통화코드(원화:410, 달러:840)
UriOfsendToPg += "Ret_URL=<?php echo $g['url_root']?>/modules/<?php echo $m?>/pg/<?php echo $d['shop']['pgcomp']?>/result.php&";							//리턴url

//** 제품 정보 **//
var good_info = "";
good_info +=    "seq=" + "1" + "" +
						"ordr_numb=<?php echo $_SESSION['cartid']?>" + "" + 
                        "good_name=<?php echo strip_tags($R['name']).($i>1?'외 '.$i.'건':'')?>" + "" +
                        "good_cntx=" + "1" + "" +
                        "good_amtx=" + checkPrice + "";
UriOfsendToPg += "good_info=" + good_info + "";	//제품 정보
eval('frames._action_frame_'+moduleid).location.href = UriOfsendToPg;
