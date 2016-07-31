<?
	# 등록상품
	list ($cntGoods) = $db->fetch("select count(*) from ".GD_GOODS."");

	$godo['today']	= date("Ymd");									// 오늘 날짜
	$godo['mdate']	= betweenDate($godo['sdate'],$godo['today']);	// 사용한 날짜
	$godo['pdate']	= betweenDate($godo['today'],$godo['edate']);	// 남은 날짜

	# 무료사용고객 체크 및 남은 일자 체크
	if( $godo['freeType'] == "y" ){
		$godo['freeUser']	= "yes";
		$paddingTop	= 1;
	}else{
		$godo['freeUser']	= "no";

		$paddingTop	= 4;
		if($godo['pdate'] > 30) $paddingTop2= "style=\"padding-top:16;\"";		# 30일 이상 남은 경우
	}

	# 고도 이차 도메인 사용여부
	if(!$cfg['shopUrl']) $cfg['shopUrl']	= $_SERVER['HTTP_HOST'];
	if( eregi(".godo.co.kr",$cfg['shopUrl']) ){
		$godo['godoUrl']	= true;
	}else{
		$godo['godoUrl']	= false;
	}

	switch ($godo['ecCode']) {
		case 'self_enamoo_season':	// 독립형
			$_p1_class = 'ecSelf';
			break;
		case 'rental_mxfree_season':// 무료형
			$_p1_class = 'ecFree';
			break;
		case 'rental_mx_season':// 임대형
			$_p1_class = 'ecRent';
			break;
	}

	# 용량신청버튼명
	$btnEduApply = '../img/btn_edu_apply.gif';
	if(preg_match( "/^rental/i", $godo['ecCode']) && $godo['maxDisk'] < 1024){ // 임대형
		$btnEduApply = '../img/btn_edu_extend_apply.gif';
	}
?>
<div class="main-basic-left-info">

	<!-- 사용중인 솔루션 안내 -->
	<div class="p1 <?=$_p1_class?>">
		<ul>
			<?
			// 임대형
			if(preg_match( "/^rental/i", $godo['ecCode'])){
				echo '<li><img src="../img/dday_txt01.gif" align="bottom" style="margin:0 1px 3px 0;">';
				for($i=0,$m=strlen($godo['pdate']);$i<$m;$i++){
					$cutNum = substr($godo['pdate'],$i,1);
					echo "<img src=\"../img/dday_".$cutNum.".gif\" align=\"bottom\" />";
				}
				echo '<img src="../img/dday_txt02.gif" align="bottom" style="margin:0 0 3px 1px;"></li>';

			}
			// 외부독립형일경우
			else if($godo['webCode'] == 'webhost_outside'){
				echo '<li><a href="http://hosting.godo.co.kr/shophosting/comebackhome.php" target="_blank"><img src="../img/hosting_comeback.gif" align="absmiddle" /></a></li>';
			}
			?>
		</ul>
	</div>

	<div class="p2">
		<ul>
			<? ### 무료사용고객인경우 ?>
			<? if($godo['freeUser'] == "yes" && $godo['webCode'] != 'webhost_outside'){ ?>
			<li class="minfo"><font class="ta8"><b><?=$godo['pdate']?></b></font><font class="small1">일 이후에는 무료기간 마감</font><br><a href="http://www.godo.co.kr/mygodo/index.html" target="_blank"><font class="small1"><u>정식신청하기</u></a></li>
			<? } ?>

			<? if ( preg_match( "/^rental_mxfree_season/i", $godo['ecCode'] ) && $godo['classify'] == "free_ex" && $godo['PG_YN'] == "N" ){ ?>
			<li class="minfo"><font class="ta8"><b><?=$godo['pdate']?></b></font><font class="small1">일 이내 PG 미적용시 사용 불가</font></li>
			<? } else if ( preg_match( "/^rental_mxfree_season/i", $godo['ecCode'] ) ){ ?>
			<li class="minfo"><font class="ta8"><b><?=$godo['pdate']?></b></font><font class="small1">일 이내 미접속시 자동 차단</font></li>
			<? } ?>

			<li class="ver"><span class="hd">버전</span> : <span class="accent"><?=$godo['version'][0]?></span></li>
			<li><span class="hd">최초 설치일</span> : <?=toDate($godo['sdate'],".")?></li>
			<li><span class="hd">도메인</span> : <?=$cfg['shopUrl']?><?if($godo['godoUrl'] == true){?><a href="http://www.godo.co.kr/mygodo/index.html" target="_new"><img src="../img/btn_domainmodify.gif" border="0" align="absmiddle" hspace="2"></a><?}?></a></li>
			<? if ( preg_match( "/^rental/i", $godo['ecCode'] ) ){ ?>

			<li><span class="hd">기간</span> : <?=toDate($godo['sdate'],".")?>-<?=toDate($godo['edate'],".")?></li>
			<li><span class="hd">남은기간</span> : <span class="accent"><?=$godo['pdate']?></span> 일 <a href="http://www.godo.co.kr/mygodo/index.html" target="_new"><img src="../img/btn_addperiod.gif" border="0" align="absmiddle" hspace="2" /></a></li>
			<? } ?>

			<? if ($godo['webCode']){ ?>
			<li><span class="hd">웹호스팅</span> : <?=$godo['webName']?></li>
			<? } ?>
			<li>
				<span class="hd">상품</span> : <span class="accent"><?=$cntGoods?></span> 개
				<? if ($godo['ecCode']=="self_enamoo_season"){ ?>
				<? } else if ($godo['maxGoods']=="unlimited"){ ?>(무제한)
				<? } else { ?>(최대<?=$godo['maxGoods']?>개)
				<? } ?>
			</li>
			<li><span class="hd">SMS</span> : <span class="accent"><?=number_format(getSmsPoint())?></span> point <a href="../member/sms.pay.php"><img src="../img/btn_addsms.gif" border="0" align="absmiddle" hspace="2" /></a></li>

			<? if ($godo['ecCode']!="self_enamoo_season"){ ?>
			<? include "../design/webftp/3DBar_calc_conf.php"; # 계산식 호출 ?>
			<li>
				<span class="hd">용량</span> : <a href="../design/design_webftp.php" ><span class="accent"><?=$sizeStr?></span></a>
				<? if ( intval($godo['diskSdate']) && intval($godo['diskEdate']) ){ ?>
				(<?=byte2str(mb2byte($godo['maxDisk'] + $godo['diskGoods']))?>)
				<a href="../basic/disk.pay.php"><img src="<?=$btnEduApply?>" border="0" align="absmiddle" /></a>
				<div style="padding:0 0 0 33px;"><font class="ta7">(<?=toDate($godo['diskSdate'],".")?>-<?=toDate($godo['diskEdate'],".")?>)</font></div>
				<? } else { ?>
				(<?=byte2str(mb2byte($godo['maxDisk']))?>)
				<a href="../basic/disk.pay.php" class="ver8"><img src="<?=$btnEduApply?>" border="0" align="absmiddle" /></a>
				<? } ?>
			</li>
			<li class="du-graph">
				<!--------------- 용량 표시 BAR ------------------------->
				<span class="gage" style="width:<?=$square?>px"></span>
				<!--------------- 용량 표시 BAR ------------------------->
			</li>
			<? } ?>
		</ul>

		<div id="godoinfo" style="margin:0;padding:0;"></div>
	</div>
	<script>panel('godoinfo', 'basic');</script>

</div>