<?php /* Template_ 2.2.7 2012/12/13 10:06:13 /www/francotr3287_godo_co_kr/shop/data/skin_mobileV2/default/service/agrmt.htm 000000745 */ ?>
<?php $this->print_("header",$TPL_SCP,1);?>


<script type="text/javascript">
$(document).ready(function(){
	$("#agreement > article").html($("#agreement > article").html().replace(/\n/gi, "<br/>"));
});
</script>
<style type="text/css">
#agreement{
	padding: 15px;
}
#agreement h2{
	margin-bottom: 15px;
	font-size: 15px;
}
</style>

<section id="agreement">
	<h2>이용약관</h2>
	<article><?php echo $this->define('tpl_include_file_1',"proc/_agreement.txt")?> <?php $this->print_("tpl_include_file_1",$TPL_SCP,1);?></article>
</section>

<?php $this->print_("footer",$TPL_SCP,1);?>