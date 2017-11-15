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
	//�̹��� ���� ����
	private $folderDir = '../../data/guidedSelling/';
	//�̹��� �ӽ� ���� ����
	private	$temp_folderDir = '../../data/guidedSelling/temp/';

	function __construct()
	{
		global $db;

		$this->db = $db;
	}

	/**
	 * �Ķ���� üũ
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
					throw new exception("�ùٸ� ������ �ƴմϴ�.");
				}
			}
			if(!trim($postData['guided_subject'])){
				throw new exception("���̵� ���� �̸��� �Է��� �ּ���.");
			}
			if(!trim($postData['guided_backgroundColor'])){
				throw new exception("������ �Է��Ͽ� �ּ���.");
			}
			foreach($postData['unit_question'] as $key => $questionName){
				if(!trim($questionName)){
					throw new exception("������ �Է��� �ּ���.");
					break;
				}
				foreach($postData['detail_hashtagName'][$key] as $hashtagName){
					if(!trim($hashtagName)){
						throw new exception("�ؽ��±׸� �Է��� �ּ���.");
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
	 * submit �� ������ üũ
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

			//�ؽ��±� ���� üũ
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
	 * data ����
	 * @author workingby <bumyul2000@godo.co.kr>
	 * @param array $postData
	 * @param array $fileData
	 * @return string
	 * @date 2016-11-24
	 */
	public function saveGuidedSelling($postData, $fileData = array())
	{
		try {
			//�ʼ��� üũ
			$errorMessage = '';
			$errorMessage = self::checkRequiredParameter($postData);
			if($errorMessage !== ''){
				throw new exception($errorMessage);
			}

			//�ؽ��±� ���� üũ
			$errorMessage = self::checkHashtagValidation($postData['detail_hashtagName']);
			if($errorMessage !== ''){
				throw new exception($errorMessage);
			}

			if(count($postData['existCheckImageInput']) > 0){
				foreach($postData['existCheckImageInput'] as $value){
					if($value !== 'y'){
						throw new exception("�亯 �̹����� ����� �ּ���.");
					}
				}
			}

			$errorMessage = self::insertGuidedSelling($postData);
			if($errorMessage !== ''){
				throw new exception("DB��ġ�� Ȯ���� �ּ���.");
			}

			if($this->guidedSellingNo){
				$errorMessage = self::saveGuidedSellingUnit($postData, $fileData);
				if($errorMessage !== ''){
					throw new exception("DB��ġ�� Ȯ���� �ּ���.");
				}
			}

			//�ӽ� �̹��� ���� ����
			self::resetTempImage();

			return '';
		}
		catch(Exception $e){
			return $e->getMessage();
		}
	}

	/**
	 * �ؽ��±� ��ȿ�� üũ
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
				throw new exception("�ؽ��±� ��ġ�� ������ �ּ���.");
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
								throw new exception("�������� �ʴ� �ؽ��±װ� ���ԵǾ� �ֽ��ϴ�.");
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
	 * �ؽ��±� ��ȿ�� üũ - ��ǰȮ�ν�
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
				throw new exception("�ؽ��±� ��ġ�� ������ �ּ���.");
			}

			$hashtag = $hashtagObj->setHashtag($hashtag);
			$checkHashtag = $hashtagObj->checkHashtag($hashtag);
			if($checkHashtag === true){
				throw new exception("�Էµ� �ؽ��±װ� ��ϵ��� �ʾҽ��ϴ�.\n�ٽ� Ȯ�����ּ���.");
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
	 * data ����
	 * @author workingby <bumyul2000@godo.co.kr>
	 * @param array $postData
	 * @param array $fileData
	 * @return string
	 * @date 2016-11-24
	 */
	public function modifyGuidedSelling($postData, $fileData = array())
	{
		try {
			//�ʼ��� üũ
			$errorMessage = '';
			$errorMessage = self::checkRequiredParameter($postData);
			if($errorMessage !== ''){
				throw new exception($errorMessage);
			}

			//�ؽ��±� ���� üũ
			$errorMessage = self::checkHashtagValidation($postData['detail_hashtagName']);
			if($errorMessage !== ''){
				throw new exception($errorMessage);
			}

			$res = $this->db->query("UPDATE ".GD_GUIDEDSELLING." SET guided_subject='".$postData['guided_subject']."', guided_backgroundColor='".$postData['guided_backgroundColor']."' WHERE guided_no=".$postData['guided_no']);
			if(!$res){
				throw new exception("������ �����Ͽ����ϴ�.");
			}

			//�亯 ����
			if(count($postData['answer_deleteNo']) > 0){
				foreach($postData['answer_deleteNo'] as $detailNo){
					$errorMessage = self::deleteGuidedSelling('detail', $detailNo);
					if($errorMessage !== ''){
						throw new exception($errorMessage);
					}
				}
			}
			//���� & �亯 ����
			if(count($postData['question_deleteNo']) > 0){
				foreach($postData['question_deleteNo'] as $unitNo){
					$errorMessage = self::deleteGuidedSelling('unit', $unitNo);
					if($errorMessage !== ''){
						throw new exception($errorMessage);
					}
				}
			}
			$this->guidedSellingNo = $postData['guided_no'];

			//data ����
			$errorMessage = self::saveGuidedSellingUnit($postData, $fileData);
			if($errorMessage !== ''){
				throw new exception("DB��ġ�� Ȯ���� �ּ���.");
			}

			//�ӽ� �̹��� ���� ����
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
				throw new exception("GUIDEDSELLING INSERT ����");
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

				//����
				if($guidedSellingUnitNo > 0 ){
					$mode = "modify";

					//���� �������� ����
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
				//����
				else {
					$mode = "write";

					//���� ��ϵǴ� ����
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
					throw new exception("GUIDEDSELLING UNIT SAVE ����");
				}

				//�̹��� ������ ó��, ����
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
					throw new exception("GUIDEDSELLING UNIT DETAIL SAVE ����");
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
				throw new exception("�ؽ��±� ��ġ�� ������ �ּ���.");
			}

			foreach($data['answer_no'][$uniqueKey] as $index => $answer_no){
				$detail_hashtagName = '';
				$detail_hashtagName = $hashtagObj->setHashtag($data['detail_hashtagName'][$uniqueKey][$index]);

				if($answer_no > 0){
					$mode = "modify";
					$queryDetail = "UPDATE ".GD_GUIDEDSELLING_UNITDETAIL." SET detail_hashtagName='".$detail_hashtagName."' WHERE detail_no=".$answer_no;
					$resDetail = $this->db->query($queryDetail);
					if(!$resDetail){
						throw new exception("GUIDEDSELLING UNIT DETAIL UPDATE ����");
					}
					$detailInsertId = $answer_no;
				}
				else {
					$mode = "write";
					$queryDetail = "INSERT INTO ".GD_GUIDEDSELLING_UNITDETAIL." (detail_unitNo, detail_hashtagName) VALUES ('".$unitNoInsertId."', '".$detail_hashtagName."')";
					$resDetail = $this->db->query($queryDetail);
					if(!$resDetail){
						throw new exception("GUIDEDSELLING UNIT DETAIL INSERT ����");
					}
					$detailInsertId = $this->db->_last_insert_id();
				}

				//�̹��� ������ ó��, ����
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
	 * �̹��� ���� & ���� ����
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
				//��׶��� �̹��� ����
				$searchPathFile = dirname(__FILE__).'/'.$temp_folderDir.$parameter['uniqueKey']."_*";
			}
			else if($parameter['type'] === 'detail'){
				//�亯�̹��� ����
				$searchPathFile = dirname(__FILE__).'/'.$temp_folderDir.$parameter['uniqueKey'].$parameter['index']."_*";
			}
			else {
				throw new exception("�̹��� ������Ʈ ����");
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
					throw new exception("FILE COPY ����.");
				}
			}

			if($parameter['type'] === 'unit'){
				$backgroundImageDataUpdate = false;

				//���� ����� �̹����� ���� �� ������Ʈ
				if($pcImageName){
					$backgroundImageDataUpdate = true;
				}
				//���÷��� ü������ �־��ٸ� ������Ʈ
				if($parameter['unit_backgroundImageDeleteNo'] === 'y' || $pcImageName) {
					self::deleteBackgroundImage($parameter['insertID']);
					$backgroundImageDataUpdate = true;
				}
				if($backgroundImageDataUpdate === true){
					$updateQuery = "UPDATE ".GD_GUIDEDSELLING_UNIT." SET unit_backgroundImage='".$pcImageName."' WHERE unit_no = ".$parameter['insertID'];
					$res = $this->db->query($updateQuery);
					if(!$res){
						throw new exception("�̹��� ������Ʈ ����");
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
						throw new exception("�̹��� ������Ʈ ����");
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
	 * �亯 �̹��� ����
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
	 * ���ȭ�� �̹��� ����
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
	 * ���� UI �߰�
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
					$postData['uniqueKey'] = self::getUniqueKey(); //����Ű �ο�

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

						//����Ű �ο�
						$uniqueKey = self::getUniqueKey();

						//ui ���
						$questionLayout[$index] = self::getQuestionLayout('top', $uniqueKey, $unit);

						//��׶��� �̹��� ��ư ����
						if($unit['unit_displayType'] === 't'){
							$questionLayout[$index] .= self::getBackgroundImageBtn($uniqueKey);
						}

						//�亯 ui
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

						//ui �ϴ�
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
	 * �亯 ��� �߰�
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
				throw new exception("�亯 ��� �߰��� �����Ͽ����ϴ�.");
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
	 * ���� ���÷��� ���� ��ȯ�� ���� ���̾ƿ� �߰�
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
	 * ��׶��� �̹��� ���� ��ư
	 * @author workingby <bumyul2000@godo.co.kr>
	 * @param string $uniqueKey
	 * @return string $layout
	 * @date 2016-11-24
	 */
	private function getBackgroundImageBtn($uniqueKey)
	{
		$layout = "
		<div class='guidedSelling-backgroundImageBtnArea'>
			<img src='../img/btn_background_save.png' class='guidedSelling_backgroundImageSaveBtn hand' border='0' alt='��׶��� �̹��� ���' data-uniqueKey='".$uniqueKey."' />
		</div>";

		return $layout;
	}

	/**
	 * �亯 �߰� ��ư ��ȯ
	 * @author workingby <bumyul2000@godo.co.kr>
	 * @param string $displayType
	 * @return string $layoutBtn
	 * @date 2016-11-24
	 */
	private function getAnswerAddButton($displayType)
	{
		$layoutBtn = '';
		if($displayType === 't'){
			$layoutBtn = "<div><img src='../img/btn_answer_add2.png' border='0' class='hand guidedSelling-addAnswer guidedSelling-addAnswerType2' alt='�亯�߰�' /></div>";
		}
		else {
			$layoutBtn = "<div><img src='../img/btn_answer_add1.png' border='0' class='hand guidedSelling-addAnswer guidedSelling-addAnswerType1' alt='�亯�߰�' /></div>";
		}

		return $layoutBtn;
	}

	/**
	 * ���� layout ��ȯ
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

			$selectAreaInfoMessage1 = '�ؽ��±׸� �̿��� ������ �´� �亯�� ������ּ���.';
			$selectAreaInfoMessage2 = '��ǰ�� ����� Ư¡�� �� ��Ÿ�� ��� �̹����� �ؽ��±׸� ������ּ���. ����� PC���θ����� ����˴ϴ�';
		}
		else {
			$selectAreaInfoMessage1 = '�ؽ��±׸� �̿��� ������ �´� �亯�� ������ּ���. �̹����� �Բ� ��� �� ���� ȿ�����Դϴ�.';
			$selectAreaInfoMessage2 = '��ǰ�� ����� Ư¡�� �� ��Ÿ�� �̹����� �ؽ��±׸� ������ּ���.';
		}

		if($value['guided_backgroundColor']){
			$backgroundColor = "style='background-color: #".$value['guided_backgroundColor'].";'";
		}

		$layoutArray = array();
		$layoutArray['top'] = "
			<div class='guidedSelling-questionArea' data-uniqueKey='".$uniqueKey."' data-no='".$value['unit_no']."' ".$backgroundColor.">
			<input type='hidden' name='question_no[".$uniqueKey."]' value='".$value['unit_no']."' />
				<div class='guidedSelling-subjectArea'>
					<input type='text' name=\"unit_question[".$uniqueKey."]\" class='questionSelector' border='0' placeholder='������ �Է����ּ���. ex) ã���ô� ��Ÿ���� �������ּ���.' value='".$value['unit_question']."' />
				</div>

				<div class='guidedSelling_deleteArea'><img src='../img/btn_delete2.jpg' border='0' class='hand questionDeleteSelector' alt='����' /></div>

				<div class='guidedSelling-selectArea'>
					<div class='guidedSelling-selectAreaInfo1'>".$selectAreaInfoMessage1."</div>
					<select name=\"unit_displayType[".$uniqueKey."]\" class='displayTypeSelector'>
						<option value='i' ".$selected['i'].">= �̹��� + �ؽ�Ʈ =</option>
						<option value='t' ".$selected['t'].">= �ؽ�Ʈ =</option>
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
					<div>��ϵ� �ؽ��±� ����� <a href='./adm_goods_hashtag_list.php' target='_blank'>[�ؽ��±� ����]</a>���� Ȯ���Ͻ� �� �ֽ��ϴ�.</div>
					<div>�ؽ��±׿� ��ϵ� ��ǰ �߰� �� ������ <a href='./adm_goods_manage_hashtag.php' target='_blank'>[���� �ؽ��±� ����]</a>���� �Ͻ� �� �ֽ��ϴ�.</div>
				</div>
			</div>
		";

		return $layoutArray[$type];
	}

	/**
	 * �亯 layout ��ȯ
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

			//�ؽ�Ʈ + �̹�����
			$answerLayout = "
				<div class='guidedSellingItemSelector guidedSellingItem' data-no='".$data['detail_no']."'>
				<input type='hidden' name='answer_no[".$data['uniqueKey']."][]' value='".$data['detail_no']."' />
				<input type='hidden' name='existCheckImageInput[]' value='".$existImageValue."' />
					<div style='background-image:url(".$answerImage."); background-size:183px 183px;' class='guidedSelling_imageArea'>
						<div class='guidedSelling_imageBtn' data-uniqueKey='".$data['uniqueKey']."'><img src='../img/btn_image_save.png' border='0' class='hand' alt='�̹��� ����' /></div>
						<div class='guidedSelling_answerDeleteArea'><img src='../img/btn_delete.png' class='answerDeleteSelector hand' border='0' alt='����' /></div>
						<div class='guidedSelling_hashtagNameArea'>#<input type='text' name=\"detail_hashtagName[".$data['uniqueKey']."][]\" class='hashtagInputListSearch' value='".$data['detail_hashtagName']."' placeholder='�ؽ��±׸� �Է��� �ּ���.' /></div>
					</div>
					<div class='guidedSelling_confirmArea'><img src='../img/btn_goods_confirm2.png' border='0' class='hand guidedSelling_goodsConfirmSelector' alt='��ǰȮ��' /></div>
				</div>
			";
		}
		else if($data['unit_displayType'] === 't'){
			//�ؽ�Ʈ��
			$answerLayout = "
				<div class='guidedSellingItemSelector guidedSellingItemText' data-no='".$data['detail_no']."'>
				<input type='hidden' name='answer_no[".$data['uniqueKey']."][]' value='".$data['detail_no']."' />
					<div class='guidedSellingTextArea'>
						<div class='guidedSelling_liArea'>��</div>
						<div class='guidedSelling_hashtagNameArea'>#<input type='text' name=\"detail_hashtagName[".$data['uniqueKey']."][]\" class='hashtagInputListSearch' value='".$data['detail_hashtagName']."' placeholder='�ؽ��±׸� �Է��� �ּ���.' /></div>
						<div class='guidedSelling_answerDeleteArea'><img src='../img/btn_delete.png' class='answerDeleteSelector hand' border='0' alt='����' /></div>
					</div>
					<div class='guidedSelling_confirmArea'><img src='../img/btn_goods_confirm1.png' border='0' class='hand guidedSelling_goodsConfirmSelector' alt='��ǰȮ��' /></div>
				</div>
			";
		}
		else {
			$answerLayout = '';
		}

		return $answerLayout;
	}

	/**
	 * ����Ű ���� �� ��ȯ
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
	 * �ӽ� �̹��� ����
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

			// PC �̹���
			if($fileData['detail_pcImage']['tmp_name']){
				$uploadFileName = $this->temp_folderDir.self::setImageName('pc', $imageNamePrefix, $fileData['detail_pcImage']['name']);
				$upload = new upload_file($fileData['detail_pcImage'], $uploadFileName, 'image');
				if($upload->upload()){
					$returnArray['data'] = $uploadFileName;
				}
				else {
					throw new exception("UPLOAD�� ���� �Ͽ����ϴ�.");
				}
				unset($upload);
			}
			// ����� �̹���
			if($fileData['detail_mobileImage']['tmp_name']){
				$uploadFileName = $this->temp_folderDir.self::setImageName('mobile', $imageNamePrefix, $fileData['detail_mobileImage']['name']);
				$upload = new upload_file($fileData['detail_mobileImage'], $uploadFileName, 'image');
				if(!$upload->upload()){
					throw new exception("UPLOAD�� ���� �Ͽ����ϴ�.");
				}
				unset($upload);
			}
			// ��׶��� �̹���
			if($fileData['unit_backgroundImage']['tmp_name']){
				$uploadFileName = $this->temp_folderDir.self::setImageName('pc', $postData['uniqueKey'], $fileData['unit_backgroundImage']['name']);
				$upload = new upload_file($fileData['unit_backgroundImage'], $uploadFileName, 'image');
				if($upload->upload()){
					$returnArray['data'] = $uploadFileName;
				}
				else {
					throw new exception("UPLOAD�� ���� �Ͽ����ϴ�.");
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
	 * �̹��� ���� ���� ����
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
	 * �̹����� ����
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
	 * �ӽ� �̹��� ����
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
	 * ���̵� ���� ����
	 * @author workingby <bumyul2000@godo.co.kr>
	 * @param string $type
	 * @param integer $no
	 * @return string
	 * @date 2016-11-24
	 */
	public function deleteGuidedSelling($type, $no)
	{
		try {
			//������ �̹�����
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
				throw new exception("������ �����Ͽ����ϴ�.");
			}

			//�̹��� ����
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
	 * ������ �̹����� ��ȯ
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
	 * �̹��� ����
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
	 * ���̵� ���� ������ ��ȯ
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
	 * ���̵� ���� ���� ������ ��ȯ
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
	 * ���̵� ���� �亯 ������ ��ȯ
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
	 * ������ STEP Ȯ��
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