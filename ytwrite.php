<?php
function yt()
{
    include "youtube.php";

    global $vars;

    $host = "";
    $port = "";
    $user = "";
    $pass = "";

    $html = '';
    $database = $vars['database'];
    $table = $vars['table'];
    $schema = "public";

    $pdo = new PDO("pgsql:host=$host;port=$port;dbname=$database;", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $api_url = "";
    $yt_content = new YouTubeContent($api_url);
    $yt_content->getContents($vars["url"]);
    $baselink = "https://www.youtube.com/watch?v=";

    $video_id = $yt_content->id;
    $title = $yt_content->title;

    $date = date('Y-m-d H:i:s', $yt_content->date);

    $channel = $yt_content->channel;
    $channel_url = $yt_content->channel_url;
    $thumbnail_img = file_get_contents($yt_content->thumbnail);
    $thumbnail_url = $yt_content->thumbnail;

    $url = $baselink . $video_id;
    $hours = (int) $vars["hours"];
    $minutes = (int) $vars["minutes"];
    $seconds = (int) $vars["seconds"];
    $text = $vars["text"];

    //! サムネ画像をバイナリとして保存するならこっち
    // $stmt = $pdo->prepare("INSERT INTO $table (video_id, title, channel, channel_url, thumbnail_img, thumbnail_url text, url, hours, minutes, seconds, text) VALUES (:video_id, :title, :channel, :channel_url, :thumbnail_img, :thumbnail_url, :url, :hours, :minutes, :seconds, :text)");

    //! サムネ画像をバイナリとして保存しないならこっち
    // echo "<script>alert('" . $video_id . ", " . $title . ", " . $date . ", " . $channel . ", " . $channel_url . ", " . $thumbnail_url . ", " . $url . ", " . $hours . ", " . $minutes . ", " . $seconds . ", " . $text . "');</script>";
    $stmt = $pdo->prepare("INSERT INTO $table (video_id, title, date, channel, channel_url, thumbnail_url, url, hours, minutes, seconds, text) VALUES (:video_id, :title, :date, :channel, :channel_url, :thumbnail_url, :url, :hours, :minutes, :seconds, :text)");

    // プレースホルダーに値をバインド
    $stmt->bindParam(':video_id', $video_id, PDO::PARAM_STR);
    $stmt->bindParam(':title', $title, PDO::PARAM_STR);
    $stmt->bindParam(':date', $date, PDO::PARAM_STR);
    $stmt->bindParam(':channel', $channel, PDO::PARAM_STR);
    $stmt->bindParam(':channel_url', $channel_url, PDO::PARAM_STR);
    // $stmt->bindParam(':thumbnail_img', $thumbnail_img, PDO::PARAM_LOB);      //! サムネ画像をバイナリとして保存するなら有効化
    $stmt->bindParam(':thumbnail_url', $thumbnail_url, PDO::PARAM_STR);
    $stmt->bindParam(':url', $url, PDO::PARAM_STR);
    $stmt->bindParam(':hours', $hours, PDO::PARAM_INT);
    $stmt->bindParam(':minutes', $minutes, PDO::PARAM_INT);
    $stmt->bindParam(':seconds', $seconds, PDO::PARAM_INT);
    $stmt->bindParam(':text', $text, PDO::PARAM_STR);

    // クエリの実行
    $stmt->execute();

    // return array('msg'=>"Page Title", 'body'=>"Body Main Text");
}
?>


