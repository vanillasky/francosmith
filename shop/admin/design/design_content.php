<?

$location = "�⺻���� > ���� ������ ����";
$scriptLoad='<script src="../design/codi/_codi.js"></script>';
include "../_header.php";


if ( !$_GET['mode'] ) $_GET['mode'] = "modify";


@include_once dirname(__FILE__) . "/codi/code.class.php";
$codi = new codi;
$data_file = $codi->get_fileinfo( $_GET['design_file'] ); # File Data
$data_file['real_linkurl'] = '../../' . $data_file['linkurl'];
?>


<form method=post action="design_content_indb.php?design_file=<?=$_GET['design_file']?>" onsubmit="return chkForm(this)">
<input type=hidden name=mode value="<?=$_GET['mode']?>">

<div class="title title_top" style="margin-bottom:0;">���� ������ ���� <font class=small1 color=444444><font color=FF00C0>easy��Ų</font>���� �ش�Ǵ� ����Դϴ�. �ݵ�� �����α⺻�������� <font color=FF00C0>easy��Ų�� ����</font>�Ͻð� �����۾��� �����ϼ���.</font></div>

<!-- �׺���̼� : ���� -->
<table width=100% cellpadding=0 cellspacing=0 border=0 style="margin-bottom:8px;">
<tr>
	<td bgcolor="#EEEEEE" style="padding-left:20">
	<table cellpadding=5 cellspacing=0 border=0>
	<tr>
		<td><a href="./design_content.php?design_file=service/company.htm"><img src="../images/freede_contents_s01.gif" border=0></a></td>
		<td><a href="./design_content.php?design_file=service/customer.htm"><img src="../images/freede_contents_s02.gif" border=0></a></td>
		<td><a href="./design_content.php?design_file=service/guide.htm"><img src="../images/freede_contents_s03.gif" border=0></a></td>
		<td><a href="./design_content.php?design_file=service/private.htm"><img src="../images/freede_contents_s04.gif" border=0></a></td>
		<td><a href="./design_content.php?design_file=proc/_agreement.txt"><img src="../images/freede_contents_s05.gif" border=0></a></td>
		<td><a href="./design_content.php?design_file=proc/_goods_guide.htm"><img src="../images/freede_contents_s06.gif" border=0></a></td>
	</tr>
	</table>
	</td>
</tr>
<tr><td height=3 bgcolor="#CCCCCC"></td></tr>
</table>
<!-- �׺���̼� : �� -->

<table width=758 cellpadding=0 cellspacing=0 border=0>
<tr><td align=right><div style="font:8pt ����"><?=$data_file[text]?>_<?=$data_file[linkurl]?><A HREF="<?=$data_file[real_linkurl]?>" target="_blank"><img src="../images/freede_btn_view.gif" align=absmiddle border=0></A></div></td></tr></table>


<div style="padding-top:8px"></div>

<table width="100%" border=0 cellpadding=0 cellspacing=0 align=center>
	<tr valign="top">
		<td>

<?
{ // �������ڵ���

	$tmp = array();
	$tmp['t_name']		= 'content';
	$tmp['t_width']		= '100%';
	$tmp['t_rows']		= 20;
	$tmp['t_property']	= ' required label="HTML �ҽ�"';
	$tmp['tplFile']		= "/" . $_GET['design_file'];

	echo "<script>DCTM.write('{$tmp['t_name']}', '{$tmp['t_width']}', '{$tmp['t_rows']}', '{$tmp['t_property']}', '{$tmp['tplFile']}');</script>";
}
?>
          </td>
      </tr>
</table>



<div style="padding:20px" align=center class=noline>
<input type=image src="../img/btn_register.gif">
</div>

</form>



<div id=MSG01>
<table cellpadding=1 cellspacing=0 border=0 class=small_ex>
<tr><td><div><img src="../img/icon_list.gif" align="absmiddle">�� �������� ������ �����մϴ�.</div>
<div style="padding-top:3px"></div>
<div><img src="../img/icon_list.gif" align="absmiddle">easy��Ų���� �ش�Ǵ� ����Դϴ�. �ݵ�� �����α⺻�������� easy��Ų�� �����Ͻð� �����۾��� �����ϼ���.</font></div>
</td></tr></table
</div>
<script>cssRound('MSG01')</script>


<? include "../_footer.php"; ?>