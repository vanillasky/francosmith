<?

$scriptLoad='<link rel="styleSheet" href="./_inc/style.css">';
include "../_header.popup.php";
require_once ('./_inc/config.inc.php');

// depth 1�� ������
$query = "SELECT * FROM ".GD_SHOPLE_CATEGORY." WHERE depth = 1 ORDER BY dispno";
$rs = $db->query($query);

$category = array();
while($row = $db->fetch($rs,1)) {
	$category[] = $row;
}

?>

<script language="javascript">
<!--
var category = '<?=$_REQUEST['category']?>';
var rowIdx = '<?=$_REQUEST['rowIdx']?>';
var idnm = '<?=$_REQUEST['idnm']?>';
-->
</script>

<div class="title title_top" style="margin-top:10px;">ǥ�� ī�װ� ����<span>���� ǥ�� ī�װ��� ã�� �����մϴ�.</span></div>

<form onsubmit="return nsShople.category.search();">
<table class="tb" style="margin-bottom:10px;">
<col class="cellC"><col class="cellL">
<tr>
	<td>ã�� ī�װ�</td>
	<td>
	<input type="text" name="srchName" id="srchName" value="" required label="�˻���" msgR="�˻�� �Է��ϼž� �մϴ�.">
	<input type="image" src="../img/btn_search_s.gif" align="absmiddle" hspace="10" class="null">
	</td>
</tr>
</table>
</form>

<table width="100%" cellPadding="2" cellSpacing="1" border="1" borderColor="#EBEBEB" cellpadding="0" cellspacing="3" style="border-collapse: collapse;">
<col width="80"><col width="">
<tr height="21" bgcolor="#F6F6F6">
	<td align="center">����</td>
	<td style="padding-left:210px">�˻����</td>
</tr>
</table>

<ul id="srchCatePrint">
	<li class="notice">������ ī�װ��� �����ϰ� �����ϼ���.</li>
</ul>

<div id="stepCate">
	<ul>
		<h4>��з�</h4>
		<select id="el-shople-category-1" class="el-shople-category" size=10>
		<option value="null">��з� ����</option>
		<? foreach ($category as $cate) { ?>
		<option value="<?=$cate['dispno']?>"><?=$cate['name']?></option>
		<? } ?>
		</select>
	</ul>
	<ul class="separator">��</ul>
	<ul>
		<h4>�ߺз�</h4>
		<select id="el-shople-category-2" class="el-shople-category" size=10>
		<option value="null">�ߺз� ����</option>
		</select>
	</ul>
	<ul class="separator">��</ul>
	<ul>
		<h4>�Һз�</h4>
		<select id="el-shople-category-3" class="el-shople-category" size=10>
		<option value="null">�Һз� ����</option>
		</select>
	</ul>
	<ul class="separator">��</ul>
	<ul>
		<h4>���з�</h4>
		<select id="el-shople-category-4" class="el-shople-category" size=10>
		<option value="null">���з� ����</option>
		</select>
	</ul>
</div>

<div style="clear:both; margin-top:10px;"><label for="samelow"><input type="checkbox" id="samelow"> <font color="#3A870C">�����з����� �����ϰ� �����մϴ�.</font></label></div>

<div style="margin:10px 0; text-align:center;"><a href="javascript:;" onclick="nsShople.category.apply();"><img src="../img/btn_openmarket_select_save.gif" alt="������ �з��� ��Ī����"></a></div>

<script type="text/javascript" src="./_inc/common.js"></script>
<script type="text/javascript">
function fnSetCategory() { nsShople.category.init('<?=$_GET['full_dispno']?>'); }
Event.observe(document, 'dom:loaded', fnSetCategory, false);
</script>

<script type="text/javascript">table_design_load();</script>
</body>
</html>