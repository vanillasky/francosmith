<?
	$addFields = array(
		'use_emoney' => array(
			'text' => '��������å',
			'down' => 'N',
			'desc' => '������ ������ ��å ����(0), ������ ���� ����(1) �� ���� �Է�. �⺻�� - ������ ������ ��å ����(0)'
		),
        'extra_info' => array(
            'text' => '��ǰ�ʼ�����',
            'down' => 'N',
            'desc' => '',
        ),
		'naver_event' => array(
			'text' => '�̺�Ʈ����',
			'down' => 'Y',
			'desc' => "'������>���̹�����>���̹� �����̺�Ʈ ���� ����>��ǰ�� ����' ���� �� �Է��� ��ǰ�� ���� �̺�Ʈ ���� �Է� (�ִ� 100�� �̳�)"
		),
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
?>

<style>
.title2 {
	font-weight:bold;
	padding-bottom:5px;
}
</style>

<div class="title title_top">��ǰDB�ٿ�ε�<span>��ǰ�˻��ٿ�ε�, �׸�üũ�ٿ�ε� �� �ΰ��� ������� ��ǰDB�� �ٿ�ε� ������ �� �ֽ��ϴ�</span> <a href="javascript:manual('<?=$guideUrl?>board/view.php?id=data&no=4')"><img src="../img/btn_q.gif" border=0 align=absmiddle></a></div>

<div style="padding-top:15;"></div>

<form name=fm method=post action="../data/data_goodsxls_indb.php" onsubmit="return chkForm(this)">
<div class=title2>&nbsp;&nbsp;&nbsp;<img src="../img/icon_process.gif" align=absmiddle><font class=def1 color=000000><b>��ǰ�˻����� �ٿ�ε� �ޱ�</b></font> <font class=extext(�˻������ �ش��ϴ� ��ǰ��(�⺻�׸�) �ٿ�ε��մϴ�)</font></div>

<table class=tb>
<col class=cellC><col class=cellL>
<tr>
	<td>�з�����</td>
	<td>
	<script src="../../lib/js/categoryBox.js"></script>
	<script>new categoryBox('cate[]',4,'');</script>
	</td>
</tr>
<tr>
	<td>Ű����</td>
	<td>
	<select name=skey>
	<option value="goodsnm">��ǰ��
	<option value="a.goodsno">������ȣ
	<option value="goodscd">��ǰ�ڵ�
	<option value="keyword">����˻���
	</select>
	<input type=text name=sword class=lline value="" class="line">
	</td>
</tr>
<tr>
	<td>��ǰ����</td>
	<td><font class=small color=444444>
	<input type=text name=price[] value="" class="rline"> �� -
	<input type=text name=price[] value="" class="rline"> ��
	</td>
</tr>
<tr>
	<td>��ǰ�����</td>
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
	<td>���üũ����</td>
	<td class=noline>
	<input type=radio name=open value="">��ü
	<input type=radio name=open value="1">���üũ�� ��ǰ
	<input type=radio name=open value="0">�����üũ�� ��ǰ
	</td>
</tr>
</table>

<div style="padding-top:7;"></div>

<div class=noline>
<table border=0 cellpadding=0 cellspacing=0>
<tr>
<!--<td><img src="../img/icon_list.gif" align=absmiddle><font color=0074BA>�� �˻������ �ش��ϴ� ��ǰ�� �ٿ�ε� ���� �� �ֽ��ϴ�.</font><br>
	- �ٿ�ް����ϴ� ��ǰ�� �˻����ǿ� �Է��ϼ���.<br>
	- �ٿ�ε��ư�� ���� �� �����Ͻø� �˴ϴ�</td>
	<td widht=20></td>-->
<td width=127></td>
<td>&nbsp;&nbsp;&nbsp;<input type="image" src="../img/btn_gooddown.gif" alt="��ǰDB�ٿ�ε�"></td>
</tr></table>
</div>


<div style="padding-top:40;"></div>

<div class=title2>&nbsp;&nbsp;&nbsp;<img src="../img/icon_process.gif" align=absmiddle><font class=def1 color=000000><b>���ϴ� �׸�üũ �� �ٿ�ε� �ޱ�</b></font> <font class=extext>(���ϴ� �׸��� üũ�� �� �ٿ�ε��մϴ�)</font></div>

<table class=tb>
<col class=cellC><col class=cellL>
<tr>
	<td height=30>���ϸ�</td>
	<td><input type="text" name="filename" value="[<?=strftime( '%y��%m��%d��' )?>] ��ǰ" size=40 required label="���ϸ�" class="line"> <span class=extext>Ȯ����(xls)�� ������ ���ϸ��� �Է��մϴ�</span></td>
</tr>
<tr>
	<td height=30>��ǰ���Ĺ��</td>
	<td>
	<select name="sort">
	<option value="regdt desc" selected>��ǰ����ϡ�</option>
	<option value="regdt asc">��ǰ����ϡ�</option>
	<option value="goodsnm desc">��ǰ���</option>
	<option value="goodsnm asc">��ǰ���</option>
	<option value="price desc">���ݡ�</option>
	<option value="price asc">���ݡ�</option>
	<option value="maker desc">�������</option>
	<option value="maker asc">�������</option>
	</select>
	<font class=extext>��ǰ���Ĺ���� �����ϼ���</font>
	</td>
</tr>
<tr>
	<td height=30>�ٿ�ε����</td>
	<td>
	<div style="float:left;" class=noline><input type="radio" name="limitmethod" value="all" onclick="document.getElementById('part').style.display='none';"> ��ü�ٿ� &nbsp;&nbsp;&nbsp;
	<input type="radio" name="limitmethod" value="part" onclick="document.getElementById('part').style.display='block';" checked> �κдٿ�</div>
	<div style="float:left;margin-left:5;" id="part"><input type="text" name="limit[]" value="1" size="5" style="text-align:right;"> �� �� <input type="text" name="limit[]" value="100" size="5" style="text-align:right;"> ��
	<font class=extext>��ǰ�� �ʹ� ���� ��쿡 ���
	</div>
	</td>
</tr>
<tr>
	<td valign="top" style="padding-top:10px;">�׸�(�ʵ�)üũ</td>
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
		<th bgcolor=F4F4F4><font class=small1 color=444444><b>�ѱ��ʵ��</b></th>
		<th bgcolor=F4F4F4><font class=small1 color=444444><b>�����ʵ��</th>
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
<div class=noline style="padding-left:137px;text-align:left;"><input type="image" src="../img/btn_gooddown.gif" alt="��ǰDB�ٿ�ε�"></div>
</form>


<div style="padding-top:15px"></div>

<div id=MSG01>
<table cellpadding=2 cellspacing=0 border=0 class=small_ex>
<tr>
	<td><img src="../img/icon_list.gif" align=absmiddle>�����ٿ� ������
	<ol style="margin-top:0px;margin-bottom:0px;">
	<li>Ȯ����(xls)�� ������ ���ϸ��� �Է��մϴ�.</li>
	<li>�ٿ�ε�������� �κдٿ� �� ��� ��ǰ������ ��! �Է��մϴ�.</li>
	<li>�ٿ���� �׸�(�ʵ�)�� �����մϴ�.</li>
	<li>[�ٿ�ε�] ��ư Ŭ��</li>
	</ol>
	</td>
</tr>
<tr>
	<td><img src="../img/icon_list.gif" align=absmiddle>���� ������ ���� �ٿ���� ���� ������ �� ������ ���� ��� ��#####������ ǥ�õǴ� ������ �߻��� �� �ֽ��ϴ�. �̷��� ��� ���� �������̡��� �ø��ų� �������� �����Ͽ� �ֽø� ������ ���������� ǥ�õ˴ϴ�.</td>
</tr>
</table>
</div>
<script>cssRound('MSG01')</script>