<?
	$addFields = array(
		'use_emoney' => array(
			'text' => '적립금정책',
			'down' => 'N',
			'desc' => '적립금 설정의 정책 적용(0), 적립금 개별 설정(1) 중 택일 입력. 기본값 - 적립금 설정의 정책 적용(0)'
		),
        'extra_info' => array(
            'text' => '상품필수정보',
            'down' => 'N',
            'desc' => '',
        ),
		'naver_event' => array(
			'text' => '이벤트문구',
			'down' => 'Y',
			'desc' => "'마케팅>네이버쇼핑>네이버 쇼핑이벤트 문구 설정>상품별 문구' 선택 후 입력할 상품별 개별 이벤트 문구 입력 (최대 100자 이내)"
		),
		'use_mobile_img' => array(
			'text' => '모바일 전용 이미지 사용여부',
			'down' => 'Y',
			'desc' => "모바일샵 전용 이미지 사용(1), PC 이미지 사용(0) 중 택일 입력, 기본값 - 모바일샵 전용 이미지 사용(1)<br><u style='color:#bf0000;'>모바일샵 전용 이미지 사용(1)으로 설정 시 아래 모바일 메인~확대 이미지가 적용됩니다.<br>PC 이미지 사용(0)으로 설정 시에는 아래 모바일 메인~확대에 사용될 PC 이미지가 적용됩니다.</u>"
		),
		'img_w' => array(
			'text' => '모바일 메인 이미지',
			'down' => 'Y',
			'desc' => "모바일샵 메인에 출력할 이미지명 입력. <u style='color:#bf0000;'> ※ 해당 이미지는 하나의 이미지만 적용해야 정상 적용됩니다.</u>"
		),
		'img_x' => array(
			'text' => '모바일 리스트 이미지',
			'down' => 'Y',
			'desc' => "모바일샵 리스트에 출력할 이미지명 입력. <u style='color:#bf0000;'> ※ 해당 이미지는 하나의 이미지만 적용해야 정상 적용됩니다.</u>"
		),
		'img_y' => array(
			'text' => '모바일 상세 이미지',
			'down' => 'Y',
			'desc' => "모바일샵 상세에 출력할 이미지명 입력. 다수 경우 '|' 를 구분자로 입력. <i>ex) test1.gif|test2.gif</i>"
		),
		'img_z' => array(
			'text' => '모바일 확대 이미지',
			'down' => 'Y',
			'desc' => "모바일샵 확대에 출력할 이미지명 입력. 다수 경우 '|' 를 구분자로 입력. <i>ex) test1.gif|test2.gif</i>"
		),
		'img_pc_w' => array(
			'text' => '모바일 메인에 사용될 PC 이미지',
			'down' => 'Y',
			'desc' => 'PC 이미지의 영문 타이틀명 입력. <i>(메인이미지: img_i / 리스트이미지: img_s / 상세이미지: img_m / 확대이미지: img_l 중 택1)</i>'
		),
		'img_pc_x' => array(
			'text' => '모바일 리스트 사용될 PC 이미지',
			'down' => 'Y',
			'desc' => 'PC 이미지의 영문 타이틀명 입력. <i>(메인이미지: img_i / 리스트이미지: img_s / 상세이미지: img_m / 확대이미지: img_l 중 택1)</i>'
		),
		'img_pc_y' => array(
			'text' => '모바일 상세 사용될 PC 이미지',
			'down' => 'Y',
			'desc' => 'PC 이미지의 영문 타이틀명 입력. <i>(메인이미지: img_i / 리스트이미지: img_s / 상세이미지: img_m / 확대이미지: img_l 중 택1)</i>'
		),
		'img_pc_z' => array(
			'text' => '모바일 확대 사용될 PC 이미지',
			'down' => 'Y',
			'desc' => 'PC 이미지의 영문 타이틀명 입력. <i>(메인이미지: img_i / 리스트이미지: img_s / 상세이미지: img_m / 확대이미지: img_l 중 택1)</i>'
		),
		'naver_import_flag' => array(
			'text' => '수입 및 제작 여부',
			'down' => 'Y',
			'desc' => "유형 : 해외(1), 병행(2), 주문제작(3) 중 해당 사항 택일하여 입력<br>상품이 해외구매대행인 경우 해외, 병행수입인 경우 병행, 주문제작인 경우 주문제작 입력. 해당 사항 없는 경우 표기하지 않음.<br><u style='color:#bf0000;'>※ 네이버쇼핑 3.0에 반영되는 정보로, 해당 상품임에도 해외구매대행 여부가 적절하게 표기되지 않은 경우 노출 중지 및 삭제되며, 클린프로그램이 적용되어 등급이 하락될 수 있습니다.</u>"
		),
		'naver_product_flag' => array(
			'text' => '판매방식 구분',
			'down' => 'Y',
			'desc' => "유형 : 도매(1), 렌탈(2), 대여(3), 할부(4), 예약판매(5), 구매대행(6) 중 해당 사항 택일하여 입력<br>일반적인 판매방식과는 다른 방식으로 판매되는 상품들에 표기<br><u style='color:#bf0000;'>※ 네이버쇼핑 3.0에 반영되는 정보로, 해당 상품임에도 판매방식이 적절하게 표기되지 않은 경우 네이버쇼핑에서 상품이 삭제되며, 클린프로그램이 적용되어 등급이 하락될 수 있습니다.</u>"
		),
		'naver_age_group' => array(
			'text' => '주 이용 고객층',
			'down' => 'Y',
			'desc' => '유형 : 성인(0), 청소년(1), 아동(2), 유아(3) 중 택일하여 입력. 기본값 - 성인(0)<br>상품의 주요 사용층을 텍스트로 기입. 입력하지 않는 경우 ‘성인’으로 처리'
		),
		'naver_gender' => array(
			'text' => '성별',
			'down' => 'Y',
			'desc' => '유형 : 남성(1), 여성(2), 남녀공용(3) 중 해당 사항 택일하여 입력<br>상품의 주요 구매 고객의 성별을 입력'
		),
		'naver_attribute' => array(
			'text' => '상품속성',
			'down' => 'Y',
			'desc' => '상품의 속성 정보를 ‘^’로 구분하여 입력, 최대 500자<br><i>ex) 서울^1개^오션뷰^2명^주중^조식포함^무료주차^와이파이</i>'
		),
		'naver_search_tag' => array(
			'text' => '검색태그',
			'down' => 'Y',
			'desc' => '상품의 검색태그에 대하여 ‘|’(Vertical bar)로 구분하여 입력. 최대 100자<br><i>ex) 물방울패턴원피스|2016S/S신상원피스|결혼식아이템|여친룩</i>'
		),
		'naver_category' => array(
			'text' => '네이버 카테고리',
			'down' => 'Y',
			'desc' => '네이버 카테고리의 ID를 입력. 최대 8자<br>입력하는 경우, 네이버 쇼핑에서 해당 카테고리에 매칭하는데 반영 '
		),
		'naver_product_id' => array(
			'text' => '가격비교 페이지 ID',
			'down' => 'Y',
			'desc' => "네이버 가격비교 페이지 ID를 입력할 경우 네이버 가격비교 추천에 반영. 최대 50자<br><i>ex) http://shopping.naver.com/detail/detail.nhn?nv_mid=<u style='color:#bf0000;'>8535546055</u>&cat_id=50000151</i>"
		),
	);
?>

<style>
.title2 {
	font-weight:bold;
	padding-bottom:5px;
}
</style>

<div class="title title_top">상품DB다운로드<span>상품검색다운로드, 항목체크다운로드 등 두가지 방법으로 상품DB를 다운로드 받으실 수 있습니다</span> <a href="javascript:manual('<?=$guideUrl?>board/view.php?id=data&no=4')"><img src="../img/btn_q.gif" border=0 align=absmiddle></a></div>

<div style="padding-top:15;"></div>

<form name=fm method=post action="../data/data_goodsxls_indb.php" onsubmit="return chkForm(this)">
<div class=title2>&nbsp;&nbsp;&nbsp;<img src="../img/icon_process.gif" align=absmiddle><font class=def1 color=000000><b>상품검색으로 다운로드 받기</b></font> <font class=extext(검색결과에 해당하는 상품만(기본항목) 다운로드합니다)</font></div>

<table class=tb>
<col class=cellC><col class=cellL>
<tr>
	<td>분류선택</td>
	<td>
	<script src="../../lib/js/categoryBox.js"></script>
	<script>new categoryBox('cate[]',4,'');</script>
	</td>
</tr>
<tr>
	<td>키워드</td>
	<td>
	<select name=skey>
	<option value="goodsnm">상품명
	<option value="a.goodsno">고유번호
	<option value="goodscd">상품코드
	<option value="keyword">유사검색어
	</select>
	<input type=text name=sword class=lline value="" class="line">
	</td>
</tr>
<tr>
	<td>상품가격</td>
	<td><font class=small color=444444>
	<input type=text name=price[] value="" class="rline"> 원 -
	<input type=text name=price[] value="" class="rline"> 원
	</td>
</tr>
<tr>
	<td>상품등록일</td>
	<td>
	<input type=text name=regdt[] value="" onclick="calendar(event)" class="cline"> -
	<input type=text name=regdt[] value="" onclick="calendar(event)" class="cline">
	<a href="javascript:setDate('regdt[]',<?=date("Ymd")?>,<?=date("Ymd")?>)"><img src="../img/sicon_today.gif" align=absmiddle></a>
	<a href="javascript:setDate('regdt[]',<?=date("Ymd",strtotime("-7 day"))?>,<?=date("Ymd")?>)"><img src="../img/sicon_week.gif" align=absmiddle></a>
	<a href="javascript:setDate('regdt[]',<?=date("Ymd",strtotime("-15 day"))?>,<?=date("Ymd")?>)"><img src="../img/sicon_twoweek.gif" align=absmiddle></a>
	<a href="javascript:setDate('regdt[]',<?=date("Ymd",strtotime("-1 month"))?>,<?=date("Ymd")?>)"><img src="../img/sicon_month.gif" align=absmiddle></a>
	<a href="javascript:setDate('regdt[]',<?=date("Ymd",strtotime("-2 month"))?>,<?=date("Ymd")?>)"><img src="../img/sicon_twomonth.gif" align=absmiddle></a>
	<a href="javascript:setDate('regdt[]')"><img src="../img/sicon_all.gif" align=absmiddle></a>
	</td>
</tr>
<tr>
	<td>출력체크여부</td>
	<td class=noline>
	<input type=radio name=open value="">전체
	<input type=radio name=open value="1">출력체크된 상품
	<input type=radio name=open value="0">미출력체크된 상품
	</td>
</tr>
</table>

<div style="padding-top:7;"></div>

<div class=noline>
<table border=0 cellpadding=0 cellspacing=0>
<tr>
<!--<td><img src="../img/icon_list.gif" align=absmiddle><font color=0074BA>위 검색결과에 해당하는 상품만 다운로드 받을 수 있습니다.</font><br>
	- 다운받고자하는 상품을 검색조건에 입력하세요.<br>
	- 다운로드버튼을 누른 후 저장하시면 됩니다</td>
	<td widht=20></td>-->
<td width=127></td>
<td>&nbsp;&nbsp;&nbsp;<input type="image" src="../img/btn_gooddown.gif" alt="상품DB다운로드"></td>
</tr></table>
</div>


<div style="padding-top:40;"></div>

<div class=title2>&nbsp;&nbsp;&nbsp;<img src="../img/icon_process.gif" align=absmiddle><font class=def1 color=000000><b>원하는 항목체크 후 다운로드 받기</b></font> <font class=extext>(원하는 항목을 체크한 후 다운로드합니다)</font></div>

<table class=tb>
<col class=cellC><col class=cellL>
<tr>
	<td height=30>파일명</td>
	<td><input type="text" name="filename" value="[<?=strftime( '%y년%m월%d일' )?>] 상품" size=40 required label="파일명" class="line"> <span class=extext>확장자(xls)를 제외한 파일명을 입력합니다</span></td>
</tr>
<tr>
	<td height=30>상품정렬방식</td>
	<td>
	<select name="sort">
	<option value="regdt desc" selected>상품등록일↑</option>
	<option value="regdt asc">상품등록일↓</option>
	<option value="goodsnm desc">상품명↑</option>
	<option value="goodsnm asc">상품명↓</option>
	<option value="price desc">가격↑</option>
	<option value="price asc">가격↓</option>
	<option value="maker desc">제조사↑</option>
	<option value="maker asc">제조사↓</option>
	</select>
	<font class=extext>상품정렬방식을 선택하세요</font>
	</td>
</tr>
<tr>
	<td height=30>다운로드범위</td>
	<td>
	<div style="float:left;" class=noline><input type="radio" name="limitmethod" value="all" onclick="document.getElementById('part').style.display='none';"> 전체다운 &nbsp;&nbsp;&nbsp;
	<input type="radio" name="limitmethod" value="part" onclick="document.getElementById('part').style.display='block';" checked> 부분다운</div>
	<div style="float:left;margin-left:5;" id="part"><input type="text" name="limit[]" value="1" size="5" style="text-align:right;"> 개 ∼ <input type="text" name="limit[]" value="100" size="5" style="text-align:right;"> 개
	<font class=extext>상품이 너무 많을 경우에 사용
	</div>
	</td>
</tr>
<tr>
	<td valign="top" style="padding-top:10px;">항목(필드)체크</td>
	<td style="padding:5px;">
	<div style="padding-top:5;"></div>
	&nbsp;&nbsp;<font class=extext>아래 체크된 항목들은 기본항목입니다</font>
	<div style="padding-top:7;"></div>
	<style>
	#field_table { border-collapse:collapse; float:left; margin-right:10px; }
	#field_table th { padding:4; }
	#field_table td { border-style:solid;border-width:1;border-color:#EBEBEB;color:#4c4c4c;padding:4; }
	#field_table i { color:green; font:8pt dotum; }
	</style>
<?
$fields = parse_ini_file("../../conf/data_goodsddl.ini", true);
if($addFields && is_array($addFields)) {
	foreach($addFields as $k => $v) {
		if(!$fields[$k]) $fields[$k] = $v;
	}
}
$subcnt = ceil( count( $fields ) / 3 );

for ( $i = 0; $i < 3; $i++ ){

	$idx = 0;
	while( list ($key, $arr) = each ( $fields ) ){
		$idx++;

		if ( $idx == 1 ){?>
	<table id="field_table">
	<tr bgcolor="#eeeeee">
		<th bgcolor=F4F4F4><font class=small1 color=444444><b>한글필드명</b></th>
		<th bgcolor=F4F4F4><font class=small1 color=444444><b>영문필드명</th>
	</tr>
		<?}?>
	<tr bgcolor="white">
		<td><span class=noline><font class=def1 color=444444><input type="checkbox" name="field[]" value="<?=$key?>" <?=( $arr['down'] == 'Y' ? 'checked' : '' )?>></span> <?=$arr['text']?></td>
		<td width=80><font class=ver81><?=$key?></td>
	</tr>
		<?
		if ( $idx == $subcnt || current( $fields ) == null  ){
			echo '</table>';
			break;
		}
	}
}
?>

	</td>
</tr>
</table>

<div style="padding-top:7px"></div>
<div class=noline style="padding-left:137px;text-align:left;"><input type="image" src="../img/btn_gooddown.gif" alt="상품DB다운로드"></div>
</form>


<div style="padding-top:15px"></div>

<div id=MSG01>
<table cellpadding=2 cellspacing=0 border=0 class=small_ex>
<tr>
	<td><img src="../img/icon_list.gif" align=absmiddle>엑셀다운 사용순서
	<ol style="margin-top:0px;margin-bottom:0px;">
	<li>확장자(xls)를 제외한 파일명을 입력합니다.</li>
	<li>다운로드범위에서 부분다운 할 경우 상품개수를 꼭! 입력합니다.</li>
	<li>다운받을 항목(필드)을 선택합니다.</li>
	<li>[다운로드] 버튼 클릭</li>
	</ol>
	</td>
</tr>
<tr>
	<td><img src="../img/icon_list.gif" align=absmiddle>엑셀 버전에 따라 다운받은 엑셀 파일의 셀 내용이 많을 경우 ‘#####’으로 표시되는 현상이 발생할 수 있습니다. 이러한 경우 셀의 ‘열넓이’를 늘리거나 셀서식을 변경하여 주시면 내용이 정상적으로 표시됩니다.</td>
</tr>
</table>
</div>
<script>cssRound('MSG01')</script>