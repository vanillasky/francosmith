<?

// 2010-04-04

$location = "����ϼ����� > ���θ� ���� ���������� ����";
include "../_header.php";

/**
 * �����ΰ��� ���÷� ������ xml ����
 *	<item>
 *	<title> ���� </title> 
 *	<description> ������, ����Ÿ��Ʋ, ���ܼ���, ���� ���� .. </description> <-- �� �κп��� �������� ���� �����ϳ� , ���� ���� �����͸� ����.
 *	<link> ����� ������ </link>
 *	<media:thumbnail width="������̹������λ�����" height="������̹������λ�����" url="������̹����ּ�"/>
 *	</item>
**/

$load_config_shoppingApp = $config->load('shoppingApp');

$data_apppremium = unserialize($load_config_shoppingApp['app_premium']);

?>
<script>
	function add_div(){

		var cntTable = document.f_app_list.getElementsByTagName('table').length;		

		if( cntTable >= 50 ){ alert("���������ǿ� ���� �� �ִ� ������ 50���� ���� �� �����ϴ�."); return false;}

		var sethtml = "";

		sethtml = "<div>";
		sethtml += "<table class=tb>";
		sethtml += "<col class=cellC><col class=cellL>";
		sethtml += "<tr>";
		sethtml += "<td>����</td>";
		sethtml += "<td><input style=\"width:80%;\" type=\"text\" name=\"title[]\" label=\"Ÿ��Ʋ\" value=\"\" maxlen=\"40\"><font class=extext> �ִ���� - �ѱ۱���(20��)</font></td>";
		sethtml += "</tr>";
		sethtml += "<tr>";
		sethtml += "<td>���ܼ���</td>";
		sethtml += "<td><input style=\"width:80%;\" type=\"text\" name=\"description[]\" label=\"���ܼ���\" value=\"\" maxlen=\"40\"><font class=extext> �ִ���� - �ѱ۱���(20��)</font></td>";
		sethtml += "</tr>";
		sethtml += "<tr>";
		sethtml += "<td>����� ������ ����</td>";
		sethtml += "<td><input style=\"width:80%;\" type=\"text\" name=\"link[]\" value=\"\"><br/>";
		sethtml += "<div style=\"padding-top:4px\"><font class=extext>���� : http://www.godo.co.kr/ </font></div>";
		sethtml += "</td>";
		sethtml += "</tr>";
		sethtml += "<tr>";
		sethtml += "<td>����� �̹���</td>";
		sethtml += "<td><input type=\"file\" name=\"thumbnail[]\"></td>";
		sethtml += "</tr>";
		sethtml += "</table>";
		sethtml += "<div align=\"right\"><img src=\"../img/btn_delete.gif\" onclick=\"javascript:del_div(this);\" value=\"����\"></div><p/>";
		sethtml += "</div>";

		document.getElementById('app_list').innerHTML += sethtml;

		table_design_load();
	}

	function del_div(div){
		div.parentNode.parentNode.removeNode(true);
	}
</script>
<div class="title title_top">���θ� ���� ���������� ���� <a href="javascript:manual('<?=$guideUrl?>board/view.php?id=mobileshop&no=10')"><img src="../img/btn_q.gif" border=0 align=absmiddle hspace=2></a></div>
<form name="f_app_list" method="post" action="indb.php" enctype="multipart/form-data" onsubmit="return chkForm(this);">
<input type="hidden" name="mode" value="AppPremium">

<? if( !$data_apppremium ) {	 // �����Ͱ� ���� ��� �⺻ ��� ?>
<div>
	<table class=tb>
	<col class=cellC><col class=cellL>
		<tr>	
			<td>����</td>
			<td><input style="width:80%;" type="text" name="title[]" label="Ÿ��Ʋ" value="" maxlen="50" required><font class=extext> �ִ���� - �ѱ۱���(20��)</font></td>
		</tr>
		<tr>	
			<td>���ܼ���</td>
			<td><input style="width:80%;" type="text" name="description[]" label="���ܼ���" value="" maxlen="50"><font class=extext> �ִ���� - �ѱ۱���(20��)</font></td>
		</tr>
		<tr>	
			<td>����� ������ ����</td>
			<td><input style="width:80%;" type="text" name="link[]" value=""><br/>
				<div style="padding-top:4px"><font class=extext>���� : http://www.godo.co.kr/ </font></div>
			</td>
		</tr>
		<tr>	
			<td>����� �̹���</td>
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
			<td>����</td>
			<td><input style="width:80%;" type="text" name="title[]" label="Ÿ��Ʋ" value="<?=$v['title']?>" maxlen="50" required><font class=extext> �ִ���� - �ѱ۱���(20��)</font></td>
		</tr>
		<tr>	
			<td>���ܼ���</td>
			<td><input style="width:80%;" type="text" name="description[]" label="���ܼ���" value="<?=$v['description']?>" maxlen="50"><font class=extext> �ִ���� - �ѱ۱���(20��)</font></td>
		</tr>
		<tr>	
			<td>����� ������ ����</td>
			<td><input style="width:80%;" type="text" name="link[]" value="<?=$v['link']?>"><br/>
				<div style="padding-top:4px"><font class=extext>���� : http://www.godo.co.kr/ </font></div>
			</td>
		</tr>
		<tr class="noline">	
			<td>����� �̹���</td>
			<td><input type="file" name="thumbnail[]">
			<? if($v['thumbnail'] && file_exists("../../data/m/app/".$v['thumbnail']) ){
					echo "<img src='../../data/m/app/".$v['thumbnail']."' width='50px' height='50px'>";
					echo "&nbsp;<input type=\"checkbox\" name=\"del_file[]\" value=\"".$k."\">����";
			}?>
			</td>
		</tr>
	</table>
	<div align="right"><img src="../img/btn_delete.gif" onclick="javascript:del_div(this);" value="����"></div>
	<p/>	
</div>

<? }	} ?>

<div id="app_list"></div>

<div align="right"><img src="../img/btn_product_write.gif" onclick="javascript:add_div();"></div>

<div class=button_top><input type=image src="../img/btn_save.gif"></div>

</form>
<? include "../_footer.php"; ?>