<?

$location = "��Ÿ���� > ���� �ڵ����";
include "../_header.php";

{ // �ڵ�з�
	$b_groupcd = array();
	$res = $db->query("SELECT itemcd, itemnm FROM ".GD_CODE." WHERE groupcd='' ORDER BY sort");
	while ( $row = $db->fetch($res) ) $b_groupcd[ $row[itemcd] ] = $row[itemnm];

	if( isset( $_GET['sgroupcd'] ) == false ) $_GET['sgroupcd'] = array_shift( array_keys( $b_groupcd ) ); // �ڵ�з� �⺻��
}

list ($total) = $db->fetch("select count(*) from ".GD_CODE." where groupcd!='' and groupcd='" . $_GET['sgroupcd'] . "'"); # �� ���ڵ��
$res = $db->query("select sno, groupcd, itemcd, itemnm, sort from ".GD_CODE." where groupcd!='' and groupcd='" . $_GET['sgroupcd'] . "' order by sort asc");
?>

<form name=frmList>
<div class="title title_top">���� �ڵ����<span>ȸ�����ɺо��׸�, 1:1�����׸�, FAQ�׸� �� ���� �ڵ��׸��� �����մϴ�</span> <a href="javascript:manual('<?=$guideUrl?>board/view.php?id=data&no=7')"><img src="../img/btn_q.gif" border=0 align=absmiddle></a></div>
<table class=tb>
<col class=cellC><col class=cellL>
<tr>
	<td>�з�����</td>
	<td>
		<SELECT NAME="sgroupcd" onchange="this.form.submit();">
		<option value="">�� �з��� �����ϼ���.</option>
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
	<td class=pageInfo>�� <b><?=$total?></b>��</td>
</tr>
</table>
</form>

<form method="post" action="" name="fmList">
<INPUT TYPE="hidden" name="allmodify">
<table width=100% cellpadding=0 cellspacing=0>
<tr><td class=rnd colspan=10></td></tr>
<tr class=rndbg>
	<th width="150">�ڵ��ȣ</th>
	<th>�ڵ��</th>
	<th width="150">��ȿ</th>
	<th width="80">����</th>
	<th width="50">����</th>
	<th width="50">����</th>
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

	<? if ( $upRow != 0 || $downRow != 0 ){ // ����ȭ��ǥ ��ư ���� ?>
		<td width="25%"><?if ( $upRow != 0 ){?><a href="javascript:act_modSort( 'sort_up', '<?=$pri_code?>' );"><img src="../img/ico_arrow_up.gif" alt="���� �̵�" border="0" align="absmiddle" hspace="1"></a><?}?></td>
		<td width="25%"><?if ( $downRow != 0 ){?><a href="javascript:act_modSort( 'sort_down', '<?=$pri_code?>' );"><img src="../img/ico_arrow_down.gif" alt="���� �̵�" border="0" align="absmiddle" hspace="1"></a><?}?></td>
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
<img src="../img/btn_allselect_s.gif" alt="��ü����"  border="0" align='absmiddle' style="cursor:hand" <?if ( $total != 0 ){?>onclick="javascript:PubAllSordes( 'select', fmList['confirmyn'] );"<?}else{?>onclick="javascript:alert( '����Ÿ�� �������� �ʽ��ϴ�.' );"<?}?>>
<img src="../img/btn_allreselect_s.gif" alt="���ù���"  border="0" align='absmiddle' style="cursor:hand" <?if ( $total != 0 ){?>onclick="javascript:PubAllSordes( 'reflect', fmList['confirmyn'] );"<?}else{?>onclick="javascript:alert( '����Ÿ�� �������� �ʽ��ϴ�.' );"<?}?>>
<img src="../img/btn_alldeselect_s.gif" alt="��������"  border="0" align='absmiddle' style="cursor:hand" <?if ( $total != 0 ){?>onclick="javascript:PubAllSordes( 'deselect', fmList['confirmyn'] );"<?}else{?>onclick="javascript:alert( '����Ÿ�� �������� �ʽ��ϴ�.' );"<?}?>>
<img src="../img/btn_alldelet_s.gif" alt="���û���" border="0" align='absmiddle' style="cursor:hand" <?if ( $total != 0 ){?>onclick="javaScript:act_delete();"<?}else{?>onclick="javascript:alert( '����Ÿ�� �������� �ʽ��ϴ�.' );"<?}?>>
</div>

<div style="float:right;margin-top:10px;">
<A HREF="javascript:act_allmodify();"><img src="../img/btn_allmodify_s.gif" alt="�ϰ�����" border=0 align=absmiddle></A>
<a href="javascript:popupLayer('data_code_register.php?mode=register&groupcd=<?echo($_GET['sgroupcd'])?>')"><img src="../img/btn_regist_s.gif" alt="���" border=0 align=absmiddle></a>
</div>


<div style="padding-top:60px"></div>


<div id=MSG01>
<table cellpadding=1 cellspacing=0 border=0 class=small_ex>
<tr><td><img src="../img/arrow_blue.gif" align=absmiddle>�׸�����ٲٱ� (�׸��ȣ ������� �����ȭ�鿡�� ���Դϴ�)</td></tr>
<tr><td><img src="../img/icon_list.gif" align=absmiddle>���1) <img src="../img/ico_arrow_down.gif" align=absmiddle> <img src="../img/ico_arrow_up.gif" align=absmiddle> ȭ��ǥ : ������ ����ȭ��ǥ�� ���� ������ �ٲټ���.</td></tr>
<tr><td><img src="../img/icon_list.gif" align=absmiddle>���2) �������� ����: ��ȣĭ�� �������ڸ� �Է��ϰ� 'Enter key' ������ ������ ����˴ϴ�.</td></tr>
<tr><td><img src="../img/icon_list.gif" align=absmiddle>���3) ��ü���� ���� : �� '��ȣĭ'�� �������ڸ� �Է��ϰ� [�ϰ�����]�� Ŭ���Ͻø� �˴ϴ�.</td></tr>
</table>
</div>
<script>cssRound('MSG01')</script>

<SCRIPT LANGUAGE="JavaScript"><!--
/*-------------------------------------
 �ϰ�����
-------------------------------------*/
function act_allmodify(){

	var fs = document.fmList; // ����Ʈ��

	if( fs['code'] == null ) return; // ���ڵ尡 1�̸��� ���

	var fieldnm = new Array( 'code', 'sort' ); // �ʵ��
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

	fmList.action = "data_code_indb.php?mode=allmodify";
	fmList.submit() ;
}
//--></SCRIPT>



<SCRIPT LANGUAGE="JavaScript"><!--
/*-------------------------------------
 ��������
-------------------------------------*/
function act_modSort( mode, code, sort ){
	fmList.action = "data_code_indb.php?mode=" + mode + "&code=" + code + "&sort=" + sort;
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
	fmList.action = "data_code_indb.php?mode=delete" ;
	fmList.submit() ;
}
//--></SCRIPT>



<? include "../_footer.php"; ?>