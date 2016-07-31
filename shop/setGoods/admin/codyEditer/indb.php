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

		### ���� �α��� üũ
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
		$T_img_cnt = $this->req->get('T_img_cnt');										###��ϵ��̹�����
		$imgnm =  explode("^",substr(($this->req->get('imgnm')),0,-1));					###��ϵ��̹��� �̸�
		$imgno =  explode("^",substr(($this->req->get('imgno')),0,-1));					###��ǰ ��ȣ
		$imgnmsize = explode("^",substr(($this->req->get('imgnmsize')),0,-1));			###��ϵ��̹��� ������
		$imgRotate = explode("^",substr(($this->req->get('imgRotate')),0,-1));			###��ϵ��̹��� ȸ������
		$imgPosition = explode("^",substr(($this->req->get('imgPosition')),0,-1));		###��ϵ��̹��� �̹��� ��ġ
		$divArea = explode("^",substr(($this->req->get('divArea')),0,-1));				###��ϵ��̹��� �̹��� ���̾�ũ��
		$divpos = explode("^",$this->req->get('Divpos'));								###���ø� ��ġ
		$codycontent = $this->req->get('codycontent');									###�ڸ�Ʈ
		$boardnm = $this->req->get('boardnm');											###���� ��������
		$TP_id = $this->req->get('TP_id');												###���ø�����
		$campusSize = explode("^",$this->req->get('campusSize'));						###���ø�ũ��
		$cody_name = strip_tags($this->req->get('cody_name'));							###�ڵ��
		$thumbnail = date('Ymdhis')."_".$TP_id.".jpg";									### �����Ǵ� ���� �����̸�
		$back ='../../images/chambers.jpg';												###���� ��׶���
		$dept = '../../data';															###����� �̹��� ��� 
		
		if($this->Edao->goodsConfirm($imgno,$T_img_cnt) == 'N'){
			L_mata::close('�ڵ� ������ �Ϻ� ��ǰ������ ���������� ������� �ʾҽ��ϴ�. \n���� �Ǵ� ���������� �ִ��� Ȯ�� ��, ���� ����� �ּ���.');
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
        
       		
		###��׶��� �̹��� ����
		$background_img_o = imagecreatefromjpeg($back);
		$cpX = imageSX($background_img_o); 
		$cpY = imageSY($background_img_o);
		$background_img = imagecreatetruecolor($campusSize[0], $campusSize[1]);
		imagecopyresampled($background_img, $background_img_o, 0, 0, 0, 0, $campusSize[0], $campusSize[1], $cpX, $cpY); 
				

		for($i=0;$i < $T_img_cnt;$i++){
			$filename = "../../../../" . $imgnm[$i];
			
			$divImg = explode(":",$divArea[$i]);
			$Position = explode(":",$imgPosition[$i]);
			$resize = explode(":",$imgnmsize[$i]);			###�̹����� ������¡ ũ��
			$divposition = explode(":",$divpos[$i]);		###���ø� ��ġ
			$imgInfo = getimagesize($filename);
			
			###ȸ�� ���� ������ 0 �� �ִ´�.
			if($imgRotate[$i]) $rotate = "-".$imgRotate[$i];
			else $rotate = "0";
			
			$PsizeX = $Position[0];
			$PsizeY = $Position[1];

			###�������� �̹��� �ҷ��´�.
			if($imgInfo[2] == '2'){
				$overlay_src_o = imagecreatefromjpeg($filename);  ### jpg ����̹���				
			}else if($imgInfo[2] == '1'){
				$overlay_src_o = imagecreatefromgif($filename);  ### gif ����̹���		
            }else if($imgInfo[2] == '3'){
				$overlay_src_o = imagecreatefrompng($filename);  ### png ����̹���				
			}else{
                 // �������� �ʴ� �����̸� ����ڵ� �����Ѵ�.
                $this->dao->delete("idx = '".$cody_idx."'");
				L_mata::close('�̹����� JPG , GIF, PNG �� �����մϴ�.');
                
               	exit;
			}

			$ORX = imageSX($overlay_src_o); 
			$ORY = imageSY($overlay_src_o);

			###�����̹��� ������� ���� ķ�۽��� �����.
			$overlay_src = imagecreatetruecolor($resize[0], $resize[1]);
			###�̹����� ���ߴٸ� ������¡
			imagecopyresampled($overlay_src, $overlay_src_o, 0, 0, 0, 0, $resize[0], $resize[1], $ORX, $ORY); 
			
			###�̹����� ȸ���Ѵ�.
			$bgc_dst = imagecolorallocate($overlay_src, 255, 255, 255);
			$dst_img = imagerotate($overlay_src, $rotate, $bgc_dst);
			###ȸ���̹��� ��ġ�� ���Ѵ�.			
			$X = imageSX($dst_img);
			$Y = imageSY($dst_img);
			
			/*
			 * ȸ���̹��� �������ϱ�
			 * ȸ���̹����� �����̹������� ����(����)�� �۴ٸ� 
			 * �����ǰ��� ����Ѱ��� ���Ѵ�.
			 *     ������ X = (Rotate �̹��� ���� - ���� �̹��� ���� )/2
			 *     ������ Y = (Rotate �̹��� ���� - ���� �̹��� ���� )/2
			 */
		    
			if($X > $resize[0]) $PsizeX_1 = "-".(($X-$resize[0])/2);
			else $PsizeX_1 = str_replace( "-","",(($X-$resize[0])/2) );

			if($Y > $resize[1]) $PsizeY_1 = "-".(($Y-$resize[1])/2);
			else $PsizeY_1 = str_replace( "-","",(($Y-$resize[1])/2) );
			
			###�ۼ��� �̹��� �� �����.
			$overlay_img = imagecreatetruecolor($divImg[0], $divImg[1]);		###ķ�۽� 
			$bgc_overlay = imagecolorallocate($overlay_img, 255, 255, 255);		###ķ�۽� ���� ���			
			imagefilledrectangle($overlay_img, 0, 0, $divImg[0],$divImg[1], $bgc_overlay);	###���̾����� ���� ���	
			###�̹��� ����
			imagecopyresampled($overlay_img, $dst_img, $PsizeX+$PsizeX_1, $PsizeY+$PsizeY_1, 0, 0, $X, $Y, $X, $Y);
			
			### �����̹��� ����
			$imgfile = '/piece/'.$cody_idx.'_'.$imgno[$i].'_droppable'.($i+1).'-images_'.$thumbnail;;
			$piece_img[$i] = "./data".$imgfile;
			imagejpeg($overlay_img, $dept.$imgfile, '100');	

			###�̹����� �ռ��Ѵ�.
			imagecopyresampled($background_img, $overlay_img, $divposition[0], $divposition[1], 0, 0, $divImg[0], $divImg[1], $divImg[0], $divImg[1]);
			
			###������ �̹����� ����
			imagedestroy($overlay_src_o);
			imagedestroy($overlay_src);
			imagedestroy($dst_img);
			imagedestroy($overlay_img);
			
		}
		###��� ����ߴٸ� �����Ѵ�.
		###100_20120903123202_t2b1.xxx
		imagejpeg($background_img, $dept.'/org/'.$thumbnail, '100');
		
        if(!is_file($dept.'/org/'.$thumbnail)) {
            $this->dao->delete("idx = '".$cody_idx."'");
            L_mata::close('������ �������� �ʽ��ϴ�.');
        }

        ###����ϸ����
		$imgw100 = 100;
		L_File::resizeImage($dept.'/org/'.$thumbnail, $dept.'/Tnail/100/100_'.$thumbnail, $imgw100);
		$imgw200 = 200;
		L_File::resizeImage($dept.'/org/'.$thumbnail, $dept.'/Tnail/200/200_'.$thumbnail, $imgw200);
		$imgw300 = 300;
		L_File::resizeImage($dept.'/org/'.$thumbnail, $dept.'/Tnail/300/300_'.$thumbnail, $imgw300);
		
		imagedestroy($background_img);		###��׶��� �̹��� id ����
		imagedestroy($background_img_o);	###��׶��� �̹��� id ����
		
		
		/*�ڵ� ���̺� �ڵ� ���� �Է�
		  �̹��� ���� ��� (�����̹��� ��� ���� ���
	 	  �ڵ� ���ø� ��� - ���ø� html
		      ���ø���,�̹�����,���ø� html	

		*/
		
		###�̹��� ���� ����
		$setCost = 0;
		$set_runout = "Y"; ###�⺻�� ����
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
		

		### ��Ʈ ��ǰ ��Ʈ ����update
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