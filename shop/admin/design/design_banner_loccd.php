<?
/*------------------------------------------------------------------------------
�� Copyright 2005, Flyfox All right reserved.
@���ϳ���: �ΰ�/��� ���� ����
@��������/������/������:
------------------------------------------------------------------------------*/
include "../_header.popup.php";


if ( !$_GET['mode'] ) $_GET['mode'] = "modify_loccd";

switch ( $_GET['mode'] ){
	case "modify_loccd":
		if ( file_exists( $tmp = dirname(__FILE__) . "/../../conf/config.banner_".$cfg['tplSkinWork'].".php" ) ) @include $tmp;
		else @include dirname(__FILE__) . "/../../conf/config.banner.php";

		if(!$b_loccd['90']) $b_loccd['90']	= "���ηΰ�";
		if(!$b_loccd['91']) $b_loccd['91']	= "�ϴܷΰ�";
		if(!$b_loccd['92']) $b_loccd['92']	= "���Ϸΰ�";
		if(!$b_loccd['93']) $b_loccd['93']	= "�ΰ���ġ�Է�";
		if(!$b_loccd['94']) $b_loccd['94']	= "�ΰ���ġ�Է�";
		if(!$b_loccd['95']) $b_loccd['95']	= "�ΰ���ġ�Է�";

		break;
}

$tmps = array_fill(1, 200, 0);
for ( $i = 90; $i <= 95; $i++ ){
	unset($tmps[$i]);
}
$nums = array_keys($tmps);
$half = count($nums) / 2;
?>


<form name="fm" method=post action="design_banner_indb.php" onsubmit="return chkForm(this)">
<input type=hidden name=mode value="<?=$_GET['mode']?>">

<div class="title title_top">�ΰ�/�����ġ����<span>����� �ΰ�/��ʵ鿡 �µ��� �̸� �ΰ�/�����ġ�� �Է��س�������. ��) ���λ��ū���, �����߾ӹ��1, �����͹��4 -> �䷸�� ��������. </span><a href="javascript:manual('<?=$guideUrl?>board/view.php?id=design&no=6')"><img src="../img/btn_q.gif" border=0 align=absmiddle hspace=2></a></div>
<?=$workSkinStr?>
<table class=tb style="float:left;">
<col class=cellC><col class=cellL><col class=cellC><col class=cellL>
<? for ( $i = 90; $i <= 92; $i++ ){ $j = $i + 3; ?>
<tr>
	<td>�ΰ��ڵ� <?=$i?></td>
	<td><input type=text name="loccd[<?=$i?>]" value="<?=( $b_loccd[$i] ? $b_loccd[$i] : '�ΰ���ġ�Է�' );?>" class="line" style="width:200;"></td>
	<td>�ΰ��ڵ� <?=$j?></td>
	<td><input type=text name="loccd[<?=$j?>]" value="<?=( $b_loccd[$j] ? $b_loccd[$j] : '�ΰ���ġ�Է�' );?>" class="line" style="width:200;"></td>
</tr>
<? } ?>
<? for ( $k = 0; $k < $half; $k++ ){ $i = $nums[$k]; $j = $nums[$k + $half]; ?>
<tr>
	<td>����ڵ� <?=$i?></td>
	<td><input type=text name="loccd[<?=$i?>]" value="<?=( $b_loccd[$i] ? $b_loccd[$i] : '�����ġ�Է�' );?>" class="line" style="width:200;"></td>
	<td>����ڵ� <?=$j?></td>
	<td><input type=text name="loccd[<?=$j?>]" value="<?=( $b_loccd[$j] ? $b_loccd[$j] : '�����ġ�Է�' );?>" class="line" style="width:200;"></td>
</tr>
<? } ?>
</table>

<div style="padding:20px" align=center class=noline>
<input type=image src="../img/btn_register.gif">
</div>

</form>


<script>
table_design_load();
</script>