<?
$integrate_cfg = array();

$integrate_cfg['channels'] = array(
				'enamoo' => !empty($cfg['shopName']) ? $cfg['shopName'] : '�̳���',
				'payco' => '������',
				'checkout' => '���̹�üũ�ƿ�',
				'shople' => '����',
				'ipay' => '����iPay',
				'mobile' => '����ϼ�',
				'todayshop' => '�����̼�',
				'pluscheese' => '�÷���ġ��',
				);

$integrate_cfg['inflows'] = array(
				"naverCheckout" => "���̹�üũ�ƿ�",
				"naver" => "���̹����ļ���",
				"naver_price" => "���̹����ݺ�",
				"danawa" => "�ٳ���",
				"mm" => "���̸���",
				"bb" => "����Ʈ���̾�",
				"omi" => "����",
				"enuri" => "������",
				//"yahooysp" => "����������",
				"yahoo_fss" => "�����мǼ�ȣ",
				"yahoo" => "���İ��ݺ�",
				//"interpark" => "������ũ���÷���",
				"openstyle" => "������ũ���½�Ÿ��",
				"openstyleOutlink" => "������ũ���½�Ÿ�Ͼƿ���ũ",
				"naver_pchs_040901" => "���̹����ļ�����õ����",
				"auctionos" => "���Ǿ�ٿ�",
				"daumCpc" => "���������Ͽ�",
				"cywordScrap" => "���̿��彺ũ��",
				);

$integrate_cfg['step'] = array(
				0	=> '�ֹ�����',
				1	=> '�Ա�Ȯ��',
				2	=> '����غ���',
				3	=> '�����',
				4	=> '��ۿϷ�',

				10	=> '�������',
				11	=> '��ҿϷ�',

				20	=> 'ȯ������',
				21	=> 'ȯ�ҿϷ�',

				30	=> '��ǰ����',
				31	=> '��ǰ�Ϸ�',

				40	=> '��ȯ����',
				41	=> '��ȯ�Ϸ�',

				50	=> '�����õ�',
				51	=> 'PGȮ�ο��',
				54	=> '��������',
				91  => '���ֹ�',

				99 => '�˼�����',	// ó�� �ܰ谡 ��Ȯ���� �ʾ�, ������ �ǸŰ������� ��ȸ�غ��� ��
);

// ��ۻ� ����

// �̳���
$query = "SELECT deliveryno AS code, deliverycomp AS name, useyn  FROM ".GD_LIST_DELIVERY." WHERE useyn = 'y' ORDER BY deliverycomp";
$res = $db->query($query);
while ($data=$db->fetch($res)){
	$integrate_cfg['dlv_company']['enamoo'][$data['code']] = $data['name'];
}

// ����
$integrate_cfg['dlv_company']['shople'] = array(
	'00001' => '�����ͽ�������',
	'00002' => '�����ù�',
	'00003' => '�簡���ͽ�����',
	'00006' => '���ο�ĸ',
	'00007' => '��ü���ù�',
	'00008' => '������',
	'00010' => '�ϳ����ù�',
	'00011' => '�����ù�',
	'00012' => '�����ù�',
	'00013' => 'CJ-GLS',
	'00014' => 'KGB�ù�',
	'00017' => '�������',
	'00019' => '�̳������ù�',
	'00021' => '����ù�',
	'00022' => '�Ͼ������',
	'00023' => 'ACI',
	'00025' => 'WIZWA',
	'00026' => '�浿�ù�',
	'00027' => 'õ���ù�',
	'00099' => '��Ÿ'
	);

// üũ�ƿ�
$integrate_cfg['dlv_company']['checkout'] = array(
	'CJGLS' => 'CJ�������',
	'KOREXG' => 'CJ�������(�����ù�)',
	'DHLDE' => 'DHL(����)',
	'KGB' => '�����ù�',
	'DONGBU' => 'KG������',
	'EPOST' => '��ü���ù�',
	'REGISTPOST' => '������',
	'HANJIN' => '�����ù�',
	'HYUNDAI' => '�����ù�',
	'KGBLS' => 'KGB �ù�',
	'INNOGIS' => 'GTX������',
	'DAESIN' => '����ù�',
	'ILYANG' => '�Ͼ������',
	'KDEXP' => '�浿�ù�',
	'CHUNIL' => 'õ���ù�',
	'DHL' => 'DHL',
	'FEDEX' => 'FEDEX',
	'GSMNTON' => 'GSMNTON',
	'WARPEX' => 'WarpEx',
	'WIZWA' => 'WIZWA',
	'EMS' => 'EMS',
	'ACIEXPRESS' => 'ACI',
	'EZUSA' => 'EZUSA',
	'PANTOS' => '�������佺',
	'SWGEXP' => '�����۷ι�',
	'TNT' => 'TNT',
	'UPS' => 'UPS',
	'CVSNET' => '�������ù�',
	'HDEXP' => '�յ��ù�',
	'CH1' => '��Ÿ �ù�',
);

// ipay
$integrate_cfg['dlv_company']['ipay'] = array(
	'korex' => '��������ù�',
	'hyundai' => '�����ù�',
	'epost' => '��ü���ù�',
	'dongbu' => '�����ͽ��������ù�',
	'ajutb' => '�����ο����ù�',
	'cjgls' => 'CJ GLS�ù�',
	'hth' => 'CJ GLS�ù�',
	'kgb' => '�����ù�',
	'yellow' => '���ο�ĸ',
	'hanjin' => '�����ù�',
	'kgbls' => 'KGB�ù�',
	'hanaro' => '�ϳ��η�����',
	'sagawa' => 'SC������(�簡��)',
	'ktlogistics' => 'KT������',
	'sedex' => '�����ù�',
	'nedex' => '�����ù�',
	'innogis' => '�̳������ù�',
	'gmgls' => '�¸���ù�',
	'daesin' => '����ù�',
	'ilyang' => '�Ͼ������',
	'kyungdong' => '�浿�ù�',
	'chonil' => 'õ���ù�',
	'gtx' => 'GTX�ù�',
	'etc' => '��Ÿ',
);


// �ù�� ��Ī ��
// Ű�� �̳��� �ù�� idx
$integrate_cfg['dlv_company']['map'] = array(
	'checkout' => array(
		'KGBLS' => 1,
		'KOREX' => 4,
		'KGB' => 5,
		'YELLOW' => 8,
		'EPOST' => 9,
		'REGISTPOST' => 18,
		'HANJIN' => 12,
		'HYUNDAI' => 13,
		'CJGLS' => 15,
		'SAGAWA' => 17,
		'DONGBU' => 21,
		'KDEXP' => 39,
		'HANARO' => '20',
		'INNOGIS' => '32',
		'DAESIN' => '33',
		'ILYANG' => '22',
		'CHUNIL' => '19',
	),
	'ipay' => array(
		'kgbls' =>  1,
		'korex' => 4,
		'kgb'  => 5,
		'yellow' =>  8,
		'epost' => 9,
		'hanjin' => 12,
		'hyundai' => 13,
		'cjgls'  =>15,
		'sagawa' => 17,
		'dongbu' => 21,
		'kyungdong' => 39,
	),
	'shople' => array(
		'00014' => 1,
		'00017' => 4,
		'00002' => 5,
		'00006' => 8,
		'00007' => 9,
		'00011' => 12,
		'00012' => 13,
		'00013' => 15,
		'00003' => 17,
		'00001' => 21,
		'00026' => 39,
	)
);

// Ŭ���� ���� �ڵ�
$integrate_cfg['claim_code'] = array(
	'enamoo' => codeitem("cancel"),
	'shople' => array(
		'06' => '��� ���� ����',
		'07' => '��ǰ/���� ���� �߸� �Է�',
		'08' => '��ǰ ǰ��(��ü�ɼ�)',
		'09' => '�ɼ� ǰ��(�ش�ɼ�)',
		'10' => '������',
		'99' => '��Ÿ',
		'101' => '������',
		'102' => '����ҷ�',
		'103' => '���� �� ��ǰ�Ҹ���',
		'104' => '��ǰ����',
		'105' => '��ǰ��������',
		'106' => '��ǰ�ļ�',
		'107' => '����ź�',
		'108' => '�����',
		'109' => '�ݼ�',
		'110' => '������, ���� ���� �߸� ������',
		'111' => '��۵� ��ǰ�� �ļ�/����/���� �ҷ�',
		'112' => '��ǰ�� �����ϰ� ���� ����',
		'113' => '��Ÿ',
		'119' => '������ ����Ȯ���� ���',
		'201' => '������',
		'202' => '����ҷ�',
		'203' => '���� �� ��ǰ�Ҹ���',
		'204' => '��ǰ����',
		'205' => '��ǰ�ļ�',
		'206' => '������ �Ǵ� ���� ���� �߸� ������',
		'207' => '��۵� ��ǰ�� �ļ�/����/���� �ҷ�',
		'208' => '�ٸ� ��ǰ�� �߸� ��۵�',
		'209' => 'ǰ�� ���� ������ �Ǹ��� ���� �� ��ȯ',
		'210' => '��ǰ�� ��ǰ�� ������ Ʋ��',
		'211' => '��Ÿ',
		'301' => '��۴���',
		'302' => '��ǰ�н�',
		'303' => '����ź�',
	),
	'ipay' => array(
		'LowerThanWishPrice' => '���������� ����ǸŰ��ݿ� ��ġ�� ����',
		'ManufacturingDefect' => '��ǰ�� ���ڰ� ���ܼ� �ǸźҰ�',
		'RunOutOfStock' => '������(ǰ��)',
		'SellToOtherDitstributionChannel' => '�ٸ� ��η� �Ǹ��ϰ��� ��',
		'SoldToOtherBuyer' => '�������� �Ա��������� ���Ͽ� �ٸ� �����ڿ��� �Ǹ�',
		'UnreliableBuyer' => '�峭/���� ������ ������ �ǸŰź�',
		'OtherReason' => '��Ÿ ����',
	),
);

// ���� ���
$integrate_cfg['pay_method'] = $r_settlekind;
$integrate_cfg['pay_method']['o'] = '�ǽð�������ü';
$integrate_cfg['pay_method']['NAVER_CASH'] = '���̹� ĳ��';


// ���հ˻�
$integrate_cfg['skey'] = array(
	array(
		'field'=>'o.ordno',
		'condition'=>'equal',
		'pattern'=>'/^[0-9]+$/',
	),
	array(
		'field'=>'o.ord_name',
		'condition'=>'like',
		'pattern'=>'/.{4,}/',
	),
	array(
		'field'=>'o.rcv_name',
		'condition'=>'like',
		'pattern'=>'/.{4,}/',
	),
	array(
		'field'=>'o.pay_bank_name',
		'condition'=>'like',
		'pattern'=>'/.{4,}/',
	),
	array(
		'field'=>'m.m_id',
		'condition'=>'equal',
		'pattern'=>'/^[\xa1-\xfea-zA-Z0-9_-]{4,20}$/',
	),
	array(
		'field'=>'o.ord_phone',
		'condition'=>'like',
		'pattern'=>'/[0-9]{4,}/',
	),
	array(
		'field'=>'o.ord_mobile',
		'condition'=>'like',
		'pattern'=>'/[0-9]{4,}/',
	),
	array(
		'field'=>'o.rcv_phone',
		'condition'=>'like',
		'pattern'=>'/[0-9]{4,}/',
	),
	array(
		'field'=>'o.rcv_mobile',
		'condition'=>'like',
		'pattern'=>'/[0-9]{4,}/',
	),
	array(
		'field'=>'o.rcv_address',
		'condition'=>'like',
		'pattern'=>'/.{4,}/',
	),
	array(
		'field'=>'o.dlv_no',
		'condition'=>'like',
		'pattern'=>'/^[0-9]+$/',
	)
);
?>