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

	/**
	 * Has this kpi been measured today.
	 */
	public static function isKpiMeasuredToday($kpiId) {
		global $wpdb;

		$day=$wpdb->get_var($wpdb->prepare(
				"SELECT   day ".
				"FROM     {$wpdb->prefix}kpimeasurement ".
				"WHERE    kpi=%s ".
				"ORDER BY timestamp DESC ".
				"LIMIT 1",
				$kpiId
			));

		if ($wpdb->last_error)
			throw new Exception($wpdb->last_error);

		if ($day==date("Y-m-d"))
			return TRUE;

		return FALSE;
	}

	/**
	 * Get the current kpi value from the database.
	 */
	public static function getCurrentKpiValue($kpiId) {
		global $wpdb;

		$val=$wpdb->get_var($wpdb->prepare(
				"SELECT   value ".
				"FROM     {$wpdb->prefix}kpimeasurement ".
				"WHERE    kpi=%s ".
				"ORDER BY timestamp DESC ".
				"LIMIT 1",
				$kpiId
			));

		if ($wpdb->last_error)
			throw new Exception($wpdb->last_error);

		return $val;
	}

	/**
	 * Get a 30 day history of the measured kpi values from the database.
	 * Will return an array where the value at index 0 is the oldest value,
	 * and the value at index 29 is the current value.
	 */
	public static function getHistoricalKpiValues($kpiId) {
		global $wpdb;

		$rows=$wpdb->get_results($wpdb->prepare(
				"SELECT   day,avg(value) AS value ".
				"FROM     {$wpdb->prefix}kpimeasurement ".
				"WHERE    kpi=%s ".
				"GROUP BY day",
				$kpiId
			),ARRAY_A);

		if ($wpdb->last_error)
			throw new Exception($wpdb->last_error);

		$valueByDay=array();
		foreach ($rows as $row)
			$valueByDay[$row["day"]]=$row["value"];

		$t=time();
		$res=array();
		for ($i=0; $i<30; $i++) {
			$day=date("Y-m-d",$t-(29-$i)*60*60*24);

			if (isset($valueByDay[$day]))
				$res[]=$valueByDay[$day];

			else
				$res[]=NULL;
		}

		return $res;
	}
}