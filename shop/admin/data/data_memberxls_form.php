<?php

require_once dirname(__FILE__).'/../lib.php';

$checked = array();

$memberExcelDownloadConfig = $config->load('memberExcelDownload');
if ($memberExcelDownloadConfig['lockPassword']) {
	$checked['lockPassword'][$memberExcelDownloadConfig['lockPassword']] = 'checked';
}
else {
	$memberExcelDownloadConfig['lockPassword'] = 'y';
	$checked['lockPassword'][$memberExcelDownloadConfig['lockPassword']] = 'checked';
}

$addFields = array(
	'zonecode' => array(
		'text' => '�� �����ȣ',
		'down' => 'Y',
		'desc' => '5�ڸ��� �� �����ȣ(�������ʱ�����ȣ) �Է�. ����) XXXXX'
	),
);
?>
<style>
.title2 {
	font-weight:bold;
	padding-bottom:5px;
}
</style>

<div class="title title_top">ȸ��DB�ٿ�ε�<span>ȸ���˻��ٿ�ε�, �׸�üũ�ٿ�ε� �� �ΰ��� ������� ȸ��DB�� �ٿ�ε� ������ �� �ֽ��ϴ�</span> <a href="javascript:popup('<?=$guideUrl?>board/view.php?id=data&no=5')"><img src="../img/btn_q.gif" border=0 align=absmiddle></a></div>

<div style="padding-top:15;"></div>

<form name="fm" method="post" action="../data/data_memberxls_indb.php" onsubmit="return chkForm(this)" target="ifrmHidden">

<div class=title2>&nbsp;&nbsp;&nbsp;<img src="../img/icon_process.gif" align=absmiddle><span class="def1" style="color: #000000; font-weight: bold;">��й�ȣ ��뿩�� ����</span></div>

<table class="tb">
<col class="cellC"><col class="cellL">
<tr>
	<td width="80">�ٿ�ε� ����<br/>��й�ȣ</td>
	<td class="noline">
		<input type="radio" id="lock-password-y" name="lockPassword" value="y" style="vertical-align: middle;" <?php echo $checked['lockPassword']['y']; ?>/>
		<label for="lock-password-y" style="vertical-align: middle;">�����(����)</label>
		<input type="radio" id="lock-password-n" name="lockPassword" value="n" style="vertical-align: middle;" <?php echo $checked['lockPassword']['n']; ?>/>
		<label for="lock-password-n" style="vertical-align: middle;">������</label>
		<ul style="color: #627dce; list-style: none; padding: 0;">
			<li>��ȸ��DB �ٿ�ε�� �ٿ�ε�� ���Ͽ� ��й�ȣ�� ������ �� �ֽ��ϴ�.</li>
			<li>���������� ����� ������ ��ȣ��ġ ���ؿ� ������, ���������� PC�� ����� �ǹ������� �̸� ��ȣȭ�ؾ� �մϴ�.</li>
		</ul>
	</td>
</tr>
</table>
<div style="color: #627dce; border: solid #627dce 3px; margin-top: 10px; padding: 5px;">
	<div style="font-weight: bold;">�� ���������� ����� ������ ��ȣ��ġ ���� �ȳ�</div>
	<div>�� 6��(�������� ��ȣȭ)</div>
	<div>�� ������ż��� �����ڵ��� �̿����� ���������� ���ο���ǻ�� (PC)�� ������ ������ �̸� ��ȣȭ �ؾ��Ѵ�.(<a href="http://guide.godo.co.kr/guide/doc/(���������_��2012-50ȣ)_����������_�����.������_��ȣ��ġ_����_����.hwp">��������� ��2012-50ȣ ���� �ٿ�ε�</a>)</div>
</div>
<br/>
<br/>
<div class=title2>&nbsp;&nbsp;&nbsp;<img src="../img/icon_process.gif" align=absmiddle><font class=def1 color=000000><b>ȸ���˻����� �ٿ�ε� �ޱ�</b></font> <font class=extext>(�˻������ �ش��ϴ� ȸ����(�⺻�׸�) �ٿ�ε��մϴ�)</font></div>

<?
### ȸ�� �˻���
include "../member/_listForm.php";
?>

<div style="padding-top:7;"></div>
<div style="color: red; padding: 0px 0px 5px 3px;">*������Ÿ����� ���� �и� ����� �޸�ȸ���� ������ �ٿ�ε� �� �� �����ϴ�.</div>
<div class=noline>
<table border=0 cellpadding=0 cellspacing=0>
<tr>
<!--<td><img src="../img/icon_list.gif" align=absmiddle><font color=0074BA>�� �˻������ �ش��ϴ� ��ǰ�� �ٿ�ε� ���� �� �ֽ��ϴ�.</font><br>
	- �ٿ�ް����ϴ� ��ǰ�� �˻����ǿ� �Է��ϼ���.<br>
	- �ٿ�ε��ư�� ���� �� �����Ͻø� �˴ϴ�</td>
	<td widht=20></td>-->
<td width=127></td>
<td><input type="image" src="../img/btn_gooddown.gif" alt="ȸ��DB�ٿ�ε�"></td>
</tr></table>
</div>


<div style="padding-top:40;"></div>

<div class=title2>&nbsp;&nbsp;&nbsp;<img src="../img/icon_process.gif" align=absmiddle><font class=def1 color=000000><b>���ϴ� �׸�üũ �� �ٿ�ε� �ޱ�</b></font> <font class=extext>(���ϴ� �׸��� üũ�� �� �ٿ�ε��մϴ�)</font></div>


<table class=tb>
<col class=cellC><col class=cellL>
<tr>
	<td>���ϸ�</td>
	<td>
	<input type="text" name="filename" value="[<?=strftime( '%y��%m��%d��' )?>] ȸ��" size=40 required label="���ϸ�"> <span class=small_tip>Ȯ����(xls)�� ������ ���ϸ��� �Է��մϴ�.</span></td>
</tr>
<tr>
	<td>ȸ�����Ĺ��</td>
	<td>
	<select name="sort">
	<option value="regdt desc" selected>- ������ ���ġ�</option>
	<option value="regdt asc">- ������ ���ġ�</option>
	<option value="last_login desc">- �����α��� ���ġ�</option>
	<option value="last_login asc">- �����α��� ���ġ�</option>
	<option value="cnt_login desc">- �湮�� ���ġ�</option>
	<option value="cnt_login asc">- �湮�� ���ġ�</option>
    <optgroup label="------------"></optgroup>
	<option value="name desc">- �̸� ���ġ�</option>
	<option value="name asc">- �̸� ���ġ�</option>
	<option value="m_id desc">- ���̵� ���ġ�</option>
	<option value="m_id asc">- ���̵� ���ġ�</option>
    <optgroup label="------------"></optgroup>
	<option value="emoney desc">- ������ ���ġ�</option>
	<option value="emoney asc">- ������ ���ġ�</option>
	<option value="sum_sale desc">- ���űݾ� ���ġ�</option>
	<option value="sum_sale asc">- ���űݾ� ���ġ�</option>
	</select>
	</td>
</tr>
<tr>
	<td>�ٿ�ε����</td>
	<td>
	<div style="float:left;" class=noline><input type="radio" name="limitmethod" value="all" onclick="document.getElementById('part').style.display='none';"> ��ü�ٿ� &nbsp;&nbsp;&nbsp;
	<input type="radio" name="limitmethod" value="part" onclick="document.getElementById('part').style.display='block';" checked> �κдٿ�</div>
	<div style="float:left;margin-left:10;" id="part"><input type="text" name="limit[]" value="1" size="5" style="text-align:right;"> �� �� <input type="text" name="limit[]" value="100" size="5" style="text-align:right;"> ��
	<font class=extext>ȸ���� �ʹ� ���� ��쿡 ���</div>
	</td>
</tr>
<tr>
	<td valign="top" style="padding-top:4px;">�׸�(�ʵ�)üũ</td>
	<td style="padding:5px;">
	<div style="padding-top:5;"></div>
	&nbsp;&nbsp;<font class=extext>�Ʒ� üũ�� �׸���� �⺻�׸��Դϴ�</font>
	<div style="padding-top:7;"></div>
	<style>
	#field_table { border-collapse:collapse; float:left; margin-right:10px; }
	#field_table th { padding:4; }
	#field_table td { border-style:solid;border-width:1;border-color:#EBEBEB;color:#4c4c4c;padding:4; }
	#field_table i { color:green; font:8pt dotum; }
	</style>
<?
$fields = parse_ini_file("../../conf/data_memberddl.ini", true);
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

		if($key == 'resno1' || $key == 'resno2'){
			continue;
		}
		if ( $idx == 1 ){?>
	<table id="field_table">
	<tr bgcolor="#eeeeee">
		<th bgcolor=F4F4F4><font class=small1 color=444444><b>�ѱ��ʵ��</b></th>
		<th bgcolor=F4F4F4><font class=small1 color=444444><b>�����ʵ��</th>
	</tr>
		<?}?>
	<tr bgcolor="<?=( $idx % 2 == 0 ? '#ffffff' : '#ffffff' )?>">
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
<div style="color: red; padding-bottom: 5px;">*������Ÿ����� ���� �и� ����� �޸�ȸ���� ������ �ٿ�ε� �� �� �����ϴ�.</div>
<div class=noline style="padding-left:127px;text-align:left;"><input type="image" src="../img/btn_gooddown.gif" alt="ȸ��DB�ٿ�ε�"></div>
</form>


<div style="padding-top:15px"></div>

<div id=MSG01>
<table cellpadding=2 cellspacing=0 border=0 class=small_ex>
<tr>
	<td><img src="../img/icon_list.gif" align=absmiddle>�����ٿ� ������</font>
	<ol style="margin-top:0px;margin-bottom:0px;">
	<li>Ȯ����(xls)�� ������ ���ϸ��� �Է��մϴ�.</li>
	<li>�ٿ�ε�������� �κдٿ� �� ��� ��ǰ����� ��! �Է�</font>�մϴ�.</li>
	<li>�ٿ���� �׸�(�ʵ�)�� �����մϴ�.</li>
	<li>[�ٿ�ε�] ��ư Ŭ��</li>
	</ol>
	</td>
</tr>
</table>
</div>
<script>cssRound('MSG01')</script>