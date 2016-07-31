<? 
require_once "../../class/framework/Request.class.php";
require_once "../../class/enamu/EnamuDAO.class.php";
require_once "../../class/lib/file.class.php";
require_once "../../class/framework/Request.class.php";
require_once "../../class/cody/CodyDAO.class.php";
require_once "../../class/image/ImageDAO.class.php";
require_once "../../class/lib/Loader.class.php";
require_once "../../class/lib/Mata.class.php";


class indb{
	var $req;
	var $tpl;
	var $dao;
	var $Edao;
	
	var $Idao;

	function indb(){
		$this->Edao = new EnamuDAO();
		$this->req = new Request('POST');
		$this->dao = new CodyDAO();
		$this->Idao = new ImageDAO();	
		$this->load = new Loader();

		### 어드민 로그인 체크
		if(!$this->Edao->adminAuth()){
			L_mata::mata('../../../admin/');
		}
	}

	function index(){
		
		if($this->req->get('fn') == 'I'){
			$this->Insert();
		}else if($this->req->get('fn') == 'M'){
			$this->modify();
		}else if($this->req->get('fn') == 'CI'){
			$this->CodyInfo();
		}
	
	}

	function Insert(){
		
		$codyHtml = stripcslashes($this->req->get('codyhtml'));							###template html
		$T_img_cnt = $this->req->get('T_img_cnt');										###등록된이미지수
		$imgnm =  explode("^",substr(($this->req->get('imgnm')),0,-1));					###등록된이미지 이름
		$imgno =  explode("^",substr(($this->req->get('imgno')),0,-1));					###상품 번호
		$imgnmsize = explode("^",substr(($this->req->get('imgnmsize')),0,-1));			###등록된이미지 사이즈
		$imgRotate = explode("^",substr(($this->req->get('imgRotate')),0,-1));			###등록된이미지 회전각도
		$imgPosition = explode("^",substr(($this->req->get('imgPosition')),0,-1));		###등록된이미지 이미지 위치
		$divArea = explode("^",substr(($this->req->get('divArea')),0,-1));				###등록된이미지 이미지 레이어크기
		$divpos = explode("^",$this->req->get('Divpos'));								###템플릿 위치
		$codycontent = $this->req->get('codycontent');									###코멘트
		$boardnm = $this->req->get('boardnm');											###보드 선택정보
		$TP_id = $this->req->get('TP_id');												###템플릿정보
		$campusSize = explode("^",$this->req->get('campusSize'));						###템플릿크기
		$cody_name = strip_tags($this->req->get('cody_name'));							###코디명
		$thumbnail = date('Ymdhis')."_".$TP_id.".jpg";									### 생성되는 파일 기준이름
		$back ='../../images/chambers.jpg';												###원본 백그라운드
		$dept = '../../data';															###썸네일 이미지 경로 
		
		if($this->Edao->goodsConfirm($imgno,$T_img_cnt) == 'N'){
			L_mata::close('코디 생성중 일부 상품정보가 정상적으로 적용되지 않았습니다. \n삭제 또는 변경정보가 있는지 확인 후, 새로 등록해 주세요.');
			exit;
		}

		$obj = new Cody();
		$obj->set('member_id', $this->Edao->getSession('m_id'));
		$obj->set('tem_idx', $TP_id);
		$obj->set('memo', $codycontent);
		$obj->set('member_name', $this->Edao->getSession('name'));
		$obj->set('thumnail_name', $thumbnail);
		$obj->set('cody_name', $cody_name);
		$obj->set('regdate', 'now()');
		$this->dao->setObject($obj);

		$this->dao->add(array('member_id','tem_idx','memo','member_name','thumnail_name','cody_name','regdate'));
		$cody_idx = $this->dao->lastInsertId;
        
       		
		###백그라운드 이미지 생성
		$background_img_o = imagecreatefromjpeg($back);
		$cpX = imageSX($background_img_o); 
		$cpY = imageSY($background_img_o);
		$background_img = imagecreatetruecolor($campusSize[0], $campusSize[1]);
		imagecopyresampled($background_img, $background_img_o, 0, 0, 0, 0, $campusSize[0], $campusSize[1], $cpX, $cpY); 
				

		for($i=0;$i < $T_img_cnt;$i++){
			$filename = "../../../../" . $imgnm[$i];
			
			$divImg = explode(":",$divArea[$i]);
			$Position = explode(":",$imgPosition[$i]);
			$resize = explode(":",$imgnmsize[$i]);			###이미지의 리싸이징 크기
			$divposition = explode(":",$divpos[$i]);		###템플릿 위치
			$imgInfo = getimagesize($filename);
			
			###회전 값이 없으면 0 을 넣는다.
			if($imgRotate[$i]) $rotate = "-".$imgRotate[$i];
			else $rotate = "0";
			
			$PsizeX = $Position[0];
			$PsizeY = $Position[1];

			###오리지날 이미지 불러온다.
			if($imgInfo[2] == '2'){
				$overlay_src_o = imagecreatefromjpeg($filename);  ### jpg 대상이미지				
			}else if($imgInfo[2] == '1'){
				$overlay_src_o = imagecreatefromgif($filename);  ### gif 대상이미지		
            }else if($imgInfo[2] == '3'){
				$overlay_src_o = imagecreatefrompng($filename);  ### png 대상이미지				
			}else{
                 // 지원하지 않는 포멧이면 등록코디를 삭제한다.
                $this->dao->delete("idx = '".$cody_idx."'");
				L_mata::close('이미지는 JPG , GIF, PNG 만 지원합니다.');
                
               	exit;
			}

			$ORX = imageSX($overlay_src_o); 
			$ORY = imageSY($overlay_src_o);

			###원본이미지 리싸이즈를 위한 캠퍼스를 만든다.
			$overlay_src = imagecreatetruecolor($resize[0], $resize[1]);
			###이미지가 변했다면 리싸이징
			imagecopyresampled($overlay_src, $overlay_src_o, 0, 0, 0, 0, $resize[0], $resize[1], $ORX, $ORY); 
			
			###이미지를 회전한다.
			$bgc_dst = imagecolorallocate($overlay_src, 255, 255, 255);
			$dst_img = imagerotate($overlay_src, $rotate, $bgc_dst);
			###회전이미지 수치를 구한다.			
			$X = imageSX($dst_img);
			$Y = imageSY($dst_img);
			
			/*
			 * 회전이미지 비율구하기
			 * 회전이미지가 원본이미지보다 넓이(높이)가 작다면 
			 * 포지션값에 계산한값을 더한다.
			 *     포지션 X = (Rotate 이미지 넓이 - 원본 이미지 넓이 )/2
			 *     포지션 Y = (Rotate 이미지 높이 - 원본 이미지 높이 )/2
			 */
		    
			if($X > $resize[0]) $PsizeX_1 = "-".(($X-$resize[0])/2);
			else $PsizeX_1 = str_replace( "-","",(($X-$resize[0])/2) );

			if($Y > $resize[1]) $PsizeY_1 = "-".(($Y-$resize[1])/2);
			else $PsizeY_1 = str_replace( "-","",(($Y-$resize[1])/2) );
			
			###작성된 이미지 를 만든다.
			$overlay_img = imagecreatetruecolor($divImg[0], $divImg[1]);		###캠퍼스 
			$bgc_overlay = imagecolorallocate($overlay_img, 255, 255, 255);		###캠퍼스 색상 흰색			
			imagefilledrectangle($overlay_img, 0, 0, $divImg[0],$divImg[1], $bgc_overlay);	###레이어위에 색상 등록	
			###이미지 생성
			imagecopyresampled($overlay_img, $dst_img, $PsizeX+$PsizeX_1, $PsizeY+$PsizeY_1, 0, 0, $X, $Y, $X, $Y);
			
			### 조각이미지 저장
			$imgfile = '/piece/'.$cody_idx.'_'.$imgno[$i].'_droppable'.($i+1).'-images_'.$thumbnail;;
			$piece_img[$i] = "./data".$imgfile;
			imagejpeg($overlay_img, $dept.$imgfile, '100');	

			###이미지를 합성한다.
			imagecopyresampled($background_img, $overlay_img, $divposition[0], $divposition[1], 0, 0, $divImg[0], $divImg[1], $divImg[0], $divImg[1]);
			
			###생성된 이미지들 삭제
			imagedestroy($overlay_src_o);
			imagedestroy($overlay_src);
			imagedestroy($dst_img);
			imagedestroy($overlay_img);
			
		}
		###모두 등록했다면 생성한다.
		###100_20120903123202_t2b1.xxx
		imagejpeg($background_img, $dept.'/org/'.$thumbnail, '100');
		
        if(!is_file($dept.'/org/'.$thumbnail)) {
            $this->dao->delete("idx = '".$cody_idx."'");
            L_mata::close('파일이 존재하지 않습니다.');
        }

        ###썸네일만들기
		$imgw100 = 100;
		L_File::resizeImage($dept.'/org/'.$thumbnail, $dept.'/Tnail/100/100_'.$thumbnail, $imgw100);
		$imgw200 = 200;
		L_File::resizeImage($dept.'/org/'.$thumbnail, $dept.'/Tnail/200/200_'.$thumbnail, $imgw200);
		$imgw300 = 300;
		L_File::resizeImage($dept.'/org/'.$thumbnail, $dept.'/Tnail/300/300_'.$thumbnail, $imgw300);
		
		imagedestroy($background_img);		###백그라운드 이미지 id 삭제
		imagedestroy($background_img_o);	###백그라운드 이미지 id 삭제
		
		
		/*코디 테이블 코디 정보 입력
		  이미지 정보 등록 (기존이미지 사용 정보 상속
	 	  코디 템플릿 등록 - 템플릿 html
		      템플릿명,이미지수,템플릿 html	

		*/
		
		###이미지 저장 정보
		$setCost = 0;
		$set_runout = "Y"; ###기본값 개시
		$Iobj = new Image();
		for($i=0;$i < $T_img_cnt;$i++){
			$goodsInfo =  $this->Edao->goodsFetch($imgno[$i]);
			$goodsOption =  $this->Edao->goodsOptionPrice($imgno[$i]);
			$setCost += $goodsOption[price];		
			
			$runout = $this->Edao->Runout($goodsInfo);
			if($runout == "N") $set_runout = $runout;

			$Iobj->set('type', 'M');
			$Iobj->set('tem_index', ($i+1));
			$Iobj->set('cody_idx', $cody_idx);
			$Iobj->set('name', $goodsInfo['goodsnm']);
			$Iobj->set('file_name', $imgnm[$i]);
			$Iobj->set('goods_idx', $goodsInfo['goodsno']);
			$Iobj->set('file_alt', $goodsInfo['maker']);
			$Iobj->set('source', $this->Edao->cfg['shopName']);
			$Iobj->set('price', $goodsOption[price]);
			$Iobj->set('regdate', 'now()');
			
			$this->Idao->setObject($Iobj);

			$this->Idao->add(array('type','tem_index','cody_idx','name','file_name','goods_idx','file_alt','source','price','regdate'));	
		}
		

		### 세트 상품 세트 가격update
		for($i=0;$i < $T_img_cnt;$i++){
			$codyHtml = preg_replace('/<img(\s+)[^>]+id\="droppable'.($i+1).'-images"[^>]+>/','<img id="droppable'.($i+1).'-images" class="ui-draggable dropped-images_chang" src="'.$piece_img[$i].'" title="'.$imgno[$i].'">',$codyHtml);
		}

		$obj->set('CD_content', $codyHtml);
		$obj->set('setCost', $setCost);
		$obj->set('goodsState', $set_runout);
		$obj->set('goodsStateRegdate', 'now()');
		$this->dao->setObject($obj);
		$this->dao->modify(array('setCost','goodsState','CD_content','goodsStateRegdate'),"idx = '".$cody_idx."'");

		$this->load->View('./html/close.php',$request_data);
	}
	
	function modify(){
		
		$codycontent = $this->req->get('codycontent');							
		$cody_name = $this->req->get('cody_name');							
		$idx = $this->req->get('idx');	
		$state = $this->req->get('state');	

		$obj = new Cody();
		$obj->set('memo', $codycontent);
		$obj->set('cody_name', $cody_name);
		$obj->set('state', $state);
		$this->dao->setObject($obj);

		$this->dao->modify(array('memo','cody_name','state'),"idx = ".$idx);
		$request_data = array('fn'=>'M');
		$this->load->View('./html/close.php',$request_data);
	}

	function CodyInfo(){
		$codyhtml = stripcslashes($this->req->get('codyhtml'));	
		$T_img_cnt = $this->req->get('T_img_cnt');		
		$imgnm = $this->req->get('imgnm');
		$imgno = $this->req->get('imgno');
		$imgnmsize = $this->req->get('imgnmsize');
		$imgRotate = $this->req->get('imgRotate');
		$imgPosition = $this->req->get('imgPosition');
		$divArea = $this->req->get('divArea');
		$Divpos = $this->req->get('Divpos');
		$TP_id = $this->req->get('TP_id');
		$campusSize = $this->req->get('campusSize');
		
		$request_data = array(	'codyhtml'=>$codyhtml,
								'T_img_cnt'=>$T_img_cnt,
								'imgnm'=>$imgnm,
								'imgno'=>$imgno,
								'imgnmsize'=>$imgnmsize,
								'imgRotate'=>$imgRotate,
								'imgPosition'=>$imgPosition,
								'divArea'=>$divArea,
								'Divpos'=>$Divpos,
								'TP_id'=>$TP_id,
								'campusSize'=>$campusSize
								 );

		$this->load->View('./html/codyinfo_form.php',$request_data);

	}
 
}


 $in = new indb();

 $in->index();
 ?>