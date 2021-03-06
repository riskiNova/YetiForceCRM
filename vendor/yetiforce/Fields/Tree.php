<?php
/**
 * Tree
 * @package YetiForce.App
 * @copyright YetiForce Sp. z o.o.
 * @license YetiForce Public License 2.0 (licenses/License.html or yetiforce.com)
 * @author Mariusz Krzaczkowski <m.krzaczkowski@yetiforce.com>
 */
namespace App\Fields;

/**
 * Tree class
 */
class Tree
{

	/**
	 * Get tree values by ID
	 * @param int $templateId
	 * @return array[]
	 */
	public static function getValuesById($templateId)
	{
		if (\App\Cache::has('TreeValuesById', $templateId)) {
			return \App\Cache::get('TreeValuesById', $templateId);
		}
		$rows = (new \App\Db\Query())
				->from('vtiger_trees_templates_data')
				->where(['templateid' => $templateId])->indexBy('tree')->all();
		\App\Cache::save('TreeValuesById', $templateId, $rows, \App\Cache::MEDIUM);
		return $rows;
	}

	/**
	 * Get tree values by tree ID
	 * @param int $templateId
	 * @param string $tree
	 * @return array[]
	 */
	public static function getValueByTreeId($templateId, $tree)
	{
		$rows = static::getValuesById($templateId);
		return $rows[$tree];
	}

	/**
	 * Get picklist values
	 * @param int $templateId
	 * @param string $moduleName
	 * @return string[]
	 */
	public static function getPicklistValue($templateId, $moduleName)
	{
		$values = [];
		$dataTree = self::getValuesById((int) $templateId);
		foreach ($dataTree as $row) {
			$tree = $row['tree'];
			$parent = '';
			$parentName = '';
			if ($row['depth'] > 0) {
				$parentTrre = $row['parenttrre'];
				$cut = strlen('::' . $tree);
				$parentTrre = substr($parentTrre, 0, - $cut);
				$pieces = explode('::', $parentTrre);
				$parent = end($pieces);
				$parentName = $dataTree[$parent]['name'];
				$parentName = '(' . \App\Language::translate($parentName, $moduleName) . ') ';
			}
			$values[$row['tree']] = $parentName . \App\Language::translate($row['name'], $moduleName);
		}
		return $values;
	}

	/**
	 * Delete trees of the module
	 * @param int $moduleId
	 */
	public static function deleteForModule($moduleId)
	{
		$db = \App\Db::getInstance();
		$db->createCommand()->delete('vtiger_trees_templates', ['module' => $moduleId])->execute();
	}
}
