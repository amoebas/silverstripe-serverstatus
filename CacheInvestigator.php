<?php
/**
 * CacheInvestigator extends the SS_Cache with the possibility to get the cache
 * backends
 *
 * @package serverstatus
 */
class CacheInvestigator extends SS_Cache {

	/**
	 *
	 * @return array
	 */
	public static function get_backends() {
		return parent::$backends;
	}

	/**
	 *
	 * @return array
	 */
	public static function get_backend_picks() {
		return parent::$backend_picks;
	}
}
