<?php

require_once (t3lib_extMgm::extPath('caretaker') . '/classes/repositories/class.tx_caretaker_NodeRepository.php');
require_once (t3lib_extMgm::extPath('caretaker') . '/classes/class.tx_caretaker_Helper.php');
require_once (t3lib_extMgm::extPath('nxcaretakerservices') . '/classes/nodes/class.tx_caretaker_ActionNode.php');

class tx_nxcaretakerservices_Action {
	
	public function doaction($params, &$ajaxObj){
		
		$result="";
		$node_id = t3lib_div::_GP('node');
		if ($node_id && $node = tx_caretaker_Helper::id2node($node_id) ){
			
			$serviceText = t3lib_div::_GP('service');
			$service = t3lib_div::makeInstanceService('caretaker_test_service', $serviceText);
			$service->setInstance( $node->getInstance() );
		
			$method = t3lib_div::_GP('method');
			if($method)
			{
				$result = $service->doAction($method);				
			}
			else $result = $service->getView($serviceText);
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
		$resp= t3lib_div::_GP('resp');
		if(!$resp) $resp = "no"; 
		$result = '[';
		
		if ($node_id && $node = tx_caretaker_Helper::id2node($node_id) ){

			$currentInstance = $node->getInstance();
			$actions = $this->getActionsByInstanceUid($currentInstance->getUid(), false, false);
			
			foreach ($actions as $action){
				
			if(	$result != '[') $result = $result . ',';
			$result = $result . '
			{ 
			text	: "' . $action->getTitle() . '",
			icon    : "../res/icons/pencil.png",
			handler :   function (){								
        						Ext.Ajax.request({
           							url: tx.caretaker.back_path + "ajax.php",
           							success : function (response, opts){											
      									
           								var node_info_panel = Ext.getCmp("node-info-action");
        								node_info_panel.load( tx.caretaker.back_path + "ajax.php?ajaxID=tx_nxcaretakerservices::actioninfo&node=" + tx.caretaker.node_info.id + "&action='.$action->getUid().'");
        								
        								var jsonData = Ext.util.JSON.decode(response.responseText);
        								var node_added_panel = Ext.getCmp("node-added-action");
        								
        								node_added_panel.getBottomToolbar().removeAll();
        								node_added_panel.getBottomToolbar().addButton(
        								{			
											text	:	"refresh",
											icon    : 	"../res/icons/arrow_refresh_small.png",
											handler	:	function (){
         										var node_info_panel = Ext.getCmp("node-info-action");
        										node_info_panel.load( tx.caretaker.back_path + "ajax.php?ajaxID=tx_nxcaretakerservices::actioninfo&node=" + tx.caretaker.node_info.id + "&action='.$action->getUid().'");
        										}
										});
										
										node_added_panel.getBottomToolbar().addButton(jsonData);
										node_added_panel.doLayout();        								       								       								
    									}     , 
           							params: { 
               							ajaxID: "tx_nxcaretakerservices::doaction",
               							node:   tx.caretaker.node_info.id,
               							service:   "'.$action->getServiceType().'"               							               							             							
            								}
        							});
    						}
			}';					
			}
		}			
		echo $result.']';	
	}
		
	
	public function ajaxGetNodeInfo($params, &$ajaxObj){
		
			$node_id = t3lib_div::_GP('node');
			$action =  t3lib_div::_GP('action');

		if (!$action && $node_id && $node = tx_caretaker_Helper::id2node($node_id) ){

			$local_time = localtime(time(), true);
			$local_hour = $local_time['tm_hour'];

			switch ( get_class($node) ){
				case "tx_caretaker_TestNode":

					$interval_info = '';
					$interval = $node->getInterval();
					if ( $interval < 60){
						$interval_info .= $interval.' Seconds';
					} else if ($interval < 60*60){
						$interval_info .= ($interval/60).' Minutes';
					} else if ($interval < 60*60*60){
						$interval_info .= ($interval/(60*60)).' Hours';
					} else {
						$interval_info .= ($interval/86400).' Days';
					}

					if ($node->getStartHour() || $node->getStopHour() >0){
						$interval_info .= ' [';
						if ($node->getStartHour() )
							$interval_info .= ' after:'.$node->getStartHour();
						if ($node->getStopHour() )
							$interval_info .= ' before:'.$node->getStopHour();
						$interval_info .= ' ]';
					}

					$result = $node->getTestResult();
					$info = '<div class="tx_caretaker_node_info tx_caretaker_node_info_state_'.$result->getStateInfo().'">'.
						'Title:'.$node->getTitle().'<br/>'.
						'Description: '.$node->getDescription().'<br/>'.
						'Interval: '.$interval_info.'<br/>'.
						'Hidden: '.$node->getHidden().'<br/>'.
						'last Execution: '.strftime('%x %X',$result->getTimestamp()).'<br/>'.
						'State: '.$result->getLocallizedStateInfo().'<br/>'.
						'Value: '.$result->getValue().'<br/>'.
						'Message: <br/>'.nl2br( str_replace( ' ' , '&nbsp;', $result->getLocallizedMessage() ) ) .'<br/>'.
						'</div>';
					break;
				default:
					$result = $node->getTestResult();
					$info = '<div class="tx_caretaker_node_info tx_caretaker_node_info_state_'.$result->getStateInfo().'">'.
						'Title: '.$node->getTitle().'<br/>'.
						'Description: '.$node->getDescription().'<br/>'.
						'Hidden: '.$node->getHidden().'<br/>'.
						'last Execution: '.strftime('%x %X',$result->getTimestamp()).'<br/>'.
						'State: '.$result->getLocallizedStateInfo().'<br/>'.
						'Message: '.nl2br($result->getLocallizedMessage()).'<br/>'.
						'</div>';
						
					break;
				}

			echo $info;

		} else {
			if($action && $node = tx_caretaker_Helper::id2node($node_id))
			{				
				if(!$resp) $resp = 'none';
				$actionNode = $this->getActionsByUid ($action , $node->getInstance());
				$result = $actionNode->updateTestResult(true);
					$info = '<div class="tx_caretaker_node_info tx_caretaker_node_info_state_'.$result->getStateInfo().'">'.
						'Title: '.$actionNode->getTitle().'<br/>'.
						'Description: '.$actionNode->getDescription().'<br/>'.
						'Hidden: '.$actionNode->getHidden().'<br/>'.						
						'last Execution: '.strftime('%x %X',$result->getTimestamp()).'<br/>'.
						'State: '.$result->getLocallizedStateInfo().'<br/>'.
						'Message: '.nl2br($result->getLocallizedMessage()).'<br/>'.
						'</div>';
					echo $info;
			}
			else echo "please selext a node";

		}
		
	}

	public function ajaxRefreshNode($params, &$ajaxObj){

		$node_id = t3lib_div::_GP('node');
		$force   = (boolean)t3lib_div::_GP('force');

		if ($node_id && $node = tx_caretaker_Helper::id2node($node_id) ){

			require_once (t3lib_extMgm::extPath('caretaker').'/classes/class.tx_caretaker_MemoryLogger.php');
			$logger  = new tx_caretaker_MemoryLogger();

			$node->setLogger($logger);
			$node->updateTestResult($force);

			echo nl2br($logger->getLog());
			
		} else {
			echo "please give a valid node id";
		}
	}

	public function ajaxGetNodeGraph($params, &$ajaxObj){

		$node_id    = t3lib_div::_GP('node');
		
		$duration   = (int)t3lib_div::_GP('duration');
		$date_stop  = time();
		$date_start = $date_stop - $duration;

		if ($node_id && $node = tx_caretaker_Helper::id2node($node_id) ){

			require_once (t3lib_extMgm::extPath('caretaker').'/classes/class.tx_caretaker_ResultRangeRenderer_pChart.php');

			$result_range = $node->getTestResultRange($date_start , $date_stop);

			if ($result_range->count() > 1 ){
				$filename = 'typo3temp/caretaker/charts/'.$node_id.'_'.$duration.'.png';
				$renderer = tx_caretaker_ResultRangeRenderer_pChart::getInstance();
				$base_url = t3lib_div::getIndpEnv('TYPO3_SITE_URL');

				if (is_a($node, 'tx_caretaker_TestNode' ) ){
					if ($renderer->renderTestResultRange(PATH_site.$filename, $result_range , $node->getTitle(), $node->getValueDescription()) !== false) {
						echo '<img src="'.$base_url.$filename.'?random='.rand().'" />';
					}
				} else  if (is_a( $node, 'tx_caretaker_AggregatorNode')){
					if ($renderer->renderAggregatorResultRange(PATH_site.$filename, $result_range , $node->getTitle()) !== false) {
						echo '<img src="'.$base_url.$filename.'?random='.rand().'" />';
					}
				}
			} else {
				echo 'not enough results';
			}

		} else {
			echo "please give a valid node id";
		}
	}

    public function ajaxGetNodeLog ($params, &$ajaxObj){

        $node_id = t3lib_div::_GP('node');

        if ($node_id && $node = tx_caretaker_Helper::id2node($node_id) ){
            
            $start     = (int)t3lib_div::_GP('start');
            $limit     = (int)t3lib_div::_GP('limit');

            $count   = $node->getTestResultNumber();
            $results = $node->getTestResultRangeByOffset($start, $limit);
            
            $content = Array(
                'totalCount' => $count,
                'logItems' => Array()
            );

            $logItems = array();
            foreach ($results as $result){
                $logItems[] = Array (
                    'num'=> ($i+1) ,
                    'title'=>'title_'.rand(),
                    'timestamp' => $result->getTimestamp(),
                    'stateinfo' => $result->getStateInfo(),
                    'message'   => $result->getMessage(),
                    'state'     => $result->getState(),
                );
            }
            $content['logItems'] = array_reverse($logItems);


            $ajaxObj->setContent($content);
            $ajaxObj->setContentFormat('jsonbody');
        }
    }
	
	
}
?>