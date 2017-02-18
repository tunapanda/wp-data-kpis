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
}