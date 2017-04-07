<?php

global $modules;

function slugify($str) {
    $search = array('Ș', 'Ț', 'ş', 'ţ', 'Ş', 'Ţ', 'ș', 'ț', 'î', 'â', 'ă', 'Î', 'Â', 'Ă', 'ë', 'Ë');
    $replace = array('s', 't', 's', 't', 's', 't', 's', 't', 'i', 'a', 'a', 'i', 'a', 'a', 'e', 'E');

    $str = str_ireplace($search, $replace, strtolower(trim($str)));
    $str = preg_replace('/[^\w\d\-\ ]/', '', $str);
    $str = str_replace(' ', '-', $str);

    return preg_replace('/\-{2,}/', '-', $str);
}

class AcfModule {
	private $fields;
	private $path;

	private $defaults = [
		"name" => false,
		"enabled" => true,
		"group" => "flexible",
		"file" => false,
		"fieldsDir" => "fields/",
		"fields" => false
	];

	public $fieldGroup;
	public $groupName;
	public $settings;

	private function checkSettings($path) {
		$requiredFields = array('fields', 'file', 'name');

		foreach ($requiredFields as $field) {
			if (!$this->settings[$field]) {
				throw new Exception('Settings files require ' . $field . ' to be set at: ' . $path . '/settings.json');
			}
		}
	}

	private function syncSettingsWithDefaults($json) {
		return array_merge($this->defaults, $json);
	}

	private function registerSettings($path) {
		$string = file_get_contents($path . '/settings.json');
		$json = json_decode($string, true);

		$this->settings = $this->syncSettingsWithDefaults($json);

		$this->checkSettings($path);
	}

	private function registerFields($path) {
		$this->path = $path;

		$fieldsDir = $path . '/fields';
		$fieldsPath = $fieldsDir . '/' . $this->settings['fields'];

		// Add the fields to class variable
		$string = file_get_contents($fieldsPath);

		$this->fields = json_decode($string, true);

		// ------------------------------------
		// Custom ACF JSON loading directory

		add_filter('acf/settings/load_json', function( $paths ) {
			// append path
			$paths[] = $this->fieldsDir;

			return $paths;
		});
	}

	public function addFieldsToFlexible($flexible) {
		$flexible['fields'][0]['layouts'][] = array(
			'key' => 'flexiblesection-' . $this->groupName,
			'name' => $this->fieldGroup,
			'label' => $this->settings['name'],
			'sub_fields' => array (
				array (
					'key' => 'field_flexsubfield-' . $this->groupName,
					'name' => $this->fieldGroup,
					'label' => $this->settings['name'],
					'type' => 'clone',
					'clone' => array (
						0 => $this->groupName,
					)
				),
			)
		);

		return $flexible;
	}

	public function getFilePath() {
		// Get the base file name... stripping the .php
		$file = basename($this->settings['file'], '.php');

		if (!file_exists($this->path . '/' . $file . '.php')) {
			throw new Exception('Template file does not exist: ' . $this->path . '/' . $file . '.php');
		}

		return $this->moduleName . '/' . $file;
	}

	public function __construct($folderName, $path) {
		$this->registerSettings($path);
		$this->registerFields($path);

		$this->moduleName = $folderName;
		$this->groupName = $this->fields['key'];
		$this->fieldGroup = slugify($this->settings['name']);
	}
}

class AcfModulesHandler {
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

	private $modules = [];

	public function registerModule($module) {
		$this->checkForDupGroupNames($module);

		$this->modules[$module->fieldGroup] = $module;
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
		// array_diff will remove the '..' and '.' from the results
		$moduleNames = array_diff(scandir($moduleDir), array('..', '.', '.DS_Store'));

		foreach ($moduleNames as $moduleName) {
			// Take the module directory and append the unique module folder name to it
			$modulePath = $moduleDir . '/'. $moduleName;

			// Create the new module
			$module = new AcfModule($moduleName, $modulePath);

			// And register it with the module handler
			$this->registerModule($module);

			$this->flexible = $module->addFieldsToFlexible($this->flexible);
		}

		acf_add_local_field_group($this->flexible);
	}
}

$modules = new AcfModulesHandler;
