<?php

namespace datakpi;

/**
 * Represents one insigt.
 */
class Insight extends PostTypeModel {

	protected static $posttype="insight";
	private $kpis;

	/**
	 * Get the type of insight.
	 */
	public function getChartType() {
		return $this->getMeta("chartType");
	}

	/**
	 * Return an array of Kpi objects.
	 */
	public function getKpis() {
		if (!is_array($this->kpis)) {
			$this->kpis=array();
			$ids=$this->getMeta("kpis");
			foreach ($ids as $id)
				$this->kpis[]=new Kpi($id);
		}

		return $this->kpis;
	}
}