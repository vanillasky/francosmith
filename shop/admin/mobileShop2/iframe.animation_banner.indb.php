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
		// ���� Ȯ���� �˻�
		foreach ($uploadFiles['name']['image'] as $index => $fileName) {
			if (strlen($fileName) > 0 && preg_match('/\.(jpg|jpeg|gif|png|bmp)$/i', $fileName) < 1) {
				msg('����̹����� ����� �� ���� ������ ���ε� �Ǿ����ϴ�.', -1);
			}
		}
		foreach ($uploadFiles['name']['onAnchor'] as $index => $fileName) {
			if (strlen($fileName) > 0 && preg_match('/\.(jpg|jpeg|gif|png|bmp)$/i', $fileName) < 1) {
				msg('������̼� Ȱ�� ��ư�� ����� �� ���� ������ ���ε� �Ǿ����ϴ�.', -1);
			}
		}
		foreach ($uploadFiles['name']['offAnchor'] as $index => $fileName) {
			if (strlen($fileName) > 0 && preg_match('/\.(jpg|jpeg|gif|png|bmp)$/i', $fileName) < 1) {
				msg('������̼� ��Ȱ�� ��ư�� ����� �� ���� ������ ���ε� �Ǿ����ϴ�.', -1);
			}
		}

		// ����� ��� �̹��� ����Ʈ
		$usingBanner = array();
		foreach ($_POST['imageURL'] as $index => $imageName) {
			$usingBanner[$index] = $imageName;
		}

		// ��� �̹��� ���ε�
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
					msg('����̹����� �̹����� �ƴ� ������ ���ε� �Ǿ����ϴ�.', -1);
				}
			}
			else {
				continue;
			}
		}

		// ����� ������̼� �̹��� ����Ʈ
		$usingOnAnchor = array();
		$usingOffAnchor = array();
		foreach ($_POST['onAnchorURL'] as $index => $imageName) {
			$usingOnAnchor[$index] = $imageName;
		}
		foreach ($_POST['offAnchorURL'] as $index => $imageName) {
			$usingOffAnchor[$index] = $imageName;
		}

		// ������̼�ON �̹��� ���ε�
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
					msg('������̼� Ȱ�� ��ư�� �̹����� �ƴ� ������ ���ε� �Ǿ����ϴ�.', -1);
				}
			}
			else {
				continue;
			}
		}

		// ������̼�OFF �̹��� ���ε�
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
					msg('������̼� ��Ȱ�� ��ư�� �̹����� �ƴ� ������ ���ε� �Ǿ����ϴ�.', -1);
				}
			}
			else {
				continue;
			}
		}

		// ����� ��� �̹����� �����ϰ� ��� ����ó��
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

		// ����� ������̼� �̹����� �����ϰ� ��� ����ó��
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

		// �������� �ۼ�
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
		msg('���������� ����Ǿ����ϴ�.', './iframe.animation_banner.php');
		break;
	case "previewUpload":
		foreach (reverse_file_array($_FILES['image']) as $index => $banner) {
			if (strlen($banner['tmp_name']) > 0) {
				if($cfg['tplSkinMobileWork'] == '' || $mobileAnimationBannerDataDir == '') {
					msg('����ϼ� > ����ϼ� �����ΰ������� [�۾���Ų]�� [��뽺Ų]�� ������ �ּ���.', -1);
				}
				$imageName = 'banner_'.sprintf('%02s', md5($index.'_'.time()));
				$upload = new upload_file($banner, $mobileAnimationBannerDataDir.'/'.$imageName, 'image');
				$uploadResult = $upload->upload();

				if(file_exists($mobileAnimationBannerDataDir.'/'.$imageName) === false) {
					msg('�̹��� ���ε尡 ���еǾ����ϴ�.', -1);
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
					msg('����̹����� �̹����� �ƴ� ������ ���� �Ǿ����ϴ�.', -1);
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
					msg('Ȱ�� ��ư�� �̹����� �ƴ� ������ ���� �Ǿ����ϴ�.', -1);
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
					msg('��Ȱ�� ��ư�� �̹����� �ƴ� ������ ���� �Ǿ����ϴ�.', -1);
				}
			}
			else {
				continue;
			}
		}
		break;
}