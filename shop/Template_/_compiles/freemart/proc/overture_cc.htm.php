<?php /* Template_ 2.2.7 2014/07/30 21:43:01 /www/francotr3287_godo_co_kr/shop/data/skin/freemart/proc/overture_cc.htm 000001010 */ ?>
<?php if($TPL_VAR["overture_code"]){?>
<SCRIPT LANGUAGE="JavaScript">
<!-- Overture Services Inc. 07/15/2003
var cc_tagVersion = "1.0";
var cc_accountID = "<?php echo $TPL_VAR["overture_code"]?>";
var cc_marketID =  "5";
var cc_protocol="http";
var cc_subdomain = "convctr";
if(location.protocol == "https:")
{
    cc_protocol="https";
     cc_subdomain="convctrs";
}
var cc_queryStr = "?" + "ver=" + cc_tagVersion + "&aID=" + cc_accountID + "&mkt=" + cc_marketID +"&ref=" + escape(document.referrer);
var cc_imageUrl = cc_protocol + "://" + cc_subdomain + ".overture.com/images/cc/cc.gif" + cc_queryStr;
var cc_imageObject = new Image();
cc_imageObject.src = cc_imageUrl;
// -->
</SCRIPT>
<?php }?>
<?php if($TPL_VAR["linkprice_code"]){?><?php echo $TPL_VAR["linkprice_code"]?><?php }?>
<?php if($TPL_VAR["aceCounterTags"]){?><?php echo $TPL_VAR["aceCounterTags"]?><?php }?>