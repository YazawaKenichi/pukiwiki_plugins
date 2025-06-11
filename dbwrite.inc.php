<?php
function debugf($text)
{
    $scr = "<script>alert(" . json_encode($text) . ");</script>";
    echo $scr;
}

function plugin_dbwrite_convert()
{
    global $vars;

    $args = func_get_args();
    if(is_null($args[0]))
    {
        echo "<script>alert('データベースが指定されていません');</script>";
    }

    if(is_null($args[1]))
    {
        echo "<script>alert('テーブルが指定されていません');</script>";
    }

    $html = '';
    $html .= '<form method="post" action="' . get_base_uri() . '?cmd=dbwrite&page=' . $vars['page'] . '">';
    $html .= '<h2 hidden>Create New Record</h2>';
    $html .= '<label hidden>Database : <input type="text" name="database" placeholder = "DATABASE" value = ' . $args[0] . ' required></label>';
    $html .= '<label hidden>Table : <input type="text" name="table" placeholder = "TABLE" value = ' . $args[1] . ' required></br></label>';
    $html .= '<input type="text" name="url" placeholder = "URL" size = "40" required></br>';
    $html .= '<label>';
    //! HH
    $html .= '<select id="hours" name = "hours">';
    for($i = 0; $i < 12; $i++)
    {
        $html .= '<option value = "' . $i . '">' . $i . '</option>';
    }
    $html .= '</select>';
    $html .= ' 時間 ';
    //! MM
    $html .= '<select id="minutes" name = "minutes">';
    for($i = 0; $i < 60; $i++)
    {
        $html .= '<option value = "' . $i . '">' . $i . '</option>';
    }
    $html .= '</select>';
    $html .= ' 分 ';
    //! SS
    $html .= '<select id="seconds" name = "seconds">';
    for($i = 0; $i < 60; $i++)
    {
        $html .= '<option value = "' . $i . '">' . $i . '</option>';
    }
    $html .= '</select>';
    $html .= ' 秒 ';
    $html .= '</label></br>';
    $html .= '<textarea id="text" name="text" placeholder="感想" cols = "42" rows="10"></textarea></br>';
    $html .= '<input type="submit" value="登録" /></br>';
    $html .= '</form></br>';
    return $html;
}

function plugin_dbwrite_action()
{
    include "youtube.php";

    global $vars;

    $host = "192.168.3.26";
    $port = "25253";
    $user = "yazawa";
    $pass = "lkjl";

    $data_info = [];
    $data_info["url"] = $vars["url"];
    $data_info["hours"] = (int) $vars["hours"];
    $data_info["minutes"] = (int) $vars["minutes"];
    $data_info["seconds"] = (int) $vars["seconds"];
    $data_info["text"] = $vars["text"];

    $database_info = [];
    $database_info["database"] = $vars["database"];
    $database_info["table"] = $vars["table"];
    $database_info["schema"] = "public";
    $database_info["host"] = $host;
    $database_info["port"] = $port;
    $database_info["user"] = $user;
    $database_info["pass"] = $pass;

    insert_database($data_info, $database_info);

    // return array('msg'=>"Page Title", 'body'=>"Body Main Text");
}
?>

