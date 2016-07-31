<?

$location = "문의관리 > 광고·제휴문의";
include "../_header.php";
include "../../lib/page.class.php";

list ($total) = $db->fetch("select count(*) from ".GD_COOPERATION.""); # 총 레코드수

### 변수할당
if (!$_GET[page_num]) $_GET[page_num] = 10; # 페이지 레코드수
$selected[page_num][$_GET[page_num]] = "selected";

$orderby = ($_GET[sort]) ? $_GET[sort] : "regdt desc"; # 정렬 쿼리
$selected[sort][$orderby] = "selected";

$selected[skey][$_GET[skey]] = "selected";
$selected[sitemcd][$_GET[sitemcd]] = "selected";
$selected[sreplyyn][$_GET[sreplyyn]] = "checked";
$selected[smailyn][$_GET[smailyn]] = "checked";

### 목록
$pg = new Page($_GET[page],$_GET[page_num]); # 페이징 선언
$pg->field = "*, date_format( regdt, '%Y.%m.%d' ) as regdts, date_format( replydt, '%Y.%m.%d' ) as replydts, date_format( maildt, '%Y.%m.%d' ) as maildts"; # 필드 쿼리
$db_table = "".GD_COOPERATION.""; # 테이블 쿼리

if ($_GET[skey] && $_GET[sword]){
	if ( $_GET[skey]== 'all' ){
		$where[] = "concat( name, title, content, reply ) like '%$_GET[sword]%'";
	}
	else $where[] = "$_GET[skey] like '%$_GET[sword]%'";
}

if ( $_GET[sitemcd] <> '' ) $where[] = "itemcd='" . $_GET[sitemcd] . "'"; # 분야검색

if ( $_GET[sreplyyn] == 'Y' ) $where[] = "reply != ''";
else if ( $_GET[sreplyyn] == 'N' ) $where[] = "( reply = '' OR reply IS NULL )"; # 답변여부

if ( $_GET[smailyn] == 'Y' ) $where[] = "unix_timestamp(maildt) != 0";
else if ( $_GET[smailyn] == 'N' ) $where[] = "unix_timestamp(maildt) = 0"; # 답변메일여부

if ($_GET[sregdt][0] && $_GET[sregdt][1]) $where[] = "regdt between date_format({$_GET[sregdt][0]},'%Y-%m-%d 00:00:00') and date_format({$_GET[sregdt][1]},'%Y-%m-%d 23:59:59')";
if ($_GET[sreplydt][0] && $_GET[sreplydt][1]) $where[] = "replydt between date_format({$_GET[sreplydt][0]},'%Y-%m-%d') and date_format({$_GET[sreplydt][1]},'%Y-%m-%d')";
if ($_GET[smaildt][0] && $_GET[smaildt][1]) $where[] = "maildt between date_format({$_GET[smaildt][0]},'%Y-%m-%d 00:00:00') and date_format({$_GET[smaildt][1]},'%Y-%m-%d 23:59:59')";

$pg->setQuery($db_table,$where,$orderby); # 페이징 쿼리 실행
$pg->exec(); # ?

$res = $db->query($pg->query);
?>

<form name=frmList>
<div class="title title_top">광고·제휴문의<span>광고제휴건으로 들어온 문의를 처리하실 수 있습니다</span> <a href="javascript:manual('<?=$guideUrl?>board/view.php?id=board&no=7')"><img src="../img/btn_q.gif" border=0 align=absmiddle hspace=2></a></div>
<table class=tb>
<col class=cellC><col class=cellL><col class=cellC><col class=cellL>
<tr>
	<td>키워드검색</td>
	<td>
	<select name="skey">
	<option value="all" <?=$selected[skey]['all']?>> 통합검색 </option>
	<option value="title" <?=$selected[skey]['title']?>> 문의제목 </option>
	<option value="content" <?=$selected[skey]['content']?>> 문의내용 </option>
	<option value="reply" <?=$selected[skey]['reply']?>> 답변 </option>
	<option value="name" <?=$selected[skey]['name']?>> 이름 </option>
	</select> <input type="text" NAME="sword" value="<?=$_GET['sword']?>" class=line>
	</td>
	<td>문의분야</td>
	<td>
	<select name="sitemcd">
	<option value=""> - 전체 - </option>
	<?foreach ( codeitem('cooperation') as $k => $v ){?>
	<option value='<?=$k?>' <?=$selected[sitemcd][$k]?>><?=$v?></option>
	<?}?>
	</select>
	</td>
</tr>
<tr>
	<td>답변여부</td>
	<td class=noline>
	<label for="r1"><input type="radio" id="r1" name="sreplyyn" value="Y" <?=$selected[sreplyyn]['Y']?>> 답변 후 </label>
	<label for="r2"><input type="radio" id="r2" name="sreplyyn" value="N" <?=$selected[sreplyyn]['N']?>> 답변 전 </label>
	</select>
	</td>
	<td>답변메일여부</td>
	<td class=noline>
	<label for="r3"><input type="radio" id="r3" name="smailyn" value="Y" <?=$selected[smailyn]['Y']?>> 전송 후 </label>
	<label for="r4"><input type="radio" id="r4" name="smailyn" value="N" <?=$selected[smailyn]['N']?>> 전송 전 </label>
	</td>
</tr>
<tr>
	<td>접수일</td>
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
<tr>
	<td>답변일</td>
	<td colspan="3">
	<input type=text name=sreplydt[] value="<?=$_GET[sreplydt][0]?>" onclick="calendar(event)" class=line> -
	<input type=text name=sreplydt[] value="<?=$_GET[sreplydt][1]?>" onclick="calendar(event)" class=line>
	<a href="javascript:setDate('sreplydt[]',<?=date("Ymd")?>,<?=date("Ymd")?>)"><img src="../img/sicon_today.gif" align=absmiddle></a>
	<a href="javascript:setDate('sreplydt[]',<?=date("Ymd",strtotime("-7 day"))?>,<?=date("Ymd")?>)"><img src="../img/sicon_week.gif" align=absmiddle></a>
	<a href="javascript:setDate('sreplydt[]',<?=date("Ymd",strtotime("-15 day"))?>,<?=date("Ymd")?>)"><img src="../img/sicon_twoweek.gif" align=absmiddle></a>
	<a href="javascript:setDate('sreplydt[]',<?=date("Ymd",strtotime("-1 month"))?>,<?=date("Ymd")?>)"><img src="../img/sicon_month.gif" align=absmiddle></a>
	<a href="javascript:setDate('sreplydt[]',<?=date("Ymd",strtotime("-2 month"))?>,<?=date("Ymd")?>)"><img src="../img/sicon_twomonth.gif" align=absmiddle></a>
	<a href="javascript:setDate('sreplydt[]')"><img src="../img/sicon_all.gif" align=absmiddle></a>
	</td>
</tr>
<tr>
	<td>메일전송일</td>
	<td colspan="3">
	<input type=text name=smaildt[] value="<?=$_GET[smaildt][0]?>" onclick="calendar(event)" class=line> -
	<input type=text name=smaildt[] value="<?=$_GET[smaildt][1]?>" onclick="calendar(event)" class=line>
	<a href="javascript:setDate('smaildt[]',<?=date("Ymd")?>,<?=date("Ymd")?>)"><img src="../img/sicon_today.gif" align=absmiddle></a>
	<a href="javascript:setDate('smaildt[]',<?=date("Ymd",strtotime("-7 day"))?>,<?=date("Ymd")?>)"><img src="../img/sicon_week.gif" align=absmiddle></a>
	<a href="javascript:setDate('smaildt[]',<?=date("Ymd",strtotime("-15 day"))?>,<?=date("Ymd")?>)"><img src="../img/sicon_twoweek.gif" align=absmiddle></a>
	<a href="javascript:setDate('smaildt[]',<?=date("Ymd",strtotime("-1 month"))?>,<?=date("Ymd")?>)"><img src="../img/sicon_month.gif" align=absmiddle></a>
	<a href="javascript:setDate('smaildt[]',<?=date("Ymd",strtotime("-2 month"))?>,<?=date("Ymd")?>)"><img src="../img/sicon_twomonth.gif" align=absmiddle></a>
	<a href="javascript:setDate('smaildt[]')"><img src="../img/sicon_all.gif" align=absmiddle></a>
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
    <optgroup label="------------"></optgroup>
    <option value="title desc" <?=$selected[sort]['title desc']?>>- 문의제목 정렬↑</option>
    <option value="title asc" <?=$selected[sort]['title asc']?>>- 문의제목 정렬↓</option>
    <option value="itemcd desc" <?=$selected[sort]['itemcd desc']?>>- 문의분야 정렬↑</option>
    <option value="itemcd asc" <?=$selected[sort]['itemcd asc']?>>- 문의분야 정렬↓</option>
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
	<th width="110">분야</th>
	<th>문의제목</th>
	<th width="70">글쓴이</th>
	<th width="70">접수일</th>
	<th width="70">답변일</th>
	<th width="70">답변메일</th>
	<th width="60">답변</th>
	<th width="50">삭제</th>
</tr>
<tr><td class=rnd colspan=10></td></tr>
<?
while ($data=$db->fetch($res)){
	?>
<INPUT TYPE="hidden" NAME="code" VALUE="<?echo($data['sno'])?>">
<tr><td height=4 colspan=10></td></tr>
<tr height=25 align="center">
	<td><font class=ver81 color=444444><?=$pg->idx--?></td>
	<td>
	<select name="itemcd" style="width:80;">
	<option value=""> - 문의분야 - </option>
	<?foreach ( codeitem('cooperation') as $k => $v ){?>
	<option value='<?=$k?>' <?=( $k == $data['itemcd'] ? 'selected' : '' )?>><?=$v?></option>
	<?}?>
	</select>
	</td>
	<td align=left><a href="cooperation_register.php?mode=modify&sno=<?echo($data['sno'])?>"><?=( $data[reply] == ''?'<b>':'' )?><?=$data[title]?></font></a></td>
	<td><?=$data[name]?></td>
	<td><font class=ver81 color=444444><?=$data[regdts]?></td>
	<TD><font class=ver81 color=444444><?echo( str_replace( ".", "", $data[replydts] ) > 0 ? $data[replydts] : '미답변' )?></TD>
	<TD><font class=ver81 color=444444><?echo( str_replace( ".", "", $data[maildts] ) > 0 ? $data[maildts] : '미발송' )?></TD>
	<td STYLE="PADDING-TOP:3PX;"><a href="cooperation_register.php?mode=modify&sno=<?echo($data['sno'])?>"><img src="../img/i_reply.gif"></a></td>
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
</div>

<div style="padding-top:35;"></div>

<div id=MSG01>
<table cellpadding=1 cellspacing=0 border=0 class=small_ex>
<tr><td><img src="../img/icon_list.gif" align=absmiddle>답변글이 없는 경우 문의 제목이 굵게 출력됩니다.</td></tr>
</table>
</div>
<script>cssRound('MSG01')</script>



<SCRIPT LANGUAGE="JavaScript"><!--
/*-------------------------------------
 일괄수정
-------------------------------------*/
function act_allmodify(){

	var fs = document.fmList; // 리스트폼

	if( fs['code'] == null ) return; // 레코드가 1미만인 경우

	var fieldnm = new Array( 'code', 'itemcd' ); // 필드명
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

	fmList.action = "cooperation_indb.php?mode=allmodify";
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
	fmList.action = "cooperation_indb.php?mode=delete" ;
	fmList.submit() ;
}
//--></SCRIPT>



<? include "../_footer.php"; ?>