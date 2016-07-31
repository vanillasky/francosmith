<? ### 举肺快母(22222222222)
// 价厘 眠利 林家 : https://www.kgyellowcap.co.kr/delivery/waybill.html?mode=bill
$out = iconv('UTF-8', 'EUC-KR', $out);
$displayout = split_betweenStr($out, '<!-- contentsWrap -->', '<!-- //contentsWrap -->');
$delout1 = split_betweenStr($out, '<!-- divUtill -->', '<!-- //divUtill -->');
$delout2 = split_betweenStr($out, '<!-- titArea -->', '<!-- //titArea -->');
$displayout = str_replace($delout1,'',$displayout[0]);
$displayout = str_replace($delout2,'',$displayout);
$displayout = str_replace('"/img/ywcap_bt.gif"','"http://www.kgyellowcap.co.kr/img/ywcap_bt.gif"',$displayout);
?>
<style>
table.list{ border-top:#777 2px solid; border-right:1px solid #dbdbdb; color:#666; text-align:left; width:100%; border-collapse:separate; *border-collapse:collapse; border-spacing:0px; clear:both;}
table.list th,
table.list td{ padding:11px 0; border-bottom:1px solid #dbdbdb; border-left:1px solid #dbdbdb; vertical-align:middle; text-align:center;}
table.list th{ background:#fafafa; font-size:13px; }
table.list td.subject{ text-align:left; padding-left:20px;}
table.list td.subject .icon{ margin-left:10px; vertical-align:middle;}
table.list td.left{ text-align:left; padding-left:20px;}
table.list td img{ vertical-align:middle;}
table.list td.noData{ padding:30px 0;}
table.list .fir{ border-left:none;}
table.view{ border-top:#777 2px solid; border-right:1px solid #dbdbdb; /* color:#666; */ text-align:left; width:100%; border-collapse:separate; *border-collapse:collapse; border-spacing:0px; clear:both;}
table.view th,
table.view td{ padding:11px 0; border-bottom:1px solid #dbdbdb; border-left:1px solid #dbdbdb; vertical-align:middle;}
table.view th{ background:#fafafa; font-size:13px; text-align:left; padding-left:20px;}
table.view td{ padding-left:10px;}
table.view td img{ vertical-align:middle;}
.viewTd{ padding:30px 20px; border-left:none;}
</style>
<div style="padding-top:10px">
<?=$displayout?>
</div>