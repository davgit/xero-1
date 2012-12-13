<?php

	require_once EXTENSIONS . '/xero/libs/xero.php';

	Class extension_Xero extends Extension {

		public static $xero = null;
		public static $config_handle = 'xero';

		public function getSubscribedDelegates(){
			return array(
				array(
					'page'		=> '/system/preferences/',
					'delegate'	=> 'AddCustomPreferenceFieldsets',
					'callback'	=> 'appendPreferences'
				),
				array(
					'page'		=> '/system/preferences/',
					'delegate'	=> 'Save',
					'callback'	=> 'savePreferences'
				),
			);
		}

	/*-------------------------------------------------------------------------
		Utilities:
	-------------------------------------------------------------------------*/

		public function getSetting($key) {
			return Symphony::Configuration()->get($key, self::$config_handle);
		}


	/*-------------------------------------------------------------------------
		Preferences:
	-------------------------------------------------------------------------*/

		public function getPreferencesData() {
			$data = array(
				'xero-key' => '',
				'xero-secret' => '',
			);

			foreach ($data as $key => &$value) {
				$value = $this->getSetting($key);
			}

			return $data;
		}

		/**
		 * Allow the user to add their Xero keys.
		 *
		 * @uses AddCustomPreferenceFieldsets
		 */
		public function appendPreferences($context) {
			$data = $this->getPreferencesData();

			$fieldset = new XMLElement('fieldset');
			$fieldset->setAttribute('class', 'settings');
			$fieldset->appendChild(new XMLElement('legend', __('Xero Accountant')));

			$this->buildPreferences($fieldset, array(
				array(
					'label' => 'Xero Key',
					'name' => 'xero-key',
					'value' => $data['xero-key']
				),
				array(
					'label' => 'Xero Secret',
					'name' => 'xero-secret',
					'value' => $data['xero-secret']
				)
			));

			$context['wrapper']->appendChild($fieldset);
		}


		public function buildPreferences($fieldset, $data) {
			$row = null;

			foreach ($data as $index => $item) {
				if ($index % 2 == 0) {
					if ($row) $fieldset->appendChild($row);

					$row = new XMLElement('div');
					$row->setAttribute('class', 'group');
				}

				$label = Widget::Label(__($item['label']));
				$name = 'settings[' . self::$config_handle . '][' . $item['name'] . ']';

				$input = Widget::Input($name, $item['value']);

				$label->appendChild($input);
				$row->appendChild($label);
			}

			$fieldset->appendChild($row);
		}

		/**
		 * Saves the Xero configuration
		 *
		 * @uses savePreferences
		 */
		public function savePreferences(array &$context){
			$settings = $context['settings'];

            Symphony::Configuration()->set('xero-key', $settings['xero']['xero-key'], 'xero');
			Symphony::Configuration()->set('xero-secret', $settings['xero']['xero-secret'], 'xero');

			Administration::instance()->saveConfig();
		}

	}
