<?php

include '../lib.php';
include '../../lib/qfile.class.php';
include '../../lib/upload.lib.php';

$configFilePath = '../../conf/config.mobileAnimationBanner_'.$cfg['tplSkinMobileWork'].'.php';
$mobileAnimationBannerDataDir = realpath(dirname(__FILE__).'/../../data/skin_mobileV2/'.$cfg['tplSkinMobileWork'].'/common/img/animation_banner/banner');
$mobileAnimationBannerNaviDir = realpath(dirname(__FILE__).'/../../data/skin_mobileV2/'.$cfg['tplSkinMobileWork'].'/common/img/animation_banner/navi');

if (file_exists($mobileAnimationBannerDataDir) === false) {
	mkdir($mobileAnimationBannerDataDir, 0707, true);
}
if (file_exists($mobileAnimationBannerNaviDir) === false) {
	mkdir($mobileAnimationBannerNaviDir, 0707, true);
}

switch ($_POST['mode']) {
	case 'save':
		// 파일 확장자 검사
		foreach ($uploadFiles['name']['image'] as $index => $fileName) {
			if (strlen($fileName) > 0 && preg_match('/\.(jpg|jpeg|gif|png|bmp)$/i', $fileName) < 1) {
				msg('배너이미지에 사용할 수 없는 파일이 업로드 되었습니다.', -1);
			}
		}
		foreach ($uploadFiles['name']['onAnchor'] as $index => $fileName) {
			if (strlen($fileName) > 0 && preg_match('/\.(jpg|jpeg|gif|png|bmp)$/i', $fileName) < 1) {
				msg('내비게이션 활성 버튼에 사용할 수 없는 파일이 업로드 되었습니다.', -1);
			}
		}
		foreach ($uploadFiles['name']['offAnchor'] as $index => $fileName) {
			if (strlen($fileName) > 0 && preg_match('/\.(jpg|jpeg|gif|png|bmp)$/i', $fileName) < 1) {
				msg('내비게이션 비활성 버튼에 사용할 수 없는 파일이 업로드 되었습니다.', -1);
			}
		}

		// 사용할 배너 이미지 리스트
		$usingBanner = array();
		foreach ($_POST['imageURL'] as $index => $imageName) {
			$usingBanner[$index] = $imageName;
		}

		// 배너 이미지 업로드
		$uploadedBannerFiles = array();
		$bannerFiles = reverse_file_array($_FILES['image']);
		foreach ($bannerFiles as $index => $banner) {
			if (strlen($banner['tmp_name']) > 0) {
				$imageName = 'banner_'.sprintf('%02s', md5($index.'_'.time()));
				$upload = new upload_file($banner, $mobileAnimationBannerDataDir.'/'.$imageName, 'image');
				$uploadResult = $upload->upload();
				if ($uploadResult) {
					$uploadedBannerFiles[] = $imageName;
					$usingBanner[$index] = $imageName;
				}
				else {
					foreach ($uploadedBannerFiles as $_index => $_file) {
						unlink($mobileAnimationBannerDataDir.'/'.$_file);
					}
					msg('배너이미지에 이미지가 아닌 파일이 업로드 되었습니다.', -1);
				}
			}
			else {
				continue;
			}
		}

		// 사용할 내비게이션 이미지 리스트
		$usingOnAnchor = array();
		$usingOffAnchor = array();
		foreach ($_POST['onAnchorURL'] as $index => $imageName) {
			$usingOnAnchor[$index] = $imageName;
		}
		foreach ($_POST['offAnchorURL'] as $index => $imageName) {
			$usingOffAnchor[$index] = $imageName;
		}

		// 내비게이션ON 이미지 업로드
		$uploadedOnAnchorFiles = array();
		$onAnchorFiles = reverse_file_array($_FILES['onAnchor']);
		foreach ($onAnchorFiles as $index => $files) {
			if (strlen($files['tmp_name']) > 0) {
				$imageName = 'on_'.sprintf('%02s', md5($index.'_'.time()));
				$upload = new upload_file($files, $mobileAnimationBannerNaviDir.'/'.$imageName, 'image');
				$uploadResult = $upload->upload();
				if ($uploadResult) {
					$uploadedOnAnchorFiles[] = $imageName;
					$usingOnAnchor[$index] = $imageName;
				}
				else {
					foreach ($uploadedOnAnchorFiles as $_index => $_file) {
						unlink($mobileAnimationBannerNaviDir.'/'.$_file);
					}
					msg('내비게이션 활성 버튼에 이미지가 아닌 파일이 업로드 되었습니다.', -1);
				}
			}
			else {
				continue;
			}
		}

		// 내비게이션OFF 이미지 업로드
		$uploadedOffAnchorFiles = array();
		$offAnchorFiles = reverse_file_array($_FILES['offAnchor']);
		foreach ($offAnchorFiles as $index => $files) {
			if (strlen($files['tmp_name']) > 0) {
				$imageName = 'off_'.sprintf('%02s', md5($index.'_'.time()));
				$upload = new upload_file($files, $mobileAnimationBannerNaviDir.'/'.$imageName, 'image');
				$uploadResult = $upload->upload();
				if ($uploadResult) {
					$uploadedOffAnchorFiles[] = $imageName;
					$usingOffAnchor[$index] = $imageName;
				}
				else {
					foreach ($uploadedOffAnchorFiles as $_index => $_file) {
						unlink($mobileAnimationBannerNaviDir.'/'.$_file);
					}
					msg('내비게이션 비활성 버튼에 이미지가 아닌 파일이 업로드 되었습니다.', -1);
				}
			}
			else {
				continue;
			}
		}

		// 사용할 배너 이미지를 제외하고 모두 삭제처리
		$bannerDir = opendir($mobileAnimationBannerDataDir);
		while ($name = readdir($bannerDir)) {
			if ($name === '.' || $name === '..') {
				continue;
			}
			else if (in_array($name, $usingBanner)) {
				continue;
			}
			else {
				unlink($mobileAnimationBannerDataDir.'/'.$name);
			}
		}
		closedir($bannerDir);

		// 사용할 내비게이션 이미지를 제외하고 모두 삭제처리
		$naviDir = opendir($mobileAnimationBannerNaviDir);
		while ($name = readdir($naviDir)) {
			if ($name === '.' || $name === '..') {
				continue;
			}
			else if (in_array($name, $usingOnAnchor) || in_array($name, $usingOffAnchor)) {
				continue;
			}
			else {
				unlink($mobileAnimationBannerNaviDir.'/'.$name);
			}
		}
		closedir($naviDir);

		// 설정파일 작성
		$qfile = new qfile();
		$qfile->open($configFilePath);
		$qfile->write('<?php'.PHP_EOL);
		$qfile->write('$mobileAnimationBannerConfig = array();'.PHP_EOL);
		$qfile->write('$mobileAnimationBannerConfig["enable"] = "'.$_POST['enable'].'";'.PHP_EOL);
		$qfile->write('$mobileAnimationBannerConfig["type"] = "'.$_POST['type'].'";'.PHP_EOL);
		$qfile->write('$mobileAnimationBannerConfig["duration"] = "'.$_POST['duration'].'";'.PHP_EOL);
		$qfile->write('$mobileAnimationBannerConfig["directionAnchorDisplay"] = "'.$_POST['directionAnchorDisplay'].'";'.PHP_EOL);
		$qfile->write('$mobileAnimationBannerConfig["shiftType"] = "'.$_POST['shiftType'].'";'.PHP_EOL);
		$qfile->write('$mobileAnimationBannerConfig["interval"] = "'.$_POST['interval'].'";'.PHP_EOL);
		$qfile->write('$mobileAnimationBannerConfig["imageWidth"] = "'.$_POST['imageWidth'].'";'.PHP_EOL);
		$qfile->write('$mobileAnimationBannerConfig["imageHeight"] = "'.$_POST['imageHeight'].'";'.PHP_EOL);
		$qfile->write('$mobileAnimationBannerConfig["width"] = "'.$_POST['width'].'";'.PHP_EOL);
		$qfile->write('$mobileAnimationBannerConfig["height"] = "'.$_POST['height'].'";'.PHP_EOL);
		foreach ($usingBanner as $index => $imageName) {
			$qfile->write('$mobileAnimationBannerConfig["image"]['.$index.'] = "'.$imageName.'";'.PHP_EOL);
		}
		foreach ($_POST['link'] as $index => $link) {
			$qfile->write('$mobileAnimationBannerConfig["link"]['.$index.'] = "'.$link.'";'.PHP_EOL);
		}
		foreach ($_POST['target'] as $index => $target) {
			$qfile->write('$mobileAnimationBannerConfig["target"]['.$index.'] = "'.$target.'";'.PHP_EOL);
		}
		$qfile->write('$mobileAnimationBannerConfig["anchorDisplay"] = "'.$_POST['anchorDisplay'].'";'.PHP_EOL);
		foreach ($usingOnAnchor as $index => $imageName) {
			$qfile->write('$mobileAnimationBannerConfig["onAnchor"]['.$index.'] = "'.$imageName.'";'.PHP_EOL);
		}
		foreach ($usingOffAnchor as $index => $imageName) {
			$qfile->write('$mobileAnimationBannerConfig["offAnchor"]['.$index.'] = "'.$imageName.'";'.PHP_EOL);
		}
		$qfile->close();
		chmod($configFilePath, 0707);
		msg('정상적으로 저장되었습니다.', './iframe.animation_banner.php');
		break;
	case "previewUpload":
		foreach (reverse_file_array($_FILES['image']) as $index => $banner) {
			if (strlen($banner['tmp_name']) > 0) {
				if($cfg['tplSkinMobileWork'] == '' || $mobileAnimationBannerDataDir == '') {
					msg('모바일샵 > 모바일샵 디자인관리에서 [작업스킨]과 [사용스킨]을 설정해 주세요.', -1);
				}
				$imageName = 'banner_'.sprintf('%02s', md5($index.'_'.time()));
				$upload = new upload_file($banner, $mobileAnimationBannerDataDir.'/'.$imageName, 'image');
				$uploadResult = $upload->upload();

				if(file_exists($mobileAnimationBannerDataDir.'/'.$imageName) === false) {
					msg('이미지 업로드가 실패되었습니다.', -1);
				}

				if ($uploadResult) {
					if($index == '0') {
						$ImgSize	= getimagesize($mobileAnimationBannerDataDir.'/'.$imageName);
						$image_width = $ImgSize[0];
						$image_height = $ImgSize[1];
					} else {
						$image_width = 0;
						$image_height = 0;
					}
					exit('<script type="text/javascript">parent.previewImageCallback("image", '.$index.', "'.$imageName.'", '.$image_width.', '.$image_height.');</script>');
				}
				else {
					msg('배너이미지에 이미지가 아닌 파일이 선택 되었습니다.', -1);
				}
			}
			else {
				continue;
			}
		}

		foreach (reverse_file_array($_FILES['onAnchor']) as $index => $anchor) {
			if (strlen($anchor['tmp_name']) > 0) {
				$imageName = 'on_'.sprintf('%02s', md5($index.'_'.time()));
				$upload = new upload_file($anchor, $mobileAnimationBannerNaviDir.'/'.$imageName, 'image');
				$uploadResult = $upload->upload();
				if ($uploadResult) {
					exit('<script type="text/javascript">parent.previewImageCallback("onAnchor", '.$index.', "'.$imageName.'", 0,0);</script>');
				}
				else {
					msg('활성 버튼에 이미지가 아닌 파일이 선택 되었습니다.', -1);
				}
			}
			else {
				continue;
			}
		}

		foreach (reverse_file_array($_FILES['offAnchor']) as $index => $anchor) {
			if (strlen($anchor['tmp_name']) > 0) {
				$imageName = 'off_'.sprintf('%02s', md5($index.'_'.time()));
				$upload = new upload_file($anchor, $mobileAnimationBannerNaviDir.'/'.$imageName, 'image');
				$uploadResult = $upload->upload();
				if ($uploadResult) {
					exit('<script type="text/javascript">parent.previewImageCallback("offAnchor", '.$index.', "'.$imageName.'", 0,0);</script>');
				}
				else {
					msg('비활성 버튼에 이미지가 아닌 파일이 선택 되었습니다.', -1);
				}
			}
			else {
				continue;
			}
		}
		break;
}