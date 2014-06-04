<?php

	namespace Signes\Navigation;

	/**
	 * @since      File available since Release 1.0
	 */
	class Menu {

		/**
		 * Navigation name
		 *
		 * @var
		 */
		private $_name;

		/**
		 * All menu elements
		 *
		 * @var array
		 */
		private $_elements = array();

		/**
		 * Keys of registered elements
		 *
		 * @var array
		 */
		private $_registered_elements = array();

		/**
		 * Set active items
		 *
		 * @var array
		 */
		private $_active_items = array();

		/**
		 * Build tree
		 *
		 * @var null
		 */
		private $_tree = null;

		/**
		 * Default configuration
		 *
		 * @var array
		 */
		private $_configuration = array(
			'item_tag'           => 'li',
			'item_attributes'    => array(),
			'subitem_tag'        => 'ul',
			'subitem_attributes' => array(),
			'href_attributes'    => array(),
			'active_class'       => 'active',
		);

		/**
		 * @param $name
		 * @param array $configuration
		 */
		public function __construct($name, array $configuration = array()) {
			$this->_name = (string) $name;
			$this->_configuration = array_merge($this->_configuration, (array) $configuration);
		}

		/**
		 * Get configuration
		 * s*
		 *
		 * @param null $config
		 * @return array|null
		 */
		public function getConfiguration($config = null) {
			if($config === null) {
				return $this->_configuration;
			}

			return (isset($this->_configuration[$config])) ? $this->_configuration[$config] : null;
		}

		/**
		 * Reister new item for navigation
		 *
		 * @param Item $item
		 * @return bool
		 */
		public function registerItem(Item $item) {
			return $this->_addItem($item);
		}

		/**
		 * Unregister menu item
		 *
		 * @param $item
		 * @return bool, true if success, otherwise false
		 */
		public function unregisterItem($item) {

			if($item instanceof Item) {
				$item = $item->getName();
			}

			if($this->hasItem($item)) {
				foreach($this->_elements as $parent => $priority_list) {
					foreach($priority_list as $priority => $elements_list) {
						foreach($elements_list as $key => $element) {
							if($element->getName() === $item) {
								unset($this->_elements[$parent][$priority][$key]);
								$this->_registered_elements = array_diff($this->_registered_elements, array($item));
								return true;
							}
						}
					}
				}
			}

			return false;
		}

		/**
		 * Set active items
		 *
		 * @param $item , null for no active items
		 * @param bool $single
		 * @return array
		 */
		public function setActive($item, $single = true) {

			$single = (bool) $single;

			if($item instanceof Item) {
				$item = $item->getName();
			}

			// Check if we have item like this one
			$item_exists = $this->hasItem($item);

			if($item === null) {
				$this->_active_items = array();
			} elseif($single === true && $item_exists) {
				$this->_active_items = array($item);
			} elseif($item_exists) {
				$this->_active_items[] = $item;
			}

			return $this->_active_items;
		}

		/**
		 * Check if item is active
		 *
		 * @param $item
		 * @return bool
		 */
		public function isActive($item) {

			if($item instanceof Item) {
				$item = $item->getName();
			}

			return in_array($item, $this->_active_items);
		}

		/**
		 * Check if specific item is registered for menu
		 *
		 * @param $item
		 * @return bool
		 */
		public function hasItem($item) {

			if($item instanceof Item) {
				return in_array($item->getName(), $this->_registered_elements);
			}

			return in_array($item, $this->_registered_elements);
		}

		/**
		 * Build navigation tree
		 */
		public function build() {
			$this->_tree = $this->build_leaf(0);
			return $this->_tree;
		}

		/**
		 * Build navigation leaf
		 *
		 * @param $position
		 * @return array
		 */
		public function build_leaf($position) {
			$menu_elements = isset($this->_elements[$position]) ? $this->_elements[$position] : array();
			ksort($menu_elements);

			foreach($menu_elements as $items) {
				foreach($items as $item) {
					if($this->isActive($item)) {
						$item->setActive();
					}

					/**
					 * Build child's
					 */
					if(isset($this->_elements[$item->getName()])) {
						$item->setChildrens($this->build_leaf($item->getName()));
					}

				}
			}

			return $menu_elements;
		}

		/**
		 * Render this view
		 *
		 * @param array $attributes
		 * @return string
		 */
		public function render(array $attributes = array()) {

			$this->build();
			$list = "";
			if($this->_tree) {

				/**
				 * Get list items attributes
				 */
				$top_html_tag = (isset($attributes["subitem_tag"])) ? $attributes["subitem_tag"] : $this->getConfiguration('subitem_tag');
				$top_html_attr = (isset($attributes["subitem_attributes"])) ? $attributes["subitem_attributes"] : $this->getConfiguration('subitem_attributes');

				/**
				 * Create list content
				 */
				$list_content = $this->render_leaf($this->_tree);

				/**
				 * Create list
				 */
				$html_attributes = "";
				foreach($top_html_attr as $tag_name => $tag_value) {
					$html_attributes .= " {$tag_name}=\"{$tag_value}\"";
				}

				$list = "<{$top_html_tag}{$html_attributes}>{$list_content}</{$top_html_tag}>";
			}

			return $list;
		}

		/**
		 * Render one leaf
		 *
		 * @param array $elements
		 * @return string
		 */
		public function render_leaf(array $elements = array()) {

			$return = "";

			foreach($elements as $elements_level) {
				foreach($elements_level as $element) {

					$childrens_tags = null;

					/**
					 * Get list items attributes
					 */
					$item_tag = ($element->getProperties('item_tag')) ? $element->getProperties('item_tag') : $this->getConfiguration('item_tag');
					$item_attr = (array) ($element->getProperties('item_attributes')) ? $element->getProperties('item_attributes') : $this->getConfiguration('item_attributes');

					$subitem_tag = ($element->getProperties('subitem_tag')) ? $element->getProperties('subitem_tag') : $this->getConfiguration('subitem_tag');
					$subitem_attr = (array) ($element->getProperties('subitem_attributes')) ? $element->getProperties('subitem_attributes') : $this->getConfiguration('subitem_attributes');

					$href_attr = (array) ($element->getProperties('href_attributes')) ? $element->getProperties('href_attributes') : $this->getConfiguration('href_attributes');

					if($element->hasChildrens()) {
						$childrens_tags = $this->render_leaf($element->getChildrens());
					}

					if($element->isActive()) {
						if(isset($item_attr['class'])) {
							$item_attr['class'] .= ' ' . $this->getConfiguration('active_class');
						} else {
							$item_attr['class'] = $this->getConfiguration('active_class');
						}
					}

					$html_attributes = "";
					foreach($href_attr as $tag_name => $tag_value) {
						$html_attributes .= " {$tag_name}=\"{$tag_value}\"";
					}

					$this_tag = "<a href=\"{$element->getHref()}\"{$html_attributes}>{$element->getText()}</a>";

					if(isset($childrens_tags)) {

						$html_attributes = "";
						foreach($subitem_attr as $tag_name => $tag_value) {
							$html_attributes .= " {$tag_name}=\"{$tag_value}\"";
						}

						$this_tag .= "<{$subitem_tag}{$html_attributes}>{$childrens_tags}</{$subitem_tag}>";
					}

					$html_attributes = "";
					foreach($item_attr as $tag_name => $tag_value) {
						$html_attributes .= " {$tag_name}=\"{$tag_value}\"";
					}

					$return .= "<{$item_tag}{$html_attributes}>{$this_tag}</{$item_tag}>";
				}
			}

			return $return;
		}

		/**
		 * Add item and item key to this navigation
		 *
		 * @param Item $item
		 * @param null $parent
		 * @param null $priority
		 * @return bool
		 * @throws \Exception
		 */
		private function _addItem(Item $item, $parent = null, $priority = null) {

			if($this->hasItem($item)) {
				throw new \Exception("Navigation item with name {$item->getName()}, already exists.");
			}

			$parent = ($parent === null) ? $item->getParent() : $parent;
			$priority = (int) (($priority === null) ? $item->getPriority() : $priority);

			if($parent !== 0 && !$this->hasItem($parent)) {
				throw new \Exception("You are trying register item for not existing parent {$parent}.");
			}

			$this->_elements[$parent][$priority][] = $item;
			$this->_registered_elements[] = $item->getName();

			return true;
		}

	}