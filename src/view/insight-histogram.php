<div class="kpi-histogram-holder">
	<div class="kpi-histogram-summary">
		<?php foreach ($kpis as $kpi) { ?>
			<div class="kpi-histogram-entry" style="width: <?php echo $entryWidth; ?>">
				<div class="kpi-histogram-number">
					<?php echo $kpi["currentValue"]; ?>
				</div>
				<div class="kpi-histogram-legend">
					<div class="kpi-histogram-marker" style="background: <?php echo $kpi["color"]; ?>"></div>
					<?php echo $kpi["name"]; ?>
				</div>
			</div>
		<?php } ?>
	</div>
	<div class="wp-data-kpi-line-chart" id="<?php echo $uid; ?>"></div>
</div>

<script>
	jQuery(function($) {
		$(document).ready(function() {
			$("#<?php echo $uid; ?>").CanvasJSChart({
				/*title: {
					text: "<?php echo esc_attr($title); ?>"
				},*/
				axisX: {
					interval: 10
				},
				data: [
					<?php foreach ($kpis as $kpi) { ?>
						{
							type: "line",
							name: '<?php echo $kpi["name"]; ?>',
							color: '<?php echo $kpi["color"]; ?>',
							dataPoints: [
								<?php foreach ($kpi["historicalValues"] as $index=>$value) { ?>
									{
										x: new Date(2012, 00, <?php echo $index+1; ?>),
										y: <?php echo $value?$value:"null"; ?>
									},
								<?php } ?>
							]
						},
					<?php } ?>
				]
			});
		});
	});
</script>