<?php
/**
 * 멀티팝업 class
 * @author cjb3333 , artherot @ godosoft development team.
 */
class MultiPopup
{
	private $db;
	private $now;
	private $imgField = array(
		'mouseOnImg'=>'N',
		'mouseOutImg'=>'T',
		'mainBannerImg'=>'B',
	);
	private $data_path;
	private $tmp_data_path;
	private $env_code;

	/**
	 * 생성자
	 */
	public function __construct()
	{
		$this->db				= Core::loader('db');
		$this->data_path		= dirname(__FILE__).'/../data/multipopup/';
		$this->tmp_data_path	= dirname(__FILE__).'/../data/tmp_skinCopy/';
		$this->now				= time();
		$this->env_code			= 'multiPopup';
	}

	/**
	 * 멀티 팝업 코드 생성
	 * @return string 멀티 팝업 코드 (UNIXTIME)
	 */
	public function getNewCode()
	{
		$newCode	= $this->now;

		return $newCode;
	}

	/**
	 * 멀티 팝업 정보 확인
	 * @param integer $code 멀티 팝업 코드
	 * @return array 멀티 팝업 정보
	 */
	public function getPopupData($code)
	{
		$query		= "SELECT * FROM ".GD_ENV." WHERE category = '".$this->env_code."' AND name = '".$code."'";
		$popupData	= $this->db->fetch($query);

		return $popupData;
	}

	/**
	 * 멀티 팝업 정보 리스트
	 * @return array 멀티 팝업 정보 리스트
	 */
	public function getPopupList()
	{
		$query	= "SELECT * FROM ".GD_ENV." WHERE category = '".$this->env_code."' ORDER BY name";
		$result	= $this->db->query($query);

		// 정보를 json 처리를 함
		while($data = $this->db->fetch($result)) {
			$value[] = gd_json_decode(stripslashes($data[value]));
		}

		return $value;
	}

	/**
	 * POST 정보를 가공 처리 (저장 및 수정을 하기 위한 정보)
	 * @return array 가공한 정보
	 */
	private function postDataProcessing()
	{
		// 특정기간동안 팝업창 열림
		if($_POST['popup_dt2tm'] == 'Y'){
			$_POST['popup_sdt']		= $_POST['popup_sdt_tg'];
			$_POST['popup_edt']		= $_POST['popup_edt_tg'];
			$_POST['popup_stime']	= $_POST['popup_stime_tg_h'].$_POST['popup_stime_tg_m'];
			$_POST['popup_etime']	= $_POST['popup_etime_tg_h'].$_POST['popup_etime_tg_m'];
		}
		// 특정기간동안 특정시간에만 팝업창 열림
		else if($_POST['popup_dt2tm'] == 'T'){
			$_POST['popup_stime']	= $_POST['popup_stime_h'].$_POST['popup_stime_m'];
			$_POST['popup_etime']	= $_POST['popup_etime_h'].$_POST['popup_etime_m'];
		}
		// 항상 팝업창 열림
		else {
			$_POST['popup_dt2tm']	= 'N';
			unset($_POST['popup_sdt'], $_POST['popup_edt'], $_POST['popup_stime'], $_POST['popup_etime']);
		}

		// 기간별 노출 설정에 일자가 없으면 항상 팝업창 열림으로 처리
		if (empty($_POST['popup_sdt']) || empty($_POST['popup_edt'])) {
			$_POST['popup_dt2tm']	= 'N';
			unset($_POST['popup_sdt'], $_POST['popup_edt'], $_POST['popup_stime'], $_POST['popup_etime']);
		}

		// 필요 없는 데이타 삭제
		unset($_POST['popup_sdt_tg'], $_POST['popup_edt_tg']);
		unset($_POST['popup_stime_tg_h'], $_POST['popup_stime_tg_m'], $_POST['popup_etime_tg_h'], $_POST['popup_etime_tg_m']);
		unset($_POST['popup_stime_h'], $_POST['popup_stime_m'], $_POST['popup_etime_h'], $_POST['popup_etime_m']);

		// 저장할 데이타
		$popupConf = array();
		foreach ( $_POST as $k => $v ){
			$popupConf[$k] = $v;
		}

		$_mainBannerImg[0]	= 0;
		$_mainBannerImg[1]	= 0;
		$_mouseOutImg[0]	= 0;
		$_mouseOutImg[1]	= 0;

		//temp 폴더에 있는 이미지 복사
		foreach($popupConf as $key => $value)
		{
			if(array_key_exists($key, $this->imgField))
			{
				// 이미지 개수
				$tmp		= explode('_', $popupConf['displaySet']);
				$row		= $tmp[0];
				$col		= $tmp[1];
				$imgCount	= $row * $col;

				for($k = 1; $k <= $imgCount; $k++)
				{
					$image_name	= $popupConf[$key][$k];

					// 해당 이미지가 있는지 체크
					if (!preg_match('/^http(s)?:\/\/.+$/', $image_name))
					{
						// temp폴더에 이미지가 있다면 새로 등록한 이미지
						if (is_file($this->tmp_data_path.$image_name))
						{
							// 멀티이미지 팝업용 폴더 체크
							if(!is_dir($this->data_path))
							{
								mkdir($this->data_path);
								chmod($this->data_path,0777);
							}

							// 이미지 이름변경
							$newImgNm	= str_replace('tmp_','ori_',$image_name);

							// 등록시 이미지 리사이징 부분
							if($key == 'mainBannerImg'){
								thumbnail($this->tmp_data_path.$image_name,$this->data_path.$newImgNm,$popupConf['mainImgSizew'], $popupConf['mainImgSizeh'],1);
							}else{
								thumbnail($this->tmp_data_path.$image_name,$this->data_path.$newImgNm,$popupConf['mouseImgSizew'], $popupConf['mouseImgSizeh'],1);
							}

							$_key	= preg_replace('/^[a-z]_/','',$key);
							@unlink( $this->tmp_data_path.$image_name );					// temp폴더 파일 삭제
							@unlink( $this->data_path.$popupConf['prev_'.$_key][$k] );	// 기존에 등록한 이미지 삭제

							// 이미지 이름을 새로운 이미지 명으로 대체
							$image_name				= $newImgNm;
							$popupConf[$key][$k]	= $newImgNm;

						}
						// 기존에 올라가 이미지가 있는 경우
						else if(is_file($this->data_path.$image_name)){

							// 수정시 이미지 리사이징 부분
							if($key == 'mainBannerImg'){
								thumbnail($this->data_path.$image_name,$this->data_path.$image_name,$popupConf['mainImgSizew'], $popupConf['mainImgSizeh'],1);
							}else{
								thumbnail($this->data_path.$image_name,$this->data_path.$image_name,$popupConf['mouseImgSizew'], $popupConf['mouseImgSizeh'],1);
							}
						}
					}

					// 이미지중에 가장 큰 이미지를 창크기로 계산
					$imgSize	= @getimagesize($this->data_path.$image_name);

					if($key == 'mainBannerImg') {
						if($imgSize[0] > $_mainBannerImg[0] ) $_mainBannerImg[0] = $imgSize[0];
						if($imgSize[1] > $_mainBannerImg[1] ) $_mainBannerImg[1] = $imgSize[1];

					}
					else {
						if($imgSize[0] > $_mouseOutImg[0] ) $_mouseOutImg[0] = $imgSize[0];
						if($imgSize[1] > $_mouseOutImg[1] ) $_mouseOutImg[1] = $imgSize[1];
					}
				}
			}
		}

		// 오늘 하루 보지않음 높이
		if($popupConf['popup_invisible'] == 'Y') {
			$popupInvisibleHeight	= 20;
		}

		if($_mainBannerImg[0] < ($_mouseOutImg[0] * $row) ){
			$popup_sizew	= floor(($_mouseOutImg[0] * $row)+($popupConf['outlinePadding']*2));	//버튼가로 + 가로패팅
			$popup_sizeh	= floor(($_mainBannerImg[1]+($_mouseOutImg[1]*$col))+$popupInvisibleHeight+($popupConf['outlinePadding']*2)+$popupConf['outlinePadding']);	//메인배너세로 + 오버이미지높이 + 오늘하루보임높이 + 세로패팅
		}else{
			$popup_sizew	= floor($_mainBannerImg[0]+($popupConf['outlinePadding']*2));			//메인배너가로 + 가로패팅
			$popup_sizeh	= floor(($_mainBannerImg[1]+($_mouseOutImg[1]*$col))+$popupInvisibleHeight+($popupConf['outlinePadding']*2)+$popupConf['outlinePadding']);	//메인배너세로 + 오버이미지높이 + 오늘하루보임높이 + 세로패팅
		}

		// 창사이즈설정
		$popupConf['popup_sizew']	= $popup_sizew;
		$popupConf['popup_sizeh']	= $popup_sizeh;

		// 이미지 호스팅 이미지가 있는지 확인하여 있다면 설정된 크기로 창크기 재설정
		foreach($popupConf['image_attach_method'] as $image_attach_method){
			if($image_attach_method == 'url'){
				$popupConf['popup_sizew'] = $_POST['popup_sizew'];
				$popupConf['popup_sizeh'] = $_POST['popup_sizeh'];
				break;
			}
		}

		return $popupConf;
	}

	/**
	 * 멀티 팝업 정보 저장
	 * @param integer $code 멀티 팝업 코드
	 */
	public function popupRegister($code)
	{
		// 저장할 정보 가공
		$postData		= $this->postDataProcessing();

		// json 처리
		if (empty($postData) === false) {
			$popupJsonData	= gd_json_encode($postData);
		} else {
			msg("멀티 팝업 정보에 이상이 있어서 저장에 오류가 발생이 되었습니다.");
			return false;
		}

		// 멀티 팝업 정보 저장
		$query	= "INSERT INTO ".GD_ENV." SET category = '".$this->env_code."', name = '".$code."' , value = '".addslashes($popupJsonData)."'";
		$res	= $this->db->query($query);
		if($res) {
			msg("[".$postData['text']."] 멀티 팝업 창이 저장 되었습니다.");
			return true;
		} else {
			msg("[".$postData['text']."] 멀티 팝업 창 저장에 오류가 발생이 되었습니다.");
			return false;
		}
	}

	/**
	 * 멀티 팝업 정보 수정
	 * @param integer $code 멀티 팝업 코드
	 */
	public function popupModifiy($code)
	{
		// 수정할 정보 가공
		$postData	= $this->postDataProcessing();

		// json 처리
		if (empty($postData) === false) {
			$popupJsonData	= gd_json_encode($postData);
		} else {
			msg("멀티 팝업 정보에 이상이 있어서 수정시 오류가 발생 되었습니다.");
			return false;
		}

		// 멀티 팝업 정보 수정
		$query	= "UPDATE ".GD_ENV." SET value = '".addslashes($popupJsonData)."' WHERE category = '".$this->env_code."' AND name = '".$code."'";
		$res	= $this->db->query($query);
		if($res) {
			msg("[".$postData['text']."] 멀티 팝업 창이 수정 되었습니다.");
		} else {
			msg("[".$postData['text']."] 멀티 팝업 창 수정에 오류가 발생이 되었습니다.");
		}
	}

	/**
	 * 멀티 팝업 정보 삭제
	 * @param integer $code 멀티 팝업 코드
	 */
	public function popupDelete($code)
	{
		// 이미지 갯수 정보를 위한 정보 호출
		$data		= $this->getPopupData($code);
		$popData	= gd_json_decode(stripslashes($data['value']));

		// 팝업 이미지 개수
		$tmp		= explode('_', $popData['displaySet']);
		$imgCount	= $tmp[0] * $tmp[1];

		// 이미지 화일의 삭제
		foreach($this->imgField as $key => $val)
		{
			for($k = 1; $k <= $imgCount; $k++)
			{
				if(is_file($this->data_path.$popData[$key][$k])){
					@unlink( $this->data_path.$popData[$key][$k] );
				}
			}
		}

		// 디비 정보 삭제
		$query	= "DELETE FROM ".GD_ENV." where category = '".$this->env_code."' and name = '".$code."'";
		$res	= $this->db->query($query);

		if($res) {
			msg("[".$popData['text']."] 멀티 팝업 창이 삭제 되었습니다.");
		} else {
			msg("[".$popData['text']."] 멀티 팝업 창 삭제에 오류가 발생이 되었습니다.");
		}
	}

	/**
	 * 이미지 파일 검사
	 * @param string $type 이미지 종류
	 */
	private function chkPopupimg($type)
	{
		require_once(dirname(__FILE__)."/upload.lib.php");
		$upload = new upload_file;
		$upload->upload_file($_FILES[$type],'','image');
		if(!$upload->file_extension_check())return false;
		if(!$upload->file_type_check())return false;
		return true;
	}

	/**
	 * 이미지업로드
	 * @return text 결과 내용 및 json_encode 처리된 이미지명
	 */
	public function imgUploadTemp()
	{
		foreach($this->imgField as $key => $val)
		{
			if(!$this->chkPopupimg($key))
			{
				echo 'fileError';
				exit;
			}
		}

		// 업로드된 이미지 배열
		$uploadImgArray = array();

		foreach($this->imgField as $key => $val)
		{
			// 이미지가 업로드 된경우라면
			if (is_uploaded_file($_FILES[$key]['tmp_name']))
			{
				// 임시 폴더에 업로드 폴더 및 권한 체크
				if(!is_dir($this->tmp_data_path))
				{
					mkdir($this->tmp_data_path);
					chmod($this->tmp_data_path,0707);
				}

				$_ext	= array_pop(explode(".",$_FILES[$key]['name']));
				$_key	= $val;
				$_rnd	= mt_rand(0,999);

				while (is_file($this->tmp_data_path.$this->now.$_rnd.$_key.".".$_ext))
				{
					$this->now++;
					$_rnd	= mt_rand(0,999);
				}

				// 업로드 이미지 설정
				$file[$key]['name']	= "tmp_".$this->now.$_rnd.$_key.".".$_ext;

				move_uploaded_file($_FILES[$key]['tmp_name'],$this->tmp_data_path.$file[$key][name]);
				chmod($this->tmp_data_path.$file[$key][name],0707); // 업로드된 파일 권한 변경
			}
			// 이미지 업로드 없이 수정인 경우 기존의 값 대입
			else if($_POST[$key]){
				$file[$key]['name']	= $_POST[$key];

			}

			// 업로드된 이미지 화일명
			$uploadImgArray[$key]	= $file[$key]['name'];
		}

		// 이미지 내용 출력
		if (empty($uploadImgArray) === false && is_array($uploadImgArray)) {
			echo gd_json_encode($uploadImgArray);	// json_encode 처리
		} else {
			echo 'fileError';
		}

		return true;
	}

	/**
	 * 멀티 팝업 정보 복사
	 * @param integer $code 멀티 팝업 코드
	 */
	public function popupCopy($code)
	{
		// 멀티 팝업 정보
		$data		= $this->getPopupData($code);
		$popData	= gd_json_decode(stripslashes($data['value']));

		// 복사될 멀티 팝업의 코드
		$newcode	= $this->getNewCode();

		// 저장될 정보
		$copyPopdata	= array();

		foreach ( $popData as $key => $value)
		{
			if(array_key_exists($key, $this->imgField)){

				// 팝업 이미지 개수
				$tmp		= explode('_', $popData['displaySet']);
				$imgCount	= $tmp[0] * $tmp[1];

				if ($now === null) $now = time();

				for($k=1;$k<=$imgCount;$k++){

					$image_name = $popData[$key][$k];

					if (! preg_match('/^http(s)?:\/\/.+$/', $image_name)) {

						if(is_file($this->data_path.$image_name)){
							$_ext = array_pop(explode(".",$image_name));
							$_key = $this->imgField[$key];
							$_rnd = mt_rand(0,999);

							while (is_file($this->data_path.$now.$_rnd.$_key.".".$_ext)) {
								$now++;
								$_rnd = mt_rand(0,999);
							}

							$_image_name = "ori_".$now.$_rnd.$_key.".".$_ext;

							@copy($this->data_path.$image_name,$this->data_path.$_image_name);

							$image_name = $_image_name;
						} else {
							$image_name = '';
						}
					}

					$copyPopdata[$key][$k] = $image_name;
				}

				continue;
			}

			if($key == "code"){
				$copyPopdata[$key] = $newcode;
			}else if($key == "popup_use"){
				$copyPopdata[$key] = 'N';
			}else{
				$copyPopdata[$key] = $value; //원본키값 대입
			}
		}

		$copyJsonPopdata = $copyPopdata ? gd_json_encode($copyPopdata) : 'false';

		$query	= "INSERT INTO ".GD_ENV." set category = '".$this->env_code."', name ='".$newcode."', value = '".addslashes($copyJsonPopdata)."'";
		$res	= $this->db->query($query);

		if($res) {
			msg("멀티 팝업 창이 복사 되었습니다.");
		} else {
			msg("멀티 팝업 창 복사에 오류가 발생이 되었습니다.");
		}
	}

	/**
	 * 이미지 출력
	 * @param string $imgUrl 이미지 경로
	 * @param string $imgSize 이미지 종류
	 * @param string $parm 파라메터
	 * @return text img 테그
	 */
	public function popupimg($imgUrl, $imgSize = '', $parm = '')
	{
		global $cfg;

		if(!$imgUrl) return;

		if(!preg_match('/http:\/\//',$imgUrl)){
			$imgUrl = $cfg['rootDir'].'/data/multipopup/'.$imgUrl;
		}

		if ($imgSize){
			$size	= explode(',', $imgSize);
			$vsize	= ' width="'.$size[0].'" ';
			if ($size[1]) {
				$vsize .= ' height="'.$size[1].'" ';
			}
		}

		$imgTag		= '<img src="'.$imgUrl.'" '.$vsize.' '.$parm.' />';

		return $imgTag;
	}

	/**
	 * 이미지 링크 주소
	 * @param string $linkTarget 타겟 종류
	 * @param string $linkUrl 링크 주소
	 * @return text a 테그내 elemnt
	 */
	public function getLink($linkTarget, $linkUrl)
	{
		if ($linkTarget == 'self' && empty($linkUrl) === false) {
			$link	= 'href=javascript:goLink(\''.$linkUrl.'\');';
		}
		else if(!empty($linkUrl)) {
			$link = 'href="'.$linkUrl.'" target="_blank"';
		}
		else {
			$link	= '';
		}

		return $link;

	}

	/**
	 * 이미지 주소
	 * @param string $img 이미지명
	 * @param string $_dir http 주소
	 * @return text 실제 이미지 주소
	 */
	public function getImgSrc($img,$_dir)
	{
		if(!preg_match('/http:\/\//',$img)){
			$img	= $_dir.$img;
		}else{
			$img	= $img;
		}

		return $img;

	}

	/**
	 * 멀티 팝업 사용여부 체크 및 데이타의 json 처리
	 */
	public function ajaxDataPopup()
	{
		// 멀티 팝업 정보 리스트
		$popupList	= $this->getPopupList();

		// 실제 출력할 멀티 팝업 정보
		$popupData	= array();

		foreach ( $popupList as $getData )
		{
			// 팝업창 미사용인 경우
			if ( $getData['popup_use'] != 'Y' ) continue;

			// 특정기간동안 팝업창 열림인 경우
			if ($getData['popup_dt2tm'] == 'Y')
			{
				// 기간이 안되었다면 패스
				if ( ($getData['popup_sdt'].$getData['popup_stime']) > date('YmdHi') || ($getData['popup_edt'].$getData['popup_etime']) < date('YmdHi') )
				{
					continue;
				}
			}

			// 특정기간동안 특정시간에만 팝업창 열림
			if ($getData['popup_dt2tm'] == 'T')
			{
				// 날짜가 안되었다면 패스
				if ($getData['popup_sdt'] > date('Ymd') || $getData['popup_edt'] < date('Ymd') )
				{
					continue;
				}
				else {
					// 날짜를 되었다고 해도 시간이 맞지 않으면 패스
					if ($getData['popup_stime'] > date('Hi') || $getData['popup_etime'] < date('Hi') ) {
						continue;
					}
				}
			}

			// 출력할 멀티 팝업창 데이타 정보
			$popupData[]	= $getData;
		}

		return gd_json_encode($popupData);
	}
}
?>