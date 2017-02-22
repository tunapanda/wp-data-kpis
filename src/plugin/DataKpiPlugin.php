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
		return Kpi::getAllAvailable();
	}

	/**
	 * Get plugin filename.
	 */
	public function getPluginFileName() {
		return DATAKPI_PATH."/wp-data-kpis.php";
	}

	/**
	 * Measure the current state of the KPIs.
	 */
	public function measureKpis() {
		foreach (Kpi::getAllAvailable() as $kpi)
			$kpi->measureAndStore();
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