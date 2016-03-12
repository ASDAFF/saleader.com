<?php
use Bitrix\Lists\Internals\Error\Error;
use Bitrix\Main\Localization\Loc;
use Bitrix\Main\Loader;
use Bitrix\Lists\Internals\Controller;

define('STOP_STATISTICS', true);
define('BX_SECURITY_SHOW_MESSAGE', true);

require_once($_SERVER['DOCUMENT_ROOT'].'/bitrix/modules/main/include/prolog_before.php');

if (!Loader::includeModule('lists') || !\Bitrix\Main\Application::getInstance()->getContext()->getRequest()->getQuery('action'))
{
	return;
}

Loc::loadMessages(__FILE__);

class ListAjaxController extends Controller
{
	/** @var  string or int */
	protected $listPerm;
	/** @var  string */
	protected $iblockTypeId;
	/** @var int */
	protected $iblockId;
	protected $socnetGroupId;
	protected $sectionId;
	/** @var array */
	protected $documentStates;

	protected function listOfActions()
	{
		return array(
			'performActionBp' => array(
				'method' => array('POST'),
			),
		);
	}

	protected function processActionPerformActionBp()
	{
		if(!Loader::includeModule('bizproc'))
		{
			$this->errorCollection->add(array(new Error(Loc::getMessage('LISTS_LAC_MODULE_NOT_INSTALLED'))));
		}
		$this->checkRequiredPostParams(array('iblockTypeId', 'iblockId', 'sectionId', 'workflowId', 'elementId', 'action'));
		if($this->request->getPost('iblockTypeId') == COption::getOptionString('lists', 'livefeed_iblock_type_id'))
		{
			$this->errorCollection->add(array(new Error(Loc::getMessage('LISTS_LAC_UNKNOWN_ERROR'))));
		}
		if($this->errorCollection->hasErrors())
		{
			$this->sendJsonErrorResponse();
		}

		$this->iblockTypeId = $this->request->getPost('iblockTypeId');
		$this->iblockId = $this->request->getPost('iblockId');
		$this->sectionId = $this->request->getPost('sectionId');
		$workflowId = $this->request->getPost('workflowId');
		$elementId = $this->request->getPost('elementId');
		$action = $this->request->getPost('action');
		$this->documentStates = CBPDocument::getDocumentStates(
			BizProcDocument::generateDocumentComplexType($this->iblockTypeId, $this->iblockId),
			BizProcDocument::getDocumentComplexId($this->iblockTypeId, $elementId)
		);

		if(isset($this->documentStates[$workflowId]['WORKFLOW_STATUS']) && $this->documentStates[$workflowId]['WORKFLOW_STATUS'] !== null)
		{
			$this->terminateWorkflow($workflowId, $elementId);
		}

		if($action == 'delete')
		{
			if(CBPDocument::canUserOperateDocument(
				CBPCanUserOperateOperation::CreateWorkflow,
				$this->getUser(),
				BizProcDocument::getDocumentComplexId($this->iblockTypeId, $elementId),
				array("DocumentStates" => $this->documentStates)
				)
			)
			{
				CBPTaskService::DeleteByWorkflow($workflowId);
				CBPTrackingService::DeleteByWorkflow($workflowId);
				CBPStateService::DeleteWorkflow($workflowId);
			}
			else
			{
				$this->errorCollection->add(array(new Error(Loc::getMessage('LISTS_LAC_ACCESS_DENIED'))));
			}
			if($this->errorCollection->hasErrors())
			{
				$this->sendJsonErrorResponse();
			}
		}

		$this->sendJsonSuccessResponse(array('message' => Loc::getMessage('LISTS_LAC_MESSAGE_SUCCESS')));
	}

	protected function terminateWorkflow($workflowId, $elementId)
	{
		$this->checkPermission();
		if(!CBPDocument::canUserOperateDocument(
			CBPCanUserOperateOperation::StartWorkflow,
			$this->getUser(),
			BizProcDocument::getDocumentComplexId($this->iblockTypeId, $elementId),
			array("DocumentStates" => $this->documentStates)
		)
		)
		{
			$this->errorCollection->add(array(new Error(Loc::getMessage('LISTS_LAC_ACCESS_DENIED'))));
		}
		if($this->errorCollection->hasErrors())
		{
			$this->sendJsonErrorResponse();
		}

		if (CIBlockElementRights::userHasRightTo($this->iblockId, $elementId, "element_rights_edit"))
		{
			$errors = array();
			CBPDocument::terminateWorkflow(
				$workflowId,
				BizProcDocument::getDocumentComplexId($this->iblockTypeId, $elementId),
				$errors
			);
			foreach($errors as $error)
			{
				$this->errorCollection->add(array(new Error($error["message"])));
			}
		}
		else
		{
			$this->errorCollection->add(array(new Error(Loc::getMessage('LISTS_LAC_ACCESS_DENIED'))));
		}
		if($this->errorCollection->hasErrors())
		{
			$this->sendJsonErrorResponse();
		}
	}

	protected function checkPermission()
	{
		global $USER;
		$this->listPerm = CListPermissions::checkAccess(
			$USER,
			$this->iblockTypeId,
			$this->iblockId,
			$this->socnetGroupId
		);
		if($this->listPerm < 0)
		{
			switch($this->listPerm)
			{
				case CListPermissions::WRONG_IBLOCK_TYPE:
					$this->errorCollection->add(array(new Error(Loc::getMessage('LISTS_LAC_WRONG_IBLOCK_TYPE'))));
					break;
				case CListPermissions::WRONG_IBLOCK:
					$this->errorCollection->add(array(new Error(Loc::getMessage('LISTS_LAC_WRONG_IBLOCK'))));
					break;
				case CListPermissions::LISTS_FOR_SONET_GROUP_DISABLED:
					$this->errorCollection->add(array(new Error(Loc::getMessage('LISTS_LAC_SONET_GROUP_DISABLED'))));
					break;
				default:
					$this->errorCollection->add(array(new Error(Loc::getMessage('LISTS_LAC_UNKNOWN_ERROR'))));
					break;
			}
		}
		elseif(
			$this->listPerm < CListPermissions::CAN_READ && !(
				CIBlockRights::userHasRightTo($this->iblockId, $this->iblockId, "element_read") ||
				CIBlockSectionRights::userHasRightTo($this->iblockId, $this->sectionId, "section_element_bind")
			)
		)
		{
			$this->errorCollection->add(array(new Error(Loc::getMessage('LISTS_LAC_ACCESS_DENIED'))));
		}
	}
}
$controller = new ListAjaxController();
$controller
	->setActionName(\Bitrix\Main\Application::getInstance()->getContext()->getRequest()->getQuery('action'))
	->exec();