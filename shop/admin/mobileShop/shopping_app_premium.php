<?

// 2010-04-04

$location = "모바일샵관리 > 쇼핑몰 어플 자유주제탭 설정";
include "../_header.php";

/**
 * 디자인공장 어플로 보내는 xml 형식
 *	<item>
 *	<title> 제목 </title> 
 *	<description> 부제목, 서브타이틀, 간단설명, 한줄 설명 .. </description> <-- 이 부분에는 여러가지 값이 가능하나 , 간단 설명 데이터를 보냄.
 *	<link> 연결될 페이지 </link>
 *	<media:thumbnail width="썸네일이미지가로사이즈" height="썸네일이미지세로사이즈" url="썸네일이미지주소"/>
 *	</item>
**/

$load_config_shoppingApp = $config->load('shoppingApp');

$data_apppremium = unserialize($load_config_shoppingApp['app_premium']);

?>
<script>
	function add_div(){

		var cntTable = document.f_app_list.getElementsByTagName('table').length;		

		if( cntTable >= 50 ){ alert("자유주제탭에 넣을 수 있는 내용은 50개를 넘을 수 없습니다."); return false;}

		var sethtml = "";

		sethtml = "<div>";
		sethtml += "<table class=tb>";
		sethtml += "<col class=cellC><col class=cellL>";
		sethtml += "<tr>";
		sethtml += "<td>제목</td>";
		sethtml += "<td><input style=\"width:80%;\" type=\"text\" name=\"title[]\" label=\"타이틀\" value=\"\" maxlen=\"40\"><font class=extext> 최대길이 - 한글기준(20자)</font></td>";
		sethtml += "</tr>";
		sethtml += "<tr>";
		sethtml += "<td>간단설명</td>";
		sethtml += "<td><input style=\"width:80%;\" type=\"text\" name=\"description[]\" label=\"간단설명\" value=\"\" maxlen=\"40\"><font class=extext> 최대길이 - 한글기준(20자)</font></td>";
		sethtml += "</tr>";
		sethtml += "<tr>";
		sethtml += "<td>연결될 페이지 설정</td>";
		sethtml += "<td><input style=\"width:80%;\" type=\"text\" name=\"link[]\" value=\"\"><br/>";
		sethtml += "<div style=\"padding-top:4px\"><font class=extext>예시 : http://www.godo.co.kr/ </font></div>";
		sethtml += "</td>";
		sethtml += "</tr>";
		sethtml += "<tr>";
		sethtml += "<td>썸네일 이미지</td>";
		sethtml += "<td><input type=\"file\" name=\"thumbnail[]\"></td>";
		sethtml += "</tr>";
		sethtml += "</table>";
		sethtml += "<div align=\"right\"><img src=\"../img/btn_delete.gif\" onclick=\"javascript:del_div(this);\" value=\"삭제\"></div><p/>";
		sethtml += "</div>";

		document.getElementById('app_list').innerHTML += sethtml;

		table_design_load();
	}

	function del_div(div){
		div.parentNode.parentNode.removeNode(true);
	}
</script>
<div class="title title_top">쇼핑몰 어플 자유주제탭 설정 <a href="javascript:manual('<?=$guideUrl?>board/view.php?id=mobileshop&no=10')"><img src="../img/btn_q.gif" border=0 align=absmiddle hspace=2></a></div>
<form name="f_app_list" method="post" action="indb.php" enctype="multipart/form-data" onsubmit="return chkForm(this);">
<input type="hidden" name="mode" value="AppPremium">

<? if( !$data_apppremium ) {	 // 데이터가 없을 경우 기본 출력 ?>
<div>
	<table class=tb>
	<col class=cellC><col class=cellL>
		<tr>	
			<td>제목</td>
			<td><input style="width:80%;" type="text" name="title[]" label="타이틀" value="" maxlen="50" required><font class=extext> 최대길이 - 한글기준(20자)</font></td>
		</tr>
		<tr>	
			<td>간단설명</td>
			<td><input style="width:80%;" type="text" name="description[]" label="간단설명" value="" maxlen="50"><font class=extext> 최대길이 - 한글기준(20자)</font></td>
		</tr>
		<tr>	
			<td>연결될 페이지 설정</td>
			<td><input style="width:80%;" type="text" name="link[]" value=""><br/>
				<div style="padding-top:4px"><font class=extext>예시 : http://www.godo.co.kr/ </font></div>
			</td>
		</tr>
		<tr>	
			<td>썸네일 이미지</td>
			<td><input type="file" name="thumbnail[]"></td>
		</tr>
	</table><p/>
</div>

<? }else{ 
		foreach( $data_apppremium as $k=>$v ){
?>

<div>
	<input type="hidden" name="filename[]" value="<?=$v['thumbnail']?>">
	<table class=tb>
	<col class=cellC><col class=cellL>
		<tr>	
			<td>제목</td>
			<td><input style="width:80%;" type="text" name="title[]" label="타이틀" value="<?=$v['title']?>" maxlen="50" required><font class=extext> 최대길이 - 한글기준(20자)</font></td>
		</tr>
		<tr>	
			<td>간단설명</td>
			<td><input style="width:80%;" type="text" name="description[]" label="간단설명" value="<?=$v['description']?>" maxlen="50"><font class=extext> 최대길이 - 한글기준(20자)</font></td>
		</tr>
		<tr>	
			<td>연결될 페이지 설정</td>
			<td><input style="width:80%;" type="text" name="link[]" value="<?=$v['link']?>"><br/>
				<div style="padding-top:4px"><font class=extext>예시 : http://www.godo.co.kr/ </font></div>
			</td>
		</tr>
		<tr class="noline">	
			<td>썸네일 이미지</td>
			<td><input type="file" name="thumbnail[]">
			<? if($v['thumbnail'] && file_exists("../../data/m/app/".$v['thumbnail']) ){
					echo "<img src='../../data/m/app/".$v['thumbnail']."' width='50px' height='50px'>";
					echo "&nbsp;<input type=\"checkbox\" name=\"del_file[]\" value=\"".$k."\">삭제";
			}?>
			</td>
		</tr>
	</table>
	<div align="right"><img src="../img/btn_delete.gif" onclick="javascript:del_div(this);" value="삭제"></div>
	<p/>	
</div>

<? }	} ?>

<div id="app_list"></div>

<div align="right"><img src="../img/btn_product_write.gif" onclick="javascript:add_div();"></div>

<div class=button_top><input type=image src="../img/btn_save.gif"></div>

</form>
<? include "../_footer.php"; ?>