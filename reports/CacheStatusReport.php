<?php

/**
 * CacheStatusReport
 *
 * @package serverstatus
 */
class CacheStatusReport extends ServerHealthReport {

	/**
	 *
	 * @var string
	 */
	protected $title = "Server health - Cache";

	/**
	 *
	 * @var string
	 */
	protected $description = "Status for the cache";

	/**
	 *
	 * @return FieldList
	 */
	public function getReportFields() {

		if(!class_exists('Zend_Cache')) {
			require_once 'Zend/Cache.php';
		}

		$list = new ArrayList();

		foreach(CacheInvestigator::get_backends() as $for => $backend) {

			$cacheInstance = Zend_Cache::factory('Output', $backend[0], array(), $backend[1]);

			$this->pushStatusData($list, $for . '', get_class($cacheInstance->getBackend()));

			try {
				$percentage = $cacheInstance->getFillingPercentage();
			} catch(Zend_Cache_Exception $e) {
				$percentage = $e->getMessage();
			}
			$this->pushStatusData($list, 'Cache space used', $percentage . '%');
			$tags = $cacheInstance->getIds();
			$this->pushStatusData($list, 'Count of entries', count($tags));

			/* Not implemented yet due to uncertianly how to show individual cache entries
			  $i=0;
			  foreach( $tags as $tag ) {
			  $tab->push( new ReadonlyField( $for.$i++.'Cachename', $tag, $cacheInstance->load( $tag ) ) );
			  }
			 */
			unset($cacheInstance);
		}

		return $list;
	}

	/**
	 * Small helper method to cleanup the code
	 *
	 * @param SS_List $list
	 * @param string $name
	 * @param string $value
	 */
	private function pushStatusData(SS_List $list, $name, $value) {
		$list->push(new ReportData(array('Name' => $name, 'Value' => $value, 'CanView' => true)));
	}

	public function sourcerecords() {
		return $this->getReportFields();
	}

	/**
	 * @todo remove when the report admin has been fixed
	 * @return void
	 */
	public function forTemplate() {
		return;
	}

}