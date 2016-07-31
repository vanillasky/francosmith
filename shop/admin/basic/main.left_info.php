<?
	# ��ϻ�ǰ
	list ($cntGoods) = $db->fetch("select count(*) from ".GD_GOODS."");

	$godo['today']	= date("Ymd");									// ���� ��¥
	$godo['mdate']	= betweenDate($godo['sdate'],$godo['today']);	// ����� ��¥
	$godo['pdate']	= betweenDate($godo['today'],$godo['edate']);	// ���� ��¥

	# ������� üũ �� ���� ���� üũ
	if( $godo['freeType'] == "y" ){
		$godo['freeUser']	= "yes";
		$paddingTop	= 1;
	}else{
		$godo['freeUser']	= "no";

		$paddingTop	= 4;
		if($godo['pdate'] > 30) $paddingTop2= "style=\"padding-top:16;\"";		# 30�� �̻� ���� ���
	}

	# �� ���� ������ ��뿩��
	if(!$cfg['shopUrl']) $cfg['shopUrl']	= $_SERVER['HTTP_HOST'];
	if( eregi(".godo.co.kr",$cfg['shopUrl']) ){
		$godo['godoUrl']	= true;
	}else{
		$godo['godoUrl']	= false;
	}

	switch ($godo['ecCode']) {
		case 'self_enamoo_season':	// ������
			$_p1_class = 'ecSelf';
			break;
		case 'rental_mxfree_season':// ������
			$_p1_class = 'ecFree';
			break;
		case 'rental_mx_season':// �Ӵ���
			$_p1_class = 'ecRent';
			break;
	}

	# �뷮��û��ư��
	$btnEduApply = '../img/btn_edu_apply.gif';
	if(preg_match( "/^rental/i", $godo['ecCode']) && $godo['maxDisk'] < 1024){ // �Ӵ���
		$btnEduApply = '../img/btn_edu_extend_apply.gif';
	}
?>
<div class="main-basic-left-info">

	<!-- ������� �ַ�� �ȳ� -->
	<div class="p1 <?=$_p1_class?>">
		<ul>
			<?
			// �Ӵ���
			if(preg_match( "/^rental/i", $godo['ecCode'])){
				echo '<li><img src="../img/dday_txt01.gif" align="bottom" style="margin:0 1px 3px 0;">';
				for($i=0,$m=strlen($godo['pdate']);$i<$m;$i++){
					$cutNum = substr($godo['pdate'],$i,1);
					echo "<img src=\"../img/dday_".$cutNum.".gif\" align=\"bottom\" />";
				}
				echo '<img src="../img/dday_txt02.gif" align="bottom" style="margin:0 0 3px 1px;"></li>';

			}
			// �ܺε������ϰ��
			else if($godo['webCode'] == 'webhost_outside'){
				echo '<li><a href="http://hosting.godo.co.kr/shophosting/comebackhome.php" target="_blank"><img src="../img/hosting_comeback.gif" align="absmiddle" /></a></li>';
			}
			?>
		</ul>
	</div>

	<div class="p2">
		<ul>
			<? ### ��������ΰ�� ?>
			<? if($godo['freeUser'] == "yes" && $godo['webCode'] != 'webhost_outside'){ ?>
			<li class="minfo"><font class="ta8"><b><?=$godo['pdate']?></b></font><font class="small1">�� ���Ŀ��� ����Ⱓ ����</font><br><a href="http://www.godo.co.kr/mygodo/index.html" target="_blank"><font class="small1"><u>���Ľ�û�ϱ�</u></a></li>
			<? } ?>

			<? if ( preg_match( "/^rental_mxfree_season/i", $godo['ecCode'] ) && $godo['classify'] == "free_ex" && $godo['PG_YN'] == "N" ){ ?>
			<li class="minfo"><font class="ta8"><b><?=$godo['pdate']?></b></font><font class="small1">�� �̳� PG ������� ��� �Ұ�</font></li>
			<? } else if ( preg_match( "/^rental_mxfree_season/i", $godo['ecCode'] ) ){ ?>
			<li class="minfo"><font class="ta8"><b><?=$godo['pdate']?></b></font><font class="small1">�� �̳� �����ӽ� �ڵ� ����</font></li>
			<? } ?>

			<li class="ver"><span class="hd">����</span> : <span class="accent"><?=$godo['version'][0]?></span></li>
			<li><span class="hd">���� ��ġ��</span> : <?=toDate($godo['sdate'],".")?></li>
			<li><span class="hd">������</span> : <?=$cfg['shopUrl']?><?if($godo['godoUrl'] == true){?><a href="http://www.godo.co.kr/mygodo/index.html" target="_new"><img src="../img/btn_domainmodify.gif" border="0" align="absmiddle" hspace="2"></a><?}?></a></li>
			<? if ( preg_match( "/^rental/i", $godo['ecCode'] ) ){ ?>

			<li><span class="hd">�Ⱓ</span> : <?=toDate($godo['sdate'],".")?>-<?=toDate($godo['edate'],".")?></li>
			<li><span class="hd">�����Ⱓ</span> : <span class="accent"><?=$godo['pdate']?></span> �� <a href="http://www.godo.co.kr/mygodo/index.html" target="_new"><img src="../img/btn_addperiod.gif" border="0" align="absmiddle" hspace="2" /></a></li>
			<? } ?>

			<? if ($godo['webCode']){ ?>
			<li><span class="hd">��ȣ����</span> : <?=$godo['webName']?></li>
			<? } ?>
			<li>
				<span class="hd">��ǰ</span> : <span class="accent"><?=$cntGoods?></span> ��
				<? if ($godo['ecCode']=="self_enamoo_season"){ ?>
				<? } else if ($godo['maxGoods']=="unlimited"){ ?>(������)
				<? } else { ?>(�ִ�<?=$godo['maxGoods']?>��)
				<? } ?>
			</li>
			<li><span class="hd">SMS</span> : <span class="accent"><?=number_format(getSmsPoint())?></span> point <a href="../member/sms.pay.php"><img src="../img/btn_addsms.gif" border="0" align="absmiddle" hspace="2" /></a></li>

			<? if ($godo['ecCode']!="self_enamoo_season"){ ?>
			<? include "../design/webftp/3DBar_calc_conf.php"; # ���� ȣ�� ?>
			<li>
				<span class="hd">�뷮</span> : <a href="../design/design_webftp.php" ><span class="accent"><?=$sizeStr?></span></a>
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
				<!--------------- �뷮 ǥ�� BAR ------------------------->
				<span class="gage" style="width:<?=$square?>px"></span>
				<!--------------- �뷮 ǥ�� BAR ------------------------->
			</li>
			<? } ?>
		</ul>

		<div id="godoinfo" style="margin:0;padding:0;"></div>
	</div>
	<script>panel('godoinfo', 'basic');</script>

</div>