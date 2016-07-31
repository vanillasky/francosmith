<?php /* Template_ 2.2.7 2013/05/27 11:58:31 /www/francotr3287_godo_co_kr/shop/data/skin_mobileV2/default/proc/faq.htm 000004305 */ 
if (is_array($GLOBALS["loop"])) $TPL__loop_1=count($GLOBALS["loop"]); else if (is_object($GLOBALS["loop"]) && in_array("Countable", class_implements($GLOBALS["loop"]))) $TPL__loop_1=$GLOBALS["loop"]->count();else $TPL__loop_1=0;?>
<?php $this->print_("header",$TPL_SCP,1);?>


<script type="text/javascript">
$(document).ready(function(){
	$("#faq_cate option:eq(0)").attr("selected", "selected").trigger("change");
});
</script>

<style type="text/css">
html, body{ width:100%; height:100%; margin:0; padding:0; background-color:#ffffff;}

#faq_category { padding-top:10px; height:30px; text-align:center; background-color:#ECECEC; }

#faq_cate { width:95%; }

.faq { font-size:12px; margin:12px; }
.faq ul { list-style:none; margin:0; padding:0; }
.faq li { border:solid 1px #DDDDDD; margin-top:-1px; }

.faq .q { margin:0; font-size:12px; font-weight:bold;  }
.faq .trigger { display:block; height:33px; line-height:33px; padding:0 15px; text-align:left; font-size:12px; font-weight:bold; background:#ffffff; text-decoration:none !important; cursor:pointer; }
.faq .trigger .title { overflow:hidden; white-space:nowrap; margin-right:25px; }
.faq .nodata { display:block; height:33px; line-height:33px; padding:0 15px; text-align:center; color:#333; background:#ffffff; text-decoration:none !important;}

.faq .hide .trigger { background:none; color:#353535; }
.faq .hide .arrow { float:right; width:15px; height:33px; background:url(/shop/data/skin_mobileV2/default/common/img/info/icon_arrow_down.png) no-repeat center; }
.faq .show .trigger { background:none; color:#436693; }
.faq .show .arrow { float:right; width:15px; height:33px; background:url(/shop/data/skin_mobileV2/default/common/img/info/icon_arrow_up.png) no-repeat center; }

.faq .a { padding:12px 0 12px 20px; font-size:12px; color:#4D4D4D; background-color:#F5F5F5; display:none; border-top:solid 1px #DDDDDD;}
.faq .block { margin-bottom:5px;  }
.faq .question { width:16px; height:14px; background:url(/shop/data/skin_mobileV2/default/common/img/nmyp/icon_question.png) no-repeat; float:left; margin-right:5px; }
.faq .answer { width:16px; height:14px; background:url(/shop/data/skin_mobileV2/default/common/img/nmyp/icon_answer.png) no-repeat; float:left; margin-right:5px; }

#btn_faq_more_box { text-align:center; margin-top:12px; }
#btn_faq_more { background:url(/shop/data/skin_mobileV2/default/common/img/nlist/btn_more_off.png) no-repeat; width:296px; height:38px; border:none; font-size:14px; color:#FFFFFF; font-weight:bold; cursor:pointer; }
 </style>

<section id="page_title" class="content">
	<div id="top_title" class="top_title">FAQ</div>
</section>

<section id="faq_list" class="content">
	<div id="faq_category">
		<select name="faq_cate" id="faq_cate" onChange="getFaqListData(1)">
<?php if((is_array($TPL_R1=codeitem('faq'))&&!empty($TPL_R1)) || (is_object($TPL_R1) && in_array("Countable", class_implements($TPL_R1)) && $TPL_R1->count() > 0)) {foreach($TPL_R1 as $TPL_K1=>$TPL_V1){?>
			<option value="<?php echo $TPL_K1?>"><?php echo $TPL_V1?></option>
<?php }}?>									
		</select>	
	</div>

	<div class="faq">
		<input type="hidden" name="item_cnt" value="<?php echo $TPL_VAR["item_cnt"]?>" />
		<ul id="faq-item-list">
<?php if($TPL__loop_1){foreach($GLOBALS["loop"] as $TPL_V1){?>
			<li class="article">
				<div class="q trigger">
					<div class="arrow down"></div>
					<div class="title"><?php echo $TPL_V1["question"]?></div>
				</div>
				<div class="a">
<?php if($TPL_V1["descant"]!=''){?>
					<div class="block"><div class='question'></div> <?php echo $TPL_V1["descant"]?></div>
<?php }?>
					<div><div class='answer'></div> <?php echo $TPL_V1["answer"]?></div>
				</div>
			</li>
<?php }}else{?>
			<li class="article">
				<div class="q nodata">검색 결과가 없습니다.</div>
			</li>
<?php }?>
		</ul>

		
		<div id="btn_faq_more_box" <?php if($GLOBALS["k"]!=$_GET["page_num"]){?>style="display:none;"<?php }?>><button type="button" id="btn_faq_more" onClick="javascript:getFaqListData(2);">더보기</button></div>
	</div>
</section>

<?php $this->print_("footer",$TPL_SCP,1);?>