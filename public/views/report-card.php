<?php
/**
 * Template tag for outputting the SA Leader Report.
 *
 * Community Commons Salud America
 *
 * @package   Community_Commons_Salud_America
 * @author    Yan Barnett
 * @license   GPL-2.0+
 * @link      http://www.communitycommons.org
 * @copyright 2013 Community Commons
 */

/**
 * Generate Leader Report within the group.
 *
 * Output is accomplished via a template tag, for easy insertion in group pages.
 *
 * @since   1.8.0
 *
 * @return  string The html for the leader report
 */
function sa_report_card() {
	/*
	 * Is there a geoid set? We determine whether to show the report or the county
	 * selector based on this variable.
	 */
	$geoid = isset( $_GET['geoid'] ) ? $_GET['geoid'] : '';
	if (strlen($geoid) != 12 || preg_match('/^05000US\d{5}/i', $geoid) == 0) $geoid = '';
	?>
	<div class="content-row clear">
		<?php
		if (! $geoid):
		?>

			<h2 class="screamer sablue no-top-margin">See How Your Area Stacks Up in Obesity, Food Access, Physical Activity &amp; Equity</h2>

			<p>
				The <em>Salud America!</em> Salud Report Card highlights health issues in your county (vs. state + nation) with data, policy solutions, research,
				and stories so you can start and support healthy changes for Latino kids.
			</p>

		   <div id="sa-report-selection">
				<?php
			// User isn't logged in.
			if ( ! bp_loggedin_user_id() ) :
				?>
				Please <a class="login-link" href="<?php echo wp_login_url( ( is_ssl() ? 'https://' : 'http://' ) .  $_SERVER["HTTP_HOST"] . $_SERVER['REQUEST_URI'] ); ?>" title="Log in"><b>log in</b></a> to see your report card. If you don't have a Community Commons account and would like to join us, please <a href="<?php echo site_url( bp_get_signup_slug() . '?salud-america=1' ); ?>"><b>register</b></a>.
			<?php
			elseif ( ! sa_is_current_user_a_member() ) :
			?>
				<p style="margin-bottom:0.6em;">You must be registered with Salud America before you can create a report card.</p>
				<?php echo sa_get_auxiliary_signup_form(); ?>
			<?php
			else :
			?>
				<div id="select-county">Select your state and county to see your own report card:</div>
				<select id="state-list">
					<option value="" selected>--- Select a State ---</option>
				</select>
				<select id="county-list">
					<option value="" selected>--- Select a County ---</option>
				</select>
				<span id="report-wait-message">Preparing your report card, please wait...</span>
			<?php
			endif;
			?>
			</div>

			<p><strong>How can you use it?</strong></p>
			<p>Let people know what health issues are important to you!</p>
			Email the link to:
			<ul>
				<li>Your local and state PTA</li>
				<li>Your county and city health department</li>
				<li>Your friends, family, teachers</li>
			</ul>
			Print the report and hand it to:
			<ul>
				<li>Your city and school leaders</li>
			</ul>
			Share the report:
			<ul>
				<li>On social media</li>
				<li>With your neighborhood association, town halls, public health departments</li>
			</ul>
			<p><strong>You know the issues...now start a change!</strong></p>
			<p>Want to solve one of these health issues?</p>
			<p>
			<a href="http://www.communitycommons.org/groups/salud-america">Log in to our website</a> and use our research, which suggests lots of achievable healthy changes,
			or our many posts about policy changes happening now. Or follow the footsteps of Salud Heroes and how they made change, like opening locked playgrounds after school hours!
			</p>
			<p><strong>Get help</strong></p>
			<p>
			Email our Salud America! digital curators, Eric, Lisa, and Amanda at <a href="mailto:saludamerica@uthscsa.edua">saludamerica@uthscsa.edu</a>.
			They can answer questions and help you access information and data/maps on many other topics.
			</p>

		<?php
		else:
			// Generate the report.
			$group_url = bp_get_group_permalink();
			$image_url = plugins_url('images/report-card/', dirname(__FILE__));
			$plugin_base_url = sa_get_plugin_base_uri();

			// Fetch big bet terms.
			$big_bets_base_url = sa_get_section_permalink( 'big_bets' );
			$big_bets = get_terms( 'sa_advocacy_targets', array(
				'hide_empty' => 0,
			 ) );

			// get county FIPS code
			$fips = preg_replace("/[^',]*US/i", '', $geoid);

			// get data for cover page
			$cover_page = sa_report_cover_page($fips);
			$report_area = $cover_page->county_name . ', ' . $cover_page->state_name;

			// in case of District of Columbia
			if ($cover_page->county_name === $cover_page->state_name) $report_area = $cover_page->county_name;

			// get data for obesity page
			$obesity_page = sa_report_obesity_page($fips);

			// get data for fast food page
			$fast_food_page = sa_report_food_access_page($fips);

			// get data for physical activity page
			$physical_activity_page = sa_report_physical_activity_page($fips);

			// get data for health equity page
			$health_equity_page = sa_report_health_equity_page($fips);

			// get data for vulnerable population page
			$vulnerable_population_page = sa_report_vulnerable_population_page($geoid);

			// add share and mailto links
			$share_title = 'Check out this Salud Report Card on Latino health for ' . $report_area;
			$share_url = 'http' . (isset($_SERVER['HTTPS']) ? 's' : '') . '://' . "{$_SERVER['HTTP_HOST']}{$_SERVER['REQUEST_URI']}";
			$mail_to = '?subject=Check Out Latino Health in ' . $report_area . '&body=' . $share_title . ': ' . $share_url;
		?>
		<style>
			/* only apply to screen, not PDF */
			@media screen and (max-width: 800px) {
				#sa-report-content #sa-vulnerable-map{
					margin-bottom: 20px;
				}
				#sa-report-content #sa-vulnerable-sidebar {
					   clear: left;
					   padding: 0;
					   margin: 0 auto;
				}
			}
			@media only screen {
				#sa-report-content .page-break {
				  margin-top: 40px;
				}
			}
		</style>
		 <div id="sa-report-content">
			 <div id="cover-page">
				<table class="sa-report-cols"><tr>
					<td class="col3"><img src="<?php echo $image_url?>logo-salud.png" width="70" /></td>
					<td class="col3 center-align"><?php echo date("F, Y") ?></td>
					<td class="col3 right-align"><img src="<?php echo $image_url?>logo-rwjf.png" width="120" /></td>
				</tr></table>
				<div class="sa-report-title sa-report-font-2">
					<p>your very own <b>Salud Report Card</b> for</p>
					<p class="sa-report-font-large"><?php echo $report_area ?></p>
				</div>
				<p class="center-align  sa-report-font-2">1 of 3 U.S. public school students will be <span class="sa-text-orange">Latino</span> by 2024. <sup>1</sup></p>
				<div class="center-align"><img src="<?php echo $image_url?>three-figures.png" width="120" /></div>
				<div class="sa-report-spacing1"></div>
				<div class="center-align sa-report-font-3"><img src="<?php echo $image_url?>check.png" width="50" />
					In <?php echo $cover_page->county_name ?> today,</div>
				<div class="center-align sa-text-orange sa-report-font-3"><?php echo $cover_page->frac_latino_adults ?> adults
					(<?php echo $cover_page->pct_latino_adults ?>) and</div>
				<div class="center-align sa-text-orange sa-report-font-3"><?php echo $cover_page->frac_latino_kids ?> kids
					(<?php echo $cover_page->pct_latino_kids ?>) are Latino<sup>2</sup></div>
				<div class="center-align sa-report-font-3">...and it ranks <?php echo $cover_page->health_ranking ?><sup><?php echo $cover_page->health_ranking_suffix ?></sup>
					of <?php echo $cover_page->health_ranking_total ?> counties</div>
				<div class="center-align sa-report-font-3">in health outcomes.<sup>3</sup></div>
				<div class="sa-report-spacing2"></div>
				<div class="center-align sa-text-green sa-report-font-3">This <i>Salud America!</i> Salud Report Card</div>
				<div class="center-align  sa-report-font-2">highlights health issues in your county (vs. state + nation) with</div>

				<div class="center-align  sa-report-font-2 sa-report-icons">
					<span><img src="<?php echo $image_url?>icon-data.png" />DATA</span>
					<span><img src="<?php echo $image_url?>icon-policy.png" />POLICY SOLUTIONS</span>
					<span><img src="<?php echo $image_url?>icon-research.png" />RESEARCH</span>
					<span><img src="<?php echo $image_url?>icon-stories.png" />STORIES</span>
				</div>
				<div class="center-align  sa-report-font-2">so you can start and support healthy changes in these areas:</div>
				<div class="center-align sa-report-changes">

				<?php
					foreach ( $big_bets as $term ) {
						?>
						<a href="<?php echo $big_bets_base_url . $term->slug; ?>" class="big-bet-icon-link" title="Link to Big Bet archive: <?php echo $term->name; ?>"><img src="<?php echo $plugin_base_url . 'public/images/big_bets/icons-with-titles/' . $term->slug . '-112x150.png' ?>" alt="Icon for Big Bet: <?php echo $term->name; ?>" class="big-bet-icon" /></a>
						<?php
					}
				?>
				</div>
				<div class="page-break"></div>
			</div>

			 <div id="obesity-page">
				<table class="sa-report-page-header sa-background-darkgray"><tr>
					<td class="sa-img">
					 <a href="<?php echo sa_get_section_permalink( 'big-bets' ); ?>sa-healthy-weight" title="Link to Big Bet archive: Healthy Weight">
					 <img src="<?php echo $plugin_base_url . 'public/images/big_bets/icons-with-titles/sa-healthy-weight-112x150.png' ?>" alt="Icon for Big Bet: Healthier Weight" class="big-bet-icon" /></a>
					</td>
					<td class="sa-page-title">
						<div class="sa-report-font-4">Obesity</div>
						<p>More U.S. Latino kids are obese by age 5 than whites, due to maternal obesity, less breatfeeding, and workplace/childcare issues.</p>
						<a href="<?php echo sa_get_section_permalink( 'big-bets' ); ?>sa-healthy-weight/" target="_blank">
						<img src="<?php echo $image_url?>learn-more.png" /></a>
					</td>
					   </tr>
				</table>

				<p>In <span class="sa-text-orange sa-text-capital">THE UNITED STATES OVERALL</span>, nearly 40% of U.S. Latino youths ages 2-19 are overweight or obese,
					compared with only 28.5% of non-Latino white youths.</p>
				<p>In <span class="sa-text-capital sa-text-purple"><?php echo $cover_page->state_name ?> OVERALL</span>,
					<?php echo $obesity_page->pct_latino_obese ?> of Latino children ages 10-17 are overweight or obese,
					compared with <?php echo $obesity_page->pct_white_obese ?> of non-Latino white children.<sup>4</sup></p>
				<p>In <span class="sa-text-capital sa-text-green"><?php echo $cover_page->county_name ?> OVERALL</span>,
				<?php echo $obesity_page->pct_obese ?>
				of all adults aged 20 and older self-report that they have a Body Mass Index (BMI; a measure of body fat based on height and weight) greater than 30.0 (obese).<sup>5</sup>
					<?php if ($obesity_page->pct_overweight != -1): ?>
					<?php echo $obesity_page->pct_overweight ?> of all adults aged 18 and older self-report that they have a Body Mass Index (BMI) between 25.0 and 30.0 (overweight).
					<?php else: ?>
					The overweight indicator measures percent of all adults aged 18 and older who self-report that they have a Body Mass Index (BMI) between 25.0 and 30.0 (overweight).
					Due to small number of survey respondents, county-level results are not available.
					<?php endif ?>
				<sup>5</sup>
			</p>

			<div class="sa-report-spacing1"></div>

				 <div class="w3-row">
					 <div class="w3-half">
					<?php echo $obesity_page->dial_obese ?>
					 </div>
					 <div class="w3-half">
					<?php echo $obesity_page->dial_overweight ?>
					 </div>
				 </div>

				<div class="sa-report-spacing2"></div>

				 <div class="w3-row">
					   <div class="w3-third sa-col-end sa-col-media center-align">
						<a href="https://youtu.be/3yFOgZ2ku6k" target="_blank" class="center-align">
							<img src="<?php echo $image_url?>icon-policy.png" height="52" /><br />
							<p class="sa-report-font-2">Policy<br />Solutions</p>
							<p><img src="<?php echo $image_url?>obesity-policy.jpg" /></p>
						</a>
						</div>
						<div class="w3-third sa-col-middle sa-col-media center-align">
						<a href="<?php echo sa_get_section_permalink( 'big-bets' ); ?>sa-healthy-weight/" target="_blank" class="center-align">
							<img src="<?php echo $image_url?>icon-research.png" height="52"  /><br />
							<p class="sa-report-font-2">Research <br />and Infographics</p>
							<p><img src="<?php echo $image_url?>obesity-research.jpg" /></p>
						</a>
						</div>
						<div class="w3-third sa-col-end sa-col-media center-align">
						<a  class="center-align" href="<?php echo sa_get_section_permalink( 'heroes' ); ?>baby-cafe-opens-to-bring-breastfeeding-peer-counselors-to-san-antonio-mothers/" target="_blank">
							<img src="<?php echo $image_url?>icon-stories.png" height="52"  /><br />
							<p class="sa-report-font-2">Salud Heroes: How to Start a Baby Cafe</p>
							<p><img src="<?php echo $image_url?>obesity-stories.jpg" /></p>
						</a>
						</div>
				</div>

				<div class="page-break"></div>
			</div>

			 <div id="food-access-page">
				<table class="sa-report-page-header sa-background-darkgray"><tr>
				<td class="sa-img">
				 <a href="<?php echo sa_get_section_permalink( 'big-bets' ); ?>sa-better-food-in-neighborhoods" title="Link to Big Bet archive: Better Food in Neighborhoods">
				 <img src="<?php echo $plugin_base_url . 'public/images/big_bets/icons-with-titles/sa-better-food-in-neighborhoods-112x150.png' ?>" alt="Icon for Big Bet: Better Food in the Neighborhood" class="big-bet-icon" /></a>
				 <a href="<?php echo sa_get_section_permalink( 'big-bets' ); ?>sa-sugary-drinks" title="Link to Big Bet archive: Sugary Drinks">
				 <img src="<?php echo $plugin_base_url . 'public/images/big_bets/icons-with-titles/sa-sugary-drinks-112x150.png' ?>" alt="Icon for Big Bet: Sugary Drinks" class="big-bet-icon" /></a>
				</td>
				<td class="sa-page-title">
					<div class="sa-report-font-4">Food Access</div>
					<p>U.S. Latino kids face unhealthy neighborhood food environments with fewer grocery stores and more fast food.</p>
					<a href="<?php echo sa_get_section_permalink( 'big-bets' ); ?>sa-better-food-in-neighborhoods/" target="_blank">
						<img src="<?php echo $image_url?>learn-more.png" /></a>
				</td>
				   </tr>
			</table>

			 <div class="w3-row">
				 <div class="w3-third sa-col-1">
					 <div class="sa-report-indicator-food">
						<p><span class="sa-report-indicator-title">Fast Food Restaurant Access</span> <sup>6</sup></p>
						<p>This indicator reports the number of fast food restaurants per 100,000 people in
							<span class="sa-text-green sa-text-capital"><?php echo $cover_page->county_name ?> OVERALL</span>.
							This indicator is relevant because it measures environmental influences on dietary behaviors.</p>
					 </div>
					  <?php echo $fast_food_page->dial_fast_food ?>
				 </div>

				 <div class="w3-third sa-col-2">
					 <div class="sa-report-indicator-food">
					<p><span class="sa-report-indicator-title">Grocery Store Access</span> <sup>6</sup></p>
					<p>This indicator reports the number of grocery stores per 100,000 people in
						<span class="sa-text-green sa-text-capital"><?php echo $cover_page->county_name ?> OVERALL</span>.
						This indicator is relevant because it provides a measure of healthy food access in a community.</p>
					 </div>
					<?php echo $fast_food_page->dial_grocery ?>
				 </div>

				 <div class="w3-third sa-col-3">
					 <div class="sa-report-indicator-food">
					<p><span class="sa-report-indicator-title">Fruit/Veggie Consumption</span> <sup>7</sup></p>
					<p>
						<?php if ($fast_food_page->total_fruit != -1): ?>
							In <span class="sa-text-green sa-text-capital"><?php echo $cover_page->county_name ?> OVERALL</span>,
							<?php echo $fast_food_page->total_fruit ?>, or <?php echo $fast_food_page->pct_fruit ?>
							of adults over the age of 18 are consuming less than 5 servings of fruits and vegetables each day.
						<?php else: ?>
							This indicator reports the percent of adults over the age of 18 who are consuming less than 5 servings of fruits and vegetables each day.
							Due to small number of survey respondents, results in
							<span class="sa-text-green sa-text-capital"><?php echo $cover_page->county_name ?> OVERALL</span> are not available.
						<?php endif; ?>
							This indicator is relevant because unhealthy eating may cause health issues.</p>
						</div>
					 <?php echo $fast_food_page->dial_fruit ?>
				 </div>
			 </div>

			 <div class="sa-report-spacing2"></div>

			 <div class="w3-row">
				 <div class="w3-third sa-col-end sa-col-media center-align">
						<a href="https://youtu.be/WLoZrkIAZT8" target="_blank">
						<img src="<?php echo $image_url?>icon-policy.png" height="52" /><br />
						<p class="sa-report-font-2">Policy<br />Solutions</p>
						<p><img src="<?php echo $image_url?>food-policy.jpg" /></p>
						</a>
				 </div>
				 <div class="w3-third sa-col-middle sa-col-media center-align">
						<a href="<?php echo sa_get_section_permalink( 'big-bets' ); ?>sa-better-food-in-neighborhoods/" target="_blank">
						<img src="<?php echo $image_url?>icon-research.png" height="52" /><br />
						<p class="sa-report-font-2">Research <br />and Infographics</p>
						<p><img src="<?php echo $image_url?>food-research.jpg" /></p>
						</a>
				 </div>
				 <div class="w3-third sa-col-end sa-col-media center-align">
						<a href="<?php echo sa_get_section_permalink( 'heroes' ); ?>dignowity-hill-farmers-market/" target="_blank">
						<img src="<?php echo $image_url?>icon-stories.png" height="52" /><br />
						<p class="sa-report-font-2">Salud Heroes: How to Start a Farmers Market</p>
						<p><img src="<?php echo $image_url?>food-stories.jpg" /></p>
						</a>
				 </div>
			 </div>

			   <div class="page-break"></div>
		 </div>

			 <div id="physical-activity-issues-page">
				<table class="sa-report-page-header sa-background-darkgray"><tr>
					<td class="sa-img">
					<a href="<?php echo sa_get_section_permalink( 'big-bets' ); ?>sa-active-spaces" title="Link to Big Bet archive: Active Spaces">
					<img src="<?php echo $plugin_base_url . 'public/images/big_bets/icons-with-titles/sa-active-spaces-112x150.png' ?>" alt="Icon for Big Bet: Active Spaces" class="big-bet-icon" /></a>
					</td>
					<td class="sa-page-title">
						<div class="sa-report-font-4">Physical Activity Issues</div>
						<p>Latino children and adults face less access to recreational facilities and lower activity rates. </p>
						<a href="<?php echo sa_get_section_permalink( 'big-bets' ); ?>sa-active-spaces/" target="_blank"><img src="<?php echo $image_url?>learn-more.png" /></a>
					</td>
					   </tr>
				</table>

				<div class="w3-row">
					<div class="w3-third sa-col-1">
						<div class="sa-report-indicator-physical">
							<p><span class="sa-report-indicator-title">Recreation and Fitness Facility Access</span> <sup>8</sup></p>
							<p>This indicator reports the number per 100,000 population of recreation facilities in
								<span class="sa-text-green sa-text-capital"><?php echo $cover_page->county_name ?> OVERALL</span>.
								This indicator is relevant because access to recreation facilities encourages physical activity and healthy behaviors.</p>
						</div>
						<?php echo $physical_activity_page->dial_recreation ?>
					</div>
					<div class="w3-third sa-col-1">
						<div class="sa-report-indicator-physical">
						<p><span class="sa-report-indicator-title">Physical Inactivity</span> <sup>6</sup></p>
						<p>In <span class="sa-text-green sa-text-capital"><?php echo $cover_page->county_name ?> OVERALL</span>,
							<?php echo $physical_activity_page->total_physical?> or <?php echo $physical_activity_page->pct_physical?>
						of adults aged 20+ self-report no leisure time for activity,
						based on the question: "During the past month, other than your regular job,
						did you participate in any physical activities or exercises such as running, calisthenics, golf, gardening, or walking for exercise?"</p>
						</div>
						<?php echo $physical_activity_page->dial_physical ?>
					</div>
					<div class="w3-third sa-col-1">
						<div class="sa-report-indicator-physical">
						<p><span class="sa-report-indicator-title">Pedestrian-Motor Vehicle Accidents</span> <sup>7</sup></p>
						<p><span class="sa-text-green sa-text-capital"><?php echo $cover_page->county_name ?> OVERALL</span> had 50 pedestrian deaths from 2011-13.
						This indicator shows the pedestrian deaths by motor vehicles per 100,000 people.
						These preventable deaths may be associated with a lack of safe routes (sidewalks, crosswalks, walkable spaces) and narrow traffic lanes/roads. </p>
						</div>
						<?php echo $physical_activity_page->dial_pedestrian ?>
					</div>
				</div>

				<div class="sa-report-spacing2"></div>

					  <div class="w3-row">
					 <div class="w3-third sa-col-end sa-col-media center-align">
							<a href="https://youtu.be/CJfaMmsCzNo" target="_blank">
							<img src="<?php echo $image_url?>icon-policy.png" height="52" /><br />
							<p class="sa-report-font-2">Policy<br />Solutions</p>
							<p><img src="<?php echo $image_url?>physical-policy.jpg" /></p>
							</a>
					</div>
					 <div class="w3-third sa-col-middle sa-col-media center-align">
							<a href="<?php echo sa_get_section_permalink( 'big-bets' ); ?>sa-active-spaces/" target="_blank">
							<img src="<?php echo $image_url?>icon-research.png" height="52" /><br />
							<p class="sa-report-font-2">Research <br />and Infographics</p>
							<p><img src="<?php echo $image_url?>physical-research.jpg" /></p>
							</a>
					</div>
					<div class="w3-third sa-col-end sa-col-media center-align">
							<a href="<?php echo sa_get_section_permalink( 'heroes' ); ?>move-el-paso-walking-trails-encourage-local-residents-to-get-up-get-walking/" target="_blank">
							<img src="<?php echo $image_url?>icon-stories.png" height="52" /><br />
							<p class="sa-report-font-2">Salud Heroes: How to Start Local Trails</p>
							<p><img src="<?php echo $image_url?>physical-stories.jpg" /></p>
							</a>
					</div>
					</div>

				   <div class="page-break"></div>
			 </div>


			 <div id="health-equity-page">
				<table class="sa-report-page-header sa-background-darkgray"><tr>
					<td class="sa-img">
					<a href="<?php echo sa_get_section_permalink( 'big-bets' ); ?>sa-health-equity" title="Link to Big Bet archive: Health Equity">
					<img src="<?php echo $plugin_base_url . 'public/images/big_bets/icons-with-titles/sa-health-equity-112x150.png' ?>" alt="Icon for Big Bet: Health Equity" class="big-bet-icon" /></a>
					</td>
					<td class="sa-page-title">
						<div class="sa-report-font-4">Health Equity</div>
						<p>Latino families face inequities in educational attainment, income, residential segregation, access to care, and more.</p>
						<a href="<?php echo sa_get_section_permalink( 'big-bets' ); ?>sa-health-equity/" target="_blank"><img src="<?php echo $image_url?>learn-more.png" /></a>
					</td>
					   </tr>
				</table>

				<div class="w3-row">
					 <div class="w3-third sa-col-1">
						 <div class="sa-report-indicator-health">
						<p><span class="sa-report-indicator-title">Children in Poverty</span> <sup>2</sup></p>
						<p>In <span class="sa-text-green sa-text-capital"><?php echo $cover_page->county_name ?> OVERALL</span>,
							<?php echo $health_equity_page->pct_poverty ?> or <?php echo $health_equity_page->total_poverty ?>
							 children 0-17 live in households with income below the Federal Poverty Level (FPL).
							Poverty creates barriers to access of health services, healthy food, and other necessities that contribute to poor health status.</p>
						 </div>
						 <?php echo $health_equity_page->dial_poverty ?>
					</div>
					 <div class="w3-third sa-col-1">
						 <div class="sa-report-indicator-health">
						<p><span class="sa-report-indicator-title">Access to Primary Care</span> <sup>11</sup></p>
						<p>This indicator reports the number of primary care physicians per 100,000 population in
							<span class="sa-text-green sa-text-capital"><?php echo $cover_page->county_name ?> OVERALL</span>.
							Having a usual primary care provider is linked to a higher likelihood of appropriate care (and thus better health outcomes). 	</p>
						 </div>
						 <?php echo $health_equity_page->dial_primary_care ?>
					</div>
					 <div class="w3-third sa-col-1">
						 <div class="sa-report-indicator-health">
						<p><span class="sa-report-indicator-title">% with No Health Insurance</span> <sup>2</sup></p>
						<p>This indicator reports the percentage of the total civilian non-institutionalized population
							without health insurance coverage in <span class="sa-text-green sa-text-capital"><?php echo $cover_page->county_name ?> OVERALL</span>.
							Lack of health insurance is considered a key driver of health status.</p>
						 </div>
						 <?php echo $health_equity_page->dial_insurance ?>
					</div>
				</div>

				<div class="sa-report-spacing2"></div>
				<div class="center-align sa-report-font-2"><u>Socio-economic Barriers for
					<span class="sa-text-capital sa-text-green"><?php echo $cover_page->county_name ?></span>
					<span class="sa-text-orange">Latinos</span></u> <sup>2</sup>
				</div>
				<table class="sa-report-sociobarriers">
					<tr>
						<td></td>
						<td>% among <span class="sa-text-orange">Latino</span> Population</td>
						<td>% among Non-Latino Population</td>
					</tr>
					<tr class="sa-background-gray"><td colspan="3"><b>No High School Diploma</b></td></tr>
					<tr>
						<td class="sa-report-sociobarriers-area"><?php echo $cover_page->county_name ?></td>
						<td class="sa-text-orange sa-text-bold"><?php echo $health_equity_page->pct_latino_hs1 ?></td>
						<td class="sa-text-bold"><?php echo $health_equity_page->pct_nonlatino_hs1 ?></td>
					</tr>
					<tr class="sa-background-gray">
						<td class="sa-report-sociobarriers-area"><?php echo $cover_page->state_name ?></td>
						<td class="sa-text-orange sa-text-bold"><?php echo $health_equity_page->pct_latino_hs2 ?></td>
						<td class="sa-text-bold"><?php echo $health_equity_page->pct_nonlatino_hs2 ?></td>
					</tr>
					<tr>
						<td class="sa-report-sociobarriers-area">United States</td>
						<td class="sa-text-orange sa-text-bold"><?php echo $health_equity_page->pct_latino_hs3 ?></td>
						<td class="sa-text-bold"><?php echo $health_equity_page->pct_nonlatino_hs3 ?></td>
					</tr>
					<tr class="sa-background-gray"><td colspan="3"><b>Children in Poverty</b></td></tr>
					<tr>
						<td class="sa-report-sociobarriers-area"><?php echo $cover_page->county_name ?></td>
						<td class="sa-text-orange sa-text-bold"><?php echo $health_equity_page->pct_latino_pov1 ?></td>
						<td class="sa-text-bold"><?php echo $health_equity_page->pct_nonlatino_pov1 ?></td>
					</tr>
					<tr class="sa-background-gray">
						<td class="sa-report-sociobarriers-area"><?php echo $cover_page->state_name ?></td>
						<td class="sa-text-orange sa-text-bold"><?php echo $health_equity_page->pct_latino_pov2 ?></td>
						<td class="sa-text-bold"><?php echo $health_equity_page->pct_nonlatino_pov2 ?></td>
					</tr>
					<tr>
						<td class="sa-report-sociobarriers-area">United States</td>
						<td class="sa-text-orange sa-text-bold"><?php echo $health_equity_page->pct_latino_pov3 ?></td>
						<td class="sa-text-bold"><?php echo $health_equity_page->pct_nonlatino_pov3 ?></td>
					</tr>
					<tr class="sa-background-gray"><td colspan="3"><b>Uninsured</b></td></tr>
					<tr>
						<td class="sa-report-sociobarriers-area"><?php echo $cover_page->county_name ?></td>
						<td class="sa-text-orange sa-text-bold"><?php echo $health_equity_page->pct_latino_ins1 ?></td>
						<td class="sa-text-bold"><?php echo $health_equity_page->pct_nonlatino_ins1 ?></td>
					</tr>
					<tr class="sa-background-gray">
						<td class="sa-report-sociobarriers-area"><?php echo $cover_page->state_name ?></td>
						<td class="sa-text-orange sa-text-bold"><?php echo $health_equity_page->pct_latino_ins2 ?></td>
						<td class="sa-text-bold"><?php echo $health_equity_page->pct_nonlatino_ins2 ?></td>
					</tr>
					<tr>
						<td class="sa-report-sociobarriers-area">United States</td>
						<td class="sa-text-orange sa-text-bold"><?php echo $health_equity_page->pct_latino_ins3 ?></td>
						<td class="sa-text-bold"><?php echo $health_equity_page->pct_nonlatino_ins3 ?></td>
					</tr>
				</table>
				<div class="page-break"></div>
			 </div>
			 <?php if ($vulnerable_population_page->pct_white != -1) :?>
			 <div id="vulnerable-population-page">
				<div class="sa-report-page-header">
				<div class="float-left">
				<img src="<?php echo $image_url?>vulnerable-population.jpg" width="210" />
				</div>
				<div class="sa-vulnerable-header sa-page-title">
						<div class="sa-report-font-4">Vulnerable Populations</div>
						<p>"Vulnerable populations" are those with over 20% of the population living below the poverty level
							AND over 25% of the population with less than high school education-two indicators that are primary social determinants of population health.
						</p>
				</div>
				</div>
				<p>In <span class="sa-text-green sa-text-capital"><?php echo $cover_page->county_name ?> OVERALL</span>,
					Latinos comprise <?php echo $vulnerable_population_page->pct_latino ?>
					of the entire vulnerable population (who are in both poverty and have less than a high school educational attainment),
					compared to non-Latino whites who comprise <?php echo $vulnerable_population_page->pct_white ?> of the entire vulnerable population.<sup>2</sup>
					The map below shows the vulnerable population in red.
				</p>
				<div class="w3-row">
					<div id="sa-vulnerable-map" class="w3-threequarter"><?php echo $vulnerable_population_page->map ?></div>
					<div id="sa-vulnerable-sidebar" class="w3-rest">
							<div id="sa-vulnerable-maplegend">
								<p><b>Vulnerable Populations Footprint, ACS 2009-13</b></p>
								<div class="sa-vulnerable-map-legend"><span style="background: #A32A00; border: 1px solid #7F0400"></span>Above all thresholds (Footprint)</div>
								<div class="sa-vulnerable-map-legend"><span style="background: #F09432; border: 1px solid #D17615"></span>Population living below poverty: >= 20%</div>
								<div class="sa-vulnerable-map-legend"><span style="background: #9D6EBA; border: 1px solid #80509C"></span>Populaton with below high school attainment: >= 25%</div>
							</div>
							<div id="sa-vulnerable-contact">
								<p style="color: #f00">Like maps?</p>
						<p>Want to know where the parks are in your county? The farmer's markets? The hospitals? </p>
						<p><a href="mailto:saludamerica@uthscsa.edu?Subject=Creating Maps">Email us</a> and we can help you create a map of almost anything in your area, for you to show to decision-makers!</p>
							</div>
					</div>
				</div>

				<div class="page-break"></div>
			</div>
			 <?php endif ?>

			 <div id="reference-page">
				<div class="sa-report-page-header"></div>
				<div class="center-align sa-report-font-3"><p>You know the issues. Now what?</p></div>
				<div class="center-align sa-report-font-large sa-text-orange">
					<div><img src="<?php echo $image_url?>icon-share.png" width="35" /> Share This Report!</div>
				</div>
				<div class="sa-report-font-2">
					<a href="mailto:<?php echo $mail_to ?>">Email this report to colleagues</a>;
					share it to start discussions on <a href="http://www.facebook.com/sharer.php?t=<?php echo $share_title ?>&u=<?php echo $share_url ?>" target="_blank">Facebook</a> and
					<a href="http://twitter.com/share?text=<?php echo $share_title ?>&url=<?php echo $share_url ?>" target="_blank">Twitter</a>, or at the PTA, etc.;
					and bring it to city/school leaders to spur change.</div>
				 <div class="sa-report-spacing1"></div>
				<div class="center-align sa-report-font-large sa-text-orange"><p>Start (or Support) a Change!</p></div>
					  <div class="sa-list">
						<p  class="sa-report-font-2">1. <a href="<?php echo $group_url; ?>share-your-story/" target="_blank">Start a change and share it with us</a>.
					We might write it up, film it, promote it nationally, and move you from Leader to Hero!</p>
						<p class="sa-report-font-2">2. <a href="http://maps.communitycommons.org/policymap/?bbox=<?php echo $vulnerable_population_page->bbox ?>" target="_blank">
					Use our map</a> to connect with Salud Leaders who you can ask to support your change, or find changes you can support!</p>
						<p class="sa-report-font-2">3. Gather support from others by
							<a href="<?php echo $group_url ?>forum/" target="_blank">starting your own forum for discussion on the SA! Hub</a>.</p>
					</div>
				<p></p>
				<div class="center-align sa-report-font-3"><p>Need more data first?</p></div>
				<p>Email our <i>Salud America!</i> digital curators,
					<a href="http://www.communitycommons.org/members/amfitness/profile/" target="_blank">Amanda</a>,
					<a href="http://www.communitycommons.org/members/lveraza/profile/" target="_blank">Lisa</a> and
					<a href="http://www.communitycommons.org/members/ericmoreno77/profile/" target="_blank">Eric</a>,
					who can answer questions and help you access information, data and maps on many other topics.
				</p>
				<div id="sa-reference">
					<p><b>References</b></p>
					<p>Note: Data in this Salud Report Card was selected by <i>Salud America!</i> curators from several data and mapping tools
						available on the Community Commons website, including the
						<a href="http://www.communitycommons.org/chna/" target="_blank">Community Health Needs Assessment</a>
						and the <a href="http://assessment.communitycommons.org/Footprint/" target="_blank">Vulnerable Populations Footprint</a> tools.
						Contact our curator team at <a href="mailto:saludamerica@uthscsa.edu">saludamerica@uthscsa.edu</a> for a full report or more tailored data.
					</p>

					<div class="sa-list">
						<p>1. National Center for Education Statistics. <a href="http://nces.ed.gov/programs/coe/indicator_cge.asp" target="_blank">http://nces.ed.gov/programs/coe/indicator_cge.asp</a>. </p>
						<p>2. U.S. Census Bureau. American Community Survey (<a href="http://www.census.gov/programs-surveys/acs/" target="_blank">ACS</a>). 2010-2014. U.S. Department of Commerce, Washington, DC. Available.</p>
						<p>3. County Health Rankings (2016). <a href="http://www.countyhealthrankings.org/" target="_blank">http://www.countyhealthrankings.org/</a>.</p>
						<p>4. National Survey of Children's Health 2011-12. Data Resource Center for Child and Adolescent Health.</p>
						<p>5. Overweight and Obesity: Centers for Disease Control and Prevention (CDC), Behavioral Risk Factor Surveillance System (BRFSS). 2011-12. Atlanta, GA..<sup>1</sup></p>
						<p>6. Fast food restaurant access and grocery store access: U.S. Census Bureau. (2012). County Business Patterns. 2013. U.S. Department of Commerce.<sup>1</sup></p>
						<p>7. CDC, Behavioral Risk Factor Surveillance System (<a href="http://www.cdc.gov/brfss/" target="_blank">BRFSS</a>). 2005-09. Atlanta, GA. Available.<sup>1</sup></p>
						<p>8. U.S. Census Bureau. County Business Patterns. (<a href="http://www.census.gov/programs-surveys/cbp.html" target="_blank">CBP</a>). 2013. U.S. Department of Commerce, Washington, DC. Available.<sup>1</sup></p>
						<p>9. CDC, National Center for Chronic Disease Prevention and Health Promotion (<a href="http://www.cdc.gov/nccdphp/dnpao/index.html" target="_blank">NCCDPHP</a>). 2012. Atlanta, GA. Available.<sup>1</sup></p>
						<p>10. U.S. Department of Transportation, National Highway Traffic Safety Administration, <a href="http://www.nhtsa.gov/FARS" target="_blank">Fatality Analysis Reporting System</a>. 2011-13 (county data).</p>
						<p>11. U.S. Department of Health and Human Services, HRSA, Area Health Resource File (<a href="http://ahrf.hrsa.gov/" target="_blank">AHRF</a>). 2012. Washington, DC. Available.<sup>1</sup></p>
					</div>
					<div id="footer"></div>
					<sup>1</sup> Additional estimation and analysis done by <a href="http://cares.missouri.edu" target="_blank">CARES</a>.
				</div>
			 </div>
		 </div>

		<div id="sa-report-action">
			 <input type="button" class="button sa-report-export" id="sa-report-export" value="Export Report to PDF" />
			 <input type="button" class="button sa-report-save" id="sa-report-save" value="Save Report to My Library" />
			 <div id="report-save-message">Saving your report card, please wait...</div>
			 <input type="hidden" id="report-card-geoid" value="<?php echo $geoid ?>" />
			 <input type="hidden" id="report-card-county" value="<?php echo $cover_page->county_name ?>" />
			 <input type="hidden" id="report-card-state" value="<?php echo $cover_page->state_name ?>" />
			 <input type="hidden" id="report-card-wpnonce" value="<?php echo wp_create_nonce( 'save-leader-report-' . bp_loggedin_user_id() ) ?>" />
		</div>
		<?php
		endif;
		?>

	</div><!-- end .content-row -->
	<?php
}


function console_log( $data ){
	echo '<script>';
	echo 'console.log(1, '. json_encode( $data ) .')';
	echo '</script>';
}

// get json object from API service
function sa_report_get_json($fips, $id='', $param = '', $api_name ='indicator'){
	$api_url = 'http://services.communitycommons.org/api-report/v1/' . $api_name . '/Salud/';
	$api_url .= $id . '?area_type=county&area_ids=' . $fips . $param;
	$result = file_get_contents($api_url);
	return json_decode($result);
}

// get indicator dial
function sa_report_get_dial($json){
	return "<div class='center-align'><p><b>". $json->data->gauge_label . "</b></p><div class='sa-report-dial'>" . $json->gauge . "</div></div>";
}

// get indicator value as double
function sa_report_get_double($value){
	if ($value == "no data" || $value == "suppressed"){
		return 0;
	}else{
		$value = preg_replace("/[^.0-9]/", '', $value);
		return floatval($value);
	}
	return 0;
}

// write the percentage in single digit format
function sa_report_get_single_digit_pct($value){
	if ($value == 0) {
		return "0%";
	}else{
		return number_format($value, 1) . "%";
	}
}

// get fraction expression for latino adults & kids, called from sa_report_cover_page()
function sa_report_set_fraction_value($value){
	if ($value > 0){
		$fraction = round(100 / $value);

		// in case we have "1 in 1", we change to common fractors
		if ($fraction == 1){
			$fraction = round($value / 10);
			return "$fraction in 10";
		}else{
			return "1 in $fraction";
		}
	}else{
		return "0 in all";
	}
}

// get all data for the cover page
function sa_report_cover_page($fips){
	$data = (object) array(
				'frac_latino_adult' =>'1 in all',
				'pct_latino_adults' =>0,
				'frac_latino_kids' =>'1 in all',
				'pct_latino_kids' =>0,
			);

	try{
		// Latino adults - percentage and fraction
		$json_value = sa_report_get_json($fips, '7080', '&breakout_id=8269');        // age 18+
		$data->county_name = substr($json_value->area_name, 0, strrpos($json_value->area_name, ','));
		$data->state_name = $json_value->data->state_list[0]->values[0];

		$value_double = sa_report_get_double($json_value->data->summary->values[3]);
		$data->pct_latino_adults = sa_report_get_single_digit_pct($value_double);
		$data->frac_latino_adults =    sa_report_set_fraction_value($value_double);

		// Latino kids - percentage and fraction
		$json_value = sa_report_get_json($fips, '706', '&breakout_id=8590');
		$value_double = sa_report_get_double($json_value->data->summary->values[3]);
		$data->pct_latino_kids = sa_report_get_single_digit_pct($value_double);
		$data->frac_latino_kids =    sa_report_set_fraction_value($value_double);

		// health outcoome ranking
		$json_value = sa_report_get_json($fips, '8040');
		$data->health_ranking_total = $json_value->data->summary->values[1];
		$data->health_ranking = $json_value->data->summary->values[4];
		switch(substr($data->health_ranking, -1)){
			case '1':
				$data->health_ranking_suffix = 'st';
				break;
			case '2':
				$data->health_ranking_suffix = 'nd';
				break;
			case '3':
				$data->health_ranking_suffix = 'rd';
				break;
			default:
				$data->health_ranking_suffix = 'th';
				break;
		}
	}
	catch (Exception $e){
		console_log($e->getMessage());
	}

	return $data;
}

// get all data for the obesity page
function sa_report_obesity_page($fips){
	$data = (object) array(
		'pct_overweight'=>-1
		);
	try{
		// state overall percentage overweight or obese
		$json_value = sa_report_get_json($fips, '8018', '&breakout_id=3');
		$value_double = sa_report_get_double($json_value->data->state_list[0]->values[1]);
		$data->pct_latino_obese = sa_report_get_single_digit_pct($value_double);
		$value_double = sa_report_get_double($json_value->data->state_list[0]->values[2]);
		$data->pct_white_obese = sa_report_get_single_digit_pct($value_double);

		// obese percentage and dial - should always have data for counties except PR
		$json_value = sa_report_get_json($fips, '603', "&output_gauge=true");
		$data->dial_obese = sa_report_get_dial($json_value);
		$value_double = sa_report_get_double($json_value->data->summary->values[3]);
		$data->pct_obese = sa_report_get_single_digit_pct($value_double);

		// overweight percentage and dial - may not have data for the county
		$json_value = sa_report_get_json($fips, '604', "&output_gauge=true");
		$data->dial_overweight = sa_report_get_dial($json_value);
		$value_double = sa_report_get_double($json_value->data->summary->values[3]);
		if ($value_double !== 0){
			$data->pct_overweight = sa_report_get_single_digit_pct($value_double);
		}
	}catch(Exception $e){
		console_log($e->getMessage());
	}

	return $data;
}

// get data for food access page
function sa_report_food_access_page($fips){
	$data = (object) array(
		'total_fruit'=>-1
		);
	try{
		// fast food dial
		$json_value = sa_report_get_json($fips, '401', "&output_gauge=true");
		$data->dial_fast_food = sa_report_get_dial($json_value);

		// grovery store
		$json_value = sa_report_get_json($fips, '402', "&output_gauge=true");
		$data->dial_grocery = sa_report_get_dial($json_value);

		// fruit/veggis - may not have value
		$json_value = sa_report_get_json($fips, '301', "&output_gauge=true");
		$data->dial_fruit = sa_report_get_dial($json_value);

		$value_double = sa_report_get_double($json_value->data->summary->values[3]);
		if ($value_double !== 0){
			$data->total_fruit = $json_value->data->summary->values[2];
			$data->pct_fruit = sa_report_get_single_digit_pct($value_double);
		}
	}catch(Exception $e){
		console_log($e->getMessage());
	}
	return $data;
}

function sa_report_physical_activity_page($fips){
	$data = (object) array();
	try{
		 // recreation
		$json_value = sa_report_get_json($fips, '408', "&output_gauge=true");
		$data->dial_recreation = sa_report_get_dial($json_value);

		// physical inactivity
		$json_value = sa_report_get_json($fips, '306', "&output_gauge=true");
		$data->dial_physical = sa_report_get_dial($json_value);
		$data->total_physical = $json_value->data->summary->values[2];
		$value_double = sa_report_get_double($json_value->data->summary->values[3]);
		$data->pct_physical = sa_report_get_single_digit_pct($value_double);

		// pedestrian
		$json_value = sa_report_get_json($fips, '627', "&output_gauge=true");
		$data->dial_pedestrian = sa_report_get_dial($json_value);
	}catch(Exception $e){
		console_log($e->getMessage());
	}
	return $data;
}

function sa_report_health_equity_page($fips){
	$data = (object) array();
	try{
		// children in poverty
		$json_value = sa_report_get_json($fips, '781', "&output_gauge=true");
		$data->dial_poverty = sa_report_get_dial($json_value);
		$data->total_poverty = $json_value->data->summary->values[3];
		$value_double = sa_report_get_double($json_value->data->summary->values[4]);
		$data->pct_poverty = sa_report_get_single_digit_pct($value_double);

		// access to primary care
		$json_value = sa_report_get_json($fips, '505', "&output_gauge=true");
		$data->dial_primary_care = sa_report_get_dial($json_value);

		// no insurance
		$json_value = sa_report_get_json($fips, '202', "&output_gauge=true");
		$data->dial_insurance = sa_report_get_dial($json_value);

		// socio-economic - high school graduation
		$json_value = sa_report_get_json($fips, '760', "&breakout_id=465");
		$data->pct_latino_hs1 = $json_value->data->summary->values[3];
		$data->pct_nonlatino_hs1 = $json_value->data->summary->values[4];
		$data->pct_latino_hs2 = $json_value->data->state_list[0]->values[3];
		$data->pct_nonlatino_hs2 = $json_value->data->state_list[0]->values[4];
		$data->pct_latino_hs3 = $json_value->data->usa_summary->values[3];
		$data->pct_nonlatino_hs3 = $json_value->data->usa_summary->values[4];

		// socio-economic - poverty
		$json_value = sa_report_get_json($fips, '781', "&breakout_id=925");
		$data->pct_latino_pov1 = $json_value->data->summary->values[3];
		$data->pct_nonlatino_pov1 = $json_value->data->summary->values[4];
		$data->pct_latino_pov2 = $json_value->data->state_list[0]->values[3];
		$data->pct_nonlatino_pov2 = $json_value->data->state_list[0]->values[4];
		$data->pct_latino_pov3 = $json_value->data->usa_summary->values[3];
		$data->pct_nonlatino_pov3 = $json_value->data->usa_summary->values[4];

		// socio-economic - insurance
		$json_value = sa_report_get_json($fips, '202', "&breakout_id=917");
		$data->pct_latino_ins1 = $json_value->data->summary->values[3];
		$data->pct_nonlatino_ins1 = $json_value->data->summary->values[4];
		$data->pct_latino_ins2 = $json_value->data->state_list[0]->values[3];
		$data->pct_nonlatino_ins2 = $json_value->data->state_list[0]->values[4];
		$data->pct_latino_ins3 = $json_value->data->usa_summary->values[3];
		$data->pct_nonlatino_ins3 = $json_value->data->usa_summary->values[4];
	}
	catch(Exception $e){
		console_log($e->getMessage());
	}
	return $data;
}

function sa_report_vulnerable_population_page($geoid){
	$data = (object) array(
		'pct_white'=>-1
		);
	try{
		// get data and map for vulnerable population
		$json_value = sa_report_get_json('', $geoid, '&map_width=600&map_height=620', 'vpf');

		if ($json_value->values != null){
			$data->pct_white = sa_report_get_single_digit_pct($json_value->values[0]->value);
			$data->pct_latino = sa_report_get_single_digit_pct($json_value->values[1]->value);
			$data->map = htmlspecialchars_decode($json_value->map);
		}
		$data->bbox = join(',', $json_value->bbox);
	}
	catch(Exception $e){
		console_log($e->getMessage());
	}
	return $data;
}