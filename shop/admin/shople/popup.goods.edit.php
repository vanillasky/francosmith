<?
include "../_header.popup.php";
require_once ('./_inc/config.inc.php');

$goodsno	= isset($_GET['goodsno']) ? $_GET['goodsno'] : '';
$goodsno11st= isset($_GET['goodsno11st']) ? $_GET['goodsno11st'] : '';
$mode		= isset($_GET['mode']) ? $_GET['mode'] : '';

if (!$mode || (!$goodsno && !$goodsno11st)) {	msg('잘못된 접근입니다.','close'); 	exit; }
if ($goodsno11st != '') list($goodsno) = $db->fetch("select goodsno from ".GD_SHOPLE_GOODS_MAP." where 11st='{$goodsno11st}'");

$shople = Core::loader('shople');
$data = $shople->getGoods($goodsno);

// mode 에 따른 에디팅 및 윈도우 리사이즈.
switch ($mode) {
	case 'descript':
		$win['size'] = array(800,650);
		$win['title'] = '상세설명';
		break;
	case 'option':
		$win['size'] = array(800,400);
		$win['title'] = '상품옵션';
		break;
	case 'image':
		$win['size'] = array(650,400);
		$win['title'] = '이미지';
		break;
	default:
		msg('잘못된 접근입니다.','close');
		exit;
		break;
}
?>
<script type="text/javascript" src="./_inc/common.js"></script>
<script type="text/javascript">
	/* 옵션 부분 삭제 */
	function delopt1part(rid)
	{
		var obj = document.getElementById(rid);
		var tbOption = document.getElementById('tbOption');
		if (tbOption.rows.length>2) tbOption.deleteRow(obj.rowIndex);
	}
	function delopt2part(cid)
	{
		var obj = document.getElementById(cid);
		var tbOption = document.getElementById('tbOption');

		if (tbOption.rows[0].cells.length<3) return;
		for (i=0;i<tbOption.rows.length;i++){
			tbOption.rows[i].deleteCell(obj.cellIndex);
		}
	}

	/*** 폼체크 ***/
	function chkForm2(obj)
	{
		if (!chkOption()) return false;
		if (!chkForm(obj)) return false;
	}

	/*** 상품 가격/재고 ***/
	function addopt1()
	{
		var name;
		var fm = document.forms[0];
		var tbOption = document.getElementById('tbOption');
		var Rcnt = tbOption.rows.length;
		oTr = tbOption.insertRow();
		oTr.id = "trid_" + Rcnt;

		for (i=0;i<tbOption.rows[0].cells.length;i++){
			oTd = oTr.insertCell();
			switch (i){
				case 0: oTd.innerHTML = "<input type=text class='opt gray' name=opt1[] value='옵션명1' required label='1차옵션명' ondblclick=\"delopt1part('"+oTr.id+"')\" onclick='chkOptName(this)' onblur=\"chkOptNameOver(this)\" style='width:110px;'>";
				break;
				case 1:	oTd.innerHTML = "<input type=text name=option[price][] class='opt gray' value='' style='width:65px;'>"; break;
				case 2:	oTd.innerHTML = "<input type=text name=option[consumer][] class='opt gray' value='' style='width:65px;'>"; break;
				default: oTd.innerHTML = "<input type=text name=option[stock][] class='opt gray' value='재고' onclick='chkOptName(this)' onblur=\"chkOptNameOver(this)\"><input type=hidden name=option[optno][]>"; break;
			}
		}
	}


	function addopt2()
	{
		var name;
		var tbOption = document.getElementById('tbOption');
		if (tbOption.rows.length<2){
			alert('1차옵션을 먼저 추가해주세요');
			return;
		}

		var Ccnt = tbOption.rows[0].cells.length;

		for (i=0;i<tbOption.rows.length;i++){
			oTd = tbOption.rows[i].insertCell();
			if(!i)oTd.id = "tdid_"+Ccnt;
			oTd.innerHTML = (i) ? "<input type='text' name=option[stock][] class='opt gray' value='재고' onclick='chkOptName(this)' onblur=\"chkOptNameOver(this)\">" : "<input type='text' class='opt gray' name=opt2[] value='옵션명2' required label='2차옵션명' ondblclick=\"delopt2part('"+oTd.id+"')\" onclick='chkOptName(this)' onblur=\"chkOptNameOver(this)\">";
		}
	}
	function delopt1()
	{
		var tbOption = document.getElementById('tbOption');
		if (tbOption.rows.length>2) tbOption.deleteRow();
	}
	function delopt2()
	{
		var tbOption = document.getElementById('tbOption');
		if (tbOption.rows[0].cells.length<7) return;
		for (i=0;i<tbOption.rows.length;i++){
			tbOption.rows[i].deleteCell();
		}
	}

	/*** 자동으로 가격필드에 입력값 저장 ***/
	function autoPrice(obj)
	{
		var name = obj.name;
		var el = document.getElementsByName('option[' + name + '][]');
		el[0].value = obj.value;
	}

	function vOption()
	{
		//document.fm.stock.disabled = !document.fm.stock.disabled;
		openLayer('objOption');
	}

	function chkOptName(obj){
		if(obj.value=='옵션명2' || obj.value=='옵션명1'){
			obj.className = 'fldtitle';
			obj.value = '';
		}
		if(obj.value=='재고'){
			obj.className = 'opt';
			obj.value = '';
		}
	}

	function chkOptNameOver(obj){
		if(obj.value==''){
			obj.className = 'opt gray';
			if(obj.name == 'opt1[]') obj.value = '옵션명1';
			if(obj.name == 'opt2[]') obj.value = '옵션명2';
			if(obj.name == 'option[stock][]') obj.value = '재고';
		}
	}

	function chkOption(){
		var obj = document.getElementsByName('opt1[]');
		var chk = false;
		for(var i=0;i < obj.length;i++){
			 chkOptName(obj[i]);
			 if(obj[i].value == '' && obj.length > 1){
				alert('옵션 1은 필수 항목입니다.');
				obj[i].focus();
				return false;
			 }
			 if( (obj[i].value || obj.length > 1) && !chk) chk = true;
		}

		var obj = document.getElementsByName('opt2[]');
		for(var i=0;i < obj.length;i++){
			chkOptName(obj[i]);
			if(chk && obj[i].value == '' && obj.length > 1){
				alert('옵션 2은 필수 항목입니다.');
				obj[i].focus();
				return false;
			}
		}

		var obj = document.getElementsByName('option[stock][]');
		for(var i=0;i < obj.length;i++){
			chkOptName(obj[i]);
		}

		return true;
	}

	function setShipDisabled()
	{
		obj = document.getElementsByName('delivery_type');
		for (i = 0; i < obj.length; i++){
			isDisabled = (obj[i].checked == true ? false : true);
			inputObj = obj[i].parentNode.parentNode.getElementsByTagName('td')[1].getElementsByTagName('input');

			for (j = 0; j < inputObj.length; j++){
				inputObj[j].disabled = isDisabled;
				inputObj[j].style.backgroundColor = (isDisabled ? '#DDDDDD' : '#FFFFFF');
			}
		}
	}

	function fnSetImageAttachForm() {

		var m, obj = document.fm.image_attach_method;

		for (var i=0; i <obj.length; i++) {
			if (obj[i].checked)
			 var m = obj[i].value;
		}

		if (m == 'file') {
			document.getElementById('image_attach_method_upload_wrap').style.display = 'block';
			document.getElementById('image_attach_method_link_wrap').style.display = 'none';
		}
		else {
			document.getElementById('image_attach_method_upload_wrap').style.display = 'none';
			document.getElementById('image_attach_method_link_wrap').style.display = 'block';
		}

	}

	function fnResetWindow(x,y) {
		var pos_x = (screen.availWidth - x)/2;
		var pos_y = (screen.availHeight - y)/2;

		window.resizeTo(x,y);
		window.moveTo(pos_x,pos_y);
	}
</script>

<div class="title title_top" style="margin-top:10px;"><?=$win['title']?> 수정</div>

<form name="fm" method="post" action="./indb.popup.goods.php" enctype="multipart/form-data" onsubmit="return chkForm2(this)" target="ifrmHidden">
	<input type="hidden" name="mode" value="<?=$mode?>">
	<input type="hidden" name="goodsno" value="<?=$goodsno?>">
	<? include('./popup.goods.edit.inc.'.$mode.'.php'); ?>

	<div class="button">
		<input type=image src="../img/btn_modify.gif">
		<img src="../img/btn_cancel.gif" class="hand" onClick="self.close();">
	</div>

</form>

<div style="padding-top:15px"></div>

<script type="text/javascript" src="../proc/warning_disk_js.php"><!-- not_delete --></SCRIPT>
<script type="text/javascript">
function _fnInit() {
	table_design_load();
	fnResetWindow(<?=$win['size'][0]?>,<?=$win['size'][1]?>);

	<? if($mode == 'image') { ?>fnSetImageAttachForm();<? } ?>
}
Event.observe(document, 'dom:loaded', _fnInit, false);
</script>
</body>
</html>
