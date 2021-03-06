<?php
/* +***********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 * *********************************************************************************** */

class Calendar_Delete_Action extends Vtiger_Delete_Action
{

	public function process(\App\Request $request)
	{
		$moduleName = $request->getModule();
		$recordId = $request->getInteger('record');

		$recordModel = Vtiger_Record_Model::getInstanceById($recordId, $moduleName);
		$recordModel->delete();

		$typeRemove = Events_RecuringEvents_Model::UPDATE_THIS_EVENT;
		if (!$request->isEmpty('typeRemove')) {
			$typeRemove = $request->getInteger('typeRemove');
		}
		$recurringEvents = Events_RecuringEvents_Model::getInstance();
		$recurringEvents->typeSaving = $typeRemove;
		$recurringEvents->recordModel = $recordModel;
		$recurringEvents->templateRecordId = $recordId;
		$recurringEvents->delete();
		$listViewUrl = $recordModel->getModule()->getListViewUrl();
		if ($request->getBoolean('ajaxDelete')) {
			$response = new Vtiger_Response();
			$response->setResult($listViewUrl);
			return $response;
		} else {
			header("Location: $listViewUrl");
		}
	}
}
