<?

$scriptLoad='<link rel="styleSheet" href="./js/style.css">';
$scriptLoad.='<script src="./js/common.js"></script>';
include "../_header.popup.php";

?>

<script language="javascript"><!--
var category = '<?=$_REQUEST['category']?>';
var rowIdx = '<?=$_REQUEST['rowIdx']?>';
var idnm = '<?=$_REQUEST['idnm']?>';
--></script>

<div class="title title_top" style="margin-top:10px;">분류매칭하기<span>매칭할 오픈마켓 표준분류를 찾아 연결합니다.</span></div>

<form onsubmit="return callSrch();">
<table class="tb" style="margin-bottom:10px;">
<col class="cellC"><col class="cellL">
<tr>
	<td>찾을 분류명</td>
	<td>
	<input type="text" name="srchName" id="srchName" value="" required label="검색어" msgR="검색어를 입력하셔야 합니다.">
	<input type="image" src="../img/btn_search_s.gif" align="absmiddle" hspace="10" class="null">
	</td>
</tr>
</table>
</form>

<table width="100%" cellPadding="2" cellSpacing="1" border="1" borderColor="#EBEBEB" cellpadding="0" cellspacing="3" style="border-collapse: collapse;">
<col width="10%"><col width="90%">
<tr height="21" bgcolor="#F6F6F6">
	<td align="center">선택</td>
	<td style="padding-left:210px">검색결과</td>
</tr>
</table>

<ul id="srchCatePrint">
<table width="100%" cellPadding="2" cellSpacing="1" border="1" borderColor="#EBEBEB" cellpadding="0" cellspacing="3" style="border-collapse: collapse; border-width:0;">
<col width="10%"><col width="90%">
<tr height="130" bgcolor="#FFFFFF">
	<td colspan="2" align="center"><font color="#6d6d6d">매칭할 분류를 선택하고 매칭저장하세요.</font></td>
</tr>
</table>
</ul>

<div id="stepCate">
	<ul>
		<h4>1차 분류</h4>
		<div id="cat_div1"></div>
	</ul>
	<ul class="separator">▶</ul>
	<ul>
		<h4>2차 분류</h4>
		<div id="cat_div2"></div>
	</ul>
	<ul class="separator">▶</ul>
	<ul>
		<h4>3차 분류</h4>
		<div id="cat_div3"></div>
	</ul>
	<ul class="separator">▶</ul>
	<ul>
		<h4>4차 분류</h4>
		<div id="cat_div4"></div>
	</ul>
</div>

<div style="clear:both; margin-top:10px;"><label for="samelow"><input type="checkbox" id="samelow"> <font color="#3A870C">하위분류에도 동일하게 저장합니다.</font></label></div>

<div style="margin:10px 0; text-align:center;"><a href="javascript:;" onclick="callApply()"><img src="../img/btn_openmarket_select_save.gif" alt="선택한 분류로 매칭적용"></a></div>

<script>callStepCate('<?=$_GET['defaultOpt']?>');</script>
<script>table_design_load();</script>
</body>
</html>