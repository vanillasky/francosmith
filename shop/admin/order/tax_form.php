<?

include "../../conf/config.pay.php";

$set = $set['tax'];

if(!$set['tax_delivery']) $set['tax_delivery'] = "n";

$checked['useyn'][$set[useyn]] = "checked";
$checked['step'][$set[step]] = "checked";
$checked['tax_delivery'][$set['tax_delivery']] = "checked";

$checked['use_a'][$set[use_a]] = "checked";
$checked['use_c'][$set[use_c]] = "checked";
$checked['use_o'][$set[use_o]] = "checked";
$checked['use_v'][$set[use_v]] = "checked";

?>

<form method=post action="../order/tax_indb.php" enctype="multipart/form-data">
<input type=hidden name=mode value="tax">

<div class="title title_top">세금계산서설정<span>회원에게 발행되는 세금계산서 대한 정책입니다</span> <a href="javascript:manual('<?=$guideUrl?>board/view.php?id=order&no=7')"><img src="../img/btn_q.gif" border=0 hspace=2 align=absmiddle></a></div>
<table class=tb>
<col class=cellC><col class=cellL>
<tr>
	<td>발행 사용여부</td>
	<td class=noline>
	<input type=radio name=useyn value='y' <?=$checked['useyn']['y']?>> 사용
	<input type=radio name=useyn value='n' <?=$checked['useyn']['n']?>> 사용안함
	</td>
</tr>
<tr>
	<td>발행 결제조건</td>
	<td class=noline>
	<input type=checkbox name=use_a <?=$checked['use_a']['on']?>> 무통장입금
	<input type=checkbox name=use_c <?=$checked['use_c']['on']?> disabled> 신용카드
	<input type=checkbox name=use_o <?=$checked['use_o']['on']?>> 계좌이체
	<input type=checkbox name=use_v <?=$checked['use_v']['on']?>> 가상계좌
	</td>
</tr>
<tr>
	<td>발행 시작단계</td>
	<td class=noline>
	<input type=radio name=step value='1' <?=$checked['step']['1']?>> 입금확인
	<input type=radio name=step value='2' <?=$checked['step']['2']?>> 배송준비중
	<input type=radio name=step value='3' <?=$checked['step']['3']?>> 배송중
	<input type=radio name=step value='4' <?=$checked['step']['4']?>> 배송완료
	</td>
</tr>
<tr>
	<td>배송비 포함여부</td>
	<td class=noline>
	<input type=radio name=tax_delivery value='y' <?=$checked['tax_delivery']['y']?>> 배송비 포함
	<input type=radio name=tax_delivery value='n' <?=$checked['tax_delivery']['n']?>> 배송비 비포함
	</td>
</tr>
<tr>
	<td>인감이미지</td>
	<td>
	<input type="file" name="seal_up" size="50" class=line><input type="hidden" name="seal" value="<?=$set[seal]?>">
	<a href="javascript:webftpinfo( '<?=( $set[seal] != '' ? '/data/skin/' . $cfg['tplSkin'] . '/img/common/' . $set[seal] : '' )?>' );"><img src="../img/codi/icon_imgview.gif" border="0" alt="이미지 보기" align="absmiddle"></a>
	<? if ( $set[seal] != '' ){ ?>&nbsp;&nbsp;<span class="noline"><input type="checkbox" name="seal_del" value="Y">삭제</span><? } ?>
	</td>
</tr>
</table>

<div class=button>
<input type=image src="../img/btn_save.gif">
<a href="javascript:history.back()"><img src="../img/btn_cancel.gif"></a>
</div>

</form>


<div id=MSG01>
<table cellpadding=1 cellspacing=0 border=0 class=small_ex>
<tr><td><img src="../img/icon_list.gif" align="absmiddle">신용카드 결제주문은 세금계산서를 발행하지 않습니다.</td></tr>
<tr><td style="padding-left:7pt;">2004년 개정된 부가가치세법에 의하면, 2004년 7월 1일 이후 신용카드로 결제된 건에 대해서는 세금 계산서 발행이 불가</font>하며 신용카드 매출전표로 부가가치세 신고</font>를 하셔야 합니다.<br>
[ 부가가치세법 시행령 57조 관련법규 참조 ]</td></tr>
<tr><td><img src="../img/icon_list.gif" align="absmiddle">인감이미지의 사이즈는 가로/세로 74 pixel로 만드시고, 파일종류는 JPG 또는 GIF파일로 만드세요.</td></tr>
<tr><td><img src="../img/icon_list.gif" align="absmiddle">일반세금계산서 발행방식 안내
<ol type="a" style="margin:0px 0px 0px 40px;">
<li>세금계산서를 수기로 작성한 후 우편발송이나 직접 전달하는 종이 세금계산서를 뜻합니다.</li>
<li>고도몰에서는 종이 세금계산서를 손쉽게 작성/전달 할 수 있도록 프린트 기능을 제공하고 있습니다.</li>
</ol>
</td></tr>
</table>
</div>
<script>cssRound('MSG01')</script>