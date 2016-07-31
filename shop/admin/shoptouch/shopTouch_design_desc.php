<?php
$location = "쇼핑몰 App관리 > 상품상세화면 템플릿 선택";
include "../_header.php";
@include "../../lib/pAPI.class.php";
@include_once "../../lib/json.class.php";

$pAPI = new pAPI();
$json = new Services_JSON(16);

$expire_dt = $pAPI->getExpireDate();
if(!$expire_dt) {
	msg('서비스 신청후에 사용가능한 메뉴입니다.', -1);
}

$now_date = date('Y-m-d 23:59:59');
$tmp_now_date = date('Y-m-d 23:59:59', mktime(0,0,0, substr($now_date, 5, 2), substr($now_date, 8, 2) - 30, substr($now_date, 0, 4)));
if($expire_dt < $tmp_now_date) {
	msg('서비스 사용기간 만료후 30일이 지나 서비스가 삭제 되었습니다.\n서비스를 다시 신청해 주시기 바랍니다.', -1);
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
<div class="title title_top"><div id="select_title">쇼핑몰 App 상품상세화면 템플릿 선택 <a href="javascript:manual('<?=$guideUrl?>board/view.php?id=shoppingapp&no=15')"><img src="../img/btn_q.gif" border=0 align=absmiddle hspace=2></a></div><div id="edit_title" style="display:none;">쇼핑몰 App 상품상세화면 템플릿 편집 <a href="javascript:manual('<?=$guideUrl?>board/view.php?id=shoppingapp&no=15')"><img src="../img/btn_q.gif" border=0 align=absmiddle hspace=2></a></div> </div>
<div id="template_select">
<div class="title_sub" style="margin:0px 0px 5px 0px;border-bottom:none;">나의 템플릿<span>템플릿을 편집, 적용 할수 있습니다. <font class=extext>(적용을 하시려면 편집후 반드시 적용 버튼을 눌러주세요)</font></span></div>
<div><iframe id=ifrmMyTemplate name=ifrmMyTemplate src="iframe.shopTouch_myTemplate.php?ifrmScroll=1&menu_idx=detail" style="width:834px;height:185px;scroll-bar:none;" frameborder=0></iframe></div>
<div class="title_sub" style="margin:35px 0px 5px 0px;border-bottom:none;">템플릿 선택<span>템플릿을 편집, 복사, 적용 할수 있습니다. <font class=extext>(적용을 하시려면 편집후 반드시 적용 버튼을 눌러주세요)</font></span></div>
<div><iframe id=ifrmTemplate name=ifrmTemplate src="iframe.shopTouch_template.php?ifrmScroll=1&menu_idx=detail" style="width:834px;height:185px;scroll-bar:none;" frameborder=0></iframe></div>
</div>
<div id="template_edit" style="display:none;">
<div class="title_sub" style="margin:0px 0px 5px 0px;border-bottom:none;">쇼핑몰 App 템플릿 편집 <a href="javascript:listTemplate();"><img src="../img/btn_choice_view.gif" align="absmiddle" alt="템플릿 선택화면으로"></a></div>
<div><iframe id=ifrmTemplateDesign name=ifrmTemplateDesign src="" style="width:1024px;height:748px;scroll-bar:none;border:solid 2px #e5e5e5;" scrolling="no" frameborder=0></iframe></div>
</div>

<div id=MSG01>
<table cellpadding=1 cellspacing=0 border=0 class=small_ex>
<tr><td><img src="../img/icon_list.gif" align="absmiddle">쇼핑몰 App의 상품상세화면을 선택할 수 있습니다.</td></tr>
<tr><td><img src="../img/icon_list.gif" align="absmiddle">아래 템플릿 선택에서 제공되는 템플릿을 선택후 편집하시어 저장하시면 나의 템플릿으로 이동됩니다.</td></tr>
<tr><td><img src="../img/icon_list.gif" align="absmiddle">나의 템플릿에 편집해 놓은 템플릿을 적용하여 상품 상세화면을 구성할 수 있습니다.</td></tr>
<tr><td><img src="../img/icon_list.gif" align="absmiddle">상품 상세화면은 구매시에 필요한 정보가 정해져 있기 때문에 배경화면 정보만 변경하실 수 있습니다.</td></tr>
</table>
</div>
<script>cssRound('MSG01')</script>

<? include "../_footer.php"; ?>
