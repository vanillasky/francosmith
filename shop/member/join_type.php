<?php

include dirname(__FILE__).'/../_header.php';
include '../conf/fieldset.php';

if (in_array(SocialMemberService::FACEBOOK, $socialMemberService->getEnabledServiceList())) {
	$facebookService = SocialMemberService::getMember(SocialMemberService::FACEBOOK);
	$tpl->assign('FacebookLoginURL', $facebookService->getLoginURL());
}

$tpl->print_('tpl');

?>