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