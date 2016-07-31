<?

$location = "문의관리 > FAQ관리";
include "../_header.php";
include "../../lib/page.class.php";
if( isset( $_GET['sitemcd'] ) == false ) $_GET['sitemcd'] = array_shift( array_keys( codeitem('faq') ) ); // 분류 기본값

list ($total) = $db->fetch("select count(*) from ".GD_FAQ.""); # 총 레코드수

### 변수할당
if (!$_GET[page_num]) $_GET[page_num] = 10; # 페이지 레코드수
$selected[page_num][$_GET[page_num]] = "selected";

$orderby = ($_GET[sort]) ? $_GET[sort] : "sort asc"; # 정렬 쿼리
$selected[sort][$orderby] = "selected";

$selected[skey][$_GET[skey]] = "selected";
$selected[sitemcd][$_GET[sitemcd]] = "selected";
$selected[sbest][$_GET[sbest]] = "checked";

### 목록
$pg = new Page($_GET[page],$_GET[page_num]); # 페이징 선언
$pg->field = "sno, itemcd, question, sort, best, bestsort, date_format( regdt, '%Y.%m.%d' ) as regdts"; # 필드 쿼리
$db_table = "".GD_FAQ.""; # 테이블 쿼리

if ($_GET[skey] && $_GET[sword]){
	if ( $_GET[skey]== 'all' ){
		$where[] = "concat( question, descant, answer ) like '%$_GET[sword]%'";
	}
	else $where[] = "$_GET[skey] like '%$_GET[sword]%'";
}

if ( $_GET[sitemcd] <> '' && $_GET[sitemcd] <> 'all' ) $where[] = "itemcd='" . $_GET[sitemcd] . "'"; # 분류검색

if ( $_GET[sbest] <> '' ) $where[] = "best='" . $_GET[sbest] . "'"; # 베스트여부

if ($_GET[sregdt][0] && $_GET[sregdt][1]) $where[] = "regdt between date_format({$_GET[sregdt][0]},'%Y-%m-%d 00:00:00') and date_format({$_GET[sregdt][1]},'%Y-%m-%d 23:59:59')";

$pg->setQuery($db_table,$where,$orderby); # 페이징 쿼리 실행
$pg->exec(); # ?

$res = $db->query($pg->query);
?>

<form name=frmList>
<div class="title title_top">FAQ관리<span>고객들이 자주하는 질문을 미리 예상해서 작성합니다 <a href="javascript:manual('<?=$guideUrl?>board/view.php?id=board&no=8')"><img src="../img/btn_q.gif" border=0 align=absmiddle hspace=2></a></div>
<table class=tb>
<col class=cellC><col class=cellL><col class=cellC><col class=cellL>
<tr>
	<td>키워드</td>
	<td>
	<select name="skey">
	<option value="all" <?=$selected[skey]['all']?>> 통합검색 </option>
	<option value="question" <?=$selected[skey]['question']?>> 질문 ( 단문 ) </option>
	<option value="descant" <?=$selected[skey]['descant']?>> 질문 ( 장문 ) </option>
	<option value="answer" <?=$selected[skey]['answer']?>> 답변 </option>
	</select> <input type="text" NAME="sword" value="<?=$_GET['sword']?>" class=line>
	</td>
	<td>분류선택</td>
	<td>
	<select name="sitemcd">
	<option value="all" <?=$selected[sitemcd]['all']?>> - 전체 - </option>
	<?foreach ( codeitem('faq') as $k => $v ){?>
	<option value='<?=$k?>' <?=$selected[sitemcd][$k]?>><?=$v?></option>
	<?}?>
	</select>
	</td>
</tr>
<tr>
	<td>베스트여부</td>
	<td colspan="3" class=noline>
	<label for="r1"><input type="radio" id="r1" name="sbest" value="Y" <?=$selected[sbest]['Y']?>> 베스트 </label>
	<label for="r2"><input type="radio" id="r2" name="sbest" value="N" <?=$selected[sbest]['N']?>> 미설정 </label>
	</select>
	</td>
</tr>
<tr>
	<td>등록일</td>
	<td colspan="3">
	<input type=text name=sregdt[] value="<?=$_GET[sregdt][0]?>" onclick="calendar(event)" class=line> -
	<input type=text name=sregdt[] value="<?=$_GET[sregdt][1]?>" onclick="calendar(event)" class=line>
	<a href="javascript:setDate('sregdt[]',<?=date("Ymd")?>,<?=date("Ymd")?>)"><img src="../img/sicon_today.gif" align=absmiddle></a>
	<a href="javascript:setDate('sregdt[]',<?=date("Ymd",strtotime("-7 day"))?>,<?=date("Ymd")?>)"><img src="../img/sicon_week.gif" align=absmiddle></a>
	<a href="javascript:setDate('sregdt[]',<?=date("Ymd",strtotime("-15 day"))?>,<?=date("Ymd")?>)"><img src="../img/sicon_twoweek.gif" align=absmiddle></a>
	<a href="javascript:setDate('sregdt[]',<?=date("Ymd",strtotime("-1 month"))?>,<?=date("Ymd")?>)"><img src="../img/sicon_month.gif" align=absmiddle></a>
	<a href="javascript:setDate('sregdt[]',<?=date("Ymd",strtotime("-2 month"))?>,<?=date("Ymd")?>)"><img src="../img/sicon_twomonth.gif" align=absmiddle></a>
	<a href="javascript:setDate('sregdt[]')"><img src="../img/sicon_all.gif" align=absmiddle></a>
	</td>
</tr>
</table>
<div class=button_top><input type=image src="../img/btn_search2.gif"></div>

<table width=100%>
<tr>
	<td class=pageInfo><font class=ver8>
	총 <b><?=number_format($total)?></b>개, 검색 <b><?=number_format($pg->recode[total])?></b>개, <b><?=number_format($pg->page[now])?></b> of <?=number_format($pg->page[total])?> Pages
	</td>
	<td align=right>
	<select name="sort" onchange="this.form.submit();">
	<option value="regdt desc" <?=$selected[sort]['regdt desc']?>>- 등록일 정렬↑</option>
	<option value="regdt asc" <?=$selected[sort]['regdt asc']?>>- 등록일 정렬↓</option>
	<option value="sort desc" <?=$selected[sort]['sort desc']?>>- 출력순서 정렬↑</option>
	<option value="sort asc" <?=$selected[sort]['sort asc']?>>- 출력순서 정렬↓</option>
    <optgroup label="------------"></optgroup>
	<option value="question desc" <?=$selected[sort]['question desc']?>>- 질문( 단문 ) 정렬↑</option>
	<option value="question asc" <?=$selected[sort]['question asc']?>>- 질문( 단문 ) 정렬↓</option>
	<option value="itemcd desc" <?=$selected[sort]['itemcd desc']?>>- 질문유형 정렬↑</option>
	<option value="itemcd asc" <?=$selected[sort]['itemcd asc']?>>- 질문유형 정렬↓</option>
	<option value="bestsort desc" <?=$selected[sort]['bestsort desc']?>>- 베스트 출력순 정렬↑</option>
	<option value="bestsort asc" <?=$selected[sort]['bestsort asc']?>>- 베스트 출력순 정렬↓</option>
	</select>&nbsp;
	<select name=page_num onchange="this.form.submit()">
	<?
	$r_pagenum = array(10,20,40,60,100);
	foreach ($r_pagenum as $v){
	?>
	<option value="<?=$v?>" <?=$selected[page_num][$v]?>><?=$v?>개 출력
	<? } ?>
	</select>
	</td>
</tr>
</table>
</form>

<form method="post" action="" name="fmList">
<INPUT TYPE="hidden" name="allmodify">
<table width=100% cellpadding=0 cellspacing=0>
<tr><td class=rnd colspan=10></td></tr>
<tr class=rndbg>
	<th width="60">번호</th>
	<th width="110">질문유형</th>
	<th>질문</th>
	<th width="70">등록일</th>
	<th width="120">베스트(순서)</th>
	<th width="80">순서</th>
	<th width="50">수정</th>
	<th width="50">삭제</th>
</tr>
<tr><td class=rnd colspan=10></td></tr>

<?
while ($data=$db->fetch($res)){

	$pri_code = $data['itemcd'] . '|' . $data['sno'];

	if ( $_GET['sitemcd'] <> '' && $selected[sort]['sort asc'] == 'selected' ){ // 정렬화살표 버튼 제어

		list ($upRow) = $db->fetch("select count(*) from ".GD_FAQ." where itemcd='" . $data['itemcd'] . "' and sort < '" . $data['sort'] . "' order by sort desc limit 1");
		list ($downRow) = $db->fetch("select count(*) from ".GD_FAQ." where itemcd='" . $data['itemcd'] . "' and sort > '" . $data['sort'] . "' order by sort asc limit 1");
	}
	?>
<INPUT TYPE="hidden" NAME="code" VALUE="<?echo($data['sno'])?>">
<tr><td height=4 colspan=10></td></tr>
<tr height=25 align="center">
	<td><?=$pg->idx--?></td>
	<td>
	<select name="itemcd" style="width:100;">
	<option value=""> - 분류 - </option>
	<?foreach ( codeitem('faq') as $k => $v ){?>
	<option value='<?=$k?>' <?=( $k == $data['itemcd'] ? 'selected' : '' )?>><?=$v?></option>
	<?}?>
	</select>
	</td>
	<td align=left><a href="faq_register.php?mode=modify&sno=<?echo($data['sno'])?>"><?=$data[question]?></a></td>
	<td><font class=ver8 color=444444><?=$data[regdts]?></td>
	<td>
	<select name="best">
	<option value=""> - 선택 - </option>
	<option value="Y" <?=( 'Y' == $data['best'] ? 'selected' : '' )?>> 베스트 </option>
	<option value="N" <?=( 'N' == $data['best'] ? 'selected' : '' )?>> 미설정 </option>
	</select>&nbsp;<input type="text" size="3" name="bestsort" value="<?=$data['bestsort']?>" style="width:30;text-align:center" class=line>
	</td>
	<td align="center">
	<table border="0" cellspacing="0" cellpadding="0" style="padding:0 3 0 3;">
	<tr>

	<? if ( $upRow != 0 || $downRow != 0 ){ // 정렬화살표 버튼 제어 ?>
		<td width="25%"><?if ( $upRow != 0 ){?><a href="javascript:act_modSort( 'sort_up', '<?=$pri_code?>' );"><img src="../img/ico_arrow_up.gif" alt="상위 이동" border="0" align="absmiddle" hspace="1"></a><?}?></td>
		<td width="25%"><?if ( $downRow != 0 ){?><a href="javascript:act_modSort( 'sort_down', '<?=$pri_code?>' );"><img src="../img/ico_arrow_down.gif" alt="하위 이동" border="0" align="absmiddle" hspace="1"></a><?}?></td>
	<? } ?>

		<td width="50%" align="center"><input type="text" size="25" name="sort" value="<?=$data['sort']?>" style="width:30;text-align:center" onkeyPress="if(event.keyCode == 13){ act_modSort( 'sort_direct', '<?=$pri_code?>', this.value ); }" class=line></td>
	</tr>
	</table>
	</td>
	<td STYLE="PADDING-TOP:3PX;"><a href="faq_register.php?mode=modify&sno=<?echo($data['sno'])?>"><img src="../img/i_edit.gif"></a></td>
	<td class="noline"><input type=checkbox name=confirmyn value="<?=$data['sno']?>"></td>
</tr>
<tr><td height=4 colspan=10></td></tr>
<tr><td colspan=10 class=rndline></td></tr>
<? } ?>
</table>
<INPUT TYPE="hidden" style="width:300" NAME="nolist">
</form>

<div align=center class=pageNavi><?=$pg->page[navi]?></div>

<div style="float:left;">
<img src="../img/btn_allselect_s.gif" alt="전체선택"  border="0" align='absmiddle' style="cursor:hand" <?if ( $pg->recode[total] != 0 ){?>onclick="javascript:PubAllSordes( 'select', fmList['confirmyn'] );"<?}else{?>onclick="javascript:alert( '데이타가 존재하지 않습니다.' );"<?}?>>
<img src="../img/btn_allreselect_s.gif" alt="선택반전"  border="0" align='absmiddle' style="cursor:hand" <?if ( $pg->recode[total] != 0 ){?>onclick="javascript:PubAllSordes( 'reflect', fmList['confirmyn'] );"<?}else{?>onclick="javascript:alert( '데이타가 존재하지 않습니다.' );"<?}?>>
<img src="../img/btn_alldeselect_s.gif" alt="선택해제"  border="0" align='absmiddle' style="cursor:hand" <?if ( $pg->recode[total] != 0 ){?>onclick="javascript:PubAllSordes( 'deselect', fmList['confirmyn'] );"<?}else{?>onclick="javascript:alert( '데이타가 존재하지 않습니다.' );"<?}?>>
<img src="../img/btn_alldelet_s.gif" alt="선택삭제" border="0" align='absmiddle' style="cursor:hand" <?if ( $pg->recode[total] != 0 ){?>onclick="javaScript:act_delete();"<?}else{?>onclick="javascript:alert( '데이타가 존재하지 않습니다.' );"<?}?>>
</div>

<div style="float:right;">
<A HREF="javascript:act_allmodify();"><img src="../img/btn_allmodify_s.gif" alt="일괄수정" border=0 align=absmiddle></A>
<a href="faq_register.php"><img src="../img/btn_regist_s.gif" alt="등록" border=0 align=absmiddle></a>
</div>

<div style="padding-top:35;"></div>

<div id=MSG01>
<table cellpadding=1 cellspacing=0 border=0 class=small_ex>
<tr><td><img src="../img/arrow_blue.gif" align=absmiddle>'출력순서' 변경</font></td></tr>
<tr><td><img src="../img/icon_list.gif" align=absmiddle>유저모드 : 오름차순 출력</td></tr>
<tr><td><img src="../img/icon_list.gif" align=absmiddle>방법1) 화살표 : 상하위로 한칸씩 이동. 단, 분류검색 후 정렬방식이 '출력순서 정렬↓' 일 경우만 가능합니다.</td></tr>
<tr><td><img src="../img/icon_list.gif" align=absmiddle>방법2) 개별수정 : '출력순서' 칸을 입력 후 해당 칸내에서 'Enter key' 클릭하시면 자동으로 전체가 재정렬됩니다.</td></tr>
<tr><td><img src="../img/icon_list.gif" align=absmiddle>방법3) 일괄수정 : 각 '출력순서' 칸을 입력 후 [일괄수정]을 클릭하시면 됩니다.</td></tr>
<tr><td><img src="../img/icon_list.gif" align=absmiddle>베스트>로 선정해놓으면 베스트문의 5개가 고객센터 메인화면에 나오게 됩니다.</td></tr>

</table>
</div>
<script>cssRound('MSG01')</script>



<SCRIPT LANGUAGE="JavaScript"><!--
/*-------------------------------------
 일괄수정
-------------------------------------*/
/* 수정전 함수 
function act_allmodify(){

	var fs = document.fmList; // 리스트폼

	if( fs['code'] == null ) return; // 레코드가 1미만인 경우

	var fieldnm = new Array( 'code', 'sort', 'itemcd', 'best', 'bestsort' ); // 필드명
	var csField = new Array(); // 필드데이타저장

	for( var nm in fieldnm ) csField[ fieldnm[nm] ] = ''; // 필드데이타초기화

	var count = fs['code'].length;	// 레코드수

	if( count == undefined ){ // 레코드수가 1개 인 경우

		for( var nm in fieldnm ){
			var Obj = eval( "fs['" + fieldnm[nm] + "']" );
			if( Obj.type != 'checkbox' ) csField[ fieldnm[nm] ] += Obj.value + ";"; else csField[ fieldnm[nm] ] += Obj.checked + ";";
		}
	}
	else { // 레코드수가 2개 이상인 경우

		for( var i = 0; i < count; i++ ){

			for( var nm in fieldnm ){
				var Obj = eval( "fs['" + fieldnm[nm] + "']" );
				if( Obj[i].type != 'checkbox' ) csField[ fieldnm[nm] ] += Obj[i].value + ";"; else csField[ fieldnm[nm] ] += Obj[i].checked + ";";
			}
		}
	}

	for( var nm in fieldnm ) fs.allmodify.value += fieldnm[nm] + '==' + csField[ fieldnm[nm] ] + '||';

	fmList.action = "faq_indb.php?mode=allmodify";
	fmList.submit() ;
}
*/ 
// 수정 후 함수
function act_allmodify(){

	var fs = document.fmList; // 리스트폼

	if( fs['code'] == null ) return; // 레코드가 1미만인 경우

	var fieldnm = new Array( 'code', 'sort', 'itemcd', 'best', 'bestsort' ); // 필드명
	var csField = new Array(); // 필드데이타저장

	// 필드데이타초기화
	fieldnm.each(function(item) { 
		csField[item] = '';
	}); 
	

	var count = fs['code'].length;	// 레코드수

	if( count == undefined ){ // 레코드수가 1개 인 경우

		fieldnm.each(function(item) { 
			var Obj = eval( "fs['" + item + "']" );
			if( Obj.type != 'checkbox' ) csField[item] += Obj.value + ";"; else csField[item] += Obj.checked + ";";
		});
	}
	else { // 레코드수가 2개 이상인 경우

		for( var i = 0; i < count; i++ ){

			fieldnm.each(function(item) { 
				var Obj = eval( "fs['" + item + "']" );
				if( Obj[i].type != 'checkbox' ) csField[item] += Obj[i].value + ";"; else csField[item] += Obj[i].checked + ";";
			});
		}
	}

	fieldnm.each(function(item) { 
		fs.allmodify.value += item + '==' + csField[item] + '||';
	});
	
	fmList.action = "faq_indb.php?mode=allmodify";
	fmList.submit() ;
}
//--></SCRIPT>



<SCRIPT LANGUAGE="JavaScript"><!--
/*-------------------------------------
 순서수정
-------------------------------------*/
function act_modSort( mode, code, sort ){
	fmList.action = "faq_indb.php?mode=" + mode + "&code=" + code + "&sort=" + sort;
	fmList.submit() ;
}
//--></SCRIPT>



<SCRIPT LANGUAGE=JavaScript><!--
/*-------------------------------------
 삭제
-------------------------------------*/
function act_delete(){

	if ( PubChkSelect( fmList['confirmyn'] ) == false ){
		alert( "삭제하실 내역을 선택하여 주십시요." );
		return;
	}

	if ( confirm( "선택한 아이템을 정말 삭제하시겠습니까?\n삭제 후 복구할 수 없습니다." ) == false ) return;

	var idx = 0;
	var codes = new Array();
	var count = fmList['confirmyn'].length;

	if ( count == undefined ) codes[ idx++ ] = fmList['confirmyn'].value;
	else {

		for ( i = 0; i < count ; i++ ){
			if ( fmList['confirmyn'][i].checked ) codes[ idx++ ] = fmList['confirmyn'][i].value;
		}
	}

	fmList.nolist.value = codes.join( ";" );
	fmList.action = "faq_indb.php?mode=delete" ;
	fmList.submit() ;
}
//--></SCRIPT>



<? include "../_footer.php"; ?>