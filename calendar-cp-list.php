<?php
if (isset($_REQUEST['format']) && $_REQUEST['format'] == "json_fc"){
	$args = array(
		'post_type' => 'event_cp',
		'date_query' => array(
			'after' => $_REQUEST['after'],
			'before' => $_REQUEST['before']
		)
	);
	// The Query
	$the_query = new WP_Query( $args );
	// The Loop
	if ( $the_query->have_posts() ) {
		echo "[";
		while ( $the_query->have_posts() ) {
			$the_query->the_post();
			echo '{"title": "'.$post->post_title.'","start": "'.date( "Y-m-d\TH:i:s",strtotime($post->post_date)).'","end": "'.date( "Y-m-d\TH:i:s",(strtotime($post->post_date)+$post->menu_order)).'", "url":"'.get_permalink().'"}';
			if ($the_query->current_post+1 != $the_query->post_count) echo ",";
		}
		echo "]";
	}
}else{
get_header(); ?>

<section id="primary" class="content-area">
	<div id="content" class="site-content" role="main">

		<header class="page-header">
			<h1 id="page-title">Calendar</h1>
			<?php ?>
		</header><!-- .page-header -->
<script>
	jQuery(document).ready(function() {
		Date.prototype.yyyymmdd = function() {
			var yyyy = this.getFullYear().toString();
			var mm = (this.getMonth()+1).toString(); // getMonth() is zero-based
			var dd  = this.getDate().toString();
			return yyyy + '-' + (mm[1]?mm:"0"+mm[0]) + '-' + (dd[1]?dd:"0"+dd[0]); // padding
		};
		Date.prototype.yyyymmddhhiiss = function() {
			var hh = this.getHours().toString();
			var ii = this.getMinutes().toString();
			var ss = this.getSeconds().toString();
			return this.yyyymmdd() + 'T' + (hh[1]?hh:"0"+hh[0]) + ':' + (ii[1]?ii:"0"+ii[0]) + ':' + (ss[1]?ss:"0"+ss[0]); // padding
		};
		console.log(<? echo @$wp_query->query_vars['format']; ?>)
		d = new Date();
		jQuery('#calendar').fullCalendar({
			header: {
				left: 'prev,next today',
				center: 'title',
				right: 'month,agendaWeek,agendaDay'
			},
			startParam: 'after',
			endParam: 'before',
			defaultDate: d.yyyymmdd(),
			<?
				if ( is_day() ) :
					echo "defaultView: 'basicDay',";
				elseif ( is_month() ) :
					echo "defaultView: 'month',";
				elseif ( isset($wp_query->query['w'])) :
					echo "defaultView: 'basicWeek',";
				endif;
			?>
			
			editable: false,
			eventLimit: false, // allow "more" link when too many events
			eventSources:[
				"?post_type=event_cp&format=json_fc"
			]
		});
		
	});


</script>
<div id="calendar"></div>

	</div><!-- #content .site-content -->
</section><!-- #primary .content-area -->

<?php get_sidebar(); ?>
<?php get_footer(); ?>
<? } ?>