<?

if (basename($_SERVER['PHP_SELF']) == 'popup.banner.php'){
	include "../_header.popup.php";
	$popupWin = true;
}
else {
	$location = "�����ΰ��� > �ΰ�/��� ����";
	include "../_header.php";
}
include "../../lib/page.class.php";

# �ΰ�/�����ġ ��������
if ( file_exists( $tmp = dirname(__FILE__) . "/../../conf/config.banner_".$cfg['tplSkinWork'].".php" ) ) @include $tmp;
else @include dirname(__FILE__) . "/../../conf/config.banner.php";

if(!$b_loccd['90']) $b_loccd['90']	= "���ηΰ�";
if(!$b_loccd['91']) $b_loccd['91']	= "�ϴܷΰ�";
if(!$b_loccd['92']) $b_loccd['92']	= "���Ϸΰ�";
if(!$b_loccd['93']) $b_loccd['93']	= "�ΰ���ġ�Է�";
if(!$b_loccd['94']) $b_loccd['94']	= "�ΰ���ġ�Է�";
if(!$b_loccd['95']) $b_loccd['95']	= "�ΰ���ġ�Է�";

if ( isset( $_GET['sloccd'] ) == false ) $_GET['sloccd'] = 'all'; // ��ġ �⺻��

# WebFTP ����
include dirname(__FILE__) . "/webftp/webftp.class.php";
$webftp = new webftp;
$webftp->ftp_path = str_replace( $_SERVER['SCRIPT_NAME'], "", $_SERVER['SCRIPT_FILENAME'] ) . $cfg['rootDir'] . '/data/skin/' . $cfg['tplSkinWork']; # ��Ų���

list ($total) = $db->fetch("select count(*) from ".GD_BANNER." where tplSkin = '".$cfg['tplSkinWork']."'"); # �� ���ڵ��

### �����Ҵ�
if (!$_GET['page_num']) $_GET['page_num'] = 10; # ������ ���ڵ��
$selected['page_num'][$_GET['page_num']] = "selected";

$orderby = ($_GET['sort']) ? $_GET['sort'] : "abs(loccd) desc"; # ���� ����
$selected['sort'][$orderby] = "selected";

### ���
$pg = new Page($_GET['page'],$_GET['page_num']); # ����¡ ����
$pg->field = "sno, loccd, img, regdt, sort"; # �ʵ� ����
$db_table = GD_BANNER; # ���̺� ����

$where[] = "tplSkin = '".$cfg['tplSkinWork']."'";
if ( $_GET['sloccd'] <> '' && $_GET['sloccd'] <> 'all' ) $where[] = "loccd='" . $_GET['sloccd'] . "'"; # ��ġ�˻�

$pg->setQuery($db_table,$where,$orderby); # ����¡ ���� ����
$pg->exec(); # ?

$res = $db->query($pg->query);
?>

<form name="frmList">
<div class="title title_top">�ΰ�/��ʰ���<span>�ΰ�� ��ʸ� ����ϰ� �����ϴ� �����Դϴ�</span> <a href="javascript:manual('<?=$guideUrl?>board/view.php?id=design&no=6')"><img src="../img/btn_q.gif" align="absmiddle" hspace="2" /></a></div>
<?=$workSkinStr?>
<table class="tb">
<col class="cellC" /><col class="cellL" />
<tr>
	<td>�����ġ ����ϱ�</td>
    <td>���Ӱ� ��ʸ� �߰�����Ϸ��� ���� <a href="javascript:popupLayer('../design/design_banner_loccd.php',780,600);"><img src="../img/btn_bangroup.gif" align="absmiddle" /></a> ���� ��������. <a href="javascript:manual('<?=$guideUrl?>board/view.php?id=design&no=6')"><img src="../img/btn_bn_manual.gif" align="absmiddle" /></a></td>
</tr>
<tr>
	<td>�ΰ�/��� ���ú���</td>
	<td>
		<SELECT NAME="sloccd" onchange="this.form.submit();">
		<option value="all">==== ��ü���� ====</option>
		<optgroup label="-- �ΰ��� --"></optgroup>
		<?
		# �ΰ��
		foreach ( $b_loccd as $lKey => $lVal ){
			if( $lKey < 90 ) continue;
		?>
		<option value="<?=$lKey?>" <?=$lKey==$_GET['sloccd']?" selected":""?>><?=$lVal?></option>
		<?}?>
		<optgroup label="-- ���ʺ��� --"></optgroup>
		<?
		# ���ʿ�
		foreach ( $b_loccd as $k => $v ){
			if( $k >= 90 ) continue;
		?>
		<option value="<?=$k?>" <?=$k==$_GET['sloccd']?" selected":""?>><?=$v?></option>
		<?}?>
		</SELECT>  �����ϸ� �ش� ��ġ�� �ΰ�/��ʸ� �� �� �ֽ��ϴ�.
	</td>
</tr>
</table>
<div class="button_top"><!--<input type=image src="../img/btn_search2.gif" />--></div>

<table width="100%">
<tr>
	<td class="pageInfo">
	�� <b><?=number_format($total)?></b>��, �˻� <b><?=number_format($pg->recode['total'])?></b>��, <b><?=number_format($pg->page['now'])?></b> of <?=number_format($pg->page['total'])?> Pages
	</td>
	<!--<td><font color=EA0095><b>**</b></font> <a href="http://www.godomall.co.kr/edu/edu_board_list.html?cate=design&in_view=y&sno=166#Go_view" target=_blank><font class=small1 color=EA0095><b><u>�ΰ�/��ʵ�Ͽ� ���� �ڼ��� �Ŵ��� ���� <font color=0074BA>[�ʵ�]</font></u></b></font></a> <font color=EA0095><b>**</b></font></td>-->
	<td align="right">
	<select name="sort" onchange="this.form.submit();">
	<option value="abs(loccd) desc" <?=$selected['sort']['abs(loccd) desc']?>>- ġȯ�ڵ� ���ġ�</option>
	<option value="abs(loccd) asc" <?=$selected['sort']['abs(loccd) asc']?>>- ġȯ�ڵ� ���ġ�</option>
	<option value="regdt desc" <?=$selected['sort']['regdt desc']?>>- ����� ���ġ�</option>
	<option value="regdt asc" <?=$selected['sort']['regdt asc']?>>- ����� ���ġ�</option>
	<option value="sort desc" <?=$selected['sort']['sort desc']?>>- ��¼��� ���ġ�</option>
	<option value="sort asc" <?=$selected['sort']['sort asc']?>>- ��¼��� ���ġ�</option>
	</select>&nbsp;
	<select name="page_num" onchange="this.form.submit();">
	<?
	$r_pagenum = array(10,20,40,60,100);
	foreach ($r_pagenum as $v){
	?>
	<option value="<?=$v?>" <?=$selected['page_num'][$v]?>><?=$v?>�� ���
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
	<th width="40">��ȣ</th>
	<th width="130">�ΰ�/�����ġ</th>
	<th>ġȯ�ڵ�</th>
	<th>�̹���</th>
	<th width="130">�����</th>
	<th width="100">����</th>
	<th width="50">����</th>
	<th width="50">����</th>
</tr>
<tr><td class="rnd" colspan="10"></td></tr>

<?
while ($data=$db->fetch($res)){

	$pri_code = $data['loccd'] . '|' . $data['sno'];

	if ( $_GET['sloccd'] <> '' && $selected['sort']['sort asc'] == 'selected' ){ // ����ȭ��ǥ ��ư ����

		list ($upRow) = $db->fetch("select count(*) from ".GD_BANNER." where loccd='" . $data['loccd'] . "' and sort < '" . $data['sort'] . "' order by sort desc limit 1");
		list ($downRow) = $db->fetch("select count(*) from ".GD_BANNER." where loccd='" . $data['loccd'] . "' and sort > '" . $data['sort'] . "' order by sort asc limit 1");
	}
	?>
<input type="hidden" name="code" value="<?echo($data['sno'])?>" />
<tr><td height="4" colspan="10"></td></tr>
<tr height="25" align="center">
	<td><font class="ver81" color="444444"><?=$pg->idx--?></td>
	<td style="padding:0 5px" align="left">
	<?=$b_loccd[ $data['loccd'] ]?><!--�ΰ�/��ʹ�ȣ : <b><?=$data['sno']?></b>-->
	</td>
	<td style="font:8pt tahoma">{@dataBanner(<?=$data['loccd']?>)}</td>
	<td><?=$webftp->confirmImage( "../../data/skin/" . $cfg['tplSkinWork'] . "/img/banner/" . $data['img'],200,50,"0");?></td>
	<td><font class="ver81" color="444444"><?=$data[regdt]?></td>
	<td align="center">
	<table border="0" cellspacing="0" cellpadding="0" style="padding:0 3px 0 3px;">
	<tr>

	<? if ( $upRow != 0 || $downRow != 0 ){ // ����ȭ��ǥ ��ư ���� ?>
		<td width="25%"><?if ( $upRow != 0 ){?><a href="javascript:act_modSort( 'sort_up', '<?=$pri_code?>' );"><img src="../img/ico_arrow_up.gif" alt="���� �̵�" border="0" align="absmiddle" hspace="1" /></a><?}?></td>
		<td width="25%"><?if ( $downRow != 0 ){?><a href="javascript:act_modSort( 'sort_down', '<?=$pri_code?>' );"><img src="../img/ico_arrow_down.gif" alt="���� �̵�" border="0" align="absmiddle" hspace="1" /></a><?}?></td>
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
<img src="../img/btn_allselect_s.gif" alt="��ü����"  border="0" align='absmiddle' style="cursor:hand" <?if ( $pg->recode['total'] != 0 ){?>onclick="javascript:PubAllSordes( 'select', fmList['confirmyn'] );"<?}else{?>onclick="javascript:alert( '����Ÿ�� �������� �ʽ��ϴ�.' );"<?}?> />
<img src="../img/btn_allreselect_s.gif" alt="���ù���"  border="0" align='absmiddle' style="cursor:hand" <?if ( $pg->recode['total'] != 0 ){?>onclick="javascript:PubAllSordes( 'reflect', fmList['confirmyn'] );"<?}else{?>onclick="javascript:alert( '����Ÿ�� �������� �ʽ��ϴ�.' );"<?}?> />
<img src="../img/btn_alldeselect_s.gif" alt="��������"  border="0" align='absmiddle' style="cursor:hand" <?if ( $pg->recode['total'] != 0 ){?>onclick="javascript:PubAllSordes( 'deselect', fmList['confirmyn'] );"<?}else{?>onclick="javascript:alert( '����Ÿ�� �������� �ʽ��ϴ�.' );"<?}?> />
<img src="../img/btn_alldelet_s.gif" alt="���û���" border="0" align='absmiddle' style="cursor:hand" <?if ( $pg->recode['total'] != 0 ){?>onclick="javaScript:act_delete();"<?}else{?>onclick="javascript:alert( '����Ÿ�� �������� �ʽ��ϴ�.' );"<?}?> />
</div>

<div style="float:right;">
<A HREF="javascript:act_allmodify();"><img src="../img/btn_allmodify_s.gif" alt="�ϰ�����" align="absmiddle" /></A>
<a href="design_banner_register.php"><img src="../img/btn_regist_s.gif" alt="���" align="absmiddle" /></a>
</div>

<div style="padding-top:35;"></div>

<div id="MSG01">
<table cellpadding="1" cellspacing="0" border="0" class="small_ex">
<tr><td><img src="../img/icon_list.gif" align="absmiddle" />ġȯ�ڵ� : ġȯ�ڵ带 �ش� �������� HTML �ҽ��� �Է��Ͽ� Ȱ���� �� �ֽ��ϴ�.</td></tr>
<tr><td style="padding-top:10px"><img src="../img/icon_list.gif" align="absmiddle" />�ΰ�/��ʼ����ٲٱ�</td></tr>
<tr><td><img src="../img/icon_list.gif" align="absmiddle" />���1) �������� ����: ��ȣĭ�� �������ڸ� �Է��ϰ� 'Enter key' ������ ������ ����˴ϴ�.</td></tr>
<tr><td><img src="../img/icon_list.gif" align="absmiddle" />���2) ��ü���� ���� : �� '��ȣĭ'�� �������ڸ� �Է��ϰ� [�ϰ�����]�� Ŭ���Ͻø� �˴ϴ�.</td></tr>
</table>
</div>
<script>cssRound('MSG01')</script>



<SCRIPT LANGUAGE="JavaScript"><!--
/*-------------------------------------
 �ϰ�����
-------------------------------------*/
/* ���� �� �Լ�
function act_allmodify(){

	var fs = document.fmList; // ����Ʈ��

	if( fs['code'] == null ) return; // ���ڵ尡 1�̸��� ���

	var fieldnm = new Array( 'code', 'sort' ); // �ʵ��
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

	fmList.action = "design_banner_indb.php?mode=allmodify";
	fmList.submit() ;
}
*/
// ���� �� �Լ�
function act_allmodify(){

	var fs = document.fmList; // ����Ʈ��

	if( fs['code'] == null ) return; // ���ڵ尡 1�̸��� ���

	var fieldnm = new Array('code', 'sort'); // �ʵ��

	var csField = new Array(); // �ʵ嵥��Ÿ����

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
 ��������
-------------------------------------*/
function act_modSort( mode, code, sort ){
	fmList.action = "design_banner_indb.php?mode=" + mode + "&code=" + code + "&sort=" + sort;
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