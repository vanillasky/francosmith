<?
$imgs = $urls = explode("|",$data['img_m']);

$checked[image_attach_method][file] = $checked[image_attach_method][url] = 'checked';

if (preg_match('/^http(s)?:\/\//',$imgs[0])) {
	$checked[image_attach_method][file] = '';
	$imgs	= array();
}
else {
	$urls	= array();
	$checked[image_attach_method][url] = '';
}
?>
<!-- ��ǰ �̹��� -->

<table class="tb">
<col class="cellC"><col class="cellL">
<tr>
	<td>�̹�����Ϲ��</td>
	<td class="noline">
	<label><input type="radio" name="image_attach_method" value="file" onClick="fnSetImageAttachForm();" <?=$checked[image_attach_method]['file']?>>���� ���ε�</label>
	<label><input type="radio" name="image_attach_method" value="url" onClick="fnSetImageAttachForm();" <?=$checked[image_attach_method]['url']?>>�̹���ȣ���� URL �Է�</label>

	</td>
</tr>
</table>

<div id="image_attach_method_upload_wrap">
	<!-- ���� ���ε� -->
	<table class="tb">
	<col class="cellC"><col class="cellL"><col class="cellC"><col class="cellL">
	<? $t = array_map("toThumb",$imgs); ?>
	<tr>
		<td>���̹���</td>
		<td>

		<table>
		<col valign="top" span="2">
		<? for ($i=0;$i<4;$i++){ ?>
		<tr>
			<td>
			<span><input type="file" name="imgs[]" style="width:200px"></span>
			</td>
			<td>
			<?=goodsimg($t[$i],20,"style='border:1px solid #cccccc' onclick=popupImg('../data/goods/$imgs[$i]','../') class=hand",2)?>
			</td>
			<td>
			<? if ($imgs[$i]){ ?>
			<div style="padding:0" class="noline"><input type="checkbox" name="del[imgs][<?=$i?>]"><font class="small" color="#585858">���� (<?=$imgs[$i]?>)</font></div>
			<? } ?>
			</td>
		</tr>
		<? } ?>
		</table>

		</td>
	</tr>
	</table>
	<!--//���� ���ε� -->
</div>

<div id="image_attach_method_link_wrap">
	<!-- URL �Է� -->
	<table class="tb">
	<col class="cellC"><col class="cellL"><col class="cellC"><col class="cellL">

	<tr>
		<td>���̹���</td>
		<td>

		<table>
		<col valign="top">
		<? for ($i=0;$i<4;$i++){ ?>
		<tr>
			<td>
			<span><input type="text" name="urls[]" style="width:400px" value="<?=$urls[$i]?>"></span>
			</td>
			<td>
			<?=goodsimg($urls[$i],20,"style='border:1px solid #cccccc' onclick=popupImg('$urls[$i]','../') class=hand",2)?>
			</td>
		</tr>
		<? } ?>
		</table>

		</td>
	</tr>
	</table>
	<!--//URL �Է� -->
</div>