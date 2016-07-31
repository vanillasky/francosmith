<?
$hiddenLeft = 1;
$location = "���θ� App���� > ��ǰ����Ʈ ���ø� ����";
$scriptLoad='
	<link rel="stylesheet" type="text/css" href="../DynamicTree.css">
	<script src="../DynamicTree.js"></script>
	<script src="../DynamicTreeSorting.js"></script>
';
include "../_header.php";
@include "../../lib/pAPI.class.php";
@include_once "../../lib/json.class.php";

$pAPI = new pAPI();
$json = new Services_JSON(16);

$expire_dt = $pAPI->getExpireDate();
if(!$expire_dt) {
	msg('���� ��û�Ŀ� ��밡���� �޴��Դϴ�.', -1);
}

$now_date = date('Y-m-d 23:59:59');
$tmp_now_date = date('Y-m-d 23:59:59', mktime(0,0,0, substr($now_date, 5, 2), substr($now_date, 8, 2) - 30, substr($now_date, 0, 4)));
if($expire_dt < $tmp_now_date) {
	msg('���� ���Ⱓ ������ 30���� ���� ���񽺰� ���� �Ǿ����ϴ�.\n���񽺸� �ٽ� ��û�� �ֽñ� �ٶ��ϴ�.', -1);
}

### IFRAME �Ѱ����� ����Ÿ
parse_str( $_SERVER['QUERY_STRING'], $query_str );
unset( $query_str[ifrmScroll] );
unset( $query_str[category] );
foreach( $query_str as $k => $v ) $query_str[$k] = "$k=$v";
$query_str = implode( "&", $query_str );

$json_gr_nm = $pAPI->getGroupNm($godo['sno']);
$arr_gr_nm = $json->decode($json_gr_nm);
$gr_nm = $arr_gr_nm['gr_nm'];
?>

<script>

/*** �з�Ʈ�� �Ϻγ�� �ε� ***/
function openTree(obj, chkable) {

	tree.sorting.ready(obj);
	if (chkable && ifrmTemplate.document.getElementsByName('category')[0] != undefined){
		if (ifrmTemplate.document.getElementsByName('category')[0].value == obj.getElementsByTagName('input')[0].value) return;
	}

	if (chkable && ifrmMyTemplate.document.getElementsByName('category')[0] != undefined){
		if (ifrmMyTemplate.document.getElementsByName('category')[0].value == obj.getElementsByTagName('input')[0].value) return;
	}

	ifrmMyTemplate.location.href = "iframe.shopTouch_myTemplate.php?menu_idx=" + obj.getElementsByTagName('input')[0].value + "&<?=$query_str?>";

	ifrmTemplate.location.href = "iframe.shopTouch_template.php?menu_idx=" + obj.getElementsByTagName('input')[0].value + "&<?=$query_str?>";

}

function loadHistory(category, chkable) {
	var len = category.length / 3;
	var el = "cate" + len + "[]";
	var obj = document.getElementsByName(el);
	for (i=0;i<obj.length;i++){
		if (obj[i].value==category){
			openTree(obj[i].parentNode, chkable);
			break;
		}
	}
}

function editTemplate(tp_idx, mode) {

	var url = "./indb.php?mode=getSid";
	var sid;

	new Ajax.Request(url, {
		method: "get",
		asynchronous: false,
		onSuccess: function(transport) {
			sid = transport.responseText;
		}
	});

	document.getElementById("ifrmTemplateDesign").src="http://godo.vercoop.com/vt_editor/manager_page?tp_idx="+tp_idx+"&shop_idx=<?=$gr_nm?>&sid="+sid;
	document.getElementById("template_select").style.display="none";
	document.getElementById("template_edit").style.display="block";
	document.getElementById("select_title").style.display="none";
	document.getElementById("edit_title").style.display="block";

}

function listTemplate() {

	document.getElementById("ifrmTemplateDesign").src="";
	document.getElementById("template_edit").style.display="none";
	document.getElementById("template_select").style.display="block";
	document.getElementById("edit_title").style.display="none";
	document.getElementById("select_title").style.display="block";
}

</script>
<?
if($expire_dt < $now_date) {
	@include('shopTouch_expire_msg.php');
}
?>
<div class="title title_top"><div id="select_title">���θ� App ��ǰ����Ʈ ���ø� ���� <a href="javascript:manual('<?=$guideUrl?>board/view.php?id=shoppingapp&no=14')"><img src="../img/btn_q.gif" border=0 align=absmiddle hspace=2></a></div><div id="edit_title" style="display:none;">���θ� App ��ǰ����Ʈ ���ø� ���� <a href="javascript:manual('<?=$guideUrl?>board/view.php?id=shoppingapp&no=14')"><img src="../img/btn_q.gif" border=0 align=absmiddle hspace=2></a></div></div>
<div id="template_select">
<div style="width:200px;float:left;" >
<form method=post action="indb.php">
	<input type=hidden name=mode value="chgCategorySort">
	<input type=hidden name=category>
	<div id=treeCategory class=scroll style="width:200px;-webkit-padding-start:0px;-webkit-padding-end:0px;" onmousewheel="return iciScroll(this)">

	<div style="padding-bottom:1px">���θ� App ī�װ�(�ֻ����з�)</div>
	<div class="DynamicTree"><div class="wrap" id="tree">
		<span><font class=extext>ī�װ��� �ҷ����� ���Դϴ�.</font></span>
	</div></div>
	</div>
	</form>
	<div style="clear:both;"></div>
	<div id=MSG01>
	<table cellpadding=1 cellspacing=0 border=0 class=small_ex>
	<tr><td><img src="../img/icon_list.gif" align="absmiddle">ī�װ��� ������ �ּ���.</td></tr>
	</table>
	</div>
	<script>cssRound('MSG01')</script>
</div>

<div style="float:left;position:absolute;margin-left:10px;-webkit-margin-start:210px;">

<div class="title_sub" style="margin:0px 0px 5px 0px;border-bottom:none;">���� ���ø�<span>���ø��� ����, ���� �Ҽ� �ֽ��ϴ�. <font class=extext>(������ �Ͻ÷��� ������ �ݵ�� ���� ��ư�� �����ּ���)</font></span></div>
<div><iframe id=ifrmMyTemplate name=ifrmMyTemplate src="iframe.shopTouch_myTemplate.php?ifrmScroll=1" style="width:834px;height:185px;scroll-bar:none;" frameborder=0></iframe></div>
<div class="title_sub" style="margin:35px 0px 5px 0px;border-bottom:none;">���ø� ����<span>���ø��� ����, ����, ���� �Ҽ� �ֽ��ϴ�. <font class=extext>(������ �Ͻ÷��� ������ �ݵ�� ���� ��ư�� �����ּ���)</font></span></div>
<div><iframe id=ifrmTemplate name=ifrmTemplate src="iframe.shopTouch_template.php?ifrmScroll=1" style="width:834px;height:185px;scroll-bar:none;" frameborder=0></iframe></div>
</div>
</div>
<div style="float:left;margin-left:10px;">
<div id="template_edit" style="display:none;">
<div class="title_sub" style="margin:0px 0px 5px 0px;border-bottom:none;">���θ� App ���ø� ���� <a href="javascript:listTemplate();"><img src="../img/btn_choice_view.gif" align="absmiddle" alt="����ȭ������"></a></div>
<div><iframe id=ifrmTemplateDesign name=ifrmTemplateDesign src="" style="width:1024px;height:748px;scroll-bar:none;border:solid 2px #e5e5e5;" scrolling="no" frameborder=0></iframe></div>
</div>
</div>
<script type="text/javascript">
var tree = new DynamicTree("tree");
tree.category = '<?=$_GET[shopTouch_category]?>';
<? if ( preg_match( "/^rental_mxfree/i", $godo[ecCode] ) ){ ?>
tree.useHidding = false;
tree.useShifting = false;
<? } ?>
tree.init('shoptouch');
tree.Sorting();
</script>

<? include "../_footer.php"; ?>
