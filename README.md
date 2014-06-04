# Navigation Module

* Version: 1.0

## Description

Navigation module give you possibility to create your multi-level navigation system, fast and easy.

## Quick start

Create new navigation by:

```php
$nav = Signes\Navigation::forge('my-navigation');
```

Add new elements to your new navigation:

```php
$item1 = new Signes\Navigation\Item(array(
	'name' => 'item1',
	'text' => 'Home',
	'href' => '/'
));

$item2 = new Signes\Navigation\Item(array(
	'name' => 'item2',
	'text' => 'Our offer',
	'href' => 'offer'
));

$item3 = new Signes\Navigation\Item(array(
	'name' => 'item3',
	'text' => 'Contact',
	'href' => 'contact'
));

$nav->registerItem($item1);
$nav->registerItem($item2);
$nav->registerItem($item3);
```

And display it when ready:

```php
$rendered_navigation = $nav->render();
echo $rendered_navigation;
```

## Advanced example

Advanced item define:

```php
$menu_items = array(
	array(
		'priority' => 0,
		'name'     => 'homepage',
		'text'     => 'Home',
		'href'     => '/',
	),
	array(
		'priority'        => 2,
		'name'            => 'offers',
		'text'            => 'Offers',
		'href'            => 'offers',
		'href_attributes' => array(
			'class'     => 'my-class',
				'id'        => 'my-id',
				'data-test' => 'my-test',
		)
	),
	array(
		'priority'           => 1,
		'name'               => 'offers_map',
		'text'               => 'Map',
		'href_class'         => 'href-class',
		'item_attributes'    => array(
			'class' => 'element-class'
		),
		'subitem_tag'        => 'ol',
		'subitem_attributes' => array(
			'data-item' => 'ol-item',
			'id'        => 'unique_id',
		),
		'href'               => 'offers/map',
		'childrens_html_tag' => 'ol',
	),
	array(
		'priority'             => 1,
		'parent'               => 'offers_map',
		'name'                 => 'offers_map_children',
		'text'                 => 'Map',
		'href_class'           => 'href-class',
		'container_attributes' => array('class' => 'element-class'),
		'href'                 => 'offers/map',
	),
);

foreach($site_main_menu as $item) {
	Signes\Navigation::registerItem('test_renderadvanded', new Signes\Navigation\Item($item));
}
```

will produce:

```html
<header class="top-level">
	<li>
		<a href="">Home</a>
	</li>
	<li class="element-class">
		<a href="offers/map">Map</a>
		<ol data-item="ol-item" id="unique_id">
			<li>
				<a href="offers/map">Map</a>
			</li>
		</ol>
	</li>
	<li>
		<a href="offers" class="my-class" id="my-id" data-test="my-test">Offers</a>
	</li>
</header>
```

## Options

You can define few options to control navigation code.

### What is rendered

Render method return html navigation block:

```
<{subitem_tag} {subitem_attributes}>
	<{item_tag} {item_attributes}>
		<a href={href} {href_attributes}> {text} </a>
	</{item_tag}>
	<{item_tag} {item_attributes}>
    	<a href={href} {href_attributes}> {text} </a>
    	<{subitem_tag} {subitem_attributes}>
        	<{item_tag} {item_attributes}>
        		<a href={href} {href_attributes}> {text} </a>
        	</{item_tag}>
        	<{item_tag} {item_attributes}>
            	<a href={href} {href_attributes}> {text} </a>
            </{item_tag}>
        </{subitem_tag}>
    </{item_tag}>
</{subitem_tag}>
```

### Menu options

Each menu can be initiate with it's own configuration. This configuration defines global tags and attributes for all single menu element.

You can set it as array of options:

```php
$nav = Signes\Navigation::forge('nav1', array(
	'item_html' => 'div',
	'item_attributtes' => array(
		'class' => 'global-class-for-wrapper'
	)
));
```

,or as preset name. Presets are defined in configuration file.

```php
$nav = Signes\Navigation::forge('nav1', 'preset_name');
```

Menu options can be overwritten by single configuration defined with **item** (see **item options**).

### Item options

You can define few options with Item definition:

```php
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
```

* **name**
Name is menu item identify key and must be unique for menu instance.

* **text**
This is link text, it will be visible as clickable content (it can be also html code).

* **href**
This is URI address for link.

* **href_attributes**
Here you can set any attributes used with link.

* **item_tag**
Item tag is wrapper for link. It can be any html tag.

* **item_attributes**
Here you can set any attributes used with link wrapper.

* **subitem_tag**
Subitem tag is wrapper for all children's connected with current item, and is used only when we have any children's.  It can be any html tag.

* **subitem_attributes**
Here you can set any attributes used with children's wrapper.

* **priority**
Define order of elements in tree (lower is on the top, higher on the bottom).

* **parent**
Here we can set parent **name**, and this item will be children of his parent.

* **active**
Is this element currently active or not. For active elements we add additional class selector, defined in **menu options**

### Top level navigation

With menu option (attributes and html tags), you can define global template for your navigation, but sometimes, you may want to change only first, top level node. You can do it by **render()** method:

```php
echo $menu->render(array(
	'subitem_tag' => 'header',
	'subitem_attributes' => array(
		'id' => 'top-level'
	)
))
```

or

```php
echo Signes\Navigation\render('menu-name', array(
	'subitem_tag' => 'header',
	'subitem_attributes' => array(
		'id' => 'top-level'
)));
```

in both cases, you will get something like:

```html
<header id="top-level">
	<{item_tag} {item_attributes}>
		<a href={href} {href_attributes}> {text} </a>
	</{item_tag}>
	<{item_tag} {item_attributes}>
    	<a href={href} {href_attributes}> {text} </a>
    	<{subitem_tag} {subitem_attributes}>
        	<{item_tag} {item_attributes}>
        		<a href={href} {href_attributes}> {text} </a>
        	</{item_tag}>
        	<{item_tag} {item_attributes}>
            	<a href={href} {href_attributes}> {text} </a>
            </{item_tag}>
        </{subitem_tag}>
    </{item_tag}>s
</header>
```

### Credits

* Pawel Grzesiecki - Developer ([http://signes.pl/](http://signes.pl/))

[MIT](http://opensource.org/licenses/MIT) License