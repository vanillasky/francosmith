<?
$location = "ũ���׿� ����/����";
@include "../../conf/criteo.cfg.php";
include "../_header.php";
?>
<div class="title title_top">ũ���׿� ����/����<span><a href="<?=$guideUrl?>board/view.php?id=marketing&no=31" target="_blank"><img src="../img/btn_q.gif"  /></a></div>
<form name=form method=post action="indb.php" onsubmit="return chk()">
	<div class="extext">ũ���׿� ������ ũ���׿��� ��û�� �Ŀ� �̿��� �� �ֽ��ϴ�.(���� Ÿ���û ��� �̿� ����)</div>
	<table class=tb border=0>
	<col class=cellC><col class=cellL>
		<tr>
			<td>WI �ڵ�</td>
			<td >
				<label><input type="text" value="<?=$criteo['wi_code1']?>" name="wi_code1" class="line"  size="10"/> / <input type="text" class="line"    name="wi_code2" size="10" value="<?=$criteo['wi_code2']?>"/>   (ũ���׿� ��ǰ/�ŷ� ���� �ڵ�)
				</label>
			</td>
		</tr>
		<tr>
			<td>P �ڵ�</td>
			<td><input type="text" name="p_code" class="line" size="10" value="<?=$criteo['p_code']?>" /> (ũ���׿� ��Ʈ�� �ڵ�)</td>
		</tr>
	</table>
	<div style="padding-top:10px;padding-left:200px">
		<input type="image" src="../img/btn_save.gif" style="border:0" />
	 
	</div>
</form>
 
<table width=100% cellpadding=0 cellspacing=0 style="margin-top:15px"  class=tb >
	<col class=cellC><col style="padding:5px 10px;line-height:140%">
	<tr>
		<td>��ǰ URL</td>
		<td>
			��ǰURL�� ���� ����ڿ��� �����Ͻʽÿ�.<br/>
			<font color="57a300"><a href="../../partner/criteoGoods.html" target=_blank><font class=ver8>http://<?=$_SERVER['HTTP_HOST'].$cfg[rootDir]?>/shop/partner/criteoGoods.html</font> </a><br>
		</td>
	</tr>
</table>

<div style="padding-top:15px"></div>
<div id=MSG01 >
	<table cellpadding=2 cellspacing=0 border=0 class=small_ex>
		<tr>
			<td>
			 
				<table>
					<tr>
						<td><b><span class="color_ffe">ũ���׿� ���� ����</span></b></td>
					</tr> 
				</table>
				<div style="line-height:15px">
					1. ũ���׿� �����û �� �Ա��� ����� �Ϸ�Ǹ� �������ڷκ��� WI �ڵ�� P �ڵ带 �ް� �˴ϴ�. �ڵ带 �Է��ϰ� [����]�Ͻʽÿ�.<br/>
					2. ��ǰURL�� �����Ͽ� �������ڿ��� �����Ͻʽÿ�. <br/>
					3. ��ǰURL�� �����ϸ� �������ڰ� �̹��� ��ʸ� �����帳�ϴ�(2�� �̳�). ��� Ȯ�� �� �������ڿ��� �̻��� ���ٰ� �����ϸ� 1�� �Ŀ� ���� ���۵˴ϴ�.<br/>  
					4. ũ���׿� ���� ���۽� ũ���׿����� ���� ������ Ȱ��ȭ�Ǿ��ٴ� ������ �����帳�ϴ�.
<br/><br/><br/>
								ũ���׿� ���� : ������Ʈ �������� 02-567-3719
			</div>
				 			</td>
		</tr>
	</TABLE>
</div>
<div style="padding-top:15px"></div>

<div class="title title_top">ũ���׿� ����<span>  </div>

<div><a href="http://advertising.criteo.com" target="_blank" class="extext" ><img src="../img/btn_cre_go.gif" /></a></div>
<div id=MSG02>
	<table cellpadding=2 cellspacing=0 border=0 class=small_ex>
		<tr>
			<td> 
			ũ���׿� ���� ������ ũ���׿� ���� ����Ʈ(<a href="http://advertising.criteo.com" target="_blank" class="extext" ><span class="color_ffe">http://advertising.criteo.com</span></a>)�� �̿��Ͻʽÿ�. �������ڰ� ID/��й�ȣ �߱��ص帳�ϴ�.
			</td>
		</tr>
	</TABLE>
</div>



<script>cssRound('MSG01','#F7F7F7');cssRound('MSG02','#F7F7F7');</script>
<script type="text/javascript">
<!--
	table_design_load();
//-->
</script>
<script type="text/javascript">
<!--
function chk(){
	if (form.wi_code1.value=='' && form.wi_code2.value==''&&form.p_code.value=='')
	{
		if (!confirm('����� �ڵ尡 �����˴ϴ�. �����Ͻðڽ��ϱ�?'))
		{
			form.reset();
			return false;
		}
	}
}
//-->
</script>