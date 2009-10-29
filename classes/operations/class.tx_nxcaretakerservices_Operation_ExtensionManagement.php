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

//define('TYPO3_MOD_PATH', '../typo3conf/');
$BACK_PATH='../../../../../typo3/';

$GLOBALS['LANG'] = t3lib_div::makeInstance('language');
$GLOBALS['LANG']->init($BE_USER->uc['lang']);

//require_once (PATH_typo3 . 'init.php');
require_once(PATH_typo3 . 'mod/tools/em/class.em_index.php');
require_once(PATH_typo3 . 'template.php');
require_once(t3lib_extMgm::extPath('caretaker_instance', 'classes/class.tx_caretakerinstance_OperationResult.php'));

/**
 * An Operation that returns the first record matched by a field name and value as an array (excluding protected record details like be_user password).
 * This operation should be SQL injection safe. The table has to be mapped in the TCA.
 * 
 * @author Christopher Hlubek <hlubek@networkteam.com>
 * @package		TYPO3
 * @subpackage	tx_caretakerinstance
 */
class tx_nxcaretakerservices_Operation_ExtensionManagement implements tx_caretakerinstance_IOperation {
	
	/**
	 * 
	 * @param array $parameter A table 'table', field name 'field' and the value 'value' to find the record 
	 * @return The first found record as an array or FALSE if no record was found
	 */
	public function execute($parameter = array()) {
		
		$action = $parameter['action'];
		$extkey = $parameter['extkey'];
		
		$dirname = PATH_site . 'typo3conf/ext/'.$extkey.'/';
		
		$retval = new tx_caretakerinstance_OperationResult(false, 'error');
		
		switch ($action){
			case 'svninfo' :
				$retval = $this->getSVNInfo($dirname);
				break;
			case 'uninstall' :			
				$retval = $this->uninstallExtension($extkey);
				break;
			case 'install' :			
				$retval = $this->installExtension($extkey);
				break;
			case 'delete' :
				$retval = $this->deleteAndBackupExtension($extkey, $dirname);
				break;
			case 'update' :
				$retval = $this->updateExtensionBySVN($dirname);
				break;
			case 'checkout' :
				$retval = $this->getExtensionFromSVN($extkey);
				break;
			case 'fetch' :
				$retval = $this->getExtensionFromTER($extkey);
				break;
			case 'databaseUpdate' :
				$retval = $this->updateDatabase($extkey);
				break;
		}
		
		return $retval;
		
	}
	
	protected function getSVNInfo($dirname) {
		$svnCommand = 'cd ' . $dirname . ' && /usr/bin/svn info';
		exec($svnCommand, $output);
		$result = 'SVN Info:';
		foreach($output as $line){
			$result = $result . "\n" . $line;				
		}
		if($result == 'SVN Info:') $retval = new tx_caretakerinstance_OperationResult(FALSE, 'Not in SVN repository or SVN version too old.');
		else $retval = new tx_caretakerinstance_OperationResult(TRUE, $result);
		
		return $retval;
	}
	
	protected function uninstallExtension($extkey) {
		$extManager = t3lib_div::makeInstance('SC_mod_tools_em_index');			
		$extManager->requiredExt = t3lib_div::trimExplode(',',$TYPO3_CONF_VARS['EXT']['requiredExt'],1);
		$extManager->doc = t3lib_div::makeInstance('template');
		$extlist = $extManager->getInstalledExtensions();
		$list = reset($extlist);
		
		$newExtList = $extManager->removeExtFromList($extkey, $list);
		if($newExtList === -1) $retval = new tx_caretakerinstance_OperationResult(TRUE, $extManager->content);	
		else {
			$extManager->writeNewExtensionList($newExtList);							
			$retval = new tx_caretakerinstance_OperationResult(TRUE, 'Extension ' . $extkey . ' uninstalled.');
		}			
		
		return $retval;
	}
	
	protected function installExtension($extkey) {
		$extManager = t3lib_div::makeInstance('SC_mod_tools_em_index');			
		$extManager->requiredExt = t3lib_div::trimExplode(',',$TYPO3_CONF_VARS['EXT']['requiredExt'],1);
		$extManager->doc = t3lib_div::makeInstance('template');
		$extlist = $extManager->getInstalledExtensions();
		$list = reset($extlist);
		
		$extManager->CMD['load'] = true;
		$GLOBALS['BE_USER'] = t3lib_div::makeInstance('t3lib_beUserAuth');	
		$extManager->CMD['standAlone']	= true;	
		
		$extManager->showExtDetails($extkey);
		$retval = new tx_caretakerinstance_OperationResult(FALSE,  $extManager->content);
		if (ereg( 'DATABASE NEEDS TO BE UPDATED', $extManager->content)) {
			$result = 'Before the extension can be installed the database needs to be updated with new tables or fields.';
			if (ereg( 'This extension requests the cache to be cleared when it is installed', $extManager->content)) $result = $result . '<br /><br />This extension requests the cache to be cleared when it is installed.';
			if (ereg( 'The extension requires the upload folder ', $extManager->content)) {
				$result = $result . '<br /><br />The extension requires the upload folder:';
				$result = $result . '<br />' . PATH_site.$extManager->ulFolder($extkey);
				if ($list[$extkey]['EM_CONF']['createDirs'])	{
					$createDirs = array_unique(t3lib_div::trimExplode(',',$list[$extkey]['EM_CONF']['createDirs'],1));
					foreach($createDirs as $crDir)	{
						$result = $result . '<br />' . PATH_site.$crDir;
					}
					$result = $result . '<br /> DO NOT UPDATE!'; //TODO: make this work
				}
			}
			$retval = new tx_caretakerinstance_OperationResult(FALSE,  $result);
		}
		if (ereg( 'Dependency Error', $extManager->content)) {	
			$result = $extManager->content;
			$result = substr($result, strpos($result, '<!-- Section content -->')+25,strpos($result, '<br />&nbsp;&nbsp;&nbsp;&nbsp;')-(strpos($result, '<!-- Section content -->')+25));
			$retval = new tx_caretakerinstance_OperationResult(FALSE,  'Dependency Error. '.$result);				
		}
		if (ereg( 'The extension has been installed', $extManager->content)) $retval = new tx_caretakerinstance_OperationResult(TRUE,  'This extension has been installed.');
		return $retval;
	}
	
	protected function updateDatabase($extkey) {
		$extManager = t3lib_div::makeInstance('SC_mod_tools_em_index');			
		$extManager->requiredExt = t3lib_div::trimExplode(',',$TYPO3_CONF_VARS['EXT']['requiredExt'],1);
		$extManager->doc = t3lib_div::makeInstance('template');
		$extManager->typePaths = Array (
			'S' => TYPO3_mainDir.'sysext/',
			'G' => TYPO3_mainDir.'ext/',
			'L' => 'typo3conf/ext/'
		);
		$extManager->typeBackPaths = Array (
			'S' => '../../../',
			'G' => '../../../',
			'L' => '../../../../'.TYPO3_mainDir
		);
		$this->includeTCA();
		$extlist = $extManager->getInstalledExtensions();
		$list = reset($extlist);
		
		$extManager->CMD['load'] = true;
		$GLOBALS['BE_USER'] = t3lib_div::makeInstance('t3lib_beUserAuth');	
		$extManager->CMD['standAlone']	= true;	
				
		$result = $extManager->checkDBupdates($extkey,$list[$extkey]);
		
		$statementkeys = array();		
		if(preg_match_all('/name="TYPO3_INSTALL\[database_update\]\[([0-9a-f]{32})\]"/', $result, $statementkeys) > 0) {
			$_GET['TYPO3_INSTALL']['database_update'] = array_fill_keys($statementkeys[1], 1);
		}
		$_POST['_uploadfolder'] = 1;
		$_POST['_clear_all_cache'] = 1;		
		$_GET['_do_install'] = 1;
		$_GET['_clrCmd'] = 1;	
		 	
		$extManager->showExtDetails($extkey);
		
		$retval = new tx_caretakerinstance_OperationResult(FALSE,  $extManager->content);
		if (ereg( 'The extension has been installed', $extManager->content)) $retval = new tx_caretakerinstance_OperationResult(TRUE,  'This extension has been installed.');
		
		return $retval;
	}
			
	protected function includeTCA() {
		require_once(PATH_tslib.'class.tslib_fe.php');

			// require some additional stuff in TYPO3 4.1
		require_once(PATH_t3lib.'class.t3lib_cs.php');
		require_once(PATH_t3lib.'class.t3lib_userauth.php');
		require_once(PATH_tslib.'class.tslib_feuserauth.php');

			// Make new instance of TSFE object for initializing user:
		$temp_TSFEclassName = t3lib_div::makeInstanceClassName('tslib_fe');
		$TSFE = new $temp_TSFEclassName($TYPO3_CONF_VARS,0,0);
		$TSFE->includeTCA();
	}
	
	protected function deleteAndBackupExtension($extkey, $dirname) {
		if(!file_exists(PATH_site . 'typo3conf/backup/')) t3lib_div::mkdir(PATH_site . 'typo3conf/backup/');
		$backupdir = PATH_site . 'typo3conf/backup/'.$extkey.'/';
		if(file_exists($backupdir)) $this->unlinkRecursive($backupdir);
		$result = rename($dirname, $backupdir);				
		if(!$result) $retval = new tx_caretakerinstance_OperationResult(FALSE, 'Extension could not be deleted.');
		else $retval = new tx_caretakerinstance_OperationResult(TRUE, 'Extension was deleted. (backuped)');
		return $retval;
	}
	
	protected function updateExtensionBySVN($dirname) {
		$svnCommand = 'cd ' . $dirname . ' && /usr/bin/svn up';				
		$result = exec($svnCommand);
		if(!$result) $retval = new tx_caretakerinstance_OperationResult(FALSE, 'Not in SVN repository or SVN version too old.');
		else $retval = new tx_caretakerinstance_OperationResult(TRUE, $result);
		return $retval;
	}
	
	protected function getExtensionFromSVN($extkey) {
		$data = split(',', $extkey);
		$extkey = $data[0];
		$rep = $data[1];			
		$dirname = PATH_site . 'typo3conf/ext/'.$extkey.'/';
		if(!file_exists($dirname))	t3lib_div::mkdir($dirname);
		$svnCommand = 'cd ' . $dirname . ' && /usr/bin/svn co ' . $rep . ' .';				
		$result = exec($svnCommand);
		if(!$result) $retval = new tx_caretakerinstance_OperationResult(FALSE, 'Not in SVN repository or SVN version too old.'.$svnCommand);
		else $retval = new tx_caretakerinstance_OperationResult(TRUE, $result);
		return $retval;
	}
	
	protected function getExtensionFromTER($extkey) {
		$data = split(',', $extkey);
		$extkey = $data[0];
		$version = $data[1];	
		$rep_url = $data[2];		
		
		$extManager = t3lib_div::makeInstance('SC_mod_tools_em_index');			
		$extManager->requiredExt = t3lib_div::trimExplode(',',$TYPO3_CONF_VARS['EXT']['requiredExt'],1);
		$extManager->doc = t3lib_div::makeInstance('template');				
		
		$extManager->typePaths = Array (
			'S' => TYPO3_mainDir.'sysext/',
			'G' => TYPO3_mainDir.'ext/',
			'L' => 'typo3conf/ext/'
		);
		$extManager->typeBackPaths = Array (
			'S' => '../../../',
			'G' => '../../../',
			'L' => '../../../../'.TYPO3_mainDir
		);
		$extManager->MOD_SETTINGS['rep_url'] = $rep_url;
		$extManager->terConnection = t3lib_div::makeInstance('SC_mod_tools_em_terconnection');
		$extManager->terConnection->emObj = $extManager;
		$extManager->terConnection->wsdlURL = $GLOBALS['TYPO3_CONF_VARS']['EXT']['em_wsdlURL'];
		$extManager->xmlhandler = t3lib_div::makeInstance('SC_mod_tools_em_xmlhandler');
		$extManager->xmlhandler->emObj = $extManager;
		$extManager->xmlhandler->useUnchecked = $extManager->MOD_SETTINGS['display_unchecked'];
		$extManager->xmlhandler->useObsolete = $extManager->MOD_SETTINGS['display_obsolete'];
		
		$result = $extManager->importExtFromRep($extkey, $version,'L');
		if (ereg( 'SUCCESS', $extManager->content))$retval = new tx_caretakerinstance_OperationResult(TRUE, 'The extension was imported.');
		else $retval = new tx_caretakerinstance_OperationResult(TRUE, $extManager->content);	
		
		return $retval;		
	}
	
	protected function unlinkRecursive($dir)
	{
	    if(!$dh = @opendir($dir))
	    {
	        return;
	    }
	    while (false !== ($obj = readdir($dh)))
	    {
	        if($obj == '.' || $obj == '..')
	        {
	            continue;
	        }
	
	        if (!@unlink($dir . '/' . $obj))
	        {
	            $this->unlinkRecursive($dir.'/'.$obj, true);
	        }
	    }
	
	    closedir($dh);
	    @rmdir($dir);
	   
	    return;
	}
}
?>