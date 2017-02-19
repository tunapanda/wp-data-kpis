<div class="line">
	<div class="wp-data-kpi-line-chart" id="<?php echo $uid; ?>"></div>
</div>

<script>
	jQuery(function($) {
		$(document).ready(function() {
			$("#<?php echo $uid; ?>").CanvasJSChart({
				title: {
					text: "<?php echo esc_attr($title); ?>"
				},
				axisX: {
					interval: 10
				},
				data: [{
					type: "line",
					dataPoints: [
						<?php foreach ($data as $index=>$value) { ?>
							{
								x: <?php echo $index; ?>,
								y: <?php echo $value?$value:"null"; ?>
							},
						<?php } ?>
					]
				}]
			});
		});
	});
</script>