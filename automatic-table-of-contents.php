<?php
/**
 * Plugin Name:       Automatic Table of Contents
 * Plugin URI:        https://plugins.club/wordpress/automatic-table-of-contents/
 * Description:       Adds a shortcode and a widget for displaying a nested table of contents from all heading tags found on the page.
 * Version:           1.1
 * Author:            plugins.club
 * Author URI:        https://plugins.club
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Requires at least: 5.0
 * Tested up to: 	  6.1.1
*/

// WIDGET
require_once(dirname(__FILE__) . '/toc-widget.php');


// SHORTCODE
function toc_shortcode_func($atts) {
    // Extract the attributes, accept exclude attribute
    $atts = shortcode_atts(array('exclude' => ''), $atts);
	$excluded = array_map('trim', explode(',', $atts['exclude']));
 
    // Get the content of the post/page
    global $post;
    $content = $post->post_content;
 
    // Find all headings in the content
    preg_match_all('/<h([1-6])[^>]*>(.*?)<\/h[1-6]>/i', $content, $matches);
    $headings = $matches[2];
    $levels = $matches[1];
 
    // Build the nested table of contents
    $output = '<div class="toc">';
    $output .= '<ul>';
    $current_level = 0;
    for($i=0; $i<count($headings); $i++) {
		if (in_array($headings[$i], $excluded)) {
        continue;
		}
        if ($levels[$i] > $current_level) {
            for ($j = $current_level; $j < $levels[$i]; $j++) {
                $output .= '<ul>';
            }
        } else if ($levels[$i] < $current_level) {
            for ($j = $levels[$i]; $j < $current_level; $j++) {
                $output .= '</ul>';
            }
        }
        $current_level = $levels[$i];
        $output .= '<li>';
        $output .= '<a href="#heading-' . $i . '">' . $headings[$i] . '</a>';
        $output .= '</li>';
    }
    for ($j = 1; $j <= $current_level; $j++) {
        $output .= '</ul>';
    }
    $output .= '</div>';
 
    return $output;
}
add_shortcode('toc', 'toc_shortcode_func');

// TAG HEADINGS
function add_anchors_to_headings($content) {
        preg_match_all('/<h([1-6])[^>]*>(.*?)<\/h[1-6]>/i', $content, $matches);
    $headings = $matches[2];
    $levels = $matches[1];

    for($i=0; $i<count($headings); $i++) {
        $content = preg_replace('/<h' . $levels[$i] . '[^>]*>' . $headings[$i] . '<\/h' . $levels[$i] . '>/i', '<h' . $levels[$i] . ' id="heading-' . $i . '">' . $headings[$i] . '</h' . $levels[$i] . '>', $content, 1);
    }
    return $content;
}
add_filter( 'the_content', 'add_anchors_to_headings' );
