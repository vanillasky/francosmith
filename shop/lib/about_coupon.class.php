<?php
class about_coupon {
    var $cfg;
    var $use = false;    
    var $sale = "8%";
    function about_coupon(){
	$config = Core::loader('config');
	$this->cfg = $config->load('aboutcoupon');
	$sess = $GLOBALS['sess'];
	$today = date("Ymd");	
	if ($_COOKIE['cc_inflow']=='auctionos'
	    && $this->cfg['use_aboutcoupon'] == 'Y'
	    && ( ($this->cfg['use_test']=='N') || ($this->cfg['use_test']=='Y' && $sess['level']>80) )
	    && ( $today >= $this->cfg['startdate'] && $today <= $this->cfg['enddate'] )	   
	    &&(!preg_match('/goods_qna_list|goods_review_list|popup/',$_SERVER['PHP_SELF'])) ) {
		    $this->use = true;
	}
    }
    function about_coupon_popup(){	
	if($this->use)	return "<div id='aboutcoupon_popup' STYLE='position:absolute; width:192; height:100; left:".$this->cfg['left_loc']."; top:".$this->cfg['top_loc']."; z-index:200; '><div onmousedown=\"Start_move(event,'aboutcoupon_popup');\" onmouseup=\"Moveing_stop();\" style='cursor:move;padding:0 0 0 0; '><img src='/shop/admin/img/about/about_coupon_top.gif' /></div><a href=\"/shop/proc/aboutcoupon_setcookie.php\" target='ifrmHidden' ><img src='/shop/admin/img/about/about_coupon_dn.gif' /></a><div align=right><input type='button' onClick='aboutcoupon_popup.style.display=\"none\"' value='´İ±â'></div></div>";
    }
    
}
?>
