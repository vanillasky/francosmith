<?
/*********************************************************
* 파일명     :  disp_main.php
* 프로그램명 :	모바일샵 메인진열
* 작성자     :  dn
* 생성일     :  2012.04.30
**********************************************************/	

$location = "모바일샵 > 메인페이지 상품진열";
include "../_header.php";
include "../../conf/design.main.php";

$goodsDisplay = Core::loader('Mobile2GoodsDisplay');

if ($goodsDisplay->displayTypeIsSet() === false) {
	if ($goodsDisplay->isInitStatus()) {
		$goodsDisplay->saveMainDisplayType('pc');
	}
	else {
		$goodsDisplay->saveMainDisplayType('mobile');
	}
}

$res_design = $goodsDisplay->initializeMainDisplay();

?>
<script type="text/javascript">
function addDesignForm() {
	var form_div = $("design_forms").getElementsByTagName("div");	
	
	var form_no = (form_div.length + 2).toString();
	var new_div = document.createElement("DIV");
	new_div.setAttribute("style", "width:100%;");
	new_div.setAttribute("id", "design_form_"+form_no);
	
	var new_mdesign_no = generateFormKey();
	
	var add_html = '<iframe name="ifrm_form[]" id="ifrm_form'+new_mdesign_no+'" src="disp_main_form.php?mdesign_no='+new_mdesign_no+'" width="100%" scrolling="no" frameborder="0"></iframe>';
	new_div.innerHTML = add_html;
	
	$("design_forms").appendChild(new_div);
}

function generateFormKey() {
		
		var mode = "temp_design_insert";
		var key;
		var ajax = new Ajax.Request('./indb.php', {
			method: "post",
			parameters: 'mode='+mode,
			asynchronous: false,
			onComplete: function(response) { if (response.status == 200) {
				var json = response.responseText.evalJSON(true);
				key = json.mdesign_no;
			}}
		});

		return key;
	}

function saveAll() 
{
	var saveResult = true;
	var iframes = document.getElementsByName("ifrm_form[]"); 
	for(i=0; i<iframes.length; i++) {
		saveResult = iframes[i].contentWindow.modTimeout();
		if (saveResult === false) {
			break;
		}
	}
}

document.observe('dom:loaded', function() {
	cssRound('MSG01');
});
</script>
<style type="text/css">
a.blue:hover{
	color: #000000;
}
</style>
<?php if ($goodsDisplay->isPCDisplay()) { ?>
<div id="auto-display-guide" style="background-color: #ffdc6d; padding: 10px;">
	<span class="blue" style="font-weight: bold;">
		※ 현재 모바일샵 메인페이지는 온라인쇼핑몰(PC버전)과 동일하게 상품이 진열되어 보여지고 있습니다.<br/>
		<a href="<?php echo $cfg['rootDir']; ?>/admin/goods/disp_main.php" class="blue" target="_blank">[상품관리 > 상품진열관리 > 메인페이지 상품진열]</a> 에 설정된 상품정보가 모바일샵에 동일하게 적용됩니다.
	</span>
	<div style="line-height: 18px; margin-top: 5px;">
		온라인쇼핑몰(PC버전)과 동일하게 메인상품진열은 <a href="<?php echo $cfg['rootDir']; ?>/admin/mobileShop2/mobile_view_set.php">[모바일샵 상품관리 > 모바일샵 노출 설정 > 메인상품진열 노출설정]</a>에서 설정 가능합니다.</a>
	</div>
</div>
<?php } ?>

<div class="title">메인 페이지 상품진열 <a href="javascript:manual('<?=$guideUrl?>board/view.php?id=mobileshopV2&no=4')"><img src="../img/btn_q.gif"  align="absmiddle"></a></div>
<div>
	<font class='extext'>각 상품진열 영역별 변경사항 저장을 위해 <img src="../img/i_edit.gif" align="absmiddle" /> 버튼을 선택합니다.</font>
	<font class='extext'>전체 상품진열을 한번에 저장하기 위해서 화면 하단의 [등록] 버튼을 선택합니다.</font>
</div>
<div id="design_forms">
	<? 
	$i = 0;
	foreach($res_design as $row_design) {
		$i ++;
		$ifrm_src = 'disp_main_form.php?mdesign_no='.$row_design['mdesign_no'];
		if($i < 3) $ifrm_src .= '&content_no='.$i;
	?>
	<div style="width:100%;" id="design_form_<?=$i?>" >
		<iframe name="ifrm_form[]" id="ifrm_form<?=$row_design['mdesign_no']?>" src="<?=$ifrm_src?>" width="100%" scrolling="no" frameborder="0" ></iframe>
	</div>
	<? } ?>
</div>
<div class="button" style="text-align:center;">
<a href="javascript:saveAll();" ><img src="../img/btn_register.gif"></a>
</div>
<div style="margin-top:30px;margin-bottom:30px;">
	<a href="javascript:addDesignForm();" ><img src="../img/btn_goodsadd.gif"></a>
	<a href="javascript:manual('<?=$guideUrl?>board/view.php?id=mobileshopV2&no=4')"><img src="../img/btn_q.gif" style="vertical-align: top;"/></a>
</div>
<div id="MSG01">
<table cellpadding=1 cellspacing=0 border=0 class=small_ex>
<tr><td><img src="../img/icon_list.gif" align="absmiddle">각 상품진열 영역별 변경사항 저장을 위해 <img src="../img/i_edit.gif" align="absmiddle" /> 버튼을 선택합니다.</td></tr>
<tr><td><img src="../img/icon_list.gif" align="absmiddle">전체 상품진열을 한번에 저장하기 위해서 화면 하단의 [등록] 버튼을 선택합니다.</td></tr>
<tr>
	<td style="padding-top: 10px;">
		<span style="color: #ffff00; font-size: 12px; font-weight: bold;">※ "온라인 쇼핑몰(PC버전)과 동일하게 메인 상품진열 적용" 상태</span>
		<ol style="margin: 0 5px 0 20px;">
			<li>[모바일샵 노출 설정 > 메인 상품 진열 노출 설정]에서 설정 가능합니다.</li>
			<li>상품진열1, 상품진열2 와 같이 템플릿이 디자인스킨에 소스삽입이 되어있으며, 사용유무가 체크되어 있는 진열영역에만 상품이 진열되어 보여집니다.</li>
			<li>
				온라인쇼핑몰(PC버전) 메인의 진열영역 개수와 상품을 모바일샵 메인에 동일하게 보여지게 하시려면,
				<div>
					1) [ 상품진열 추가하기 ] 를 버튼을 클릭하여 진열영역을 추가 합니다. 진열할 수 있는 상품의 최대개수는 300개 입니다.<br/>
					2) 각 진열영역별 사용유무 체크 합니다.<br/>
					3) 페이지 하단의 [ 등록 ] 버튼을 클릭하여 설정을 저장합니다.<br/>
					4) 각 진열영역별 ‘템플릿 소스코드’ 를 복사합니다.<br/>
					5) 복사한 ‘템플릿 소스코드’를 [ 모바일샵 디자인관리 > 왼쪽 트리메뉴 > 모바일 메인페이지 ] 에서 소스편집에디터 맨 하단 &lt;section id="main" class="content"&gt;영역에 삽입한 후 [ 저장 ] 합니다.<br/>
					6) 자세한 내용은 <a href="javascript:manual('<?=$guideUrl?>board/view.php?id=mobileshopV2&no=4')"><img src="../img/btn_q.gif" style="vertical-align: middle;"/></a>을 참고해 주세요.
				</div>
			</li>
		</ol>
	</td>
</tr>
</table>
</div>



