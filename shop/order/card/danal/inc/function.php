<?php
	/********************************************************************************
	 *
	 * �ٳ� �޴��� ����
	 *
	 * - Function Library
	 *	���� ������ �ʿ��� Function �� ������ ���� 
	 *
	 * ���� �ý��� ������ ���� ���ǻ����� �����ø� ���񽺰��������� ���� �ֽʽÿ�.
	 * DANAL Commerce Division Technique supporting Team
	 * EMail : tech@danal.co.kr
	 *
	 ********************************************************************************/
	include dirname(__FILE__).'/../../../../lib/library.php';

	$danal = Core::loader('Danal');
	$cart = Core::loader('cart', $_COOKIE['gd_isDirect']);
	$config = Core::loader('config');

	$shopConfig = $config->load('config');		// ���� ������
	$danalCfg = $danal->getConfig();			// �ٳ� ������

	/******************************************************
	 *
	 * Client Module ��μ���
	 *
	 ******************************************************/
	$TeleditBinPath = "./bin";

	/******************************************************
	 * ID		: �ٳ����� ������ �帰 CPID
	 * PWD		: �ٳ����� ������ �帰 CPPWD
	 * AMOUNT	: ���� �ݾ�
	 ******************************************************/
	$ID  = $danalCfg['M_CPID'];
	$PWD = $danalCfg['servicePwd'];
	$AMOUNT = "";

	/******************************************************
	 * - CallTeledit
	 * - CallTeleditCancel
	 *	�ٳ� ������ ����ϴ� �Լ��Դϴ�.
	 *	$Debug�� true�ϰ�� ���������� debugging �޽����� ����մϴ�.
	 ******************************************************/
	function CallTeledit($TransR,$Debug=false) {

		global $TeleditBinPath;

		$Bin = "SClient";
		$arg = MakeParam( $TransR );

		$Input = $TeleditBinPath."/".$Bin." \"$arg\"";

		exec( $Input,$Output,$Ret );

		if( $Debug )
		{
			echo "Exec : ".trim($Input)."<BR>";
			echo "Ret : ".$Ret."<BR>";

			for( $i=0;$i<count($Output);$i++ )
			{
				echo( "Out Line[$i]: ".trim($Output[$i])."<BR>" );
			}
		}

		$MapOutput = Parsor( $Output );

		return $MapOutput;
	}

	function CallTeleditCancel($TransR,$Debug=false) {

		global $TeleditBinPath;

		$Bin = "BackDemo";
	//      $Bin = "AutoCancel"; // For Window

		$arg = MakeParam( $TransR );

		$Input = $TeleditBinPath."/".$Bin." \"$arg\"";

		exec( $Input,$Output,$Ret );

		if( $Debug )
		{
			echo "Exec : ".trim($Input)."<BR>";
			echo "Ret : ".$Ret."<BR>";

			for( $i=0;$i<count($Output);$i++ )
			{
				echo( "Out Line[$i]: ".trim($Output[$i])."<BR>" );
			}
		}

		$MapOutput = Parsor( $Output );

		return $MapOutput;
	}

	function Parsor($str,$sep1="&",$sep2="=") {

		$Out = array();
		$in = "";

		if( is_array($str) )
		{
			for( $i=0;$i<count($str);$i++ )
			{
				$in .= $str[$i].$sep1;
			}
		}
		else
		{
			$in = $str;
		}

		$tok = explode( $sep1,$in );

		for( $i=0;$i<count($tok);$i++ )
		{
			$tmp = explode( $sep2,$tok[$i] );

			$name = trim($tmp[0]);
			$value = trim($tmp[1]);

			for( $j=2;$j<count($tmp);$j++ )
				$value .= $sep2.trim($tmp[$j]);

			$Out[$name] = urldecode($value);
		}

		return $Out;
	}

	function MakeFormInput($arr,$ext=array(),$Prefix="") {

		$PreLen = strlen( trim($Prefix) );

		$keys = array_keys($arr);

		for( $i=0;$i<count($keys);$i++ )
		{
			$key = $keys[$i];

			if( trim($key) == "" ) continue;

			if( !in_array($key,$ext) && substr($key,0,$PreLen) == $Prefix )
			{
				echo( "<input type=\"hidden\" name=\"".$key."\" value=\"".$arr[$key]."\">\n" );
			}
		}
	}

	function MakeAddtionalInput($Trans,$HTTPVAR,$Names) {

		while( $name=array_pop($Names) ) 
		{
			$Trans[$name] = $HTTPVAR[$name];
		}

		return $Trans;
	}

	function MakeItemInfo($ItemAmt,$ItemCode,$ItemName) {

		$ItemInfo = substr($ItemCode,0,1) ."|". $ItemAmt ."|1|". $ItemCode ."|". $ItemName;
		return $ItemInfo;
	}

	function MakeParam($arr) {

		$ret = array();
		$keys = array_keys($arr);

		for( $i=0;$i<count($keys);$i++ )
		{
			$key = $keys[$i];
			array_push( $ret,$key."=".$arr[$key] );
		}

		return MakeInfo($ret);
	}

	function MakeInfo($Arr,$joins=";") {

		return join( $joins,$Arr );
	}

	function GetItemName($CPName,$nCPName,$ItemName,$nItemName) {

		$convItemName = "(". substr($CPName,0,$nCPName) .") ". substr($ItemName,0,$nItemName);

		return $convItemName;
	}

	function GetCIURL($IsUseCI,$CIURL) {

		/*
		 * Default Danal CI
		 */
		$URL = "https://ui.teledit.com/Danal/Teledit/Web/images/customer_logo.gif";

		if( $IsUseCI == "Y" && !is_null($CIURL) )
		{
			$URL = $CIURL;
		}
	
		return $URL;
	} 

	function Map2Str($arr) {

		$ret = array();
		$keys = array_keys($arr);

		for( $i=0;$i<count($keys);$i++ )
		{
			$key = $keys[$i];

			if( !trim($key) ) continue;

			array_push( $ret,$key." = ".$arr[$key] );
		}

		return join( "<BR>",$ret );
	}

	function GetBgColor($BgColor) {

		/*
		 * Default : Blue
		 */
		$Color = 0;

		if( intval($BgColor) > 0 && intval($BgColor) < 11 )
		{
			$Color = $BgColor;
		}

		return sprintf( "%02d",$Color );
	}
?>
