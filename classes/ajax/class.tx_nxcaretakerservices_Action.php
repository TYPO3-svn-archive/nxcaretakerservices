<?php

require_once (t3lib_extMgm::extPath('caretaker') . '/classes/repositories/class.tx_caretaker_NodeRepository.php');
require_once (t3lib_extMgm::extPath('caretaker') . '/classes/class.tx_caretaker_Helper.php');
require_once (t3lib_extMgm::extPath('nxcaretakerservices') . '/classes/nodes/class.tx_caretaker_ActionNode.php');

class tx_nxcaretakerservices_Action {
	
	public function doaction($params, &$ajaxObj){
		
		$result="";
		$node_id = t3lib_div::_GP('node');
		$action_id = t3lib_div::_GP('actionid');
	
		if ($node_id && $node = tx_caretaker_Helper::id2node($node_id)  ){
			 	
			
			$serviceText = t3lib_div::_GP('service');
			if($serviceText){			
				$service = t3lib_div::makeInstanceService('nxcaretakerservices_action_service', $serviceText);
				$action = $this->getActionsByUid($action_id, $node->getInstance());
				if($action)	$service->setConfiguration($action->getServiceConfiguration());
				$service->setInstance( $node->getInstance() );
			}
			$method = t3lib_div::_GP('method');
			if($method)
			{
				$result = $service->doAction($params, $ajaxObj);				
			}
			else $result = $service->getView($params, $ajaxObj);//$serviceText, $actionid);
		}
	echo $result;
	}

	public function getActionsByInstanceUid ($instance_id, $parent = false, $show_hidden = FALSE){
		$ids = array();
		$res = $GLOBALS['TYPO3_DB']->exec_SELECTquery('uid_local', 'tx_nxcaretakerservices_instance_action_mm', 'uid_foreign='.(int)$instance_id, '' , 'sorting_foreign');
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
	
	public function getActionsByUid ($uid, $parent = false, $show_hidden = FALSE){
		$hidden = '';
		if (!$show_hidden) {
			$hidden = ' AND hidden=0 ';
		} 
		$res = $GLOBALS['TYPO3_DB']->exec_SELECTquery('*', 'tx_caretaker_action', 'deleted=0 '.$hidden.' AND uid='.(int)$uid);
		$row = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($res);
		if ($row){
			return $this->dbrow2test($row, $parent);
		}
		return false;
	}
	
	private function dbrow2test($row, $parent = false){
		$instance = new tx_caretaker_ActionNode( $row['uid'], $row['title'], $parent, $row['test_service'], $row['test_conf'], $row['test_interval'], $row['test_interval_start_hour'], $row['test_interval_stop_hour'] , $row['hidden']);
		if ($row['notifications'] ) $instance->setNotificationIds(explode(',', $row['notifications'] ) );
		if ($row['description'] )   $instance->setDescription( $row['description'] );
		return $instance; 
	}
	
public function ajaxGetActionButtons($params, &$ajaxObj){
		
		$node_id = t3lib_div::_GP('node');
		$back_path = t3lib_div::_GP('back_path');
	
		$result = '[';
		
		if ($node_id && $node = tx_caretaker_Helper::id2node($node_id) ){

			$currentInstance = $node->getInstance();
			if(!$currentInstance) $result = $result . '{text:"Please select an instance."}';
			else{
				$actions = $this->getActionsByInstanceUid($currentInstance->getUid(), false, false);
				
				foreach ($actions as $action){
					
					if(	$result != '[') $result = $result . ',';
					$result = $result . '
					{ 
					text	: 	"' . $action->getTitle() . '",
					icon    : 	"../res/icons/test.png",
					handler :   function (){		
								            									
	           				var nxparams = new Array();
	           				nxparams["md5src"] = "' . $back_path . t3lib_extMgm::extRelPath('nxcaretakerservices') . 'classes/ajax/md5.js";
	           				nxparams["ext_expander"] = "' . $back_path . t3lib_extMgm::extRelPath('nxcaretakerservices') . 'classes/ajax/ext_expander.js";
	           				nxparams["ajaxLoadingImg"] = "'.$back_path.'"+"'.t3lib_iconWorks::skinImg('', 'sysext/t3skin/extjs/images/grid/loading.gif', '', 1).'";
	           				nxparams["garbageImg"] = "'.$back_path.'"+"'.t3lib_iconWorks::skinImg('', 'gfx/garbage.gif', '', 1).'";
	           				nxparams["unhideImg"] = "'.$back_path.'"+"'.t3lib_iconWorks::skinImg('', 'gfx/button_unhide.gif', '', 1).'";
	           				nxparams["hideImg"] = "'.$back_path.'"+"'.t3lib_iconWorks::skinImg('', 'gfx/button_hide.gif', '', 1).'";
	           				nxparams["adminImg"] = "'.$back_path.'"+"'.t3lib_iconWorks::skinImg('', 'gfx/i/be_users_admin.gif', '', 1).'";
	           				nxparams["userImg"] = "'.$back_path.'"+"'.t3lib_iconWorks::skinImg('', 'gfx/i/be_users.gif', '', 1).'";
	           				nxparams["addImg"] = "'.$back_path.'"+"'.t3lib_iconWorks::skinImg('', 'gfx/new_el.gif', '', 1).'";
	           				
	           				var successFunction = function(response) {								
								eval(response.responseText);
	           					'.str_replace('_','.',$action->getServiceType()).'("'.$back_path.'", "'.$node_id.'", "'.$action->getServiceType().'", "'.$action->getUid().'", nxparams);
							};
	           				
							Ext.Ajax.request({url: "' . $back_path . t3lib_extMgm::extRelPath('nxcaretakerservices') . 'classes/services/'.$action->getServiceType().'.js", success : successFunction});
	           					           				
	    				}
					}';					
				}
			}
		}			
		echo $result.']';	
	}
		
	
	public function ajaxGetNodeInfo($params, &$ajaxObj){
			
			$node_id = t3lib_div::_GP('node');
			$action_id =  t3lib_div::_GP('actionid');
			$node = tx_caretaker_Helper::id2node($node_id);
			$action = $this->getActionsByUid($action_id, $node->getInstance());
			if($node  && $action)
			{				
			$result = $action->updateTestResult(true);
			$info = '<div>'.
					'State: '.$result->getLocallizedMessage().'<br/>'.					
					'</div>';
			
			echo $info;
			}
			else echo "please selext a node";
	}
	

}
?>