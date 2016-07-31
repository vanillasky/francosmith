<?

$location = "기타관리 > 각종 코드관리";
include "../_header.php";

{ // 코드분류
	$b_groupcd = array();
	$res = $db->query("SELECT itemcd, itemnm FROM ".GD_CODE." WHERE groupcd='' ORDER BY sort");
	while ( $row = $db->fetch($res) ) $b_groupcd[ $row[itemcd] ] = $row[itemnm];

	if( isset( $_GET['sgroupcd'] ) == false ) $_GET['sgroupcd'] = array_shift( array_keys( $b_groupcd ) ); // 코드분류 기본값
}

list ($total) = $db->fetch("select count(*) from ".GD_CODE." where groupcd!='' and groupcd='" . $_GET['sgroupcd'] . "'"); # 총 레코드수
$res = $db->query("select sno, groupcd, itemcd, itemnm, sort from ".GD_CODE." where groupcd!='' and groupcd='" . $_GET['sgroupcd'] . "' order by sort asc");
?>

<form name=frmList>
<div class="title title_top">각종 코드관리<span>회원관심분야항목, 1:1문의항목, FAQ항목 등 각종 코드항목을 관리합니다</span> <a href="javascript:manual('<?=$guideUrl?>board/view.php?id=data&no=7')"><img src="../img/btn_q.gif" border=0 align=absmiddle></a></div>
<table class=tb>
<col class=cellC><col class=cellL>
<tr>
	<td>분류선택</td>
	<td>
		<SELECT NAME="sgroupcd" onchange="this.form.submit();">
		<option value="">↓ 분류을 선택하세요.</option>
		<?foreach ( $b_groupcd as $k => $v ){?>
		<option value='<?=$k?>' <?=$k==$_GET['sgroupcd']?" selected":""?>><?=$v?></option>
		<?}?>
		</SELECT>
	</td>
</tr>
</table>
<div class=button_top><!--<input type=image src="../img/btn_search2.gif">--></div>

<table width=100%>
<tr>
	<td class=pageInfo>총 <b><?=$total?></b>개</td>
</tr>
</table>
</form>

<form method="post" action="" name="fmList">
<INPUT TYPE="hidden" name="allmodify">
<table width=100% cellpadding=0 cellspacing=0>
<tr><td class=rnd colspan=10></td></tr>
<tr class=rndbg>
	<th width="150">코드번호</th>
	<th>코드명</th>
	<th width="150">수효</th>
	<th width="80">순서</th>
	<th width="50">수정</th>
	<th width="50">삭제</th>
</tr>
<tr><td class=rnd colspan=10></td></tr>

<?
while ($data=$db->fetch($res)){

	$pri_code = $data['groupcd'] . '|' . $data['sno'];


	list ($upRow) = $db->fetch("select count(*) from ".GD_CODE." where groupcd='" . $data['groupcd'] . "' and sort < '" . $data['sort'] . "' order by sort desc limit 1");
	list ($downRow) = $db->fetch("select count(*) from ".GD_CODE." where groupcd='" . $data['groupcd'] . "' and sort > '" . $data['sort'] . "' order by sort asc limit 1");
	?>
<INPUT TYPE="hidden" NAME="code" VALUE="<?echo($data['sno'])?>">
<tr><td height=4 colspan=10></td></tr>
<tr height=25 align="center">
	<td><b><?=$data['itemcd']?></b></td>
	<td align="left"><?=$data['itemnm']?></td>
	<td><?=$data[regdt]?></td>
	<td align="center">
	<table border="0" cellspacing="0" cellpadding="0" style="padding:0 3 0 3;">
	<tr>

	<? if ( $upRow != 0 || $downRow != 0 ){ // 정렬화살표 버튼 제어 ?>
		<td width="25%"><?if ( $upRow != 0 ){?><a href="javascript:act_modSort( 'sort_up', '<?=$pri_code?>' );"><img src="../img/ico_arrow_up.gif" alt="상위 이동" border="0" align="absmiddle" hspace="1"></a><?}?></td>
		<td width="25%"><?if ( $downRow != 0 ){?><a href="javascript:act_modSort( 'sort_down', '<?=$pri_code?>' );"><img src="../img/ico_arrow_down.gif" alt="하위 이동" border="0" align="absmiddle" hspace="1"></a><?}?></td>
	<? } ?>

		<td width="50%" align="center"><input type="text" size="25" name="sort" value="<?=$data['sort']?>" style="width:30;text-align:center" onkeyPress="if(event.keyCode == 13){ act_modSort( 'sort_direct', '<?=$pri_code?>', this.value ); }" class=cline></td>
	</tr>
	</table>
	</td>
	<td STYLE="PADDING-TOP:3PX;"><a href="javascript:popupLayer('data_code_register.php?mode=modify&sno=<?echo($data['sno'])?>')"><img src="../img/i_edit.gif"></a></td>
	<td class="noline"><input type=checkbox name=confirmyn value="<?=$data['sno']?>"></td>
</tr>
<tr><td height=4 colspan=10></td></tr>
<tr><td colspan=10 class=rndline></td></tr>
<? } ?>
</table>
<INPUT TYPE="hidden" style="width:300" NAME="nolist">
</form>

<div style="float:left;margin-top:10px;">
<img src="../img/btn_allselect_s.gif" alt="전체선택"  border="0" align='absmiddle' style="cursor:hand" <?if ( $total != 0 ){?>onclick="javascript:PubAllSordes( 'select', fmList['confirmyn'] );"<?}else{?>onclick="javascript:alert( '데이타가 존재하지 않습니다.' );"<?}?>>
<img src="../img/btn_allreselect_s.gif" alt="선택반전"  border="0" align='absmiddle' style="cursor:hand" <?if ( $total != 0 ){?>onclick="javascript:PubAllSordes( 'reflect', fmList['confirmyn'] );"<?}else{?>onclick="javascript:alert( '데이타가 존재하지 않습니다.' );"<?}?>>
<img src="../img/btn_alldeselect_s.gif" alt="선택해제"  border="0" align='absmiddle' style="cursor:hand" <?if ( $total != 0 ){?>onclick="javascript:PubAllSordes( 'deselect', fmList['confirmyn'] );"<?}else{?>onclick="javascript:alert( '데이타가 존재하지 않습니다.' );"<?}?>>
<img src="../img/btn_alldelet_s.gif" alt="선택삭제" border="0" align='absmiddle' style="cursor:hand" <?if ( $total != 0 ){?>onclick="javaScript:act_delete();"<?}else{?>onclick="javascript:alert( '데이타가 존재하지 않습니다.' );"<?}?>>
</div>

<div style="float:right;margin-top:10px;">
<A HREF="javascript:act_allmodify();"><img src="../img/btn_allmodify_s.gif" alt="일괄수정" border=0 align=absmiddle></A>
<a href="javascript:popupLayer('data_code_register.php?mode=register&groupcd=<?echo($_GET['sgroupcd'])?>')"><img src="../img/btn_regist_s.gif" alt="등록" border=0 align=absmiddle></a>
</div>


<div style="padding-top:60px"></div>


<div id=MSG01>
<table cellpadding=1 cellspacing=0 border=0 class=small_ex>
<tr><td><img src="../img/arrow_blue.gif" align=absmiddle>항목순서바꾸기 (항목번호 순서대로 사용자화면에서 보입니다)</td></tr>
<tr><td><img src="../img/icon_list.gif" align=absmiddle>방법1) <img src="../img/ico_arrow_down.gif" align=absmiddle> <img src="../img/ico_arrow_up.gif" align=absmiddle> 화살표 : 오름과 내림화살표를 눌러 순서를 바꾸세요.</td></tr>
<tr><td><img src="../img/icon_list.gif" align=absmiddle>방법2) 개별순서 수정: 번호칸에 순서숫자를 입력하고 'Enter key' 누르면 순서가 저장됩니다.</td></tr>
<tr><td><img src="../img/icon_list.gif" align=absmiddle>방법3) 전체순서 수정 : 각 '번호칸'에 순서숫자를 입력하고 [일괄수정]을 클릭하시면 됩니다.</td></tr>
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

	var fieldnm = new Array( 'code', 'sort' ); // 필드명
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

	fmList.action = "data_code_indb.php?mode=allmodify";
	fmList.submit() ;
}
//--></SCRIPT>



<SCRIPT LANGUAGE="JavaScript"><!--
/*-------------------------------------
 순서수정
-------------------------------------*/
function act_modSort( mode, code, sort ){
	fmList.action = "data_code_indb.php?mode=" + mode + "&code=" + code + "&sort=" + sort;
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
	fmList.action = "data_code_indb.php?mode=delete" ;
	fmList.submit() ;
}
//--></SCRIPT>



<? include "../_footer.php"; ?>