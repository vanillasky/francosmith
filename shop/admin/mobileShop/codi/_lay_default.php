<?
/*------------------------------------------------------------------------------
ⓒ Copyright 2005, Flyfox All right reserved.
@파일내용: 디자인코디툴 > 레이아웃 > 전체레이아웃
@수정내용/수정자/수정일:
------------------------------------------------------------------------------*/
?>

<div style="padding-top:10;"></div>

<form method="post" name="fm" action="../mobileShop/codi/indb.php?mode=save&design_file=<?=$_GET['design_file']?>" onsubmit="return chkForm( this );" enctype="multipart/form-data">


<?
@include_once dirname(__FILE__) . "/_codi_map.php";
?>


<div style="margin:17px 0;"></div>


<div align=center class=noline>
	<input type=image src="../img/btn_save.gif" alt="저장하기">&nbsp;&nbsp;
	<a href="javascript:file_batch();"><img src="../img/btn_applyall.gif" border=0></a>
</div>

<div style="padding: 6px 0 25px 0" align=center><font color="#627dce">※</font> <font class="extext">[일괄적용]은 모든 페이지에 적용됩니다. 신중하게 저장하세요.</font></div>

</form>
