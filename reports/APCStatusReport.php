<?php
/**
 * APC Reports gives us the status of the APC cache, if existing on the server.
 *
 * @package serverstatus
 */
class APCStatusReport extends ServerHealthReport {

	/**
	 *
	 * @var string
	 */
	protected $title = "Server health - APC";
	
	/**
	 *
	 * @var string
	 */
	protected $description = "See the status of the APC cache on this server.";

	/**
	 *
	 * @return Tab 
	 */
	public function getReportFields() {
		$apcTab = new Tab( 'apc', 'APC' );
		foreach( $this->getStatus() as $data ) {
			$apcTab->push( new ReadonlyField( $data->Name , $data->Name, $data->Value ) );
		}
		return $apcTab;
	}

	/**
	 * 
	 * Will return the DataObjectset containing the Status of APC
	 *
	 * @return DataObjectSet 
	 */
	public function getStatus() {
		$dataObjectSet = new DataObjectSet();
		if( !function_exists( 'apc_cache_info' ) || !($cache = @apc_cache_info( $cache_mode )) ) {
			$dataObjectSet->push( new ArrayData( array( 'Name' => 'APC version', 'Value' => 'No cache info available. APC does not appear to be running.' ) ) );
			return;
		}

		$mem = apc_sma_info();
		$mem_size = $mem[ 'num_seg' ] * $mem[ 'seg_size' ];
		$mem_avail = $mem[ 'avail_mem' ];
		$mem_used = $mem_size - $mem_avail;
		$seg_size = $this->bsize( $mem[ 'seg_size' ] );
		$number_files = $cache[ 'num_entries' ];
		$size_files = $this->bsize( $cache[ 'mem_size' ] );

		$this->pushStatusData( $dataObjectSet, 'APC version', phpversion( 'apc' ) );
		$this->pushStatusData( $dataObjectSet, 'APC Cache full count', $cache[ 'expunges' ] );
		$this->pushStatusData( $dataObjectSet, 'APC Memory used', $this->percentage( $mem_size, $mem_used ).' ('.$this->bsize( $mem_used ) . ' / ' . $this->bsize( $mem_size ).')' );
		$this->pushStatusData( $dataObjectSet, 'APC Memory fragmentation', $this->getAPCFragmentation( $mem ) );
		$this->pushStatusData( $dataObjectSet, 'APC Cached files (size of files)', $number_files.' ('.$size_files.')' );
		$this->pushStatusData( $dataObjectSet, 'APC Shared memory', $mem[ 'num_seg' ].' Segment(s) with '.$seg_size.' '.$cache[ 'memory_type' ].' memory, '.$cache[ 'locking_type' ].' locking' );
		return $dataObjectSet;
	}
	
	/**
	 * Get the fragmentation of the apc memory
	 *
	 * @param array $mem
	 * @return string
	 */
	protected function getAPCFragmentation( $mem ) {
		$nseg = $freeseg = $fragsize = $freetotal = 0;
		for( $i = 0; $i < $mem[ 'num_seg' ]; $i++ ) {
			$ptr = 0;
			foreach( $mem[ 'block_lists' ][ $i ] as $block ) {
				if( $block[ 'offset' ] != $ptr ) {
					++$nseg;
				}
				$ptr = $block[ 'offset' ] + $block[ 'size' ];
				/* Only consider blocks <5M for the fragmentation % */
				if( $block[ 'size' ] < (5 * 1024 * 1024) )
					$fragsize+=$block[ 'size' ];
				$freetotal+=$block[ 'size' ];
			}
			$freeseg += count( $mem[ 'block_lists' ][ $i ] );
		}
		if( $freeseg > 1 ) {
			$frag = sprintf( "%.2f%% (%s out of %s in %d fragments)", ($fragsize / $freetotal) * 100, $this->bsize( $fragsize ), $this->bsize( $freetotal ), $freeseg );
		} else {
			$frag = "0%";
		}
		return $frag;
	}
	
	/**
	 * Small helper method to cleanup the code
	 *
	 * @param DataObjectSet $dataObjectSet
	 * @param string $name
	 * @param string $value 
	 */
	private function pushStatusData( $dataObjectSet, $name, $value ) {
		$dataObjectSet->push( new ArrayData( array( 'Name' => $name, 'Value' => $value ) ) );
	}
}