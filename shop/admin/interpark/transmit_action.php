<?

if (!$_POST['returnUrl'] && $_GET['returnUrl']) $_POST['returnUrl'] = $_GET['returnUrl'];
if (!$_POST['returnUrl']) $_POST['returnUrl'] = $_SERVER[HTTP_REFERER];

$popup = preg_match('/popup(\.)?[^.]*\.php/i', $_POST['returnUrl'], $matches);
$location = "��ǰ���� > ������ũ ��ǰ����";
$scriptLoad = '<script src="../interpark/js/transmit_action.js"></script>';
if ($popup) include "../_header.popup.php";
else include "../_header.php";

### ��������
$num = 10;

if ($_POST['isall'] == 'Y' && $_POST['query']){
	$_POST['query'] = stripslashes($_POST['query']);
	$res = $db->query($_POST['query']);
	$total = ceil($db->count_($res) / $num);
}
else {
	unset($_POST['query']);
	$goodsno = array();
	$goodsno = array_merge((array)$_GET['goodsno'],(array)$_POST['goodsno'],(array)$_POST['chk']);
	$property = array();
	for ($i = 0; $i < count($goodsno); $i+=$num){
		$property[] = count($property) . ':"' . implode(":", array_slice($goodsno, $i, $num)) . '"';
	}
	$total = count($property);
	$sectionObj = '({' . implode(',', $property) . '})';
}

?>

<div class="title title_top">������ũ ��ǰ����<span>������ ����Ͻ� ��ǰ�� ������ũ���� �����մϴ�.</div>

<div class=title2>&nbsp;&nbsp;&nbsp;<img src="../img/icon_process.gif" align=absmiddle><font class=def1 color=0074BA><b>���� ���</b></font> <font class=small1 color=6d6d6d>(������ũ�� ������ ��û�� ��ǰ�����Դϴ�.)</font></div>
<div style="padding-top:10px;"></div>
<table width=100% cellpadding=4 border=1 bordercolor="#EBEBEB" style="border-collapse:collapse;" id="result">
<col width=50><col width=100><col width=130><col width=300>
<tr bgcolor="#eeeeee">
	<th bgcolor=F4F4F4><font color=444444>No.</font></th>
	<th bgcolor=F4F4F4><font color=444444>������ȣ</font></th>
	<th bgcolor=F4F4F4><font color=444444>������ũ ��ǰ��ȣ</font></th>
	<th bgcolor=F4F4F4><font color=444444>��ǰ��</font></th>
	<th bgcolor=F4F4F4><font color=444444>���</font></th>
</tr>
</table>

<? if ($_POST[returnUrl]){ ?>
<div style="margin:10px; text-align:center;">
<a href="<?=$_POST[returnUrl]?>"><img src="../img/btn_confirm.gif"></a>
</div>
<? } ?>


<script>
ITG.sections = eval( '<?=$sectionObj?>' );
ITG.query = "<?=str_replace("\r\n", "", $_POST['query'])?>";
ITG.total = <?=sprintf("%0d",$total)?>;
ITG.send();
</script>


<?

if ($popup) echo "<script>table_design_load();</script>";
else include "../_footer.php";

?>
