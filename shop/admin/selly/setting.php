<?
$location = "셀리 > 서비스 환경설정";
include "../_header.php";

### 사용자 설정 / 국가 설정
@include "../../conf/config.selly.php";
@include "../selly/code.php";

// 설정
$checked['delivery_type'][$selly['set']['delivery_type']] = "checked";
$selected['origin'][$selly['set']['origin']] = " selected";

// 인증여부 확인
list($selly['cust_cd']) = $db->fetch("SELECT value FROM gd_env WHERE category = 'selly' AND name = 'cust_cd'");
list($selly['cust_seq']) = $db->fetch("SELECT value FROM gd_env WHERE category = 'selly' AND name = 'cust_seq'");
list($selly['domain']) = $db->fetch("SELECT value FROM gd_env WHERE category = 'selly' AND name = 'domain'");
?>

<div class="title title_top">서비스 환경설정 <span>SELLY로 상품 전송을 위해, 내 쇼핑몰의 기본정보를 설정합니다. <a href="javascript:manual('<?=$guideUrl?>board/view.php?id=selly&no=2')"><img src="../img/btn_q.gif" align="absmiddle" /></a></div>

<form method="post" action="../selly/indb.php">
<input type="hidden" name="mode" value="set">

<? if(!$selly['cust_cd'] or !$selly['cust_seq']) { ?>
<div style="width:550px; border:3px #DCE1E1 solid; padding:10px; margin:10px 0px;">
<strong>※ 셀리를 신청하고 상점 인증 등록 후에 사용가능한 서비스입니다.</strong> <a href="../selly/index.php" class="extext" style="font-weight:bold;">[셀리 신청하러 가기]</a>
</div>
<? } ?>

<table class="tb">
<col class="cellC"><col class="cellL">
<tr height="35">
	<td>상점 인증</td>
	<td><a href="../selly/indb.php?mode=idshop"><img src="../img/btn_apply_shop.gif" align="absbottom" /></a> &nbsp;<span class="extext" ><? if(!$selly['cust_cd'] || !$selly['cust_seq'] || !$selly['domain']) { ?>* 인증에 성공하셨더라도 인증 정보가 유실되면 다시 인증등록을 요청해 주시기 바랍니다.<? } else { ?>인증 확인 되셨습니다.<? } ?></span></td>
</tr>
<tr height="35">
	<td>상품 카테고리</td>
	<td>
		<a href="../selly/indb.php?mode=category"><img src="../img/btn_apply_cate.gif" align="absbottom" /></a>
		&nbsp;<span class="extext">* 상품 카테고리를 등록하지 않으면 상품 DATA 전송시 오류가 발생합니다.</span>
	</td>
</tr>
<tr height="35">
	<td>배송비 정책</td>
	<td>
		<? foreach($selly['delivery_type'] as $k => $v) { ?>
		<input type="radio" name="delivery_type" value="<?=$k?>" class="null" <?=$checked['delivery_type'][$k]?> /> <?=$v?>
		<? } ?>
		<span style="margin-left:20px;">배송비 : </span><input type="text" name="delivery_price" value="<?=$selly['set']['delivery_price']?>" class="line" style="width:80px;" /> 원
	</td>
</tr>
<tr height="35">
	<td>원산지</td>
	<td>
		<select name="origin" id="origin">
			<option value="">= 원산지 =</option>
			<? foreach($selly['origin'] as $k => $v) { ?>
			<option value="<?=$k?>"<?=$selected['origin'][$k]?>><?=$v?></option>
			<? } ?>
		</select>
	</td>
</tr>
<tr height="35">
	<td>상점주소</td>
	<td>
		http://<?=$_SERVER['HTTP_HOST'].$cfg['rootDir']?> <a href="javascript:;" onclick="window.clipboardData.setData('Text', 'http://<?=$_SERVER['HTTP_HOST'].$cfg['rootDir']?>');alert('상점주소가 복사되었습니다.\n\n    Ctrl + V 를 사용해 붙여넣기 해주세요.\n\n※ 엑세스를 허용하지 않으신 경우는 복사되지 않습니다.');"><img src="../img/btn_catelink_copy.gif" align="absmiddle" /></a>
		&nbsp;&nbsp;&nbsp; <span class="extext">* 상점주소는 SELLY에서 쇼핑몰 등록시 사용되는 주소입니다.</span>
	</td>
</tr>
</table>

<div style="height:20px"></div>

<table cellpadding="0" cellspacing="0" width="650">
<tr>
	<td align="center"><input type="image" src="../img/btn_regist.gif" class="null"></td>
</tr>
</table>

<div style="height:20px"></div>

<div id="MSG01">
<table cellpadding="1" cellspacing="0" border="0" class="small_ex">
<tr><td height="5"></td></tr>
<tr><td>
<b>① 상점 인증/등록</b><br />
SELLY로 상품 DATA 전송은 먼저 SELLY에 가입 후 인증을 받아야 가능합니다.<br />
이 설정은 상점이 셀리 쪽에 인증요청을 보내 상품 DATA 전송시 필요한 인증 정보인 인증코드와 인증키를 받아 데이터에 저장하도록 하는 기능을 합니다.<br />
인증을 받지 않아 인증정보가 없으면 카테고리 및 상품 DATA를 전송할 수 없습니다.<br />
<br /><br />
<b>② 상품 카테고리 등록</b><br />
상점의 카테고리 정보를 SELLY로 전송합니다. 상점의 카테고리 정보가 수정되면 카테고리 등록을 다시 해야 하며, 카테고리를 등록하지 않은 경우 상품 DATA 전송시 오류가 발생합니다.<br />
상품 카테고리는 항상 가장 최신의 카테고리가 등록되어야 합니다.<br />
<br /><br />
<b>③ 배송비 정책</b><br />
e나무와 같이 SELLY에도 배송비 정책이 존재합니다. 기본으로 배송비 정책을 설정하여 SELLY로 등록하며, 판매상품 등록하기에서 각 상품별 배송비 정책 수정이 가능합니다.<br />
<br /><br />
<b>④ 원산지</b><br />
원산지 설정은 SELLY에서 상품판매시 사용되는 국가코드입니다.
</td></tr>
</table>
</div>
<script>cssRound('MSG01')</script>

</form>
<? include "../_footer.php"; ?>