<?

$location = "��ǰ���� > �˻������� ��ǰ����";
include "../_header.php";
@include "../../conf/design.search.php";
@include "../../conf/design_search.".$cfg['tplSkinWork'].".php";

//0
$checked['keyword_chk'][$cfg_search[0]['keyword_chk']] = 'checked="checked"';
$checked['disp_sort'][$cfg_search[0]['disp_sort']] = 'checked="checked"';
$pr_text = explode(',', $cfg_search[0]['pr_text']);
$link_url = explode(',', $cfg_search[0]['link_url']);

//1
$keyword = explode(',', $cfg_search[1]['keyword']);

//2
$detail_type = explode(',', $cfg_search[2]['detail_type']);
foreach($detail_type as $val){
	$checked['detail_type'][$val] = 'checked="checked"';
}
$detail_add_type = explode(',', $cfg_search[2]['detail_add_type']);

foreach($detail_add_type as $val){
	$checked['detail_add_type'][$val] = 'checked="checked"';
}

//3
$disp_type = explode(',', $cfg_search[3]['disp_type']);
foreach($disp_type as $val){
	$checked['disp_type'][$val] = 'checked="checked"';
}
?>

<style>
.display-type-wrap {width:94px;float:left;margin:3px;}
.display-type-wrap img {border:none;width:94px;height:72px;}
.display-type-wrap div {text-align:center;}
</style>
<script type="text/javascript">
function display_add(obj){
	if( obj.checked ) document.getElementById("benefit_box").style.display = 'block';
	else document.getElementById("benefit_box").style.display = 'none';
}

function inputbox_remove(obj){
	var chk_len = 0;
	if( obj.indexOf("best_keywords") == 0){
		chk_len = document.getElementById("best_keywords").childNodes.length;
	}else{
		chk_len = document.getElementById("keywords").childNodes.length;
	}

	if(chk_len == 1) return;

	var srcNode = document.getElementById(obj);
	srcNode.removeNode(true);
}
function addKeywordRow()
{
	var keywordSrcRow = document.getElementById("keyword-row-0");
	var table = keywordSrcRow.parentNode;
	var keywordClnRow = keywordSrcRow.cloneNode(true);
	var inputCollection = keywordClnRow.getElementsByTagName("input");
	var anchor = keywordClnRow.getElementsByTagName("a")[0];
	var img = anchor.getElementsByTagName("img")[0];
	table.rows[table.rows.length-1].id.match(/keyword-row-(\d)+$/);
	var index = parseInt(RegExp.$1) + 1;
	keywordClnRow.id = "keyword-row-"+index;
	anchor.href = "javascript:removeKeywordRow("+index+")";
	img.src = "../img/i_del.gif";
	for (var index = 0; index < inputCollection.length; index++) {
		inputCollection[index].value = "";
	}
	table.appendChild(keywordClnRow);
}
function removeKeywordRow(index)
{
	var keywordRow = document.getElementById("keyword-row-"+index);
	keywordRow.parentNode.removeChild(keywordRow);
}
function addBestKeywordRow()
{
	var bestKeywordSrcRow = document.getElementById("best-keyword-row-0");
	var container = bestKeywordSrcRow.parentNode;
	var bestKeywordClnRow = bestKeywordSrcRow.cloneNode(true);
	var inputCollection = bestKeywordClnRow.getElementsByTagName("input");
	var anchor = bestKeywordClnRow.getElementsByTagName("a")[0];
	var img = anchor.getElementsByTagName("img")[0];
	container.children[container.children.length-1].id.match(/best-keyword-row-(\d)+$/);
	var index = parseInt(RegExp.$1) + 1;
	bestKeywordClnRow.id = "best-keyword-row-"+index;
	anchor.href = "javascript:removeBestKeywordRow("+index+")";
	img.src = "../img/i_del.gif";
	for (var index = 0; index < inputCollection.length; index++) {
		inputCollection[index].value = "";
	}
	container.appendChild(bestKeywordClnRow);
}
function removeBestKeywordRow(index)
{
	var keywordRow = document.getElementById("best-keyword-row-"+index);
	keywordRow.parentNode.removeChild(keywordRow);
}
</script>
<form name="frm" method="post" action="indb.php">
<input type="hidden" name="mode" value="disp_search" />
<input type="hidden" name="tplSkinWork" value="<?=$cfg['tplSkinWork']?>">
<div class="title" style="margin-top:0">�˻� ������ ��ǰ ���� <a href="javascript:manual('<?=$guideUrl?>board/view.php?id=product&no=36')"><img src="../img/btn_q.gif" border="0" align="absmiddle"></a></div>
<?=$strMainGoodsTitle?>

<div class="title" style="margin-top:0">
	��ǰ �˻� Ű���� ���� <span>������ ��ǰ�˻� �Է� â�� ȫ�������� ����Ͽ� ���������� Ȱ���� �� �ֽ��ϴ�.</font>
</div>
<table class="tb">
<col class="cellC"><col class="cellL">
<tr>
	<th>��뿩��</th>
	<td>
		<label><input type="radio" name="keyword_chk" value="on" style="border=0;" <?=$checked['keyword_chk']['on']?>/>�����</label>
		<label><input type="radio" name="keyword_chk" value="off" style="border=0;" <?=$checked['keyword_chk']['off']?>/>������</label>
	</td>
</tr>
<tr>
	<th>Ű���� ���� ���</th>
	<td>
		<label><input type="radio" name="disp_sort" value="rand" style="border=0;" checked/>��������</label>
	</td>
</tr>
<tr>
	<th>Ű���� ���</th>
	<td>
		<div style="padding:4px,0,4px,0;"><font class="extext">������ ����ϰ� �˻�â�� ����� ������ �˻����� �� ����Ǵ� ���� ������ ��ũ������ �Է��� �ּ���.<br/>�߰� ��ư�� Ŭ���ϸ� ȫ�������� ���� �� ����Ͻ� �� ��
		���ϴ�.<font></div>
		<div id="keywords">
			<table class="tb">
				<colgroup>
					<col width="210"/>
				</colgroup>
				<tr>
					<th class="cellC">ȫ������</th>
					<th class="cellC">��ũ������</th>
				</tr>
<?php for ($index = 0; $index < (is_array($pr_text) ? count($pr_text) : 1); $index++) { ?>
				<tr id="keyword-row-<?php echo $index; ?>">
					<td><input type="text" class="lline" name="pr_text[]" style="width: 200px;" value="<?php echo $pr_text[$index]; ?>"/></td>
					<td>
						<input type="text" class="lline" name="link_url[]" style="width: 400px;" value="<?php echo $link_url[$index]; ?>"/>
						<?php if ($index === 0) { ?>
						<a href="javascript:addKeywordRow();"><img src="../img/btn_add2.gif"/></a>
						<?php } else { ?>
						<a href="javascript:removeKeywordRow(<?php echo $index; ?>);"><img src="../img/i_del.gif"/></a>
						<?php } ?>
					</td>
				</tr>
<?php } ?>
			</table>
		</div>
	</td>
</tr>
</table>

<div class="title">
	�α� �˻��� ���� <span>�α� �˻�� ����Ͽ� ȫ���������� Ȱ���մϴ�.</span>
</div>
<table class="tb">
<col class="cellC"><col class="cellL">
<tr>
	<th>�α� �˻��� ���</th>
	<td>
		<div style="padding:4px,0,4px,0;"><font class="extext">�������� �ַ��ϴ� ��ǰ�� ȫ���ϴ� �������� Ȱ���� �� �ֽ��ϴ�.<br/>
�˻��� ������ �˻���� ��ǰ��Ͻÿ� ����� ��ǰ��, ������, �귣��, ����˻�� ����� �ܾ �˻��� �����մϴ�.
</font></div>
		<div id="best_keywords" style="padding-top:4px;display:inline-block;" >
<?php for ($index = 0; $index < (is_array($keyword) ? count($keyword) : 1); $index++) { ?>
			<div style="padding-bottom:4px;" id="best-keyword-row-<?php echo $index; ?>" >
				<input type="text" name="keyword[]" class="lline" style="width:150px;" value="<?php echo $keyword[$index]; ?>"/>
				<?php if ($index === 0) { ?>
				<a href="javascript:addBestKeywordRow()"><img src="../img/btn_add2.gif"></a>
				<?php } else { ?>
				<a href="javascript:removeBestKeywordRow(<?php echo $index; ?>)"><img src="../img/i_del.gif"></a>
				<?php } ?>
			</div>
<?php } ?>
		</div>
	</td>
</tr>
</table>

<div class="title">
	�󼼰˻� ���� <span>�˻� ��� ���������� ���� ���ϴ� ������ �� �� ��Ȯ�ϰ� �˻��� �� �ֵ��� �󼼰˻� ��� ������ �����մϴ�.</span>
</div>
<table class="tb">
<col class="cellC"><col class="cellL">
<tr>
	<th>�󼼰˻� ���� ����</th>
	<td>
		<div style="padding:4px,0,4px,0;"><font class="extext">���ǰ��� �ϳ��� �������� ���� ��� ������������ ������� �ʽ��ϴ�.</font></div>
		<label><input type="checkbox" name="detail_type[]" value="category" style="border:0;" <?= $checked['detail_type']['category']?>/>��ǰ�з��˻�</label><br/>
		<label><input type="checkbox" name="detail_type[]" value="price" style="border:0;" <?= $checked['detail_type']['price']?>/>���ݰ˻�</label><br/>
		<label><input type="checkbox" name="detail_type[]" value="add" style="border:0;" <?= $checked['detail_type']['add']?> onclick="display_add(this)"/>���ü��� �˻�</label>
			<font class="extext">( ���ü��� �˻��� �����ϰ��� �ϴ� ��� ������ ���� �˻� ������ �������ּ��� )</font>
		<div id="benefit_box" style="padding:5px,0,0,20px;display:none;">
			<label><input type="checkbox" name="detail_add_type[]" value="free_deliveryfee" style="border:0;" <?= $checked['detail_add_type']['free_deliveryfee']?>/>������</label>
			<label><input type="checkbox" name="detail_add_type[]" value="dc" style="border:0;" <?= $checked['detail_add_type']['dc']?>/>��������</label>
			<label><input type="checkbox" name="detail_add_type[]" value="save" style="border:0;" <?= $checked['detail_add_type']['save']?>/>��������</label>
			<label><input type="checkbox" name="detail_add_type[]" value="new" style="border:0;" <?= $checked['detail_add_type']['new']?>/>�Ż�ǰ</label>
			<label><input type="checkbox" name="detail_add_type[]" value="event" style="border:0;" <?= $checked['detail_add_type']['event']?>/>�̺�Ʈ��ǰ</label>
		</div>

		<br><label><input type="checkbox" name="detail_type[]" value="color" style="border:0;" <?= $checked['detail_type']['color']?> />����˻�</label>
		<div style="padding:4px,0,4px,0;">
		<font class="extext">
		���� �˻��� ��ǰ��� ������������ ��ǥ���� ���� ����� ��ǰ�� ���Ͽ� �˻��Ǿ� ���ϴ�.<br/>
		<a href="./goods_color.php"><font class=extext_l>[ ��ǰ�ϰ����� > ���� ��ǥ���� ���� ]</font></a> �� ���Ͽ� ���� ��ǰ�� ��ǥ������ �ϰ��� ���� �Ͻ� �� �ֽ��ϴ�.
		</font>
		</div>
	</td>
</tr>
</table>
<script>
<? if(count($detail_add_type) >= 1 && $detail_add_type[0] != '') { ?> document.getElementById("benefit_box").style.display = "block";<? } ?>
</script>

<div class="title">
	���÷��� ���� <span>�˻� ��� �������� ����Ǵ� ��ǰ ������ �����մϴ�.</span>
</div>
<table class="tb">
<col class="cellC"><col class="cellL">
<tr>
	<th>���÷��� ����</th>
	<td>
		<div style="padding:4px,0,4px,0;"><font class="extext">�˻���� �������� �̹��� �������� �����Ǵ� ���������� ����Ʈ���� �����Ͽ� ������ �� �ֽ��ϴ�.</font></div>
		<div class="display-type-wrap">
			<img src="../img/goodalign_style_01.gif">
			<div class="noline"><input type="radio" name="disp_type" value="gallery" <?= $checked['disp_type']['gallery']?>/></div>
		</div>
		<div class="display-type-wrap">
			<img src="../img/goodalign_style_02.gif">
			<div class="noline"><input type="radio" name="disp_type" value="list" <?= $checked['disp_type']['list']?>/></div>
		</div>
		<div style="width:100%; height:110px"></div>
		<div style="padding-top:4px;"><font class="extext">���θ� �˻���� ����Ʈ�������� �⺻ ������ ����� ���÷��� ������ �����մϴ�.<br/>������� ������������ üũ�� ��� ����Ʈ ���� ����� ������ ������ ���� ����Ǹ�, ���θ� ���� ���ϴ� ���������� �����Ͽ� ����� �� �ֽ��ϴ�.</font></div>
	</td>
</tr>
</table>

<div class="button">
<input type="image" src="../img/btn_register.gif" />
<a href="javascript:history.back();"><img src="../img/btn_cancel.gif" /></a>
</div>


</form>
<? include "../_footer.php"; ?>