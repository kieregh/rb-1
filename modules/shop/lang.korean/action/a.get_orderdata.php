<?php
if(!defined('__KIMS__')) exit;

 // 테이블 컬럼명 추출
function get_OrderTblColums($table)
{
    global $DB_CONNECT;
    $cols=array();
    $result = db_query("SHOW COLUMNS FROM ".$table,$DB_CONNECT);
    while ($r=db_fetch_array($result))
    {
       $cols[]= $r["Field"];     
     }  
    return $cols;      
} 

// 테이블 필드명으로 데이타값 추출
function get_UidData($table,$uid) 
{
   global $DB_CONNECT;
	$sql=" where uid=".$uid;
	$result =db_query("SELECT * FROM ".$table.$sql,$DB_CONNECT);
	$row = array();
	while( $r = db_fetch_assoc($result)) 
	{
	     $row[] = $r;
	}
	return $row;        
}

// 주문상품 내역 추출
$g_info=getDbSelect($table[$m.'ordergoods'],'parent='.$uid,'*');
$g_qty=0;
while ($G=db_fetch_assoc($g_info))
{
   $g_name=$G['goodsname'];
  $g_qty++;
}
// 주문 order 내역 추출
$order=get_UidData($table[$m.'order'],$uid);
$cols=get_OrderTblColums($table[$m.'order']);
$order_data=array();

// order 테이블 필드값 추출해서 배열로 세팅  
foreach ($order as $O) {
  $_tmp=array();
  foreach ($cols as $col) 
   { 
   	 $_tmp[$col]=$O[$col];
  } 
}

//$_tmp 배열에 상품수량 추가 
$_tmp['g_name']=$g_name;
$_tmp['g_qty']=$g_qty;

// 최종 json 데이타로 출력
$order_data=json_encode($_tmp);
?>
[RESULT:<?php echo $order_data?>:RESULT]
