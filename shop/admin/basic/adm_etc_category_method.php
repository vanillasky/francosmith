<?
$location = "��Ÿ���� > ��ǰ�з� ������ ��ȯ ";
include "../_header.php";

// ��ǰ�з� ������ ��ȯ ��� ��ǰ �� ��
list ($totalCount)		= $db->fetch("SELECT COUNT(0) FROM ".GD_GOODS);

// �ӽ� - ī�װ� ����/���ű����� ������ �Ѱ�� ���â�� ����
list ($cateLevelCheck)	= $db->fetch("SELECT COUNT(0) FROM ".GD_CATEGORY." WHERE level > 0");
?>
<script type="text/javascript" src="../godo.loading.indicator.js"></script>
<script type="text/javascript">
function changeCategoryMethod()
{
	var msg = "���ο� ��ǰ�з� ���������� ��ȯ�� �����մϴ�.\n\n��ǰ���� ���� ��� ��ȯ�ð��� �ټ� ����� �� ������\n\n������ �ֹ����� ���� �ð��� �̿��Ͽ� �ֽñ� �ٶ��ϴ�.\n\n��� ���� �Ͻðڽ��ϱ�?\n\n�� ��ȯ �Ŀ��� ������ ���·� �ǵ��� �� �����ϴ�.";
	if(confirm(msg)){
		popupLayerNotice('��ǰ�з� ������ ��ȯ','./adm_etc_category_method_popup.php?mode=categoryLink&totalCount=<?php echo $totalCount;?>',505,140);
		return true;
	}
	else {
		return false;
	}
}
</script>
<div class="title title_top">��ǰ�з� ������ ��ȯ <a href="javascript:manual('<?=$guideUrl?>board/view.php?id=basic&no=45')"><img src="../img/btn_q.gif" border="0" align="absmiddle"></a></div>
<div style="border:3px solid #000; padding:5px 5px 5px 15px; margin-bottom:10px;">
	<p style="color:#0099ff; font-weight:bold;">�� [��ǰ�з�(ī�װ�) ������ ���� �ȳ�]</p>
	<p style="color:#000000; font-weight:bold;">�� ������ : �����з�(ī�װ�) �м� ��ȸ ���</p>
	<p style="color:#999999;">
		��) 1�� �з� > 2�� �з� > 3�� �з�<br>
	</p>
	<p style="color:#000000; font-weight:bold; padding-top:10px">�� ������ : �����з�(ī�װ�) ���� ��� ��ȸ ���</p>
	<p style="color:#999999;">
		��)&nbsp;&nbsp;1�� �з�<br>
		&nbsp; &nbsp; &nbsp; &nbsp;1�� �з� > 2�� �з�<br>
		&nbsp; &nbsp; &nbsp; &nbsp;1�� �з� > 2�� �з� > 3�� �з�<br>
	</p>
	<p style="color:#000000; font-weight:bold; padding-top:10px">�� �̷����� �������ϴ�!</p>
	<p style="color:#999999;">
		 - ��ȸ �з� ���� ��� : �����з� ���� ����(����)�� �����ϹǷ� ��ǰ�� ǥ���ϰ��� �ϴ� �з����� ���������� �� �� �ֽ��ϴ�!<br>
		 - ��ǰ ��ȸ �ӵ� ��� : ��ǰ�з� �м� �ܰ谡 �����Ƿ� ��ǰ ��ȸ �� �ε� �ӵ��� ���������� �������ϴ�!<br>
		 - <span class="extext">Tip. <a href="./adm_etc_cache_db.php" class="extext_l">[������Ʈ �ӵ� ���]</a> ��ɰ� �Բ� ��� �� �뷮���� ��ǰ�� ����Ͽ��� �������� ������ �̿��Ͻ� �� �ֽ��ϴ�.</span>
	</p>
</div>

<div class="admin-form" style="height:300px; margin:0 auto;">

	<h2 class="title ">��ǰ�з� ������ ��ȯ ����<span>��ǰ�з� �������� ������ �˴ϴ�.</span></h2>

	<table class="admin-form-table">
	<tr>
		<th>��ȯ����</th>
		<td>
			<?php if (_CATEGORY_NEW_METHOD_ === true) { ?>
			<span style="font-weight:bold;color:#0080FF">
				<?php if ($godo['version'][0] >= '2.00.10.1120') { ?>
				�̹� ���ο� ��ǰ�з� ������ ��ȯ�� �Ϸ�� �����Դϴ�.
				<?php } else { ?>
				���ο� ��ǰ�з� ���������� ��ȯ�Ͽ� ��� �� �Դϴ�.
				<?php } ?>
			</span>
			<?php } else { ?>
				<span style="font-weight:bold;color:#FF0000">��ǰ�з� �������� ��ȯ�� �ּ���!</span>
				<?php if ($cateLevelCheck > 0) { ?>
					<span style="font-weight:normal;letter-spacing:-1px;font-size:11px;"><br>
						<span style="color:#FF0000;">
						�� ��ǰ�� ��ϵ� �з�(ī�װ�)�� ����/�����з��� ���ű����� �ٸ��� �����Ǿ� �ִ� ���, ��ǰ�� ����/���ű����� ������ �޶��� �� �ֽ��ϴ�.<br>
						</span>
						&nbsp; &nbsp; &nbsp;��ǰ�з��� ��ǰ�� ������å ������ ���� ���� ������ �����̿��� ���� �����Ͽ� �ֽñ� �ٶ��ϴ�.<br>
						&nbsp; &nbsp; &nbsp;��) A��ǰ�� �з������� 1���з�(���Ѿ���)�� 2���з�(�Ϲ�ȸ��-��������)�� �ٸ��� �����Ǿ� �ִ� ��� ��ȸ���� 1���з����� ��ǰ���Ű� ����
					</span>
				<?php } ?>
			<?php } ?>
		</td>
	</tr>
	<?php if (_CATEGORY_NEW_METHOD_ === false) { ?>
	<tr>
		<th>����ǰ��</th>
		<td>
			<b><?php echo number_format($totalCount);?></b> �� ��ǰ
		</td>
	</tr>
	<?php } ?>
	</table>

	<?php if (_CATEGORY_NEW_METHOD_ === false || $_GET['mode']) { ?>
	<div id="processBtn" style="margin:10px auto 0px auto; text-align:center;">
		<img src="../img/btn_category_method_change.gif" onclick="changeCategoryMethod();" class="hand" />
	</div>
	<?php } ?>
</div>

<div id="MSG01">
	<table cellpadding="1" cellspacing="0" border="0" class="small_ex">
		<tr>
			<td>
				<div><img src="../img/icon_list.gif" align="absmiddle"> ����� ��ǰ�з� �������� ����ǰ���(����)���޴��� ����ǰ�з��������� ���� Ȯ���Ͻ� �� �ֽ��ϴ�.<br><br></div>
				<div><img src="../img/icon_list.gif" align="absmiddle"> [���ǻ���]<br>
					&nbsp; &nbsp; &nbsp; &nbsp; - ��ȯ �Ŀ��� ������ ���·� �ǵ��� �� �����ϴ�.<br>
					&nbsp; &nbsp; &nbsp; &nbsp; - ��ȯ ��� ��ǰ���� ���� ��� ��ȯ�ð��� ���� �ɸ� �� ������ ������ �ֹ����� ���� �ð��� �����Ͽ� �ֽñ� �ٶ��ϴ�<br>
					&nbsp; &nbsp; &nbsp; &nbsp; - ��ȯ ���� �� ����ϰų� ������, ������, �ý��� ������ �ߴܵ� ��� �ٽ� �����Ͽ� �ֽñ� �ٶ��ϴ�.<br>
					&nbsp; &nbsp; &nbsp; &nbsp; - <span class="small_ex_point">�����ߺз��� ��ǰ�� ��� ���ο� ��ǰ�з� ���������� ��ȯ �� ���̹� ���ļ���, ���� �����Ͽ�, �ٳ���, ������ �� ��ǰ������ �ܺ� ����Ʈ�� �ڵ� ���� ��,</span><br>
					&nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; <span class="small_ex_point">������ �����ߴ� �з��� �ٸ� �з��� ���۵� �� �ֽ��ϴ�.</span><br>
					&nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; <span class="small_ex_point">�ϳ��� ��ǰ�� ���� ���� �з���  ��ϵ� ��� �����ؾ� �� �з������� �� �� ���� ������ �߻��� �� �ִ� �κ��̸�,</span><br>
					&nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; <span class="small_ex_point">���� �̷��� �κ��� �����ϰ��� �з������� ������ �� �ִ� ����� �����Ͽ� ������ �����̿��� ������ �ֽñ� �ٶ��ϴ�.</span><br>
				</div>
			</td>
		</tr>
	</table>
</div>
<script>cssRound('MSG01')</script>
<script language="JavaScript" type="text/JavaScript">window.onload = function(){ (typeof(UNM) != "undefined" ? UNM.inner() : ''); };</script>

<?php include "../_footer.php"; ?>