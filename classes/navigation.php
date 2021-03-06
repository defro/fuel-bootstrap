<?php

/**
 * Navigation Class
 *
 * @package		Bootstrap
 * @category	Package
 * @author		Derek Myers
 * @link		https://github.com/dmyers/fuel-bootstrap
 */
 
namespace Bootstrap;

class Navigation
{
	/**
	 * loaded navigation instance
	 */
	protected static $_instance = null;

	/**
	 * Initialize by loading config
	 */
	public static function _init()
	{
		\Config::load('navigation', true);
	}

	/**
	 * Returns a new Navigation object.
	 *
	 *     $navigation = Navigation::forge();
	 *
	 * @param	void
	 * @access	public
	 * @return  Navigation
	 */
	public static function forge()
	{
		return new static;
	}

	/**
	 * create or return the navigation instance
	 *
	 * @param	void
	 * @access	public
	 * @return	Navigation object
	 */
	public static function instance()
	{
		if (static::$_instance === null) {
			static::$_instance = static::forge();
		}

		return static::$_instance;
	}

	/**
	 * renders the navigation
	 *
	 * @param   array   array with tag attribute settings
	 * @access	public
	 * @return	void
	 */
	public static function render($type = 'default', array $attributes = array(), $header = false)
	{
		if (empty($type)) {
			return;
		}
		
		$links = \Config::get('navigation.' . $type, false);

		if (empty($links)) {
			throw new BootstrapException('Missing navigation links in config');
			return;
		}

		$callback = \Config::get('bootstrap.navigation_links_callback', null);

		if ($callback != null) {
			$links = $callback($links);
		}

		foreach ($links as $key => &$link) {
			if (empty($link['url'])) {
				$link['url'] = \Inflector::friendly_title($link['title'], '-', true);
			}

			// Set link to active if it matches the current page URI.
			if (!isset($link['active'])) {
				$link['active'] = ($link['url'] == ltrim(\Input::uri(), '/'));
			}
			
			if (empty($link['attributes'])) {
				$link['attributes'] = array();
			}

			$anchor_classs = \Config::get('bootstrap.navigation.anchor_class', true);

			if ($anchor_classs) {
				if (!isset($link['attributes']['class'])) {
					$link['class'] = \Inflector::friendly_title($link['title'], '-', true);
				}

				$anchor_prefix = \Config::get('bootstrap.navigation.anchor_prefix', 'nav-');

				if (!empty($anchor_prefix)) {
					$link['class'] = $anchor_prefix . $link['class'];
				}
			}

			if (!empty($link['class'])) {
				$link['attributes']['class'] = $link['class'];
			}
		}
		
		$ul_prefix =  \Config::get('bootstrap.navigation.ul_prefix', 'nav');

		if (isset($attributes['class'])) {
			$attributes['class'] = $ul_prefix.' '.$attributes['class'];
		} else {
			$attributes['class'] = $ul_prefix;
		}

		echo \View::forge('navigation', array(
			'header'     => $header,
			'links'      => $links,
			'attributes' => array_to_attr($attributes),
		))->render();
	}
}
