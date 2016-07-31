<?

if (basename($_SERVER['PHP_SELF']) == 'popup.banner.php'){
	include "../_header.popup.php";
	$popupWin = true;
}
else {
	$location = "디자인관리 > 로고/배너 관리";
	include "../_header.php";
}
include "../../lib/page.class.php";

# 로고/배너위치 정의파일
if ( file_exists( $tmp = dirname(__FILE__) . "/../../conf/config.banner_".$cfg['tplSkinWork'].".php" ) ) @include $tmp;
else @include dirname(__FILE__) . "/../../conf/config.banner.php";

if(!$b_loccd['90']) $b_loccd['90']	= "메인로고";
if(!$b_loccd['91']) $b_loccd['91']	= "하단로고";
if(!$b_loccd['92']) $b_loccd['92']	= "메일로고";
if(!$b_loccd['93']) $b_loccd['93']	= "로고위치입력";
if(!$b_loccd['94']) $b_loccd['94']	= "로고위치입력";
if(!$b_loccd['95']) $b_loccd['95']	= "로고위치입력";

if ( isset( $_GET['sloccd'] ) == false ) $_GET['sloccd'] = 'all'; // 위치 기본값

# WebFTP 선언
include dirname(__FILE__) . "/webftp/webftp.class.php";
$webftp = new webftp;
$webftp->ftp_path = str_replace( $_SERVER['SCRIPT_NAME'], "", $_SERVER['SCRIPT_FILENAME'] ) . $cfg['rootDir'] . '/data/skin/' . $cfg['tplSkinWork']; # 스킨경로

list ($total) = $db->fetch("select count(*) from ".GD_BANNER." where tplSkin = '".$cfg['tplSkinWork']."'"); # 총 레코드수

### 변수할당
if (!$_GET['page_num']) $_GET['page_num'] = 10; # 페이지 레코드수
$selected['page_num'][$_GET['page_num']] = "selected";

$orderby = ($_GET['sort']) ? $_GET['sort'] : "abs(loccd) desc"; # 정렬 쿼리
$selected['sort'][$orderby] = "selected";

### 목록
$pg = new Page($_GET['page'],$_GET['page_num']); # 페이징 선언
$pg->field = "sno, loccd, img, regdt, sort"; # 필드 쿼리
$db_table = GD_BANNER; # 테이블 쿼리

$where[] = "tplSkin = '".$cfg['tplSkinWork']."'";
if ( $_GET['sloccd'] <> '' && $_GET['sloccd'] <> 'all' ) $where[] = "loccd='" . $_GET['sloccd'] . "'"; # 위치검색

$pg->setQuery($db_table,$where,$orderby); # 페이징 쿼리 실행
$pg->exec(); # ?

$res = $db->query($pg->query);
?>

<form name="frmList">
<div class="title title_top">로고/배너관리<span>로고와 배너를 등록하고 수정하는 영역입니다</span> <a href="javascript:manual('<?=$guideUrl?>board/view.php?id=design&no=6')"><img src="../img/btn_q.gif" align="absmiddle" hspace="2" /></a></div>
<?=$workSkinStr?>
<table class="tb">
<col class="cellC" /><col class="cellL" />
<tr>
	<td>배너위치 등록하기</td>
    <td>새롭게 배너를 추가등록하려면 먼저 <a href="javascript:popupLayer('../design/design_banner_loccd.php',780,600);"><img src="../img/btn_bangroup.gif" align="absmiddle" /></a> 부터 잡으세요. <a href="javascript:manual('<?=$guideUrl?>board/view.php?id=design&no=6')"><img src="../img/btn_bn_manual.gif" align="absmiddle" /></a></td>
</tr>
<tr>
	<td>로고/배너 선택보기</td>
	<td>
		<SELECT NAME="sloccd" onchange="this.form.submit();">
		<option value="all">==== 전체보기 ====</option>
		<optgroup label="-- 로고보기 --"></optgroup>
		<?
		# 로고용
		foreach ( $b_loccd as $lKey => $lVal ){
			if( $lKey < 90 ) continue;
		?>
		<option value="<?=$lKey?>" <?=$lKey==$_GET['sloccd']?" selected":""?>><?=$lVal?></option>
		<?}?>
		<optgroup label="-- 베너보기 --"></optgroup>
		<?
		# 베너용
		foreach ( $b_loccd as $k => $v ){
			if( $k >= 90 ) continue;
		?>
		<option value="<?=$k?>" <?=$k==$_GET['sloccd']?" selected":""?>><?=$v?></option>
		<?}?>
		</SELECT>  선택하면 해당 위치의 로고/배너만 볼 수 있습니다.
	</td>
</tr>
</table>
<div class="button_top"><!--<input type=image src="../img/btn_search2.gif" />--></div>

<table width="100%">
<tr>
	<td class="pageInfo">
	총 <b><?=number_format($total)?></b>개, 검색 <b><?=number_format($pg->recode['total'])?></b>개, <b><?=number_format($pg->page['now'])?></b> of <?=number_format($pg->page['total'])?> Pages
	</td>
	<!--<td><font color=EA0095><b>**</b></font> <a href="http://www.godomall.co.kr/edu/edu_board_list.html?cate=design&in_view=y&sno=166#Go_view" target=_blank><font class=small1 color=EA0095><b><u>로고/배너등록에 대한 자세한 매뉴얼 보기 <font color=0074BA>[필독]</font></u></b></font></a> <font color=EA0095><b>**</b></font></td>-->
	<td align="right">
	<select name="sort" onchange="this.form.submit();">
	<option value="abs(loccd) desc" <?=$selected['sort']['abs(loccd) desc']?>>- 치환코드 정렬↑</option>
	<option value="abs(loccd) asc" <?=$selected['sort']['abs(loccd) asc']?>>- 치환코드 정렬↓</option>
	<option value="regdt desc" <?=$selected['sort']['regdt desc']?>>- 등록일 정렬↑</option>
	<option value="regdt asc" <?=$selected['sort']['regdt asc']?>>- 등록일 정렬↓</option>
	<option value="sort desc" <?=$selected['sort']['sort desc']?>>- 출력순서 정렬↑</option>
	<option value="sort asc" <?=$selected['sort']['sort asc']?>>- 출력순서 정렬↓</option>
	</select>&nbsp;
	<select name="page_num" onchange="this.form.submit();">
	<?
	$r_pagenum = array(10,20,40,60,100);
	foreach ($r_pagenum as $v){
	?>
	<option value="<?=$v?>" <?=$selected['page_num'][$v]?>><?=$v?>개 출력
	<? } ?>
	</select>
	</td>
</tr>
</table>
</form>

<form method="post" action="" name="fmList">
<input TYPE="hidden" name="allmodify" />
<table width="100%" cellpadding="0" cellspacing="0">
<tr><td class="rnd" colspan="10"></td></tr>
<tr class="rndbg">
	<th width="40">번호</th>
	<th width="130">로고/배너위치</th>
	<th>치환코드</th>
	<th>이미지</th>
	<th width="130">등록일</th>
	<th width="100">순서</th>
	<th width="50">수정</th>
	<th width="50">삭제</th>
</tr>
<tr><td class="rnd" colspan="10"></td></tr>

<?
while ($data=$db->fetch($res)){

	$pri_code = $data['loccd'] . '|' . $data['sno'];

	if ( $_GET['sloccd'] <> '' && $selected['sort']['sort asc'] == 'selected' ){ // 정렬화살표 버튼 제어

		list ($upRow) = $db->fetch("select count(*) from ".GD_BANNER." where loccd='" . $data['loccd'] . "' and sort < '" . $data['sort'] . "' order by sort desc limit 1");
		list ($downRow) = $db->fetch("select count(*) from ".GD_BANNER." where loccd='" . $data['loccd'] . "' and sort > '" . $data['sort'] . "' order by sort asc limit 1");
	}
	?>
<input type="hidden" name="code" value="<?echo($data['sno'])?>" />
<tr><td height="4" colspan="10"></td></tr>
<tr height="25" align="center">
	<td><font class="ver81" color="444444"><?=$pg->idx--?></td>
	<td style="padding:0 5px" align="left">
	<?=$b_loccd[ $data['loccd'] ]?><!--로고/배너번호 : <b><?=$data['sno']?></b>-->
	</td>
	<td style="font:8pt tahoma">{@dataBanner(<?=$data['loccd']?>)}</td>
	<td><?=$webftp->confirmImage( "../../data/skin/" . $cfg['tplSkinWork'] . "/img/banner/" . $data['img'],200,50,"0");?></td>
	<td><font class="ver81" color="444444"><?=$data[regdt]?></td>
	<td align="center">
	<table border="0" cellspacing="0" cellpadding="0" style="padding:0 3px 0 3px;">
	<tr>

	<? if ( $upRow != 0 || $downRow != 0 ){ // 정렬화살표 버튼 제어 ?>
		<td width="25%"><?if ( $upRow != 0 ){?><a href="javascript:act_modSort( 'sort_up', '<?=$pri_code?>' );"><img src="../img/ico_arrow_up.gif" alt="상위 이동" border="0" align="absmiddle" hspace="1" /></a><?}?></td>
		<td width="25%"><?if ( $downRow != 0 ){?><a href="javascript:act_modSort( 'sort_down', '<?=$pri_code?>' );"><img src="../img/ico_arrow_down.gif" alt="하위 이동" border="0" align="absmiddle" hspace="1" /></a><?}?></td>
	<? } ?>

		<td width="50%" align="center"><input type="text" size="25" name="sort" value="<?=$data['sort']?>" style="width:30;text-align:center" onkeyPress="if(event.keyCode == 13){ act_modSort( 'sort_direct', '<?=$pri_code?>', this.value ); }" /></td>
	</tr>
	</table>
	</td>
	<td style="padding-top:3px;"><a href="design_banner_register.php?mode=modify&sno=<?echo($data['sno'])?>"><img src="../img/i_edit.gif" /></a></td>
	<td class="noline"><input type="checkbox" name="confirmyn" value="<?=$data['sno']?>" /></td>
</tr>
<tr><td height="4" colspan="10"></td></tr>
<tr><td colspan="10" class="rndline"></td></tr>
<? } ?>
</table>
<input type="hidden" style="width:300" name="nolist" />
</form>

<div align="center" class="pageNavi"><?=$pg->page[navi]?></div>

<div style="float:left;">
<img src="../img/btn_allselect_s.gif" alt="전체선택"  border="0" align='absmiddle' style="cursor:hand" <?if ( $pg->recode['total'] != 0 ){?>onclick="javascript:PubAllSordes( 'select', fmList['confirmyn'] );"<?}else{?>onclick="javascript:alert( '데이타가 존재하지 않습니다.' );"<?}?> />
<img src="../img/btn_allreselect_s.gif" alt="선택반전"  border="0" align='absmiddle' style="cursor:hand" <?if ( $pg->recode['total'] != 0 ){?>onclick="javascript:PubAllSordes( 'reflect', fmList['confirmyn'] );"<?}else{?>onclick="javascript:alert( '데이타가 존재하지 않습니다.' );"<?}?> />
<img src="../img/btn_alldeselect_s.gif" alt="선택해제"  border="0" align='absmiddle' style="cursor:hand" <?if ( $pg->recode['total'] != 0 ){?>onclick="javascript:PubAllSordes( 'deselect', fmList['confirmyn'] );"<?}else{?>onclick="javascript:alert( '데이타가 존재하지 않습니다.' );"<?}?> />
<img src="../img/btn_alldelet_s.gif" alt="선택삭제" border="0" align='absmiddle' style="cursor:hand" <?if ( $pg->recode['total'] != 0 ){?>onclick="javaScript:act_delete();"<?}else{?>onclick="javascript:alert( '데이타가 존재하지 않습니다.' );"<?}?> />
</div>

<div style="float:right;">
<A HREF="javascript:act_allmodify();"><img src="../img/btn_allmodify_s.gif" alt="일괄수정" align="absmiddle" /></A>
<a href="design_banner_register.php"><img src="../img/btn_regist_s.gif" alt="등록" align="absmiddle" /></a>
</div>

<div style="padding-top:35;"></div>

<div id="MSG01">
<table cellpadding="1" cellspacing="0" border="0" class="small_ex">
<tr><td><img src="../img/icon_list.gif" align="absmiddle" />치환코드 : 치환코드를 해당 페이지의 HTML 소스에 입력하여 활용할 수 있습니다.</td></tr>
<tr><td style="padding-top:10px"><img src="../img/icon_list.gif" align="absmiddle" />로고/배너순서바꾸기</td></tr>
<tr><td><img src="../img/icon_list.gif" align="absmiddle" />방법1) 개별순서 수정: 번호칸에 순서숫자를 입력하고 'Enter key' 누르면 순서가 저장됩니다.</td></tr>
<tr><td><img src="../img/icon_list.gif" align="absmiddle" />방법2) 전체순서 수정 : 각 '번호칸'에 순서숫자를 입력하고 [일괄수정]을 클릭하시면 됩니다.</td></tr>
</table>
</div>
<script>cssRound('MSG01')</script>



<SCRIPT LANGUAGE="JavaScript"><!--
/*-------------------------------------
 일괄수정
-------------------------------------*/
/* 수정 전 함수
function act_allmodify(){

	var fs = document.fmList; // 리스트폼

	if( fs['code'] == null ) return; // 레코드가 1미만인 경우

	var fieldnm = new Array( 'code', 'sort' ); // 필드명
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

	fmList.action = "design_banner_indb.php?mode=allmodify";
	fmList.submit() ;
}
*/
// 수정 후 함수
function act_allmodify(){

	var fs = document.fmList; // 리스트폼

	if( fs['code'] == null ) return; // 레코드가 1미만인 경우

	var fieldnm = new Array('code', 'sort'); // 필드명

	var csField = new Array(); // 필드데이타저장

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
				var Obj =fs[item];
				if( Obj[i].type != 'checkbox' ) csField[item] += Obj[i].value + ";"; else csField[item] += Obj[i].checked + ";";
			});
		}
	}

	fieldnm.each(function(item) {
		fs.allmodify.value += item + '==' + csField[item] + '||';
	});

	fmList.action = "design_banner_indb.php?mode=allmodify";
	fmList.submit();
}
//--></SCRIPT>



<SCRIPT LANGUAGE="JavaScript"><!--
/*-------------------------------------
 순서수정
-------------------------------------*/
function act_modSort( mode, code, sort ){
	fmList.action = "design_banner_indb.php?mode=" + mode + "&code=" + code + "&sort=" + sort;
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
	fmList.action = "design_banner_indb.php?mode=delete" ;
	fmList.submit() ;
}
//--></SCRIPT>



<?
if ($popupWin === true){
	echo '<script>table_design_load();</script>';
}
else {
	include "../_footer.php";
}
?>