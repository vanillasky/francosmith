<?php

//��� ��ǰ
list ($cntGoods) = $db->fetch("select count(*) from ".GD_GOODS."");

// ���� ��¥
$godo['today']	= date("Ymd");
// ����� ��¥
$godo['mdate']	= betweenDate($godo['sdate'],$godo['today']);
// ���� ��¥
$godo['pdate']	= betweenDate($godo['today'],$godo['edate']);


//������� üũ �� ���� ���� üũ
if( $godo['freeType'] == "y" ){
	$godo['freeUser']	= "yes";
	$paddingTop	= 1;
}else{
	$godo['freeUser']	= "no";
	$paddingTop	= 4;

	//30�� �̻� ���� ���
	if($godo['pdate'] > 30) $paddingTop2= "style=\"padding-top:16;\"";		
}

// �� ���� ������ ��뿩��
if(!$cfg['shopUrl']) $cfg['shopUrl']	= $_SERVER['HTTP_HOST'];
if( eregi(".godo.co.kr",$cfg['shopUrl']) ){
	$godo['godoUrl']	= true;
}else{
	$godo['godoUrl']	= false;
}

//�뷮��û��ư
$applyButtonImage = '<img src="../img/main/state_application.gif" alt="��û" />';
switch ($godo['ecCode']) {
	case 'rental_mxfree_season':// ������
		if($godo['maxDisk'] < 200){
			$applyButtonImage = '<img src="../img/main/state_application_free.gif" alt="��û" />';
		}
	break;
	
	case 'rental_mx_season':// �Ӵ���
		if($godo['maxDisk'] < 1024){
			$applyButtonImage = '<img src="../img/main/state_application_black.gif" alt="��û" />';
		}
	break;
}

////���� �Ⱓ
if(preg_match( "/^rental/i", $godo['ecCode'])) { //�Ӵ���, ������
	$remainderImage = '<div class="remainder">';
	$remainderImage .= '<span><img src="../img/main/remainder_txt1.gif" alt="�����Ⱓ" /></span>';
	$remainderImage .= '<div>';
	for($i=0,$m=strlen($godo['pdate']);$i<$m;$i++){
		$cutNum = substr($godo['pdate'],$i,1);
		$remainderImage .= '<img src="../img/main/remainder_day_'.$cutNum.'.gif" alt="3" />';
	}
	$remainderImage .= '</div>';
	$remainderImage .= '<span><img src="../img/main/remainder_txt2.gif" alt="��" /></span>';
	$remainderImage .= '</div>';
} else if($godo['webCode'] == 'webhost_outside') { //�ܺε�����
	$remainderImage = '<div><a href="http://hosting.godo.co.kr/shophosting/comebackhome.php" target="_blank"><img src="../img/hosting_comeback.gif" align="absmiddle" /></a></div>';
}
?>
<h2><img src="../img/main/<?=$godo['ecCode']?>_logo.gif" alt="e���� ����4" /></h2>
<?=$remainderImage?>
<ul class="state">
	<!-- ������ ���� ��� -->
	<? if($godo['freeUser'] == "yes" && $godo['webCode'] != 'webhost_outside'){ ?>
	<li>
		<strong><?=$godo['pdate']?></strong> <span>�� ���Ŀ��� ����Ⱓ ����</span>
		<br><span style="padding-left:10px;"><a href="http://www.godo.co.kr/mygodo/index.html" target="_blank"><u>���Ľ�û�ϱ�</u></a></span>
	</li>
	<? } ?>

	<? if ( preg_match( "/^rental_mxfree_season/i", $godo['ecCode'] ) && $godo['classify'] == "free_ex" && $godo['PG_YN'] == "N" ){ ?>
	<li>
		<strong><?=$godo['pdate']?></strong>
		<span>�� �̳� PG ������� ���Ұ�</span>
	</li>	
	<? } else if ( preg_match( "/^rental_mxfree_season/i", $godo['ecCode'] ) ){ ?>
	<li>
		<strong><?=$godo['pdate']?></strong>
		<span>�� �̳� �����ӽ� �ڵ� ����</span>
	</li>
	<? } ?>

	<li>
		<strong>����</strong>
		<span><?=$godo['version'][0]?></span>
	</li>
	<li>
		<strong>���ʼ�ġ��</strong>
		<span><?=toDate($godo['sdate'],".")?></span>
	</li>
	<li>
		<div class="downlayer">
			<strong>������</strong>
			<? if($godo['godoUrl'] == true){ ?>
				<a href="http://www.godo.co.kr/mygodo/index.html" target="_new"><img src="../img/main/state_change_domain.gif" alt="����" /></a>
			<? } ?>
			<span><?=$cfg['shopUrl']?></span>
		</div>
	</li>

	<? if ( preg_match( "/^rental/i", $godo['ecCode'] ) ){ ?>
	<li>
		<strong>�Ⱓ</strong>
		<span><?=toDate($godo['sdate'],".")?> ~ <?=toDate($godo['edate'],".")?></span>
	</li>
	<li>
		<strong>�����Ⱓ</strong>
		<span><strong><?=$godo['pdate']?></strong><span class="dotum">��</span> <a href="http://www.godo.co.kr/mygodo/index.html" target="_new"><img src="../img/main/state_extended.gif" alt="�Ⱓ����" /></a></span>
	</li>
	<? } ?>
	
	<? if ($godo['webCode']){ ?>
	<li>
		<strong>��ȣ����</strong>
		<span><?=$godo['webName']?></span>
	</li>
	<? } ?>

	<li>
		<strong>��ǰ</strong>
		<span><?=$cntGoods?><span class="dotum">��</span> 
		<? if ($godo['ecCode']=="self_enamoo_season"){ ?>
		<? } else if ($godo['maxGoods']=="unlimited"){ ?>
		<span class="dotum">(������)</span>
		<? } else { ?>
		<span class="dotum">(�ִ�</span><strong><?=$godo['maxGoods']?></strong><span class="dotum">��)</span></span>
		<? } ?>			
	</li>
	<li>
		<strong>SMS</strong>
		<span><?=number_format(getSmsPoint())?></span>
		<em><strong>point</strong></em>
		<a href="../member/sms.pay.php"><img src="../img/main/state_charge.gif" alt="����" /></a>
	</li>

	<? if ($godo['ecCode']!="self_enamoo_season"){ ?>
	<? include "../design/webftp/3DBar_calc_conf.php"; // ���� ȣ�� ?>

	<li>
		<div class="downlayer">
			<strong>�뷮</strong>
			<span>
			<?=$sizeStr?>

			<? if ( intval($godo['diskSdate']) && intval($godo['diskEdate']) ){ ?>
				<em>(<strong><?=byte2str(mb2byte($godo['maxDisk'] + $godo['diskGoods']))?></strong>)</em>				
			<? } else { ?>
				<em>(<strong><?=byte2str(mb2byte($godo['maxDisk']))?></strong>)</em>
			<? } ?>

			<a href="javascript:popupLayer('../basic/adm_popup_diskRenew.php',250,250)"><img src="../img/main/state_renewal.gif" alt="����" /></a>
			<a href="../basic/disk.pay.php"><?=$applyButtonImage?></a>
			</span>
		</div>
		<!--------------- �뷮 ǥ�� BAR ------------------------->
		<div class="graph">
			<div class="graph-side">&nbsp;</div>
			<span style="width:<?=$square?>px;">&nbsp;</span>
		</div>
		<!--------------- �뷮 ǥ�� BAR ------------------------->
	</li>
	<? } ?>
</ul>

<!-- ������� -->
<div class="ad">	
	<div id="panel_MENU"></div>
</div>
<!-- ������� -->