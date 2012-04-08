<?php
/**
 * Description of ServerStatus
 *
 * @package serverstatus
 */
class GeneralStatusReport extends ServerHealthReport {

	/**
	 *
	 * @var string
	 */
	protected $title = "Server health - General";
	/**
	 *
	 * @var string
	 */
	protected $description = "See the status of the server";

	/**
	 *
	 * @return Tab
	 */
	public function getReportFields() {
		$tab = new Tab( 'general', 'General' );
		foreach( $this->getStatus() as $data ) {
			$tab->push( new ReadonlyField( $data->Name , $data->Name, $data->Value ) );
		}
		return $tab;
	}
	
	/**
	 * Does the brute work of return a 'fake' dataobject set with the actual data
	 * displayed in the report
	 * 
	 * @return ArrayList
	 */
	public function getStatus() {
		$list = new ArrayList();
		if( !empty( $_SERVER[ 'SERVER_NAME' ] ) ) {
			$host = ( getenv( 'HOSTNAME' ) )?' (' . getenv( 'HOSTNAME' ) . ')':'';
			$list->push( new ArrayData( array( 'Name' => 'Hostname', 'Value' => $_SERVER[ 'SERVER_NAME' ] . $host ) ) );
		}
		if( !empty( $_SERVER[ 'SERVER_SOFTWARE' ] ) ) {
			$list->push( new ArrayData( array( 'Name' => 'Server software', 'Value' => $_SERVER[ 'SERVER_SOFTWARE' ] ) ) );
		}
		$list->push( new ArrayData( array( 'Name' => 'PHP version', 'Value' => phpversion() ) ) );
		$list->push( new ArrayData( array( 'Name' => 'Serverload', 'Value' => $this->getServerLoad() ) ) );
		return $list;
	}

	/**
	 * Tries to fetch the average serverload on a MAC, Win and *nix environments
	 *
	 * @return string
	 */
	protected function getServerLoad() {
		$os = strtolower( PHP_OS );
		if( strpos( $os, 'darwin') === 0 ) {
			$loadAverage = exec('sysctl vm.loadavg');
			preg_match( '|\{([^\}]*)\}|', $loadAverage, $matches );
			return ( $matches[ 1 ] );
		}
		elseif( strpos( $os, "win" ) === false ) {
			if( file_exists( "/proc/loadavg" ) ) {
				$load = file_get_contents( "/proc/loadavg" );
				$load = explode( ' ', $load );
				return $load[ 0 ].' '.$load[ 1 ].' '.$load[ 2 ];
			} elseif( function_exists( "shell_exec" ) ) {
				$load = explode( ' ', `uptime` );
				return $load[ count( $load ) - 1 ];
			} else {
				return "";
			}
		} elseif( strpos( $os, "win") === true ) {
			if( class_exists( "COM" ) ) {
				$wmi = new COM( "WinMgmts:\\\\." );
				$cpus = $wmi->InstancesOf( "Win32_Processor" );

				$cpuload = 0;
				$i = 0;
				while( $cpu = $cpus->Next() ) {
					$cpuload += $cpu->LoadPercentage;
					$i++;
				}

				$cpuload = round( $cpuload / $i, 2 );
				return "$cpuload%";
			} else {
				return "";
			}
		}
	}

	/**
	 * @todo remove when the report admin has been fixed
	 * @return void
	 */
	public function forTemplate(){
		return;
	}
}