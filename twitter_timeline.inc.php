<?php
/*
    Twitterウィジェット表示プラグイン
    twitter_timeline.inc.php

    by mogaminsk

    Released under the MIT License.
    http://opensource.org/licenses/MIT
*/

function plugin_twitter_timeline_convert()
{
    $params = array(
        '@'      => '',
        'width'  => '1200',
        'height' => '600',
        'theme'  => 'light',
        'lang'   => 'ja'
    );

    if(!func_num_args()) return FALSE;
    $args = func_get_args();
    if(!empty($args)) foreach($args as $arg) twitter_timeline_check_arg($arg, $params);

    return <<<EOD
        <a class="twitter-timeline" data-lang="{$params['lang']}" data-width="{$params['width']}" data-height="{$params['height']}" data-dnt="true" data-theme="{$params['theme']}" href="https://twitter.com/{$params['@']}">Tweets by {$params['@']}</a>
        <script async src="https://platform.twitter.com/widgets.js" charset="utf-8"></script> 
        EOD;
}

function plugin_twitter_timeline_inline()
{
    $args = func_get_args();
    return call_user_func_array('plugin_twitter_timeline_convert', $args);
}

function twitter_timeline_check_arg($val, & $params)
{
    if(!strpos($val, '=')) return;
    list($l_val, $v_val) = explode('=', strtolower($val));
    foreach(array_keys($params) as $key)
    {
        if(strpos($l_val, $key) === 0)
        {
            $params[$key] = $v_val;
            return;
        }
    }
}
?>

