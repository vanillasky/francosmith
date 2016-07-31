<?php /* Template_ 2.2.7 2013/05/27 11:58:31 /www/francotr3287_godo_co_kr/shop/data/skin_mobileV2/default/myp/emoneylist.htm 000003721 */ 
if (is_array($TPL_VAR["loop"])) $TPL_loop_1=count($TPL_VAR["loop"]); else if (is_object($TPL_VAR["loop"]) && in_array("Countable", class_implements($TPL_VAR["loop"]))) $TPL_loop_1=$TPL_VAR["loop"]->count();else $TPL_loop_1=0;?>
<?php $this->print_("header",$TPL_SCP,1);?>


<?php  $TPL_VAR["page_title"] = "적립금내역";?>
<?php $this->print_("sub_header",$TPL_SCP,1);?>


<style type="text/css">
section#nemoneylist{background:#FFFFFF;}

section#content-wrap { padding:12px; background:#FFFFFF; }
section#nemoneylist {background-color:#FFFFFF;min-height:100%; }
section#nemoneylist table {width:100%; }
/*section#coupon-list table th {height:50px; border-bottom:solid 1px #bbbbbb; background-color:#cccccc; line-height:50px; font-size:16px; text-align:center; border-right:solid 1px #bbbbbb; color:#444444;}*/
section#nemoneylist th { font-size:12px; font-weight:bold; color:#353535; height:33px; line-height:33px; background:url(/shop/data/skin_mobileV2/default/common/img/nmyp/bdtit_bg.png) repeat-x; border-right:solid 1px #DBDBDB; font-family:Dotum; }
section#nemoneylist th.first{ border-left : solid 1px #DBDBDB;}
section#nemoneylist td { height:65px; border-right:solid 1px #DBDBDB; border-bottom:solid 1px #DBDBDB; font-size:14px; text-align:center; color:#353535; background-color:#FFFFFF; font-family:Dotum; vertical-align:middle; }
section#nemoneylist td.first{ border-left : solid 1px #DBDBDB;}
section#nemoneylist td.left { padding-left:10px; }
section#nemoneylist .name { font-size:14px; color:#353535; height:25px; line-height:25px; vertical-align:bottom; overflow:hidden; text-align:left; margin-top:12px; }
section#nemoneylist .remain { font-size:12px; height:20px; line-height:20px; vertical-align:top; color:#F03C3C; text-align:left; margin-bottom:8px; }
section#nemoneylist .remain span { font-weight:bold; }
section#nemoneylist .notused { font-weight:bold; color:#F03C3C; }
section#nemoneylist .used { font-weight:bold; color:#797979; }
section#nemoneylist .nolist { border-right:none; }

section#nemoneylist .more-btn {width:296px; margin:auto; background:url("/shop/data/skin_mobileV2/default/common/img/nlist/btn_more_off.png") no-repeat; text-align:center; height:38px; color:#ffffff; line-height:38px; font-size:14px; margin-top:18px; }
section#nemoneylist .more-btn:active {background:url("/shop/data/skin_mobileV2/default/common/img/nlist/btn_more_on.png") no-repeat; }{background:url('/shop/data/skin_mobileV2/default/common/img/nmyp/btn_view_on.png') no-repeat;}

</style>

<script type="text/javascript">
$(document).ready(function(){
	if($("#emoney-table .emoney-item").length < 10) {
		$(".more-btn").hide();
	}
});
</script>

<section id="content-wrap">
<section id="nemoneylist" class="content">
	<table id="emoney-table">
	<tr>
		<th class="first">내 역</th>
		<th width="100px">적립금액</th>
		<th width="100px">사용금액</th>
	</tr>
<?php if($TPL_loop_1){foreach($TPL_VAR["loop"] as $TPL_V1){?>
	
	<tr class="emoney-item">
		<td class="left first"><?php echo $TPL_V1["memo"]?></td>
		<td class="right"><?php if($TPL_V1["emoney"]> 0){?><?php echo number_format($TPL_V1["emoney"])?>원<?php }?></td>
		<td class="right"><?php if($TPL_V1["emoney"]< 0){?><?php echo number_format($TPL_V1["emoney"])?>원<?php }?></td>
	</tr>
	
<?php }}else{?>
	<tr >
		<td class="left first" colspan="3">적립금 내역이 없습니다.</td>
	</tr>

<?php }?>
	</table>

	<div class="more-btn" onclick="javascript:getLogEmoney();">더보기</div>
</section>
</section>
<?php $this->print_("footer",$TPL_SCP,1);?>