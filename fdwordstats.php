<?php
/*:noTabs=false:*/
/*
Plugin Name: Word Statistics
Plugin URI: http://flagrantdisregard.com/wordstats/
Description: Computes Gunning-Fog, Flesch, and Flesch-Kincaid readability indexes about posts as they are edited for the purpose of improving their readability.
Author: John Watson
Author URI: http://flagrantdisregard.com/
Version: 1.0
*/ 

/************************************************************
WordStatsPlugin.php - A plugin for calculating readability
statistics for WordPress based on the WordStats readability
class.

Copyright (C) Thu Aug 26 2004 John Watson
john@flagrantdisregard.com
http://flagrantdisregard.com/

$Id: wordstatsplugin.php 339 2005-12-30 18:35:04Z John $

This program is free software; you can redistribute it and/or
modify it under the terms of the GNU General Public License
as published by the Free Software Foundation; either version 2
of the License, or any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.
'
You should have received a copy of the GNU General Public License
along with this program; if not, write to the Free Software
Foundation, Inc., 59 Temple Place - Suite 330, Boston, MA  02111-1307, USA.
************************************************************/

include("wordstats/wordstats.php");

/*
==================================================
Admin functions
==================================================
*/

// Draw the readability statistics for the post
// being edited and the help rollovers.
function wordstats_draw_admin_footer() {
	global $wpdb;
	global $post;
	
	if ($post->post_content != '') {
		$stat = new WordStats;
		$stat->set_text($post->post_content);
		$template = '';
		$template = '<div id="wordstats">'
			.'<strong>Words:</strong> %d &nbsp; '
			.'<strong>Sentences:</strong> %d &nbsp; '
			.'<span onmouseover="wordstatsrollover(\'fog\')" onmouseout="wordstatsrolloff(\'fog\')"><strong>Fog:</strong> %2.1f</span> &nbsp; '
			.'<span id="wordstatsfogrollover" class="wordstatsrollover">The Gunning-Fog index gives the number of years of education needed to understand the text.  Short, plain sentences score better than long, complicated sentences.  Based on words per sentence and "hard" words per sentence.</span>'
			.'<span onmouseover="wordstatsrollover(\'kincaid\')" onmouseout="wordstatsrolloff(\'kincaid\')"><strong>Kincaid:</strong> %2.1f</span> &nbsp; '
			.'<span id="wordstatskincaidrollover" class="wordstatsrollover">The Flesch-Kincaid index gives the number of years of education needed to understand the text.  Short, plain sentences score better than long, complicated sentences.  Based on syllables per word and words per sentence.</span>'
			.'<span onmouseover="wordstatsrollover(\'flesch\')" onmouseout="wordstatsrolloff(\'flesch\')"><strong>Flesch:</strong> %3.0f</span> &nbsp; '
			.'<span id="wordstatsfleschrollover" class="wordstatsrollover">The Flesch index, usually between 0 and 100, indicates how difficult the text is to read.  The higher the score, the easier the text is to read.  Based on syllables per word and words per sentence.</span>'
			.'</div>';
		$pluginHTML = sprintf($template,
				$stat->get_words(),
				$stat->get_sentences(),
				$stat->get_fog(),
				$stat->get_flesch_kincaid(),
				$stat->get_flesch()
			);
		printf('<script language="javascript" type="text/javascript">
				function wordstatsrollover(v) {
					var div = document.getElementById("wordstats"+v+"rollover");
					if (div != undefined) {
						div.style.display = "inline";
					}
				}
				
				function wordstatsrolloff(v) {
					var div = document.getElementById("wordstats"+v+"rollover");
					if (div != undefined) {
						div.style.display = "none";
					}
				}
				
				var div = document.getElementById("titlediv");
				if (div != undefined) {
					div.innerHTML = \'%s\' + div.innerHTML;
				}
				</script>', str_replace("'", "\'", $pluginHTML)
			);
	}
}

// Inject some CSS into the header for displaying
// the readability stats of the post being edited.
function wordstats_draw_admin_header() {
	echo '
	<style type="text/css">
	#wordstats {
		text-align:left;
		padding:2px;
		color: #333;
		font-size: 10pt;
		line-height: 12pt;
		z-index: 100;
	}
	
	.wordstatsrollover {
		position:absolute;
		width:200px;
		margin-left:-8em;
		margin-top:1.5em;
		text-align:left;
		padding:4px;
		border:2px solid #448abd;
		background-color:#ddeaf4;
		color:#000;
		display:none;
	}
	</style>
	';
}

/*
==================================================
Template functions
==================================================
You can use these just like other template tags in your
blog.  They all take at least one argument of the content
being edited.  Like this:

	Fog index: <?php wordstats_fog($pages[$page-1]); ?>
*/

// Get the word count
function wordstats_words($content) {
	$count = 0;
	
	if ($content != '') {
		$stat = new WordStats;
		$stat->set_text($content);
		$count = $stat->get_words();
	}
	
	return($count);
}

// Get the sentence count
function wordstats_sentences($content) {
	$count = 0;
	
	if ($content != '') {
		$stat = new WordStats;
		$stat->set_text($content);
		$count = $stat->get_sentences();
	}
	
	return($count);
}

// Get the Gunning-Fog index value
function wordstats_fog($content) {
	$index = 0;
	
	if ($content != '') {
		$stat = new WordStats;
		$stat->set_text($content);
		$index = $stat->get_fog();
	}
	
	return($index);
}

// Get the Flesch index value
function wordstats_flesch($content) {
	$index = 0;
	
	if ($content != '') {
		$stat = new WordStats;
		$stat->set_text($content);
		$index = $stat->get_flesch();
	}
	
	return($index);
}

// Get the Flesch-Kincaid index value
function wordstats_flesch_kincaid($content) {
	$index = 0;
	
	if ($content != '') {
		$stat = new WordStats;
		$stat->set_text($content);
		$index = $stat->get_flesch_kincaid();
	}
	
	return($index);
}

/*
==================================================
Add action hooks
==================================================
*/
add_action('admin_head', 'wordstats_draw_admin_header');
add_action('admin_footer', 'wordstats_draw_admin_footer');
?>
