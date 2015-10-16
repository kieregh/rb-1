
var UriOfsendToPg = "<?php echo $g['url_root']?>/modules/<?php echo $m?>/pg/<?php echo $d['shop']['pgcomp']?>/kcp_cross_pay.php?";

UriOfsendToPg += "site_name=<?php echo $d['shop']['g_shopname']?>&";	//site 이름
UriOfsendToPg += "site_cd=<?php echo $d['shop']['pgid']?>&";			//상점아이디
UriOfsendToPg += "ordr_idxx="+orderid+"&";		//주문번호
UriOfsendToPg += "Ret_URL=<?php echo $g['url_root']?>/modules/<?php echo $m?>/pg/<?php echo $d['shop']['pgcomp']?>/result_payonly.php&";		//Return url 

if (payType == 'bank')
{
	if (escr== 1)
	{
		UriOfsendToPg += "shop_service_type=001000000000&"; //에스크로
	}
}
if (payType == 'card')
{
	UriOfsendToPg += "shop_service_type=100000000000&"; //신용카드
}
if (payType == 'virt')
{
	UriOfsendToPg += "shop_service_type=010000000000&"; //계좌이체
}
if (payType == 'ziro')
{
	UriOfsendToPg += "shop_service_type=001000000000&"; //가상계좌
}
if (payType == 'phone')
{
	UriOfsendToPg += "shop_service_type=000010000000&"; //핸드폰
}

UriOfsendToPg += "good_name="+g_name+g_name_detail+"&";		//주문상품
UriOfsendToPg += "good_mny=" + amount + "&";										//결제금액
UriOfsendToPg += "quotaopt=&";															//할부개월수
UriOfsendToPg += "good_cnt=1&";															//구매수량
UriOfsendToPg += "buyr_name="+ b_name +"&";										//주문자명
UriOfsendToPg += "buyr_tel1=" + o_tel1 + "&";	//전화번호
UriOfsendToPg += "buyr_tel2=" + o_tel2 + "&";	//휴대폰
UriOfsendToPg += "buyr_mail=" + o_email + "&";													//이메일
UriOfsendToPg += "soc_no=&";																			//주민번호

if (escr && escr == 1)
{
	UriOfsendToPg += "escw_used=Y&"; //에스크로사용여부 = 카드는 디폴트 N 
	UriOfsendToPg += "pay_mod=Y&"; //에스크로결제처리모드 에스크로: Y, 일반: N, KCP 설정 조건: O
}
else {
	UriOfsendToPg += "escw_used=N&"; //에스크로사용여부 = 카드는 디폴트 N 
	UriOfsendToPg += "pay_mod=O&"; //에스크로결제처리모드 에스크로: Y, 일반: N, KCP 설정 조건: O
}

UriOfsendToPg += "rcvr_name=" + r_name + "&";													//수취인 이름
UriOfsendToPg += "rcvr_tel1=" + r_tel1 + "&";	//수취인 전화번호
UriOfsendToPg += "rcvr_tel2=" + r_tel2 + "&";	//수취인 휴대전화
UriOfsendToPg += "rcvr_mail=&";																			//수취인 메일
UriOfsendToPg += "rcvr_zipx=" + r_zip + "&";									//수취인 우편번호
UriOfsendToPg += "rcvr_add1=" + r_addr1 + "&";													//수취인 주소1
UriOfsendToPg += "rcvr_add2=" + r_addr2 + "&";													//수취인 주소2										
UriOfsendToPg += "pg_type=PGNW&";		//PG구분(PGNW로 셋팅)   
UriOfsendToPg += "currency=410&";		//통화코드(원화:410, 달러:840)


//** 제품 정보 **//
var good_info = "";
good_info +=    "seq=" + "1" + "" +
						"ordr_numb="+orderid+ "" + 
                        "good_name="+g_name + g_name_detail+ "" +
                        "good_cntx=" + "1" + "" +
                        "good_amtx=" + amount + "";
UriOfsendToPg += "good_info=" + good_info + "";	//제품 정보
eval('frames._action_frame_'+moduleid).location.href = UriOfsendToPg;
