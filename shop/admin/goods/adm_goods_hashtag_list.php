<?php
$location = "��ǰ���� > �ؽ��±� ����";
include '../_header.php';

$hashtag = Core::loader('hashtag');
$hashtagConfig = $hashtag->getConfig();

if(!$hashtagConfig['hashtag_snsUse']) $hashtagConfig['hashtag_snsUse'] = 'y';
$checked['hashtag_snsUse'][$hashtagConfig['hashtag_snsUse']] = "checked='checked'";
?>
<link href="<?php echo $cfg['rootDir']; ?>/lib/js/jquery-ui-1.10.4.custom.css" rel="stylesheet" type="text/css"/>
<script type="text/javascript" src="<?php echo $cfg['rootDir']; ?>/proc/hashtag/hashtagControl.js?actTime=<?php echo time(); ?>"></script>
<style>
.hashtagList-boxBorder { border: 1px solid #e8e8e8; }
.hashtagList-layout { width: 1000px; }
.hashtagList-layout .hashtagList-marginTop5 { margin-top: 5px; }
.hashtagList-layout .hashtagList-common-layout { width: 100%; height: 200px;}
.hashtagList-layout .hashtagList-common-layout .hashtagListConfig-save-button { width: 100%; margin-top: 10px; text-align: center; }
.hashtagList-layout .hashtagList-common-layout .hashtag-skinPatchInfo { border: 1px solid #cccccc; height: 30px; line-height:30px; margin-bottom: 10px; width: 99%; padding: 3px; color: red; font-weight: bold;}
.hashtagList-layout .hashtagList-list-layout { width: 100%; margin-top: 40px; height: 600px; }
.hashtagList-layout .hashtagList-list-layout .hashtagList-leftLayout { float: left; width: 300px; height: 550px; }
.hashtagList-layout .hashtagList-list-layout .hashtagList-leftLayout .hashtagList-hashtagAdd {  width: 100%; margin-top: 10px; padding: 5px; }
.hashtagList-layout .hashtagList-list-layout .hashtagList-leftLayout .hashtagList-hashtagSearch {  width: 100%; margin-top: 10px; padding: 5px; }
.hashtagList-layout .hashtagList-list-layout .hashtagList-leftLayout .hashtagList-hashtagListBox {  width: 100%; height: 350px; padding: 5px; overflow-y: auto; border-top: none; background-color: #cccccc;}
.hashtagList-layout .hashtagList-list-layout .hashtagList-rightLayout { float: left; width: 600px; height: 700px; margin-left: 30px; }
.hashtagList-layout .hashtagList-list-layout .hashtagList-rightLayout .right-second-layout { }
.hashtagList-layout .hashtagList-list-layout .hashtagList-rightLayout .right-second-layout .right-second-title { font-weight: bold; font-family: Dotum; font-size: 14px; margin-bottom: 10px; }
.hashtagList-layout .hashtagList-list-layout .hashtagList-rightLayout .right-second-layout .right-second-title span { font: 11px dotum; padding-left: 10px; color: #6d6d6d; }
.hashtagInputText { border: 1px #BDBDBD solid; width: 170px; float: left; height: 18px; }
.hashtagInputText input { border: none; height: 16px; width: 150px; }
.hashtagInputTextButton { margin-left: 3px; }
</style>

<input type="hidden" name="cfgRootDir" id="cfgRootDir" value="<?php echo $cfg['rootDir']; ?>" />
<div class="hashtagList-layout">
	<!-- ��� ���̾ƿ� -->
	<div class="hashtagList-common-layout">
		<form name="hashtagListConfigForm" id="hashtagListConfigForm" action="./adm_goods_hashtag_indb.php" method="post" target="ifrmHidden">
		<input type="hidden" name="mode" id="mode" value="" />

		<div class="title title_top">�ؽ��±� ��ǰ����Ʈ ���� ���� <span>�ؽ��±� ��ǰ����Ʈ �������� ����� �����մϴ�.</span></div>
		
		<div class="hashtag-skinPatchInfo">
			��2016�� 10�� 06�� ���� ���� ��Ų�� ����Ͻô� ��� �ݵ�� ��Ų��ġ�� �����ؾ� ��� ����� �����մϴ�.
			<a href="http://www.godo.co.kr/customer_center/patch.php?sno=2634" class="extext" style="font-weight:bold" target="_blank"> [��ġ �ٷΰ���]</a>
		</div>

		<table class="tb">
			<colgroup>
				<col class="cellC" />
				<col class="cellL" />
			</colgroup>
			<tbody>
			<tr>
				<td>SNS �������<br />��뼳��</td>
				<td>
					<div>
						<input type="radio" name="hashtag_snsUse" value="y" <?php echo $checked['hashtag_snsUse']['y']; ?> /> ���
						&nbsp;
						<input type="radio" name="hashtag_snsUse" value="n" <?php echo $checked['hashtag_snsUse']['n']; ?> /> ������
					</div>
					<div class="extext hashtagList-marginTop5">���θ�� > SNS �����ϱ� ���������� ������� ������ SNS ���� ����� ����մϴ�.</div>
					<div class="extext">���θ���, �ؽ��±׸�, �ش� ��ǰ����Ʈ URL�� ������ �� �ֽ��ϴ�.</div>
				</td>
			</tr>
			</tbody>
		</table>

		<div class="hashtagListConfig-save-button"><img src="../img/btn_save.gif" border="0" style="cursor: pointer;" id="hashtagListConfig-save-btn" /></div>
		</form>
	</div>
	<!-- ��� ���̾ƿ� -->

	<!-- �ϴ� ���̾ƿ� -->
	<div class="hashtagList-list-layout">
		<div class="title title_top">
			�ؽ��±� ����
			<span>�ؽ��±� ��� �� ����, �ؽ��±� ��ǰ����Ʈ �������� ������ �� �ֽ��ϴ�.</span>
			<a href="javascript:manual('<?=$guideUrl?>board/view.php?id=product&no=51')"><img src="../img/btn_q.gif" border=0 align=absmiddle hspace=2></a>
		</div>
		<!-- ���� ���̾ƿ� -->
		<div class="hashtagList-leftLayout">
			<!-- �ؽ��±� �߰� -->
			<div class="hashtagList-hashtagAdd hashtagList-boxBorder">
				<div>
					<div>
					  <div class="hashtagInputText">#<input type="text" name="hashtag" id="hashtag" class="hashtagInputListSearch" maxlength="20" /></div>
					  <img src="../img/btn_add3.png" border="0" class="hand hashtagInputTextButton" id="hashtagAddBtn" alt="�߰�" align="absmiddle" />
					</div>
				</div>
				<div class="hashtagList-marginTop5">
					<div>ENTER Ű�ε� �ؽ��±� �߰��� �����մϴ�.(�ִ� 20��)</div>
					<div class="extext">'��','��' ���� �������� ���ڴ� ��� �� �� �����ϴ�.</div>
				</div>
			</div>
			<!-- �ؽ��±� �߰� -->

			<!-- �ؽ��±� �˻� -->
			<div class="hashtagList-hashtagSearch hashtagList-boxBorder">
				<div>
					<div>
					  <div class="hashtagInputText">#<input type="text" name="hashtagSearch" id="hashtagSearch" class="hashtagInputListSearch" maxlength="20" /></div>
					   <img src="../img/btn_search3.png" border="0" class="hand hashtagInputTextButton" id="hashtagSearchBtn" alt="�˻�" align="absmiddle" />
					</div>
				</div>
			</div>
			<!-- �ؽ��±� �˻� -->

			<form name="hashtagListForm" id="hashtagListForm" action="./adm_goods_hashtag_indb.php" method="post" target="ifrmHidden">
			<input type="hidden" name="mode" id="mode" value="" />
			<div id="hashtagListBox" class="hashtagList-hashtagListBox hashtagList-boxBorder"></div>
			</form>
		</div>
		<!-- ���� ���̾ƿ� -->

		<!-- ���� ���̾ƿ� -->
		<div class="hashtagList-rightLayout">
			<div class="right-second-layout">
				<div class="right-second-title">�ؽ��±� ���� <span>�ؽ��±� ���� ������ Ȯ���Ͻ� �� �ֽ��ϴ�.</span></div>
				<input type="hidden" name="hashtagWidget_name" id="hashtagWidget_name" value="" />
				<table class="tb">
					<colgroup>
						<col class="cellC" />
						<col class="cellL" />
					</colgroup>
					<tbody>
					<tr>
						<td>��ϵ� ��ǰ</td>
						<td><span id="hashtagRegistGoodsCount">0</span>��</td>
					</tr>
					<tr>
						<td>��ǰ����Ʈ URL</td>
						<td>
							<div id="hashtagWidgetUrl">�ؽ��±׸� �����Ͽ� �ּ���.</div>
							<div class="extext hashtagList-marginTop5">URL�� �����Ͽ� ���, �˾� � ��ũ�� �ɾ� ȫ���غ�����.</div>
						</td>
					</tr>
					<tr>
						<td>��ǰ����Ʈ<br />�ڵ� ����</td>
						<td>
							<div class="hashtagList-marginTop5 extext">�ش� �ؽ��±� ��ǰ����Ʈ�� �̺�Ʈ ������ � ������ �� �ֵ��� �ҽ��ڵ带 ������ �� �ֽ��ϴ�.</div>

							<table class="tb">
							<colgroup>
								<col class="cellC" />
								<col class="cellL" />
							</colgroup>
							<tbody>
							<tr>
								<td>���̾ƿ�</td>
								<td>
									<input type="text" name="hashtagWidget_width" id="hashtagWidget_width" size="2" value="4" maxlength="2" />
									*
									<input type="text" name="hashtagWidget_height" id="hashtagWidget_height" size="2" value="2" maxlength="2" />
								</td>
							</tr>
							<tr>
								<td>��ǰ ����Ʈ<br />���� ������</td>
								<td>
									<input type="text" name="hashtagWidget_iframeWidth" id="hashtagWidget_iframeWidth" size="4" value="1000" maxlength="4" />px
									&nbsp;<span class="extext">���� ����� ������ �ּ���.</span>
								</td>
							</tr>
							<tr>
								<td>��ǰ �̹���<br />������</td>
								<td>
									<input type="text" name="hashtagWidget_imageWidth" id="hashtagWidget_imageWidth" size="4" value="150" maxlength="4" />px
									&nbsp;<span class="extext">���� ����� ������ �ּ���.</span>
								</td>
							</tr>
							</table>

							<div class="hashtagList-marginTop5"><img src="../img/createCode.png" border="0" class="hand" align="absmiddle" id="hashtagCreateCodeBtn" /></div>
						</td>
					</tr>
					</tbody>
				</table>
			</div>
		</div>
		<!-- ���� ���̾ƿ� -->
	</div>
	<!-- �ϴ� ���̾ƿ� -->
</div>

<script type="text/javascript">
jQuery(document).ready(HashtagListController);
</script>
<?php include '../_footer.php'; ?>