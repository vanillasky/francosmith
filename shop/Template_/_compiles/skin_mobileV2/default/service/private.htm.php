<?php /* Template_ 2.2.7 2013/11/26 16:49:10 /www/francotr3287_godo_co_kr/shop/data/skin_mobileV2/default/service/private.htm 000000747 */ ?>
<?php $this->print_("header",$TPL_SCP,1);?>


<script type="text/javascript">
$(document).ready(function(){
	$("#private > article").html($("#private > article").html().replace(/\n/gi, "<br/>"));
});
</script>
<style type="text/css">
#private{
	padding: 15px;
}
#private h2{
	margin-bottom: 15px;
	font-size: 15px;
}
</style>

<section id="private">
	<h2>개인정보취급방침</h2>
	<article><?php echo $this->define('tpl_include_file_1',"service/_private1.txt")?> <?php $this->print_("tpl_include_file_1",$TPL_SCP,1);?></article>
</section>

<?php $this->print_("footer",$TPL_SCP,1);?>