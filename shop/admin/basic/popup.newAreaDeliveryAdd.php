<?php
include '../lib.php';
?>
<html>

<head>
	<meta http-equiv="Content-Type" content="text/html; charset=euc-kr">
	<title>++ GODOMALL NEWAREA DELIVERY ADD ++</title>
	<script src="../common.js"></script>
	<link rel="styleSheet" href="../style.css">
	<script type="text/javascript" src="<? echo $cfg['rootDir']; ?>/lib/js/jquery-1.10.2.min.js"></script>
</head>

<style type="text/css">
tr						{ height: 40px; }
td						{ padding-left: 5px; }
img						{ cursor: pointer; }
.newAreaInputText		{ width: 98%; }
.newAreaPayInputText	{ width: 98%; ime-mode: disabled;}
.newAreaTrHeight180		{ height: 180px; }
.newAreaTrHeight25		{ height: 25px; }
.newAreaFontBold		{ font-weight: bold; }
.newAreaAlignCenter		{ text-align: center; }
.newAreaAlignLeft		{ text-align: left; }
.newAreaPaddingLtz		{ padding-left: 0px; }
.newAreaPaddingBt5		{ padding-bottom: 5px; }
.newAreaPaddingTp20		{ padding-top: 20px; }
.newAreaBgColorGray1	{ background-color:#A6A6A6; }
.newAreaBgColorGray2	{ background-color:#EAEAEA; }
.newAreaBgColorWhite	{ background-color: white; }
</style>

<script type="text/javascript">
var ajaxPage = "./popup.newAreaDeliveryAjax.php";

$(document).ready(function() {
	var setNewAreaGugun = '<select name="newAreaGugun" id="newAreaGugun"><option value="">�������ּ���</option></select>';

	//�õ� ����
	$.post(ajaxPage, { mode : "getAddressApi", listType : "newAreaSido"}, function(data){
		$("#newAreaSido").html(data);
		$("#newAreaGugun").html(setNewAreaGugun);
	}).fail(function() {
		alert("��� ������ �߻��Ͽ����ϴ�."); return false;
	});

	//���� ����
	$("#newAreaSido").change(function(){
		if($("#newAreaSido option:selected").val() == ""){
			$("#newAreaGugun").html(setNewAreaGugun);
			return false;
		}
		var newAreaSidoValue = $("#newAreaSido option:selected").val().split("|");
		$.post(ajaxPage, {
			mode : "getAddressApi", 
			listType : "newAreaGugun", 
			newAreaSido: newAreaSidoValue[0]
		}, function(data){
			$("#newAreaGugun").html(data);
		}).fail(function() {
			alert("��� ������ �߻��Ͽ����ϴ�."); return false;
		});
	});

	$("#registerArea").click(function(){
		var newAreaSidoValue= new Array();
		var newAreaSido		= '';
		if($("#newAreaSido option:selected").val()){
			newAreaSidoValue= $("#newAreaSido option:selected").val().split("|");
			newAreaSido		= newAreaSidoValue[1];
		}
		var newAreaGugun	= $("#newAreaGugun option:selected").val();
		var newAreaPay		= $("input[name=newAreaPay]").val();
		var newAreaEtc		= $("input[name=newAreaAdress]").val();
		
		if(newAreaSido.length < 1 ){
			alert("�õ� �� �����Ͽ� �ּ���.");
			return false;
		}

		if(newAreaSido!='����Ư����ġ��' && newAreaGugun.length < 1){
			alert("�ñ��� �� �����Ͽ� �ּ���.");
			return false;
		}

		if(newAreaPay == ""){
			alert("�߰���ۺ� �Է��Ͽ� �ּ���.");
			return false;
		}

		$.post(ajaxPage, { mode : "write", newAreaSido: newAreaSido, newAreaGugun: newAreaGugun, newAreaEtc: newAreaEtc, newAreaPay: newAreaPay }, function(data){
			if(data.match(/error-/)){
				data = data.replace('error-', '');
				alert(data);
			}
			else{
				alert(data);
				parent.newAreaLayerReload();
				parent.newAreaLayerClose();
			}
		}).fail(function() {
			alert("��� ������ �߻��Ͽ����ϴ�."); return false;
		});
	});
});
</script>

<body topmargin=5 margintop=5 leftmargin=10 rightmargin=10 marginwidth=10 marginheight=5>
<div class="title title_top">�߰� ����ϱ� <span>���� �߰��� �ּ����� ����մϴ�.</span></div>

<table cellpadding="0" cellspacing="1" width="100%" border="0" class="newAreaBgColorGray1" summary="�ּ� ����Ʈ">
<colgroup>
	<col width="135px" />
	<col width="*" />
</colgroup>
<tr class="newAreaTrHeight180">
	<td class="newAreaBgColorGray2 newAreaAlignLeft newAreaFontBold">�ּ��� �Է�</td>
	<td class="newAreaBgColorWhite">
		<table cellpadding="0" cellspacing="0" width="100%" border="0">
		<colgroup>
			<col width="19%" />
			<col width="31%" />
			<col width="19%" />
			<col width="31%" />
		</colgroup>
		<tr>
			<td class="newAreaFontBold">�õ� <img src="../img/icons/bullet_compulsory.gif" border="0" style="vertical-align:bottom;"/></td>
			<td class="newAreaPaddingLtz" id="newAreaSido"></td>
			<td class="newAreaFontBold">�ñ��� <img src="../img/icons/bullet_compulsory.gif" border="0" style="vertical-align:bottom;"/></td>
			<td class="newAreaPaddingLtz" id="newAreaGugun"></td>
		</tr>
		<tr>
			<td colspan="4">
				<div class="newAreaFontBold newAreaPaddingBt5">������ �ּ� �Է�</div>
				<input type="text" name="newAreaAdress" class="newAreaInputText" />
			</td>
		</tr>
		<tr class="newAreaTrHeight25">
			<td colspan="4" class="extext_t">
				- ���õ�/�ñ������� ���� �� �߰���ۺ� ����� ���������������� �Է����ּ���.<br />
				��) ���󳲵� �žȱ� ����
			</td>
		</tr>
		<tr class="newAreaTrHeight25">
			<td colspan="4" class="extext_t">
				- ���ñ����� ���� ��� ����Ǵ� ������ ��� ���������ּ� �Է£��κ��� ����νñ� �ٶ��ϴ�.<br />
				��) ���ϵ� �︪��
			</td>
		</tr>
		<tr class="newAreaTrHeight25">
			<td colspan="4" class="extext_t">
				- ���θ�/���� �ּ� ���� ���� <strong>1,000</strong>�� ���� �����մϴ�.
			</td>
		</tr>
		</table>
	</td>
</tr>
<tr>
	<td class="newAreaBgColorGray2 newAreaAlignLeft newAreaFontBold">�߰���ۺ� �Է� <img src="../img/icons/bullet_compulsory.gif" border="0" style="vertical-align:bottom;"/></td>
	<td class="newAreaBgColorWhite"><input type="text" name="newAreaPay" id="newAreaPay" class="newAreaPayInputText" onkeydown="javascript:parent.checkNumber(event);" onkeyup="javascript:parent.checkNumber(event);" maxlength="8" /></td>
</tr>
</table>


<table cellpadding="0" cellspacing="0" width="100%" border="0" class="newAreaPaddingTp20">
<tr>
	<td class="newAreaAlignCenter"><img src="../img/btn_register.gif" border="0" id="registerArea" /></td>
</tr>
</table>

</body>
</html>