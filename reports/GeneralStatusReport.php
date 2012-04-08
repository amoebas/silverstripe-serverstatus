<?php

/**
 * GeneralStatusReport reports about basic Operative system values like load and
 * Server software version etc.
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
		$tab = new Tab('general', 'General');
		foreach($this->getStatus() as $data) {
			$tab->push(new ReadonlyField($data->Name, $data->Name, $data->Value));
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
		if(!empty($_SERVER['SERVER_NAME'])) {
			$host = ( getenv('HOSTNAME') ) ? ' (' . getenv('HOSTNAME') . ')' : '';
			$list->push(new ArrayData(array('Name' => 'Hostname', 'Value' => $_SERVER['SERVER_NAME'] . $host)));
		}
		if(!empty($_SERVER['SERVER_SOFTWARE'])) {
			$list->push(new ArrayData(array('Name' => 'Server software', 'Value' => $_SERVER['SERVER_SOFTWARE'])));
		}
		$list->push(new ArrayData(array('Name' => 'PHP version', 'Value' => phpversion())));
		$list->push(new ArrayData(array('Name' => 'Serverload', 'Value' => $this->getServerLoad())));
		return $list;
	}

	/**
	 * Tries to fetch the average serverload on a MAC, Win and *nix environments
	 *
	 * @return string
	 */
	protected function getServerLoad() {
		$os = strtolower(PHP_OS);
		if(strpos($os, 'darwin') === 0) {
			return $this->osxLoad();
		} elseif(strpos($os, "win") === false) {
			return $this->nixLoad();
		} elseif(strpos($os, "win") === true) {
			return $this->winLoad();
		}
	}

	/**
	 *
	 * @return string
	 */
	protected function winLoad() {
		if(!class_exists("COM")) {
			return "";
		}

		$wmi = new COM("WinMgmts:\\\\.");
		$cpus = $wmi->InstancesOf("Win32_Processor");

		$cpuload = 0;
		$i = 0;
		while($cpu = $cpus->Next()) {
			$cpuload += $cpu->LoadPercentage;
			$i++;
		}

		$cpuload = round($cpuload / $i, 2);
		return "$cpuload%";
	}

	/**
	 *
	 * @return string
	 */
	protected function nixLoad() {
		if(file_exists("/proc/loadavg")) {
			$load = file_get_contents("/proc/loadavg");
			$load = explode(' ', $load);
			return $load[0] . ' ' . $load[1] . ' ' . $load[2];
		} elseif(function_exists("shell_exec")) {
			$load = explode(' ', `uptime`);
			return $load[count($load) - 1];
		} else {
			return "";
		}
	}

	/**
	 *
	 * @return string
	 */
	protected function osxLoad() {
		$loadAverage = exec('sysctl vm.loadavg');
		preg_match('|\{([^\}]*)\}|', $loadAverage, $matches);
		return implode(' / ', explode(' ', trim($matches[1])));
	}

	/**
	 * @todo remove when the report admin has been fixed
	 * @return void
	 */
	public function forTemplate() {
		return;
	}
}