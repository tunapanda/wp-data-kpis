<?php

namespace datakpi;

/**
 * Represents one kpi that can be measured.
 * Instances of this class is created as needed, there is no direct
 * correlation between instances of this class and data stored in
 * the database.
 */
class Kpi {

	private $id;

	/**
	 * Construct.
	 */
	public function __construct($id) {
		$this->id=$id;
	}

	/**
	 * Get id.
	 */
	public function getId() {
		return $this->id;
	}

	/**
	 * Get current value from the database.
	 */
	public function getCurrentValue() {
		return KpiMeasurement::getCurrentKpiValue($this->id);
	}

	/**
	 * Get current value from the database.
	 */
	public function getHistoricalValues() {
		return KpiMeasurement::getHistoricalKpiValues($this->id);
	}
}