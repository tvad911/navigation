<?php

	namespace Signes;

	class Navigation {

		static private $_menus = array();

		/**
		 * Alias for "self::registerMenu"
		 *
		 * @param $menu_name
		 * @param array $preset
		 * @return mixed
		 */
		static public function forge($menu_name, array $preset = array()) {
			return self::registerMenu($menu_name, $preset);
		}

		/**
		 * Register new menu item
		 * If menu exists, return it, if not, create new one
		 *
		 * @param $menu_name
		 * @param array $preset
		 * @return mixed
		 */
		static public function registerMenu($menu_name, array $preset = array()) {

			$menu_name = (string) $menu_name;

			if(isset(self::$_menus[$menu_name]) && self::$_menus[$menu_name] instanceof Navigation\Menu) {
				return self::$_menus[$menu_name];
			} else {
				self::$_menus[$menu_name] = new Navigation\Menu($menu_name, $preset);
				return self::$_menus[$menu_name];
			}

		}

		/**
		 * Register new item for menu
		 *
		 * @param $menu_name
		 * @param Navigation\Item $item
		 */
		static public function registerItem($menu_name, Navigation\Item $item) {
			$menu_name = (string) $menu_name;
			$menu = self::registerMenu($menu_name);
			$menu->registerItem($item);
		}

		/**
		 *  Unregister menu item
		 *
		 * @param $menu_name
		 * @param $item
		 * @return bool, true if success, otherwise false
		 */
		static public function unregisterItem($menu_name, $item) {
			$menu = self::getMenu($menu_name);
			if($menu) {
				return $menu->unregisterItem($item);
			}

			return false;
		}

		/**
		 * Get registered menu object. or null
		 *
		 * @param $menu_name
		 * @return null
		 */
		static public function getMenu($menu_name) {
			return (isset(self::$_menus[$menu_name]) && self::$_menus[$menu_name] instanceof Navigation\Menu) ? self::$_menus[$menu_name] : null;
		}

		/**
		 * Set active element
		 *
		 * @param $menu_name
		 * @param $item
		 * @param bool $single
		 */
		static public function setActive($menu_name, $item, $single = true) {
			$menu = self::getMenu($menu_name);
			if($menu) {
				$menu->setActive($item, $single);
			}
		}

		/**
		 * Render navigation
		 *
		 * @param $menu_name
		 * @param $attributes
		 * @return null
		 */
		static public function render($menu_name, array $attributes = array()) {
			$menu = self::getMenu($menu_name);
			if($menu) {
				return $menu->render($attributes);
			}

			return null;
		}

	}