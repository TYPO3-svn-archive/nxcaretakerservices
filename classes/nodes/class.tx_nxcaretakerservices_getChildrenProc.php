<?php

/***************************************************************
 *  Copyright notice
 *
 *  (c) 2008 Matthias Elbert <matthias.elbert@netlogix.de>
 *  All rights reserved
 *
 *  This script is part of the TYPO3 project. The TYPO3 project is
 *  free software; you can redistribute it and/or modify
 *  it under the terms of the GNU General Public License as published by
 *  the Free Software Foundation; either version 2 of the License, or
 *  (at your option) any later version.
 *
 *  The GNU General Public License can be found at
 *  http://www.gnu.org/copyleft/gpl.html.
 *
 *  This script is distributed in the hope that it will be useful,
 *  but WITHOUT ANY WARRANTY; without even the implied warranty of
 *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *  GNU General Public License for more details.
 *
 *  This copyright notice MUST APPEAR in all copies of the script!
 ***************************************************************/

require_once (t3lib_extMgm::extPath('nxcaretakerservices').'/classes/nodes/class.tx_caretaker_ActionNode.php');

class tx_nxcaretakerservices_getChildrenProc {
	
	public function actionNodeGetChildren( &$params ){
		$parentNodeUid = $params['uid'];
		$parentNode = $params['parent'];
		$children = $params['children'];
		
		
		$actions = $this->getActionsByGroupUid($parentNodeUid, $parentNode, FALSE);	
		
	
		$params['children'] = array_merge($params['children'], $actions);
			
		
	}	
	
	private function getActionsByGroupUid ($group_id, $parent = false, $show_hidden = FALSE){
		$ids = array();
		$res = $GLOBALS['TYPO3_DB']->exec_SELECTquery('uid_local', 'tx_caretaker_testgroup_test_mm', 'uid_foreign='.(int)$group_id , '' , 'sorting_foreign');
		while ($row = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($res) ){
			$ids[] = $row['uid_local'];
		}
		$actions = array();
		foreach ($ids as $uid){
			$item = $this->getActionsByUid($uid,$parent,$show_hidden);
			if ($item){
				$actions[]=$item;
			}
		}
		return $actions;
	}
	
	private function getActionsByUid ($uid, $parent = false, $show_hidden = FALSE){
		$hidden = '';
		if (!$show_hidden) {
			$hidden = ' AND hidden=0 ';
		} 
		$res = $GLOBALS['TYPO3_DB']->exec_SELECTquery('*', 'tx_caretaker_action', 'deleted=0 '.$hidden.' AND uid='.(int)$uid);
		$row = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($res);
		if ($row){
			return $this->dbrow2action($row, $parent);
		}
		return false;
	}
	
	private function dbrow2action($row, $parent = false){
		$instance = new tx_caretaker_ActionNode( $row['uid'], $row['title'], $parent, $row['test_service'], $row['test_conf'], $row['test_interval'], $row['test_interval_start_hour'], $row['test_interval_stop_hour'] , $row['hidden']);
		if ($row['notifications'] ) $instance->setNotificationIds(explode(',', $row['notifications'] ) );
		if ($row['description'] )   $instance->setDescription( $row['description'] );
		return $instance; 
	}
}
?>