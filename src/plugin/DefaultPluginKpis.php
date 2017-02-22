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
		add_filter('register_kpis', array($this, 'register_posts_kpi'));
	}

	/**
	 * Measure published posts.
	 */
	public function measurePublishedPosts() {
		$count=wp_count_posts('post');

		return $count->publish;
	}

	/**
	 * Implementation of the register_kpis filter.
	 */
	public function register_posts_kpi($kpis){
		$kpis['published_posts']=array(
			"title"=>"Published Posts",
			"measure_func"=>array($this,"measurePublishedPosts")
		);

		return $kpis;
	}
}
