<?
//�ʱⰪ ����
if(!$setGoodsConfig[state]) $setGoodsConfig[state] = 'N';
if(!$setGoodsConfig[setGoodsBanner]) $setGoodsConfig[setGoodsBanner] = "sky_ban_codi.gif";
?>
<html>
<head>
	<title>'Godo Shoppingmall e���� Season4 �����ڸ��'</title>
	<meta http-equiv="Content-Type" content="text/html; charset=euc-kr">
	<script type="text/javascript" src="../../js/jquery-1.7.2.min.js"></script>
	<script type="text/javascript" src="../../js/default.js"></script>	
	<link rel="styleSheet" href="../../../admin/style.css">
	<link rel="styleSheet" href="../../../admin/_contextmenu/contextmenu.css?<?=time()?>">
	<script src="../../../admin/common.js"></script>
	<style>
		/*** ���� ���̾ƿ� ���� ***/
		body {margin:0 0 0 0px}
	</style>
	<script>
		function chkForm2(fm)
		{
			/*
			if (fm.sessTime.value && fm.sessTime.value<20){
				alert("ȸ������ �����ð� ���ѽ� 20�� �̻� �����մϴ�");
				fm.sessTime.value = 20;
				fm.sessTime.focus();
				return false;
			}
			*/
		}
		function copy_txt(val){
			window.clipboardData.setData('Text', val);
			alert("Ŭ�����忡 ����Ǿ����ϴ�.");
		}

		function cl_use() {
			jQuery('#tb0').attr('disabled',false);
			jQuery('#tb1').attr('disabled',false);
			jQuery('#tb2').attr('disabled',false);
			jQuery('#tb3').attr('disabled',false);
		}

		function cl_none() {
			jQuery('#tb0').attr('disabled',true);
			jQuery('#tb1').attr('disabled',true);
			jQuery('#tb2').attr('disabled',true);
			jQuery('#tb3').attr('disabled',true);			
		}

	</script>
</head>
<body>

<form name=form method=post action="./indb.php" enctype="multipart/form-data" onsubmit="return chkForm2(this)">
	<input type="hidden" name="fn" value="F">
	<input type="hidden" name="setGoodsBanner_old" value="<?=$setGoodsConfig[setGoodsBanner]?>">
	<div class="title title_top">��뼳��<span><a href="javascript:manual('http://guide.godo.co.kr/season4/board/view.php?id=product&no=41')"><img src="../../../admin/img/btn_q.gif" border=0 align=absmiddle></a></span></div>
	<table class=tb>
		<col class=cellC>
		<col class=cellL>
		<tr>
			<td>��뿩�� ����</td>
			<td>
				 <span style="width:80px;" class="noline"><input type="radio" name="state" value="Y" <?if($setGoodsConfig[state]=='Y'){?>checked<?}?> onclick="cl_use()"/>&nbsp;���</span> 
				 <span  class="noline"><input type="radio" name="state" value="N" <?if($setGoodsConfig[state]=='N'){?>checked<?}?>  onclick="cl_none()"/>&nbsp;������</span>
				 <div class="extext" style="margin:3px 0 0 3px;line-height:150%">
					<div style="padding:5px,0;"><b>* [���] ���� ���� ��</b><br>
					- �ڵ� ������������ �ڵ� ���������� Ȱ��ȭ �Ǹ�, ����4 ���� ��Ų�� �ڵ� ������������ ��ũ�� ��ʰ� ��µ˴ϴ�.
					</div>

					<div style="padding:5px,0;">
					<b>* [������] ���� ���� ��</b><br>
					- �ڵ� ������������ �ڵ� ���������� ��Ȱ��ȭ �Ǹ�, ������ ���� �� ���� ���� �ʽ��ϴ�. <br>
					- ����4 ���ν�Ų�� �ڵ� ������������ ��ũ�� ��ʰ� ��µ��� �ʽ��ϴ�.<br>
					</div>
				</div>
			
			</td>		
		</tr>
	</table>

	<div>
		<div class="title title_top">��ʵ�� ����<span><a href="javascript:manual('http://guide.godo.co.kr/season4/board/view.php?id=product&no=41')"><img src="../../../admin/img/btn_q.gif" border=0 align=absmiddle></a></span></div>
		<table class=tb id="tb0">
			<col class=cellC>
			<col class=cellL>
			<tr>
				<td>�ڵ� ������ ����<br>��� ����</td>
				<td>
					<div style="margin:0 0 20px; 0">
						<span style="color:green;padding:0,5px,0,0;">[�ڵ� ���������� URL]</span> http://<?=$_SERVER['HTTP_HOST']?>/shop/setGoods/
						<span><a href="http://<?=$_SERVER['HTTP_HOST']?>/shop/setGoods/" target="_blank"><img src="../../../admin/img/btn_go.gif" alt="�ٷΰ���" /></a></span>
					</div>
					
					<div>
						��� ġȯ�ڵ� :  {Banner} <span style="padding:0,5px;vertical-align:bottom;cursor:pointer;"><img src="../../../admin/img/btn_cate_copy.gif" onclick="copy_txt('{Banner}')" /></span>
					</div>

					<div class="extext" style="margin:5px 0 0 3px;line-height:150%">
						1. ġȯ�ڵ带 �����Ͽ� �������ΰ���>�������� �����Ρ� ���ϴ� ��ġ�� '�ٿ��ֱ�(Ctrl+V)'�Ͽ� ������ �ּ���. <br>
						&nbsp;&nbsp;&nbsp;�� ����4 �⺻��Ų(apple_tree)�� ��� ġ���ڵ尡 �⺻���� ����/����Ǿ� �ֽ��ϴ�.<br>
						2. �Ʒ��� ����� ��� �̹����� �ش� ��ġ�� ��µǸ�, ��� Ŭ���� �ڵ� ����������URL�� ����˴ϴ�.		
					</div>

					<div style="margin:10px 0 10px; 0 text-align:top;">
						��� �̹��� : <img src="../../data/banner/<?=$setGoodsConfig[setGoodsBanner]?>" align="middle"><br>
					</div>

					<div style="margin:10px 0 10px; 0">
						�̹��� Upload : <input type="file" id="setGoodsBanner" name="setGoodsBanner" style="width:300px;">

					</div>

				</td>		
			</tr>
		</table>
	</div>
	<div>
		<div class="title title_top">���������� ����<span><a href="javascript:manual('http://guide.godo.co.kr/season4/board/view.php?id=product&no=41')"><img src="../../../admin/img/btn_q.gif" border=0 align=absmiddle></a></span></div>
		<table class=tb id="tb1">
			<col class=cellC>
			<col class=cellL>
			<tr>
				<td>�ڵ� ���� Ÿ��</td>
				<td>				
					<div class="display-type-wrap">
					<img src="../../images/img_codytype_tile.gif" />
					<div class="noline" style="padding:0,35px;">
					<input type="radio" name="display_type" value="" checked>
					</div>
					<!-- ���Ŀ� �������� �߰��� ����-->
				</td>		
			</tr>
			<tr>
				<td>��������</td>
				<td>
					<?	
						if($setGoodsConfig[listing] == 'D') $listingD = "checked";
						else if($setGoodsConfig[listing] == 'L') $listingL = "checked";
						else $listingR = "checked";
					?>
					<span style="width:200px;float:left;" class="noline"><input type="radio" name="listing" value="R" <?=$listingR?>>���� (���� ���ٽ� �� ��ġ)</span>
					<span style="width:80px;float:left;" class="noline"><input type="radio" name="listing" value="D" <?=$listingD?>>��� �� </span>
					<span style="width:150px;" class="noline"><input type="radio" name="listing" value="L" <?=$listingL?>>�α�� (���ƿ� ���� ��)</span>
				</td>		
			</tr>
			<tr>
				<td>�ڵ��ǰ ���� ����</td>
				<td>
					<?	
						if($setGoodsConfig[goods_display] == 'N') $goods_displayN = "checked";
						else $goods_displayY = "checked";
					?>
					<span style="width:230px;" class="noline"><input type="radio" name="goods_display" value="Y" <?=$goods_displayY?>>ǰ����ǰ�� ���Ե� �ڵ��ǰ ������ </span> 
					<span style="width:230px;" class="noline"><input type="radio" name="goods_display" value="N" <?=$goods_displayN?>>ǰ����ǰ�� ���Ե� �ڵ��ǰ ��������</span>
					<div class="extext" style="margin:10px 0 0 3px;line-height:150%">
						�ڵ𳻿� ǰ����ǰ�� ���ԵǾ� ���� ����� �������θ� �����մϴ�.<br>
						���� ǰ����ǰ�� ���Ե� �ڵ��ǰ�� �ֹ��� ǰ����ǰ�� ���ܵǾ� �ֹ��� ����˴ϴ�.		
					</div>
				</td>		
			</tr>
		</table>
	</div>
	
	<div>
		<div class="title title_top">�������� ����<span><a href="javascript:manual('http://guide.godo.co.kr/season4/board/view.php?id=product&no=41')"><img src="../../../admin/img/btn_q.gif" border=0 align=absmiddle></a></span></div>
		<table class='tb' id="tb2">
			<col class=cellC>
			<col class=cellL>
			<tr>
				<td>�ٸ� �ڵ� ���� ���</td>
				<td>
					<?	
						if($setGoodsConfig[means] == '2') $means2 = "checked";
						else if($setGoodsConfig[means] == '3') $means3 = "checked";
						else if($setGoodsConfig[means] == '4') $means4 = "checked";
						else $means1 = "checked";
					?>
					<span style="width:150px;float:left;" class="noline"><input type="radio" name="means" value="1" <?=$means1?>>��� �����ڵ�</span>
					<span style="width:200px;float:left;" class="noline"><input type="radio" name="means" value="2" <?=$means2?>>��ϼ�(�ֱ� �ڵ�) 10����</span>
					<span style="width:200px;float:left;" class="noline"><input type="radio" name="means" value="3" <?=$means3?>>�α��(���ƿ� ���� ��) 10����</span>
					<span style="width:200px;clear:both;" class="noline"><input type="radio" name="means" value="4" <?=$means4?>>��¾���</span>
			
						<div class="extext" style="margin:10px 0 0 3px;line-height:150%">
						 �ڵ� ���������� [�ٸ��ڵ𺸱�] ������ ���� ���ǿ� ���� 6�� �ڵ��ǰ�� �������� ǥ�õ˴ϴ�.
					</div>
				</td>		
			</tr>		
			<tr>
				<td>��� ���</td>
				<td>
					<?	
						if($setGoodsConfig[memo] == 'N') $memoN = "checked";
						else $memoY = "checked";

						if($setGoodsConfig[memo_permission] == 'all') $all = "selected";
						else $user = "selected";				
					?>
					<span style="width:150px;float:left;" class="noline"><input type="radio" name="memo" value="Y" <?=$memoY?>>���: ��۾��� ���� </span>
					<span style="width:150px;float:left;" class="noline">	<select name="memo_permission">
							<option value="all" <?=$all?>>�⺻:��ü���
							<option value="user" <?=$user?>>ȸ������
						</select>

					</span>
					<span style="width:150px;clear:both;" class="noline"><input type="radio" name="memo" value="N" <?=$memoN?>>������</span>
					<div class="extext" style="margin:10px 0 0 3px;line-height:150%">
						�������� �ϴܿ����� ���(�ı�)�ޱ� ����� Ȱ��ȭ �Ǿ� ��µ˴ϴ�.<br>
						��ϵ� ����� �������������� �ֱ� ��ϼ����� 7������ ��µ˴ϴ�.
					</div>
				</td>		
			</tr>
			<!--tr>
				<td>SNS �����ϱ�</td>
				<td>
					<?	
						if($setGoodsConfig[SNS] == 'N') $SNSN = "checked";
						else $SNSY = "checked";
					?>
					<input type="radio" name="SNS" value="Y" <?=$SNSY?>>���					
					<input type="radio" name="SNS" value="N" <?=$SNSN?>>������  [���۳��� �����ϱ�]
					<div class="extext" style="margin:3px 0 0 3px;line-height:150%">
						�ڵ�� �������� SNS�������� ����˴ϴ�.
					</div>
				</td>		
			</tr-->		
		</table>
	</div>

	<div>
		<div class="title title_top">�ΰ���� ����<span><a href="javascript:manual('http://guide.godo.co.kr/season4/board/view.php?id=product&no=41')"><img src="../../../admin/img/btn_q.gif" border=0 align=absmiddle></a></span></div>
		<table class=tb id="tb3">
			<col class=cellC>
			<col class=cellL>
			<tr>
				<td>��ǰ�� �����ڵ� ����</td>
				<td>
					<?	
						if($setGoodsConfig[setconnection] == 'Y') $setconnectionY = "checked";
						else $setconnectionN = "checked";
					?>
					<span style="width:80px;float:left;" class="noline"><input type="radio" name="setconnection" value="Y" <?=$setconnectionY?>>��� </span>
					<span style="width:80px;float:left;" class="noline"><input type="radio" name="setconnection" value="N" <?=$setconnectionN?>>������</span>

					<div class="extext" style="clear:both;margin:3px 0 0 3px;line-height:150%">
						������ǰ ���������� �ش� ��ǰ�� ���Ե� �ڵ� ������ �������� ������ �ִ� ����Դϴ�.<br>
						������� ������, ������ǰ ���������� [�ش��ǰ �����ڵ� ����] ��ư�� �����˴ϴ�. <br>
						��ư Ŭ���� �ش� ��ǰ�� ���Ե� �ڵ��ǰ ����Ʈ ������ �����˴ϴ�.<br>
					 </div>

				</td>		
			</tr>
		</table>
	</div>
	<div class="button">
		<input type=image src="../../../admin/img/btn_register.gif">		
	</div>
</form>


<div id="MSG01">
<table cellpadding="1" cellspacing="0" border="0" class="small_ex">
<tr><td><img src="../../../admin/img/icon_list.gif" align="absmiddle">* �ڵ��ǰ ���� ��å "�߿�!" </td></tr>
<tr><td><img src="../../../admin/img/icon_list.gif" align="absmiddle">- �ڵ��ǰ ���� �Ϻ� ��ǰ�� ������ �� ���� ó���� �� ���, �ش� �ڵ��ǰ�� �������°� <font style="color:#37a3ee;font-weight:bold;">YES</font>-><font style="color:#ef2869;font-weight:bold;">NO</font>�� ����Ǹ�, �������������� �������� ������ �������� ���ٵ��� �ʽ��ϴ�.</td></tr>
<tr><td><img src="../../../admin/img/icon_list.gif" align="absmiddle">&nbsp;&nbsp;&nbsp;(�������������� ���ܵǴµ� �ټ� �ð��� �ɸ� �� ������ ���� ������ �ּ���.)</td></tr>
</table>
<br/>
<table cellpadding="1" cellspacing="0" border="0" class="small_ex">
<tr><td><img src="../../../admin/img/icon_list.gif" align="absmiddle">* �ڵ���� ������ �����μ���</td></tr>
<tr><td style="padding-left:10">- �ڵ� ��ǰ�� ���� �� ��, �����ڵ� �������� ���� �޴����� �� ����(���鰨��) ������ ��Ÿ�Ϸ� �����˴ϴ�. </td></tr>
<tr><td style="padding-left:10">- �����ΰ��� ���� Ʈ�� �׸��� �ڵ�(Set) ������ <a href="../../../admin/design/codi.php?design_file=setGoods/index.htm" target="_blank" 
style="color:#ffffff;font-weight:bold">[ �ڵ����������� ]</a> <a href="../../../admin/design/codi.php?design_file=setGoods/content.htm" target="_blank" style="color:#ffffff;font-weight:bold">[ �ڵ�������� ]</a> ���� �ش� �������� ���, ����, �ϴ��� ���̾ƿ� ������ ������ �����մϴ�. </td></tr>
</table>
<br/>
<table cellpadding="1" cellspacing="0" border="0" class="small_ex">
<tr><td><img src="../../../admin/img/icon_list.gif" align="absmiddle">* SNS������ ���</td></tr>
<tr><td style="padding-left:10">- <a href="/shop/admin/sns/sns.config.php" target="_blank" style="color:#ffffff;font-weight:bold">[ ���θ�� > SNS���� > SNS�����ϱ⼳������ ]</a>���� ��뼳���� ��������� ������, �ڵ� ������������ SNS�������� ��µ˴ϴ�. </td></tr>
<tr><td style="padding-left:10">- �ڵ� ���������� SNS������ ����� ������ ������ ���, �����ΰ��� ���� Ʈ�� �׸񿡼� </td></tr>
<tr><td style="padding-left:10">  &nbsp;&nbsp;<a href="../../../admin/design/codi.php?design_file=setGoods/content.htm" target="_blank" style="color:#ffffff;font-weight:bold">[ �ڵ�(Set) > �ڵ�������� ]</a> �� ���� ġȯ�ڵ� {snsBtn} �� ������ �ּ���.</td></tr>
<tr><td style="padding-left:10">- �������� ��Ų ������� �� �� ��Ų �߰��� SNS�����ϱ⸦ ������� �����Ͽ��� SNS��ư�� ��µ��� 
   ���� �� �ֽ��ϴ�. </td></tr>
<tr><td style="padding-left:10">- ��º��� ��� : SNS�����ϱ� ġȯ�ڵ� <b>{snsBtn}</b> �� �����ΰ��� <a href="../../../admin/design/codi.php?design_file=setGoods/content.htm" target="_blank" style="color:#ffffff;font-weight:bold">[ �ڵ�(Set) > �ڵ�������� ]</a> �� ���� 
   ���ϴ� ��ġ�� �ڵ带 ������ �ּ���.
</td></tr>
</table>
</div>
<script>cssRound('MSG01',null,null,'../../../admin/')</script>
<script>
table_design_load();

<?if($setGoodsConfig[state] == 'N'){?>cl_none();<?}?>
<?if($setGoodsConfig[state] == 'Y'){?>cl_use();<?}?>
</script>


</body>
</html>
