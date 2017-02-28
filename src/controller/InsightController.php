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
		foreach ($kpis as $kpi)
			$kpiOptions[$kpi->getId()]=$kpi->getTitle();

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

		wp_enqueue_style("wp-data-kpis",
			DATAKPI_URL."/wp-data-kpis.css");

		wp_enqueue_script("canvasjs",
			DATAKPI_URL."/ext/canvasjs/jquery.canvasjs.min.js",
			array("jquery"));

		$insight=Insight::getById($post->ID);

		switch ($insight->getChartType()) {
			case "number":
				$kpis=$insight->getKpis();
				if (sizeof($kpis)!=1)
					return "A number chart can only have one value.";

				$firstKpi=$kpis[0];
				$template=new Template(__DIR__."/../view/insight-number.php");
				$data=array(
					"value"=>$firstKpi->getCurrentValue()
				);
				$insightContent=$template->render($data);
				break;

			case "line":
				$kpis=$insight->getKpis();
				if (!sizeof($kpis))
					return "A line chart must have at least one value.";

				$widthByNum=array(1=>"100%",2=>"50%",3=>"33%",4=>"25%");
				$colors=array("#f00","#0f0","#00f","#ff0","#f0f","0ff");
				$num=sizeof($kpis);

				$kpiData=array();
				foreach ($kpis as $i=>$kpi) {
					$kpiData[]=array(
						"name"=>$kpi->getTitle(),
						"currentValue"=>$kpi->getStoredValue(),
						"historicalValues"=>$kpi->getHistoricalValues(),
						"color"=>$colors[$i%sizeof($colors)],
					);
				}

				$template=new Template(__DIR__."/../view/insight-line.php");
				$insightContent=$template->render(array(
					"uid"=>"data-kpi-line-chart-".uniqid(),
					"title"=>$insight->getPost()->post_title,
					"kpis"=>$kpiData,
					"entryWidth"=>$widthByNum[$num]
				));
				break;

			default:
				$insightContent="(unknown chart type: ".$insight->getChartType().")";
				break;
		}

		$template=new Template(__DIR__."/../view/insight.php");
		$data=array(
			"title"=>$insight->getPost()->post_title,
			"content"=>$insightContent
		);

		return $template->render($data);
	}
}