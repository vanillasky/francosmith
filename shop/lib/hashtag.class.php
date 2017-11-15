<?php
/**
 * Copyright (c) 2016 GODO Co. Ltd
 * All right reserved.
 *
 * This software is the confidential and proprietary information of GODO Co., Ltd.
 * You shall not disclose such Confidential Information and shall use it only in accordance
 * with the terms of the license agreement  you entered into with GODO Co., Ltd
 *
 * Revision History
 * Author            Date              Description
 * ---------------   --------------    ------------------
 * workingby         2016.10.05        First Draft.
 */
set_time_limit(0);
ini_set("memory_limit", -1);
/**
 * HASH TAG
 *
 * @author hashtag.class.php workingby <bumyul2000@godo.co.kr>
 * @version 1.0
 * @date 2016-10-05
 */
class hashtag
{
	private $configFile;
	private $db;
	//상품 해시태그 최대 등록가능 개수
	private $goodsHashtagMaxCount = 10;

	function __construct()
	{
		global $db;

		$this->configFile = dirname(__FILE__).'/../conf/config.hashtag.php';
		$this->db = $db;
	}

	/**
	 * 설정 정보 저장
	 * @author workingby <bumyul2000@godo.co.kr>
	 * @param array $data
	 * @return boolean
	 * @date 2016-10-05
	 */
	public function saveConfig($data)
	{
		global $qfile;

		try {
			unset($data['mode']);
			$hashtagConfig = array();

			if(!is_object($qfile)){
				include dirname(__FILE__).'/../lib/qfile.class.php';
				$qfile = new qfile();
			}
			if(is_file($this->configFile)){
				include $this->configFile;
			}

			$hashtagConfig = array_merge((array)$hashtagConfig, (array)$data);

			$qfile->open($this->configFile);
			$qfile->write("<?php \n");
			$qfile->write("\$hashtagConfig = array( \n");
			foreach($hashtagConfig as $key => $value){
				$qfile->write("'".$key."' => '".$value."', \n");
			}
			$qfile->write(");?>");
			$qfile->close();

			@chmod($this->configFile, 0707);

			if(!is_file($this->configFile)){
				throw new Exception('FILE 저장에 실패하였습니다.');
			}

			return '';
		}
		catch(Exception $e){
			return $e->getMessage();
		}
	}

	/**
	 * 설정 정보 반환
	 * @author workingby <bumyul2000@godo.co.kr>
	 * @param void
	 * @return void
	 * @date 2016-10-05
	 */
	public function getConfig()
	{
		$hashtagConfig = array();
		if(is_file($this->configFile)){
			include $this->configFile;
		}

		return $hashtagConfig;
	}

	/**
	 * hashtag input list
	 * @author workingby <bumyul2000@godo.co.kr>
	 * @param string $searchText
	 * @return array $returnArray
	 * @date 2016-10-05
	 */
	public function getInputListHashtag($searchText)
	{
		$returnArray = array('result' => 'success');

		try {
			$query = "SELECT hashtag FROM ".GD_HASHTAG_STATISTICS." WHERE hashtag LIKE '".$searchText."%' ORDER BY hashtag LIMIT 10";
			$res = $this->db->query($query);
			if($res){
				while($row = $this->db->fetch($res, 1)){
					$returnArray['data'][] = $row['hashtag'];
				}
			}

			return $returnArray;
		}
		catch(Exception $e){
			$returnArray['result'] = 'fail';
			$returnArray['data'] = $e->getMessage();

			return $returnArray;
		}
	}

	public function getGoodsListHashtag($goodsno)
	{
		$returnArray = array('result' => 'success');

		try {
			$query = "
				SELECT
					a.hashtag, b.no
				FROM
					".GD_HASHTAG." AS a
				LEFT JOIN
					".GD_HASHTAG_STATISTICS." AS b
				ON
					a.hashtag=b.hashtag
				WHERE
					a.goodsno=".$goodsno."
				ORDER BY
					a.hashtagSort ASC,
					a.no DESC
				LIMIT 10
			";
			$res = $this->db->query($query);
			if(!$res){
				throw new exception("HASHTAG 조회 실패.");
			}
			while($row = $this->db->fetch($res, 1)){
				$returnArray['data'][] = self::setHashTagLayout($row, array('style'=>'cursor: pointer;'));
			}

			return $returnArray;
		}
		catch(Exception $e){
			$returnArray['result'] = 'fail';
			$returnArray['data'] = $e->getMessage();

			return $returnArray;
		}
	}

	/**
	 * hashtag all list
	 * @author workingby <bumyul2000@godo.co.kr>
	 * @param array $param
	 * @return void
	 * @date 2016-10-05
	 */
	public function getAllListHashtag($param)
	{
		$returnArray = array('result' => 'success');

		try {
			$query = self::getAllListHashtagQuery($param);
			$res = $this->db->query($query);
			if($res){
				while($row = $this->db->fetch($res, 1)){
					$returnArray['data'][] = self::setHashTagLayout($row, array('style'=>'cursor: pointer;'));
				}
			}

			return $returnArray;
		}
		catch(Exception $e){
			$returnArray['result'] = 'fail';
			$returnArray['data'] = $e->getMessage();

			return $returnArray;
		}
	}

	private function getAllListHashtagQuery($param)
	{
		include '../lib/page.class.php';

		$limit = $where = '';
		if($param['searchText']){
			$where = " WHERE hashtag like '%".$param['searchText']."%' ";
		}

		$countQuery = "SELECT COUNT(*) FROM ".GD_HASHTAG_STATISTICS.$where;
		list($totalCount) = $this->db->fetch($countQuery);
		$pg = new Page($param['page'], $param['pageNum']);
		$pg->recode['total'] = $totalCount;
		$limited = ($pg->recode['start']+$pg->page['num']<$pg->recode['total']) ? $pg->page['num'] : $pg->recode['total'] - $pg->recode['start'];
		$pg->idx = $pg->recode['total'] - $pg->recode['start'];

		$limit = " LIMIT ".$pg->recode['start'].",".$limited;

		$query = "SELECT hashtag, cnt, no FROM ".GD_HASHTAG_STATISTICS.$where." ORDER BY regDate DESC".$limit;

		return $query;
	}


	/**
	 * hashtag layout form
	 * @author workingby <bumyul2000@godo.co.kr>
	 * @param array $row
	 * @param array $hashtagLayoutOption
	 * @return string $hashtag
	 * @date 2016-10-05
	 */
	private function setHashTagLayout($row, $hashtagOption=array())
	{
		global $_SERVER, $cfg;

		$option = array();
		$opt_inputInclude = $opt_pointer = $opt_closeBtn = $opt_link = '';

		$defaultOption = array(
			'inputInclude' => true, //input hidden 정보 포함여부
			'closeBtn' => true, //close button 포함 여부
			'style' => '', //추가 style
			'link' => false, //링크추가 여부
			'mobilePath' => '', //모바일경로 추가 여부
		);
		$option = array_merge((array)$defaultOption, (array)$hashtagOption);

		//input hidden 정보 포함여부
		if($option['inputInclude'] === true){
			//no - GD_HASHTAG_STATISTICS의 no
			$opt_inputInclude = "
				<input type='hidden' name='hashtagNo[]' value='".$row['no']."' />
				<input type='hidden' name='hashtagName[]' value='".$row['hashtag']."' />
			";
		}
		//close button 포함 여부
		if($option['closeBtn'] === true){
			$opt_closeBtn = " &nbsp;<span>X</span>";
		}
		//링크여부
		if($option['link'] === true){
			if($cfg['rootDir']){
				$opt_path = $cfg['rootDir'].'/';
			}
			else {
				$opt_path = '../';
			}

			//모바일 경로
			if($option['mobilePath']){
				$opt_path = $option['mobilePath'];
			}
			$opt_link = "onclick=\"javascript:location.href='".$opt_path."goods/goods_hashtag_list.php?hashtag=".urlencode($row['hashtag'])."';\"";
		}

		$hashtag = "
		<div style='padding: 3px; font:11px Dotum; color:#2188f1; border: 1px #898989 solid; min-width: 20px; float: left; margin: 0 3px 3px 0; background-color: white;".$option['style']."' class='hashtagSelector' data-name='".$row['hashtag']."' data-goodsCount='".$row['cnt']."' ".$opt_link.">
			#".$row['hashtag'].$opt_closeBtn."
			".$opt_inputInclude."
		</div>";

		return $hashtag;
	}

	/**
	 * check hashtag
	 * @author workingby <bumyul2000@godo.co.kr>
	 * @param string $hashtag
	 * @return string $returnArray
	 * @date 2016-10-05
	 */
	public function addHashtagLayout($hashtag)
	{
		$returnArray = array('result' => 'success');

		try {
			$hashtag = self::setHashtag($hashtag);

			$returnArray['data'] = self::setHashTagLayout(array('hashtag' => $hashtag), array('style' => 'cursor: pointer;'));

			return $returnArray;
		}
		catch(Exception $e){
			$returnArray['result'] = 'fail';
			$returnArray['data'] = $e->getMessage();

			return $returnArray;
		}
	}

	/**
	 * check hashtag
	 * true - 중복미존재, false - 중복존재
	 * @author workingby <bumyul2000@godo.co.kr>
	 * @param string $hashtag
	 * @return boolean
	 * @date 2016-10-05
	 */
	public function checkHashtag($hashtag)
	{
		list($cnt) = $this->db->fetch("SELECT COUNT(*) as cnt FROM ".GD_HASHTAG_STATISTICS." WHERE hashtag='".$hashtag."' LIMIT 1");
		if((int)$cnt > 0){
			return false;
		}
		return true;
	}

	/**
	 * 실시간 해시태그 등록
	 * @author workingby <bumyul2000@godo.co.kr>
	 * @param string $hashtag
	 * @return array $returnArray
	 * @date 2016-10-05
	 */
	public function addHashtagLive($hashtag)
	{
		$returnArray = array('result' => 'success');

		try {
			if(!trim($hashtag)){
				throw new Exception('해시태그명을 입력해 주세요.');
			}

			$hashtag = self::setHashtag($hashtag);

			//check hashtag
			$checkResult = true;
			$checkResult = self::checkHashtag($hashtag);
			if($checkResult === false){
				throw new Exception('이미 등록된 해시태그 입니다.');
			}

			//hashtag table insert
			$errorMessage = '';
			$errorMessage = self::save_hashtagTable($hashtag);
			if($errorMessage !== ''){
				throw new Exception($errorMessage);
			}

			//statistics table insert
			$errorMessage = '';
			$errorMessage = self::save_statisticsTable($hashtag, false);
			if($errorMessage !== ''){
				throw new Exception($errorMessage);
			}

			$hashtagLayoutOption = array(
				'style' => 'cursor: pointer;', //style 추가
			);
			$returnArray['data'] = self::setHashTagLayout(array('cnt' => 0, 'hashtag' => $hashtag), $hashtagLayoutOption);

			return $returnArray;
		}
		catch(Exception $e){
			$returnArray['result'] = 'fail';
			$returnArray['data'] = $e->getMessage();

			return $returnArray;
		}
	}

	public function deleteHashtagLive($hashtag)
	{
		$returnArray = array('result' => 'success');

		try {
			//hashtag table delete
			$errorMessage = '';
			$errorMessage = self::delete_hashtagTable(array('hashtag'=>$hashtag));
			if($errorMessage !== ''){
				throw new Exception($errorMessage);
			}

			//statistics table delete
			$errorMessage = '';
			$errorMessage = self::delete_statisticsTable($hashtag);
			if($errorMessage !== ''){
				throw new Exception($errorMessage);
			}

			return $returnArray;
		}
		catch(Exception $e){
			$returnArray['result'] = 'fail';
			$returnArray['data'] = $e->getMessage();

			return $returnArray;
		}
	}

	/**
	 * 실시간 해시태그 삭제 - 빠른 해시태그 수정
	 * @author workingby <bumyul2000@godo.co.kr>
	 * @param string $hashtag
	 * @param integer $goodsno
	 * @return string
	 * @date 2016-10-05
	 */
	public function deleteManageHashtagLive($hashtag, $goodsno)
	{
		$returnArray = array('result' => 'success');

		try {
			//hashtag table delete
			$errorMessage = '';
			$errorMessage = self::delete_hashtagTable(array('hashtag'=>$hashtag, 'goodsno'=>$goodsno));
			if($errorMessage !== ''){
				throw new Exception($errorMessage);
			}
			$res = self::update_count_statisticsTable('minus', $hashtag);
			if(!$res){
				throw new Exception('HASHTAG_STATISTICS DB 제거를 실패하였습니다.');
			}

			return $returnArray;
		}
		catch(Exception $e){
			$returnArray['result'] = 'fail';
			$returnArray['data'] = $e->getMessage();

			return $returnArray;
		}
	}

	/**
	 * insert row - gd_hashtag
	 * @author workingby <bumyul2000@godo.co.kr>
	 * @param string $hashtag
	 * @param integer $goodsno
	 * @param integer $hashtagSort
	 * @return string
	 * @date 2016-10-05
	 */
	private function save_hashtagTable($hashtag, $goodsno=0, $hashtagSort=0)
	{
		try {
			$query = "INSERT INTO ".GD_HASHTAG." (hashtag, goodsno, hashtagSort) VALUES ('".$hashtag."', ".$goodsno.", ".$hashtagSort.")";
			$result = $this->db->query($query);
			if(!$result){
				throw new Exception('HASHTAG 등록 실패');
			}

			return '';
		}
		catch(Exception $e){
			return $e->getMessage();
		}
	}

	/**
	 * insert row - gd_hashtag_statistics
	 * @author workingby <bumyul2000@godo.co.kr>
	 * @param string $hashtag
	 * @param boolean $goodsConnect
	 * @return string
	 * @date 2016-10-05
	 */
	private function save_statisticsTable($hashtag, $goodsConnect=false)
	{
		try {
			$firstCnt = 0;
			$duplicateUpdate = '';
			if($goodsConnect === true){
				$firstCnt++;
				$duplicateUpdate = " ON DUPLICATE KEY UPDATE cnt=cnt+1";
			}
			$query = "INSERT INTO ".GD_HASHTAG_STATISTICS." (hashtag, cnt, regDate, totalSort) VALUES ('".$hashtag."', ".$firstCnt.", now(), 0)".$duplicateUpdate;
			$result = $this->db->query($query);
			if(!$result){
				throw new Exception('HASHTAG STATISTICS 등록 실패');
			}

			return '';
		}
		catch(Exception $e){
			return $e->getMessage();
		}
	}

	/**
	 * statistics table count update
	 * @author workingby <bumyul2000@godo.co.kr>
	 * @param string $type
	 * @param string $hashtag
	 * @return object
	 * @date 2016-10-05
	 */
	private function update_count_statisticsTable($type, $hashtag)
	{
		switch($type){
			case 'plus' :
				$query = "UPDATE ".GD_HASHTAG_STATISTICS." SET cnt=cnt+1 WHERE hashtag='".$hashtag."'";
			break;

			case 'minus' :
				$query = "UPDATE ".GD_HASHTAG_STATISTICS." SET cnt=cnt-1 WHERE hashtag='".$hashtag."'";
			break;

			case 'zero' :
				$query = "UPDATE ".GD_HASHTAG_STATISTICS." SET cnt=0 WHERE hashtag='".$hashtag."'";
			break;
		}
		if($query) $res = $this->db->query($query);

		return $res;
	}

	/**
	 * delete row - gd_hashtag
	 * optioin - hashtag: 해시태그, goodsno: 상품번호, lastDelete: 정렬순서상 맨 뒤에있는 건 삭제
	 * @author workingby <bumyul2000@godo.co.kr>
	 * @param array $whereOption
	 * @return string
	 * @date 2016-10-05
	 */
	private function delete_hashtagTable($whereOption)
	{
		try {
			$deleteCheck = false;
			$whereArray = array();
			$where = '';
			if($whereOption['hashtag']){
				$deleteCheck = true;
				$whereArray[] = "hashtag='".$whereOption['hashtag']."'";
			}
			if((int)$whereOption['goodsno'] > 0){
				$deleteCheck = true;
				$whereArray[] = "goodsno=".$whereOption['goodsno'];
			}
			if($whereOption['lastDelete'] === 'y'){
				$deleteCheck = true;
				$where = " ORDER BY hashtagSort DESC LIMIT 1";
			}

			if(count($whereArray) > 0){
				$where = " WHERE ".implode(" AND ", $whereArray).$where;
			}

			if($deleteCheck === true){
				$query = "DELETE FROM ". GD_HASHTAG . $where;
				$result = $this->db->query($query);
				if(!$result){
					throw new Exception('HASHTAG 삭제 실패');
				}
			}

			return '';
		}
		catch(Exception $e){
			return $e->getMessage();
		}
	}

	/**
	 * delete row - gd_hashtag_statistics
	 * @author workingby <bumyul2000@godo.co.kr>
	 * @param string $hashtag
	 * @return string
	 * @date 2016-10-05
	 */
	private function delete_statisticsTable($hashtag)
	{
		try {
			$query = "DELETE FROM ".GD_HASHTAG_STATISTICS." WHERE hashtag='".$hashtag."'";
			$result = $this->db->query($query);
			if(!$result){
				throw new Exception('HASHTAG STATISTICS 삭제 실패');
			}

			return '';
		}
		catch(Exception $e){
			return $e->getMessage();
		}
	}

	/**
	 * save hashtag goods [관리모드 - 상품 저장, 수정시]
	 * @author workingby <bumyul2000@godo.co.kr>
	 * @param array $postData
	 * @param string $goodsno
	 * @return void
	 * @date 2016-10-05
	 */
	public function saveGoodsList($postData, $goodsno='')
	{
		//update, insert
		foreach($postData['hashtagNo'] as $key => $value){
			$sortIndex = $key+1;

			$hashtagName = '';
			$hashtagName = self::setHashtag($postData['hashtagName'][$key]);

			//gd_hashtag_statistics 기준
			//상품에 저장되어 있던 해시태그
			if($value){
				$query = "UPDATE ".GD_HASHTAG." SET hashtagSort=".$sortIndex." WHERE goodsno=".$goodsno." AND hashtag='".$hashtagName."'";
				$this->db->query($query);

			}
			//새로운 해시태그 생성
			else {
				$query = "INSERT INTO ".GD_HASHTAG." (hashtag, goodsno, hashtagSort) VALUES ('".$hashtagName."', ".$goodsno.", ".$sortIndex.")";
				$query_ststistics = "INSERT INTO ".GD_HASHTAG_STATISTICS." ( hashtag, cnt, regDate, totalSort ) VALUES ('".$hashtagName."', 1, now(), 0) ON DUPLICATE KEY UPDATE cnt=cnt+1";
				$res = $this->db->query($query);
				if($res){
					$this->db->query($query_ststistics);
				}
			}
		}

		//delete
		foreach($postData['hashtagDelName'] as $key => $value){
			$query = "DELETE FROM ".GD_HASHTAG." WHERE goodsno=".$goodsno." AND hashtag='".$value."'";
			$this->db->query($query);
			self::update_count_statisticsTable('minus', $value);
		}
	}

	/**
	 * 해시태그 사용자 설정 정렬
	 * @author workingby <bumyul2000@godo.co.kr>
	 * @param array $hashtagNoArray
	 * @return array $returnArray
	 * @date 2016-10-05
	 */
	public function saveHashtagDisplay($hashtagNoArray)
	{
		$returnArray = array('result' => 'success');

		try {
			$_dataArray = array();
			parse_str($hashtagNoArray, $dataArray);
			
			$res = $this->db->query("UPDATE ".GD_HASHTAG_STATISTICS." SET totalSort = 0 WHERE totalSort != 0");
			if(!$res){
				throw new Exception("초기화를 실패하였습니다.");
			}

			if(count($dataArray['hashtagNo']) > 0){
				foreach($dataArray['hashtagNo'] as $key => $value){
					$res = $this->db->query("UPDATE ".GD_HASHTAG_STATISTICS." SET totalSort = ".($key+1)." WHERE no = ".$value);
					if(!$res){
						throw new Exception("저장을 실패하였습니다.");
					}
				}
			}

			return $returnArray;
		}
		catch(Exception $e){
			$returnArray['result'] = 'fail';
			$returnArray['data'] = $e->getMessage();

			return $returnArray;
		}
	}

	/**
	 * setting hashtag name
	 * @author workingby <bumyul2000@godo.co.kr>
	 * @param string $hashtag
	 * @return string $hashtag
	 * @date 2016-10-05
	 */
	public function setHashtag($hashtag)
	{
		$hashtag = preg_replace ("/[#\&\+\-%@=\/\\\:;,\.'\"\^`~|\!\?\*$#<>()\[\]\{\}]/i", "", trim($hashtag));
		$hashtag = preg_replace("/\s/", "_", $hashtag);
		$hashtag = mb_substr($hashtag, 0, 20, 'EUC-KR');

		return $hashtag;
	}

	/**
	 * get iframe code
	 * @author workingby <bumyul2000@godo.co.kr>
	 * @param array $getData
	 * @return string $iframeHtml
	 * @date 2016-10-05
	 */
	public function getIframeWidgetCode($getData)
	{
		$iframeHtml = '';
		$iframeID = 'hashtagWidgetIframe_' . time();
		$iframeHtml = '<iframe name="hashtagWidgetIframe" id="'.$iframeID.'" src="'. self::setIframeWidgetUri($iframeID, $getData) . '" allowTransparency="true" frameborder="0" scrolling="no" style="border:none; overflow:hidden;width:'.$getData['hashtagIframeWidth'].'px;" width="'.$getData['hashtagIframeWidth'].'"></iframe>';

		return $iframeHtml;
	}

	/**
	 * set iframe uri
	 * @author workingby <bumyul2000@godo.co.kr>
	 * @param string $iframeID
	 * @param array $getData
	 * @return string $returnUri
	 * @date 2016-10-05
	 */
	private function setIframeWidgetUri($iframeID, $getData)
	{
		global $cfg;

		$returnUri = $cfg['rootDir'] . '/proc/hashtag_widget_list.php';
		$setUriArray = array(
			'hashtag' => $getData['hashtag'],
			'hashtagWidth' => $getData['hashtagWidth'],
			'hashtagHeight' => $getData['hashtagHeight'],
			'hashtagIframeWidth' => $getData['hashtagIframeWidth'],
			'hashtagImageWidth' => $getData['hashtagImageWidth'],
			'hashtagWidgetID' => $iframeID,
		);

		$returnUri = $returnUri . '?' . base64_encode(serialize($setUriArray));

		return $returnUri;
	}

	/**
	 * get iframe uri
	 * @author workingby <bumyul2000@godo.co.kr>
	 * @param string $queryString
	 * @return array $returnArray
	 * @date 2016-10-05
	 */
	public function getIframeWidgetUri($queryString)
	{
		$returnArray = array();
		$returnArray = unserialize(base64_decode($queryString));

		return $returnArray;
	}

	/**
	 * 해시태그 상품 마이그레이션 [관리모드 > 해시태그 관련설정 > 기본 해시태그 설정]
	 * @author workingby <bumyul2000@godo.co.kr>
	 * @param string $checkboxParam
	 * @return array $returnArray
	 * @date 2016-10-05
	 */
	public function migrationHashtag($checkboxParam)
	{
		$returnArray = array('result' => 'success');
		$totalMigrationGoodsCount = 0;
		try {
			//변환될 해시태그 항목
			$articleArray = array();
			parse_str($checkboxParam, $articleArray);
			$query = self::getMigrationDefaultQuery($articleArray);
			$res = $this->db->query($query);
			if(!$res){
				throw new Exception('DB 조회를 실패하였습니다.');
			}

			while($goods = $this->db->fetch($res, 1)){
				$goodsHashtagCount = 0;
				$hashtagName = '';

				//상품당 등록된 해시태그 개수
				$goodsHashtagCount = self::getGoodsHashtagCount($goods['goodsno']);
				if($goodsHashtagCount >= $this->goodsHashtagMaxCount) continue;

				//브랜드 변환 사용 및 브랜드가 있을 경우
				if($articleArray['brand'] === 'y' && $goods['brandnm']){
					$hashtagName = self::setHashtag($goods['brandnm']);

					//해시태그 정보 저장
					self::saveMigrationHashtagData($hashtagName, $goods['goodsno']);

					$goodsHashtagCount++;
					if($goodsHashtagCount >= $this->goodsHashtagMaxCount) continue;
				}

				//유사검색어 변환 사용 및 유사검색어가 있을 경우
				if($articleArray['keyword'] === 'y' && $goods['keyword']){
					$hashtagName = self::setHashtag($goods['keyword']);

					//해시태그 정보 저장
					self::saveMigrationHashtagData($hashtagName, $goods['goodsno']);

					$goodsHashtagCount++;
					if($goodsHashtagCount >= $this->goodsHashtagMaxCount) continue;
				}

				//카테고리 변환 사용일 경우
				if($articleArray['category'] === 'y'){
					$maxlength = 0;
					$resMaxLength = $this->db->query("SELECT MAX(LENGTH(category)) as maxlength FROM ".GD_GOODS_LINK." WHERE goodsno=".$goods['goodsno']);
					if($resMaxLength){
						list($maxlength) = $this->db->fetch($resMaxLength);
					}
					if($maxlength > 0){
						$resSubCategory = $this->db->query("SELECT a.category, b.catnm FROM ".GD_GOODS_LINK." AS a INNER JOIN ".GD_CATEGORY." AS b ON a.category=b.category WHERE a.goodsno=".$goods['goodsno']." AND LENGTH(a.category)=".$maxlength);
						if($resSubCategory){
							while($category = $this->db->fetch($resSubCategory, 1)){
								$hashtagName = self::setHashtag($category['catnm']);

								//해시태그 정보 저장
								self::saveMigrationHashtagData($hashtagName, $goods['goodsno']);

								$goodsHashtagCount++;
								if($goodsHashtagCount >= $this->goodsHashtagMaxCount) continue;
							}
						}
					}
				}

				//총 상품 마이그레이션 갯수
				$totalMigrationGoodsCount++;
			}

			$returnArray['data'] = $totalMigrationGoodsCount;

			return $returnArray;
		}
		catch(Exception $e){
			$returnArray['result'] = 'fail';
			$returnArray['data'] = $e->getMessage();

			return $returnArray;
		}
	}

	/**
	 * migration query- brand
	 * @author workingby <bumyul2000@godo.co.kr>
	 * @param string $article
	 * @return string $query
	 * @date 2016-10-05
	 */
	private function getMigrationDefaultQuery($articleArray)
	{
		//유사검색어 변환 여부 따라 SELECT 컬럼 변경
		$keywordApply = '';
		if($articleArray['keyword'] === 'y'){
			$keywordApply = ", a.keyword ";
		}

		//카테고리 변환여부 따라 JOIN 타입 변경
		$categoryApply = "INNER";
		if($articleArray['category'] === 'y'){
			$categoryApply = "LEFT";
		}

		$query = "SELECT a.goodsno ".$keywordApply." FROM ".GD_GOODS." AS a";

		//브랜드 사용 여부에 따라 JOIN
		if($articleArray['brand'] === 'y'){
			$query = "SELECT a.goodsno, b.brandnm ".$keywordApply." FROM ".GD_GOODS." AS a ".$categoryApply." JOIN ".GD_GOODS_BRAND." AS b ON a.brandno=b.sno";
		}

		return $query;
	}

	/**
	 * 마이그레이션 해시태그 데이터 처리
	 * @author workingby <bumyul2000@godo.co.kr>
	 * @param string $hashtagName, integer $goodsno
	 * @return integer $count
	 * @date 2016-10-05
	 */
	private function saveMigrationHashtagData($hashtagName, $goodsno)
	{
		//기본 해시태그 추가
		self::save_hashtagTable($hashtagName);

		$hashtagSortNext = 0;
		$hashtagSortNext = self::getGoodsHashtagNextSort($goodsno);

		//상품에 연결된 해시태그 추가
		$errorMessage = '';
		$errorMessage = self::save_hashtagTable($hashtagName, $goodsno, $hashtagSortNext);
		if($errorMessage === ''){
			//statistics table insert or update
			self::save_statisticsTable($hashtagName, true);
		}
	}

	/**
	 * 상품에 등록된 해시태그 개수
	 * @author workingby <bumyul2000@godo.co.kr>
	 * @param integer $goodsno
	 * @param string $hashtag
	 * @return integer $count
	 * @date 2016-10-05
	 */
	private function getGoodsHashtagCount($goodsno, $hashtag='')
	{
		$where = '';
		if($hashtag){
			$where = " AND hashtag='".$hashtag."'";
		}

		$count = 0;
		list($count) = $this->db->fetch("SELECT COUNT(*) FROM ".GD_HASHTAG." WHERE goodsno=".$goodsno.$where);

		return $count;
	}

	/**
	 * hashtag list 출력
	 * @author workingby <bumyul2000@godo.co.kr>
	 * @param string $type
	 * @param array $param
	 * @return array $hashtagData
	 * @date 2016-10-05
	 */
	public function getHashtagList($type, $param=array())
	{
		$hashtagData = array();
		switch($type){
			//사용자 설정 [관리모드-해시태그 관련설정-사용자 설정]
			case 'userLayout':
				$hashtagLayoutOption = array(
					'inputInclude' => true, //input hidden 정보 포함여부
					'style' => 'cursor: pointer;', //style 추가
					'closeBtn' => true, //close button 포함 여부
				);
				$query = "SELECT * FROM ".GD_HASHTAG_STATISTICS." WHERE totalSort > 0 ORDER BY totalSort LIMIT 50";
			break;

			//관리모드 > 상품관리 > 상품일괄관리 > 빠른 해시태그 수정
			case 'admin_speed_goods':
				$hashtagLayoutOption = array(
					'inputInclude' => false, //input hidden 정보 포함여부
					'style' => 'cursor: pointer;',
				);
				$query = "SELECT hashtag FROM ".GD_HASHTAG." WHERE goodsno = ".$param['goodsno']." ORDER BY hashtagSort, no DESC LIMIT 10";
			break;

			case 'code_goodsCount' : //치환코드 상품등록수순
				$hashtagLayoutOption = self::getCodePluginLayoutOption($param);
				$query = "SELECT hashtag FROM ".GD_HASHTAG_STATISTICS." ORDER BY cnt DESC, no DESC LIMIT ".$param['limit'];
			break;

			case 'code_newRegister' : //치환코드 최근등록순
				$hashtagLayoutOption = self::getCodePluginLayoutOption($param);
				$query = "SELECT hashtag FROM ".GD_HASHTAG_STATISTICS." ORDER BY regDate DESC LIMIT ".$param['limit'];
			break;

			case 'code_name' : //치환코드 ㄱㄴㄷ순
				$hashtagLayoutOption = self::getCodePluginLayoutOption($param);
				$query = "SELECT hashtag FROM ".GD_HASHTAG_STATISTICS." ORDER BY hashtag LIMIT ".$param['limit'];
			break;

			case 'code_user' : //치환코드 사용자설정
				$hashtagLayoutOption = self::getCodePluginLayoutOption($param);
				$query = "SELECT hashtag FROM ".GD_HASHTAG_STATISTICS." WHERE totalSort > 0 ORDER BY totalSort LIMIT ".$param['limit'];
			break;

			//유저모드 > 메인페이지, 상품리스트, 상품상세페이지 - string 리턴
			case 'main' : case 'goodsList' : case 'goodsView' :
				$layoutHtml = '';
				$layoutHtml = self::getUserHashtagListHtml($type, $param);

				return $layoutHtml;
			break;
		}

		if(trim($query) !== ''){
			$res = $this->db->query($query);
			if($res){
				while($data = $this->db->fetch($res, 1)){
					$hashtagData[] = self::setHashTagLayout($data, $hashtagLayoutOption);
				}
			}
		}

		return $hashtagData;
	}

	private function getCodePluginLayoutOption($param)
	{
		$hashtagLayoutOption = array(
			'inputInclude' => false,
			'closeBtn' => false,
			'style' => 'cursor: pointer;',
			'link' => true,
		);
		if(trim($param['mobilePath'])){
			$hashtagLayoutOption['mobilePath'] = $param['mobilePath'];
		}

		return $hashtagLayoutOption;
	}

	private function getUserHashtagListHtml($type, $param)
	{
		global $cfg, $_SERVER;

		try {
			$hashtagConfig = array();
			$layoutHtml = '';

			if(is_file($this->configFile)){
				include $this->configFile;
			}

			$hashtagLayoutOption = array(
				'inputInclude' => false,
				'style' => 'cursor: pointer;', //style 추가
				'closeBtn'=>false,
				'link'=>true,
			);
			$query = self::getUserHashtagListQuery($type, $param, $hashtagConfig);
			if(trim($query)){
				$res = $this->db->query($query);
				if($res){
					$matchPath = array();
					if($_SERVER['PHP_SELF']){
						preg_match('/\/m2\/|\/m\//', $_SERVER['PHP_SELF'], $matchPath);
					}
					$hashtagLayoutOption['mobilePath'] = $matchPath[0];
					switch($type){
						case 'main': case 'goodsList':
							while($data = $this->db->fetch($res, 1)){
								$layoutHtml .= self::setHashTagLayout($data, $hashtagLayoutOption);
							}
						break;

						case 'goodsView':
							$layoutHtml .= "<div id='hashtagListBox'>";
							while($data = $this->db->fetch($res, 1)){
								$layoutHtml .= self::setHashTagLayout($data, $hashtagLayoutOption);
							}
							$layoutHtml .= "</div>";
							if($hashtagConfig['hashtag_goodsView_user_write'] !== 'n'){
								$divWidth = 'width: 355px;';
								$inputWidth = 'width: 335px;';
								$placeholder = '이 상품에 대한 고객님의 생각을 해시태그로 남겨주세요!';
								if($param['mobile'] === true){
									$divWidth = 'width: 175px;';
									$inputWidth = 'width: 155px;';
									$placeholder = '고객님의 생각을 남겨주세요.';
								}

								$layoutHtml .= "
									<div style='margin-top: 10px; width: 100%; display: inline-block;'>
										<div style='border: 1px #BDBDBD solid; float: left; height: 19px; padding-left: 2px; ".$divWidth."'>
										 #<input type='text' name='hashtag' id='hashtag' value='' class='hashtagInputListSearch' style='border: none; height: 16px; line-height: 16px; font:12px Arial, dotum, 돋움; ".$inputWidth."' placeholder='".$placeholder."' maxlength='20' label='해시태그' />
										</div>
										<img src='".$cfg['rootDir']."/admin/img/btn_add3.png' border='0' style='cursor: pointer; margin-bottom: 2px; margin-left: 3px;' id='hashtagAddBtn' alt='추가' align='absmiddle' />
									</div>
								";
							}
						break;
					}
				}
			}

			return $layoutHtml;
		}
		catch(Exception $e){
			return '';
		}
	}

	/**
	 * 유저모드 해시태그 네임 노출 쿼리
	 * @author workingby <bumyul2000@godo.co.kr>
	 * @param array $postData
	 * @param integer $goodsno
	 * @return array $returnArray
	 * @date 2016-10-05
	 */
	private function getUserHashtagListQuery($type, $param, $hashtagConfig)
	{
		try {
			$query = '';

			switch($type){
				//메인페이지
				case 'main' :
					if($hashtagConfig['hashtag_main_use'] !== 'n'){
						$where = '';
						$limit = ($hashtagConfig['hashtag_main_display_count']) ? $hashtagConfig['hashtag_main_display_count'] : 10;

						switch($hashtagConfig['hashtag_main_order_by']){
							case 'newRegister':
								$orderBy = 'regDate DESC';
							break;

							case 'name':
								$orderBy = 'hashtag';
							break;

							case 'user':
								$where = ' WHERE totalSort!=0 ';
								$orderBy = 'totalSort';
							break;

							case 'goodsCount': default:
								$orderBy = 'cnt DESC, no DESC';
							break;
						}

						$query = "SELECT hashtag FROM ".GD_HASHTAG_STATISTICS.$where." ORDER BY ".$orderBy." LIMIT ".$limit;
					}
				break;

				//상품상세 페이지
				case 'goodsView' :
					if($hashtagConfig['hashtag_goodsView_use'] !== 'n'){
						switch($hashtagConfig['hashtag_goodsView_order_by']){
							case 'newRegister':
								$query = "
									SELECT a.hashtag FROM
										".GD_HASHTAG." AS a
									LEFT JOIN
										".GD_HASHTAG_STATISTICS." AS b
									ON
										a.hashtag=b.hashtag
									WHERE
										a.goodsno = ".$param['goodsno']."
									ORDER BY
										b.regDate DESC
								";
							break;

							case 'name':
								$query = "SELECT hashtag FROM ".GD_HASHTAG." WHERE goodsno = ".$param['goodsno']." ORDER BY hashtag";
							break;

							case 'user':
								$query = "SELECT hashtag FROM ".GD_HASHTAG." WHERE goodsno = ".$param['goodsno']." ORDER BY hashtagSort, no DESC";
							break;

							case 'goodsCount': default:
								$query = "
									SELECT a.hashtag FROM
										".GD_HASHTAG." AS a
									LEFT JOIN
										".GD_HASHTAG_STATISTICS." AS b
									ON
										a.hashtag=b.hashtag
									WHERE
										a.goodsno = ".$param['goodsno']."
									ORDER BY
										b.cnt DESC,
										b.no DESC
								";
							break;
						}
					}
				break;

				//상품 리스트 페이지
				case 'goodsList' :
					if($hashtagConfig['hashtag_goodsList_use'] !== 'n'){
						$limit = ($hashtagConfig['hashtag_goodsList_display_count']) ? $hashtagConfig['hashtag_goodsList_display_count'] : 2;

						switch($hashtagConfig['hashtag_goodsList_order_by']){
							case 'newRegister':
								$query = "
									SELECT a.hashtag FROM
										".GD_HASHTAG." AS a
									LEFT JOIN
										".GD_HASHTAG_STATISTICS." AS b
									ON
										a.hashtag=b.hashtag
									WHERE
										a.goodsno = ".$param['goodsno']."
									ORDER BY
										b.regDate DESC
									LIMIT ".$limit;
							break;

							case 'name':
								$query = "SELECT hashtag FROM ".GD_HASHTAG." WHERE goodsno = ".$param['goodsno']." ORDER BY hashtag LIMIT ".$limit;
							break;

							case 'user':
								$query = "SELECT hashtag FROM ".GD_HASHTAG." WHERE goodsno = ".$param['goodsno']." ORDER BY hashtagSort, no DESC LIMIT ".$limit;
							break;

							case 'goodsCount': default:
								$query = "
									SELECT a.hashtag FROM
										".GD_HASHTAG." AS a
									LEFT JOIN
										".GD_HASHTAG_STATISTICS." AS b
									ON
										a.hashtag=b.hashtag
									WHERE
										a.goodsno = ".$param['goodsno']."
									ORDER BY
										b.cnt DESC,
										b.no DESC
									 LIMIT ".$limit;
							break;
						}
					}
				break;
			}

			return $query;
		}
		catch(Exception $e){
			return '';
		}
	}

	/**
	 * 빠른 해시태그 수정 처리
	 * @author workingby <bumyul2000@godo.co.kr>
	 * @param array $postData
	 * @param array $goodsnoArray
	 * @return string
	 * @date 2016-10-05
	 */
	public function indbManageHashtag($postData, $goodsnoArray)
	{
		try {
			//선처리
			switch($postData['hashtagMethod']){
				//선택된 상품들에 #해시태그를 일괄적으로 등록합니다.
				case 'all_add_goods':
					$hashtagName = self::setHashtag($postData['hashtagName1']);

					$checkResult = false;
					$checkResult = self::checkHashtag($hashtagName);
					if($checkResult === true){
						throw new Exception('등록되어 있지 않은 해시태그 입니다.');
					}
				break;

				//#해시태그 를 새로운 해시태그로 추가하고, 선택된 상품들에 일괄적으로 등록합니다.
				case 'all_add':
					$hashtagName = self::setHashtag($postData['hashtagName2']);

					$errorMessage = '';
					$errorMessage = self::save_statisticsTable($hashtagName);
					if($errorMessage !== ''){
						throw new Exception('기존에 추가된 해시태그 입니다.');
					}
				break;

				//검색된 #특정_해시태그 를 선택된 상품들에서 일괄적으로 제거합니다
				case 'tag_del':
					$hashtagName = self::setHashtag($postData['hashtagName3']);
				break;
			}

			//상품별 처리
			foreach($goodsnoArray as $goodsno) {
				switch($postData['hashtagMethod']){
					//선택된 상품들에 #해시태그를 일괄적으로 등록합니다.
					case 'all_add_goods':
						self::indbManageHashtag_allAddGoods($postData, $hashtagName, $goodsno);
					break;

					//#해시태그 를 새로운 해시태그로 추가하고, 선택된 상품들에 일괄적으로 등록합니다.
					case 'all_add':
						self::indbManageHashtag_allAdd($postData, $hashtagName, $goodsno);
					break;

					//검색된 #특정_해시태그 를 선택된 상품들에서 일괄적으로 제거합니다
					case 'tag_del':
						self::indbManageHashtag_tagDel($postData, $hashtagName, $goodsno);
					break;
				}
			}

			return '';
		}
		catch(Exception $e){
			return $e->getMessage();
		}
	}

	/**
	 * 빠른 해시태그 수정 - 선택된 상품들에 #해시태그를 일괄적으로 등록합니다.
	 * @author workingby <bumyul2000@godo.co.kr>
	 * @param array $postData
	 * @param string $hashtagName
	 * @param integer $goodsno
	 * @date 2016-10-05
	 */
	private function indbManageHashtag_allAddGoods($postData, $hashtagName, $goodsno)
	{
		$goodsHashtagCount = 0;
		$goodsHashtagCount = self::getGoodsHashtagCount($goodsno);

		//10개 이상일 시 삭제
		$errorMessage = '';
		$deleted = false;
		if($postData['all_add_goods_del'] === 'y'){
			if($goodsHashtagCount >= $this->goodsHashtagMaxCount){
				$deleteOption = array(
					'goodsno' => $goodsno, //상품번호
					'lastDelete' => 'y', //정렬순서상 맨 뒤 건 삭제
				);
				$errorMessage = self::delete_hashtagTable($deleteOption);
				if($errorMessage === ''){
					$goodsHashtagCount--;
					$deleted = true;
				}
			}
		}

		if($errorMessage === '' && $goodsHashtagCount < $this->goodsHashtagMaxCount){
			//insert hashtag table
			$errorMessage = self::save_hashtagTable($hashtagName, $goodsno);
			//지워진 내역이 없을시 카운트 증가
			if($errorMessage === '' && $deleted === false){
				self::update_count_statisticsTable('plus', $hashtagName);
			}
		}
	}

	/**
	 * 빠른 해시태그 수정 - #해시태그 를 새로운 해시태그로 추가하고, 선택된 상품들에 일괄적으로 등록합니다.
	 * @author workingby <bumyul2000@godo.co.kr>
	 * @param array $postData
	 * @param string $hashtagName
	 * @param integer $goodsno
	 * @date 2016-10-05
	 */
	private function indbManageHashtag_allAdd($postData, $hashtagName, $goodsno)
	{
		$goodsHashtagCount = 0;
		$goodsHashtagCount = self::getGoodsHashtagCount($goodsno);

		//10개 이상일 시 삭제
		$errorMessage = '';
		$deleted = false;
		if($postData['all_add_del'] === 'y'){
			if($goodsHashtagCount >= $this->goodsHashtagMaxCount){
				$deleteOption = array(
					'goodsno' => $goodsno, //상품번호
					'lastDelete' => 'y', //정렬순서상 맨 뒤 건 삭제
				);
				$errorMessage = self::delete_hashtagTable($deleteOption);
				if($errorMessage === ''){
					$goodsHashtagCount--;
					$deleted = true;
				}
			}
		}

		if($errorMessage === '' && $goodsHashtagCount < $this->goodsHashtagMaxCount){
			self::save_hashtagTable($hashtagName);

			//insert hashtag table
			$errorMessage = self::save_hashtagTable($hashtagName, $goodsno);
			//지워진 내역이 없을시 카운트 증가
			if($errorMessage === '' && $deleted === false){
				self::update_count_statisticsTable('plus', $hashtagName);
			}
		}
	}

	/**
	 * 빠른 해시태그 수정 - 검색된 #특정_해시태그 를 선택된 상품들에서 일괄적으로 제거합니다
	 * @author workingby <bumyul2000@godo.co.kr>
	 * @param array $postData
	 * @param string $hashtagName
	 * @param integer $goodsno
	 * @date 2016-10-05
	 */
	private function indbManageHashtag_tagDel($postData, $hashtagName, $goodsno)
	{
		try {
			$cnt = 0;
			list($cnt) = $this->db->fetch("SELECT COUNT(*) AS cnt FROM ".GD_HASHTAG." WHERE hashtag='".$hashtagName."'");

			if($cnt > 1){ //등록되어있는 개수가 2개 이상일때
				$errorMessage = '';
				$errorMessage = self::delete_hashtagTable(array('hashtag'=>$hashtagName, 'goodsno'=>$goodsno));
				if($errorMessage === ''){
					self::update_count_statisticsTable('minus', $hashtagName);
				}
			}
			else if($cnt == 1){
				$res = $this->db->query("UPDATE ".GD_HASHTAG." SET goodsno=0 WHERE hashtag='".$hashtagName."'");
				if($res){
					self::update_count_statisticsTable('zero', $hashtagName);
				}
			}
			else {
				self::delete_statisticsTable($hashtagName);
			}

			return '';
		}
		catch(Exception $e){
			return $e->getMessage();
		}
	}

	/**
	 * 상품에 연결된 해시태그중 가장 큰 sort 증가값
	 * @author workingby <bumyul2000@godo.co.kr>
	 * @param integer $goodsno
	 * @return integer $returnArray
	 * @date 2016-10-05
	 */
	private function getGoodsHashtagNextSort($goodsno)
	{
		$hashtagSort = 0;
		list($hashtagSort) = $this->db->fetch("SELECT MAX(hashtagSort) as hashtagSort FROM ".GD_HASHTAG." WHERE goodsno=".$goodsno);
		(int)$hashtagSort += 1;

		return $hashtagSort;
	}

	/**
	 * 유저모드 해시태그 추가
	 * @author workingby <bumyul2000@godo.co.kr>
	 * @param array $postData
	 * @return array $returnArray
	 * @date 2016-10-05
	 */
	public function saveHashtagLiveUser($postData)
	{
		$returnArray = array('result' => 'success');

		try {
			if(!trim($postData['hashtag'])){
				throw new Exception('해시태그명을 입력해 주세요.');
			}

			$hashtagName = self::setHashtag($postData['hashtag']);

			//기존 존재 여부 체크
			$hashtagGoodsCount = 0;
			$hashtagGoodsCount = self::getGoodsHashtagCount($postData['goodsno'], $postData['hashtag']);
			if($hashtagGoodsCount > 0){
				throw new Exception('동일한 해시태그가 존재합니다.');
			}

			//check hashtag
			$errorMessageStatistics = self::save_statisticsTable($hashtagName, true);
			if($errorMessageStatistics !== ''){
				throw new Exception('죄송합니다. 등록을 실패하였습니다.');
			}

			$hashtagSortNext = 0;
			$hashtagSortNext = self::getGoodsHashtagNextSort($postData['goodsno']);

			//기본 해시태그 추가
			self::save_hashtagTable($hashtagName);

			//hashtag table insert
			$errorMessage ='';
			$errorMessage = self::save_hashtagTable($hashtagName, $postData['goodsno'], $hashtagSortNext);
			if($errorMessage !== ''){
				throw new Exception($errorMessage);
			}

			$hashtagLayoutOption = array(
				'inputInclude' => false, // input hidden 정보 포함여부
				'style' => 'cursor: pointer;', //style 추가
				'closeBtn' => false, // X버튼 노출 여부
				'link' => true, // 링크 여부
				'mobilePath' => $postData['mobilePath'],
			);
			$returnArray['data'] = self::setHashTagLayout(array('hashtag' => $hashtagName), $hashtagLayoutOption);

			return $returnArray;
		}
		catch(Exception $e){
			$returnArray['result'] = 'fail';
			$returnArray['data'] = $e->getMessage();

			return $returnArray;
		}
	}

	/**
	 * 상품삭제시
	 * @author workingby <bumyul2000@godo.co.kr>
	 * @param integer $goodsno
	 * @return void
	 * @date 2016-10-05
	 */
	public function deleteGoods($goodsno)
	{
		if((int)$goodsno < 1){
			return;
		}
		$res = $this->db->query("SELECT hashtag FROM ".GD_HASHTAG." WHERE goodsno=".$goodsno);
		if($res){
			while($hashtagRow = $this->db->fetch($res, 1)){
				$count = 0;
				list($count) = $this->db->fetch("SELECT COUNT(*) FROM ".GD_HASHTAG." WHERE hashtag='".$hashtagRow['hashtag']."' AND goodsno=0 LIMIT 1");
				if($count > 0){
					$secondQuery = "DELETE FROM ".GD_HASHTAG." WHERE hashtag='".$hashtagRow['hashtag']."' AND goodsno=".$goodsno;
				}
				else {
					$secondQuery = "UPDATE ".GD_HASHTAG." SET goodsno=0, hashtagSort=0 WHERE hashtag='".$hashtagRow['hashtag']."' AND goodsno=".$goodsno;
				}
				$secondResult = $this->db->query($secondQuery);
				if($secondResult){
					self::update_count_statisticsTable('minus', $hashtagRow['hashtag']);
				}
			}
		}
	}

	/**
	 * 상품복사시
	 * @author workingby <bumyul2000@godo.co.kr>
	 * @param integer $originalGoodsno
	 * @param integer $copyGoodsno
	 * @return void
	 * @date 2016-10-05
	 */
	public function copyGoods($originalGoodsno, $copyGoodsno)
	{
		if((int)$originalGoodsno < 1 || (int)$copyGoodsno < 1){
			return;
		}
		$res = $this->db->query("SELECT hashtag, hashtagSort FROM ".GD_HASHTAG. " WHERE goodsno=".$originalGoodsno);
		if($res){
			while($hashtagRow = $this->db->fetch($res, 1)){
				$insertRes = $this->db->query("INSERT INTO ".GD_HASHTAG." (hashtag, goodsno, hashtagSort) VALUES ('".$hashtagRow['hashtag']."', ".$copyGoodsno.", '".$hashtagRow['hashtagSort']."')");
				if($insertRes){
					self::update_count_statisticsTable('plus', $hashtagRow['hashtag']);
				}
			}
		}
	}

	/**
	 * SNS PC
	 * @author workingby <bumyul2000@godo.co.kr>
	 * @param string $guidedSellingPage
	 * @return string $snsBtn
	 * @date 2016-10-05, 2016-11-24
	 */
	public function getSnsBtn($guidedSellingPage='')
	{
		global $snsCfg, $_SERVER, $_GET, $cfg, $guidedSelling;

		$snsBtn = '';
		$hashtagConfig = array();
		$hashtagConfig = self::getConfig();

		if($hashtagConfig['hashtag_snsUse'] !== 'n' && $snsCfg['useBtn'] === 'y'){
			$snsCfg['use_pinterest'] = 'n';

			include_once '../lib/sns.class.php';
			$sns = new SNS();

			if($guidedSellingPage === 'y'){
				//가이디드 셀링 SNS
				$hashtagParameter = array();
				if(count($_GET['hashtagName']) > 0){
					foreach($_GET['hashtagName'] as $hashtagName){
						$hashtagParameter[] = "hashtagName[]=".urlencode($hashtagName);
					}
				}
				$goodsGuidedSellingUrl = 'http://' . $_SERVER['HTTP_HOST'] . $_SERVER['PHP_SELF'] . '?guided_no='.$_GET['guided_no'].'&step='.$_GET['step'].'&'.implode("&", $hashtagParameter);
				if(is_object($guidedSelling)){
					$guidedData = $guidedSelling->getGuidedSellingData($_GET['guided_no']);
				}
				$goodsnm = ($guidedData['guided_subject']) ? $guidedData['guided_subject'] : 'GUIDED SELLING';
				$args = array(
					'shopnm' => $cfg['shopName'],
					'goodsnm' => $goodsnm,
					'goodsurl' => $goodsGuidedSellingUrl,
				);
			}
			else {
				//해시태그 SNS
				$goodsHashtagurl = 'http://' . $_SERVER['HTTP_HOST'] . $_SERVER['PHP_SELF'] . '?hashtag=' . urlencode($_GET['hashtag']);
				$args = array(
					'shopnm' => $cfg['shopName'],
					'goodsnm' => $_GET['hashtag'],
					'goodsurl' => $goodsHashtagurl,
				);
			}

			$snsRes = call_user_func_array(array($sns, 'get_post_btn'), array($args, ''));
			// 페이스북에 사용될 meta tag
			$snsBtn = $snsRes['btn'];
		}

		return $snsBtn;
	}

	/**
	 * SNS MOBILE
	 * @author workingby <bumyul2000@godo.co.kr>
	 * @param string $guidedSellingPage
	 * @return string $snsBtn
	 * @date 2016-10-05
	 */
	public function getMobileSnsBtn($guidedSellingPage='')
	{
		global $snsCfg, $_SERVER, $_GET, $cfg, $guidedSelling;

		$snsBtn = '';
		$hashtagConfig = $msgKakao = $msg_kakaoStory = array();
		$hashtagConfig = self::getConfig();

		if($hashtagConfig['hashtag_snsUse'] !== 'n' && $snsCfg['useBtn'] === 'y'){
			$snsCfg['use_pinterest'] = 'n';

			include_once dirname(__FILE__).'/../lib/sns.class.php';
			$sns = new SNS();
			if($guidedSellingPage === 'y'){
				$hashtagParameter = array();
				if(count($_GET['hashtagName']) > 0){
					foreach($_GET['hashtagName'] as $hashtagName){
						$hashtagParameter[] = "hashtagName[]=".urlencode($hashtagName);
					}
				}

				$goodsGuidedSellingUrl = 'http://' . $_SERVER['HTTP_HOST'] . $_SERVER['PHP_SELF'] . '?guided_no='.$_GET['guided_no'].'&step='.$_GET['step'].'&'.implode("&", $hashtagParameter);
				if(is_object($guidedSelling)){
					$guidedData = $guidedSelling->getGuidedSellingData($_GET['guided_no']);
				}
				$goodsnm = ($guidedData['guided_subject']) ? $guidedData['guided_subject'] : 'GUIDED SELLING';
				$args = array(
					'shopnm' => $cfg['shopName'],
					'goodsnm' => $goodsnm,
					'goodsurl' => $goodsGuidedSellingUrl,
					'img' => '',
					'img_l' => ''
				);
			}
			else {
				$goodsHashtagUrl = 'http://' . $_SERVER['HTTP_HOST'] . $_SERVER['PHP_SELF'] . '?hashtag=' . urlencode($_GET['hashtag']);
					$args = array(
					'shopnm' => $cfg['shopName'],
					'goodsnm' => $_GET['hashtag'],
					'goodsurl' => $goodsHashtagUrl,
					'img' => '',
					'img_l' => ''
				);
			}

			$snsRes = call_user_func_array(array($sns, 'get_post_btn_mobile'), array($args, 'm'));
			$snsBtn = $snsRes['btn'];

			// 모바일웹에서는 카카오링크를 사용하며, 싸이월드 공감을 사용하지 않음
			if($snsCfg['use_kakao'] == 'y') {
				//Ver 2.0
				$msgKakao['msg_kakao1'] = $sns->msg_kakao1;
				$msgKakao['msg_kakao2'] = $sns->msg_kakao2;
				$msgKakao['msg_kakao3'] = $sns->msg_kakao3;

				//Ver 3.5
				@include_once  dirname(__FILE__).'/../lib/kakaotalkLink.class.php';
				$kakaotalkLink = new KakaotalkLink();
				$msgKakao['kakaoTalkLinkScript'] = $kakaotalkLink->getKakaoScript(get_object_vars($sns));
			}

			// 카카오스토리
			if($snsCfg['use_kakaoStory'] == 'y') {
				$msg_kakaoStory['msg_kakaoStory_shopnm']	= $sns->msg_kakaoStory_shopnm;
				$msg_kakaoStory['msg_kakaoStory_goodsnm']	= $sns->msg_kakaoStory_goodsnm;
				$msg_kakaoStory['msg_kakaoStory_goodsurl']	= $sns->msg_kakaoStory_goodsurl;
				$msg_kakaoStory['msg_kakaoStory_img_l']		= $sns->msg_kakaoStory_img_l;
			}
		}

		return array($snsBtn, $msgKakao, $msg_kakaoStory);
	}
}
?>