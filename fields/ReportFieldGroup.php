<?php
/**
 * Description of ReportFieldGroup
 *
 * @package serverstatus
 */
class ReportFieldGroup extends FieldGroup {

	/**
	 *
	 * @return string
	 */
	function FieldHolder() {
		$Field = $this->XML_val('Field');
		return <<<HTML
<div>$Field</div>
HTML;
	}
}
