<?php
$location = "���θ� App���� > ��ǰ��ȭ�� ���ø� ����";
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

$arr_authkey['ok_id_shop'] = $_SESSION['sess']['m_id'];
$pwd_query = $db->_query_print('SELECT password FROM '.GD_MEMBER.' WHERE m_id=[s]', $arr_authkey['ok_id_shop']);

$res_pwd = $db->_select($pwd_query);
$arr_authkey['ok_pwd_shop'] = $res_pwd[0]['password'];
$arr_authkey['ok_sno_shop'] = $godo['sno'];
$arr_authkey['ok_domain_shop'] = $_SERVER['HTTP_HOST'];

$json_gr_nm = $pAPI->getGroupNm($godo['sno']);
$arr_gr_nm = $json->decode($json_gr_nm);
$gr_nm = $arr_gr_nm['gr_nm'];

?>
<script type="text/javascript">

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

	document.getElementById("ifrmTemplateDesign").src="http://godo.vercoop.com/vt_editor/manager_page?tp_idx="+tp_idx+"&shop_idx=<?=$gr_nm?>&tp_category=detail&sid="+sid;
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
<div class="title title_top"><div id="select_title">���θ� App ��ǰ��ȭ�� ���ø� ���� <a href="javascript:manual('<?=$guideUrl?>board/view.php?id=shoppingapp&no=15')"><img src="../img/btn_q.gif" border=0 align=absmiddle hspace=2></a></div><div id="edit_title" style="display:none;">���θ� App ��ǰ��ȭ�� ���ø� ���� <a href="javascript:manual('<?=$guideUrl?>board/view.php?id=shoppingapp&no=15')"><img src="../img/btn_q.gif" border=0 align=absmiddle hspace=2></a></div> </div>
<div id="template_select">
<div class="title_sub" style="margin:0px 0px 5px 0px;border-bottom:none;">���� ���ø�<span>���ø��� ����, ���� �Ҽ� �ֽ��ϴ�. <font class=extext>(������ �Ͻ÷��� ������ �ݵ�� ���� ��ư�� �����ּ���)</font></span></div>
<div><iframe id=ifrmMyTemplate name=ifrmMyTemplate src="iframe.shopTouch_myTemplate.php?ifrmScroll=1&menu_idx=detail" style="width:834px;height:185px;scroll-bar:none;" frameborder=0></iframe></div>
<div class="title_sub" style="margin:35px 0px 5px 0px;border-bottom:none;">���ø� ����<span>���ø��� ����, ����, ���� �Ҽ� �ֽ��ϴ�. <font class=extext>(������ �Ͻ÷��� ������ �ݵ�� ���� ��ư�� �����ּ���)</font></span></div>
<div><iframe id=ifrmTemplate name=ifrmTemplate src="iframe.shopTouch_template.php?ifrmScroll=1&menu_idx=detail" style="width:834px;height:185px;scroll-bar:none;" frameborder=0></iframe></div>
</div>
<div id="template_edit" style="display:none;">
<div class="title_sub" style="margin:0px 0px 5px 0px;border-bottom:none;">���θ� App ���ø� ���� <a href="javascript:listTemplate();"><img src="../img/btn_choice_view.gif" align="absmiddle" alt="���ø� ����ȭ������"></a></div>
<div><iframe id=ifrmTemplateDesign name=ifrmTemplateDesign src="" style="width:1024px;height:748px;scroll-bar:none;border:solid 2px #e5e5e5;" scrolling="no" frameborder=0></iframe></div>
</div>

<div id=MSG01>
<table cellpadding=1 cellspacing=0 border=0 class=small_ex>
<tr><td><img src="../img/icon_list.gif" align="absmiddle">���θ� App�� ��ǰ��ȭ���� ������ �� �ֽ��ϴ�.</td></tr>
<tr><td><img src="../img/icon_list.gif" align="absmiddle">�Ʒ� ���ø� ���ÿ��� �����Ǵ� ���ø��� ������ �����Ͻþ� �����Ͻø� ���� ���ø����� �̵��˴ϴ�.</td></tr>
<tr><td><img src="../img/icon_list.gif" align="absmiddle">���� ���ø��� ������ ���� ���ø��� �����Ͽ� ��ǰ ��ȭ���� ������ �� �ֽ��ϴ�.</td></tr>
<tr><td><img src="../img/icon_list.gif" align="absmiddle">��ǰ ��ȭ���� ���Žÿ� �ʿ��� ������ ������ �ֱ� ������ ���ȭ�� ������ �����Ͻ� �� �ֽ��ϴ�.</td></tr>
</table>
</div>
<script>cssRound('MSG01')</script>

<? include "../_footer.php"; ?>
