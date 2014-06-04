<?php

	namespace Signes;

	require_once(__DIR__ . "/../src/Navigation.php");
	require_once(__DIR__ . "/../src/Navigation/Menu.php");
	require_once(__DIR__ . "/../src/Navigation/Item.php");

	class NavigationTest extends \PHPUnit_Framework_TestCase {

		/**
		 * @expectedException Exception
		 */
		public function testOverwriteException() {

			$item = new Navigation\Item(array(
				'name' => 'overwrite',
				'text' => 'Overwrite',
				'href' => '#'
			));

			$menu = Navigation::forge('test_overwrite_exception');
			$menu->registerItem($item);
			$menu->registerItem($item);
		}

		/**
		 * @expectedException Exception
		 */
		public function testRegisterNoParent() {

			$item = new Navigation\Item(array(
				'parent' => 'not_exists',
				'name'   => 'overwrite',
				'text'   => 'Overwrite',
				'href'   => '#'
			));

			$menu = Navigation::forge('test_register_no_parent');
			$menu->registerItem($item);

		}

		public function testRegisterUnregisterItem() {

			$item = new Navigation\Item(array(
				'name' => 'item',
				'text' => 'Item',
				'href' => '#'
			));

			$menu = Navigation::forge('test_register_unregister_item');

			$menu->registerItem($item);
			$has_item = $menu->hasItem($item);
			$this->assertTrue($has_item);

			$menu->unregisterItem($item);
			$has_item = $menu->hasItem($item);
			$this->assertFalse($has_item);
		}

		public function testActiveItem() {

			$items = array(
				array(
					'name' => 'item1',
					'text' => 'Item1',
					'href' => '#'
				),
				array(
					'name' => 'item2',
					'text' => 'Item2',
					'href' => '#'
				),
				array(
					'name' => 'item3',
					'text' => 'Item3',
					'href' => '#'
				),
			);

			$menu = Navigation::forge('active_items');
			foreach($items as $item) {
				$menu->registerItem(new Navigation\Item($item));
			}

			$test = $menu->setActive('item1');
			$this->assertEquals(array('item1'), $test);

			$test = $menu->setActive('item3', false);
			$this->assertEquals(array('item1', 'item3'), $test);

			$test = $menu->setActive(null);
			$this->assertEquals(array(), $test);

			$menu->setActive('item2');
			$test = $menu->setActive('item3');
			$this->assertEquals(array('item3'), $test);
		}

		public function testConfiguration() {

			// TEST 1
			$menu = Navigation::forge('test_configuration');
			$expected = array(
				'item_tag'           => 'li',
				'item_attributes'    => array(),
				'subitem_tag'        => 'ul',
				'subitem_attributes' => array(),
				'href_attributes'    => array(),
				'active_class'       => 'active',
			);

			$this->assertEquals($expected, $menu->getConfiguration());

			// TEST 2
			$expected = $defined = array(
				'item_tag'           => 'div',
				'item_attributes'    => array(
					'class' => 'my-class'
				),
				'subitem_attributes' => array(
					'id' => 'my-id'
				),
				'href_attributes'    => array(),
			);
			$menu2 = Navigation::forge('test_configuration2', $defined);
			$expected['subitem_tag'] = 'ul';
			$expected['active_class'] = 'active';
			$this->assertEquals($expected, $menu2->getConfiguration());
		}

		/**
		 * Main render test
		 */
		public function testRender() {

			$expected = '<ul><li><a href="/">Home</a></li><li><a href="offers" class="my-class" id="my-id">Offers</a></li><li class="element-class"><a href="offers/map">Map</a></li></ul>';

			$site_main_menu = array(
				array(
					'priority' => 0,
					'name'     => 'homepage',
					'text'     => 'Home',
					'href'     => '/',
				),
				array(
					'priority'        => 1,
					'name'            => 'offers',
					'text'            => 'Offers',
					'href'            => 'offers',
					'item_tag'        => 'li',
					'href_attributes' => array(
						'class' => 'my-class',
						'id'    => 'my-id'
					)
				),
				array(
					'priority'        => 2,
					'name'            => 'offers_map',
					'text'            => 'Map',
					'item_attributes' => array(
						'class' => 'element-class'
					),
					'href'            => 'offers/map',
				),
			);

			foreach($site_main_menu as $item) {
				Navigation::registerItem('test_render', new Navigation\Item($item));
			}


			$this->assertEquals($expected, Navigation::render('test_render'));
		}

		/**
		 * Advanced render test
		 */
		public function testRenderadvanded() {

			$expected = '<header class="top-level"><li><a href="#element-0">Element 0</a></li><li class="element-1-container-class"><a href="#element-1">Element 1</a><ol data-item="element-1-ol-data-item" id="element-1-ol-id"><li><a href="#element-1-1">Element 1-1</a></li></ol></li><li><a href="#element-2" class="element-2-class" id="element-2-id" data-test="element-2-data-test">Element 2</a></li></header>';
			$site_main_menu = array(
				/**
				 * Define element 0
				 */
				array(
					'priority' => 0,
					'name'     => 'element-0',
					'text'     => 'Element 0',
					'href'     => '#element-0',
				),

				/**
				 * Define element 2
				 */
				array(
					'priority'        => 2,
					'name'            => 'element-2',
					'text'            => 'Element 2',
					'href'            => '#element-2',
					'href_attributes' => array(
						'class'     => 'element-2-class',
						'id'        => 'element-2-id',
						'data-test' => 'element-2-data-test',
					)
				),

				/**
				 * Define element 1
				 */
				array(
					'priority'           => 1,
					'name'               => 'element-1',
					'text'               => 'Element 1',
					'item_attributes'    => array(
						'class' => 'element-1-container-class'
					),
					'subitem_tag'        => 'ol',
					'subitem_attributes' => array(
						'data-item' => 'element-1-ol-data-item',
						'id'        => 'element-1-ol-id',
					),
					'href'               => '#element-1',
					'childrens_html_tag' => 'ol',
				),

				/**
				 * Sub-element for element 1
				 */
				array(
					'priority'             => 1,
					'parent'               => 'element-1',
					'name'                 => 'element-1-1',
					'text'                 => 'Element 1-1',
					'container_attributes' => array(
						'class' => 'element-1-1-class'
					),
					'href'                 => '#element-1-1',
				),
			);

			foreach($site_main_menu as $item) {
				Navigation::registerItem('test_renderadvanded', new Navigation\Item($item));
			}

			$result = Navigation::render('test_renderadvanded', array(
				'subitem_tag'        => 'header',
				'subitem_attributes' => array(
					'class' => "top-level",
				),
			));

			$this->assertEquals($expected, $result);
		}

	}