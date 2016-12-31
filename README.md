# wp-data-kpis
Measure various kpis and present them in a unified way.

* This plugin measures a number of KPIs.
* The KPIs can be delivered by other plugins.
* One filter is used to find out the set of possible KPIs to measure. Other plugins can implement the filter and thereby add to the number of possible KPIs to measure.
* One filter will be used to actually measure the KPIs.
* In the plugin, it is possible to create a "dashboard" or "data view". In order to create a data view, a number of KPIs are specified. The data view will the historical values of this KPIs.

## Motivation

There are a number of plugins to measure things in WordPress, see e.g. [here](http://socialmetricspro.com/social-media/10-most-popular-wordpress-analytics-plugins-review/2739/). However, they all have a number of limitations:
* The kind of data that is measurable by those plugins is coded into the plugin.
* It is not possible easily to combine data that comes from different places.
