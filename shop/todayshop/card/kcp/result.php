<?
    /* ============================================================================== */
    /* =   PAGE : ��� ó�� PAGE                                                    = */
    /* = -------------------------------------------------------------------------- = */
    /* =   Copyright (c)  2006   KCP Inc.   All Rights Reserverd.                   = */
    /* ============================================================================== */
?>
<?
    /* ============================================================================== */
    /* =   ���� ���                                                                = */
    /* = -------------------------------------------------------------------------- = */
    $req_tx           = $_POST[ "req_tx"         ];      // ��û ����(����/���)
    $use_pay_method   = $_POST[ "use_pay_method" ];      // ��� ���� ����
    $bSucc            = $_POST[ "bSucc"          ];      // ��ü DB ����ó�� �Ϸ� ����
    /* = -------------------------------------------------------------------------- = */
    $res_cd           = $_POST[ "res_cd"         ];      // ��� �ڵ�
    $res_msg          = $_POST[ "res_msg"        ];      // ��� �޽���
    /* = -------------------------------------------------------------------------- = */
    $ordr_idxx        = $_POST[ "ordr_idxx"      ];      // �ֹ���ȣ
    $tno              = $_POST[ "tno"            ];      // KCP �ŷ���ȣ
    $good_mny         = $_POST[ "good_mny"       ];      // ���� �ݾ�
    $good_name        = $_POST[ "good_name"      ];      // ��ǰ��
    $buyr_name        = $_POST[ "buyr_name"      ];      // �����ڸ�
    $buyr_tel1        = $_POST[ "buyr_tel1"      ];      // ������ ��ȭ��ȣ
    $buyr_tel2        = $_POST[ "buyr_tel2"      ];      // ������ �޴�����ȣ
    $buyr_mail        = $_POST[ "buyr_mail"      ];      // ������ E-Mail
    /* = -------------------------------------------------------------------------- = */
    // �ſ�ī��
    $card_cd          = $_POST[ "card_cd"        ];      // ī�� �ڵ�
    $card_name        = $_POST[ "card_name"      ];      // ī���
    $app_time         = $_POST[ "app_time"       ];      // ���νð� (����)
    $app_no           = $_POST[ "app_no"         ];      // ���ι�ȣ
    $quota            = $_POST[ "quota"          ];      // �Һΰ���
    /* = -------------------------------------------------------------------------- = */
    // ������ü
    $bank_name        = $_POST[ "bank_name"      ];      // �����
    /* = -------------------------------------------------------------------------- = */
    // �������
    $bankname         = $_POST[ "bankname"       ];      // �Ա� ����
    $depositor        = $_POST[ "depositor"      ];      // �Աݰ��� ������
    $account          = $_POST[ "account"        ];      // �Աݰ��� ��ȣ
    /* = -------------------------------------------------------------------------- = */
    // ����Ʈ
    $epnt_issu        = $_POST[ "epnt_issu"      ];      // ����Ʈ ���񽺻�
    $add_pnt          = $_POST[ "add_pnt"        ];      // �߻� ����Ʈ
	$use_pnt          = $_POST[ "use_pnt"        ];      // ��밡�� ����Ʈ
	$rsv_pnt          = $_POST[ "rsv_pnt"        ];      // ���� ����Ʈ
	$pnt_app_time     = $_POST[ "pnt_app_time"   ];      // ���νð�
	$pnt_app_no       = $_POST[ "pnt_app_no"     ];      // ���ι�ȣ
	$pnt_amount       = $_POST[ "pnt_amount"     ];      // �����ݾ� or ���ݾ�
	/* = -------------------------------------------------------------------------- = */
	// ���ݿ�����
	$cash_yn          = $_POST[ "cash_yn"        ];      //���ݿ����� ��� ���� 
	$cash_authno      = $_POST[ "cash_authno"    ];      //���� ������ ���� ��ȣ
	$cash_tr_code     = $_POST[ "cash_tr_code"   ];      //���� ������ ���� ����
	$cash_id_info     = $_POST[ "cash_id_info"   ];      //���� ������ ��� ��ȣ


    $req_tx_name = "";

    if( $req_tx == "pay" )
    {
        $req_tx_name = "����";
    }
    else if( $req_tx == "mod" )
    {
        $req_tx_name = "���/����";
    }
?>
    <html>
    <head>
    <link href="css/sample.css" rel="stylesheet" type="text/css">
    <script language="javascript">
        <!-- �ſ�ī�� ������ ���� ��ũ��Ʈ -->
        function receiptView(tno)
        {
            receiptWin = "http://admin.kcp.co.kr/Modules/Sale/Card/ADSA_CARD_BILL_Receipt.jsp?c_trade_no=" + tno
            window.open(receiptWin , "" , "width=420, height=670")
        }
    </script>
    </head>
    <body>
    <center>
    <table border='0' cellpadding='0' cellspacing='1' width='500' align='center'>
        <tr>
            <td align="left" height="25"><img src="./img/KcpLogo.jpg" border="0" width="65" height="50"></td>
            <td align='right' class="txt_main">KCP Online Payment System [AX_HUB PHP Version]</td>
        </tr>
        <tr>
            <td bgcolor="CFCFCF" height='3' colspan='2'></td>
        </tr>
        <tr>
            <td colspan="2">
                <br>
                <table width="90%" align="center">
                    <tr>
                        <td bgcolor="CFCFCF" height='2'></td>
                    </tr>
                    <tr>
                        <td align="center">��� ������(<?=$req_tx_name?>)</td>
                    </tr>
                    <tr>
                        <td bgcolor="CFCFCF" height='2'></td>
                    </tr>
                </table>
<?
    if ( $req_tx == "pay" )                           // �ŷ� ���� : ����
    {
        if ( $bSucc != "false" )                      // ��ü DB ó�� ����
        {
            if ( $res_cd == "0000" )                  // ���� ����
            {
?>
                <table width="85%" align="center" border='0' cellpadding='0' cellspacing='1'>
                    <tr>
                        <td>����ڵ�</td>
                        <td><?=$res_cd?></td>
                    </tr>
                    <tr><td colspan="2"><IMG SRC="./img/dot_line.gif" width="100%"></td></tr>
                    <tr>
                        <td>��� �޼���</td>
                        <td><?=$res_msg?></td>
                    </tr>
                    <tr><td colspan="2"><IMG SRC="./img/dot_line.gif" width="100%"></td></tr>
                    <tr>
                        <td>�ֹ���ȣ</td>
                        <td><?=$ordr_idxx?></td>
                    </tr>
                    <tr><td colspan="2"><IMG SRC="./img/dot_line.gif" width="100%"></td></tr>
                    <tr>
                        <td>KCP �ŷ���ȣ</td>
                        <td><?=$tno?></td>
                    </tr>
                    <tr><td colspan="2"><IMG SRC="./img/dot_line.gif" width="100%"></td></tr>
                    <tr>
                        <td>�����ݾ�</td>
                        <td><?=$good_mny?></td>
                    </tr>
                    <tr><td colspan="2"><IMG SRC="./img/dot_line.gif" width="100%"></td></tr>
                    <tr>
                        <td>��ǰ��</td>
                        <td><?=$good_name?></td>
                    </tr>
                    <tr><td colspan="2"><IMG SRC="./img/dot_line.gif" width="100%"></td></tr>
                    <tr>
                        <td>�ֹ��ڸ�</td>
                        <td><?=$buyr_name?></td>
                    </tr>
                    <tr><td colspan="2"><IMG SRC="./img/dot_line.gif" width="100%"></td></tr>
                    <tr>
                        <td>�ֹ��� ��ȭ��ȣ</td>
                        <td><?=$buyr_tel1?></td>
                    </tr>
                    <tr><td colspan="2"><IMG SRC="./img/dot_line.gif" width="100%"></td></tr>
                    <tr>
                        <td>�ֹ��� �޴�����ȣ</td>
                        <td><?=$buyr_tel2?></td>
                    </tr>
                    <tr><td colspan="2"><IMG SRC="./img/dot_line.gif" width="100%"></td></tr>
                    <tr>
                        <td>E-mail</td>
                        <td><?=$buyr_mail?></td>
                    </tr>
<?
                if ( $use_pay_method == "100000000000" )       // �ſ�ī��
                {
?>
                    <tr><td colspan="2"><IMG SRC="./img/dot_line.gif" width="100%"></td></tr>
                    <tr>
                        <td>�������� </td>
                        <td>�ſ�ī��</td>
                    </tr>
                    <tr><td colspan="2"><IMG SRC="./img/dot_line.gif" width="100%"></td></tr>
                    <tr>
                        <td>����ī��</td>
                        <td><?=$card_cd?> / <?=$card_name?></td>
                    </tr>
                    <tr><td colspan="2"><IMG SRC="./img/dot_line.gif" width="100%"></td></tr>
                    <tr>
                        <td>���νð�</td>
                        <td><?=$app_time?></td>
                    </tr>
                    <tr><td colspan="2"><IMG SRC="./img/dot_line.gif" width="100%"></td></tr>
                    <tr>
                        <td>���ι�ȣ</td>
                        <td><?=$app_no?></td>
                    </tr>
                    <tr><td colspan="2"><IMG SRC="./img/dot_line.gif" width="100%"></td></tr>
                    <tr>
                        <td>�Һΰ���</td>
                        <td><?=$quota?></td>
                    </tr>
<?
                    if ( $epnt_issu == "SCSK" || $epnt_issu == "SCWB" )
                    {
?>
                        <tr><td colspan="2"><IMG SRC="./img/dot_line.gif" width="100%"></td></tr>
                        <tr>
                            <td>����Ʈ��</td>
                            <td><?=$epnt_issu?></td>
                        </tr>
                        <tr><td colspan="2"><IMG SRC="./img/dot_line.gif" width="100%"></td></tr>
	                    <tr>
	                        <td>����Ʈ ���νð�</td>
	                        <td><?=$pnt_app_time?></td>
	                    </tr>
	                    <tr><td colspan="2"><IMG SRC="./img/dot_line.gif" width="100%"></td></tr>
	                    <tr>
	                        <td>����Ʈ ���ι�ȣ</td>
	                        <td><?=$pnt_app_no?></td>
	                    </tr>  
	                    <tr><td colspan="2"><IMG SRC="./img/dot_line.gif" width="100%"></td></tr>
	                    <tr>
	                        <td>�����ݾ� or ���ݾ�</td>
	                        <td><?=$pnt_amount?></td>
	                    </tr>
	                    <tr><td colspan="2"><IMG SRC="./img/dot_line.gif" width="100%"></td></tr>
	                    <tr>
	                        <td>�߻� ����Ʈ</td>
	                        <td><?=$add_pnt?></td>
	                    </tr>
	                    <tr><td colspan="2"><IMG SRC="./img/dot_line.gif" width="100%"></td></tr>
	                    <tr>
	                        <td>��밡�� ����Ʈ</td>
	                        <td><?=$use_pnt?></td>
	                    </tr>
	                    <tr><td colspan="2"><IMG SRC="./img/dot_line.gif" width="100%"></td></tr>
	                    <tr>
	                        <td>���� ����Ʈ</td>
	                        <td><?=$rsv_pnt?></td>
	                    </tr>
<? 
                    }
?>                  
                    <tr><td colspan="2"><IMG SRC="./img/dot_line.gif" width="100%"></td></tr>
                    <tr>
                        <td>�ſ�ī�� ������</td>
                        <td><input type="button" name="receiptView" value="������ Ȯ��" class="box" onClick="javascript:receiptView('<?=$tno?>')"></td>
                    </tr>
                    <tr><td colspan="2"><IMG SRC="./img/dot_line.gif" width="100%"></td></tr>
                    <tr>
                        <td colspan="2">�� ������ Ȯ���� ���������� ��쿡�� �����մϴ�.</td>
                    </tr>
                </table>
<?
                }
                else if ( $use_pay_method == "010000000000" )       // ������ü
                {
?>
                    <tr><td colspan="2"><IMG SRC="./img/dot_line.gif" width="100%"></td></tr>
                    <tr>
                        <td>�������� </td>
                        <td>������ü</td>
                    </tr>
                    <tr><td colspan="2"><IMG SRC="./img/dot_line.gif" width="100%"></td></tr>
                    <tr>
                        <td>��ü����</td>
                        <td><?=$bank_name?></td>
                    </tr>
                </table>
<?
                }
                else if ( $use_pay_method == "001000000000" )       // �������
                {
?>
                    <tr><td colspan="2"><IMG SRC="./img/dot_line.gif" width="100%"></td></tr>
                    <tr>
                        <td>�������� </td>
                        <td>�������</td>
                    </tr>
                    <tr><td colspan="2"><IMG SRC="./img/dot_line.gif" width="100%"></td></tr>
                    <tr>
                        <td>�Ա� ����</td>
                        <td><?=$bankname?></td>
                    </tr>
                    <tr><td colspan="2"><IMG SRC="./img/dot_line.gif" width="100%"></td></tr>
                    <tr>
                        <td>�Աݰ��� ������</td>
                        <td><?=$depositor?></td>
                    </tr>
                    <tr><td colspan="2"><IMG SRC="./img/dot_line.gif" width="100%"></td></tr>
                    <tr>
                        <td>�Աݰ��� ��ȣ</td>
                        <td><?=$account?></td>
                    </tr>
                </table>
<?
                }
                else if ( $use_pay_method == "000100000000" )         // ����Ʈ
                {
?>
                    <tr><td colspan="2"><IMG SRC="./img/dot_line.gif" width="100%"></td></tr>
                    <tr>
                        <td>�������� </td>
                        <td>����Ʈ</td>
                    </tr>                    
                    <tr>
                        <td>����Ʈ��</td>
                        <td><?=$epnt_issu?></td>
                    </tr>
                    <tr><td colspan="2"><IMG SRC="./img/dot_line.gif" width="100%"></td></tr>
                    <tr>
                        <td>����Ʈ ���νð�</td>
                        <td><?=$pnt_app_time?></td>
                    </tr>
                    <tr><td colspan="2"><IMG SRC="./img/dot_line.gif" width="100%"></td></tr>
                    <tr>
                        <td>����Ʈ ���ι�ȣ</td>
                        <td><?=$pnt_app_no?></td>
                    </tr>  
                    <tr><td colspan="2"><IMG SRC="./img/dot_line.gif" width="100%"></td></tr>
                    <tr>
                        <td>�����ݾ� or ���ݾ�</td>
                        <td><?=$pnt_amount?></td>
                    </tr>
                    <tr><td colspan="2"><IMG SRC="./img/dot_line.gif" width="100%"></td></tr>
                    <tr>
                        <td>�߻� ����Ʈ</td>
                        <td><?=$add_pnt?></td>
                    </tr>
                    <tr><td colspan="2"><IMG SRC="./img/dot_line.gif" width="100%"></td></tr>
                    <tr>
                        <td>��밡�� ����Ʈ</td>
                        <td><?=$use_pnt?></td>
                    </tr>
                    <tr><td colspan="2"><IMG SRC="./img/dot_line.gif" width="100%"></td></tr>
                    <tr>
                        <td>���� ����Ʈ</td>
                        <td><?=$rsv_pnt?></td>
                    </tr>
                </table>
<?
                }
                else if ( $use_pay_method == "000010000000" )       // �޴���
                {
?>
                    <tr><td colspan="2"><IMG SRC="./img/dot_line.gif" width="100%"></td></tr>
                    <tr>
                        <td>�������� </td>
                        <td>�޴���</td>
                    </tr>
                    <tr><td colspan="2"><IMG SRC="./img/dot_line.gif" width="100%"></td></tr>
                    <tr>
                        <td>���νð�</td>
                        <td><?=$app_time?></td>
                    </tr>
                </table>
<?
                }
                else if ( $use_pay_method == "000000001000" )       // ��ǰ��
                {
?>
                    <tr><td colspan="2"><IMG SRC="./img/dot_line.gif" width="100%"></td></tr>
                    <tr>
                        <td>�������� </td>
                        <td>��ǰ��</td>
                    </tr>
                    <tr><td colspan="2"><IMG SRC="./img/dot_line.gif" width="100%"></td></tr>
                    <tr>
                        <td>���νð�</td>
                        <td><?=$app_time?></td>
                    </tr>
                </table>
<?
                }
                else if ( $use_pay_method == "000000000100" )       // ����ī��
                {
?>
                    <tr><td colspan="2"><IMG SRC="./img/dot_line.gif" width="100%"></td></tr>
                    <tr>
                        <td>�������� </td>
                        <td>����ī��</td>
                    </tr>
                </table>
<?
                }
                else if ( $use_pay_method == "000000000010" )       // ARS
                {
?>
                    <tr><td colspan="2"><IMG SRC="./img/dot_line.gif" width="100%"></td></tr>
                    <tr>
                        <td>�������� </td>
                        <td>ARS</td>
                    </tr>
                    <tr><td colspan="2"><IMG SRC="./img/dot_line.gif" width="100%"></td></tr>
                    <tr>
                        <td>���νð�</td>
                        <td><?=$app_time?></td>
                    </tr>
                </table>
<?
                }
            }
            else                                       // ���� ����
            {
?>
                <table width="85%" align="center" border='0' cellpadding='0' cellspacing='1'>
                    <tr>
                        <td>����ڵ�</td>
                        <td><?=$res_cd?></td>
                    </tr>
                    <tr><td colspan="2"><IMG SRC="./img/dot_line.gif" width="100%"></td></tr>
                    <tr>
                        <td>��� �޼���</td>
                        <td><?=$res_msg?></td>
                    </tr>
                </table>
<?
            }

        }
        else                                           // ��ü DB ó�� ����
        {
?>
                <table width="85%" align="center" border='0' cellpadding='0' cellspacing='1'>
                    <tr>
                        <td nowrap>��� ����ڵ�</td>
                        <td><?=$res_cd?></td>
                    </tr>
                    <tr><td colspan="2"><IMG SRC="./img/dot_line.gif" width="100%"></td></tr>
                    <tr>
                        <td nowrap>��� ��� �޼���</td>
                        <td><?=$res_msg?></td>
                    </tr>
                    <tr><td colspan="2"><IMG SRC="./img/dot_line.gif" width="100%"></td></tr>
                    <tr>
                        <td nowrap>�󼼸޼���</td>
                        <td>
<?
            if ( $res_cd == "0000" )
            {
                echo("������ ���������� �̷�������� ���θ����� ���� ����� ó���ϴ� �� ������ �߻��Ͽ� �ý��ۿ��� �ڵ����� ��� ��û�� �Ͽ����ϴ�. <br> ���θ��� ��ȭ�Ͽ� Ȯ���Ͻñ� �ٶ��ϴ�.");
            }
            else
            {
                echo("������ ���������� �̷�������� ���θ����� ���� ����� ó���ϴ� �� ������ �߻��Ͽ� �ý��ۿ��� �ڵ����� ��� ��û�� �Ͽ�����, <br> <b>��Ұ� ���� �Ǿ����ϴ�.</b><br> ���θ��� ��ȭ�Ͽ� Ȯ���Ͻñ� �ٶ��ϴ�.");
            }
?>
                        </td>
                    </tr>
                </table>
<?
        }
    }
    else if ( $req_tx == "mod" )                     // �ŷ� ���� : ���/����
    {
?>
                <table width="85%" align="center" border='0' cellpadding='0' cellspacing='1'>
                    <tr>
                        <td>����ڵ�</td>
                        <td><?=$res_cd?></td>
                    </tr>
                    <tr><td colspan="2"><IMG SRC="./img/dot_line.gif" width="100%"></td></tr>
                    <tr>
                        <td>��� �޼���</td>
                        <td><?=$res_msg?></td>
                    </tr>
                </table>
<?
    }
?>
                <table width="90%" align="center">
                    <tr>
                        <td bgcolor="CFCFCF" height='2'></td>
                    </tr>
                    <tr>
                        <td height='2'>&nbsp;</td>
                    </tr>
                </table>
            </td>
        </tr>
        <tr>
            <td bgcolor="CFCFCF" height='3' colspan='2'></td>
        </tr>
    </table>
    </center>
    </body>
    </html>
