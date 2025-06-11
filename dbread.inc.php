<?php
// include "youtube.php";

function plugin_dbread_convert()
{
    $host = "";
    $port = "";
    $user = "";
    $pass = "";

    $args = func_get_args();
    if(is_null($args[0]))
    {
        return "Error : データベースが指定されていません";
    }

    if(is_null($args[1]))
    {
        return "Error : テーブルが指定されていません";
    }

    $database = $args[0];
    $table = $args[1];
    $schema = "public";

    try
    {
        $pdo = new PDO("pgsql:host=$host;port=$port;dbname=$database;", $user, $pass);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    }
    catch(PDOException $e)
    {
        die("Database connection failed: " . $e->getMessage());
    }

    $limit = (int) $args[2] ?? (int) 0;
    try
    {
        $query = $pdo->prepare("SELECT * FROM " . $schema . "." . $table . " ORDER BY id DESC" . ($limit != 0 ? " LIMIT " . $limit : ""));
        // $query = $pdo->prepare("SELECT * FROM " . $schema . "." . $table);
        $query->execute();
        $rows = $query->fetchAll(PDO::FETCH_ASSOC);
    }
    catch(PDOException $e)
    {
        return "CREATE TABLE $table (id INT GENERATED ALWAYS AS IDENTITY PRIMARY KEY, video_id text, title text, date text, channel text, channel_url text, thumbnail_url text, url text, hours INT, minutes INT, seconds INT, text text);";
    }

    $html = "<table border = '1'>";
    foreach($rows as $row)
    {
        $html .= "<tr>";

        $time = $row["hours"] * 3600 + $row["minutes"] * 60 + $row["seconds"];
        $url = "https://www.youtube.com/watch?v=" . $row["video_id"] . "&t=" . $time . "s";
        $image_style = "width: 20vw; height: auto:";

        $html .= "<td>";
        //! サムネ画像
        $html .= "<div class = 'thumbnail'><a href='" . htmlspecialchars($url) . "'>" . "<img style='" . $image_style . "' src='" . htmlspecialchars($row["thumbnail_url"]) . "'>" . "</a></div></td>";
        $html .= "</td>";

        $html .= "<td style = 'vertical-align: top;'>";
        //! 文字
        $html .= "<div class = 'text' style = 'display: flex; flex: 1; flex-direction: column; align-items: start;'>";
        //! タイトル
        $html .= "<a style = 'font-size: 175%; font-weight: 700; margin: 5px 5px;' href='" . htmlspecialchars($url) . "'>" . htmlspecialchars($row["title"]) . "</a>";
        //! 投稿日付
        $html .= "<div style = 'font-size: 150%; font-weight: 550; margin: 5px 5px;'>" . $row["date"] . "</div>";
        //! チャンネル名
        $html .= "<a style = 'font-size: 150%; font-weight: 550; margin: 5px 5px;' href='" . htmlspecialchars($row["channel_url"]) . "'>" . htmlspecialchars($row["channel"]) . "</a>";
        //! 時間
        $html .= "<div style = 'font-size: 120%; font-weight: 475; margin: 5px 5px;'>" . htmlspecialchars(sprintf("%02d:%02d:%02d", $row["hours"], $row["minutes"], $row["seconds"])) . "</div>";
        //! 感想
        $html .= "<div style = 'font-size: 100%; font-weight: 400; margin: 5px 5px;'>" . nl2br(htmlspecialchars($row["text"], ENT_QUOTES, 'UTF-8')) . "</div>";
        $html .= "</div>";
        $html .= "</td>";

        /*
        $html .= "<td>";
        $html .= '<form method="post" action="' . get_base_uri() . '?cmd=dbwrite&page=' . $vars['page'] . '">';
        $html .= '<input type="submit" value="削除" /></br>';
        $html .= '</form></br>';
        $html .= "</td>";
         */

        $html .= "</tr>";
    }
    $html .= "</table>";
    return $html;
}

/*
function plugin_dbread_action()
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
    $data_info["id"] = $vars["id"];

    $database_info = [];
    $database_info["database"] = $vars["database"];
    $database_info["table"] = $vars["table"];
    $database_info["schema"] = "public";
    $database_info["host"] = $host;
    $database_info["port"] = $port;
    $database_info["user"] = $user;
    $database_info["pass"] = $pass;

    try
    {
        $query = $pdo->prepare("SELECT * FROM " . $schema . "." . $table . " ORDER BY id DESC" . ($limit != 0 ? " LIMIT " . $limit : ""));
        // $query->execute();
        $rows = $query->fetchAll(PDO::FETCH_ASSOC);
    }
    catch(PDOException $e)
    {
        return "";
    }
}
 */

?>

