<?
$location = "SNS ���� > ���̽��� ���� ����/����";
include "../_header.php";
include	"../../lib/facebook.class.php";
$fb = new Facebook();
?>
<div class="title title_top">���̽��� ���� ����/���� <span><a href="<?=$guideUrl?>board/view.php?id=event&no=20" target="_blank"><img src="../img/btn_q.gif"   align=absmiddle hspace=2/></a></div>

 <form name="form_page" method=post enctype="multipart/form-data" action="facebook.indb.php" onsubmit="return chk1()">
 <table class=tb border=0>
 <col class=cellC><col class=cellL>
	<tr>
		<td>���̽��� ���</td>
		<td><input type="file" name="facebook_btn[]" />&nbsp;<a href="javascript:facebook_recovery()"><img src="../img/btn_icon_return.gif" /></a><br/><br/>
		<?=$fb->fbButton()?><br/><br/>
		<div class="extext">�Ʒ� ���̽��� �������� ����� ����ִ� ���θ� ȭ���� �ҷ����� ����Դϴ�.<br/><br/><br/>
		��ʸ� ���� ��ġ�� ���ϰ� ��Ų�� ġȯ�ڵ带 �־ ����ϼ���. <br/>
�����ο� ���� ��� ���� �Ӹ� �ƴ϶� �޴�ó�� ���� ���� ������ ���θ� �����ο� ���� ��︮�� �غ��ϼ���.
		</div>
		</td>
	</tr>
	<tr>
		<td>ġȯ�ڵ�</td>
		<td><a href="javascript:clipboard('{fbbnr}')">{fbbnr}</a>
		</td>
	</tr>
</table>
<div style="padding-top:10px;padding-left:200px">
	<input type="image" src="../img/btn_save.gif" style="border:0" />	 
</div>
<br/><br/> 


	<input type="hidden" name="mode" value="page" />
	<div style="padding-top:10px;padding-bottom:5px">&nbsp;<img src="../img/icon_arrow.gif" /> <b>���̽��� ����</b></div>
	<table class=tb border=0>
	<col class=cellC><col class=cellL>
		<tr>
			<td>���̽��� ������</td>
			<td >
				<input type="radio" name="useYn" value="y" style="border:none" <?if($fb->pageUseYn=='y')echo 'checked';?> />����� <input type="radio" name="useYn" value="n" style="border:none" <?if($fb->pageUseYn=='n')echo 'checked';?> />������	 
			</td>
		</tr>
		<tr>
			<td>���̽��� �ּ�</td>
			<td>http://facebook.com/ <input type="text" value="<?if($fb->pageAddr==''){echo $fb->defaultAddr;}else{ echo $fb->pageAddr;}?>" name="addr" class="line"   /></td>
		</tr>
		<tr>
			<td>���̽��� ũ��</td>
			<td>���� <input type="text" value="<?=$fb->pageWidth?>" name="width" class="line" size="10"  onkeypress="NumObj(this);" style="ime-mode:disabled;" /> px / 
			���� <input type="text" value="<?=$fb->pageHeight?>" name="height" class="line" size="10" onkeypress="NumObj(this);" style="ime-mode:disabled;"  /> px 
			<div class="extext">���� �� �ּ� �ʺ�� 292px, ���� ���̴� 570px�Դϴ�.</div>
			</td>
		</tr>
		<tr>
			<td>�׵θ� ����</td>
			<td><input type="text" value="<?=$fb->pageBordercolor?>" name="bordercolor" class="line" size="10"   />  
				<div class="extext">��Ų ���� ���� RGB �����ڵ带 �־� ������ �� �ֽ��ϴ�.</div>
			</td>
		</tr>
			<tr>
			<td>��� ����</td>
			<td>
				<table>
					<tr>
						<td valign="top" width="80">
						  <input type="checkbox" checked disabled style="border:none"  /> ���ƿ�
						</td>
						<td>
							<img src="../img/setting1_o.jpg"   /> 
						</td>
					</tr>

					<tr>
						<td valign="top" width="80">
							<input type="checkbox" style="border:none" name="streamYn" value="true" <?if($fb->pageStreamYn=='true' || $fb->pageStreamYn=='' )echo 'checked';?> onclick="set_toggle(this,'setting2')" /> �Խñ�
						</td>
						<td>
							<img src="../img/<?if($fb->pageStreamYn=='true')echo "setting2_o.jpg";else echo "setting2_x.jpg";?>" id="setting2" />
						</td>
					</tr>
					<tr>
						<td valign="top">
							<input type="checkbox" style="border:none" name="facesYn" value="true" <?if($fb->pageFacesYn=='true' || $fb->pageFacesYn=='')echo 'checked';?> onclick="set_toggle(this,'setting3')"  /> ������<br/>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;�����
						</td>
						<td>
							<img src="../img/<?if($fb->pageFacesYn=='true')echo "setting3_o.jpg";else echo "setting3_x.jpg";?>"  id="setting3" />
						</td>
					</tr>
				</table>
			</td>
		</tr>
		</tr>
		<tr>
			<td>ġȯ�ڵ�</td>
			<td><a href="javascript:clipboard('{facepage}')">{facepage}</a><br/>
				<div class="extext">�������� ������ ��ġ �̿ܿ� �ְ� ���� ���, ��Ų�� ġȯ�ڵ带 �����ϼ���.</div>
			</td>
		</tr>
	</table>
	<div style="padding-top:10px;padding-left:200px">
		<input type="image" src="../img/btn_save.gif" style="border:0" />	 
	</div>
</form>

<div style="padding-top:5px"></div>
<div id=MSG01 >
	<table cellpadding=2 cellspacing=0 border=0 class=small_ex>
		<tr>
			<td>
				<div style="line-height:15px">
					<b>������ �ּ�</b><br/>
					���̽��� �������� ���� �� �ּҸ� ������ �� �ֽ��ϴ�. (��. Facebook.com/<?= $fb->defaultAddr ?>)<br/>
					�ʱ⼳���� ���Ǵ� ����� �����帮�� ���� ���� �������� �ӽ÷� �־��Ƚ��ϴ�. �����Ͽ� ����ϼ���.<br/><br/>
					<br/>
					<b>�׵θ� ���� ���� </b><br/>
					�׵θ��� �����ϰ� ������ RGB ������ �����Ͽ� �ְ�, �׵θ��� ���߰� ������ ��Ų�� ������ ������  
					��������(���� ��� ��� #FFFFFF) �����ϼ���.
				</div>
			</td>
		</tr>
	</TABLE>
</div>

<!---->
<div style="padding-top:65px"></div>
<form name="form_cmt" method=post action="facebook.indb.php" onsubmit="return chk2()">
	<input type="hidden" name="mode" value="cmt" />
	<a name="#cmt"></a>
	<div style="padding-bottom:5px">&nbsp;<img src="../img/icon_arrow.gif" /> <b>��� ����</b></div>
	<table class=tb border=0 >
	<col class=cellC><col class=cellL>
		<tr>
			<td>���</td>
			<td >
				<input type="radio" name="useYn" value="y" style="border:none" <?if($fb->cmtUseYn=='y')echo 'checked';?> />����� <input type="radio" name="useYn" value="n" style="border:none" <?if($fb->cmtUseYn=='n')echo 'checked';?> />������	 
			</td>
		</tr>
		<tr>
			<td>�Խù� ��</td>
			<td><input type="text" value="<?=$fb->cmtCount?>" name="count" class="line" onkeypress="NumObj(this);" style="ime-mode:disabled;" /> ��</td>
		</tr>
		<tr>
			<td>���� ��</td>
			<td><input type="text" value="<?=$fb->cmtWidth?>" name="width" class="line" size="10" onkeypress="NumObj(this);" style="ime-mode:disabled;"  /> px 
			<div class="extext">�ּ� ���� �ʺ�� 470px�Դϴ�.</div>
			</td>
		</tr>
		<tr>
			<td>ġȯ�ڵ�</td>
			<td><a href="javascript:clipboard('{facecmt}')">{facecmt}</a>
			<div class="extext">����� ������ ��ġ �̿ܿ� �ְ� ���� ���,&nbsp;&nbsp;��Ų�� ġȯ�ڵ带 �����ϼ���.</div>
			</td>
		</tr> 
	</table>
	<div style="padding-top:10px;padding-left:200px">
		<input type="image" src="../img/btn_save.gif" style="border:0" />
	</div>
</form>
<!---->

<div style="padding-top:5px"></div>
<div id=MSG02 >
	<table cellpadding=2 cellspacing=0 border=0 class=small_ex>
		<tr>
			<td>
				<div style="line-height:15px">
					<b>ġȯ�ڵ� �̿� ��� </b><br/>
					������� ���ϴ�  ��ġ�� ��Ų ���� �������� �̵��մϴ�. <br/>
					��ġȯ�ڵ带 �����Ͽ� ���ϴ� ��ġ�� �ְ� �����մϴ�. <br/>
					����θ��� ���� ���̽����̳� ����� Ȯ���մϴ�. <br/>
					<br/>
				</div>
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
function set_toggle(obj,objId){
	var imgname=document.getElementById(objId).src
	imgname_on=imgname.replace('_x','_o');
	imgname_off=imgname.replace('_o','_x');
	if (obj.checked)
		document.getElementById(objId).src=imgname_on;
	else
		document.getElementById(objId).src=imgname_off;
}
function chk1(){
	var frm=document.form_page;
	if (!frm.useYn[0].checked && !frm.useYn[1].checked )
	{
		alert('���̽��� ������ ��뿩�θ� üũ�� �ּ���.');
		return false;
	}
}

function chk2(){
	var frm=document.form_cmt;
	if (!frm.useYn[0].checked && !frm.useYn[1].checked )
	{
		alert('���̽��� ��� ��뿩�θ� üũ�� �ּ���.');
		return false;
	}
}

function NumObj(sip)
{
	if (event.keyCode >= 48 && event.keyCode <= 57) { //����Ű�� �Է�
		return true;
	} 
	else {
		 alert("���ڸ� �����ϼ���");
		 event.returnValue = false;
	}
}

function clipboard(str){
    window.clipboardData.setData('Text',str);
    alert("Ŭ�����忡 ����Ǿ����ϴ�.");
}

function facebook_recovery(){
		document.form_page["facebook_btn[]"].value='';
		document.form_page.submit();
}
//-->
</script>
<?include "../_footer.php"; ?>