<?php
/*
Copyright (c) 2007 - 2010 Arkayne, Inc.

Permission is hereby granted, free of charge, to any person obtaining a copy of this software and associated documentation files (the "Software"), to deal in the Software without restriction, including without limitation the rights to use, copy, modify, merge, publish, distribute, sublicense, and/or sell copies of the Software, and to permit persons to whom the Software is furnished to do so, subject to the following conditions:

The above copyright notice and this permission notice shall be included in all copies or substantial portions of the Software.

THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY, FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM, OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE SOFTWARE.
*/

// Global variable to flag excerpt
$ARKAYNE_IS_EXCERPT = 0;

// Custom Arkayne excerpt is identical except it removes the plugin to prevent partial rendering
// Replaces: wp-includes/formatting.php (wp_trim_excerpt)
function arkayne_trim_excerpt($text) {
        $raw_excerpt = $text;
        if ( '' == $text ) {
                $GLOBALS['ARKAYNE_IS_EXCERPT'] = 1; // Added by Arkayne, sets flag for the_content
                $text = get_the_content('');
                $text = strip_shortcodes( $text );
                $text = apply_filters('the_content', $text);
                $GLOBALS['ARKAYNE_IS_EXCERPT'] = 0; // Added by Arkayne, unsets flag for the_content
                $text = str_replace(']]>', ']]&gt;', $text);
                $text = strip_tags($text);
                $excerpt_length = apply_filters('excerpt_length', 55);
                $words = explode(' ', $text, $excerpt_length + 1);
                if (count($words) > $excerpt_length) {
                        array_pop($words);
                        array_push($words, '[...]');
                        $text = implode(' ', $words);
                }
        }
        return apply_filters('wp_trim_excerpt', $text, $raw_excerpt);
}

// Switch out filter to accomodate Arkayne code at the bottom of the content
remove_filter('get_the_excerpt', 'wp_trim_excerpt');
add_filter('get_the_excerpt', 'arkayne_trim_excerpt');
?>
