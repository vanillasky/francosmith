<?
$location = "SNS 서비스 > 페이스북 연동 설정/관리";
include "../_header.php";
include	"../../lib/facebook.class.php";
$fb = new Facebook();
?>
<div class="title title_top">페이스북 연동 설정/관리 <span><a href="<?=$guideUrl?>board/view.php?id=event&no=20" target="_blank"><img src="../img/btn_q.gif"   align=absmiddle hspace=2/></a></div>

 <form name="form_page" method=post enctype="multipart/form-data" action="facebook.indb.php" onsubmit="return chk1()">
 <table class=tb border=0>
 <col class=cellC><col class=cellL>
	<tr>
		<td>페이스북 배너</td>
		<td><input type="file" name="facebook_btn[]" />&nbsp;<a href="javascript:facebook_recovery()"><img src="../img/btn_icon_return.gif" /></a><br/><br/>
		<?=$fb->fbButton()?><br/><br/>
		<div class="extext">아래 페이스북 페이지와 댓글이 들어있는 쇼핑몰 화면을 불러오는 배너입니다.<br/><br/><br/>
		배너를 넣을 위치를 정하고 스킨에 치환코드를 넣어서 사용하세요. <br/>
디자인에 따라 배너 형식 뿐만 아니라 메뉴처럼 넣을 수도 있으니 쇼핑몰 디자인에 따라 어울리게 준비하세요.
		</div>
		</td>
	</tr>
	<tr>
		<td>치환코드</td>
		<td><a href="javascript:clipboard('{fbbnr}')">{fbbnr}</a>
		</td>
	</tr>
</table>
<div style="padding-top:10px;padding-left:200px">
	<input type="image" src="../img/btn_save.gif" style="border:0" />	 
</div>
<br/><br/> 


	<input type="hidden" name="mode" value="page" />
	<div style="padding-top:10px;padding-bottom:5px">&nbsp;<img src="../img/icon_arrow.gif" /> <b>페이스북 설정</b></div>
	<table class=tb border=0>
	<col class=cellC><col class=cellL>
		<tr>
			<td>페이스북 페이지</td>
			<td >
				<input type="radio" name="useYn" value="y" style="border:none" <?if($fb->pageUseYn=='y')echo 'checked';?> />사용함 <input type="radio" name="useYn" value="n" style="border:none" <?if($fb->pageUseYn=='n')echo 'checked';?> />사용안함	 
			</td>
		</tr>
		<tr>
			<td>페이스북 주소</td>
			<td>http://facebook.com/ <input type="text" value="<?if($fb->pageAddr==''){echo $fb->defaultAddr;}else{ echo $fb->pageAddr;}?>" name="addr" class="line"   /></td>
		</tr>
		<tr>
			<td>페이스북 크기</td>
			<td>가로 <input type="text" value="<?=$fb->pageWidth?>" name="width" class="line" size="10"  onkeypress="NumObj(this);" style="ime-mode:disabled;" /> px / 
			세로 <input type="text" value="<?=$fb->pageHeight?>" name="height" class="line" size="10" onkeypress="NumObj(this);" style="ime-mode:disabled;"  /> px 
			<div class="extext">가로 폭 최소 너비는 292px, 세로 높이는 570px입니다.</div>
			</td>
		</tr>
		<tr>
			<td>테두리 색상</td>
			<td><input type="text" value="<?=$fb->pageBordercolor?>" name="bordercolor" class="line" size="10"   />  
				<div class="extext">스킨 색상에 따라 RGB 색상코드를 넣어 설정할 수 있습니다.</div>
			</td>
		</tr>
			<tr>
			<td>기능 선택</td>
			<td>
				<table>
					<tr>
						<td valign="top" width="80">
						  <input type="checkbox" checked disabled style="border:none"  /> 좋아요
						</td>
						<td>
							<img src="../img/setting1_o.jpg"   /> 
						</td>
					</tr>

					<tr>
						<td valign="top" width="80">
							<input type="checkbox" style="border:none" name="streamYn" value="true" <?if($fb->pageStreamYn=='true' || $fb->pageStreamYn=='' )echo 'checked';?> onclick="set_toggle(this,'setting2')" /> 게시글
						</td>
						<td>
							<img src="../img/<?if($fb->pageStreamYn=='true')echo "setting2_o.jpg";else echo "setting2_x.jpg";?>" id="setting2" />
						</td>
					</tr>
					<tr>
						<td valign="top">
							<input type="checkbox" style="border:none" name="facesYn" value="true" <?if($fb->pageFacesYn=='true' || $fb->pageFacesYn=='')echo 'checked';?> onclick="set_toggle(this,'setting3')"  /> 좋아한<br/>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;사람들
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
			<td>치환코드</td>
			<td><a href="javascript:clipboard('{facepage}')">{facepage}</a><br/>
				<div class="extext">페이지를 지정된 위치 이외에 넣고 싶을 경우, 스킨에 치환코드를 삽입하세요.</div>
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
					<b>페이지 주소</b><br/>
					페이스북 페이지를 만들 때 주소를 지정할 수 있습니다. (예. Facebook.com/<?= $fb->defaultAddr ?>)<br/>
					초기설정은 사용되는 모습을 보여드리기 위해 고도의 페이지를 임시로 넣어드렸습니다. 수정하여 사용하세요.<br/><br/>
					<br/>
					<b>테두리 색상 조절 </b><br/>
					테두리를 강조하고 싶으면 RGB 색상값을 지정하여 넣고, 테두리를 감추고 싶으면 스킨의 배경색과 동일한  
					색상으로(예를 들어 흰색 #FFFFFF) 설정하세요.
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
	<div style="padding-bottom:5px">&nbsp;<img src="../img/icon_arrow.gif" /> <b>댓글 설정</b></div>
	<table class=tb border=0 >
	<col class=cellC><col class=cellL>
		<tr>
			<td>댓글</td>
			<td >
				<input type="radio" name="useYn" value="y" style="border:none" <?if($fb->cmtUseYn=='y')echo 'checked';?> />사용함 <input type="radio" name="useYn" value="n" style="border:none" <?if($fb->cmtUseYn=='n')echo 'checked';?> />사용안함	 
			</td>
		</tr>
		<tr>
			<td>게시물 수</td>
			<td><input type="text" value="<?=$fb->cmtCount?>" name="count" class="line" onkeypress="NumObj(this);" style="ime-mode:disabled;" /> 개</td>
		</tr>
		<tr>
			<td>가로 폭</td>
			<td><input type="text" value="<?=$fb->cmtWidth?>" name="width" class="line" size="10" onkeypress="NumObj(this);" style="ime-mode:disabled;"  /> px 
			<div class="extext">최소 권장 너비는 470px입니다.</div>
			</td>
		</tr>
		<tr>
			<td>치환코드</td>
			<td><a href="javascript:clipboard('{facecmt}')">{facecmt}</a>
			<div class="extext">댓글을 지정한 위치 이외에 넣고 싶을 경우,&nbsp;&nbsp;스킨에 치환코드를 삽입하세요.</div>
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
					<b>치환코드 이용 방법 </b><br/>
					①삽입을 원하는  위치의 스킨 수정 페이지로 이동합니다. <br/>
					②치환코드를 복사하여 원하는 위치에 넣고 저장합니다. <br/>
					③쇼핑몰을 열어 페이스북이나 댓글을 확인합니다. <br/>
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
		alert('페이스북 페이지 사용여부를 체크해 주세요.');
		return false;
	}
}

function chk2(){
	var frm=document.form_cmt;
	if (!frm.useYn[0].checked && !frm.useYn[1].checked )
	{
		alert('페이스북 댓글 사용여부를 체크해 주세요.');
		return false;
	}
}

function NumObj(sip)
{
	if (event.keyCode >= 48 && event.keyCode <= 57) { //숫자키만 입력
		return true;
	} 
	else {
		 alert("숫자만 기입하세요");
		 event.returnValue = false;
	}
}

function clipboard(str){
    window.clipboardData.setData('Text',str);
    alert("클립보드에 복사되었습니다.");
}

function facebook_recovery(){
		document.form_page["facebook_btn[]"].value='';
		document.form_page.submit();
}
//-->
</script>
<?include "../_footer.php"; ?>