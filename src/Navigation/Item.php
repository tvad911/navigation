<?php

	namespace Signes\Navigation;

	class Item {

		/**
		 * Menu item properties
		 *
		 * @var array
		 */
		private $_properties = array(
			'name'               => '',
			'text'               => '',
			'href'               => null,
			'href_attributes'    => null,
			'item_tag'           => null,
			'item_attributes'    => null,
			'subitem_tag'        => null,
			'subitem_attributes' => null,
			'priority'           => 500,
			'parent'             => 0,
			'active'             => false,
		);

		/**
		 * Construct new menu element
		 *
		 * @param array $options
		 */
		public function __construct(array $options) {

			$options = array_merge($this->_properties, $options);
			foreach($options as $key => $val) {
				if(array_key_exists($key, $this->_properties)) {
					$this->_setProperties($key, $val);
				}
			}

		}

		/**
		 * Get current item properties
		 *
		 * @param $properties
		 * @return mixed
		 */
		public function getProperties($properties) {
			return (isset($this->_properties[$properties])) ? $this->_properties[$properties] : null;
		}

		/**
		 * Set current item properties
		 *
		 * @param $properties
		 * @param $value
		 */
		private function _setProperties($properties, $value) {
			$this->_properties[$properties] = $value;
		}

		/**
		 * Check if current navigation item is active
		 *
		 * @return bool
		 */
		public function isActive() {
			return (bool) $this->getProperties('active');
		}

		/**
		 * Set current navigation item as active
		 */
		public function setActive() {
			$this->_setProperties('active', true);
		}

		/**
		 * Set childrens for current navigation item
		 *
		 * @param array $childrens
		 */
		public function setChildrens(array $childrens = null) {
			$this->_setProperties('childrens', $childrens);
		}

		/**
		 * Get menu item parent,
		 * zero if not exists (top)
		 */
		public function getParent() {
			return $this->getProperties('parent');
		}

		/**
		 * Get menu item priority
		 */
		public function getPriority() {
			return (int) $this->getProperties('priority');
		}

		/**
		 * Get menu item name
		 *
		 * @return string
		 */
		public function getName() {
			return $this->getProperties('name');
		}

		/**
		 * @return boolean
		 */
		public function getHrefClass() {
			return $this->getProperties('href_class');
		}

		/**
		 * @return string
		 */
		public function getHref() {
			return $this->getProperties('href');
		}

		/**
		 * @return string
		 */
		public function getText() {
			return $this->getProperties('text');
		}


		/**
		 * @return array
		 */
		public function getContainerAttributes() {
			return $this->getProperties('container_attributes');
		}

		/**
		 * @return mixed
		 */
		public function getChildrens() {
			return $this->getProperties('childrens');
		}

		/**
		 * @return bool
		 */
		public function hasChildrens() {
			$childrens = $this->getChildrens();
			return (is_array($childrens) && !empty($childrens));
		}

		/**
		 * @return bool
		 */
		public function getChildrensHtmlTag() {
			return $this->getProperties('childrens_html_tag');
		}

	}