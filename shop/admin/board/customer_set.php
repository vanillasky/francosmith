<?

$location = "����/�ı���� > ����/�ı� �Խ��� ����";
include "../_header.php";

include "../../lib/page.class.php";
@include "../../conf/phone.php";
include "../../conf/config.php";

### ���� ����
	$selected['qnaAllListSet'][$cfg['qnaAllListSet']] = "selected";
	$selected['qnaAuth_W'][$cfg['qnaAuth_W']] = "selected";
	$selected['qnaAuth_P'][$cfg['qnaAuth_P']] = "selected";
	$checked['qnaSecret'][$cfg['qnaSecret']] = "checked";
	if(!$cfg['qnaSpamComment'] && $cfg['qnaSpamComment'] != 0) $qnaSpamComment = 3;
	else $qnaSpamComment = $cfg['qnaSpamComment'];

	if(!$cfg['qnaSpamBoard'] && $cfg['qnaSpamBoard'] != 0) $qnaSpamBoard = 3;
	else $qnaSpamBoard = $cfg['qnaSpamBoard'];

### �ı� ����
	$selected['reviewAllListSet'][$cfg['reviewAllListSet']] = "selected";
	$selected['reviewAuth_W'][$cfg['reviewAuth_W']] = "selected";
	$selected['reviewAuth_P'][$cfg['reviewAuth_P']] = "selected";
	$checked['reviewWriteAuth'][$cfg['reviewWriteAuth']] = "checked";
	if(!$cfg['reviewSpamComment'] && $cfg['reviewSpamComment'] != 0) $reviewSpamComment = 3;
	else $reviewSpamComment = $cfg['reviewSpamComment'];

	if(!$cfg['reviewSpamBoard'] && $cfg['reviewSpamBoard'] != 0) $reviewSpamBoard = 3;
	else $reviewSpamBoard = $cfg['reviewSpamBoard'];

	if(!$cfg['reviewFileNum']) $cfg['reviewFileNum']  = 1;
	if(!$cfg['reviewLimitPixel']) $cfg['reviewLimitPixel']  = '';
	if(!$cfg['reviewFileSize']) $cfg['reviewFileSize']  = '';
	$selected['reviewFileNum'][$cfg['reviewFileNum']] = "selected";
	$selected['reviewFileSize'][$cfg['reviewFileSize']] = "selected";
?>

<form method="post" action="../board/customer_indb.php?mode=replySet" name="fmSet">
<div class="title title_top">��ǰ���ǰԽ��� ���� <span>����Ʈ ���� ���� �� �۾��⿡ ���� ������ �����Ͻ� �� �ֽ��ϴ�</span> <a href="javascript:manual('<?=$guideUrl?>board/view.php?id=board&no=11')"><img src="../img/btn_q.gif" border="0" align="absmiddle" hspace="2"></a></div>
<table class="tb">
<col class="cellC"><col class="cellL">
<tr>
	<td style="line-height:17px;">�Խ���<br />�������� �Խñ� ����</td>
	<td><select name="qnaAllListSet">
			<option value="10" <?=$selected['qnaAllListSet'][10]?>>10���� ���</option>
			<option value="20" <?=$selected['qnaAllListSet'][20]?>>20���� ���</option>
			<option value="30" <?=$selected['qnaAllListSet'][30]?>>30���� ���</option>
			<option value="40" <?=$selected['qnaAllListSet'][40]?>>40���� ���</option>
			<option value="50" <?=$selected['qnaAllListSet'][50]?>>50���� ���</option>
		</select> <span class="extext">�Խ��� ��ü������ �������� �Խñ� ���� ������ �����մϴ�.</span></td>
</tr>
<tr>
	<td style="line-height:17px;">��ǰ����<br />�������� �Խñ� ����</td>
	<td><input type="text" name="qnaListCnt" value="<?=$cfg['qnaListCnt']?>" size="6" class="rline" onkeydown="onlynumber();" /> �� <span class="extext">��ǰ �󼼺��� �ϴ��� �������� �Խñ� ���� ������ �����մϴ�.</span></td>
</tr>
<tr>
	<td>�۾��� ����</td>
	<td class="noline">
		<table>
			<tr>
				<td>
			<table align="left" border="0">
			<tr>
				<td align="center">�۾���</td>
				<td align="center">��۾���</td>
			</tr>
			<tr>
				<?
				$r_level = array("W","P");

				$res2 = $db->query("select * from ".GD_MEMBER_GRP." order by level");
				while ($data=$db->fetch($res2)) $memberGrp[$data['level']] = $data['grpnm'];

				$selected['qnaAuth_W'][$qnaAuth_W] = "selected";
				$selected['qnaAuth_P'][$qnaAuth_P] = "selected";

				for ($i=0;$i<count($r_level);$i++){
				?>
				<td>
					<select name="qnaAuth_<?=$r_level[$i]?>">
					<option value=''>���Ѿ���</option>
					<? foreach ($memberGrp as $k => $v){ ?>
					<option value="<?=$k?>" <?=$selected["qnaAuth_$r_level[$i]"][$k]?> style="background-color:#E9FFE9"><?=$v?> - lv[<?=$k?>]</option>
					<? } ?>
					</select>
				</td>
				<? } ?>
			</tr>
			</table>
				</td>
			</tr>
			<tr>
				<td>
			<div style="padding:3 0 6 0"><font class="extext"><a href="/shop/admin/member/group.php" target="_new"><font class="extext_l">[�׷����]</font></a> ���� �׷��� ���弼��</div>
			<div>�׷���ѽ� ���� ���� ���� �׷� ������ ���� ����� ���� ������ �ֽ��ϴ�.</font></div>
				</td>
			</tr>
		</table>
	</td>
</tr>
<tr>
	<td>��б� ���</td>
	<td class="noline"><input type="radio" name="qnaSecret" value="" <?=$checked['qnaSecret']['']?>> �̻��&nbsp;&nbsp;
	<input type="radio" name="qnaSecret" value="secret" <?=$checked['qnaSecret']['secret']?>> ���</td>
</tr>
<tr>
	<td>�ڸ�Ʈ ���Թ���</td>
	<td class="noline">
		<label><input type="checkbox" name="qnaSpamComment[]" value="1" <? if ($qnaSpamComment&1) echo"checked" ?>>�ܺ���������</label>&nbsp;&nbsp;
		<label><input type="checkbox" name="qnaSpamComment[]" value="2" <? if ($qnaSpamComment&2) echo"checked" ?>>�ڵ���Ϲ�������</label>&nbsp;&nbsp;
		<div style="padding:2px"></div>
		<font class="extext">�� ���Թ�������� ���� ���׷��̵� �� ����Դϴ�. ��ɻ�� ���� �� <a href="javascript:manual('<?=$guideUrl?>board/view.php?id=board&no=9')"><u>��ġ�ȳ�</u></a>�� �о����</font>
	</td>
</tr>
<tr>
	<td>�Խñ� ���Թ���</td>
	<td class="noline">
		<label><input type="checkbox" name="qnaSpamBoard[]" value="1" <? if ($qnaSpamBoard&1) echo"checked" ?>>�ܺ���������</label>&nbsp;&nbsp;
		<label><input type="checkbox" name="qnaSpamBoard[]" value="2" <? if ($qnaSpamBoard&2) echo"checked" ?>>�ڵ���Ϲ�������</label> <font class="extext"><a href="javascript:popupLayer('../board/popup.captcha.php')"><font class="extext_l">[�̹�������]</font></a>
		<div style="padding:2px"></div>
		<font class="extext">���Թ����� ���� �ڼ��� �����Ͻ÷��� <a href="http://www.godo.co.kr/edu/edu_board_list.html?cate=adminen&in_view=y&sno=408#Go_view" target="_blank"><font class="extext_l">[�����ڷ�]</font></a> �� Ȯ���ϼ���</font></font><br>
		</div>
	</td>
</tr>
</table>

<div style="padding-top:20px"></div>

<div class="title title_top">��ǰ�ı�Խ��� ���� <span>����Ʈ ���� ���� �� �۾��⿡ ���� ������ �����Ͻ� �� �ֽ��ϴ�</span> <a href="javascript:manual('<?=$guideUrl?>board/view.php?id=board&no=11')"><img src="../img/btn_q.gif" align="absmiddle" hspace="2" /></a></div>
<table class="tb">
<col class="cellC" /><col class="cellL" />
<tr>
	<td style="line-height:17px;">�Խ���<br />�������� �Խñ� ����</td>
	<td><select name="reviewAllListSet">
			<option value="10" <?=$selected['reviewAllListSet'][10]?>>10���� ���</option>
			<option value="20" <?=$selected['reviewAllListSet'][20]?>>20���� ���</option>
			<option value="30" <?=$selected['reviewAllListSet'][30]?>>30���� ���</option>
			<option value="40" <?=$selected['reviewAllListSet'][40]?>>40���� ���</option>
			<option value="50" <?=$selected['reviewAllListSet'][50]?>>50���� ���</option>
		</select> <span class="extext">�Խ��� ��ü������ �������� �Խñ� ���� ������ �����մϴ�.</span></td>
</tr>
<tr>
	<td style="line-height:17px;">��ǰ����<br />�������� �Խñ� ����</td>
	<td><input type="text" name="reviewListCnt" value="<?=$cfg['reviewListCnt']?>" size="6" class="rline" onkeydown="onlynumber();" /> �� <span class="extext">��ǰ���� �������� �Խñ� ���� ������ �����մϴ�.</span></td>
</tr>
<tr>
	<td>�۾��� ����</td>
	<td class="noline">
		<table>
			<tr>
				<td>
			<table align="left" border="0">
			<tr>
				<td align="center">�۾���</td>
				<td align="center">��۾���</td>
			</tr>
			<tr>
				<?
				$r_level = array("W","P");

				$res2 = $db->query("select * from ".GD_MEMBER_GRP." order by level");
				while ($data=$db->fetch($res2)) $memberGrp[$data['level']] = $data['grpnm'];

				$selected['reviewAuth_W'][$reviewAuth_W] = "selected";
				$selected['reviewAuth_P'][$reviewAuth_P] = "selected";

				for ($i=0;$i<count($r_level);$i++){
				?>
				<td>
					<select name="reviewAuth_<?=$r_level[$i]?>">
					<option value=''>���Ѿ���</option>
					<? foreach ($memberGrp as $k => $v){ ?>
					<option value="<?=$k?>" <?=$selected["reviewAuth_$r_level[$i]"][$k]?> style="background-color:#E9FFE9"><?=$v?> - lv[<?=$k?>]</option>
					<? } ?>
					</select>
				</td>
				<? } ?>
			</tr>
			</table>
				</td>
			</tr>
			<tr>
				<td>
			<div style="padding:3 0 6 0"><font class="extext"><a href="/shop/admin/member/group.php" target="_new"><font class="extext_l">[�׷����]</font></a> ���� �׷��� ���弼��</div>
			<div>�׷���ѽ� ���� ���� ���� �׷� ������ ���� ����� ���� ������ �ֽ��ϴ�.</font></div>
				</td>
			</tr>
		</table>
	</td>
</tr>
<tr>
	<td>�ڸ�Ʈ ���Թ���</td>
	<td class="noline">
		<label><input type="checkbox" name="reviewSpamComment[]" value="1" <? if ($reviewSpamComment&1) echo"checked" ?>>�ܺ���������</label>&nbsp;&nbsp;
		<label><input type="checkbox" name="reviewSpamComment[]" value="2" <? if ($reviewSpamComment&2) echo"checked" ?>>�ڵ���Ϲ�������</label>&nbsp;&nbsp;
		<div style="padding:2px"></div>
		<font class="extext">�� ���Թ�������� ���� ���׷��̵� �� ����Դϴ�. ��ɻ�� ���� �� <a href="javascript:manual('<?=$guideUrl?>board/view.php?id=board&no=9')"><u>��ġ�ȳ�</u></a>�� �о����</font>
	</td>
</tr>
<tr>
	<td>�Խñ� ���Թ���</td>
	<td class="noline">
		<label><input type="checkbox" name="reviewSpamBoard[]" value="1" <? if ($reviewSpamBoard&1) echo"checked" ?>>�ܺ���������</label>&nbsp;&nbsp;
		<label><input type="checkbox" name="reviewSpamBoard[]" value="2" <? if ($reviewSpamBoard&2) echo"checked" ?>>�ڵ���Ϲ�������</label> <font class="extext"><a href="javascript:popupLayer('../board/popup.captcha.php')"><font class="extext_l">[�̹�������]</font></a>
		<div style="padding:2px"></div>
		<font class="extext">���Թ����� ���� �ڼ��� �����Ͻ÷��� <a href="http://www.godo.co.kr/edu/edu_board_list.html?cate=adminen&in_view=y&sno=408#Go_view" target="_blank"><font class="extext_l">[�����ڷ�]</font></a> �� Ȯ���ϼ���</font></font><br>
		</div>
	</td>
</tr>
<tr>
	<td>÷�� �̹��� ����</td>
	<td>
		<label>
		÷�� �̹��� ����
		<select name="reviewFileNum">
		<?php for($fn=1;$fn<=10;$fn++){ ?>
		<option value='<?=$fn;?>' <?=$selected["reviewFileNum"][$fn]?>><?=$fn;?>��</option>
		<?php } ?>
		</select>
		</label>
		, 
		<label>
		(����)������ ���� 
		<input type="text" name="reviewLimitPixel" value="<?=$cfg["reviewLimitPixel"];?>" size="6" class="rline" onkeydown="onlynumber();">px
		</label>
		,
		<label>
		�뷮 ����
		<select name="reviewFileSize">
		<option value="">��������</option>
		<?php for($fs=100;$fs<=500;$fs+=100){ ?>
		<option value='<?=$fs;?>' <?=$selected["reviewFileSize"][$fs]?>><?=$fs;?>KB</option>
		<?php } ?>
		</select>
		</label>
		<div style="padding:2px"></div>
		<font class="extext">�ı� ��� �� ��� ������ �̹��� ������ ������ �׸��� �� �̹��� �� ���� �뷮�� �����մϴ�.(��������� : 300px, ����뷮 : 300KB)<br>
ȸ���� ����� �̹����� ���λ���� ������ ������� Ŭ ��� �ڵ� �������� �˴ϴ�.</font></font><br>
		</div>
	</td>
</tr>
</table>
<div class="button_top"><input type="image" src="../img/btn_save3.gif" /></div>
</form>

<script>window.onload = function(){ UNM.inner();};</script>
<? include "../_footer.php"; ?>