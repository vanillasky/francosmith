<?php

try {

	include dirname(__FILE__).'/../lib.php';
	include dirname(__FILE__).'/../../lib/Widget/AdminWidget/AdminWidgetLoader.php';

	$action = $_POST['Action'];
	$parameter = $_POST['Parameter'];

	switch ($action) {

		// ���� ǥ������ ��ȸ API ȣ��
		case 'GetWidgetSurfaceInfo':
			echo $adminWidgetService->getWidgetSurfaceInfo($parameter);
			break;

		// ���� �������� �ε�
		case 'LoadWidgetStage':
			// ������Ʈ�� �����ϴ��� ���񽺴� �����۵� �ؾ��ϱ⶧����, ������ ����ó����
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

		// ���� �������� ����
		case 'SaveWidgetStage':
			echo $adminWidgetService->saveWidgetStage($parameter);
			break;

		// ���� ��ũ��Ʈ �ε�
		case 'LoadWidgetScript':
			echo $adminWidgetService->loadWidgetScript($parameter);
			break;

		// ���� �ʱ���� ����Ʈ ��ȸ
		case 'GetInitializeWidgetList':
			echo $adminWidgetService->getInitializeWidgetList($parameter);
			break;

		// �̸������ ���� �������� �ε�
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