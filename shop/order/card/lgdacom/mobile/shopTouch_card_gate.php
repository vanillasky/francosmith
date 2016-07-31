<?php

	include dirname(__FILE__)."/../../../../conf/config.mobileShop.php";
	include dirname(__FILE__)."/../../../../conf/pg_mobile.lgdacom.php";

	// ������ ����
	$pg_mobile['zerofee']	= ( $pg_mobile['zerofee'] == "yes" ? '1' : '0' );			// ������ ���� (Y:1 / N:0)

	// ��ǰ ����
	if(!preg_match('/mypage/',$_SERVER['SCRIPT_NAME'])){
		$item = $cart -> item;
	}
	foreach($item as $v){
		$i++;
		if($i == 1) $ordnm = $v['goodsnm'];
	}
	if($i > 1)$ordnm .= " ��".($i-1)."��";

    /*
     * [���� ������û ������(STEP2-1)]
     *
     * ���������������� �⺻ �Ķ���͸� ���õǾ� ������, ������ �ʿ��Ͻ� �Ķ���ʹ� �����޴����� �����Ͻþ� �߰� �Ͻñ� �ٶ��ϴ�.     
     */

    /*
     * 1. �⺻���� ������û ���� ����
     * 
     * �⺻������ �����Ͽ� �ֽñ� �ٶ��ϴ�.(�Ķ���� ���޽� POST�� ����ϼ���)
     */
    $CST_PLATFORM               = $pg_mobile['serviceType'];					//LG�ڷ��� ���� ���� ����(test:�׽�Ʈ, service:����)
    $CST_MID                    = $pg_mobile['id'];							//�������̵�(LG�ڷ������� ���� �߱޹����� �������̵� �Է��ϼ���)
                                                                        //�׽�Ʈ ���̵�� 't'�� �ݵ�� �����ϰ� �Է��ϼ���.
    $LGD_MID                    = (("test" == $CST_PLATFORM)?"t":"").$pg_mobile['id'];  //�������̵�(�ڵ�����)
    $LGD_OID                    = $_POST['ordno'];						//�ֹ���ȣ(�������� ����ũ�� �ֹ���ȣ�� �Է��ϼ���)
    $LGD_AMOUNT                 = $_POST['settleprice'];				//�����ݾ�("," �� ������ �����ݾ��� �Է��ϼ���)
    $LGD_BUYER                  = $_POST["nameOrder"];					//�����ڸ�
    $LGD_PRODUCTINFO            = $ordnm;								//��ǰ��
    $LGD_BUYEREMAIL             = $_POST["email"];						//������ �̸���
    $LGD_TIMESTAMP              = date(YmdHms);                         //Ÿ�ӽ�����
    $LGD_CUSTOM_SKIN            = $pg_mobile['skin']?$pg_mobile['skin']:"blue";		//�������� ����â ��Ų (red, blue, cyan, green, yellow)
	$LGD_NOINTINF				= $pg_mobile['zerofee'] == '1' ? $pg_mobile['zerofee_period'] : '' ;		// Ư��ī��/Ư�����������ڼ���
	$LGD_INSTALLRANGE			= $pg_mobile['quota'];							// �Ϲ��ҺαⰣ
	$CASHRECEIPTYN				= $pg_mobile['receipt'] == 'Y' ? 'Y' : 'N';	// ���ݿ����� ��뿩�� Y/N

	$LGD_ESCROW_USEYN			= $_POST['escrow'];	// ����ũ�� ��뿩�� Y/N
	$LGD_ESCROW_ZIPCODE			= implode("-",$_POST['zipcode']);
	$LGD_ESCROW_ADDRESS1		= $_POST['address'];
	$LGD_ESCROW_ADDRESS2		= $_POST['address_sub'];
	$LGD_ESCROW_BUYERPHONE		= implode("-",$_POST['mobileOrder']);

	switch ($_POST[settlekind]){
		case "c":	// �ſ�ī��
			$LGD_CUSTOM_USABLEPAY		= "SC0010";
			break;
//		case "o":	// ������ü
//			$LGD_CUSTOM_USABLEPAY		= "SC0030";
//			break;
		case "v":	// �������
			$LGD_CUSTOM_USABLEPAY		= "SC0040";
			break;
		case "h":	// �ڵ���
			$LGD_CUSTOM_USABLEPAY		= "SC0060";
			break;
	}

	$configPath 				= $_SERVER['DOCUMENT_ROOT'].$cfg['rootDir']."/conf/lgdacom_mobile"; 						//LG�ڷ��޿��� ������ ȯ������("/conf/lgdacom.conf") ��ġ ����. 	    
    
    /*
     * �������(������) ���� ������ �Ͻô� ��� �Ʒ� LGD_CASNOTEURL �� �����Ͽ� �ֽñ� �ٶ��ϴ�. 
     */    
    $LGD_CASNOTEURL				= "http://".$_SERVER['HTTP_HOST'].$cfg['rootDir']."/order/card/lgdacom/cas_noteurl.php";    

    /*
     * LGD_RETURNURL �� �����Ͽ� �ֽñ� �ٶ��ϴ�. �ݵ�� ���� �������� ������ ����Ʈ�� ��  ȣ��Ʈ�̾�� �մϴ�. �Ʒ� �κ��� �ݵ�� �����Ͻʽÿ�.
     */    
    $LGD_RETURNURL				= "http://".$_SERVER['HTTP_HOST'].$cfg['rootDir']."/order/card/lgdacom/mobile/shopTouch_cart_return.php";        
    /*
     *************************************************
     * 2. MD5 �ؽ���ȣȭ (�������� ������) - BEGIN
     * 
     * MD5 �ؽ���ȣȭ�� �ŷ� �������� �������� ����Դϴ�. 
     *************************************************
     *
     * �ؽ� ��ȣȭ ����( LGD_MID + LGD_OID + LGD_AMOUNT + LGD_TIMESTAMP + LGD_MERTKEY )
     * LGD_MID          : �������̵�
     * LGD_OID          : �ֹ���ȣ
     * LGD_AMOUNT       : �ݾ�
     * LGD_TIMESTAMP    : Ÿ�ӽ�����
     * LGD_MERTKEY      : ����MertKey (mertkey�� ���������� -> ������� -> ���������������� Ȯ���ϽǼ� �ֽ��ϴ�)
     *
     * MD5 �ؽ������� ��ȣȭ ������ ����
     * LG�ڷ��޿��� �߱��� ����Ű(MertKey)�� ȯ�漳�� ����(lgdacom/conf/mall.conf)�� �ݵ�� �Է��Ͽ� �ֽñ� �ٶ��ϴ�.
     */
    require_once(dirname(__FILE__)."/XPayClient.php");
    $xpay = &new XPayClient($configPath, $LGD_PLATFORM);
   	$xpay->Init_TX($LGD_MID);

    $LGD_HASHDATA = md5($LGD_MID.$LGD_OID.$LGD_AMOUNT.$LGD_TIMESTAMP.$xpay->config[$LGD_MID]);
    $LGD_CUSTOM_PROCESSTYPE = "TWOTR";
    /*
     *************************************************
     * 2. MD5 �ؽ���ȣȭ (�������� ������) - END
     *************************************************
     */
?>


<script language="javascript" src="http://xpay.lgdacom.net/xpay/js/xpay_crossplatform.js" type="text/javascript"></script>
<script type="text/javascript">

/*
* iframe���� ����â�� ȣ���Ͻñ⸦ ���Ͻø� iframe���� ���� (������ ���� �Ұ�)
*/
	var LGD_window_type = "iframe"; 
/*
* �����Ұ�
*/
function launchCrossPlatform(){
      lgdwin = open_paymentwindow(document.getElementById('LGD_PAYINFO'), '<?= $CST_PLATFORM ?>', LGD_window_type);
}
/*
* FORM ��  ���� ����
*/
function getFormObject() {
        return document.getElementById("LGD_PAYINFO");
}
/*
* �Ϲݿ� ��������(�Լ����� ���� �Ұ�)
*/
function setLGDResult(){
	if( LGD_window_type == 'iframe' ){
		document.getElementById('LGD_PAYMENTWINDOW').style.display = "none";
		document.getElementById('LGD_RESPCODE').value = lgdwin.contentWindow.document.getElementById('LGD_RESPCODE').value; 
		document.getElementById('LGD_RESPMSG').value = lgdwin.contentWindow.document.getElementById('LGD_RESPMSG').value;
		if(lgdwin.contentWindow.document.getElementById('LGD_PAYKEY') != null){
			document.getElementById('LGD_PAYKEY').value = lgdwin.contentWindow.document.getElementById('LGD_PAYKEY').value;
		}
	}  else {
		document.getElementById('LGD_RESPCODE').value = lgdwin.document.getElementById('LGD_RESPCODE').value; 
		document.getElementById('LGD_RESPMSG').value = lgdwin.document.getElementById('LGD_RESPMSG').value;
		if(lgdwin.document.getElementById('LGD_PAYKEY') != null){
			document.getElementById('LGD_PAYKEY').value = lgdwin.document.getElementById('LGD_PAYKEY').value;
		}
	}
	
	if(document.getElementById('LGD_RESPCODE').value == '0000' ){
		getFormObject().target = "_self";
		getFormObject().action = "shopTouch_card_return.php";
		getFormObject().submit();
	} else {
		alert(document.getElementById('LGD_RESPMSG').value);
	}
	
}
/*
* ����Ʈ���� ��������(�Լ����� ���� �Ұ�)
*/

function doSmartXpay(){

        var LGD_RESPCODE        = dpop.getData('LGD_RESPCODE');       //����ڵ�
        var LGD_RESPMSG         = dpop.getData('LGD_RESPMSG');        //����޼���

        if( "0000" == LGD_RESPCODE ) { //��������
            var LGD_PAYKEY      = dpop.getData('LGD_PAYKEY');         //LG�ڷ��� ����KEY
            document.getElementById('LGD_PAYKEY').value = LGD_PAYKEY;
            getFormObject().submit();
        } else { //��������
            alert("������ �����Ͽ����ϴ�. " + LGD_RESPMSG);
        }
        
}

</script>
<!--  ���� �Ұ�(IFRAME ��Ľ� ���)   -->
<div id="LGD_PAYMENTWINDOW" style="display:none; width:100%;">
     <iframe id="LGD_PAYMENTWINDOW_IFRAME" name="LGD_PAYMENTWINDOW_IFRAME" width="100%" scrolling="no" frameborder="0">
     </iframe>
</div>

<form method="post" id="LGD_PAYINFO" action="shopTouch_card_return.php">

<input type="hidden" name="CST_PLATFORM"                value="<?= $CST_PLATFORM ?>">                   <!-- �׽�Ʈ, ���� ���� -->
<input type="hidden" name="CST_MID"                     value="<?= $CST_MID ?>">                        <!-- �������̵� -->
<input type="hidden" name="LGD_MID"                     value="<?= $LGD_MID ?>">                        <!-- �������̵� -->
<input type="hidden" name="LGD_OID"                     value="<?= $LGD_OID ?>">                        <!-- �ֹ���ȣ -->
<input type="hidden" name="LGD_BUYER"                   value="<?= $LGD_BUYER ?>">           			<!-- ������ -->
<input type="hidden" name="LGD_PRODUCTINFO"             value="<?= $LGD_PRODUCTINFO ?>">     			<!-- ��ǰ���� -->
<input type="hidden" name="LGD_AMOUNT"                  value="<?= $LGD_AMOUNT ?>">                     <!-- �����ݾ� -->
<input type="hidden" name="LGD_BUYEREMAIL"              value="<?= $LGD_BUYEREMAIL ?>">                 <!-- ������ �̸��� -->
<input type="hidden" name="LGD_CUSTOM_SKIN"             value="<?= $LGD_CUSTOM_SKIN ?>">                <!-- ����â SKIN -->
<input type="hidden" name="LGD_CUSTOM_PROCESSTYPE"      value="<?= $LGD_CUSTOM_PROCESSTYPE ?>">         <!-- Ʈ����� ó����� -->
<input type="hidden" name="LGD_TIMESTAMP"               value="<?= $LGD_TIMESTAMP ?>">                  <!-- Ÿ�ӽ����� -->
<input type="hidden" name="LGD_HASHDATA"                value="<?= $LGD_HASHDATA ?>">                   <!-- MD5 �ؽ���ȣ�� -->
<input type="hidden" name="LGD_VERSION"         		value="PHP_SmartXPay_1.0">				   	    <!-- �������� (�������� ������) -->
<input type="hidden" name="LGD_USABLECARD"  			value="41:51:61:71">							<!-- ��밡���� �ſ�ī��  -->

<!-- �������(������) ���������� �Ͻô� ���  �Ҵ�/�Ա� ����� �뺸�ޱ� ���� �ݵ�� LGD_CASNOTEURL ������ LG �ڷ��޿� �����ؾ� �մϴ� . -->
<!-- input type="hidden" name="LGD_CASNOTEURL"          	value="<?= $LGD_CASNOTEURL ?>"-->			<!-- ������� NOTEURL -->  
<input type="hidden" name="LGD_RETURNURL"   			value="<?= $LGD_RETURNURL ?>">      			<!-- �������������--> 
<input type="hidden" name="LGD_CUSTOM_USABLEPAY"   		value="<?= $LGD_CUSTOM_USABLEPAY ?>">			<!-- ��밡�ɰ�������--> 

<? if( $_POST[settlekind] == 'c') { ?>
<!-- �Һΰ��� ����â ��� ���� �������� hidden���� -->
<input type="hidden" name="LGD_INSTALLRANGE"			value="<?= $LGD_INSTALLRANGE ?>">				<!-- �Һΰ��� ����-->
<!-- ������ �Һ�(������ �����δ�) ���θ� �����ϴ� hidden���� -->
<input type="hidden" name="LGD_NOINTINF"				value="<?= $LGD_NOINTINF ?>">					<!-- �ſ�ī�� ������ �Һ� �����ϱ� -->
<? } ?>

<? if( $_POST[settlekind] == 'o' || $_POST[settlekind] == 'v' ) { ?>
<!--������ü|�������Ա�(�������)-->
<input type="hidden" name="LGD_CASHRECEIPTYN"			value="<?= $CASHRECEIPTYN ?>">					<!-- ���ݿ����� �̻�뿩��(Y:�̻��,N:���) -->
<? } ?>

<? if( $_POST[settlekind] == 'v'){ ?>
<!-- �������(������) ���������� �Ͻô� ���  �Ҵ�/�Ա� ����� �뺸�ޱ� ���� �ݵ�� LGD_CASNOTEURL ������ LG �����޿� �����ؾ� �մϴ� . -->
<input type="hidden" name="LGD_CASNOTEURL"          	value="<?= $LGD_CASNOTEURL ?>">			<!-- ������� NOTEURL -->  
<? } ?>

<input type="hidden" name="LGD_ESCROW_USEYN"			value="<?= $LGD_ESCROW_USEYN; ?>">					<!-- ����ũ�� ���� : ����(Y),������(N)-->
<? if($LGD_ESCROW_USEYN == 'Y'){ ?>
	<? foreach($cart->item as $row) { ?>
	<input type="hidden" name="LGD_ESCROW_GOODID"			value="<?=$row['goodsno']?>">						<!-- ����ũ�λ�ǰ��ȣ -->
	<input type="hidden" name="LGD_ESCROW_GOODNAME"			value="<?=$row['goodsnm']?>">						<!-- ����ũ�λ�ǰ�� -->
	<input type="hidden" name="LGD_ESCROW_GOODCODE"			value="">								<!-- ����ũ�λ�ǰ�ڵ� -->
	<input type="hidden" name="LGD_ESCROW_UNITPRICE"		value="<?=$row['price']+$row['addprice']?>">			<!-- ����ũ�λ�ǰ���� -->
	<input type="hidden" name="LGD_ESCROW_QUANTITY"			value="<?=$row['ea']?>">							<!-- ����ũ�λ�ǰ���� -->
	<?}?>
<input type="hidden" name="LGD_ESCROW_ZIPCODE" value="<?= $LGD_ESCROW_ZIPCODE ?>">		<!-- ����ũ�ι���������ȣ -->
<input type="hidden" name="LGD_ESCROW_ADDRESS1"	value="<?= $LGD_ESCROW_ADDRESS1 ?>">						<!-- ����ũ�ι�����ּҵ����� -->
<input type="hidden" name="LGD_ESCROW_ADDRESS2"	value="<?= $LGD_ESCROW_ADDRESS2 ?>">					<!-- ����ũ�ι�����ּһ� -->
<input type="hidden" name="LGD_ESCROW_BUYERPHONE" value="<?= $LGD_ESCROW_BUYERPHONE ?>">	<!-- ����ũ�α������޴�����ȣ -->
<? } ?>

<!-- ���� �Ұ� ( ���� �� �ڵ� ���� ) -->
<input type="hidden" name="LGD_RESPCODE" id="LGD_RESPCODE">
<input type="hidden" name="LGD_RESPMSG" id="LGD_RESPMSG">
<input type="hidden" name="LGD_PAYKEY"  id="LGD_PAYKEY">      
</form>

<script>
$('#LGD_PAYMENTWINDOW_IFRAME').resize(function(){
	if(parseInt($('#LGD_PAYMENTWINDOW_IFRAME').css('width'))>parseInt($('#wrap').css('width'))){
		$('#wrap').css('width',$('#LGD_PAYMENTWINDOW_IFRAME').css('width'));
	}
	setTimeout(scrollTo, 0, 0, 0);
});
</script>