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
	}

	/**
	 * Measure KPIs.
	 */
	public function measure() {
		DataKpiPlugin::instance()->measureKpis();
	}
}