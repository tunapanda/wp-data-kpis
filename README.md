# wp-data-kpis
Measure various [KPIs](http://searchcrm.techtarget.com/definition/key-performance-indicator) related to a running WordPress site, and present them in a unified way.

This is how the plugins is intended to work:

* This plugin measures a number of KPIs.
* The KPIs can be delivered by other plugins.
* One filter is used to find out the set of possible KPIs to measure. Other plugins can implement the filter and thereby add to the number of possible KPIs to measure. Adding a KPI would be done something like this for the plugin:
```
function myplugin_register_kpis($kpis) {
	$kpis[]=array(
		"name"=>"itemsBought",
		"title"=>"Bought items",
		"description"=>"Measures "
	);

	return $kpis;
}

add_filter("register_kpis","myplugin_register_kpis");
``` 
* One filter will be used to actually measure the KPIs. The code should, whenever it is asked, return the value for its KPIs from the last 24 hours. The code to return the data from a plugin would look like:
```
function myplugin_measure_kpis($values) {
	global $wpdb;

	$val=$wpdb->get_var("SELECT COUNT(*) FROM checkouts WHERE timestamp>=DATE_SUB(NOW(),INTERVAL 1 DAY)");
	$kpis["itemsBought"]=$val;

	return $kpis;
}

add_filter("measure_kpis","myplugin_measure_kpis");
```
* When gathering KPIs, the granularity of how to measure it is fixed and it is per day. It is not necesarily the only way to display the information. But the way that it is measuerd is always per day.
* It is possible to create a "dashboard" or "data view". In order to create a data view, a number of KPIs are specified. The data view will the historical values of these KPIs as different charts. One line on the chart per KPI.
* It is possible to create new KPIs by combining existing ones with an expression. For example, it would be possible to create the KPI `numberOfItemsBoughtPerVisitor` using the formula `itemsBought / numberOfVisitors`.
* The plugin has a REST endpoint to deliver all the current values of all defined KPIs.
* The plugin can be configured to combine data gathered at other sites. This can be done in two ways:
  * The plugin can go an ask the other sites for the current KPI values, using the REST API. This requires the other site to be publically visible on the Internet, which does not work for an intranet behind a firewall, etc.
  * The plugin on the remote site can be configured to send its KPIs nightly to a central location. The central location is also running an instance of the plugin.
* Some types of KPIs represent just a number and can't be broken down further. An example would be `numberOfNewPagesPublished`, which would represent the number of new WordPress posts published in the last 34 hours. Some kinds of KPIs are interesting to break down, such as `pageViews`. In this case it is interesting to know the total number of page views, but also the number of pageViews of each page. In the first case, the code for the `measure_kpis` filter would look like above, and a single value would be placed by the function in the `$values` array. In the case where the KPI can be broken down, an array with the broken down values would be placed in the array indexed by a textual slug. Something like below (the function `get_number_of_visitors_for_page` is imaginary):
```
function myplugin_measure_kpis($values) {
	$vals=array();

	foreach (get_pages() as $page)
		$vals[$page->post_name]=get_number_of_visitors_for_page($page);

	$kpis["itemsBought"]=$vals;

	return $kpis;
}

add_filter("measure_kpis","myplugin_measure_kpis");
```
* The plugin hook can be implemented in the plugin generating the data that should be measured. In the case of Ubercart, it would mean that the hooks described above would actually be implemented in the Ubercart plugin. This might not be feasible, since it would require getting code commited to an existing and well established project. In this case, the implementation of the hooks could be done in a 3rd party plugin. So you would have:
  * `wp-data-kpis` - Responsible for querying the KPIs, storing them in the database and displaying them.
  * `ubercart` - A webshop. Generates a lot of interesting data.
  * `ubercart-data-kpis` - Implements the hooks to present the data from `ubercart` so that `wp-data-kpis` can understand them.

## Motivation

There are a number of plugins to measure things in WordPress, see e.g. [here](http://socialmetricspro.com/social-media/10-most-popular-wordpress-analytics-plugins-review/2739/). However, they all have a number of limitations:
* The kind of data that is measurable by those plugins is coded into the plugin.
* It is not possible easily to combine data that comes from different places. For example, there are a number of web shop related things that can be measured by [Ubercart](http://www.ubercart.org/docs/user/323/viewing_reports). There are several plugins that measure things related to visitor behaviour, for example [Wp Statistics](https://wordpress.org/plugins/wp-statistics/). These both plugins are good at what they do, but in order to view the numbers they generate, you would have to go to 2 different places in the WordPress admin. If you would want to combine them in order to try and understand how one value might affect another, you need to export the data and import it into a spreadsheet program. The `wp-data-kpis` plugin tries to simplify this. 
