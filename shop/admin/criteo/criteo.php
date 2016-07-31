<?
$location = "크리테오 설정/관리";
@include "../../conf/criteo.cfg.php";
include "../_header.php";
?>
<div class="title title_top">크리테오 설정/관리<span><a href="<?=$guideUrl?>board/view.php?id=marketing&no=31" target="_blank"><img src="../img/btn_q.gif"  /></a></div>
<form name=form method=post action="indb.php" onsubmit="return chk()">
	<div class="extext">크리테오 설정은 크리테오를 신청한 후에 이용할 수 있습니다.(고도나 타사신청 모두 이용 가능)</div>
	<table class=tb border=0>
	<col class=cellC><col class=cellL>
		<tr>
			<td>WI 코드</td>
			<td >
				<label><input type="text" value="<?=$criteo['wi_code1']?>" name="wi_code1" class="line"  size="10"/> / <input type="text" class="line"    name="wi_code2" size="10" value="<?=$criteo['wi_code2']?>"/>   (크리테오 상품/거래 추적 코드)
				</label>
			</td>
		</tr>
		<tr>
			<td>P 코드</td>
			<td><input type="text" name="p_code" class="line" size="10" value="<?=$criteo['p_code']?>" /> (크리테오 파트너 코드)</td>
		</tr>
	</table>
	<div style="padding-top:10px;padding-left:200px">
		<input type="image" src="../img/btn_save.gif" style="border:0" />
	 
	</div>
</form>
 
<table width=100% cellpadding=0 cellspacing=0 style="margin-top:15px"  class=tb >
	<col class=cellC><col style="padding:5px 10px;line-height:140%">
	<tr>
		<td>제품 URL</td>
		<td>
			제품URL을 광고 담당자에게 전달하십시오.<br/>
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
						<td><b><span class="color_ffe">크리테오 광고 과정</span></b></td>
					</tr> 
				</table>
				<div style="line-height:15px">
					1. 크리테오 광고신청 및 입금후 계약이 완료되면 광고담당자로부터 WI 코드와 P 코드를 받게 됩니다. 코드를 입력하고 [저장]하십시오.<br/>
					2. 제품URL을 복사하여 광고담당자에게 전달하십시오. <br/>
					3. 제품URL을 전달하면 광고담당자가 이미지 배너를 보내드립니다(2일 이내). 배너 확인 후 광고담당자에게 이상이 없다고 승인하면 1일 후에 광고가 시작됩니다.<br/>  
					4. 크리테오 광고 시작시 크리테오에서 광고 계정이 활성화되었다는 메일을 보내드립니다.
<br/><br/><br/>
								크리테오 문의 : 고도소프트 마케팅팀 02-567-3719
			</div>
				 			</td>
		</tr>
	</TABLE>
</div>
<div style="padding-top:15px"></div>

<div class="title title_top">크리테오 관리<span>  </div>

<div><a href="http://advertising.criteo.com" target="_blank" class="extext" ><img src="../img/btn_cre_go.gif" /></a></div>
<div id=MSG02>
	<table cellpadding=2 cellspacing=0 border=0 class=small_ex>
		<tr>
			<td> 
			크리테오 광고 관리는 크리테오 관리 사이트(<a href="http://advertising.criteo.com" target="_blank" class="extext" ><span class="color_ffe">http://advertising.criteo.com</span></a>)를 이용하십시오. 광고담당자가 ID/비밀번호 발급해드립니다.
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
		if (!confirm('저장된 코드가 삭제됩니다. 저장하시겠습니까?'))
		{
			form.reset();
			return false;
		}
	}
}
//-->
</script>