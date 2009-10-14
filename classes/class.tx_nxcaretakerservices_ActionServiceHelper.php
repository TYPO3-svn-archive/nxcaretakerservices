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

class tx_nxcaretakerservices_ActionServiceHelper {

	/**
	 * Returns an array with all services with the type "caretaker"
	 *
	 * @return array
	 */
	public function getAllCaretakerServices() {
		global $T3_SERVICES;
		return $T3_SERVICES['caretaker_test_service'];
	}
		
		
	/**
	 * Adds a service for caretaker. The service is registered and the type and flexform is added to the testconf
	 *
	 * @param string $extKey: kex of the extension wich is adding the service
	 * @param string $path: path to the flexform and service class without slahes before and after
	 * @param key $key: key wich is used for the service
	 * @param class: the class wich implements the service (default: <path>/class.<key>.txt)
	 * @param flexform: the flexform config (default: <path>/ds.<key>.xml)
	 */
	
	public static function registerCaretakerActionService ($extKey, $path, $key, $title, $description=''){
		global $TCA;
		
		t3lib_div::loadTCA('tx_caretaker_action');
		
			// Register test service
		t3lib_extMgm::addService(
			'nxcaretakerservices',
			'caretaker_test_service',
			$key,	
			array(
				'title' => $title,
				'description' => $description,
				'subtype' => $key,
				'available' => TRUE,
				'priority' => 50,
				'quality' => 50,
				'os' => '',
				'exec' => '',
				'classFile' => t3lib_extMgm::extPath($extKey).$path.'/class.'.$key.'ActionService.php',
				'className' => $key.'ActionService',
			)
		);
		
			// Add actiontype to TCA 
		if (is_array($TCA['tx_caretaker_action']['columns']) && is_array($TCA['tx_caretaker_action']['columns']['test_service']['config']['items'])) {
			$TCA['tx_caretaker_action']['columns']['test_service']['config']['items'][] =  array( $title, $key);
		}
		
			// Add flexform to service-item
		if (is_array($TCA['tx_caretaker_action']['columns']) && is_array($TCA['tx_caretaker_action']['columns']['test_conf']['config']['ds'])) {
			$TCA['tx_caretaker_action']['columns']['test_conf']['config']['ds'][$key] = 'FILE:EXT:'.$extKey.'/'.$path.'/'.( $flexform ? $flexform:'ds.'.$key.'ActionService.xml');
		}
		
	}
	
}
?>