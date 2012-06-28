<?php
/*
Copyright (c) 2007 - 2010 Arkayne, Inc.

Permission is hereby granted, free of charge, to any person obtaining a copy of this software and associated documentation files (the "Software"), to deal in the Software without restriction, including without limitation the rights to use, copy, modify, merge, publish, distribute, sublicense, and/or sell copies of the Software, and to permit persons to whom the Software is furnished to do so, subject to the following conditions:

The above copyright notice and this permission notice shall be included in all copies or substantial portions of the Software.

THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY, FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM, OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE SOFTWARE.
*/

function show_arkayne_meta_box()
{
  global $post;
  global $arkayne_server;

  $url = "";
  $status =  get_post_status($post->ID);

  /* if not yet saved */
  if ($status == 'auto-draft')
  {
    echo '<p>You must save a draft first.</p>';
  }
  else if ($status == "publish")
  {
    $url = urlencode(get_permalink($post->ID));
  }
  else 
  {
    $url = urlencode(apply_filters('preview_post_link', add_query_arg('arkayne_preview', $post->ID, add_query_arg('preview', 'true', get_permalink($post->ID)))));
  }

  if ($url)
  {
    /* Grep the token for the current post */
    list($temp_num, $temp_string) = split('-', arkayne_token($post->ID), 2);
    $seo_token = $temp_num . '-' . md5($temp_string);

    if (get_option('arkayne_show_seo') == 'TRUE')
    {
      echo '<p><a href="http://' . $arkayne_server . '/seo/test/' . $seo_token . '/?url=' . $url . '&TB_iframe=true&height=600&width=400" title="BlogGlue Analyzer - Improve your chances of being found online!" class="thickbox"><img src="http://s3.amazonaws.com/arkayne-media/img/btn-test-now.png" style="vertical-align: middle; margin-right: 5px;"></a> Check SEO Before Publishing.</p>';
    }

  }
}

function add_arkayne_seo_box() 
{
  if (get_option('arkayne_show_seo') == 'TRUE')
  {
    add_meta_box('arkayne-seo-box', 'BlogGlue Toolbox', 'show_arkayne_meta_box', 'post', 'side', 'high');
    add_meta_box('arkayne-seo-box', 'BlogGlue Toolbox', 'show_arkayne_meta_box', 'page', 'side', 'high');
  }
}

if (is_admin()) add_action('admin_menu', 'add_arkayne_seo_box');

/* Handle Preview Draft Issue */

class Arkayne_Preview 
{
  var $id;

  function Arkayne_Preview() 
  {
    if (!is_admin()) add_action('init', array(&$this, 'show_arkayne_preview'));
  }

  function arkayne_publish($posts) 
  {
    $posts[0]->post_status = 'publish';
    return $posts;
  }

  function show_arkayne_preview() 
  {
    if (!is_admin() && isset($_GET['preview']) && isset($_GET['arkayne_preview']) ) 
    {
      $this->id = (int) $_GET['arkayne_preview'];
      add_filter('posts_results', array(&$this, 'arkayne_publish'));
    }
  }
}

$arkayne_preview = new Arkayne_Preview();
?>
