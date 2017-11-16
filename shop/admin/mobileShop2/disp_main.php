<?
/*********************************************************
* ���ϸ�     :  disp_main.php
* ���α׷��� :	����ϼ� ��������
* �ۼ���     :  dn
* ������     :  2012.04.30
**********************************************************/	

$location = "����ϼ� > ���������� ��ǰ����";
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
		�� ���� ����ϼ� ������������ �¶��μ��θ�(PC����)�� �����ϰ� ��ǰ�� �����Ǿ� �������� �ֽ��ϴ�.<br/>
		<a href="<?php echo $cfg['rootDir']; ?>/admin/goods/disp_main.php" class="blue" target="_blank">[��ǰ���� > ��ǰ�������� > ���������� ��ǰ����]</a> �� ������ ��ǰ������ ����ϼ��� �����ϰ� ����˴ϴ�.
	</span>
	<div style="line-height: 18px; margin-top: 5px;">
		�¶��μ��θ�(PC����)�� �����ϰ� ���λ�ǰ������ <a href="<?php echo $cfg['rootDir']; ?>/admin/mobileShop2/mobile_view_set.php">[����ϼ� ��ǰ���� > ����ϼ� ���� ���� > ���λ�ǰ���� ���⼳��]</a>���� ���� �����մϴ�.</a>
	</div>
</div>
<?php } ?>

<div class="title">���� ������ ��ǰ���� <a href="javascript:manual('<?=$guideUrl?>board/view.php?id=mobileshopV2&no=4')"><img src="../img/btn_q.gif"  align="absmiddle"></a></div>
<div>
	<font class='extext'>�� ��ǰ���� ������ ������� ������ ���� <img src="../img/i_edit.gif" align="absmiddle" /> ��ư�� �����մϴ�.</font>
	<font class='extext'>��ü ��ǰ������ �ѹ��� �����ϱ� ���ؼ� ȭ�� �ϴ��� [���] ��ư�� �����մϴ�.</font>
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
<tr><td><img src="../img/icon_list.gif" align="absmiddle">�� ��ǰ���� ������ ������� ������ ���� <img src="../img/i_edit.gif" align="absmiddle" /> ��ư�� �����մϴ�.</td></tr>
<tr><td><img src="../img/icon_list.gif" align="absmiddle">��ü ��ǰ������ �ѹ��� �����ϱ� ���ؼ� ȭ�� �ϴ��� [���] ��ư�� �����մϴ�.</td></tr>
<tr>
	<td style="padding-top: 10px;">
		<span style="color: #ffff00; font-size: 12px; font-weight: bold;">�� "�¶��� ���θ�(PC����)�� �����ϰ� ���� ��ǰ���� ����" ����</span>
		<ol style="margin: 0 5px 0 20px;">
			<li>[����ϼ� ���� ���� > ���� ��ǰ ���� ���� ����]���� ���� �����մϴ�.</li>
			<li>��ǰ����1, ��ǰ����2 �� ���� ���ø��� �����ν�Ų�� �ҽ������� �Ǿ�������, ��������� üũ�Ǿ� �ִ� ������������ ��ǰ�� �����Ǿ� �������ϴ�.</li>
			<li>
				�¶��μ��θ�(PC����) ������ �������� ������ ��ǰ�� ����ϼ� ���ο� �����ϰ� �������� �Ͻ÷���,
				<div>
					1) [ ��ǰ���� �߰��ϱ� ] �� ��ư�� Ŭ���Ͽ� ���������� �߰� �մϴ�. ������ �� �ִ� ��ǰ�� �ִ밳���� 300�� �Դϴ�.<br/>
					2) �� ���������� ������� üũ �մϴ�.<br/>
					3) ������ �ϴ��� [ ��� ] ��ư�� Ŭ���Ͽ� ������ �����մϴ�.<br/>
					4) �� ���������� �����ø� �ҽ��ڵ塯 �� �����մϴ�.<br/>
					5) ������ �����ø� �ҽ��ڵ塯�� [ ����ϼ� �����ΰ��� > ���� Ʈ���޴� > ����� ���������� ] ���� �ҽ����������� �� �ϴ� &lt;section id="main" class="content"&gt;������ ������ �� [ ���� ] �մϴ�.<br/>
					6) �ڼ��� ������ <a href="javascript:manual('<?=$guideUrl?>board/view.php?id=mobileshopV2&no=4')"><img src="../img/btn_q.gif" style="vertical-align: middle;"/></a>�� ������ �ּ���.
				</div>
			</li>
		</ol>
	</td>
</tr>
</table>
</div>



