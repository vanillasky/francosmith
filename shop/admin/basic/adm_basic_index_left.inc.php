<?php

//등록 상품
list ($cntGoods) = $db->fetch("select count(*) from ".GD_GOODS."");

// 오늘 날짜
$godo['today']	= date("Ymd");
// 사용한 날짜
$godo['mdate']	= betweenDate($godo['sdate'],$godo['today']);
// 남은 날짜
$godo['pdate']	= betweenDate($godo['today'],$godo['edate']);


//무료사용고객 체크 및 남은 일자 체크
if( $godo['freeType'] == "y" ){
	$godo['freeUser']	= "yes";
	$paddingTop	= 1;
}else{
	$godo['freeUser']	= "no";
	$paddingTop	= 4;

	//30일 이상 남은 경우
	if($godo['pdate'] > 30) $paddingTop2= "style=\"padding-top:16;\"";		
}

// 고도 이차 도메인 사용여부
if(!$cfg['shopUrl']) $cfg['shopUrl']	= $_SERVER['HTTP_HOST'];
if( eregi(".godo.co.kr",$cfg['shopUrl']) ){
	$godo['godoUrl']	= true;
}else{
	$godo['godoUrl']	= false;
}

//용량신청버튼
$applyButtonImage = '<img src="../img/main/state_application.gif" alt="신청" />';
switch ($godo['ecCode']) {
	case 'rental_mxfree_season':// 무료형
		if($godo['maxDisk'] < 200){
			$applyButtonImage = '<img src="../img/main/state_application_free.gif" alt="신청" />';
		}
	break;
	
	case 'rental_mx_season':// 임대형
		if($godo['maxDisk'] < 1024){
			$applyButtonImage = '<img src="../img/main/state_application_black.gif" alt="신청" />';
		}
	break;
}

////남은 기간
if(preg_match( "/^rental/i", $godo['ecCode'])) { //임대형, 무료형
	$remainderImage = '<div class="remainder">';
	$remainderImage .= '<span><img src="../img/main/remainder_txt1.gif" alt="남은기간" /></span>';
	$remainderImage .= '<div>';
	for($i=0,$m=strlen($godo['pdate']);$i<$m;$i++){
		$cutNum = substr($godo['pdate'],$i,1);
		$remainderImage .= '<img src="../img/main/remainder_day_'.$cutNum.'.gif" alt="3" />';
	}
	$remainderImage .= '</div>';
	$remainderImage .= '<span><img src="../img/main/remainder_txt2.gif" alt="일" /></span>';
	$remainderImage .= '</div>';
} else if($godo['webCode'] == 'webhost_outside') { //외부독립형
	$remainderImage = '<div><a href="http://hosting.godo.co.kr/shophosting/comebackhome.php" target="_blank"><img src="../img/hosting_comeback.gif" align="absmiddle" /></a></div>';
}
?>
<h2><img src="../img/main/<?=$godo['ecCode']?>_logo.gif" alt="e나무 시즌4" /></h2>
<?=$remainderImage?>
<ul class="state">
	<!-- 무료형 고객인 경우 -->
	<? if($godo['freeUser'] == "yes" && $godo['webCode'] != 'webhost_outside'){ ?>
	<li>
		<strong><?=$godo['pdate']?></strong> <span>일 이후에는 무료기간 마감</span>
		<br><span style="padding-left:10px;"><a href="http://www.godo.co.kr/mygodo/index.html" target="_blank"><u>정식신청하기</u></a></span>
	</li>
	<? } ?>

	<? if ( preg_match( "/^rental_mxfree_season/i", $godo['ecCode'] ) && $godo['classify'] == "free_ex" && $godo['PG_YN'] == "N" ){ ?>
	<li>
		<strong><?=$godo['pdate']?></strong>
		<span>일 이내 PG 미적용시 사용불가</span>
	</li>	
	<? } else if ( preg_match( "/^rental_mxfree_season/i", $godo['ecCode'] ) ){ ?>
	<li>
		<strong><?=$godo['pdate']?></strong>
		<span>일 이내 미접속시 자동 차단</span>
	</li>
	<? } ?>

	<li>
		<strong>버전</strong>
		<span><?=$godo['version'][0]?></span>
	</li>
	<li>
		<strong>최초설치일</strong>
		<span><?=toDate($godo['sdate'],".")?></span>
	</li>
	<li>
		<div class="downlayer">
			<strong>도메인</strong>
			<? if($godo['godoUrl'] == true){ ?>
				<a href="http://www.godo.co.kr/mygodo/index.html" target="_new"><img src="../img/main/state_change_domain.gif" alt="변경" /></a>
			<? } ?>
			<span><?=$cfg['shopUrl']?></span>
		</div>
	</li>

	<? if ( preg_match( "/^rental/i", $godo['ecCode'] ) ){ ?>
	<li>
		<strong>기간</strong>
		<span><?=toDate($godo['sdate'],".")?> ~ <?=toDate($godo['edate'],".")?></span>
	</li>
	<li>
		<strong>남은기간</strong>
		<span><strong><?=$godo['pdate']?></strong><span class="dotum">일</span> <a href="http://www.godo.co.kr/mygodo/index.html" target="_new"><img src="../img/main/state_extended.gif" alt="기간연장" /></a></span>
	</li>
	<? } ?>
	
	<? if ($godo['webCode']){ ?>
	<li>
		<strong>웹호스팅</strong>
		<span><?=$godo['webName']?></span>
	</li>
	<? } ?>

	<li>
		<strong>상품</strong>
		<span><?=$cntGoods?><span class="dotum">개</span> 
		<? if ($godo['ecCode']=="self_enamoo_season"){ ?>
		<? } else if ($godo['maxGoods']=="unlimited"){ ?>
		<span class="dotum">(무제한)</span>
		<? } else { ?>
		<span class="dotum">(최대</span><strong><?=$godo['maxGoods']?></strong><span class="dotum">개)</span></span>
		<? } ?>			
	</li>
	<li>
		<strong>SMS</strong>
		<span><?=number_format(getSmsPoint())?></span>
		<em><strong>point</strong></em>
		<a href="../member/sms.pay.php"><img src="../img/main/state_charge.gif" alt="충전" /></a>
	</li>

	<? if ($godo['ecCode']!="self_enamoo_season"){ ?>
	<? include "../design/webftp/3DBar_calc_conf.php"; // 계산식 호출 ?>

	<li>
		<div class="downlayer">
			<strong>용량</strong>
			<span>
			<?=$sizeStr?>

			<? if ( intval($godo['diskSdate']) && intval($godo['diskEdate']) ){ ?>
				<em>(<strong><?=byte2str(mb2byte($godo['maxDisk'] + $godo['diskGoods']))?></strong>)</em>				
			<? } else { ?>
				<em>(<strong><?=byte2str(mb2byte($godo['maxDisk']))?></strong>)</em>
			<? } ?>

			<a href="javascript:popupLayer('../basic/adm_popup_diskRenew.php',250,250)"><img src="../img/main/state_renewal.gif" alt="갱신" /></a>
			<a href="../basic/disk.pay.php"><?=$applyButtonImage?></a>
			</span>
		</div>
		<!--------------- 용량 표시 BAR ------------------------->
		<div class="graph">
			<div class="graph-side">&nbsp;</div>
			<span style="width:<?=$square?>px;">&nbsp;</span>
		</div>
		<!--------------- 용량 표시 BAR ------------------------->
	</li>
	<? } ?>
</ul>

<!-- 좌측배너 -->
<div class="ad">	
	<div id="panel_MENU"></div>
</div>
<!-- 좌측배너 -->