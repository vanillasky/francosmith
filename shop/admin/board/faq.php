<?

$location = "���ǰ��� > FAQ����";
include "../_header.php";
include "../../lib/page.class.php";
if( isset( $_GET['sitemcd'] ) == false ) $_GET['sitemcd'] = array_shift( array_keys( codeitem('faq') ) ); // �з� �⺻��

list ($total) = $db->fetch("select count(*) from ".GD_FAQ.""); # �� ���ڵ��

### �����Ҵ�
if (!$_GET[page_num]) $_GET[page_num] = 10; # ������ ���ڵ��
$selected[page_num][$_GET[page_num]] = "selected";

$orderby = ($_GET[sort]) ? $_GET[sort] : "sort asc"; # ���� ����
$selected[sort][$orderby] = "selected";

$selected[skey][$_GET[skey]] = "selected";
$selected[sitemcd][$_GET[sitemcd]] = "selected";
$selected[sbest][$_GET[sbest]] = "checked";

### ���
$pg = new Page($_GET[page],$_GET[page_num]); # ����¡ ����
$pg->field = "sno, itemcd, question, sort, best, bestsort, date_format( regdt, '%Y.%m.%d' ) as regdts"; # �ʵ� ����
$db_table = "".GD_FAQ.""; # ���̺� ����

if ($_GET[skey] && $_GET[sword]){
	if ( $_GET[skey]== 'all' ){
		$where[] = "concat( question, descant, answer ) like '%$_GET[sword]%'";
	}
	else $where[] = "$_GET[skey] like '%$_GET[sword]%'";
}

if ( $_GET[sitemcd] <> '' && $_GET[sitemcd] <> 'all' ) $where[] = "itemcd='" . $_GET[sitemcd] . "'"; # �з��˻�

if ( $_GET[sbest] <> '' ) $where[] = "best='" . $_GET[sbest] . "'"; # ����Ʈ����

if ($_GET[sregdt][0] && $_GET[sregdt][1]) $where[] = "regdt between date_format({$_GET[sregdt][0]},'%Y-%m-%d 00:00:00') and date_format({$_GET[sregdt][1]},'%Y-%m-%d 23:59:59')";

$pg->setQuery($db_table,$where,$orderby); # ����¡ ���� ����
$pg->exec(); # ?

$res = $db->query($pg->query);
?>

<form name=frmList>
<div class="title title_top">FAQ����<span>������ �����ϴ� ������ �̸� �����ؼ� �ۼ��մϴ� <a href="javascript:manual('<?=$guideUrl?>board/view.php?id=board&no=8')"><img src="../img/btn_q.gif" border=0 align=absmiddle hspace=2></a></div>
<table class=tb>
<col class=cellC><col class=cellL><col class=cellC><col class=cellL>
<tr>
	<td>Ű����</td>
	<td>
	<select name="skey">
	<option value="all" <?=$selected[skey]['all']?>> ���հ˻� </option>
	<option value="question" <?=$selected[skey]['question']?>> ���� ( �ܹ� ) </option>
	<option value="descant" <?=$selected[skey]['descant']?>> ���� ( �幮 ) </option>
	<option value="answer" <?=$selected[skey]['answer']?>> �亯 </option>
	</select> <input type="text" NAME="sword" value="<?=$_GET['sword']?>" class=line>
	</td>
	<td>�з�����</td>
	<td>
	<select name="sitemcd">
	<option value="all" <?=$selected[sitemcd]['all']?>> - ��ü - </option>
	<?foreach ( codeitem('faq') as $k => $v ){?>
	<option value='<?=$k?>' <?=$selected[sitemcd][$k]?>><?=$v?></option>
	<?}?>
	</select>
	</td>
</tr>
<tr>
	<td>����Ʈ����</td>
	<td colspan="3" class=noline>
	<label for="r1"><input type="radio" id="r1" name="sbest" value="Y" <?=$selected[sbest]['Y']?>> ����Ʈ </label>
	<label for="r2"><input type="radio" id="r2" name="sbest" value="N" <?=$selected[sbest]['N']?>> �̼��� </label>
	</select>
	</td>
</tr>
<tr>
	<td>�����</td>
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
	�� <b><?=number_format($total)?></b>��, �˻� <b><?=number_format($pg->recode[total])?></b>��, <b><?=number_format($pg->page[now])?></b> of <?=number_format($pg->page[total])?> Pages
	</td>
	<td align=right>
	<select name="sort" onchange="this.form.submit();">
	<option value="regdt desc" <?=$selected[sort]['regdt desc']?>>- ����� ���ġ�</option>
	<option value="regdt asc" <?=$selected[sort]['regdt asc']?>>- ����� ���ġ�</option>
	<option value="sort desc" <?=$selected[sort]['sort desc']?>>- ��¼��� ���ġ�</option>
	<option value="sort asc" <?=$selected[sort]['sort asc']?>>- ��¼��� ���ġ�</option>
    <optgroup label="------------"></optgroup>
	<option value="question desc" <?=$selected[sort]['question desc']?>>- ����( �ܹ� ) ���ġ�</option>
	<option value="question asc" <?=$selected[sort]['question asc']?>>- ����( �ܹ� ) ���ġ�</option>
	<option value="itemcd desc" <?=$selected[sort]['itemcd desc']?>>- �������� ���ġ�</option>
	<option value="itemcd asc" <?=$selected[sort]['itemcd asc']?>>- �������� ���ġ�</option>
	<option value="bestsort desc" <?=$selected[sort]['bestsort desc']?>>- ����Ʈ ��¼� ���ġ�</option>
	<option value="bestsort asc" <?=$selected[sort]['bestsort asc']?>>- ����Ʈ ��¼� ���ġ�</option>
	</select>&nbsp;
	<select name=page_num onchange="this.form.submit()">
	<?
	$r_pagenum = array(10,20,40,60,100);
	foreach ($r_pagenum as $v){
	?>
	<option value="<?=$v?>" <?=$selected[page_num][$v]?>><?=$v?>�� ���
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
	<th width="60">��ȣ</th>
	<th width="110">��������</th>
	<th>����</th>
	<th width="70">�����</th>
	<th width="120">����Ʈ(����)</th>
	<th width="80">����</th>
	<th width="50">����</th>
	<th width="50">����</th>
</tr>
<tr><td class=rnd colspan=10></td></tr>

<?
while ($data=$db->fetch($res)){

	$pri_code = $data['itemcd'] . '|' . $data['sno'];

	if ( $_GET['sitemcd'] <> '' && $selected[sort]['sort asc'] == 'selected' ){ // ����ȭ��ǥ ��ư ����

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
	<option value=""> - �з� - </option>
	<?foreach ( codeitem('faq') as $k => $v ){?>
	<option value='<?=$k?>' <?=( $k == $data['itemcd'] ? 'selected' : '' )?>><?=$v?></option>
	<?}?>
	</select>
	</td>
	<td align=left><a href="faq_register.php?mode=modify&sno=<?echo($data['sno'])?>"><?=$data[question]?></a></td>
	<td><font class=ver8 color=444444><?=$data[regdts]?></td>
	<td>
	<select name="best">
	<option value=""> - ���� - </option>
	<option value="Y" <?=( 'Y' == $data['best'] ? 'selected' : '' )?>> ����Ʈ </option>
	<option value="N" <?=( 'N' == $data['best'] ? 'selected' : '' )?>> �̼��� </option>
	</select>&nbsp;<input type="text" size="3" name="bestsort" value="<?=$data['bestsort']?>" style="width:30;text-align:center" class=line>
	</td>
	<td align="center">
	<table border="0" cellspacing="0" cellpadding="0" style="padding:0 3 0 3;">
	<tr>

	<? if ( $upRow != 0 || $downRow != 0 ){ // ����ȭ��ǥ ��ư ���� ?>
		<td width="25%"><?if ( $upRow != 0 ){?><a href="javascript:act_modSort( 'sort_up', '<?=$pri_code?>' );"><img src="../img/ico_arrow_up.gif" alt="���� �̵�" border="0" align="absmiddle" hspace="1"></a><?}?></td>
		<td width="25%"><?if ( $downRow != 0 ){?><a href="javascript:act_modSort( 'sort_down', '<?=$pri_code?>' );"><img src="../img/ico_arrow_down.gif" alt="���� �̵�" border="0" align="absmiddle" hspace="1"></a><?}?></td>
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
<img src="../img/btn_allselect_s.gif" alt="��ü����"  border="0" align='absmiddle' style="cursor:hand" <?if ( $pg->recode[total] != 0 ){?>onclick="javascript:PubAllSordes( 'select', fmList['confirmyn'] );"<?}else{?>onclick="javascript:alert( '����Ÿ�� �������� �ʽ��ϴ�.' );"<?}?>>
<img src="../img/btn_allreselect_s.gif" alt="���ù���"  border="0" align='absmiddle' style="cursor:hand" <?if ( $pg->recode[total] != 0 ){?>onclick="javascript:PubAllSordes( 'reflect', fmList['confirmyn'] );"<?}else{?>onclick="javascript:alert( '����Ÿ�� �������� �ʽ��ϴ�.' );"<?}?>>
<img src="../img/btn_alldeselect_s.gif" alt="��������"  border="0" align='absmiddle' style="cursor:hand" <?if ( $pg->recode[total] != 0 ){?>onclick="javascript:PubAllSordes( 'deselect', fmList['confirmyn'] );"<?}else{?>onclick="javascript:alert( '����Ÿ�� �������� �ʽ��ϴ�.' );"<?}?>>
<img src="../img/btn_alldelet_s.gif" alt="���û���" border="0" align='absmiddle' style="cursor:hand" <?if ( $pg->recode[total] != 0 ){?>onclick="javaScript:act_delete();"<?}else{?>onclick="javascript:alert( '����Ÿ�� �������� �ʽ��ϴ�.' );"<?}?>>
</div>

<div style="float:right;">
<A HREF="javascript:act_allmodify();"><img src="../img/btn_allmodify_s.gif" alt="�ϰ�����" border=0 align=absmiddle></A>
<a href="faq_register.php"><img src="../img/btn_regist_s.gif" alt="���" border=0 align=absmiddle></a>
</div>

<div style="padding-top:35;"></div>

<div id=MSG01>
<table cellpadding=1 cellspacing=0 border=0 class=small_ex>
<tr><td><img src="../img/arrow_blue.gif" align=absmiddle>'��¼���' ����</font></td></tr>
<tr><td><img src="../img/icon_list.gif" align=absmiddle>������� : �������� ���</td></tr>
<tr><td><img src="../img/icon_list.gif" align=absmiddle>���1) ȭ��ǥ : �������� ��ĭ�� �̵�. ��, �з��˻� �� ���Ĺ���� '��¼��� ���ġ�' �� ��츸 �����մϴ�.</td></tr>
<tr><td><img src="../img/icon_list.gif" align=absmiddle>���2) �������� : '��¼���' ĭ�� �Է� �� �ش� ĭ������ 'Enter key' Ŭ���Ͻø� �ڵ����� ��ü�� �����ĵ˴ϴ�.</td></tr>
<tr><td><img src="../img/icon_list.gif" align=absmiddle>���3) �ϰ����� : �� '��¼���' ĭ�� �Է� �� [�ϰ�����]�� Ŭ���Ͻø� �˴ϴ�.</td></tr>
<tr><td><img src="../img/icon_list.gif" align=absmiddle>����Ʈ>�� �����س����� ����Ʈ���� 5���� ������ ����ȭ�鿡 ������ �˴ϴ�.</td></tr>

</table>
</div>
<script>cssRound('MSG01')</script>



<SCRIPT LANGUAGE="JavaScript"><!--
/*-------------------------------------
 �ϰ�����
-------------------------------------*/
/* ������ �Լ� 
function act_allmodify(){

	var fs = document.fmList; // ����Ʈ��

	if( fs['code'] == null ) return; // ���ڵ尡 1�̸��� ���

	var fieldnm = new Array( 'code', 'sort', 'itemcd', 'best', 'bestsort' ); // �ʵ��
	var csField = new Array(); // �ʵ嵥��Ÿ����

	for( var nm in fieldnm ) csField[ fieldnm[nm] ] = ''; // �ʵ嵥��Ÿ�ʱ�ȭ

	var count = fs['code'].length;	// ���ڵ��

	if( count == undefined ){ // ���ڵ���� 1�� �� ���

		for( var nm in fieldnm ){
			var Obj = eval( "fs['" + fieldnm[nm] + "']" );
			if( Obj.type != 'checkbox' ) csField[ fieldnm[nm] ] += Obj.value + ";"; else csField[ fieldnm[nm] ] += Obj.checked + ";";
		}
	}
	else { // ���ڵ���� 2�� �̻��� ���

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
// ���� �� �Լ�
function act_allmodify(){

	var fs = document.fmList; // ����Ʈ��

	if( fs['code'] == null ) return; // ���ڵ尡 1�̸��� ���

	var fieldnm = new Array( 'code', 'sort', 'itemcd', 'best', 'bestsort' ); // �ʵ��
	var csField = new Array(); // �ʵ嵥��Ÿ����

	// �ʵ嵥��Ÿ�ʱ�ȭ
	fieldnm.each(function(item) { 
		csField[item] = '';
	}); 
	

	var count = fs['code'].length;	// ���ڵ��

	if( count == undefined ){ // ���ڵ���� 1�� �� ���

		fieldnm.each(function(item) { 
			var Obj = eval( "fs['" + item + "']" );
			if( Obj.type != 'checkbox' ) csField[item] += Obj.value + ";"; else csField[item] += Obj.checked + ";";
		});
	}
	else { // ���ڵ���� 2�� �̻��� ���

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
 ��������
-------------------------------------*/
function act_modSort( mode, code, sort ){
	fmList.action = "faq_indb.php?mode=" + mode + "&code=" + code + "&sort=" + sort;
	fmList.submit() ;
}
//--></SCRIPT>



<SCRIPT LANGUAGE=JavaScript><!--
/*-------------------------------------
 ����
-------------------------------------*/
function act_delete(){

	if ( PubChkSelect( fmList['confirmyn'] ) == false ){
		alert( "�����Ͻ� ������ �����Ͽ� �ֽʽÿ�." );
		return;
	}

	if ( confirm( "������ �������� ���� �����Ͻðڽ��ϱ�?\n���� �� ������ �� �����ϴ�." ) == false ) return;

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