<?
$scriptLoad='<link rel="styleSheet" href="./_inc/style.css">';
include "../_header.popup.php";
require_once ('./_inc/config.inc.php');

$mode = $_GET['m'];

$shople = Core::loader('shople');
?>
<script type="text/javascript" src="./_inc/common.js"></script>
<!-- * -->
<script type="text/javascript">
function _fnDownload() {
	opener.nsShople.order.download();
}

</script>

<form name="frmClaim" id="frmClaim" method="post" enctype="multipart/form-data" action="./ax.indb.order.php">
<input type="hidden" name="mode" value="excel">

<div class="title title_top" style="margin-top:10px;">�����ȣ �ϰ����<span>&nbsp;</span></div>
<p class="gd_notice">
<span>��������Ʈ�� �ٿ�޾� �ù���ڵ�� �����ȣ�� �Է��Ͻ� �� �ٽ� ���ε��Ͻø� �ϰ����ó���� �˴ϴ�.</span>
<span>�κй߼�ó�� �ֹ����� �����߼�ó�� �ٶ��ϴ�. (�ٿ���� �������Ͽ����� �κй߼� �ֹ����� ������ ����)</span>
<span>���� ���ε� �� �ݵ�� �����ư�� ���� �ϰ������ �ϷḦ �� �ֽʽÿ�.</span>
<span>��¥ �Է�, �����ȣ�� ������(-) ���� �Է��� �ֽʽÿ�. (��: 20071215)</span>
<span>�ѹ��� �ִ� 1000������ ����� �����մϴ�.</span>
<span>�ɼǰ�, �ɼǰ����� �޸�(,)�� �����Ͽ� �Է��� �ֽʽÿ�. (�ɼǰ����� ���ڷθ� �Է��� �ֽʽÿ�.)</span>
<span>�ɼǺ� ����Ʈ������ ������ �Ͻ� ��� ������۴���(��۹�ȣ ����)�� ó���˴ϴ�.</span>
<span>��) ��۹�ȣ 12345�� �ΰ��� ��ǰ�� ���ó���� ��� 1���� �ֹ�ó���˴ϴ�.</span>
<span class="red">����!! ���� ���� ��� ó������� 1�� ����ó��,1�� ��ó���׸��̶�� �޽����� ������ ����ó���Ȱ��Դϴ�. ���� �Ͻñ� �ٶ��ϴ�</span>
</p>


<div class="title title_top" style="margin-top:10px;">�������� �ٿ�ε�<span>&nbsp;</span></div>
	<p class="gd_notice">
	<a href="javascript:_fnDownload();"><img src="../img/btn_excel_download.gif" alt="�����ٿ�ε�"></a> �������� �ٿ�ε� �� �ù��, �����ȣ�� �Է��Ͻʽÿ�.
	</p>


<div class="title title_top" style="margin-top:10px;">�ù�� �ڵ�<span>(�Ǹ��ڴ��� ����Ͻô� �ù�� �ڵ��Դϴ�. �ù�� �̸���� �ڵ带 �Է����ֽʽÿ�.)</span></div>
	<table class="tb" width="100%">
	<col class="cellC"><col class="cellL">
	<col class="cellC"><col class="cellL">
	<col class="cellC"><col class="cellL">
	<tr>
		<th>�����ͽ�������</th>
		<td>00001</td>
		<th>�����ù�</th>
		<td>00002</td>
		<th>���ο�ĸ</th>
		<td>00006</td>
	</tr>
	<tr>
		<th>��ü���ù�</th>
		<td>00007</td>
		<th>������</th>
		<td>00008</td>
		<th>�����ù�</th>
		<td>00011</td>
	</tr>
	<tr>
		<th>�����ù�</th>
		<td>00012</td>
		<th>CJ-GLS</th>
		<td>00013</td>
		<th>KGB�ù�</th>
		<td>00014</td>
	</tr>
	<tr>
		<th>�������</th>
		<td>00017</td>
		<th>�̳������ù�</th>
		<td>00019</td>
		<th>����ù�</th>
		<td>00021</td>
	</tr>
	<tr>
		<th>�Ͼ������</th>
		<td>00022</td>
		<th>ACI</th>
		<td>00023</td>
		<th>WIZWA</th>
		<td>00025</td>
	</tr>
	<tr>
		<th>�浿�ù�</th>
		<td>00026</td>
		<th>õ���ù�</th>
		<td>00027</td>
		<th>KGL</th>
		<td>00028</td>
	</tr>
	<tr>
		<th>��Ÿ</th>
		<td>00099</td>
	</table>

<div class="title title_top" style="margin-top:10px;">�������� ���ε�<span>&nbsp;</span></div>
	<p class="gd_notice">
	<input type="file" name="excel" value=""> * ������ ���� ����(CSV)�� ���ε� �մϴ�.

	</p>

<div class="button">
	<input type="image" src="../img/btn_save.gif">
	<img src="../img/btn_cancel.gif" class="hand" onClick="self.close();">
</div>

	</form>

<!-- eof * -->
<script type="text/javascript">
linecss();
table_design_load();
</script>
</body>
</html>
