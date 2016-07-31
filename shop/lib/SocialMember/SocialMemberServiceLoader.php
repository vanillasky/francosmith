<?php

define('GD_SNS_MEMBER', 'gd_sns_member');

if (class_exists('SocialMember') === false) include dirname(__FILE__).'/SocialMember.class.php';
if (class_exists('FacebookMember') === false) include dirname(__FILE__).'/FacebookMember.class.php';
if (class_exists('SocialMemberService') === false) include dirname(__FILE__).'/SocialMemberService.class.php';

$socialMemberService = new SocialMemberService();