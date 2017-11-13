<?
require_once("../../lib/qfile.class.php");
$qfile = new qfile();

$ddlpath = "../../conf/data_goodsddl.ini";

$addFields = array(
	'use_mobile_img' => array(
		'text' => '����� ���� �̹��� ��뿩��',
		'down' => 'Y',
		'desc' => "����ϼ� ���� �̹��� ���(1), PC �̹��� ���(0) �� ���� �Է�, �⺻�� - ����ϼ� ���� �̹��� ���(1)<br><u style='color:#bf0000;'>����ϼ� ���� �̹��� ���(1)���� ���� �� �Ʒ� ����� ����~Ȯ�� �̹����� ����˴ϴ�.<br>PC �̹��� ���(0)���� ���� �ÿ��� �Ʒ� ����� ����~Ȯ�뿡 ���� PC �̹����� ����˴ϴ�.</u>"
		),
	'img_w' => array(
		'text' => '����� ���� �̹���',
		'down' => 'Y',
		'desc' => "����ϼ� ���ο� ����� �̹����� �Է�. <u style='color:#bf0000;'> �� �ش� �̹����� �ϳ��� �̹����� �����ؾ� ���� ����˴ϴ�.</u>"
		),
	'img_x' => array(
		'text' => '����� ����Ʈ �̹���',
		'down' => 'Y',
		'desc' => "����ϼ� ����Ʈ�� ����� �̹����� �Է�. <u style='color:#bf0000;'> �� �ش� �̹����� �ϳ��� �̹����� �����ؾ� ���� ����˴ϴ�.</u>"
		),
	'img_y' => array(
		'text' => '����� �� �̹���',
		'down' => 'Y',
		'desc' => "����ϼ� �󼼿� ����� �̹����� �Է�. �ټ� ��� '|' �� �����ڷ� �Է�. <i>ex) test1.gif|test2.gif</i>"
		),
	'img_z' => array(
		'text' => '����� Ȯ�� �̹���',
		'down' => 'Y',
		'desc' => "����ϼ� Ȯ�뿡 ����� �̹����� �Է�. �ټ� ��� '|' �� �����ڷ� �Է�. <i>ex) test1.gif|test2.gif</i>"
		),
	'img_pc_w' => array(
		'text' => '����� ���ο� ���� PC �̹���',
		'down' => 'Y',
		'desc' => 'PC �̹����� ���� Ÿ��Ʋ�� �Է�. <i>(�����̹���: img_i / ����Ʈ�̹���: img_s / ���̹���: img_m / Ȯ���̹���: img_l �� ��1)</i>'
		),
	'img_pc_x' => array(
		'text' => '����� ����Ʈ ���� PC �̹���',
		'down' => 'Y',
		'desc' => 'PC �̹����� ���� Ÿ��Ʋ�� �Է�. <i>(�����̹���: img_i / ����Ʈ�̹���: img_s / ���̹���: img_m / Ȯ���̹���: img_l �� ��1)</i>'
		),
	'img_pc_y' => array(
		'text' => '����� �� ���� PC �̹���',
		'down' => 'Y',
		'desc' => 'PC �̹����� ���� Ÿ��Ʋ�� �Է�. <i>(�����̹���: img_i / ����Ʈ�̹���: img_s / ���̹���: img_m / Ȯ���̹���: img_l �� ��1)</i>'
		),
	'img_pc_z' => array(
		'text' => '����� Ȯ�� ���� PC �̹���',
		'down' => 'Y',
		'desc' => 'PC �̹����� ���� Ÿ��Ʋ�� �Է�. <i>(�����̹���: img_i / ����Ʈ�̹���: img_s / ���̹���: img_m / Ȯ���̹���: img_l �� ��1)</i>'
		),
	'naver_import_flag' => array(
		'text' => '���� �� ���� ����',
		'down' => 'Y',
		'desc' => "���� : �ؿ� / ���� / �ֹ�����<br>��ǰ�� �ؿܱ��Ŵ����� ��� �ؿ�, ��������� ��� ����, �ֹ������� ��� �ֹ����� �Է�. �ش� ���� ���� ��� ǥ������ ����.<br><u style='color:#bf0000;'>�� ���̹����� 3.0�� �ݿ��Ǵ� ������, �ش� ��ǰ�ӿ��� �ؿܱ��Ŵ��� ���ΰ� �����ϰ� ǥ����� ���� ��� ���� ���� �� �����Ǹ�, Ŭ�����α׷��� ����Ǿ� ����� �϶��� �� �ֽ��ϴ�.</u>"
		),
	'naver_import_flag' => array(
		'text' => '���� �� ���� ����',
		'down' => 'Y',
		'desc' => "���� : �ؿ�(1), ����(2), �ֹ�����(3) �� �ش� ���� �����Ͽ� �Է�<br>��ǰ�� �ؿܱ��Ŵ����� ��� �ؿ�, ��������� ��� ����, �ֹ������� ��� �ֹ����� �Է�. �ش� ���� ���� ��� ǥ������ ����.<br><u style='color:#bf0000;'>�� ���̹����� 3.0�� �ݿ��Ǵ� ������, �ش� ��ǰ�ӿ��� �ؿܱ��Ŵ��� ���ΰ� �����ϰ� ǥ����� ���� ��� ���� ���� �� �����Ǹ�, Ŭ�����α׷��� ����Ǿ� ����� �϶��� �� �ֽ��ϴ�.</u>"
		),
	'naver_product_flag' => array(
		'text' => '�ǸŹ�� ����',
		'down' => 'Y',
		'desc' => "���� : ����(1), ��Ż(2), �뿩(3), �Һ�(4), �����Ǹ�(5), ���Ŵ���(6) �� �ش� ���� �����Ͽ� �Է�<br>�Ϲ����� �ǸŹ�İ��� �ٸ� ������� �ǸŵǴ� ��ǰ�鿡 ǥ��<br><u style='color:#bf0000;'>�� ���̹����� 3.0�� �ݿ��Ǵ� ������, �ش� ��ǰ�ӿ��� �ǸŹ���� �����ϰ� ǥ����� ���� ��� ���̹����ο��� ��ǰ�� �����Ǹ�, Ŭ�����α׷��� ����Ǿ� ����� �϶��� �� �ֽ��ϴ�.</u>"
		),
	'naver_age_group' => array(
		'text' => '�� �̿� ����',
		'down' => 'Y',
		'desc' => '���� : ����(0), û�ҳ�(1), �Ƶ�(2), ����(3) �� �����Ͽ� �Է�. �⺻�� - ����(0)<br>��ǰ�� �ֿ� ������� �ؽ�Ʈ�� ����. �Է����� �ʴ� ��� �����Ρ����� ó��'
		),
	'naver_gender' => array(
		'text' => '����',
		'down' => 'Y',
		'desc' => '���� : ����(1), ����(2), �������(3) �� �ش� ���� �����Ͽ� �Է�<br>��ǰ�� �ֿ� ���� ���� ������ �Է�'
		),
	'naver_attribute' => array(
		'text' => '��ǰ�Ӽ�',
		'down' => 'Y',
		'desc' => '��ǰ�� �Ӽ� ������ ��^���� �����Ͽ� �Է�, �ִ� 500��<br><i>ex) ����^1��^���Ǻ�^2��^����^��������^��������^��������</i>'
		),
	'naver_search_tag' => array(
		'text' => '�˻��±�',
		'down' => 'Y',
		'desc' => '��ǰ�� �˻��±׿� ���Ͽ� ��|��(Vertical bar)�� �����Ͽ� �Է�. �ִ� 100��<br><i>ex) ��������Ͽ��ǽ�|2016S/S�Ż���ǽ�|��ȥ�ľ�����|��ģ��</i>'
		),
	'naver_category' => array(
		'text' => '���̹� ī�װ�',
		'down' => 'Y',
		'desc' => '���̹� ī�װ��� ID�� �Է�. �ִ� 8��<br>�Է��ϴ� ���, ���̹� ���ο��� �ش� ī�װ��� ��Ī�ϴµ� �ݿ� '
		),
	'naver_product_id' => array(
		'text' => '���ݺ� ������ ID',
		'down' => 'Y',
		'desc' => "���̹� ���ݺ� ������ ID�� �Է��� ��� ���̹� ���ݺ� ��õ�� �ݿ�. �ִ� 50��<br><i>ex) http://shopping.naver.com/detail/detail.nhn?nv_mid=<u style='color:#bf0000;'>8535546055</u>&cat_id=50000151</i>"
		),
);

	$fields = parse_ini_file($ddlpath, true);

	foreach($addFields as $k => $v) {
		if(!$fields[$k]) $fields[$k] = $v;
	}

	$qfile->open( $ddlpath);

	foreach ( $fields as $key => $arr ){

		$qfile->write("[" . $key . "]" . "\n" );
		$qfile->write("text = \"" . $arr['text'] . "\"" . "\n" );
		$qfile->write("down = \"" . $arr['down'] . "\"" . "\n" );
		$qfile->write("desc = \"" . $arr['desc'] . "\"" . "\n\n" );
	}

	$qfile->close();
	@chMod( $ddlpath, 0707 );
?>
<script type="text/javascript" src="../godo.loading.indicator.js"></script>
<script type="text/javascript">
function _chkForm(f) {

	if (!chkForm(f)) return false;

	f.target = "ifrmHidden";

	nsGodoLoadingIndicator.init({
		psObject : $$('iframe[name="ifrmHidden"]')[0]
	});

	nsGodoLoadingIndicator.show();

	return true;

}
</script>

<!-- ��) ���� ��� & ���� -->
<div class="title title_top">��ǰ DB���<span>�뷮�� ��ǰ DB�� ������ ���(Up Date) �Ͻ� �� �ֽ��ϴ�. <a href="javascript:manual('<?=$guideUrl?>board/view.php?id=data&no=2')"><img src="../img/btn_q.gif" border=0 align=absmiddle></a></div>
<div style="padding-top:5px"></div>

<form name=fm method=post action="../data/data_goodscsv_indb.php" target="_blank" enctype="multipart/form-data" onsubmit="return _chkForm(this)">
<table class=tb>
<col class=cellC><col class=cellL>
<tr>
	<td width="150">�������� �ٿ�ε�</td>
	<td>
		<a href="../data/csv_goods.xls"><img src="../img/btn_goodcsv_sample.gif" alt="��ǰCSV �������� �ٿ�ε�"></a>
		<p class="extext_t" style="margin:0;">
		���������� �ٿ�޾� �������� ��ǰ������ �ۼ��մϴ�. <br>
		�ۼ��� ��ǰ�з�(goodscate) ���� '������ > ǥ������'�� �ؽ�Ʈ �������� ������ �ּ���. �������� �ʴ� ��� ��ǰ�з� ���� ��002�� -> ��2���� ����Ǿ� ��ϵ� �� �ֽ��ϴ�.<br>
		���� ���� ������ �Է��Ͽ� CSV�� ������ ��� ���� ������ ���� ��#####������ ����Ǿ� ��ǰ������ ���������� ��ϵ��� ���� �� ������,<br>
		�ݵ�� �������� ���Ϲݡ� ������ �����Ͽ� ��#####���� ���� �ʵ��� �� �� ���� �� ���ε��Ͽ� �ֽñ� �ٶ��ϴ�.
		</p>
	</td>
</tr>
<tr>
	<td>��ǰ CSV ���� �ø���</td>
	<td>
		<input type="file" name="file_excel" size="45" required label="CSV ����"> &nbsp;&nbsp; <span class="noline"><input type=image src="../img/btn_regist_s.gif" align="absmiddle"></span>

		<p class="extext_t" style="margin:0;">
		�ۼ� �Ϸ�� ��ǰCSV ������ �ø�����. <br>
		����� �Ϸ�Ǹ� [��ǰ����Ʈ] ���� ��ϵ� ��ǰ�� Ȯ���Ͻ� �� �ֽ��ϴ�.
		</p>
	</td>
</tr>
</table>
</form>

<style>
div.admin-necessarily-remark {border:4px solid #dce1e1;margin:10px 0 10px 0;padding:10px;}
div.admin-necessarily-remark h3 {margin:0;padding:0;font-size:12px;font-weight:bold;color:#0074BA;}
div.admin-necessarily-remark ol {margin:7px 0 0px 0px;}
div.admin-necessarily-remark ol li {list-style-type:none;color:#666666;margin:0 0 3px 0;}
</style>

<div class="admin-necessarily-remark">
	<h3>��! �˾Ƶα�</h3>

	<ol>
		<li>1) ���� ����� ��ǰ�� ��ǰ��ȣ�� ����νʽÿ�. �̹� ��ϵ� ��ǰ��ȣ�� ���ø� �ش� ��ǰ�� ������ �����˴ϴ�.</li>
		<li>2) ��ϵ� ��ǰ ����/UpDate �� ��ǰ��ȣ, ��ǰ�� ���� �ݵ�� �־�� �ش� ��ǰ���� ���ε尡 �˴ϴ�.</li>
		<li>3) �ʼ��׸�(��ǰ��ȣ, ��ǰ��)�� �����ϰ�� ��� ������ ���� ��� �������� �νðų�. �ش��׸��� ���� �ϰ� ����Ͻø� �˴ϴ�. <a href="javascript:popup('http://guide.godo.co.kr/guide/php/ex_data_goodscsv_1.html',870,523)"><img src="../img/icon_sample.gif" border="0" align=absmiddle hspace=2></a></li>
		<li>4) ���þ�Ŀ��� �����Ǵ� ���� Ÿ��Ʋ���� �ݵ�� �����Ͽ��� �ϸ�, ����/���� ��  �������� ��Ͻ� ���� ó���Ǿ� ��ϵ��� �ʽ��ϴ�.</li>
		<li>5) ��ϵ� ������ ���� �� ������� ���� �����Ͽ� ��� �� UpDate �Ͻ� ��� �ش� �Է¶��� null �� �Է��� �ּž� �մϴ�. �������� ��Ͻ� ������� �ʽ��ϴ�. <a href="javascript:popup('http://guide.godo.co.kr/guide/php/ex_data_goodscsv_1.html',870,523)"><img src="../img/icon_sample.gif" border="0" align=absmiddle hspace=2></a></li>
		<li>6) ������ �����Ͻ� ������(CSV)�� ������ �ּ���.</li>
	</ol>
</div>



<!-- ��) ���� ��� & ���� -->
<div class="title title_top">�ű� ��ǰDB �ϰ���� <span>���� ����� ��ǰ DB�� ������ ���� ��� �Ͻ� �� �ֽ��ϴ�.</div>
<div style="padding-top:5px"></div>

<form name=fm2 method=post action="../data/data_goodscsv_indb_new.php" target="_blank" enctype="multipart/form-data" onsubmit="return _chkForm(this)">
<table class=tb>
<col class=cellC><col class=cellL>
<tr>
	<td width="150">�������� �ٿ�ε�</td>
	<td>
		<a href="../data/csv_goods.new.xls"><img src="../img/btn_goodcsv_sample_new.gif" alt="NEW ��ǰCSV �������� �ٿ�ε�"></a>
		<p class="extext_t" style="margin:0;">
		���������� �ٿ�޾� �������� ��ǰ������ �ۼ��մϴ�. <br>
		�ۼ��� ��ǰ�з�(goodscate) ���� '������ > ǥ������'�� �ؽ�Ʈ �������� ������ �ּ���. �������� �ʴ� ��� ��ǰ�з� ���� ��002�� -> ��2���� ����Ǿ� ��ϵ� �� �ֽ��ϴ�.<br>
		���� ���� ������ �Է��Ͽ� CSV�� ������ ��� ���� ������ ���� ��#####������ ����Ǿ� ��ǰ������ ���������� ��ϵ��� ���� �� ������,<br>
		�ݵ�� �������� ���Ϲݡ� ������ �����Ͽ� ��#####���� ���� �ʵ��� �� �� ���� �� ���ε��Ͽ� �ֽñ� �ٶ��ϴ�.
		</p>
	</td>
</tr>
<tr>
	<td>��ǰ CSV ���� �ø���</td>
	<td>
		<input type="file" name="file_excel" size="45" required label="CSV ����"> &nbsp;&nbsp; <span class="noline"><input type=image src="../img/btn_regist_s.gif" align="absmiddle"></span>

		<p class="extext_t" style="margin:0;">
		�ۼ� �Ϸ�� ��ǰCSV ������ �ø�����. <br>
		����� �Ϸ�Ǹ� [��ǰ����Ʈ] ���� ��ϵ� ��ǰ�� Ȯ���Ͻ� �� �ֽ��ϴ�.
		</p>
	</td>
</tr>
</table>
</form>

<div class="admin-necessarily-remark">
	<h3>��! �˾Ƶα�</h3>





	<ol>

		<li>1) �ű� ��ǰDB �ϰ������ ��ǰ��� �������̸�, �ɼ������� ���� �����ϰ� ��� �� �� �ֽ��ϴ�.<br>
		       &nbsp;&nbsp;&nbsp;&nbsp;���� ��ǰ���� ������ �� ������ ����� [��ǰDB���]�� �̿��Ͽ� �ּ���.<a href="javascript:popup('http://guide.godo.co.kr/guide/php/ex_data_goodscsv_2.html',870,523)"><img src="../img/icon_sample.gif" border="0" align=absmiddle hspace=2></a>
		</li>
		<li>2) �ʼ��׸�(��ǰ��)�� �����ϰ�� ��� ������ ���� ��� �������� �νðų�, �ش��׸��� �����ϰ� ����Ͻø� �˴ϴ�. ��ǰ���� ������ �ش���� ����� ���� �ʽ��ϴ�.</li>
		<li>3) ���þ�Ŀ��� �����Ǵ� ���� Ÿ��Ʋ���� �ݵ�� �����Ͽ��� �ϸ�, ����/���� ��  �������� ��Ͻ� ���� ó���Ǿ� ��ϵ��� �ʽ��ϴ�.</li>
		<li>4) ��ǰ�з���ȣ�� �Է����� �ʰ� ����ϴ� ��쿡�� [ ��ǰ���� > ���� �̵�/����/���� ]���� �����Ͻ� �� �ֽ��ϴ�.</li>
		<li>5) �ɼ��� ��������(����, �� ���)�� <a href="../goods/stock.php" target="_blank"><font class="extext_l">[ ��ǰ���� > ����/������/������ ]</font></a> ���� �ϰ� ���, ���� �Ͻ� �� �ֽ��ϴ�.</li>
		<li>6) ������ �����Ͻ� ������(CSV)�� ������ �ּ���.</li>



	</ol>
</div>

<div style="padding-top:30px"></div>

<div id=MSG01>
<table cellpadding=2 cellspacing=0 border=0 class=small_ex>
<tr>
	<td>
	</td>
</tr>

<tr>
	<td><div style="padding: 5px 0px 2px 5px"><img src="../img/icon_list.gif" align=absmiddle>��ǰ�ʵ弳��</div><br>
	<div style="width:100%;margin-left:10px;">
	<style>
	#field_table { border-collapse:collapse; }
	#field_table th { padding:4; }
	#field_table td { border-style:solid;border-width:1;border-color:#EBEBEB;color:#4c4c4c;padding:4; }
	#field_table i { color:green; font:8pt dotum; }
	</style>
	<table id="field_table">
	<tr bgcolor="#eeeeee">
		<th><font class=small1 color=444444><b>�ѱ� Ÿ��Ʋ</th>
		<th><font class=small1 color=444444><b>���� Ÿ��Ʋ</th>
		<th><font class=small1 color=444444><b>����</th>
	</tr>
<? foreach( parse_ini_file("../../conf/data_goodsddl.ini", true) as $key => $arr ){
    if ($key == 'extra_info') $arr['desc'] = '<a href="javascript:popup(\'http://guide.godo.co.kr/guide/php/ex_data_goodscsv_3.html\',770,523)"><img src="../img/icon_sample.gif" border="0" align=right style="margin:10px;"></a>����) {�׸��ȣ:�׸��|�׸񳻿�}<br>�� �׸� \'{ }\'�� ���� �׸� ������ \',\'�� �Է�<br>{ } �ȿ� ���γ����� \':\'�� \'|\'�� �����Ͽ� �Է� <i>ex) {1:��ǰ����|�Ұ���}</i><br><u style=\'color:#bf0000;\'>������ũ ������ǰ�� ��ǰ�ʼ����� �ϰ������ ���� �ʽ��ϴ�.</u>';
?>
	<tr bgcolor="<?=( ++$idx % 2 == 0 ? '#ffffff' : '#ffffff' )?>">
		<td><font class=small1 color=444444><?=$arr['text']?></td>
		<td><font class=ver8 color=444444><?=$key?></td>
		<td><font class=small color=444444><?=nl2br( $arr['desc'] )?></td>
	</tr>
<? } ?>
	</table>
	</div>
	</td>
</tr>
</table>
</div>
<script>cssRound('MSG01')</script>