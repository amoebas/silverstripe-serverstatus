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
	public function getReportFields(){
		
		if( !class_exists( 'Zend_Cache' ) ) {
			require_once 'Zend/Cache.php';
		}
		
		$tab =  new Tab( 'Cache' );
		
		foreach( CacheInvestigator::get_backends() as $for => $backend ) {

			$cacheInstance = Zend_Cache::factory( 'Output', $backend[ 0 ], array(), $backend[ 1 ] );

			$tab->push( new HeaderField( $for, $for, 4 ));
			$tab->push( new ReadonlyField( $for.'Backendname', 'Backend', get_class( $cacheInstance->getBackend() ) ) );

			try {
				$percentage = $cacheInstance->getFillingPercentage();
			} catch(Zend_Cache_Exception $e ) {
				$percentage = $e->getMessage();
			}
			$tab->push( new ReadonlyField( $for.'CacheUsed', 'Cache space used', $percentage.'%' ) );
			$tags = $cacheInstance->getIds();
			$tab->push( new ReadonlyField( $for.'AmountOfIds', 'Count of entries', count($tags) ) );
			
			/* Not implemented yet due to uncertianly how to show individual cache entries
			$i=0;
			foreach( $tags as $tag ) {
				$tab->push( new ReadonlyField( $for.$i++.'Cachename', $tag, $cacheInstance->load( $tag ) ) );
			}
			 */
			unset( $cacheInstance );
		}

		$fields = new FieldList( new TabSet( 'Root', $tab ) );
		return $fields;
	}

	/**
	 * @todo remove when the report admin has been fixed
	 * @return void
	 */
	public function forTemplate(){
		return;
	}
}