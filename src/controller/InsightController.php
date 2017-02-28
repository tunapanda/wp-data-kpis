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
		add_filter("template_include",array($this,"handleTemplateInclude"));

		add_shortcode("insight",array($this,"handleInsightShortcode"));
	}

	/**
	 * Handle the insight shortcode.
	 */
	public function handleInsightShortcode($args) {
		$insight=Insight::getById($args["id"]);
		return $this->renderInsight($insight);
	}

	/**
	 * Handle template_include. Make sure we use the page-template for
	 * pages, not the page-template for posts. This code doesn't seem
	 * to work. Don't know why... :(
	 */
	public function handleTemplateInclude($template) {
		$post_type=get_post_type();

		if ($post_type=="insight")
			$template=get_page_template();

		return $template;
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
	                	"histogram"=>"Current values and histogram",
	                	"pie"=>"Pie Chart",
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
		return $this->renderInsight($insight);
	}

	/**
	 * Render an insight. Returns the HTML;
	 */
	private function renderInsight($insight) {
		wp_enqueue_style("wp-data-kpis",
			DATAKPI_URL."/wp-data-kpis.css");

		wp_enqueue_script("canvasjs",
			DATAKPI_URL."/ext/canvasjs/jquery.canvasjs.min.js",
			array("jquery"));

		switch ($insight->getChartType()) {
			case "histogram":
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

					if (sizeof($kpi->getHistoricalValues())!=30)
						throw new Exception("Expected 30 and exactly 30 values, got: ".
							sizeof($kpi->getHistoricalValues()));
				}

				$days=array();
				$now=time();
				for ($i=29; $i>=0; $i--) {
					$t=$now-24*60*60*$i;
					$days[]=array(
						"y"=>date("Y",$t),
						"m"=>date("m",$t)-1,
						"d"=>date("d",$t)
					);
				}

				$template=new Template(__DIR__."/../view/insight-histogram.php");
				$insightContent=$template->render(array(
					"uid"=>"data-kpi-line-chart-".uniqid(),
					"title"=>$insight->getPost()->post_title,
					"kpis"=>$kpiData,
					"entryWidth"=>$widthByNum[$num],
					"days"=>$days
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