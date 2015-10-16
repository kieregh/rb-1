<?php
//매장출력
function getShopCategoryShow($table,$j,$parent,$depth,$uid,$CXA,$hidden)
{
	global $path,$cat,$g;
	global $MenuOpen,$numhidden,$checkbox,$headfoot;
	static $j;

	$CD=getDbSelect($table,'depth='.($depth+1).' and parent='.$parent.($hidden ? ' and hidden=0':'').' order by gid asc','*');
	while($C=db_fetch_array($CD))
	{
		$j++;
		if(@in_array($C['uid'],$CXA)) $MenuOpen .= 'trees[0].tmB('.$j.');';
		$numprintx = !$numhidden && $C['num'] ? '&lt;span class="num"&gt;('.$C['num'].')&lt;/span&gt;' : '';
		$C['name'] = $headfoot && ($C['imghead']||$C['imgfoot']||$C['codhead']||$C['codfoot']) ? '&lt;b&gt;'.$C['name'].'&lt;b&gt;' : $C['name'];
		$name = $C['uid'] != $cat ? addslashes($C['name']): '&lt;span class="on"&gt;'.addslashes($C['name']).'&lt;/span&gt;';
		$name = '&lt;span class="ticon tdepth'.$C['depth'].'"&gt;&lt;/span&gt;&lt;span class="name ndepth'.$C['depth'].'"&gt;'.$name.'&lt;/span&gt;';

		if($checkbox) $icon1 = '&lt;input type="checkbox" name="members[]" value="'.$C['uid'].'" /&gt;';
		$icon2 = $C['hidden'] ? ' &lt;img src="'.$g['img_core'].'/_public/ico_hidden.gif" class="hidden" alt="숨김상태" /&gt;' : '';

		if ($C['isson'])
		{
			echo "['".$icon1.$name.$icon2.$numprintx."','".$C['uid']."',";
			getShopCategoryShow($table,$j,$C['uid'],$C['depth'],$uid,$CXA,$hidden);
			echo "],\n";
		}
		else {
			echo "['".$icon1.$name.$icon2.$numprintx."','".$C['uid']."',''],\n";
		}
	}
}
//매장코드->경로
function getShopCategoryCodeToPath($table,$cat,$j)
{
	global $DB_CONNECT;
	static $arr;

	$R=getUidData($table,$cat);
	if($R['parent'])
	{
		$arr[$j]['uid'] = $R['uid'];
		$arr[$j]['id'] = $R['id'];
		$arr[$j]['name']= $R['name'];
		getShopCategoryCodeToPath($table,$R['parent'],$j+1);
	}
	else {
		$C=getUidData($table,$cat);
		$arr[$j]['uid'] = $C['uid'];
		$arr[$j]['id'] = $C['id'];
		$arr[$j]['name']= $C['name'];
	}
	sort($arr);
	reset($arr);
	return $arr;
}
//매장코드->SQL
function getShopCategoryCodeToSql($table,$cat)
{
	$R=getUidData($table,$cat);
	if ($R['uid']) $sql .= 'category='.$R['uid'].' or ';
	if ($R['isson'])
	{
		$RDATA=getDbSelect($table,'parent='.$R['uid'],'uid,isson');
		while($C=db_fetch_array($RDATA)) $sql .= getShopCategoryCodeToSqlX($table,$C['uid'],$C['isson']);
	}
	return substr($sql,0,strlen($sql)-4);
}
//매장코드->SQL
function getShopCategoryCodeToSqlX($table,$cat,$isson)
{
	$sql = 'category='.$cat.' or ';
	if ($isson)
	{
		$RDATA=getDbSelect($table,'parent='.$cat,'uid,isson');
		while($C=db_fetch_array($RDATA)) $sql .= getShopCategoryCodeToSqlX($table,$C['uid'],$C['isson']);
	}
	return $sql;
}
//카테고리출력
function getCategoryShowSelect($table,$j,$parent,$depth,$uid,$hidden)
{
	global $cat;
	static $j;

	$CD=getDbSelect($table,'depth='.($depth+1).' and parent='.$parent.($hidden ? ' and hidden=0':'').' order by gid asc','*');
	while($C=db_fetch_array($CD))
	{
		$j++;
		echo '<option class="selectcat'.$C['depth'].'" value="'.$C['uid'].'"'.($C['uid']==$cat?' selected="selected"':'').'>';
		if(!$depth) echo 'ㆍ';
		for($i=1;$i<$C['depth'];$i++) echo '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;';
		if ($C['depth'] > 1) echo 'ㄴ';
		echo $C['name'].'</option>';
		if ($C['isson']) getCategoryShowSelect($table,$j,$C['uid'],$C['depth'],$uid,$hidden);
	}
}
//상품가격
function getPrice($R,$unit)
{
	if ($R['price_x']) return '전화문의';
	return number_format($R['price']).$unit;
}
//상품사진
function getPic($R,$m,$n)
{
	global $g;
	$folder = substr($R['d_regis'],0,4).'/'.substr($R['d_regis'],4,2).'/'.substr($R['d_regis'],6,2);
	if (is_file($g['path_module'].$m.'/files/'.$folder.'/'.$R['uid'].'_'.$n.'.'.$R['ext'])) 
	return $g['url_root'].'/modules/'.$m.'/files/'.$folder.'/'.$R['uid'].'_'.$n.'.'.$R['ext'];
	return $g['img_core'].'/blank.gif';
}
//상품사진삭제
function delPic($R,$m)
{
	global $g;
	$folder = substr($R['d_regis'],0,4).'/'.substr($R['d_regis'],4,2).'/'.substr($R['d_regis'],6,2);
	for($i = 0; $i < 5; $i++) @unlink($g['path_module'].$m.'/files/'.$folder.'/'.$R['uid'].'_'.$i.'.'.$R['ext']);
}
//상품강조사진
function getGoodsIcon($R)
{
	global $g,$m;
	$retag = '';
	$icons = explode(',',$R['icons']);
	foreach($icons as $pic)
	{
		if (!$pic) continue;
		$retag .= '<img src="'.$g['url_root'].'/modules/'.$m.'/var/icons/'.$pic.'" alt="" /> ';
	}
	return $retag;
}
//품절상태
function getPumjeol($R)
{
	return $R['display']==1 || ( $R['stock'] && $R['num1'] > $R['stock_num'] ) ? 1 : 0;
}
//내림올림반올림
function getRound($value,$type,$n)
{
	if ($type == 'down') return $value - substr($value,-strlen($n));
	if ($type == 'half') return round($value,-strlen($n));
	if ($type == 'up'  )
	{
		$s = substr($value,-strlen($n));
		return substr($s,0,1) > 0 ? $value - $s + ($n*10) : $value - $s;
	}
}
//특별세일금액
function getSHalin($R)
{
	if ($R['halin_event'])
	{
		global $date;
		$hp = explode(',',$R['halin_event']);
		if ($hp[1] <= $date['today'] && $hp[2] >= $date['today']) return $hp[0];
	}
	return 0;
}
//회원할인금액
function getMHalin($R)
{
	global $my;
	if ($R['halin_mbr'])
	{
		$hp = getMbrPrice($R['halin_mbr'],$my['sosok']);
		return $hp;
	}
	return 0;
}
//쿠폰할인금액
function getCHalin()
{

}
//배송비
function getTprice($price,$t)
{
	if ($t['tacktype']==3) return -1;
	if ($t['tacktype']==2) return 0;
	return $t['freeprice'] <= $price ? 0 : $t['tackprice'];
}
//회원할인가
function getMbrPrice($price,$group)
{
	if ($price)
	{
		$arr = explode(',',$price);
		return $arr[$group-1];
	}
}
//추가정보
function getAddinfoTD($info)
{
	if ($info)
	{
		$array = explode(',',$info);
		foreach($array as $val)
		{
			if ($val=='') continue;
			$valx = explode('=',$val);
			echo '<tr>';
			echo '<td class="td1">'.$valx[0].'</td>';
			echo '<td class="td2">:</td>';
			echo '<td class="td3">'.$valx[1].'</td>';
			echo '</tr>';
		}
	}
}
//옵션
function getOptionsTD($options)
{
	if ($options)
	{
		$array = explode('|',$options);
		$i = 0;
		foreach($array as $val)
		{
			if ($val=='') continue;
			$valx = explode('^',$val);
			$valy = explode(',',$valx[1]);
			echo '<tr>';
			echo '<td class="td1">'.$valx[0].'</td>';
			echo '<td class="td2">:</td>';
			echo '<td class="td3">';
			
			if ($valx[2] == 'select')
			{
				echo '<select id="qshopOptions_'.$i.'" onchange="if(getId(\'autoPrice\')){calOptionPrice();}">';
				echo '<option value="">&nbsp;+ 선택하세요</option>';
				echo '<option value="">----------------------------------------</option>';
				foreach($valy as $subval)
				{
					if ($subval=='') continue;
					$valz = explode('=',$subval);
					echo '<option value="'.str_replace('=','^',$subval).'">ㆍ'.$valz[0];
					if ($valz[1])
					{
						if ($valz[1] > 0) echo ' (+'.number_format($valz[1]).')';
						if ($valz[1] < 0) echo ' ('.number_format($valz[1]).')';
					}
					if ($valz[3]) echo ' (추가적립:'.number_format($valz[3]).')';
					if ($valz[2]=='0') echo ' (품절)';
					echo '</option>';
				}
				echo '</select>';
			}
			elseif ($valx[2] == 'input')
			{
				$valz = explode('=',$valy[0]);
				$valk = explode('=',$valy[1]);
				echo '<input type="text" id="qshopOptions_'.$i.'"'.($valz[0]?' style="width:'.$valz[0].'px;"':'').' />' . $valk[0];
			}
			else {
				echo '<div class="shift">';
				foreach($valy as $subval)
				{
					if ($subval=='') continue;
					$valz = explode('=',$subval);
					echo '<input type="'.str_replace('br','',$valx[2]).'" name="qshopOptions_'.$i.'[]" value="'.str_replace('=','^',$subval).'" onclick="if(getId(\'autoPrice\')){calOptionPrice();}" />'.$valz[0];
					if ($valz[1])
					{
						if ($valz[1] > 0) echo ' <span class="priceplus">(+'.number_format($valz[1]).')</span>';
						if ($valz[1] < 0) echo ' <span class="priceminus">('.number_format($valz[1]).')</span>';
					}
					if ($valz[3]) echo ' <span class="addpoint">(추가적립:'.number_format($valz[3]).')</span>';
					if ($valz[2]=='0') echo ' <span class="stocknone">(품절)</span>';
					if (strstr($valx[2],'br')) echo '<br />';
				}
				echo '</div>';
			}
			
			echo '<input type="hidden" name="pilsuCheck_[]" value="'.$valx[3].'|'.$valx[2].'|'.$valx[0].'" />';

			echo '</td>';
			echo '</tr>';
			$i++;
		}
		echo '<input type="hidden" id="optionsNum" value="'.$i.'" />';
	}
}
//할인행사
function getHalinTD($R)
{
	global $date,$my;

	$mprice = 0;
	if ($R['halin_event'])
	{
		$hp = explode(',',$R['halin_event']);
		if ($hp[1] <= $date['today'] && $hp[2] >= $date['today'])
		{
			echo '<tr>';
			echo '<td class="td1">특별세일('.(100-getPercent($R['price']-$hp[0],$R['price'],0)).'%↓)</td>';
			echo '<td class="td2">:</td>';
			echo '<td class="td3">';
			echo '<span class="price2">'.number_format($hp[0]).'원</span> ';
			echo '<span class="price3">('.substr($hp[1],0,4).'.'.substr($hp[1],4,2).'.'.substr($hp[1],6,2).'~'.substr($hp[2],0,4).'.'.substr($hp[2],4,2).'.'.substr($hp[2],6,2).')</span>';
			echo '</td>';
			echo '</tr>';
			$mprice += $hp[0];
		}
	}
	if ($R['halin_mbr'])
	{
		$hp = getMbrPrice($R['halin_mbr'],$my['sosok']);
		if($hp)
		{
			echo '<tr>';
			echo '<td class="td1">회원할인('.(100-getPercent($R['price']-$hp,$R['price'],0)).'%↓)</td>';
			echo '<td class="td2">:</td>';
			echo '<td class="td3"><span class="price2">'.number_format($hp).'원</span></td>';
			echo '</tr>';
			$mprice += $hp;
		}
	}

	if ($mprice && !$R['price_x'])
	{
		echo '<tr>';
			echo '<td class="td1">정상가</td>';
			echo '<td class="td2">:</td>';
			echo '<td class="td3">';
			echo '<span class="price1 s">'.number_format($R['price']).'원</span>';		
			echo '</td>';
		echo '</tr>';
		echo '<tr>';
			echo '<td class="td1">할인가</td>';
			echo '<td class="td2">:</td>';
			echo '<td class="td3">';
			echo '<span class="price1"><span id="orignPrice">'.number_format($R['price']-$mprice).'</span>원</span>';		
			echo '</td>';
		echo '</tr>';
	}
	else {
		echo '<tr>';
			echo '<td class="td1">판매가</td>';
			echo '<td class="td2">:</td>';
			echo '<td class="td3">';
			echo '<span class="price1">'.($R['price_x']?'전화문의':'<span id="orignPrice">'.number_format($R['price']).'</span>원').'</span>';		
			echo '</td>';
		echo '</tr>';
	}
}
?>