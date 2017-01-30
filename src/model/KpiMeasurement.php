<?php

namespace datakpi;

use \WpRecord;

/**
 * One measured kpi.
 */
class KpiMeasurement extends WpRecord {

	/**
	 * Constructor.
	 */
	public function __construct() {
		$this->timestamp=time();
		$this->day=date("Y-m-d");
	}

	/**
	 * Initialize.
	 */
	public static function initialize() {
		self::field("id","integer not null auto_increment");
		self::field("kpi","varchar(255) not null");
		self::field("timestamp","integer not null");
		self::field("value","float not null");
		self::field("day","varchar(32) not null");
	}

	/**
	 * Set value.
	 */
	public function setValue($value) {
		$this->value=$value;
	}

	/**
	 * Get value.
	 */
	public function getValue() {
		return $this->value;
	}

	/**
	 * Set kpi.
	 */
	public function setKpi($kpi) {
		$this->kpi=$kpi;
	}

	/**
	 * Get kpi.
	 */
	public function getKpi() {
		return $this->kpi;
	}
}