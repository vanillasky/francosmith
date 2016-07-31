<?
$location = "SNS 서비스 > 페이스북 연동 설정";
@include "../../lib/facebook.class.php";
include "../_header.php";
$fb = new Facebook();
?>
<div class="title title_top">페이스북 연결 설정  <!--<span> <a href="<?=$guideUrl?>board/view.php?id=marketing&no=31" target="_blank"> <img src="../img/btn_q.gif"  /></a>--></div>

 <form name="form_page" method=post action="mobFacebook.indb.php"   enctype="multipart/form-data" >
	<input type="hidden" name="mode" value="page" />
	<div class="extext"><b>페이스북 설정</b></div>
	<table class=tb border=0>
	<col class=cellC><col class=cellL>
		<tr>
			<td>페이스북 주소</td>
			<td>http://facebook.com/ <input type="text" value="<?if($fb->mbAddr==''){echo $fb->defaultAddr;}else{echo $fb->mbAddr;} ?>" name="addr" class="line"   /></td>
		</tr>
		<tr>
			<td>연결 이미지</td>
			<td><input type="file" name="facebook_btn[]" />&nbsp;<a href="javascript:facebook_recovery()"><img src="../img/btn_icon_return.gif" /></a><br/><br/>
			<?=$fb->mbfbButton()?>
			</td>
		</tr>
		<tr>
			<td>치환코드</td>
			<td><a href="javascript:clipboard('{mfbbnr}')">{mfbbnr}</a>
			</td>
		</tr> 
	</table>
	<div style="padding-top:10px;padding-left:200px">
		<input type="image" src="../img/btn_save.gif" style="border:0" />	 
	</div>
</form>
  
<script type="text/javascript">
<!--
	table_design_load();
//-->
</script>
<script type="text/javascript">
<!--
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