<?php
	@include "../conf/merchant.php";	
	if(!$linkprice[chk])exit;

	// ��ũ�����̽� ��Ű���� ����	
	if(!get_cfg_var("register_globals"))
	{
		$a_id  = $_REQUEST["a_id"];
		$m_id  = $_REQUEST["m_id"];
		$p_id  = $_REQUEST["p_id"];
		$l_id  = $_REQUEST["l_id"];
		$l_cd1 = $_REQUEST["l_cd1"];
		$l_cd2 = $_REQUEST["l_cd2"];
		$rd    = $_REQUEST["rd"];
		$url   = $_REQUEST["url"];
	}

	if($a_id=="" or $m_id=="" or $p_id=="" or $l_id=="" or $l_cd1=="" or $l_cd2=="" or $rd=="" or $url=="") 
	{
		echo ("
<html><head><script language=\"javascript\">
<!--
    alert('LPMS: ������ �� �����ϴ�. ����Ʈ ����ڿ��� �����Ͻñ� �ٶ��ϴ�.');
    history.go(-1);
//-->
</script></head></html>
		");
		exit;
	}

	Header("P3P:CP=\"NOI DEVa TAIa OUR BUS UNI\"");
	
	If($rd==0) SetCookie("LPINFO","$a_id|$p_id|$l_id|$l_cd1|$l_cd2",0,"/");
	else       SetCookie("LPINFO","$a_id|$p_id|$l_id|$l_cd1|$l_cd2",time()+($rd*24*60*60),"/");

	Header("Location: $url");
	Header("URI: $url");
?>