<?

include "../_header.php";
chkMemberPopupConfirm();

$goodsno = $_GET['goodsno'];

##상품의 옵션 목록 불러오기
##상품 재입고 알림을 사용하는지 확인
$query = "SELECT count(goodsno) FROM ".GD_GOODS." WHERE goodsno='".$goodsno."' AND usestock='o' AND use_stocked_noti='1'";
list($useStock) = $db->fetch($query);
if($useStock < 1){
	msg("상품 재입고 알림을 사용할 수 없는 상품입니다!");
	?><script type="text/javaascript">
		self.close();
	</script><?
}

##옵션 수를 가져오기
$query = "SELECT count(goodsno) FROM ".GD_GOODS_OPTION." WHERE goodsno='".$goodsno."' and go_is_deleted <> '1' and go_is_display = '1' ";
list($optCnt) = $db->fetch($query);

##재고가 없는 옵션을 가져오기
$query = "SELECT * FROM ".GD_GOODS_OPTION." WHERE goodsno='".$goodsno."' AND stock='0'  and go_is_deleted <> '1' and go_is_display = '1' ";
$optData = $db->query($query);
while($data = $db->fetch($optData)){
	$optList[] = $data;
}

##싱픔wkdqj기쟈억;
$query = "SELECT * FROM ".GD_GOODS." WHERE goodsno='".$goodsno."'";
$optData = $db->query($query);
$data = $db->fetch($optData);
$goodsnm = strcut($data['goodsnm'], 30);

##회원정보
$query = "SELECT name, mobile FROM ".GD_MEMBER." WHERE m_no = '".$sess['m_no']."'";
$memberData = $db->fetch($query);
$name = $memberData['name'];
$mobile = str_replace("-", "", $memberData['mobile']);

// 개인정보수집 및 이용에 대한 안내
$termsPolicyCollection4 = getTermsGuideContents('terms', 'termsPolicyCollection4');
$tpl->assign('termsPolicyCollection4', $termsPolicyCollection4);

### 템플릿 출력
$tpl->assign('goodsno',$goodsno);				//상품번호
$tpl->assign('goodsnm',$goodsnm);				//상품번호
$tpl->assign('optCnt',$optCnt);					//옵션 수
$tpl->assign('optList',$optList);				//옵션 목록
$tpl->assign('memberName',$name);	//회원이름
$tpl->assign('mobile',$mobile);					//회원 전화번호
$tpl->print_('tpl');
?>