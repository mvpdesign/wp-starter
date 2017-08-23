<?php

if ( ! defined( 'ABSPATH' ) ) exit; // Bail if accessed directly

class AcfModulesHandler {
	/**
	 * Variable to contain the flexible "shell" or group
	 * all modules will be loaded into this group
	 *
	 * @var array $flexible
	 */

	private $flexible = array(
		'key' => 'group_flexible',
		'title' => 'Flexible',
		'fields' => array (
			array (
				'key' => 'field_sections',
				'label' => 'Sections',
				'name' => 'sections',
				'type' => 'flexible_content'
			),
		),
		'location' => array (
			array (
				array (
					'param' => 'post_type',
					'operator' => '==',
					'value' => 'page',
				),
			),
		)
	);



	/**
	 * Will contain all parsed modules from the module folder
	 *
	 * @var array $modules
	 */

	private $modules = [];


	// Register the module
	// Check for duplicates
	// $module
	public function registerModule($module) {
		$this->checkForDupGroupNames($module);

		return $this->modules[$module->fieldGroup] = $module;
	}

	public function getModule($layoutName) {
		return $this->modules[$layoutName];
	}

	private function checkForDupGroupNames($module) {
		$groupName = $module->groupName;

		foreach ($this->modules as $registeredModule) {
			if ($registeredModule->groupName === $groupName) {
				throw new Exception("Duplicate ACF Group Name: " . $groupName . ' found in ' . $module->fieldGroup);
			}
		}
	}

	public function __construct() {
		// The folder where all the modules live
		// This is pointing towards wp-content/<theme>/modules
		$moduleDir = get_template_directory() . '/modules';

		// Get all the folders in the module directory and list them
		// array_diff will remove the '..', '.', and '.DS_Store' from the results
		$moduleNames = array_diff(scandir($moduleDir), array('..', '.', '.DS_Store'));

		foreach ($moduleNames as $moduleName) {
			// Take the module directory and append the unique module folder name to it
			$modulePath = $moduleDir . '/'. $moduleName;

			// Create the new module
			$module = new AcfModule($moduleName, $modulePath);

			// And register it with the module handler
			if ($module->settings['enabled']) {
				$this->registerModule($module);

				// TODO: this is a backwards way to achieved the desired results of an updated flexible field
				$this->flexible = $module->addFieldsToFlexible($this->flexible);
			}
		}

		acf_add_local_field_group($this->flexible);
	}
}
