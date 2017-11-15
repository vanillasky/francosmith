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
 * workingby         2016.11.24        First Draft.
 */
set_time_limit(0);
ini_set("memory_limit", -1);
/**
 * guidedSelling
 *
 * @author guidedSelling.class.php workingby <bumyul2000@godo.co.kr>
 * @version 1.0
 * @date 2016-11-24
 */
class guidedSelling
{
	private $db;
	private $guidedSellingNo;
	//이미지 저장 폴더
	private $folderDir = '../../data/guidedSelling/';
	//이미지 임시 저장 폴더
	private	$temp_folderDir = '../../data/guidedSelling/temp/';

	function __construct()
	{
		global $db;

		$this->db = $db;
	}

	/**
	 * 파라미터 체크
	 * @author workingby <bumyul2000@godo.co.kr>
	 * @param array $postData
	 * @return string
	 * @date 2016-11-24
	 */
	private function checkRequiredParameter($postData)
	{
		try {
			if($postData['mode'] === 'modify'){
				if(!$postData['guided_no']){
					throw new exception("올바른 접근이 아닙니다.");
				}
			}
			if(!trim($postData['guided_subject'])){
				throw new exception("가이드 셀링 이름을 입력해 주세요.");
			}
			if(!trim($postData['guided_backgroundColor'])){
				throw new exception("배경색을 입력하여 주세요.");
			}
			foreach($postData['unit_question'] as $key => $questionName){
				if(!trim($questionName)){
					throw new exception("질문을 입력해 주세요.");
					break;
				}
				foreach($postData['detail_hashtagName'][$key] as $hashtagName){
					if(!trim($hashtagName)){
						throw new exception("해시태그를 입력해 주세요.");
						break;
					}
				}
			}

			return '';
		}
		catch(Exception $e){
			return $e->getMessage();
		}
	}

	/**
	 * submit 전 데이터 체크
	 * @author workingby <bumyul2000@godo.co.kr>
	 * @param array $postData
	 * @return string
	 * @date 2016-11-24
	 */
	public function checkBeforeSubmit($postData)
	{
		$returnArray = array('result' => 'success');

		try {
			$hashtagNameArray = array();
			parse_str($postData['hashtagName'], $hashtagNameArray);

			//해시태그 존재 체크
			$returnArray['data'] = self::checkHashtagValidation($hashtagNameArray['detail_hashtagName'], 'ajax');

			return $returnArray;
		}
		catch(Exception $e){
			$returnArray['result'] = 'fail';
			$returnArray['data'] = $e->getMessage();

			return $returnArray;
		}
	}

	/**
	 * data 저장
	 * @author workingby <bumyul2000@godo.co.kr>
	 * @param array $postData
	 * @param array $fileData
	 * @return string
	 * @date 2016-11-24
	 */
	public function saveGuidedSelling($postData, $fileData = array())
	{
		try {
			//필수값 체크
			$errorMessage = '';
			$errorMessage = self::checkRequiredParameter($postData);
			if($errorMessage !== ''){
				throw new exception($errorMessage);
			}

			//해시태그 존재 체크
			$errorMessage = self::checkHashtagValidation($postData['detail_hashtagName']);
			if($errorMessage !== ''){
				throw new exception($errorMessage);
			}

			if(count($postData['existCheckImageInput']) > 0){
				foreach($postData['existCheckImageInput'] as $value){
					if($value !== 'y'){
						throw new exception("답변 이미지를 등록해 주세요.");
					}
				}
			}

			$errorMessage = self::insertGuidedSelling($postData);
			if($errorMessage !== ''){
				throw new exception("DB패치를 확인해 주세요.");
			}

			if($this->guidedSellingNo){
				$errorMessage = self::saveGuidedSellingUnit($postData, $fileData);
				if($errorMessage !== ''){
					throw new exception("DB패치를 확인해 주세요.");
				}
			}

			//임시 이미지 파일 삭제
			self::resetTempImage();

			return '';
		}
		catch(Exception $e){
			return $e->getMessage();
		}
	}

	/**
	 * 해시태그 유효성 체크
	 * @author workingby <bumyul2000@godo.co.kr>
	 * @param array $detail_hashtagName
	 * @param string $type
	 * @return string
	 * @date 2016-11-24
	 */
	private function checkHashtagValidation($detail_hashtagName, $type='')
	{
		try {
			if(is_file('../../lib/hashtag.class.php')){
				$hashtagObj = Core::loader('hashtag');
			}
			if(!is_object($hashtagObj)){
				throw new exception("해시태그 패치를 진행해 주세요.");
			}
			if(count($detail_hashtagName) > 0){
				$index = 0;
				foreach($detail_hashtagName as $key => $hashtagArray){
					foreach($hashtagArray as $hashtag){
						$checkHashtag = false;

						$hashtag = $hashtagObj->setHashtag($hashtag);
						$checkHashtag = $hashtagObj->checkHashtag($hashtag);
						if($checkHashtag === true){
							if($type === 'ajax'){
								throw new exception($index);
							}
							else {
								throw new exception("존재하지 않는 해시태그가 포함되어 있습니다.");
							}
						}
						$index++;
					}
				}
			}

			return '';
		}
		catch(Exception $e){
			return $e->getMessage();
		}
	}

	/**
	 * 해시태그 유효성 체크 - 상품확인시
	 * @author workingby <bumyul2000@godo.co.kr>
	 * @param string $hashtag
	 * @return string
	 * @date 2016-11-24
	 */
	public function checkHashtagOpenPage($hashtag)
	{
		$returnArray = array('result' => 'success');

		try {
			if(is_file('../../lib/hashtag.class.php')){
				$hashtagObj = Core::loader('hashtag');
			}
			if(!is_object($hashtagObj)){
				throw new exception("해시태그 패치를 진행해 주세요.");
			}

			$hashtag = $hashtagObj->setHashtag($hashtag);
			$checkHashtag = $hashtagObj->checkHashtag($hashtag);
			if($checkHashtag === true){
				throw new exception("입력된 해시태그가 등록되지 않았습니다.\n다시 확인해주세요.");
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
	 * data 수정
	 * @author workingby <bumyul2000@godo.co.kr>
	 * @param array $postData
	 * @param array $fileData
	 * @return string
	 * @date 2016-11-24
	 */
	public function modifyGuidedSelling($postData, $fileData = array())
	{
		try {
			//필수값 체크
			$errorMessage = '';
			$errorMessage = self::checkRequiredParameter($postData);
			if($errorMessage !== ''){
				throw new exception($errorMessage);
			}

			//해시태그 존재 체크
			$errorMessage = self::checkHashtagValidation($postData['detail_hashtagName']);
			if($errorMessage !== ''){
				throw new exception($errorMessage);
			}

			$res = $this->db->query("UPDATE ".GD_GUIDEDSELLING." SET guided_subject='".$postData['guided_subject']."', guided_backgroundColor='".$postData['guided_backgroundColor']."' WHERE guided_no=".$postData['guided_no']);
			if(!$res){
				throw new exception("수정을 실패하였습니다.");
			}

			//답변 삭제
			if(count($postData['answer_deleteNo']) > 0){
				foreach($postData['answer_deleteNo'] as $detailNo){
					$errorMessage = self::deleteGuidedSelling('detail', $detailNo);
					if($errorMessage !== ''){
						throw new exception($errorMessage);
					}
				}
			}
			//질문 & 답변 삭제
			if(count($postData['question_deleteNo']) > 0){
				foreach($postData['question_deleteNo'] as $unitNo){
					$errorMessage = self::deleteGuidedSelling('unit', $unitNo);
					if($errorMessage !== ''){
						throw new exception($errorMessage);
					}
				}
			}
			$this->guidedSellingNo = $postData['guided_no'];

			//data 저장
			$errorMessage = self::saveGuidedSellingUnit($postData, $fileData);
			if($errorMessage !== ''){
				throw new exception("DB패치를 확인해 주세요.");
			}

			//임시 이미지 파일 삭제
			self::resetTempImage();

			return '';
		}
		catch(Exception $e){
			return $e->getMessage();
		}
	}

	/**
	 * insert guided selling
	 * @author workingby <bumyul2000@godo.co.kr>
	 * @param array $data
	 * @return string
	 * @date 2016-11-24
	 */
	private function insertGuidedSelling($data)
	{
		try {
			$res = $this->db->query("INSERT INTO ".GD_GUIDEDSELLING." (guided_subject, guided_backgroundColor) VALUES ('".$data['guided_subject']."', '".$data['guided_backgroundColor']."')");
			if(!$res){
				throw new exception("GUIDEDSELLING INSERT 실패");
			}
			$this->guidedSellingNo = $this->db->_last_insert_id();

			return '';
		}
		catch(Exception $e){
			return $e->getMessage();
		}
	}

	/**
	 * insert - update : guided selling unit, unit detail, copy image
	 * @author workingby <bumyul2000@godo.co.kr>
	 * @param array $data
	 * @param array $fileData
	 * @return string
	 * @date 2016-11-24
	 */
	private function saveGuidedSellingUnit($data, $fileData)
	{
		try {
			$step = 1;
			$errorMessage = '';
			foreach($data['question_no'] as $uniqueKey => $guidedSellingUnitNo){
				$questionName = $data['unit_question'][$uniqueKey];
				$displayType = $data['unit_displayType'][$uniqueKey];

				//수정
				if($guidedSellingUnitNo > 0 ){
					$mode = "modify";

					//기존 존재중인 질문
					$query = "
						UPDATE
							".GD_GUIDEDSELLING_UNIT."
						SET
							unit_question = '".$questionName."',
							unit_displayType = '".$displayType."',
							unit_step = '".$step."'
						WHERE
							unit_no=".$guidedSellingUnitNo."
					";

					$unitNoInsertId = $guidedSellingUnitNo;
					$res = $this->db->query($query);
				}
				//쓰기
				else {
					$mode = "write";

					//새로 등록되는 질문
					$query = "
						INSERT INTO
							".GD_GUIDEDSELLING_UNIT."
						SET
							unit_question = '".$questionName."',
							unit_displayType = '".$displayType."',
							unit_guidedNo = '".$this->guidedSellingNo."',
							unit_step = '".$step."'
					";

					$res = $this->db->query($query);
					$unitNoInsertId = $this->db->_last_insert_id();
				}

				if(!$res){
					throw new exception("GUIDEDSELLING UNIT SAVE 실패");
				}

				//이미지 데이터 처리, 복사
				$imageDataParameter = array(
					'mode' => $mode,
					'type' => 'unit',
					'insertID' => $unitNoInsertId,
					'uniqueKey' => $uniqueKey,
					'unit_backgroundImageDeleteNo' => $data['unit_backgroundImageDeleteNo'][$unitNoInsertId],
				);
				$errorMessage = self::saveImageData($imageDataParameter);
				if($errorMessage !== ''){
					throw new exception($errorMessage);
				}

				$errprMessage = self::saveGuidedSellingDetail($data, $uniqueKey, $unitNoInsertId);
				if($errprMessage !== ''){
					throw new exception("GUIDEDSELLING UNIT DETAIL SAVE 실패");
				}

				$step++;
			}

			return '';
		}
		catch(Exception $e){
			return $e->getMessage();
		}
	}

	/**
	 * insert-update : guided selling unit, unit detail, copy image
	 * @author workingby <bumyul2000@godo.co.kr>
	 * @param array $data
	 * @param string $uniqueKey
	 * @param string $unitNoInsertId
	 * @return string
	 * @date 2016-11-24
	 */
	private function saveGuidedSellingDetail($data, $uniqueKey, $unitNoInsertId)
	{
		try {
			self::setImageFolder(substr($this->folderDir, 3));

			$hashtagObj = Core::loader('hashtag');
			if(!is_object($hashtagObj)){
				throw new exception("해시태그 패치를 진행해 주세요.");
			}

			foreach($data['answer_no'][$uniqueKey] as $index => $answer_no){
				$detail_hashtagName = '';
				$detail_hashtagName = $hashtagObj->setHashtag($data['detail_hashtagName'][$uniqueKey][$index]);

				if($answer_no > 0){
					$mode = "modify";
					$queryDetail = "UPDATE ".GD_GUIDEDSELLING_UNITDETAIL." SET detail_hashtagName='".$detail_hashtagName."' WHERE detail_no=".$answer_no;
					$resDetail = $this->db->query($queryDetail);
					if(!$resDetail){
						throw new exception("GUIDEDSELLING UNIT DETAIL UPDATE 실패");
					}
					$detailInsertId = $answer_no;
				}
				else {
					$mode = "write";
					$queryDetail = "INSERT INTO ".GD_GUIDEDSELLING_UNITDETAIL." (detail_unitNo, detail_hashtagName) VALUES ('".$unitNoInsertId."', '".$detail_hashtagName."')";
					$resDetail = $this->db->query($queryDetail);
					if(!$resDetail){
						throw new exception("GUIDEDSELLING UNIT DETAIL INSERT 실패");
					}
					$detailInsertId = $this->db->_last_insert_id();
				}

				//이미지 데이터 처리, 복사
				$imageDataParameter = array(
					'mode' => $mode,
					'type' => 'detail',
					'insertID' => $detailInsertId,
					'uniqueKey' => $uniqueKey,
					'index' => $index,
				);

				$errorMessage = '';
				$errorMessage = self::saveImageData($imageDataParameter);
				if($errorMessage !== ''){
					throw new exception($errorMessage);
				}
			}

			return '';
		}
		catch(Exception $e){
			return $e->getMessage();
		}
	}

	/**
	 * 이미지 복사 & 정보 저장
	 * @author workingby <bumyul2000@godo.co.kr>
	 * @param array $parameter
	 * @return string
	 * @date 2016-11-24
	 */
	private function saveImageData($parameter)
	{
		try {
			$updateQuery = array();
			$imageName = $pcImageName = $mobileImageName = $backgroundImageName = '';
			$folderDir = substr($this->folderDir, 3);
			$temp_folderDir = substr($this->temp_folderDir, 3);

			if($parameter['type'] === 'unit'){
				//백그라운드 이미지 저장
				$searchPathFile = dirname(__FILE__).'/'.$temp_folderDir.$parameter['uniqueKey']."_*";
			}
			else if($parameter['type'] === 'detail'){
				//답변이미지 저장
				$searchPathFile = dirname(__FILE__).'/'.$temp_folderDir.$parameter['uniqueKey'].$parameter['index']."_*";
			}
			else {
				throw new exception("이미지 업데이트 실패");
			}

			foreach(glob($searchPathFile) as $tempFileName){
				$imageName = end(explode("/", $tempFileName));
				if(preg_match("/\_pc/", $imageName)){
					$pcImageName = $imageName;
				}
				else if(preg_match("/\_mobile/", $imageName)){
					$mobileImageName = $imageName;
				}
				else {}

				$newFileName = dirname(__FILE__).'/'.$folderDir.$imageName;
				if(!copy($tempFileName, $newFileName)){
					throw new exception("FILE COPY 실패.");
				}
			}

			if($parameter['type'] === 'unit'){
				$backgroundImageDataUpdate = false;

				//새로 등록한 이미지가 있을 시 업데이트
				if($pcImageName){
					$backgroundImageDataUpdate = true;
				}
				//디스플레이 체인지가 있었다면 업데이트
				if($parameter['unit_backgroundImageDeleteNo'] === 'y' || $pcImageName) {
					self::deleteBackgroundImage($parameter['insertID']);
					$backgroundImageDataUpdate = true;
				}
				if($backgroundImageDataUpdate === true){
					$updateQuery = "UPDATE ".GD_GUIDEDSELLING_UNIT." SET unit_backgroundImage='".$pcImageName."' WHERE unit_no = ".$parameter['insertID'];
					$res = $this->db->query($updateQuery);
					if(!$res){
						throw new exception("이미지 업데이트 실패");
					}
				}
			}
			else if($parameter['type'] === 'detail'){
				if($pcImageName || $mobileImageName){
					if($pcImageName) {
						if($parameter['mode'] === 'modify') self::deleteAnswerImage('pcImage', $parameter['insertID']);
						$updateQuery[] = " detail_pcImage='".$pcImageName."' ";
					}
					if($mobileImageName) {
						if($parameter['mode'] === 'modify') self::deleteAnswerImage('mobileImage', $parameter['insertID']);
						$updateQuery[] = " detail_mobileImage='".$mobileImageName."' ";
					}

					$updateQuery = "UPDATE ".GD_GUIDEDSELLING_UNITDETAIL." SET ".implode(", ", $updateQuery)." WHERE detail_no = ".$parameter['insertID'];
					$res = $this->db->query($updateQuery);
					if(!$res){
						throw new exception("이미지 업데이트 실패");
					}
				}
			}
			else { }

			return '';
		}
		catch(Exception $e){
			return $e->getMessage();
		}
	}

	/**
	 * 답변 이미지 삭제
	 * @author workingby <bumyul2000@godo.co.kr>
	 * @param string $deleteType
	 * @param integer $guidedSellingDetailNo
	 * @return void
	 * @date 2016-11-24
	 */
	private function deleteAnswerImage($deleteType, $guidedSellingDetailNo)
	{
		if($deleteType === 'pcImage'){
			list($imageName) = $this->db->fetch("SELECT detail_pcImage FROM ".GD_GUIDEDSELLING_UNITDETAIL." WHERE detail_no=".$guidedSellingDetailNo);
		}
		else if($deleteType === 'mobileImage'){
			list($imageName) =$this->db->fetch("SELECT detail_mobileImage FROM ".GD_GUIDEDSELLING_UNITDETAIL." WHERE detail_no=".$guidedSellingDetailNo);
		}
		else { }

		if($imageName){
			self::deleteImage(array($imageName));
		}
	}

	/**
	 * 배경화면 이미지 삭제
	 * @author workingby <bumyul2000@godo.co.kr>
	 * @param integer $guidedSellingUnitNo
	 * @return void
	 * @date 2016-11-24
	 */
	private function deleteBackgroundImage($guidedSellingUnitNo)
	{
		list($deleteBackgroundImage) = $this->db->fetch("SELECT unit_backgroundImage FROM ".GD_GUIDEDSELLING_UNIT." WHERE unit_no=".$guidedSellingUnitNo);
		if($deleteBackgroundImage){
			self::deleteImage(array($deleteBackgroundImage));
		}
	}

	/**
	 * 질문 UI 추가
	 * @author workingby <bumyul2000@godo.co.kr>
	 * @param array $postData
	 * @return array $returnArray
	 * @date 2016-11-24
	 */
	public function getLiveQuestion($postData)
	{
		$returnArray = array('result' => 'success');

		$questionLayout = '';

		try {
			switch($postData['formMode']){
				case 'write':
					$postData['uniqueKey'] = self::getUniqueKey(); //고유키 부여

					$questionLayout = self::getQuestionLayout('top', $postData['uniqueKey']);
					for($i=0; $i<3; $i++){
						$questionLayout.= self::getAnswerLayout($postData, $i+1);
					}
					$questionLayout.= self::getQuestionLayout('bottom', $postData['uniqueKey']);
				break;

				case 'modify':
					$uniqueKey = '';
					$index = 0;
					$guidedData = array();
					$guidedData = self::getGuidedSellingData($postData['guided_no']);

					$res = $this->db->query("SELECT * FROM ".GD_GUIDEDSELLING_UNIT." WHERE unit_guidedNo=".$postData['guided_no']." ORDER BY unit_step ASC");
					while($unit = $this->db->fetch($res, 1)){
						$unit['guided_backgroundColor'] = $guidedData['guided_backgroundColor'];

						//고유키 부여
						$uniqueKey = self::getUniqueKey();

						//ui 상단
						$questionLayout[$index] = self::getQuestionLayout('top', $uniqueKey, $unit);

						//백그라운드 이미지 버튼 삽입
						if($unit['unit_displayType'] === 't'){
							$questionLayout[$index] .= self::getBackgroundImageBtn($uniqueKey);
						}

						//답변 ui
						$resDetail = $this->db->query("SELECT * FROM ".GD_GUIDEDSELLING_UNITDETAIL." WHERE detail_unitNo=".$unit['unit_no']." ORDER BY detail_no ASC");
						if($resDetail){
							$answerSortNum = 1;
							while($detail = $this->db->fetch($resDetail, 1)){
								$detail['uniqueKey'] = $uniqueKey;
								$detail['unit_displayType'] = $unit['unit_displayType'];
								$questionLayout[$index] .= self::getAnswerLayout($detail, $answerSortNum);
								$answerSortNum++;
							}
						}

						//ui 하단
						$questionLayout[$index] .= self::getQuestionLayout('bottom', $uniqueKey, $unit);

						$index++;
					}
				break;
			}

			$returnArray['data'] = $questionLayout;

			return $returnArray;
		}
		catch(Exception $e){
			$returnArray['result'] = 'fail';
			$returnArray['data'] = $e->getMessage();

			return $returnArray;
		}
	}

	/**
	 * 답변 목록 추가
	 * @author workingby <bumyul2000@godo.co.kr>
	 * @param array $postData
	 * @return array $returnArray
	 * @date 2016-11-24
	 */
	public function getLiveAnswer($postData)
	{
		$returnArray = array('result' => 'success');

		try {
			$returnArray['data'] = self::getAnswerLayout($postData, $postData['answerSortNum']);
			if(!trim($returnArray['data'])){
				throw new exception("답변 목록 추가를 실패하였습니다.");
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
	 * 질문 디스플레이 유형 변환에 따른 레이아웃 추가
	 * @author workingby <bumyul2000@godo.co.kr>
	 * @param array $postData
	 * @return array $returnArray
	 * @date 2016-11-24
	 */
	public function changeAnswerDisplay($postData)
	{
		$returnArray = array('result' => 'success');

		try {
			$answerLayout = '';
			if($postData['unit_displayType'] === 't'){
				$answerLayout .= self::getBackgroundImageBtn($postData['uniqueKey']);
			}
			for($i=0; $i<3; $i++){
				$answerLayout.= self::getAnswerLayout($postData, $i+1);
			}
			$answerLayout .= self::getAnswerAddButton($postData['unit_displayType']);

			$returnArray['data'] = $answerLayout;

			return $returnArray;
		}
		catch(Exception $e){
			$returnArray['result'] = 'fail';
			$returnArray['data'] = $e->getMessage();

			return $returnArray;
		}
	}

	/**
	 * 백그라운드 이미지 삽입 버튼
	 * @author workingby <bumyul2000@godo.co.kr>
	 * @param string $uniqueKey
	 * @return string $layout
	 * @date 2016-11-24
	 */
	private function getBackgroundImageBtn($uniqueKey)
	{
		$layout = "
		<div class='guidedSelling-backgroundImageBtnArea'>
			<img src='../img/btn_background_save.png' class='guidedSelling_backgroundImageSaveBtn hand' border='0' alt='백그라운드 이미지 등록' data-uniqueKey='".$uniqueKey."' />
		</div>";

		return $layout;
	}

	/**
	 * 답변 추가 버튼 반환
	 * @author workingby <bumyul2000@godo.co.kr>
	 * @param string $displayType
	 * @return string $layoutBtn
	 * @date 2016-11-24
	 */
	private function getAnswerAddButton($displayType)
	{
		$layoutBtn = '';
		if($displayType === 't'){
			$layoutBtn = "<div><img src='../img/btn_answer_add2.png' border='0' class='hand guidedSelling-addAnswer guidedSelling-addAnswerType2' alt='답변추가' /></div>";
		}
		else {
			$layoutBtn = "<div><img src='../img/btn_answer_add1.png' border='0' class='hand guidedSelling-addAnswer guidedSelling-addAnswerType1' alt='답변추가' /></div>";
		}

		return $layoutBtn;
	}

	/**
	 * 질문 layout 반환
	 * @author workingby <bumyul2000@godo.co.kr>
	 * @param string $type
	 * @param string $uniqueKey
	 * @param array $value
	 * @return array $layoutArray
	 * @date 2016-11-24
	 */
	private function getQuestionLayout($type, $uniqueKey, $value=array())
	{
		$selected[$value['unit_displayType']] = "selected='selected'";
		$backgroundColor = $answerBackgroundImage = '';

		if($value['unit_displayType'] === 't'){
			if($value['unit_backgroundImage']){
				$answerBackgroundImage = $this->folderDir.$value['unit_backgroundImage'];
				$answerBackgroundImageStyle = "style='background-image:url(".$answerBackgroundImage."); background-size:950px'";
			}
			else {
				$answerBackgroundImageStyle = "style='background-image:url(\"../img/background_image_sample.png\"); background-size:950px'";
			}

			$selectAreaInfoMessage1 = '해시태그를 이용해 질문에 맞는 답변을 만들어주세요.';
			$selectAreaInfoMessage2 = '상품에 공통된 특징이 잘 나타난 배경 이미지와 해시태그를 등록해주세요. 배경은 PC쇼핑몰에만 적용됩니다';
		}
		else {
			$selectAreaInfoMessage1 = '해시태그를 이용해 질문에 맞는 답변을 만들어주세요. 이미지와 함께 등록 시 더욱 효과적입니다.';
			$selectAreaInfoMessage2 = '상품에 공통된 특징이 잘 나타난 이미지와 해시태그를 등록해주세요.';
		}

		if($value['guided_backgroundColor']){
			$backgroundColor = "style='background-color: #".$value['guided_backgroundColor'].";'";
		}

		$layoutArray = array();
		$layoutArray['top'] = "
			<div class='guidedSelling-questionArea' data-uniqueKey='".$uniqueKey."' data-no='".$value['unit_no']."' ".$backgroundColor.">
			<input type='hidden' name='question_no[".$uniqueKey."]' value='".$value['unit_no']."' />
				<div class='guidedSelling-subjectArea'>
					<input type='text' name=\"unit_question[".$uniqueKey."]\" class='questionSelector' border='0' placeholder='질문을 입력해주세요. ex) 찾으시는 스타일을 선택해주세요.' value='".$value['unit_question']."' />
				</div>

				<div class='guidedSelling_deleteArea'><img src='../img/btn_delete2.jpg' border='0' class='hand questionDeleteSelector' alt='삭제' /></div>

				<div class='guidedSelling-selectArea'>
					<div class='guidedSelling-selectAreaInfo1'>".$selectAreaInfoMessage1."</div>
					<select name=\"unit_displayType[".$uniqueKey."]\" class='displayTypeSelector'>
						<option value='i' ".$selected['i'].">= 이미지 + 텍스트 =</option>
						<option value='t' ".$selected['t'].">= 텍스트 =</option>
					</select>
					&nbsp;
					<span class='guidedSelling-selectAreaInfo2 extext'>".$selectAreaInfoMessage2."</span>
				</div>

				<div class='guidedSelling-itemArea' ".$answerBackgroundImageStyle.">
		";
		$layoutArray['bottom'] .= self::getAnswerAddButton($value['unit_displayType']);
		$layoutArray['bottom'] .= "
				</div>

				<div class='guidedSelling-infoArea extext'>
					<div>등록된 해시태그 목록은 <a href='./adm_goods_hashtag_list.php' target='_blank'>[해시태그 관리]</a>에서 확인하실 수 있습니다.</div>
					<div>해시태그에 등록된 상품 추가 및 삭제는 <a href='./adm_goods_manage_hashtag.php' target='_blank'>[빠른 해시태그 수정]</a>에서 하실 수 있습니다.</div>
				</div>
			</div>
		";

		return $layoutArray[$type];
	}

	/**
	 * 답변 layout 반환
	 * @author workingby <bumyul2000@godo.co.kr>
	 * @param array $data
	 * @param integer $answerSortNum
	 * @return array $answerLayout
	 * @date 2016-11-24
	 */
	private function getAnswerLayout($data, $answerSortNum)
	{

		if($data['unit_displayType'] === 'i'){
			$answerImage = ($data['detail_pcImage']) ? $this->folderDir.$data['detail_pcImage'] : "../img/guidedSelling_sample".$answerSortNum.".png";

			if($data['detail_pcImage']){
				$existImageValue = 'y';
			}
			else {
				$existImageValue = '';
			}

			//텍스트 + 이미지형
			$answerLayout = "
				<div class='guidedSellingItemSelector guidedSellingItem' data-no='".$data['detail_no']."'>
				<input type='hidden' name='answer_no[".$data['uniqueKey']."][]' value='".$data['detail_no']."' />
				<input type='hidden' name='existCheckImageInput[]' value='".$existImageValue."' />
					<div style='background-image:url(".$answerImage."); background-size:183px 183px;' class='guidedSelling_imageArea'>
						<div class='guidedSelling_imageBtn' data-uniqueKey='".$data['uniqueKey']."'><img src='../img/btn_image_save.png' border='0' class='hand' alt='이미지 저장' /></div>
						<div class='guidedSelling_answerDeleteArea'><img src='../img/btn_delete.png' class='answerDeleteSelector hand' border='0' alt='삭제' /></div>
						<div class='guidedSelling_hashtagNameArea'>#<input type='text' name=\"detail_hashtagName[".$data['uniqueKey']."][]\" class='hashtagInputListSearch' value='".$data['detail_hashtagName']."' placeholder='해시태그를 입력해 주세요.' /></div>
					</div>
					<div class='guidedSelling_confirmArea'><img src='../img/btn_goods_confirm2.png' border='0' class='hand guidedSelling_goodsConfirmSelector' alt='상품확인' /></div>
				</div>
			";
		}
		else if($data['unit_displayType'] === 't'){
			//텍스트형
			$answerLayout = "
				<div class='guidedSellingItemSelector guidedSellingItemText' data-no='".$data['detail_no']."'>
				<input type='hidden' name='answer_no[".$data['uniqueKey']."][]' value='".$data['detail_no']."' />
					<div class='guidedSellingTextArea'>
						<div class='guidedSelling_liArea'>○</div>
						<div class='guidedSelling_hashtagNameArea'>#<input type='text' name=\"detail_hashtagName[".$data['uniqueKey']."][]\" class='hashtagInputListSearch' value='".$data['detail_hashtagName']."' placeholder='해시태그를 입력해 주세요.' /></div>
						<div class='guidedSelling_answerDeleteArea'><img src='../img/btn_delete.png' class='answerDeleteSelector hand' border='0' alt='삭제' /></div>
					</div>
					<div class='guidedSelling_confirmArea'><img src='../img/btn_goods_confirm1.png' border='0' class='hand guidedSelling_goodsConfirmSelector' alt='상품확인' /></div>
				</div>
			";
		}
		else {
			$answerLayout = '';
		}

		return $answerLayout;
	}

	/**
	 * 고유키 생성 및 반환
	 * @author workingby <bumyul2000@godo.co.kr>
	 * @param void
	 * @return string $uniqueKey
	 * @date 2016-11-24
	 */
	private function getUniqueKey()
	{
		$uniqueKey = '';
		$randPrefix = mt_rand(0, mt_getrandmax());
		$uniqueKey = str_replace(".", "", uniqid($randPrefix, true));

		return $uniqueKey;
	}

	/**
	 * 임시 이미지 저장
	 * @author workingby <bumyul2000@godo.co.kr>
	 * @param array $postData
	 * @param array $fileData
	 * @return string
	 * @date 2016-11-24
	 */
	public function saveGuidedSellingTempImage($postData, $fileData)
	{
		try {
			if(is_file('../../lib/upload.lib.php')){
				unset($upload);
				include '../../lib/upload.lib.php';
			}

			$returnArray = array('result' => 'ok');

			self::setImageFolder($this->folderDir);
			self::setImageFolder($this->temp_folderDir);

			$imageNamePrefix = $postData['uniqueKey'].$postData['index'];

			// PC 이미지
			if($fileData['detail_pcImage']['tmp_name']){
				$uploadFileName = $this->temp_folderDir.self::setImageName('pc', $imageNamePrefix, $fileData['detail_pcImage']['name']);
				$upload = new upload_file($fileData['detail_pcImage'], $uploadFileName, 'image');
				if($upload->upload()){
					$returnArray['data'] = $uploadFileName;
				}
				else {
					throw new exception("UPLOAD를 실패 하였습니다.");
				}
				unset($upload);
			}
			// 모바일 이미지
			if($fileData['detail_mobileImage']['tmp_name']){
				$uploadFileName = $this->temp_folderDir.self::setImageName('mobile', $imageNamePrefix, $fileData['detail_mobileImage']['name']);
				$upload = new upload_file($fileData['detail_mobileImage'], $uploadFileName, 'image');
				if(!$upload->upload()){
					throw new exception("UPLOAD를 실패 하였습니다.");
				}
				unset($upload);
			}
			// 백그라운드 이미지
			if($fileData['unit_backgroundImage']['tmp_name']){
				$uploadFileName = $this->temp_folderDir.self::setImageName('pc', $postData['uniqueKey'], $fileData['unit_backgroundImage']['name']);
				$upload = new upload_file($fileData['unit_backgroundImage'], $uploadFileName, 'image');
				if($upload->upload()){
					$returnArray['data'] = $uploadFileName;
				}
				else {
					throw new exception("UPLOAD를 실패 하였습니다.");
				}
				unset($upload);
			}

			return $returnArray;
		}
		catch(Exception $e){
			$returnArray = array(
				'result' => 'fail',
				'data' => $e->getMessage(),
			);

			return $returnArray;
		}
	}

	/**
	 * 이미지 저장 폴더 셋팅
	 * @author workingby <bumyul2000@godo.co.kr>
	 * @param string $folderDir
	 * @return void
	 * @date 2016-11-24
	 */
	private function setImageFolder($folderDir)
	{
		if(!is_dir($folderDir)){
			@mkdir($folderDir);
			@chmod($folderDir, 0707);
		}
	}

	/**
	 * 이미지명 셋팅
	 * @author workingby <bumyul2000@godo.co.kr>
	 * @param string $type
	 * @param string $uniqueKey
	 * @param string $name
	 * @return string $imageName
	 * @date 2016-11-24
	 */
	private function setImageName($type, $uniqueKey, $name)
	{
		$imageName = '';
		$fileType = end(explode('.', $name));
		$fileName = str_replace(".", "", $uniqueKey);
		$imageName = $fileName.'_'.$type.'.'.$fileType;

		return $imageName;
	}

	/**
	 * 임시 이미지 삭제
	 * @author workingby <bumyul2000@godo.co.kr>
	 * @param void
	 * @return void
	 * @date 2016-11-24
	 */
	private function resetTempImage()
	{
		$tempFilePath = dirname(__FILE__).'/'.substr($this->temp_folderDir, 3)."*";
		foreach(glob($tempFilePath) as $tempFileName){
			if(preg_match('/\/data\/guidedSelling\/temp/', $tempFileName)){
				@unlink($tempFileName);
			}
		}
	}

	/**
	 * 가이드 셀링 삭제
	 * @author workingby <bumyul2000@godo.co.kr>
	 * @param string $type
	 * @param integer $no
	 * @return string
	 * @date 2016-11-24
	 */
	public function deleteGuidedSelling($type, $no)
	{
		try {
			//삭제될 이미지명
			$deleteImageArray = array();
			$deleteImageArray = self::getDeleteImageArray($type, $no);

			switch($type){
				case 'all':
					$query = "
						DELETE a, b, c FROM
							".GD_GUIDEDSELLING." AS a
						LEFT JOIN
							".GD_GUIDEDSELLING_UNIT." AS b
						ON
							a.guided_no=b.unit_guidedNo
						LEFT JOIN
							".GD_GUIDEDSELLING_UNITDETAIL." AS c
						ON
							b.unit_no=c.detail_unitNo
						WHERE
							a.guided_no=".$no."
					";
				break;

				case 'unit':
					$query = "
						DELETE a, b FROM
							".GD_GUIDEDSELLING_UNIT." AS a
						LEFT JOIN
							".GD_GUIDEDSELLING_UNITDETAIL." AS b
						ON
							a.unit_no=b.detail_unitNo
						WHERE
							a.unit_no=".$no."
					";
				break;

				case 'detail':
					$query = "DELETE FROM ".GD_GUIDEDSELLING_UNITDETAIL." WHERE detail_no=".$no;
				break;
			}

			$res = $this->db->query($query);
			if(!$res){
				throw new exception("삭제를 실패하였습니다.");
			}

			//이미지 삭제
			if(count($deleteImageArray) > 0){
				self::deleteImage($deleteImageArray);
			}

			return '';
		}
		catch(Exception $e){
			return $e->getMessage();
		}
	}

	/**
	 * 삭제될 이미지명 반환
	 * @author workingby <bumyul2000@godo.co.kr>
	 * @param string $type
	 * @param integer $no
	 * @return array $deleteImageArray
	 * @date 2016-11-24
	 */
	private function getDeleteImageArray($type, $no)
	{
		$deleteImageArray = array();
		switch($type){
			case 'all' : case 'unit':
				$where = '';
				if($type === 'all'){
					$where = "unit_guidedNo=".$no;
				}
				else {
					$where = "no=".$no;
				}
				$resUnit = $this->db->query("SELECT unit_no, unit_backgroundImage FROM ".GD_GUIDEDSELLING_UNIT." WHERE ".$where);
				if($resUnit){
					while($unit = $this->db->fetch($resUnit, 1)){
						if($unit['unit_backgroundImage']){
							$deleteImageArray[] = $unit['unit_backgroundImage'];
						}
						$resDetail = $this->db->query("SELECT detail_pcImage, detail_mobileImage FROM ".GD_GUIDEDSELLING_UNITDETAIL." WHERE detail_unitNo=".$unit['unit_no']." AND (detail_pcImage or detail_mobileImage)");
						if($resDetail){
							while($detail = $this->db->fetch($resDetail, 1)){
								if($detail['detail_pcImage']) $deleteImageArray[] = $detail['detail_pcImage'];
								if($detail['detail_mobileImage']) $deleteImageArray[] = $detail['detail_mobileImage'];
							}
						}
					}
				}
			break;

			case 'detail':
				$resDetail = $this->db->query("SELECT detail_pcImage, detail_mobileImage FROM ".GD_GUIDEDSELLING_UNITDETAIL." WHERE detail_no=".$no." AND (detail_pcImage or detail_mobileImage)");
				if($resDetail){
					while($detail = $this->db->fetch($resDetail, 1)){
						if($detail['detail_pcImage']) $deleteImageArray[] = $detail['detail_pcImage'];
						if($detail['detail_mobileImage']) $deleteImageArray[] = $detail['detail_mobileImage'];
					}
				}
			break;
		}

		return $deleteImageArray;
	}

	/**
	 * 이미지 삭제
	 * @author workingby <bumyul2000@godo.co.kr>
	 * @param array $deleteImageArray
	 * @return void
	 * @date 2016-11-24
	 */
	private function deleteImage($deleteImageArray)
	{
		if(count($deleteImageArray) > 0){
			foreach($deleteImageArray as $imageName){
				$deleteFilePath = $this->folderDir.$imageName;
				if($imageName && is_file($deleteFilePath)){
					unlink($deleteFilePath);
				}
			}
		}
	}

	/**
	 * 가이드 셀링 데이터 반환
	 * @author workingby <bumyul2000@godo.co.kr>
	 * @param integer $guided_no
	 * @return array $data
	 * @date 2016-11-24
	 */
	public function getGuidedSellingData($guided_no)
	{
		$data = array();
		list($data) = $this->db->_select("SELECT * FROM ".GD_GUIDEDSELLING." WHERE guided_no=".$guided_no." LIMIT 1");

		return $data;
	}

	/**
	 * 가이드 셀링 질문 데이터 반환
	 * @author workingby <bumyul2000@godo.co.kr>
	 * @param integer $guided_no
	 * @param integer $unit_step
	 * @return array $unitData
	 * @date 2016-11-24
	 */
	public function getGuidedSellingUnitData($guided_no, $unit_step = 0)
	{
		global $cfg;

		$addWhereQuery = '';
		if((int)$unit_step > 0){
			$addWhereQuery = " AND unit_step=".$unit_step;
		}

		$unitData = array();
		$query = "SELECT * FROM ".GD_GUIDEDSELLING_UNIT." WHERE unit_guidedNo = ".$guided_no.$addWhereQuery." ORDER BY unit_step ASC LIMIT 1";
		$res = $this->db->query($query);
		if($res){
			while($data = $this->db->fetch($res, 1)){
				if($data['unit_backgroundImage']){
					$data['backgroundImageUrl'] = $cfg['rootDir']."/data/guidedSelling/".$data['unit_backgroundImage'];
				}

				$unitData = $data;
			}
		}

		return $unitData;

	}

	/**
	 * 가이드 셀링 답변 데이터 반환
	 * @author workingby <bumyul2000@godo.co.kr>
	 * @param integer $unit_no
	 * @return array $detailData
	 * @date 2016-11-24
	 */
	public function getGuidedSellingDetailData($unit_no)
	{
		global $cfg;

		$detailData = array();
		$query = "SELECT * FROM ".GD_GUIDEDSELLING_UNITDETAIL." WHERE detail_unitNo = ".$unit_no." ORDER BY detail_no ASC";
		$res = $this->db->query($query);
		if($res){
			$index = 0;
			while($data = $this->db->fetch($res, 1)){
				if($data['detail_pcImage']){
					$data['pcImageUrl'] = $cfg['rootDir']."/data/guidedSelling/".$data['detail_pcImage'];
				}

				if($data['detail_mobileImage']){
					$data['mobileImageUrl'] = $cfg['rootDir']."/data/guidedSelling/".$data['detail_mobileImage'];
				}
				else {
					$data['mobileImageUrl'] = $cfg['rootDir']."/data/guidedSelling/".$data['detail_pcImage'];
				}

				$detailData[] = $data;

				$index++;
			}
		}

		return $detailData;
	}

	/**
	 * 마지막 STEP 확인
	 * @author workingby <bumyul2000@godo.co.kr>
	 * @param integer $guided_no
	 * @return integer $lastStep
	 * @date 2016-11-24
	 */
	public function getLastStep($guided_no)
	{
		$lastStep = 0;
		list($lastStep) = $this->db->fetch("SELECT MAX(unit_step) FROM ".GD_GUIDEDSELLING_UNIT." WHERE unit_guidedNo=".$guided_no);

		return $lastStep;
	}
}
?>