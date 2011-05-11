<?php
/**
 * ServerHealthReport contains some tweaks so that I can display report data 
 * not as a table but with label and read only fieldvalue
 *
 * @package serverstatus
 */
abstract class ServerHealthReport extends SS_Report {

	/**
	 *
	 * @return FieldSet
	 */
	public function getCMSFields() {
		$fields = new FieldSet(
			new LiteralField(
				'ReportTitle',
				 "<h3>{$this->title()}</h3>"
			)
		);

		if( $this->description ) {
			$fields->push(
				new LiteralField('ReportDescription', "<p>{$this->description}</p>")
			);
		}

		$fields->push( new ReportFieldGroup( 'ReportContent', $this->getReportFields() ) );
		return $fields;
	}

	/**
	 *
	 * @return Tab
	 */
	abstract public function getReportFields();


	/**
	 * Returns a percentage
	 *
	 * @param float $full
	 * @param float $part
	 * @return string
	 */
	protected function percentage( $full, $part ) {
		return round( $part/$full*100 ) . '%';
	}

	/**
	 * Converts bytes to human readable
	 *
	 * @param int $s
	 * @return string
	 */
	protected function bsize( $s ) {
		foreach( array( '', 'K', 'M', 'G' ) as $i => $k ) {
			if( $s < 1024 ) {
				break;
			}
			$s/=1024;
		}
		return sprintf( "%0.1f %sB", $s, $k );
	}

	/**
	 * returns a duration
	 *
	 * @param int $ts
	 * @return string
	 */
	protected function duration( $ts ) {
		$time = time();
		$years = (int) ((($time - $ts) / (7 * 86400)) / 52.177457);
		$rem = (int) (($time - $ts) - ($years * 52.177457 * 7 * 86400));
		$weeks = (int) (($rem) / (7 * 86400));
		$days = (int) (($rem) / 86400) - $weeks * 7;
		$hours = (int) (($rem) / 3600) - $days * 24 - $weeks * 7 * 24;
		$mins = (int) (($rem) / 60) - $hours * 60 - $days * 24 * 60 - $weeks * 7 * 24 * 60;
		$str = '';
		if( $years == 1 )
			$str .= "$years year, ";
		if( $years > 1 )
			$str .= "$years years, ";
		if( $weeks == 1 )
			$str .= "$weeks week, ";
		if( $weeks > 1 )
			$str .= "$weeks weeks, ";
		if( $days == 1 )
			$str .= "$days day,";
		if( $days > 1 )
			$str .= "$days days,";
		if( $hours == 1 )
			$str .= " $hours hour and";
		if( $hours > 1 )
			$str .= " $hours hours and";
		if( $mins == 1 )
			$str .= " 1 minute";
		else
			$str .= " $mins minutes";
		return $str;
	}
}
