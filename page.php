<?php
/**
 * The main template file
 *
 * This is the most generic template file in a WordPress theme
 * and one of the two required files for a theme (the other being style.css).
 * It is used to display a page when nothing more specific matches a query.
 * E.g., it puts together the home page when no home.php file exists.
 *
 * @link https://codex.wordpress.org/Template_Hierarchy
 *
 * @package WordPress
 * @subpackage MVP
 */
global $modules;

get_header();

if ( have_rows('sections') ) {
	while ( have_rows('sections') ) { the_row();
		if ($modules instanceof AcfModulesHandler) {
			$currentModule = $modules->getModule( get_row_layout() );
			$currentModulePath = $currentModule->getFilePath();

			get_template_part('modules/' . $currentModulePath);
		}
	}
}

get_footer();

