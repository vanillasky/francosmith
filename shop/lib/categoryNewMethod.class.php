<?php
/**
 * 상품분류 연결방식 Class
 */
class categoryNewMethod
{
	private		$_db;
	private		$_limitCount		= 200;			// 한번에 불러올 쿼리갯수
	private		$_categoryLength	= 3;			// 카테고리 노드당 길이
	private		$_categoryDepth		= 4;			// 카테고리 차수

	/**
	 * 생성자
	 */
	public function __construct()
	{
		// 디비 연결
		if (!is_object($this->_db)) {
			$this->_db 	= Core::loader('db');
		}
	}

	/**
	 * [임시] link_chk 필드의 여부를 체크 및 link_chk 필드 추가
	 */
	public function setLinkCheckFieldAdd()
	{
		// link_chk 필드의 여부를 체크 및 link_chk 필드 추가
		$strSQL		= "DESC ".GD_GOODS_LINK;
		$result		= $this->_db->query($strSQL);
		$fieldChk	= false;
		while (is_resource($result) && $data = $this->_db->fetch($result,1))
		{
			if ($data['Field'] == 'link_chk') {
				$fieldChk	= true;
			}
		}
		if ($fieldChk === false) {
			$strSQL	= "ALTER TABLE ".GD_GOODS_LINK." ADD link_chk ENUM( 'y', 'n' ) NOT NULL DEFAULT 'n'";
			$this->_db->query($strSQL);
		}
	}

	/**
	 * 상품분류 연결방식 전환 처리
	 * @param1  string $page 초기페이지 재설정
	 * @param2  string $completCnt 완료된 쿼리실행 갯수 재설정
	 * @param3  string $totnum 총 상품 개수
	 * @return  array $result query
	 */
	public function setCategoryLinkAdd($page = 1, $completCnt = 0, $totnum)
	{

		if(!$page) $page = 1;				// 초기페이지 재설정
		if(!$completCnt) $completCnt = 0;	// 완료된 쿼리실행 갯수 재설정

		if ($totnum == 0) {
			$getData['limitStart']	= 0;
			$getData['dataCnt']		= 0;
			$getData['completCnt']	= 0;

			return $getData;
		}

		$limitStart	= ($page - 1) * $this->_limitCount;		// 시작번호
		$totpage	= ceil($totnum / $this->_limitCount);
		$strSQL		= "SELECT goodsno FROM ".GD_GOODS." ORDER BY goodsno ASC LIMIT ".$limitStart.", ".$this->_limitCount;
		$result		= $this->_db->query($strSQL);
		$updateArr	= array();	// 업데이트 할 카테고리
		$excludeArr	= array();	// 중복 제거 할 카테고리
		$field		= array('sort', 'sort1', 'sort2', 'sort3', 'sort4', 'hidden', 'hidden_mobile');	// gd_goods_link 필드 명 (시즌4용)
		$dataCnt	= 0;
		$goodsnoArr	= array();

		while (is_resource($result) && $data = $this->_db->fetch($result,1))
		{
			$goodsnoArr[]	= $data['goodsno'];
			$dataCnt++;
		}

		if (count($goodsnoArr) > 0 ){

			$whereStr	= "'".implode("','", $goodsnoArr)."'";

			// 해당 goodsno 의 카테고리 정보
			$strSQL		= "SELECT goodsno, category, ".implode(', ', $field)." FROM ".GD_GOODS_LINK." WHERE goodsno IN (".$whereStr.") ORDER BY goodsno ASC, sno ASC";
			$result2	= $this->_db->query($strSQL);

			while (is_resource($result2) && $getCode = $this->_db->fetch($result2,1))
			{
				$length	= strlen($getCode['category']);		// 카테고리 코드의 길이

				// 중복 제거할 카테고리
				$excludeArr[$getCode['goodsno']][] = $getCode['category'];

				// 카테고리 차수에 의한 루프
				for ($i = 1; $i <= ($length / $this->_categoryLength); $i++)
				{
					$getCate	= substr($getCode['category'], 0, ($i * $this->_categoryLength));			// 루프에 따른 해당 카테고리 코드

					// 현재 카테고리 코드와 루프에 따른 해당 카테고리 코드를 비교를 해서 같지 않으면 상위카테고리 저장
					if ($getCate != $getCode['category']) {
						$updateArr[$getCode['goodsno']][$getCate]['category']	= $getCate;			// tmp 배열에 상품번호 -> 카테고리 -> category 정보 저장

						foreach ($field as $key => $val) {
							$updateArr[$getCode['goodsno']][$getCate][$val]	= $getCode[$val];	// tmp 배열에 상품번호 -> 카테고리 -> 각 정보 저장
						}
					}
				}
			}
		}

		// 카테고리 정보 insert
		foreach ($updateArr as $goodsno => $val)
		{
			foreach ($val as $cKey => $cVal)
			{
				// 중복 카테고리는 제외
				if (in_array($cVal['category'], $excludeArr[$goodsno])) {
					continue;
				} else {
					$querycnt = "SELECT count(1) FROM ".GD_GOODS_LINK."  WHERE goodsno='".$goodsno."' AND category='".$cVal['category']."'";
					list($chkCate) = $this->_db->fetch($querycnt);
					if ($chkCate == 0) {
						$strSQL	= "INSERT INTO ".GD_GOODS_LINK." (goodsno, link_chk, category, ".implode(', ', $field).") VALUES ('".$goodsno."','y','".implode('\', \'', $cVal)."')";
						$this->_db->query($strSQL);
						$completCnt++;
					}
				}
			}
		}

		$getData['limitStart']	= $limitStart;
		$getData['dataCnt']		= $dataCnt;
		$getData['completCnt']	= $completCnt;

		return $getData;
	}

	/**
	 * 중복 카테고리를 제거함
	 * @return  intger $removeCnt 제거 수량
	 */
	public function duplicationRemove()
	{
		// 중복 카테고리를 체크함
		//SELECT sno, count( goodsno ) , goodsno , category FROM ".GD_GOODS_LINK." GROUP BY goodsno , category HAVING count( goodsno ) >1
		$strSQL	= " SELECT c.sno FROM ".GD_GOODS_LINK." c ,
			(SELECT goodsno, category, min(sno) as m_sno FROM ".GD_GOODS_LINK." GROUP BY goodsno, category ) X
			WHERE c.goodsno = X.goodsno AND c.category = X.category AND c.sno > m_sno";
		$result		= $this->_db->query($strSQL);
		$removeCnt	= $this->_db->count_($result);

		// 처리 개수에 의해
		if ($removeCnt > 0) {
			// 삭제 처리중
			while (is_resource($result) && $data = $this->_db->fetch($result,1))
			{
				$strSQL	= "DELETE FROM ".GD_GOODS_LINK." WHERE sno = '".$data['sno']."'";
				$this->_db->query($strSQL);
			}
		}

		return $removeCnt;
	}

	/**
	 * 해당 카테고리에 해당되는 상품의 링크값을 추출함
	 * @param1  string $goodsno 상품번호
	 * @param2  string $category 카테고리코드
	 * @return  array $result 카테고리코드
	 */
	private function _getCategory($goodsno, $category)
	{
		// 체크
		if (empty($goodsno) || empty($category)) {
			return false;
		}

		// 해당 카테고리에 해당 되는 상품의 링크값을 추출함
		$getData	= array();
		$getResult	= $this->_db->query('SELECT category FROM '.GD_GOODS_LINK.' WHERE category LIKE "'.substr($category, 0, 3).'%" AND goodsno='.$goodsno);
		while ($getLink	= $this->_db->fetch($getResult, true)) {
			$getData[]	= $getLink['category'];
		}

		return $getData;
	}

	/**
	 * 두개의 카테고리 배열에서 중복되지 않은 카테고리만 추출
	 * @param1  array $category 카테고리1
	 * @param2  array $category 카테고리2
	 * @return  array $result 중복되지 않은 카테고리
	 */
	private function _getSingleCategory($category1, $category2)
	{
		// 체크
		if (empty($category1) || empty($category2)) {
			return false;
		}

		// 추출된 카테고리를 기준으로 중복되지 않은 카테고리만 추출
		$getData	= array();
		foreach ($category1 as $val) {
			if (in_array($val, $category2) === false) {
				$getData[]	= $val;
			}
		}

		return $getData;
	}

	/**
	 * 해당 상품의 해당 카테고리 링크값을 추출 (adm_goods_manage_link.php 에서의 link에 사용)
	 * @param1  string $goodsno 상품번호
	 * @param2  string $category 카테고리코드
	 * @return  array $result 해당 상품에 추가할 카테고리 코드
	 */
	public function getHighCategoryLink($goodsno, $category)
	{
		// 체크
		if (empty($goodsno) || empty($category)) {
			return false;
		}

		// 상품의 카테고리를 추출함
		$goodsCategory	= $this->_getCategory($goodsno, $category);

		// 해당 카테고리의 상위 카테고리 코드값과 비교후 없는 카테고리 코드 추출
		$getData		= array();
		foreach (getHighCategoryCode($category) as $val) {
			if(in_array($val, $goodsCategory) === false){
				$getData[]	= $val;
			}
		}

		return $getData;
	}

	/**
	 * 해당 상품의 제거할 카테고리 링크값을 추출 (adm_goods_manage_link.php 에서의 unlink에 사용)
	 * @param1  string $goodsno 상품번호
	 * @param2  string $category 카테고리코드
	 * @return  array $result 해당 상품서 제거할 카테고리 코드
	 */
	public function getHighCategoryUnlink($goodsno, $category)
	{
		// 체크
		if (empty($goodsno) || empty($category)) {
			return false;
		}

		// 상품분류 연결방식 전환 여부에 따른 처리
		if (_CATEGORY_NEW_METHOD_ === false) {
			$getData[]	= $category;

			return $getData;
			exit();
		}

		// 상품의 카테고리를 추출함
		$goodsCategory		= $this->_getCategory($goodsno, $category);

		// 카테고리의 길이
		$categoryLength		= strlen($category);

		// 카테고리의 차수
		$checkCategoryNum	= $categoryLength / $this->_categoryLength;

		// 제거될 상위 카테고리를 배열에 저장
		$tmpData			= array();
		for ($i = 1; $i <= $checkCategoryNum; $i++) {
			$tmp			= substr($category, 0, ($i * $this->_categoryLength));
			if ($tmp != $category) {
				$tmpData[]	= $tmp;
			}
		}

		// 추출된 카테고리를 기준으로 제거될 하위 카테고리를 배열에 저장
		foreach ($goodsCategory as $val) {
			if($category == substr($val, 0, $categoryLength)){
				$tmpData[]	= $val;
			}
		}

		// 추출된 카테고리를 기준으로 중복되지 않은 카테고리만 추출
		$singleData	= $this->_getSingleCategory($goodsCategory, $tmpData);

		// 중복되지 않는 카테고리가 없는 경우
		if (empty($singleData) === true) {
			$getData	= $tmpData;
		}

		// 중복되지 않는 카테고리가 있는 경우
		else {
			// 제거될 카테고리의 상위 카테고리나 동급 카테고리 사용여부에 따라서 제거될 카테고리를 조정
			$duplicationData	= array();
			foreach ($singleData as $val) {
				$checkValNum	= strlen($val) / $this->_categoryLength;
				for ($i = 1; $i <= $checkValNum; $i++) {
					$checkVal	= substr($val, 0, ($i * $this->_categoryLength));
					if (in_array($checkVal, $tmpData)) {
						$duplicationData[]	= $checkVal;
						$duplicationData	= array_unique($duplicationData);
					}
				}
			}

			// 추출된 카테고리를 기준으로 제거될 하위 카테고리를 배열에 저장
			$getData	= $this->_getSingleCategory($tmpData, $duplicationData);
		}

		return $getData;
	}
}
?>