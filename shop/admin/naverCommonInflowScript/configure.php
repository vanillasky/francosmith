<?php

if(isset($cfg)===false) @include dirname(__FILE__).'/../../conf/config.php';
include dirname(__FILE__).'/../../lib/naverCommonInflowScript.class.php';

$naverCommonInflowScript = new NaverCommonInflowScript();

?>
<link rel="stylesheet" href="<?php echo $cfg['rootDir']; ?>/admin/naverCommonInflowScript/configure.css"/>
<script type="text/javascript" src="<?php echo $cfg['rootDir']; ?>/admin/naverCommonInflowScript/configure.js"></script>
<div class="title title_top">공통유입 스크립트 설정</div>
<form id="common-inflow-script-configure-form" action="<?php echo $cfg['rootDir']; ?>/admin/naverCommonInflowScript/indb.php">
	<input type="hidden" name="mode" value=""/>
	<div class="common-inflow-script-description">
		<strong>*필독* </strong><br/>
		<p><strong>공통유입스크립트</strong>란, 네이버 쇼핑, 네이버 페이, 검색광고의 프리미엄로그분석 서비스의 유입트래킹이 따로 관리되던 것을 통합하여 관리하기위한 수단입니다.</p>
		<p>
			공통유입스크립트를 설정 하셔야만 네이버부가서비스를 사용할 수 있기 때문에, 공통유입스크립트가 설정되지 않은	가맹점은<br/>
			네이버 쇼핑, 네이버 페이 서비스 사용에 제한이 따를 수 있습니다.<br/>
			단, 네이버 쇼핑서비스만 사용하는 가맹점의 경우 <u>CPC</u><sup>1)</sup>에서 <u>CPA</u><sup>2)</sup>로의 과금전환 전까지는 설정하지 않으셔도 되지만, CPA과금전환 이후에도<br/>
			공통유입스크립트가 설정되어있지 않으면 서비스사용에 제한이 따를 수 있사오니 되도록 미리 설정하여두시기 바랍니다.
		</p>
		<p>
			1) CPC(Cost per click) : 네이버 쇼핑에 노출된 상품을 클릭하였을때 과금되는 형태.<br/>
			2) CPA(Cost per action) : 네이버로부터 유입되어 주문이 일어났을때 과금되는 형태.
		</p>
		<br/>
		<p>
		<strong>네이버공통인증키 확인방법</strong><br/>
		네이버공통인증키는 "네이버 쇼핑파트너존 > 정보관리 > 정보수정"에서 확인하실수 있습니다. 만일 네이버공통인증키를 확인하실수 없으시다면 네이버 쇼핑파트너존측으로 네이버공통인증키의 확인방법에 대하여 문의주시기 바랍니다.<br/>
		[쇼핑광고 고객센터] 1588-3819&nbsp;&nbsp;&nbsp;[검색광고 고객센터] 1588-5896
		</p>
	</div>

	<table class="tb" border="0">
		<colgroup>
			<col class="cellC"><col class="cellL">
		</colgroup>
		<tbody>
			<tr>
				<td>네이버공통인증키</td>
				<td>
					<div>
						<span class="red" style="font-weight: bold;">※주의※</span><br/>
						<span class="extext">
							한번 입력하신 "네이버공통인증키"는 변경하실 수 없습니다.<br/>
							최초입력시 유의하여주시기 바랍니다.<br/>
							만일 잘못입력하였거나 변경이 필요할 시에는 고도 고객센터로 문의주시기 바랍니다.
						</span>
					</div>
<?php if($naverCommonInflowScript->isEnabled){ ?>
					<div id="set-account-id" class="confirmed-account-id"><?php echo $naverCommonInflowScript->accountId; ?></div>
					<input type="hidden" name="accountId" value="<?php echo $naverCommonInflowScript->accountId; ?>" class="line" required="required"/>
<?php }else{ ?>
					<input type="text" name="accountId" value="<?php echo $naverCommonInflowScript->accountId; ?>" class="line" required="required"/>
					<input type="button" id="account-id-check-duplicate" value="중복확인"/>
<?php } ?>
				</td>
			</tr>
			<tr>
				<td>White List</td>
				<td>
					<div id="white-list-container" data-initialize="<?php echo is_array($naverCommonInflowScript->whiteList)?implode('|', $naverCommonInflowScript->whiteList):''; ?>"></div>
					<div class="extext">
						네이버 페이서비스의 유입경로별 혜택은 네이버 쇼핑파트너존에 등록하신 도메인에 한해서만 적용됩니다.<br/>
						쇼핑몰이 여러개의 도메인으로 운영되는 경우 White List에 해당 도메인들을 추가하여주시기 바랍니다.
					</div>
				</td>
			</tr>
		</tbody>
	</table>
	<div class="noline" style="text-align: center; padding: 10px;" id="common-inflow-script-configure-form-submit">
		<input type="image" src="<?php echo $cfg['rootDir']; ?>/admin/img/btn_naver_install.gif"/>
	</div>
</form>