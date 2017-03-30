<?php

/*
 * This theme styles the visual editor to resemble the theme style,
 * specifically font, colors, and column width.
 */
add_editor_style( array( 'assets/dist/editor-styles.css' ) );


// ---------------------------------------------------------------------------

function customEditorInit($init) {
    $init['body_class'] .= ' Editor';

    // Define the style_formats array
    $styleFormats = array(
        array(
            'title' => 'Headings',
            'items' => array(
				array(
                    'title' => 'H1',
                    'block' => 'h1',
                    'attributes' => array(
                        'class' => 'h1'
                    )
                ),
                array(
                    'title' => 'H2',
                    'block' => 'h2',
                    'attributes' => array(
                        'class' => 'h2'
                    )
                ),
                array(
                    'title' => 'H3',
                    'block' => 'h3',
                    'attributes' => array(
                        'class' => 'h3'
                    )
                ),
                array(
                    'title' => 'H4',
                    'block' => 'h4',
                    'attributes' => array(
                        'class' => 'h4'
                    )
                ),
                array(
                    'title' => 'H5',
                    'block' => 'h5',
                    'attributes' => array(
                        'class' => 'h5'
                    )
                ),
                array(
                    'title' => 'H6',
                    'block' => 'h6',
                    'attributes' => array(
                        'class' => 'h6'
                    )
                )
            )
        )
    );

    // Insert the array, JSON ENCODED, into 'style_formats'
    $init['style_formats'] = json_encode($styleFormats);
    $init['wpautop'] = false;
    $init['remove_linebreaks'] = false;
    $init['convert_newlines_to_brs'] = true;
    $init['remove_redundant_brs'] = false;

    return $init;
}

add_filter('tiny_mce_before_init', 'customEditorInit', 1, 2);



// -----------------------------------------------------------------------------

// Callback function to insert 'styleselect' into the $buttons array
function customEditorButtons2($buttons) {
    if (($key = array_search('formatselect', $buttons)) !== false) {
        unset($buttons[$key]);
    }

    array_unshift($buttons, 'styleselect');

    return $buttons;
}

add_filter('mce_buttons_2', 'customEditorButtons2');



// -----------------------------------------------------------------------------

function typekit_mce_external_plugins($plugin_array){
    $plugin_array['typekit']  =  get_template_directory_uri().'/assets/dist/typekit.tinymce.js';
    return $plugin_array;
}

add_filter("mce_external_plugins", "typekit_mce_external_plugins");



// -----------------------------------------------------------------------------

function my_mce4_options($init) {
    $custom_colours = '
        "006bb6", "SV Blue",
        "0080db", "SV Blue - Tint",
        "005b9c", "SV Blue - Shade",
        "002742", "SV Dark Blue",
        "a7b5b4", "SV Grey",
        "f5f5f5", "SV Lighter Grey",
        "dddddd", "SV Light Grey"
    ';

    // build colour grid default+custom colors
    $init['textcolor_map'] = '['.$custom_colours.']';

    // change the number of rows in the grid if the number of colors changes
    // 8 swatches per row
    $init['textcolor_rows'] = 1;

    return $init;
}

add_filter('tiny_mce_before_init', 'my_mce4_options');



// -----------------------------------------------------------------------------

function peeAllowBr($content) {
    $content = wpautop($content, false);
    return $content;
}

// allow br
remove_filter('the_content', 'wpautop');
add_filter('the_content', 'peeAllowBr');
