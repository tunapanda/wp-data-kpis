<?php

namespace datakpi;

use \Exception;

/**
 * Visual test cases.
 */
class TestController extends Singleton {

	/**
	 * Construct.
	 */
	protected function __construct() {
		add_shortcode("kpi-test",array($this,"handleTestShortcode"));
	}

	/**
	 * Handle the test shortcode.
	 */
	public function handleTestShortcode($param) {
		wp_enqueue_style("wp-data-kpis",
			DATAKPI_URL."/wp-data-kpis.css");

		wp_enqueue_script("canvasjs",
			DATAKPI_URL."/ext/canvasjs/jquery.canvasjs.min.js",
			array("jquery"));

		$num=2;
		if (isset($_REQUEST["num"]))
			$num=$_REQUEST["num"];

		$colors=array("#f00","#0f0","#00f");

		$kpiData=array();
		for ($i=0; $i<$num; $i++)
			$kpiData[]=array(
				"name"=>"Dummy Chart ".($i+1),
				"currentValue"=>($i+1)*100,
				"historicalValues"=>range(1+$i*5,30+$i*5),
				"color"=>$colors[$i%sizeof($colors)],
			);

		$widthByNum=array(1=>"100%",2=>"50%",3=>"33%",4=>"25%");

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
			"uid"=>"unique",
			"title"=>"Dummy Chart Data",
			"kpis"=>$kpiData,
			"entryWidth"=>$widthByNum[$num],
			"days"=>$days
		));

		$template=new Template(__DIR__."/../view/insight.php");
		$content=$template->render(array(
			"title"=>"Dummy Post Title",
			"content"=>$insightContent,
		));

		return $content;
	}
}