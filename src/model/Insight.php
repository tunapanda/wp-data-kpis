<?php

namespace datakpi;

/**
 * Represents one insigt.
 */
class Insight extends PostTypeModel {

	protected static $posttype="insight";

	/**
	 * Get the type of insight.
	 */
	public function getChartType() {
		return $this->getMeta("chartType");
	}

	/**
	 * Get kpi ids.
	 */
	public function getKpiIds() {
		return $this->getMeta("kpis");
	}

	/**
	 * Get kpi values.
	 */
	public function getKpiValues() {
		$ids=$this->getKpiIds();
		$vals=array();

		foreach ($ids as $id)
			$vals[]=KpiMeasurement::getCurrentKpiValue($id);

		return $vals;
	}
}