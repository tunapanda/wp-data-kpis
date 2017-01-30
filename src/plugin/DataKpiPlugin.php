<?php

namespace datakpi;

/**
 * The main plugin class.
 */
class DataKpiPlugin extends Singleton {

	/**
	 * Construct.
	 */
	public function __construct() {
		$pluginFileName=$this->getPluginFileName();
		register_activation_hook($pluginFileName,array($this,"activate"));
		register_deactivation_hook($pluginFileName,array($this,"deactivate"));
		register_uninstall_hook($pluginFileName,array($this,"uninstall"));

		add_action("datakpi_measure",array($this,"measureKpis"));

		InsightController::instance();
		WpCliController::instance();
	}

	/**
	 * Get available KPI:s.
	 */
	public function getAvailableKpis() {
		$kpis=array();
		$kpis=apply_filters("register_kpis",$kpis);
		return $kpis;
	}

	/**
	 * Get plugin filename.
	 */
	public function getPluginFileName() {
		return DATAKPI_PATH."/wp-data-kpis.php";
	}

	/**
	 * Measure the current state of the KPIs.
	 * Untested!
	 */
	public function measureKpis() {
		$kpis=array();
		$kpis=apply_filters("measure_kpis",$kpis);

		foreach ($kpis as $kpi=>$value) {
			$measurement=new KpiMeasurement();
			$measurement->setValue($value);
			$measurement->setKpi($kpi);
			$measurement->save();
		}
	}

	/**
	 * Activate the plugin.
	 */
	public function activate() {
		KpiMeasurement::install();

		if (!wp_next_scheduled("datakpi_measure"))
			wp_schedule_event(time(),"hourly","datakpi_measure");
	}

	/**
	 * Deactivate.
	 */
	public function deactivate() {
		wp_clear_scheduled_hook("datakpi_measure");
	}

	/**
	 * Uninstall.
	 */
	public function uninstall() {
		KpiMeasurement::uninstall();
	}
}