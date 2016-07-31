<?php
include '../lib.php';
include '../../lib/page.class.php';
@include './popup.newAreaDeliveryLib.func.php';

$settingText = '주소지입력';
if(!$_GET['page'])		$_GET['page'] = 1;
if(!$_GET['page_num'])	$_GET['page_num'] = 10;
if(!$_GET['sort'])		$_GET['sort'] = 'areaSido asc';

$selected[$_GET['page_num']] = " selected='selected'";

if($_GET['sort'] == 'areaSido asc'){
	$orderBy = 'areaSido asc, areaGugun asc, areaEtc asc';
}else if($_GET['sort'] == 'areaSido desc'){
	$orderBy = 'areaSido desc, areaGugun desc, areaEtc desc';
}else{
	$orderBy = $_GET['sort'];
}

if($_GET['searchText'] && $_GET['searchText'] != $settingText){
	$where[] = " concat_ws( ' ', areaSido, areaGugun, areaEtc) like '%" . $_GET['searchText'] . "%'";
}

$pg = new Page($_GET['page'], $_GET['page_num']);
$pg->field = " areaNo, areaSido, areaGugun, areaEtc, areaPay ";
$pg->setQuery(GD_AREA_DELIVERY, $where, $orderBy);
$pg->exec();

$result = $db->query($pg->query);
$newAreaDeliveryCount = newAreaTotalCount();
$limitMsg = newAreaLimitCheck();
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" lang="ko" xml:lang="ko">
<meta http-equiv="X-UA-Compatible" content="IE=edge" />
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=euc-kr">
	<title>++ GODOMALL NEWAREA DELIVERY ++</title>
	<script src="../common.js"></script>
	<link rel="styleSheet" href="../style.css">
	<script type="text/javascript" src="../../lib/js/jquery-1.10.2.min.js"></script>
</head>

<script type="text/javascript">
var settingText = '<?php echo $settingText; ?>';

function newAreaLayerClose()
{
	$('#newAreaLayerBg').remove();
	$('#newAreaLayerObj').remove();
}

function newAreaLayerReload()
{
	parent.document.getElementById('newAreaIframe').contentWindow.location.reload(true);
}

function newAreaLayer(page)
{
	var pixelBorder = 3;
	var titleHeight = 12;
	var layerWidth	= 600 + (pixelBorder * 2);
	var layerHeight = 400 + (pixelBorder * 2) + titleHeight;

	var windowWidth = ($(window).width()) ? $(window).width() : 800;
	var windowHeight = ($(window).height()) ? $(window).height() : 600;

	var posX = (windowWidth - layerWidth) / 2;
	var posY = (windowHeight - layerHeight) / 2;

	$('<div id="newAreaLayerBg"></div><div id="newAreaLayerObj"></div>').appendTo('body');
	$('#newAreaLayerBg').css({
		'position': 'absolute',
		'left': '0px',
		'top': '0px',
		'width': $(document).width()+ 'px',
		'height': $(document).height() + 'px',
		'backgroundColor': '#000000',
		'filter': 'Alpha(Opacity=80)',
		'opacity': '0.5'
	});
	$('#newAreaLayerObj').css({
		'position': 'absolute',
		'left': posX + $(window).scrollLeft() +'px',
		'top': posY + $(window).scrollTop() +'px',
		'width': layerWidth,
		'height': layerHeight,
		'backgroundColor': '#ffffff',
		'border': pixelBorder + 'px solid #000000'
	});

	$('#newAreaLayerObj').append('<div id="objBottom"><a href="javascript:newAreaLayerClose();" class="white">X close</a></div>');
	$('#objBottom').css({
		'position': 'absolute',
		'width': '100%',
		'height': titleHeight + 'px',
		'bottom': '0px',
		'backgroundColor': '#000000',
		'color': '#ffffff',
		'textAlign': 'center',
		'font': 'bold 8pt tahoma; letter-spacing:0px'
	});

	$('#newAreaLayerObj').append('<iframe id="newAreaIframe" name="newAreaIframe" frameBorder="0"></iframe>');
	$('#newAreaIframe').css({ 'width': '100%', 'height': '100%' });
	$('#newAreaIframe').attr('src', page);
	$('#newAreaIframe').focus();
}

function openAddArea(type)
{
	if(type == 'csv') var page = 'popup.newAreaDeliveryExcel.php';
	else var page = 'popup.newAreaDeliveryAdd.php';

	newAreaLayer(page);
}

function delRow()
{
	var newAreaChk = $("input[name='newAreaChk[]']");
	var checkNum = false;
	for(var i=0; i<newAreaChk.length; i++) if(newAreaChk[i].checked == true) checkNum++;
	if(checkNum === false){
		alert('삭제할 주소지를 선택해 주세요. ');
		return false;
	}
	if(confirm("정말 삭제하시겠습니까?")){
		formSubmitType('delete');
	}
	return false;
}

function checkNumber(e)
{
	var event = e || window.event;

	//숫자 + ctrl + c + v + enter + tab
	if(
		!(event.keyCode > 47 && event.keyCode < 58) && 
		!(event.keyCode > 95 && event.keyCode < 106) && 
		event.keyCode != 8 && 
		event.keyCode != 46 && 
		event.keyCode != 17 && 
		event.keyCode != 67 && 
		event.keyCode != 86 && 
		event.keyCode != 13 && 
		event.keyCode != 9
	) {
		eventFalse(event);
	}
}

function searchTabIndex(e)
{
	var event = e || window.event;
	if(event.keyCode == 13){
		eventFalse(event);
		formSubmitType('search', $("#_searchText").val());
	}
}

function eventFalse(event)
{
	if(event.preventDefault){
		event.preventDefault();
	} else {
		event.returnValue = false;
	}
}

function sortFormSubmitType(type, val){
	$("input[name=sortReady]").val('y');
	formSubmitType(type, val);
}

function formSubmitType(type, val)
{
	switch(type){
		case 'search' : 
			$("input[name=searchText]").val(val);  
			$("#newAreaSearchForm").submit();
		break;
		case 'sort' : 
			$("input[name=sort]").val(val);  
			$("#newAreaSearchForm").submit();
		break;
		case 'newAreaSetting' :
			$("input[name=type]").val('setting');
			$("#newAreaForm").submit();
		break;
		case 'delete' :
			$("input[name=type]").val('delete');
			$("#newAreaForm").submit();
		break;
	}
}

function serchTextSetting()
{
	if(!$("#_searchText").val() || $("#_searchText").val() == settingText) $("#_searchText").val(settingText).css("color", "#D5D5D5");
}

$(window).load(function(){
	//input setting
	serchTextSetting();
	$("#_searchText").bind("focus click",function(){
		$("#_searchText").css("color", "#000000");
		if($("#_searchText").val() == settingText) $("#_searchText").val("");
	})
	.blur(function(){
		serchTextSetting();
	});

	$("#newAreaSetting").click(function(){
		if('<?php echo $newAreaDeliveryCount; ?>' > 0){
			alert("등록된 주소지를 모두 삭제하신 후 기본주소지를 적용할 수 있습니다.");
		}
		else{
			if(confirm("기본주소지목록을 불러오시겠습니까?")){
				formSubmitType('newAreaSetting');
			}
		}
		
		return false;
	});

	$("#searchBtn").click(function(){
		formSubmitType("search", $("#_searchText").val());
	});

	//배송비 체크
	$("#newAreaForm").submit(function(e){
		var event = e || window.event;

		if($("input[name=type]").val() == "modify"){
			$("input[name='newAreaPay[]']").each(function(){
				if($(this).val() == ""){
					alert("추가배송비를 입력하여 주세요.");

					if(event.preventDefault) event.preventDefault();
					else event.returnValue = false;

					$(this).focus();
					return false;
				}
			});
		}
	});

	//sort img
	var sort		= "<?php echo $_GET['sort']; ?>";
	var sortReady		= "<?php echo $_GET['sortReady']; ?>";
	if(sortReady){
		var sortId		= sort.replace(" ", "_");
		var orderByImg	= $("#orderByTd").find("a").find("img"); 

		for(var i=0; i<orderByImg.length; i++){
			var imgSrc = orderByImg.eq(i).attr("src");

			if(sortId == orderByImg.eq(i).attr("id")){
				$("#" + sortId).attr("src", imgSrc.replace("off", "on"));
			}else{
				$("#" + orderByImg.eq(i).attr("id")).attr("src", imgSrc.replace("on", "off"));
			}
		}
	}
});
</script>

<style type="text/css">
html					{ height: 100%; }
a:hover					{ color: black; }
tr						{ height: 25px; }
td						{ padding-left: 5px; }
#newAreaDeliveryTable	{ word-break: break-all; }
.newAreaBar				{ position: fixed !important; bottom: 0px; width: 100%; display: block; float: none; background-color: #ffffff; left: 0px; _padding-top: 20px;}
.newAreaHeight55		{ height: 55px; }
.newAreaHeight20		{ height: 20px; }
.newAreaInputText		{ width: 95%; text-align: right; ime-mode: disabled; }
.newAreaBorder0			{ border: 0px; }
.newAreaAlignRight		{ text-align: right; }
.newAreaAlignLeft		{ text-align: left; }
.newAreaAlignCenter		{ text-align: center; }
.newAreaPaddingBt		{ padding-bottom: 3px; }
.newAreaPaddingTp		{ padding-top: 10px; }
.newAreaPaddingTp5		{ padding-top: 5px; }
.newAreaPaddingZ		{ padding: 0px; }
.newAreaPaddingL16		{ padding-left: 16px; }
.newAreaPaddingL9		{ padding-left: 9px; }
.newAreaPaddingL3		{ padding-left: 3px; }
.newAreaBgColorGray1	{ background-color:#A6A6A6; }
.newAreaBgColorGray2	{ background-color:#EAEAEA; }
.newAreaBgColorWhite	{ background-color: white; }
.newAreaVerticalBottom	{ vertical-align: bottom; }
.newAreaCursorPointer	{ cursor: pointer; }
#orderByTd img			{ vertical-align: bottom; padding-bottom: 0px; }
</style>


<body topmargin=5 margintop=5 leftmargin=10 rightmargin=10 marginwidth=10 marginheight=5>
<div class="title title_top">지역명/도로명 추가배송비 목록 <span>항공료, 도선료 등으로 인하여 기본배송비 이외에 추가로 발생하는 배송비를 지역별로 관리합니다.</span></div>

<form name="newAreaSearchForm" id="newAreaSearchForm" method="GET">
<input type="hidden" name="sort" value="<?php echo $_GET['sort']; ?>">
<input type="hidden" name="sortReady" value="<?php echo $_GET['sortReady']; ?>">
<input type="hidden" name="searchText" value="<?php echo $_GET['searchText']; ?>">
<table cellpadding="0" cellspacing="0" width="100%" border="0" class="newAreaPaddingBt">
<tr>
	<td class="newAreaAlignLeft newAreaPaddingZ">
		<?php if(!$limitMsg){ ?><a href="javascript:;" onclick="javascript:openAddArea('area');"><?php } else { ?><a href="javascript:;" onclick="javascript:alert('주소지는 1,000개 까지 등록가능합니다.');"><?php } ?><img src="../img/btn_popup_plus.gif" border="0" alt="추가등록" /></a>
		<?php if(!$limitMsg){ ?><a href="javascript:;" onclick="javascript:openAddArea('csv');"><?php } else { ?><a href="javascript:;" onclick="javascript:alert('주소지는 1,000개 까지 등록가능합니다.');"><?php } ?><img src="../img/btn_popup_csv.gif" border="0" alt="엑셀CSV등록" /></a>
		<img src="../img/btn_area_popup.gif" border="0" id="newAreaSetting" class="newAreaCursorPointer" alt="기본지역정보 적용"/>
	</td>
	<td class="newAreaAlignRight" id="orderByTd">
		<img src="../img/sname_address.gif" alt="주소지" /><a href="javascript:sortFormSubmitType('sort', 'areaSido asc')"><img id="areaSido_asc" src="../img/list_up_off.gif" border="0" /></a><a href="javascript:sortFormSubmitType('sort','areaSido desc')"><img id="areaSido_desc" src="../img/list_down_off.gif" border="0" /></a>		
		&nbsp;
		<img src="../img/sname_date.gif" alt="등록일" /><a href="javascript:sortFormSubmitType('sort','areaRegdt asc')"><img id="areaRegdt_asc" src="../img/list_up_off.gif" border="0" /></a><a href="javascript:sortFormSubmitType('sort','areaRegdt desc')"><img id="areaRegdt_desc" src="../img/list_down_off.gif" border="0" /></a>
		&nbsp;
		<img src="../img/sname_delivery.gif" alt="추가배송비" /><a href="javascript:sortFormSubmitType('sort','areaPay asc')"><img id="areaPay_asc" src="../img/list_up_off.gif" border="0" /></a><a href="javascript:sortFormSubmitType('sort','areaPay desc')"><img id="areaPay_desc" src="../img/list_down_off.gif" border="0" /></a>
		&nbsp;
		<select name="page_num" onchange="javascript:this.form.submit();">
			<option value="10" <?php echo $selected[10]; ?>>10개 출력</option>
			<option value="20" <?php echo $selected[20]; ?>>20개 출력</option>
			<option value="40" <?php echo $selected[40]; ?>>40개 출력</option>
			<option value="60" <?php echo $selected[60]; ?>>60개 출력</option>
			<option value="100" <?php echo $selected[100]; ?>>100개 출력</option>
		</select>
	</td>
</tr>
</table>
</form>

<form name="newAreaForm" id="newAreaForm" method="POST" action="popup.newAreaDeliveryIndb.php" onsubmit="return chkForm(this);">
<input type="hidden" name="type" value="modify" />
<input type="hidden" name="returnUrl" value="<?php echo http_build_query($_GET); ?>">
<table cellpadding="0" cellspacing="1" width="100%" border="0" class="newAreaBgColorGray1" summary="지역별 배송비 리스트" id="newAreaDeliveryTable">
<colgroup>
	<col width="35px" />
	<col width="35px" />
	<col width="*" />
	<col width="170px" />
</colgroup>
<tr class="newAreaBgColorGray2 newAreaHeight15 newAreaAlignCenter">
	<td class="newAreaPaddingZ"><a href="javascript:;" onclick="chkBox(document.getElementsByName('newAreaChk[]'),'rev');" />선택</a></td>
	<td class="newAreaPaddingZ">번호</td>
	<td>주소지</td>
	<td>추가배송비<img src="../img/icons/bullet_compulsory.gif" border="0" style="vertical-align:bottom;"/></td>
</tr>
<?php
while($newArea = $db->fetch($result)){
	$areaName = @trim($newArea['areaSido'] . ' ' . $newArea['areaGugun'] . ' ' . $newArea['areaEtc']);
?>
<tr class="newAreaBgColorWhite">
	<td class="newAreaPaddingZ newAreaAlignCenter"><input type="checkbox" name="newAreaChk[]" class="newAreaBorder0" value="<?php echo $newArea['areaNo']; ?>" /></td>
	<td class="newAreaPaddingZ newAreaAlignCenter"><?php echo $pg->idx--; ?></td>
	<td>
		<?php echo $areaName; ?>
		<input type="hidden" name="newAreaNo[]" value="<?php echo $newArea['areaNo']; ?>" />
		<input type="hidden" name="newAreaName[]" value="<?php echo $areaName; ?>" />
	</td>
	<td><input type="text" name="newAreaPay[]" value="<?php echo $newArea['areaPay']; ?>" onkeydown="javascript:checkNumber(event);" onkeyup="javascript:checkNumber(event);" class="newAreaInputText" label="추가배송비" tabindex="1" /></td>
</tr>
<?php } ?>
</table>

<table cellpadding="0" cellspacing="0" width="100%" border="0">
<tr>
	<td class="newAreaPaddingZ">
		<table cellpadding="0" cellspacing="0" width="100%" border="0" class="newAreaPaddingTp5">
		<tr>
			<td class="newAreaPaddingL16"><img src="../img/btn_select_delete_dot.gif" class="newAreaCursorPointer" onclick="javascript:delRow();" /></td>
			<td class="newAreaAlignRight">
				<input type="text" name="_searchText" id="_searchText" class="newAreaVerticalBottom newAreaHeight20 newAreaPaddingL3" value="<?php echo $_GET['searchText'];?>" onkeydown="javascript:searchTabIndex(event);" onkeyup="javascript:searchTabIndex(event);" />
				<img src="../img/btn_search2.gif" id="searchBtn" class="newAreaVerticalBottom newAreaCursorPointer newAreaBorder0" />
			</td>
		</tr>
		</table>
	</td>
</tr>
<tr>
	<td class="newAreaPaddingZ"><div align="center" class="pageNavi"><font class="ver9"><?php echo $pg->page['navi']; ?></font></div></td>
</tr>
<tr>
	<td class="newAreaPaddingZ">
		<div id="MSG01">
			<table cellpadding=1 cellspacing=0 border=0 class=small_ex>
			<tr>
				<td><img src="../img/icon_list.gif" align="absmiddle" /><strong>기본주소지목록 적용이란?</strong> 일반적으로 많이 사용되는 추가배송비 적용 지역에 대한 기본 목록을 제공합니다.<div class="newAreaPaddingL9">이는 지역별 추가배송비의 최초 설정 시 주소지 등록에 대한 번거로움을 줄이기 위해 제공하는 기능이므로 반드시 계약하신 택배사(대리점)에 지역별 추가배송비 정보를 받으신 후 기본주소지를 추가/삭제하여 추가배송비를 입력해 주시기 바랍니다.</td>
				</tr>
			<tr>
				<td><img src="../img/icon_list.gif" align="absmiddle" /> 도로명/지번 주소 구분 없이 <strong>1,000</strong>개 까지 등록 가능합니다.</td>
			</tr>
			</table>
		</div>
	</td>
</tr>
<tr>
	<td class="newAreaHeight55"><div class="newAreaBar newAreaAlignCenter"><input type='image' src='../img/btn_allsave.gif' class='newAreaBorder0' />&nbsp;&nbsp;<a href='javascript:parent.addNewAreaDeliveryClose()' class='white'><img src='../img/btn_cancel.gif' class='newAreaBorder0' /></a></div></td>
</tr>
</table>
</form>

<script>
cssRound('MSG01');
</script>

</body>
</html>