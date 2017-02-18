<?php

namespace datakpi;

use \Exception;

/**
 * Handle the management of insights.
 */
class InsightController extends Singleton {

	/**
	 * Construct.
	 */
	public function __construct() {
		add_action("init",array($this,"init"));

		if (is_admin())
			add_filter("rwmb_meta_boxes",array($this,'rwmbMetaBoxes'));

		add_filter("the_content",array($this,"theContent"));
	}

	/**
	 * The WordPress init action.
	 */
	public function init() {
		register_post_type("insight",array(
			"labels"=>array(
				"name"=>"Insights",
				"singular_name"=>"Insight",
				"not_found"=>"No Insights found.",
				"add_new_item"=>"Add new Insight",
				"edit_item"=>"Edit Insight",
			),
			"public"=>true,
			"supports"=>array("title"),
			"show_in_nav_menus"=>false,
		));
	}

	/**
	 * Add meta boxes.
	 */
	public function rwmbMetaBoxes($metaBoxes) {
		global $wpdb;

		$kpiOptions=array();

		$kpis=DataKpiPlugin::getAvailableKpis();
		foreach ($kpis as $kpiId=>$kpiData)
			$kpiOptions[$kpiId]=$kpiData["title"];

		$metaBoxes[]=array(
	        'title'      => 'Appearance',
	        'post_types' => 'insight',
	        'fields'     => array(
	            array(
	                'type' => 'select',
	                'id'   => 'chartType',
	                'name' => "Chart Type",
	                'desc'=>"How should the insight be visualized?",
	                'options'=>array(
	                	"number"=>"Number",
	                	"pie"=>"Pie Chart",
	                	"line"=>"Line Chart"
	                )
	            ),
	        ),
		);

		$metaBoxes[]=array(
	        'title'      => 'KPIs',
	        'post_types' => 'insight',
	        'fields'     => array(
	            array(
	                'type' => 'select_advanced',
	                'id'   => 'kpis',
	                'name' => "KPIs",
	                'clone'=>true,
	                'sort_clone'=>true,
	                'desc'=>"Select the KPIs that should be displayed.",
	                'options'=>$kpiOptions
	            ),
	        ),
		);

		return $metaBoxes;
	}

	/**
	 * Implementation of the_content.
	 */
	public function theContent($content) {
		$post=get_post();

		if ($post->post_type!="insight")
			return $content;

		$insight=Insight::getById($post->ID);

		switch ($insight->getChartType()) {
			case "number":
				return "the_num_will_be_here";
				break;

			default:
				return "(unknown chart type: ".$insight->getChartType().")";
				break;
		}

		return $content;
	}
}