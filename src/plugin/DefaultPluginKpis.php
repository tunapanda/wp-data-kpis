<?php 
namespace datakpi;

/**
 *	KPIs that the plugin measures by default.
 */
class DefaultPluginKpis extends Singleton{

	/**
	 * Constructor.
	 */
	public function __construct(){
		add_filter('measure_kpis', array($this, 'measure_posts_kpi'));
		add_filter('register_kpis', array($this, 'register_posts_kpi'));
	}

	/**
	 * Implementation of the measure_kpis filter.
	 */
	public function measure_posts_kpi($kpis){
		$all_posts = wp_count_posts('post');
		$kpis['published_posts'] = floatval($all_posts->publish);
		return $kpis;
	}

	/**
	 * Implementation of the register_kpis filter.
	 */
	public function register_posts_kpi($kpis){
		$kpis['published_posts']=array(
			"title"=>"Published Posts"
		);

		return $kpis;
	}
}
