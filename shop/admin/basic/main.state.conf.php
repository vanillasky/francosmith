<?
include "../_header.popup.php";
require_once("../../lib/qfile.class.php");
$qfile = new qfile();

include "./main.state.array.php";
@include "../../conf/admin_main_state.php";

	if($_POST['mode'] == "mainConf"){
		$qfile->open("../../conf/admin_main_state.php");
		$qfile->write("<?\n" );
		foreach ( (array)$_POST['main'] as $mKey => $mVal ){
			$qfile->write("\$adminMainState['" . $mKey . "']['chk'] = \"".$mVal."\"; \n" );
		}
		$qfile->write("?>" );
		$qfile->close();
		@chMod( "../../conf/admin_main_state.php", 0757 );
		echo "
		<script>
		parent.NowMainDisplay.inData();
		parent.closeLayer();
		</script>";
	}

?>

<div class="title title_top">�ֿ���Ȳ ����׸� ���� <font class=extext>������ ����ȭ�鿡 �������� �ֿ���Ȳ �׸��� �����ϼ���</div>


<div style="padding-top:10px"></div>

<form method="post" action="" onsubmit="return chkForm(this);">
<input type="hidden" name="mode" value="mainConf" />
<table width="100%" cellpadding="0" cellspacing="0" border="0" bgcolor="ebebeb"><tr><td bgcolor="e8e8e8">
<table width="100%" cellpadding="2" cellspacing="1" border="0" bgcolor="e8e8e8">
<?
	$i = 0;
	foreach($adminMainState AS $mKey => $mVal){
		if($i == 0 || $i % 2 == 0) echo "<tr>";
?>
	<td bgcolor="f6f6f6" width="30%" align="left" style="padding:3px 0px 0px 10px"><font class=small1 color=666666><?=$mVal['title']?></font></td>
	<td bgcolor="ffffff" width="20%" class="noline"><input type="checkbox" name="main[<?=$mKey?>]" value="on" <? if($mVal['chk'] == "on") echo" checked"; ?> /> <font class=small1 color=666666>���ο� ���</font></td>
<?
		$i++;
		if($i % 2 == 0) echo "</tr>";
	}
?>
</table>
</td></tr></table>

<div style="padding-top:10px"></div>

<div style="margin-bottom:10px;padding-top:10px;" class=noline align="center">
<input type="image" src="../img/btn_confirm_s.gif">
</form>

<div style="padding-top:10px;text-align:left;">
<font class="extext">* ������� �Ա�Ȯ���� �������� ��������, �Ա�Ȯ��,����غ���,�����,��ۿϷ� �ǿ� ���� ǥ���մϴ�.</font><br />
<font class="extext">* �ֹ��Ǽ��� �ֹ����� �������� ��������, ��� �ֹ��Ǽ��� ǥ�ø� �մϴ�.</font><br />
<font class="extext">* �Ա�Ȯ���� �Ա�Ȯ���� �������� ��������, �Ա�Ȯ�� �ǿ� ���ؼ��� ǥ���մϴ�.</font><br />
<font class="extext">* ��ۿϷ�� ��ۿϷ��� �������� ��������, ��ۿϷ� �ǿ� ���ؼ��� ǥ���մϴ�.</font><br />
<font class="extext">* ���/ȯ��/��ǰ�� �Ա�Ȯ���� �������� ��������, ���/ȯ��/��ǰ �ǿ� ���ؼ��� ǥ���մϴ�.</font><br />
<font class="red">* �ֹ����� ���� ��� ����� �׸��� ����ϰ� �Ǹ� ȭ��ε��� ������ �� �ֽ��ϴ�.</font><br />
</div>