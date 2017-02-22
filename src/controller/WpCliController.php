<?php

namespace datakpi;

use \Exception;
use \WP_CLI;

/**
 * Handle command line interaction.
 */
class WpCliController extends Singleton {

	/**
	 * Constructor.
	 */
	public function __construct() {
		if (!class_exists("WP_CLI"))
			return;

		WP_CLI::add_command("kpis measure",array($this,'measure'));
		WP_CLI::add_command("kpis list", array($this, 'list'));
		WP_CLI::add_command("kpis peek", array($this, 'peek'));
	}

	/**
	 * Measure current values of the kpis and save to the database.
	 */
	public function measure() {
		DataKpiPlugin::instance()->measureKpis();

		WP_CLI::success("Kpis measured");
	}

	/**
	 * List available kpis registered with the system.
	 */
	public function list(){
		$kpis=DataKpiPlugin::instance()->getAvailableKpis();
		$items=array();

		foreach ($kpis as $kpi)
			$items[]=array(
				"id"=>$kpi->getId(),
				"title"=>$kpi->getTitle(),
				"value"=>$kpi->getStoredValue()
			);

		WP_CLI\Utils\format_items("table",$items,array("id","title","value"));
	}

	/**
	 * Peek at the current values, but don't store anything.
	 */
	public function Peek(){
		$kpis=DataKpiPlugin::instance()->getAvailableKpis();

		foreach ($kpis as $kpi)
			$items[]=array(
				"id"=>$kpi->getId(),
				"title"=>$kpi->getTitle(),
				"value"=>$kpi->getCurrentMeasurement()
			);

		WP_CLI\Utils\format_items("table",$items,array("id","title","value"));
	}
}