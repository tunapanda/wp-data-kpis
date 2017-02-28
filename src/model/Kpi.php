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
	private $data;
	private static $kpis=NULL;

	/**
	 * Construct.
	 */
	private function __construct($id, $data) {
		$this->id=$id;
		$this->title=$data["title"];
		$this->measureFunc=$data["measure_func"];
	}

	/**
	 * Get id.
	 */
	public function getId() {
		return $this->id;
	}

	/**
	 * Get title.
	 */
	public function getTitle() {
		return $this->title;
	}

	/**
	 * Get current value from the database.
	 */
	public function getStoredValue() {
		return KpiMeasurement::getCurrentKpiValue($this->id);
	}

	/**
	 * Get current value from the database.
	 */
	public function getHistoricalValues() {
		return KpiMeasurement::getHistoricalKpiValues($this->id);
	}

	/**
	 * Get measured value.
	 */
	public function getCurrentMeasurement() {
		if (!$this->measureFunc)
			return NULL;

		$options=array(
			"kpi"=>$this->getId()
		);

		$value=call_user_func($this->measureFunc,$options);

		if (is_null($value))
			return NULL;

		return floatval($value);
	}

	/**
	 * Has this KPI been measured today?
	 */
	public function isMeasuredToday() {
		return KpiMeasurement::isKpiMeasuredToday($this->id);
	}

	/**
	 * Measure the current value, and store in the database.
	 */
	public function measureAndStore() {
		$measurement=new KpiMeasurement();
		$measurement->setValue($this->getCurrentMeasurement());
		$measurement->setKpi($this->id);
		$measurement->save();
	}

	/**
	 * Find all available kpis. Returns an array indexed on the kpi id.
	 */
	public static function getAllAvailable() {
		if (is_null(Kpi::$kpis)) {
			Kpi::$kpis=array();
			$kpiDatas=array();
			$kpiDatas=apply_filters("register_kpis",$kpiDatas);
			foreach ($kpiDatas as $id=>$kpiData)
				Kpi::$kpis[$id]=new Kpi($id,$kpiData);
		}

		return Kpi::$kpis;
	}

	/**
	 * Get kpi by id.
	 */
	public static function getById($id) {
		$kpis=Kpi::getAllAvailable();
		return $kpis[$id];
	}
}