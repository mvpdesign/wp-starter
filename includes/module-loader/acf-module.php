<?php

if ( ! defined( 'ABSPATH' ) ) exit; // Bail if accessed directly

class AcfModule {
	// TODO: update types of these variables
	/**
	 * Setup variables for later use
	 *
	 * @var string $fields
	 * @var string $path
	 * @var string $fieldGroup
	 * @var string $groupName
	 * @var string $settings
	 */

	private $fields, $path;

	public $fieldGroup, $groupName, $settings;

	// Default module config
	// These settings will be merged with the module specific settings
	private $defaults = [
		"name" => false,
		"enabled" => true,
		"file" => false,
		"fields" => false,
		"fieldsDir" => 'fields/',
		"styles" => false,
		"scripts" => false
	];


	/**
	 * Check the module settings.json file for required fields
	 *
	 * @param string $path string path that leads to the module
	 * @return return boolean
	 */

	private function checkSettings($path) {
		$requiredFields = array('fields', 'file', 'name', 'enabled');

		foreach ($requiredFields as $field) {
			if (!$this->settings[$field]) {
				throw new Exception('Settings files require ' . $field . ' to be set at: ' . $path . '/settings.json');
			}
		}

		return true;
	}

	private function registerSettings($path) {
		$string = file_get_contents($path . '/settings.json');
		$json = json_decode($string, true);

		$this->settings = array_merge($this->defaults, $json);

		return $this->checkSettings($path);
	}

	private function registerFields($path) {
		$this->path = $path;

		$this->fieldsDir = $path . '/' . $this->settings['fieldsDir'];
		$fieldsPath = $path . '/' . $this->settings['fields'];

		// Add the fields to class variable
		$string = file_get_contents($fieldsPath);

		$this->fields = json_decode($string, true);

		// -----------------------------------------
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

		if (!$this->settings['enabled']) {
			return false;
		}

		$this->registerFields($path);

		$this->moduleName = $folderName;
		$this->groupName = $this->fields['key'];
		$this->fieldGroup = slugify($this->settings['name']);

		return [
			'moduleName' => $this->moduleName,
			'groupName' => $this->groupName
		];
	}
}
