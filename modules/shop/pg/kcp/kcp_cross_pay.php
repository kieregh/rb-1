<?php
extract($_GET);
extract($_POST);
extract($_SERVER);

if (is_array($_GET))
{
	foreach($_GET as $_tmp['k'] => $_tmp['v'])
	{
		if (is_array($_GET[$_tmp['k']]))
		{
			foreach($_GET[$_tmp['k']] as $_tmp['k1'] => $_tmp['v1'])
			{
				$_GET[$_tmp['k']][$_tmp['k1']] = ${$_tmp['k']}[$_tmp['k1']] = addslashes($_tmp['v1']); 
			}
		}
		else
		{
			$_GET[$_tmp['k']] = ${$_tmp['k']} = iconv('EUC-KR','EUC-KR',$_tmp['v']) == $_tmp['v'] ? $_tmp['v'] : iconv('UTF-8','EUC-KR',$_tmp['v']);
		}
	}
}
?>
<html>
<head>
<meta http-equiv="content-type" content="text/html; charset=euc-kr" />
<title>RB샵</title>
<style type="text/css">
/*base font*/
body{font-size:12px; font-family:"Arial",굴림; color:#666666; line-height:140%; background-repeat: repeat-x;}
td{font-size:12px; font-family:"Arial",굴림; color:#666666; line-height:140%;}
select{font-size:11px; font-family:"verdana"; color:#2C2C2C;}
input{font-size:11px; font-family:"verdana"; color:#2C2C2C;}

/*base link*/
a {FONT-FAMILY: "Arial",굴림; FONT-SIZE: 12px; COLOR: #666666; TEXT-DECORATION: none}
a:hover {FONT-FAMILY: "Arial",굴림; FONT-SIZE: 12px; COLOR: #4D82A2; TEXT-DECORATION:none}

/* main */
.txt_main      { color:#647CBE; font-weight:bold; font-family: 굴림, verdana; }
.txt_sub_main  { color:#CB689E; }
.txt_sub { color:#5EACB8; }
.notice  { color:#2092B6; }

.box { background-color:silver; font-size:9pt;border:#818181 1px solid;}
</style>

<script language="javascript">
    function OpenWindow()
    {
        form = document.order_info;

        var return_gubun;
        var width  = 398;
        var height = 498;

        var leftpos = screen.width  / 2 - ( width / 2 );
        var toppos  = screen.height / 2 - ( height / 2 );

        form.action = form.action_url.value;

        var winopts   = "width=" + width   + ", height=" + height + ", toolbar=no, status=yes,statusbar=no,menubar=no,scrollbars=auto, resizable=yes";
        var position  = ",left=" + leftpos + ", top="    + toppos;
        var NEWPG_POP = window.open('','pay_popup', winopts + position);

        NEWPG_POP.focus();
    }

    function get_ordr_idxx()
    {
        form = document.order_info;

        var today = new Date();
        var year  = today.getFullYear();
        var month = today.getMonth()+ 1;
        var date  = today.getDate();
        var time  = today.getTime();

        if( parseInt(month) < 10 ){
            month = "0" + month;
        }

        if( parseInt( date ) < 10 ){
            date = "0" + date;
        }

        var vOrderID = time;
        parent.document.orderform.orderid.value=vOrderID; // orders.php 페이지 orderform 의 orderid 값 입력
        form.ordr_idxx.value = vOrderID;
    }

    // 에스크로 장바구니 상품 상세 정보 생성 예제
    function create_goodInfo()
    {
        form = document.order_info;
        var chr30 = String.fromCharCode(30);
        var chr31 = String.fromCharCode(31);
        var good_info = "seq=1" + chr31 + "ordr_numb=20060310_0001" + chr31 + "good_name=양말" + chr31 + "good_cntx=2" + chr31 + "good_amtx=1000" + chr30 +
                        "seq=2" + chr31 + "ordr_numb=20060310_0002" + chr31 + "good_name=신발" + chr31 + "good_cntx=1" + chr31 + "good_amtx=1500" + chr30 +
                        "seq=3" + chr31 + "ordr_numb=20060310_0003" + chr31 + "good_name=바지" + chr31 + "good_cntx=1" + chr31 + "good_amtx=1000";
        form.good_info.value = good_info;
    }
</script>
</head>

<body>
<form name="order_info" method="POST" target="pay_popup" onSubmit="return OpenWindow()">

    <!-- pay_method(지불방법) = 신용카드,가상계좌 선택 -->
	<input type="hidden" name='pay_method' value="<?php echo $shop_service_type?>">

	 <!--<select name='pay_method'>
                        <option value="100000000000">신용카드</option>
                        <option value="010000000000">계좌이체</option>
                        <option value="001000000000">가상계좌</option>
                        <option value="000010000000">휴대폰</option>
                        <option value="000001000000">포인트</option>
                        <option value="000000001000">상품권</option>
                        <option value="000000000010">ARS</option>
                        <option value="100001000000">신용카드/포인트</option>
                        <option value="111000000000">신용카드/계좌이체/가상계좌</option>
                        <option value="100011001010">신용카드/휴대폰/포인트/상품권/ARS</option>
                        </select>-->
     <!-- <input type="hidden" name="pay_method" value="100000000000"> ex. 신용카드인 경우 -->
                   
	 <!-- 상품명 -->
	 <input type="hidden" name='good_name' value="<?php echo $good_name?>">
					
	 <!-- good_mny(상품단가) -->
	 <input type="hidden" name="good_mny" value="<?php echo $good_mny?>">
                    
	 <!-- buyr_name(주문자이름) -->
	 <input type="hidden" name="buyr_name" value="<?php echo $buyr_name?>">
                   
	 <!-- buyr_mail(주문자 E-mail) -->
	 <input type="hidden" name="buyr_mail" value="<?php echo $buyr_mail?>">
                   
	 <!-- buyr_tel1(주문자 연락처) -->
	 <input type="hidden" name="buyr_tel1" value="<?php echo $buyr_tel1?>">
                   
	 <!-- buyr_tel2(주문자 핸드폰 번호) -->
	 <input type="hidden" name="buyr_tel2" value="<?php echo $buyr_tel2?>">
                    
	 <!-- 할부 옵션 (사용하실 상점 아이디 임의 선택 가능 ) --> 
	 <input type="hidden" name="quotaopt" value="<?php echo $quotaopt?>">
	 <!--<select name="quotaopt">
                            <option value="0">일시불만 가능</option>
                            <option value="3">3개월까지만 가능</option>
                            <option value="6">6개월까지만 가능</option>
                            <option value="12">12개월까지만 가능</option>
     </select>-->
                    
	 <!-- 수취인 이름 -->	 
	 <input type="hidden" name="rcvr_name"      value="<?php echo $rcvr_name?>" size="20">
                   
	 <!-- 수취인 전화번호 -->
	 <input type="hidden" name="rcvr_tel1"      value="<?php echo $rcvr_tel1?>" size="20">
                    
	 <!-- 수취인 휴대폰번호 -->
	 <input type="hidden" name="rcvr_tel2"      value="<?php echo $rcvr_tel2?>" size="20">
                    
	 <!-- 수취인 E-Mail -->
	 <input type="hidden" name="rcvr_mail"      value="<?php echo $rcvr_mail?>" size="40">
                   
	 <!-- 수취인 우편번호 -->
	 <input type="hidden" name="rcvr_zipx"      value="<?php echo $rcvr_zipx?>" size="6">
                    
	 <!-- 수취인 주소 -->
	 <input type="hidden" name="rcvr_add1"      value="<?php echo $rcvr_add1?>" size="50">
                  
	 <!-- 수취인 상세주소 -->
	 <input type="hidden" name="rcvr_add2"      value="<?php echo $rcvr_add2?>" size="50">
                    
	 <!-- 스킨 색상 선택 -->	
	 <input type="hidden" name="skin" value="original">
	 <!--<select name="skin">
                            <option value="original">기본스킨</option>
                            <option value="olivegreen">올리브스킨</option>
                            <option value="violet">보라색스킨</option>
                            <option value="indigoblue">남색스킨</option>
                            <option value="brown">갈색스킨</option>
     </select>-->
                   
	 <!-- 사이트 로고 -->
	 <input type="hidden" name="site_logo" value="http://pay.kcp.co.kr/plugin/shop_logo.gif">
                    
	 <input type="submit" value="결제" name="submit" style="display:none;">
      <!-- 결제버튼을 이미지로 구현하는 방법 : <input type="image" src="이미지경로"> -->
                 
<!-- 필수 항목 -->

	<!-- 사이트 아이디 -->
	<input type="hidden" name="site_cd" value="<?php echo $site_cd?>"></td>

	<!--한글 사용 불가-->     	
	<input type="hidden" name="site_name" value="<?php echo $site_name?>">

	<!-- 필수 항목 : PULGIN 설정 정보 변경하지 마세요 -->
	<input type="hidden" name="module_type" value="00">

	<!-- 화폐단위 (원화:WON, 달러:USD--> 
	<input type="hidden" name="currency" value="WON"> 

    <!-- ordr_idxx(주문번호) -->
	<input type="hidden" name="ordr_idxx" value="<?php echo $ordr_idxx?>">

<!-- 에스크로 항목 -->

		<!-- escw_used(에스크로사용여부) -->
		<input type="hidden" name="escw_used" value="<?php echo $escw_used?>">

		<!-- pay_mod(에스크로결제처리모드 에스크로: Y, 일반: N, KCP 설정 조건: O) -->
		<input type="hidden" name="pay_mod" value="<?php echo $pay_mod?>">

		<!-- 배송 소요일 : 예상 배송 소요일을 입력 -->
		<input type="hidden" name="deli_term" value="03">

		<!-- 장바구니 상품 개수 : 장바구니에 담겨있는 상품의 개수를 입력 -->
		<input type="hidden" name="bask_cntx" value="1">

		<!-- 장바구니 상품 상세 정보 (seq=1ordr_numb=20060310_0001good_name=양말good_cntx=2good_amtx=1000seq=2ordr_numb=20060310_0002good_name=신발good_cntx=1good_amtx=1500seq=3ordr_numb=20060310_0003good_name=바지good_cntx=1good_amtx=1000)-->
		<input type="hidden" name="good_info" value='<?php echo $good_info?>'>


<!-- 리턴 받을 FORM 값 -->
<input type=hidden name="res_cd" value="">
<input type=hidden name="res_msg" value="">
<input type=hidden name="tno" value="">

<input type=hidden name="trace_no" value="">
<input type=hidden name="enc_info" value="">
<input type=hidden name="enc_data" value="">
<input type=hidden name="use_pay_method" value="">
<input type=hidden name="tran_cd" value="">
<input type=hidden name="app_time" value="">
<input type=hidden name="app_no" value="">

<!-- 신용카드 -->
<input type=hidden name="card_cd" value="">
<input type=hidden name="card_name" value="">
<input type=hidden name="noinf" value="">
<input type=hidden name="quota" value="">

<!-- 계좌이체 -->
<input type=hidden name="bank_name" value="">

<!-- 계좌이체 -->
<input type=hidden name="bank_issue" value="">

<!-- 가상계좌 -->
<input type=hidden name="bankcode" value="">
<input type=hidden name="bankname" value="">
<input type=hidden name="depositor" value="">
<input type=hidden name="account" value="">

<!-- 현금영수증 관련 정보 : PLUGIN 에서 내려받는 정보입니다 -->
<input type="hidden" name="cash_tsdtime"   value="">
<input type="hidden" name="cash_yn"        value="">
<input type="hidden" name="cash_authno"    value="">

<!-- 폼값을 전송할 KCP 결제 서버의 URL (테스트와 실제 결제시 각각 값이 다름) -->
<!-- 테스트 결제 value : "https://testpay.kcp.co.kr/Pay/payplus_new.jsp" -->
<!-- 실제   결제 value : "https://pay.kcp.co.kr/Pay/payplus_new.jsp"     -->
<input type="hidden" name="action_url" size="50" value="https://<?php if($d['shop']['pgtest']):?>test<?php endif?>pay.kcp.co.kr/Pay/payplus_new.jsp">
<input type="hidden" name="Ret_URL" size="50" value="<?php echo $Ret_URL?>">
<input type="hidden" name="payIE8Use" value="Y">
<input type="hidden" name="tk_shop_id" value="testkcp"/><!-- 문화상품권 결제시 가맹점 고객 아이디 설정을 해야 합니다.(필수 설정) -->

</FORM>
<SCRIPT LANGUAGE="JavaScript">
document.order_info.submit.click();
</SCRIPT>
</body>
</html>
