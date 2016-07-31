<?php

try {

	include dirname(__FILE__).'/../lib.php';
	include dirname(__FILE__).'/../../lib/Widget/AdminWidget/AdminWidgetLoader.php';

	$action = $_POST['Action'];
	$parameter = $_POST['Parameter'];

	switch ($action) {

		// 위젯 표면정보 조회 API 호출
		case 'GetWidgetSurfaceInfo':
			echo $adminWidgetService->getWidgetSurfaceInfo($parameter);
			break;

		// 위젯 스테이지 로드
		case 'LoadWidgetStage':
			// 업데이트에 실패하더라도 서비스는 정상작동 해야하기때문에, 별도로 예외처리함
			try {
				$adminWidgetService->updateDownloadedWidget();
			}
			catch (WidgetAPIRequestException $exception) {
				$this->exception($exception->getErrorCode(), $exception->getErrorMessage(), array(
				    'RequestData' => $exception->getRequest()->toArray(),
				    'Stacktrace' => $exception->getTraceAsString()
				));
			}
			catch (Exception $exception) {
				$this->exception($exception->getCode(), $exception->getMessage(), $exception->getTrace());
			}
			echo $adminWidgetService->loadWidgetStage($parameter);
			break;

		// 위젯 스테이지 저장
		case 'SaveWidgetStage':
			echo $adminWidgetService->saveWidgetStage($parameter);
			break;

		// 위젯 스크립트 로드
		case 'LoadWidgetScript':
			echo $adminWidgetService->loadWidgetScript($parameter);
			break;

		// 위젯 초기셋팅 리스트 조회
		case 'GetInitializeWidgetList':
			echo $adminWidgetService->getInitializeWidgetList($parameter);
			break;

		// 미리보기용 위젯 스테이지 로드
		case 'PreviewWidgetStage':
			echo $adminWidgetService->getPreviewWidgetStage($parameter);
			break;
	}
}
catch (WidgetAPIRequestException $exception) {
	echo $adminWidgetService->exception($exception->getErrorCode(), $exception->getErrorMessage(), array(
	    'RequestData' => $exception->getRequest()->toArray(),
	    'Stacktrace' => $exception->getTraceAsString()
	));
}
catch (WidgetAPIResponseParseException $exception) {
	echo $adminWidgetService->exception($exception->getCode(), $exception->getMessage(), array(
	    'ParseContent' => $exception->getParseContent(),
	    'Stacktrace' => $exception->getTraceAsString()
	));
}
catch (Exception $exception) {
	echo $adminWidgetService->exception($exception->getCode(), $exception->getMessage(), $exception->getTrace());
}