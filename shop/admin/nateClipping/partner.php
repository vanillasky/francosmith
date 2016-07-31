<?
@include "../../conf/partner.php";
$location = "싸이월드 스크랩 서비스 > 싸이월드 스크랩 설정/관리";
include "../_header.php";
$curl = "../../conf/nateClipping.cfg.php";
if(file_exists($curl)) require $curl;

if(!$nateClipping['imgWidth'])$nateClipping['imgWidth']=$cfg['img_m'];
if(!$nateClipping['imgHeight'])$nateClipping['imgHeight']=$cfg['img_m'];

if($nateClipping['status'] == 2)$tag[0]="<img src=\"../img/natescrap2.gif\"/>";
else $tag[0]="<img src=\"../img/natescrap2off.gif\"/>";
if($nateClipping['status'] == 3){
	$tag[1]="<img src=\"../img/../img/natescrap3link.gif\"/>";
	$tag[2]="<img src=\"../img/natescrap3.gif\"/>";
}else{
	$tag[1]="<img src=\"../img/../img/natescrap3link.gif\"/>";
	$tag[2]="<img src=\"../img/natescrap3off.gif\"/>";
}
if($nateClipping['status'] == 4){
	$tag[1]="<img src=\"../img/natescrap4link.gif\"/>";
	$tag[2]="<img src=\"../img/natescrap4.gif\"/>";
}

?>
<script type="text/javascript">
function chk_proContentsLink(){
	var chk = document.getElementById('chklink');
	var obj = document.getElementById('proContentsLink');
	if(chk.checked == true){
		obj.value = "<?php echo $cfg['shopUrl'];?>";
	}
}
function copy_txt(val){
	window.clipboardData.setData('Text', val);
}
</script>
<div style="width:800">
<div class="title title_top">싸이월드 스크랩 서비스 상태 <a href="javascript:manual('<?=$guideUrl?>board/view.php?id=marketing&no=19')"><img src="../img/btn_q.gif" border=0 align=absmiddle hspace=2></a></div>
<div style="float:left;"><img src="../img/natescrap0.gif"/></div>
<div style="float:left;padding:0 20 0 20"><img src="../img/shopplus_process_arrow.gif"/></div>
<div style="float:left;"><?php echo $tag[0];?></div>
<div style="float:left;padding:0 20 0 20"><img src="../img/shopplus_process_arrow.gif"/></div>
<div style="float:left"><?php echo $tag[1];?></div>
<div style="float:left;padding:0 20 0 20"><img src="../img/shopplus_process_arrow.gif"/></div>
<div style="float:left"><?php echo $tag[2];?></div>
<div style="clear:both;" id=MSG01>
<table cellpadding=1 cellspacing=0 border=0 class="small_ex">
<?php if($nateClipping['status']!=3){?>
<tr>
<td>
	<div>싸이월드 스크랩 서비스 신청을 하시면 싸이월드의 담당자가 신청 쇼핑몰에 대하여 심사에 들어갑니다.</div>
	<div>심사기간은 약2일 정도 소요되며, 심사를 통과하여 승인을 받으면 바로 싸이월드 스크랩 서비스를 사용하실 수 있습니다.</div>
</td>
</tr>
<?php }?>
<?php if($nateClipping['status']==3){?>
<tr><td><div>정상적으로 서비스 승인을 받으셨습니다.</div>
<div>해당 쇼핑몰에 스크랩 서비스 치환코드를 입력하시면 바로 싸이월드 스크랩 서비스를 이용하실 수 있습니다.</div></td></tr>
<?php }?>
</table>
</div>
<script>cssRound('MSG01')</script>
<div style="height:20"></div>
<input type="hidden" name="copy" id="copy" value=""/>
<form method="post" action="indb.php" enctype="multipart/form-data" target="ifrmHidden" <?php if($nateClipping['status']!='3')echo "disabled";?>>
<div class="title title_top">쇼핑몰에 싸이월드 스크랩 버튼 삽입하기 <a href="javascript:manual('<?=$guideUrl?>board/view.php?id=marketing&no=19')"><img src="../img/btn_q.gif" border=0 align=absmiddle></a></div>
<table border="1" bordercolor="#e1e1e1" style="border-collapse:collapse" width="100%">
<col class="cellC"><col class="cellL">
<tr>
	<td>스크랩 버튼 이미지</td>
	<td>
	<div style="padding:2 0 0 0">
	<?php if($nateClipping['scrapBt']){?>
	<img src="../../data/skin/<?php echo $cfg['tplSkin'];?>/img/<?php echo $nateClipping['scrapBt'];?>" width="127" border="0"/>
	<?php }else{?>
	<img src="../img/natescrab_btn.gif" width="127" border="0"/>
	<?php }?>
	</div>
	<div style="padding:0 0 2 0">
	<input type="file" name="scrapbt"/> <span class="small1 extext">(권장사이즈 127 x 20)</span>
	</div>
	</td>
</tr>
<tr>
	<td height="50">스크랩 기능 치환코드</td>
	<td>
	<div div style="padding-top:5;">{cyworldScrap} <img class="hand" src="../img/i_copy.gif" onclick="copy_txt('{cyworldScrap}')" alt="복사하기" align="absmiddle"/></div>
	<div style="padding-top:10;" class="small1 extext">
	<div>복사하신 <b>치환코드</b>를 상품상세화면에 삽입하시면 싸이월드 스크랩 기능이 동작합니다.</div>
	</div>
	</td>
</tr>
<tr>
	<td><div>스크랩 기능 치환코드</div><div style="padding:5 0 5 0">삽입 방법</div></td>
	<td>
	<div style="padding-top:5">삽입되는 소스 페이지 : <a href="../../admin/design/codi.php" target="_blank">"쇼핑몰 관리자 > 디자인관리"</a> 좌측 트리 메뉴에서 "상품 > 상품상세화면" 메뉴 클릭</div>
	<div style="padding:5 0 5 0">치환코드 삽입 위치 : [바로구매] 버튼 위 또는 아래에 치환코드 삽입을 권장합니다.</div>
	</td>
</tr>
</table>
<p/>
<div class="title title_top">싸이월드에 스크랩되는 상품 데이터 설정하기 <a href="javascript:manual('<?=$guideUrl?>board/view.php?id=marketing&no=19')"><img src="../img/btn_q.gif" border=0 align=absmiddle></a></div>
<table border="1" bordercolor="#e1e1e1" style="border-collapse:collapse" width="100%">
<col class="cellC"><col class="cellL">
<tr>
	<td>상품 이미지</td>
	<td>
	<div style="padding:5 0 0 0">상세이미지가 자동 전송</div>
	<div style="padding:2 0 0 0" class="small1 extext">단, 이미지호스팅 주소(URL)를 상품이미지로 사용하실 경우 스크랩되는 이미지 사이즈를 설정하셔야 합니다.</div>
	<div style="padding:3 0 2 0">이미지호스팅용 상품이미지 사이즈 : <input type="text" size="3" name="imgWidth" value="<?php echo $nateClipping['imgWidth'];?>"/> x <input type="text" name="imgHeight" value="<?php echo $nateClipping['imgHeight'];?>" size="3"/></div>
	</td>
</tr>
<tr>
	<td height="30">상품 정보</td>
	<td>짧은설명,판매가,적립금이 자동 전송</td>
</tr>
</table>
<p/>
<div class="title title_top">싸이월드에 스크랩되는 추가 데이터 설정하기 (공통 사용) <a href="javascript:manual('<?=$guideUrl?>board/view.php?id=marketing&no=19')"><img src="../img/btn_q.gif" border=0 align=absmiddle></a></div>
<div style="font:8pt dotum;color:#6D6D6D;padding:0 0 3 0"><span>싸이월드 스크랩 기능은 스크랩 되는 상품정보 외 추가로 쇼핑몰을 홍보하실 수 있습니다. 아래에서 추가 정보를 설정하세요.</span></div>
<table border="1" bordercolor="#e1e1e1" style="border-collapse:collapse" width="100%">
<col class="cellC"><col class="cellL">
<tr>
	<td>로고 이미지</td>
	<td>
	<div style="padding:2 0 0 0">
	<?php if($nateClipping['logo']){?>
	<img src="../../data/skin/<?php echo $cfg['tplSkin'];?>/img/<?php echo $nateClipping['logo'];?>" width="50" border="0"/>
	<?php }else{?>
	<img src="http://gongji.godo.co.kr/userinterface/clipping/images/logo_godo.gif" width="50" border="0"/>
	<?php }?>
	</div>
	<div style="padding:0 0 2 0">
	<input type="file" name="logo"/> <span class="small1 extext">(이미지 사이즈 50 x 20)</span>
	</div>
	</td>
</tr>
<tr>
	<td>쇼핑몰 한줄 소개</td>
	<td>
	<div style="padding:2 0 0 0">
	<input type="text" name="proContents" style="width:350px" maxlength="100" value="<?php echo $nateClipping['proContents'];?>"/>
	</div>
	<div style="padding:0 0 2 0">
	<div class="noline"><label><input type="checkbox" id="chklink" name="chklink" onclick="chk_proContentsLink()"/> 대표도메인등록</label></div>
	<div>링크  : http://<input type="text" style="width:313px" id="proContentsLink" name="proContentsLink" value="<?php echo $nateClipping['proContentsLink'];?>"/></div>
	</div>
	</td>
</tr>

</table>
<div class=button>
<?php if($nateClipping['status']!='3'){?>
<img type=image src="../img/btn_save.gif">
<?php }else{?>
<input type="image" src="../img/btn_save.gif">
<?php }?>
<a href="javascript:history.back()"><img src="../img/btn_cancel.gif"></a>
</div>
</form>
<div id=MSG02>
<table cellpadding=1 cellspacing=0 border=0 class=small_ex>
<tr><td>스크랩 이미지를 스킨별로 변경하실 수 있습니다.</td></tr>
<tr><td>서비스 상태가 서비스시작인 경우에만 스크랩 기능이 활성화 됩니다.</td></tr>
</table>
</div>
<script>cssRound('MSG02')</script>

</div>
<? include "../_footer.php"; ?>