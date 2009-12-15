<?php
/***************************************************************
*  Copyright notice
*
*  (c) 2009 Christopher Hlubek (hlubek@networkteam.com)
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

require_once(t3lib_extMgm::extPath('caretaker_instance', 'classes/class.tx_caretakerinstance_OperationResult.php'));


/**
 * An Operation that returns the first record matched by a field name and value as an array (excluding protected record details like be_user password).
 * This operation should be SQL injection safe. The table has to be mapped in the TCA.
 * 
 * @author Christopher Hlubek <hlubek@networkteam.com>
 * @package		TYPO3
 * @subpackage	tx_caretakerinstance
 */
class tx_nxcaretakerservices_Operation_GetBeusers implements tx_caretakerinstance_IOperation {

	/**
	 * An array of tables and table fields that should be cleared before
	 * sending.
	 * @var array
	 */
	protected $protectedFieldsByTable = array(
		'be_users' => array('password', 'uc'),
		'fe_users' => array('password')
	);

	protected $implicitFields = array('uid', 'pid', 'deleted', 'hidden');
	
	/**
	 * 
	 * @param array $parameter A table 'table', field name 'field' and the value 'value' to find the record 
	 * @return The first found record as an array or FALSE if no record was found
	 */
	public function execute($parameter = array()) {
		
		
		$table = 'be_users';
		$field = 'username';
		
		$this->includeTCA();
		
		if (!isset($GLOBALS['TCA'][$table])) {
			return new tx_caretakerinstance_OperationResult(FALSE, 'Table [' . $table . '] not found in the TCA');
		}
		t3lib_div::loadTCA($table);
		if (!isset($GLOBALS['TCA'][$table]['columns'][$field]) ) {
			return new tx_caretakerinstance_OperationResult(FALSE, 'Field [' . $field . '] of table [' . $table . '] not found in the TCA');
		}
		
		require_once (PATH_t3lib."class.t3lib_userauthgroup.php");
		$GLOBALS['BE_USER'] = t3lib_div::makeInstance('t3lib_beUserAuth');
		$GLOBALS['BE_USER']->user['admin'] = true;
		
		$tce = t3lib_div::makeInstance('t3lib_TCEmain');							
		$tce->stripslashes_values = 0;		
		
		if($action = $parameter['action'])
		{
			$ids = Explode(',',$parameter['ids']);
			if($action == 'enable')
			{				
				foreach($ids as $id)
				{					
					$data = array($table => array($id=>array('disable'=>0)));
				
			    	$tce->start($data,array());				
			    	$tce->process_datamap();
				}
			}
			if($action == 'disable')
			{				
				foreach($ids as $id)
				{					
					$data = array($table => array($id=>array('disable'=>1)));
				
			    	$tce->start($data,array());				
			    	$tce->process_datamap();
				}
			}
			if($action == 'enableAdmin')
			{				
				foreach($ids as $id)
				{					
					$data = array($table => array($id=>array('admin'=>1)));
				
			    	$tce->start($data,array());				
			    	$tce->process_datamap();
				}
			}
			if($action == 'disableAdmin')
			{	

				foreach($ids as $id)
				{					
					$data = array($table => array($id=>array('admin'=>0)));
				
			    	$tce->start($data,array());				
			    	$tce->process_datamap();
				}
			}
			if($action == 'delete')
			{				
				foreach($ids as $id)
				{
					$cmd = array($table => array($id=>array('delete'=>1)));
				
			    	$tce->start(array(), $cmd);				
			    	$tce->process_cmdmap();
				}
			}
			if($action == 'add')
			{	
				$username = $parameter['params']['addusername'];
				$password = $parameter['params']['addpassword'];
				$name = $parameter['params']['addname'];
				$email = $parameter['params']['addemail'];
				
				$data = array($table => array('NEW'=>array( "pid" => "0", 'username'=>$username,'password'=>$password,'realName'=>$name,'email'=>$email)));	
				
				$tce->start($data,array());				
		    	$tce->process_datamap();
			}
			if($action == 'reset')
			{					
				$password = $parameter['params'];
				foreach($ids as $id)
				{
					$data = array($table => array($id=>array('password'=>$password)));
					
					$tce->start($data,array());				
		    		$tce->process_datamap();
				}
			}
			if($action == 'login')
			{				
				$uid = $parameter['params']['uid'];
				$session = $parameter['params']['session'];
				$ip = $parameter['params']['ip'];
				$sessionid = $parameter['params']['sessionid'];
				$hashlock = $hashStr.=':'.t3lib_div::getIndpEnv('HTTP_USER_AGENT');
				$hashlock = t3lib_div::md5int($hashlock);
				
				$insertFields = array(
						'ses_id' => $session,
						'ses_name' => 'be_typo_user',
						'ses_iplock' => $ip,
						'ses_hashlock' => $hashlock,
						'ses_userid' => $uid,
						'ses_tstamp' => $GLOBALS['EXEC_TIME']
				);
	
				$GLOBALS['TYPO3_DB']->exec_INSERTquery('be_sessions', $insertFields);
				
				setcookie('be_typo_user', $session, 0, '/~elbert/netlogix/typo3/');
					//print_r($insertFields);
					//die;	
				header("Location: http://dev3.internal.netlogix.de/~elbert/netlogix/typo3/");
				
			}	
				
			return new tx_caretakerinstance_OperationResult(TRUE,'ids are '.$action);
		}		
		else
		{
		
			$result = $GLOBALS['TYPO3_DB']->exec_SELECTquery(
				'uid, username, admin, disable, FROM_UNIXTIME(lastlogin) AS llogin, email, realName',
				$table,
				 $GLOBALS['TCA'][$table]['ctrl']['delete'].'=0');
			
			if ($result) {
				
				$rows = array();
				
				while($record = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($result))
				{
					if ($record !== FALSE) {				
						$rows[] = $record;
					}
				}
				return new tx_caretakerinstance_OperationResult(TRUE, $rows);
			} else {
				return new tx_caretakerinstance_OperationResult(FALSE, 'Error when executing SQL: [' . $GLOBALS['TYPO3_DB']->sql_error() . ']');
			}
		}
	}
	
	protected function includeTCA() {
		require_once(PATH_tslib.'class.tslib_fe.php');
		
		require_once(PATH_t3lib.'class.t3lib_cs.php');
		require_once(PATH_t3lib.'class.t3lib_userauth.php');
		require_once(PATH_tslib.'class.tslib_feuserauth.php');
		

			// Make new instance of TSFE object for initializing user:
		$temp_TSFEclassName = t3lib_div::makeInstanceClassName('tslib_fe');
		$TSFE = new $temp_TSFEclassName($TYPO3_CONF_VARS,0,0);
		$TSFE->includeTCA();
	}
	
	/**
	 * A simplified enableFields function (partially copied from sys_page) that
	 * can be used without a full TSFE environment. It doesn't / can't check
	 * fe_group constraints or custom hooks.
	 * 
	 * @param $table
	 * @return string The query to append
	 */
	function enableFields($table) {
		$ctrl = $GLOBALS['TCA'][$table]['ctrl'];
		$query = '';
		if (is_array($ctrl)) {
				// Delete field check:
			if ($ctrl['delete']) {
				$query .= ' AND ' . $table . '.' . $ctrl['delete'] . ' = 0';
			}

				// Filter out new place-holder records in case we are NOT in a versioning preview (that means we are online!)
			if ($ctrl['versioningWS']) {
				$query .=' AND ' . $table . '.t3ver_state <= 0'; // Shadow state for new items MUST be ignored!
			}

				// Enable fields:
			if (is_array($ctrl['enablecolumns']))	{
				if ($ctrl['enablecolumns']['disabled']) {
					$field = $table . '.' . $ctrl['enablecolumns']['disabled'];
					$query .= ' AND ' . $field . ' = 0';
				}
				if ($ctrl['enablecolumns']['starttime']) {
					$field = $table.'.'.$ctrl['enablecolumns']['starttime'];
					$query .= ' AND (' . $field . ' <= ' . time() . ')';
				}
				if ($ctrl['enablecolumns']['endtime']) {
					$field = $table . '.' . $ctrl['enablecolumns']['endtime'];
					$query .= ' AND (' . $field . ' = 0 OR ' . $field . ' > ' . time() . ')';
				}
			}
		}
		return $query;
	}
}
?>