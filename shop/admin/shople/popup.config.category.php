<?

$scriptLoad='<link rel="styleSheet" href="./_inc/style.css">';
include "../_header.popup.php";
require_once ('./_inc/config.inc.php');

// depth 1은 가져옴
$query = "SELECT * FROM ".GD_SHOPLE_CATEGORY." WHERE depth = 1 ORDER BY dispno";
$rs = $db->query($query);

$category = array();
while($row = $db->fetch($rs,1)) {
	$category[] = $row;
}

?>

<script language="javascript">
<!--
var category = '<?=$_REQUEST['category']?>';
var rowIdx = '<?=$_REQUEST['rowIdx']?>';
var idnm = '<?=$_REQUEST['idnm']?>';
-->
</script>

<div class="title title_top" style="margin-top:10px;">표준 카테고리 연결<span>쇼플 표준 카테고리를 찾아 연결합니다.</span></div>

<form onsubmit="return nsShople.category.search();">
<table class="tb" style="margin-bottom:10px;">
<col class="cellC"><col class="cellL">
<tr>
	<td>찾을 카테고리</td>
	<td>
	<input type="text" name="srchName" id="srchName" value="" required label="검색어" msgR="검색어를 입력하셔야 합니다.">
	<input type="image" src="../img/btn_search_s.gif" align="absmiddle" hspace="10" class="null">
	</td>
</tr>
</table>
</form>

<table width="100%" cellPadding="2" cellSpacing="1" border="1" borderColor="#EBEBEB" cellpadding="0" cellspacing="3" style="border-collapse: collapse;">
<col width="80"><col width="">
<tr height="21" bgcolor="#F6F6F6">
	<td align="center">선택</td>
	<td style="padding-left:210px">검색결과</td>
</tr>
</table>

<ul id="srchCatePrint">
	<li class="notice">연결할 카테고리를 선택하고 적용하세요.</li>
</ul>

<div id="stepCate">
	<ul>
		<h4>대분류</h4>
		<select id="el-shople-category-1" class="el-shople-category" size=10>
		<option value="null">대분류 선택</option>
		<? foreach ($category as $cate) { ?>
		<option value="<?=$cate['dispno']?>"><?=$cate['name']?></option>
		<? } ?>
		</select>
	</ul>
	<ul class="separator">▶</ul>
	<ul>
		<h4>중분류</h4>
		<select id="el-shople-category-2" class="el-shople-category" size=10>
		<option value="null">중분류 선택</option>
		</select>
	</ul>
	<ul class="separator">▶</ul>
	<ul>
		<h4>소분류</h4>
		<select id="el-shople-category-3" class="el-shople-category" size=10>
		<option value="null">소분류 선택</option>
		</select>
	</ul>
	<ul class="separator">▶</ul>
	<ul>
		<h4>세분류</h4>
		<select id="el-shople-category-4" class="el-shople-category" size=10>
		<option value="null">세분류 선택</option>
		</select>
	</ul>
</div>

<div style="clear:both; margin-top:10px;"><label for="samelow"><input type="checkbox" id="samelow"> <font color="#3A870C">하위분류에도 동일하게 저장합니다.</font></label></div>

<div style="margin:10px 0; text-align:center;"><a href="javascript:;" onclick="nsShople.category.apply();"><img src="../img/btn_openmarket_select_save.gif" alt="선택한 분류로 매칭적용"></a></div>

<script type="text/javascript" src="./_inc/common.js"></script>
<script type="text/javascript">
function fnSetCategory() { nsShople.category.init('<?=$_GET['full_dispno']?>'); }
Event.observe(document, 'dom:loaded', fnSetCategory, false);
</script>

<script type="text/javascript">table_design_load();</script>
</body>
</html>