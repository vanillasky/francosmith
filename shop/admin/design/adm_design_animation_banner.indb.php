<?php

include '../lib.php';
include '../../lib/qfile.class.php';
include '../../lib/upload.lib.php';

$configFilePath = '../../conf/config.animationBanner_'.$cfg['tplSkinWork'].'.php';
$animationBannerDataDir = realpath(dirname(__FILE__).'/../../data/skin/'.$cfg['tplSkinWork'].'/img/animation_banner/banner');
$animationBannerNaviDir = realpath(dirname(__FILE__).'/../../data/skin/'.$cfg['tplSkinWork'].'/img/animation_banner/navi');

if (file_exists($animationBannerDataDir) === false) {
	mkdir($animationBannerDataDir, 0707, true);
}
if (file_exists($animationBannerNaviDir) === false) {
	mkdir($animationBannerNaviDir, 0707, true);
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
				$upload = new upload_file($banner, $animationBannerDataDir.'/'.$imageName, 'image');
				$uploadResult = $upload->upload();
				if ($uploadResult) {
					$uploadedBannerFiles[] = $imageName;
					$usingBanner[$index] = $imageName;
				}
				else {
					foreach ($uploadedBannerFiles as $_index => $_file) {
						unlink($animationBannerDataDir.'/'.$_file);
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
				$upload = new upload_file($files, $animationBannerNaviDir.'/'.$imageName, 'image');
				$uploadResult = $upload->upload();
				if ($uploadResult) {
					$uploadedOnAnchorFiles[] = $imageName;
					$usingOnAnchor[$index] = $imageName;
				}
				else {
					foreach ($uploadedOnAnchorFiles as $_index => $_file) {
						unlink($animationBannerNaviDir.'/'.$_file);
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
				$upload = new upload_file($files, $animationBannerNaviDir.'/'.$imageName, 'image');
				$uploadResult = $upload->upload();
				if ($uploadResult) {
					$uploadedOffAnchorFiles[] = $imageName;
					$usingOffAnchor[$index] = $imageName;
				}
				else {
					foreach ($uploadedOffAnchorFiles as $_index => $_file) {
						unlink($animationBannerNaviDir.'/'.$_file);
					}
					msg('내비게이션 비활성 버튼에 이미지가 아닌 파일이 업로드 되었습니다.', -1);
				}
			}
			else {
				continue;
			}
		}

		// 사용할 배너 이미지를 제외하고 모두 삭제처리
		$bannerDir = opendir($animationBannerDataDir);
		while ($name = readdir($bannerDir)) {
			if ($name === '.' || $name === '..') {
				continue;
			}
			else if (in_array($name, $usingBanner)) {
				continue;
			}
			else {
				unlink($animationBannerDataDir.'/'.$name);
			}
		}
		closedir($bannerDir);

		// 사용할 내비게이션 이미지를 제외하고 모두 삭제처리
		$naviDir = opendir($animationBannerNaviDir);
		while ($name = readdir($naviDir)) {
			if ($name === '.' || $name === '..') {
				continue;
			}
			else if (in_array($name, $usingOnAnchor) || in_array($name, $usingOffAnchor)) {
				continue;
			}
			else {
				unlink($animationBannerNaviDir.'/'.$name);
			}
		}
		closedir($naviDir);

		// 설정파일 작성
		$qfile = new qfile();
		$qfile->open($configFilePath);
		$qfile->write('<?php'.PHP_EOL);
		$qfile->write('$animationBannerConfig = array();'.PHP_EOL);
		$qfile->write('$animationBannerConfig["enable"] = "'.$_POST['enable'].'";'.PHP_EOL);
		$qfile->write('$animationBannerConfig["type"] = "'.$_POST['type'].'";'.PHP_EOL);
		$qfile->write('$animationBannerConfig["duration"] = "'.$_POST['duration'].'";'.PHP_EOL);
		$qfile->write('$animationBannerConfig["directionAnchorDisplay"] = "'.$_POST['directionAnchorDisplay'].'";'.PHP_EOL);
		$qfile->write('$animationBannerConfig["shiftType"] = "'.$_POST['shiftType'].'";'.PHP_EOL);
		$qfile->write('$animationBannerConfig["interval"] = "'.$_POST['interval'].'";'.PHP_EOL);
		$qfile->write('$animationBannerConfig["width"] = "'.$_POST['width'].'";'.PHP_EOL);
		$qfile->write('$animationBannerConfig["height"] = "'.$_POST['height'].'";'.PHP_EOL);
		foreach ($usingBanner as $index => $imageName) {
			$qfile->write('$animationBannerConfig["image"]['.$index.'] = "'.$imageName.'";'.PHP_EOL);
		}
		foreach ($_POST['link'] as $index => $link) {
			$qfile->write('$animationBannerConfig["link"]['.$index.'] = "'.$link.'";'.PHP_EOL);
		}
		foreach ($_POST['target'] as $index => $target) {
			$qfile->write('$animationBannerConfig["target"]['.$index.'] = "'.$target.'";'.PHP_EOL);
		}
		$qfile->write('$animationBannerConfig["anchorDisplay"] = "'.$_POST['anchorDisplay'].'";'.PHP_EOL);
		foreach ($usingOnAnchor as $index => $imageName) {
			$qfile->write('$animationBannerConfig["onAnchor"]['.$index.'] = "'.$imageName.'";'.PHP_EOL);
		}
		foreach ($usingOffAnchor as $index => $imageName) {
			$qfile->write('$animationBannerConfig["offAnchor"]['.$index.'] = "'.$imageName.'";'.PHP_EOL);
		}
		$qfile->close();
		chmod($configFilePath, 0707);
		msg('정상적으로 저장되었습니다.', './iframe.rollbanner.php');
		break;
	case "previewUpload":
		foreach (reverse_file_array($_FILES['image']) as $index => $banner) {
			if (strlen($banner['tmp_name']) > 0) {
				$imageName = 'banner_'.sprintf('%02s', md5($index.'_'.time()));
				$upload = new upload_file($banner, $animationBannerDataDir.'/'.$imageName, 'image');
				$uploadResult = $upload->upload();
				if ($uploadResult) {
					exit('<script type="text/javascript">parent.previewImageCallback("image", '.$index.', "'.$imageName.'");</script>');
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
				$upload = new upload_file($anchor, $animationBannerNaviDir.'/'.$imageName, 'image');
				$uploadResult = $upload->upload();
				if ($uploadResult) {
					exit('<script type="text/javascript">parent.previewImageCallback("onAnchor", '.$index.', "'.$imageName.'");</script>');
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
				$upload = new upload_file($anchor, $animationBannerNaviDir.'/'.$imageName, 'image');
				$uploadResult = $upload->upload();
				if ($uploadResult) {
					exit('<script type="text/javascript">parent.previewImageCallback("offAnchor", '.$index.', "'.$imageName.'");</script>');
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