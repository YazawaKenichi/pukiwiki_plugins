<?php
function plugin_regexlist_convert()
{
    $args = func_get_args();
    if(empty($args[0]))
    {
        return "Error : 正規表現が指定されていません";
    }
    $regex = $args[0];
    $list = array();
    $pages = get_existpages();
    foreach($pages as $page)
    {
        if(preg_match("/$regex/", $page))
        {
            $list[] = $page;
        }
    }
    if(empty($list))
    {
        return "";
    }
    sort($list);
    $html = "<ul>";
    foreach($list as $title)
    {
        $html .= "<li>" . make_pagelink($title) . "</li>";
    }
    $html .= "</ul>";
    return $html;
}
?>

