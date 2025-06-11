<?php
//! YouTube を解析するのに便利なクラス
class YouTubeContent
{
    private $url;
    private $api_url;

    public $id;
    public $title;
    public $date;
    public $channel;
    public $channel_url;
    public $thumbnail;

    public function debugf($text)
    {
        # $scr = "<script>alert(" . json_encode($text) . ");</script>";
        # echo $scr;
        print_r($text);
    }

    public function __construct($api_url)
    {
        $this->api_url = $api_url;
    }

    public function parseURL($url)
    {
        $parsed_url = parse_url($url);
        parse_str($parsed_url["query"], $query_params);
        $result = [
            "domain" => $parsed_url["scheme"] . "://" . $parsed_url["host"],
            "path" => $parsed_url["path"],
            "params" => $query_params
        ];
        return $result;
    }

    public function getContents($_url)
    {
        $parsed = $this->parseURL($_url);
        if(isset($parsed["params"]["v"]))
        {
            $url = $parsed["domain"] . $parsed["path"] . "?v=" . $parsed["params"]["v"];
        }
        else
        {
            $url = $parsed["domain"] . $parsed["path"];
        }
        //! JSON データ作成
        $data = json_encode(["url" => $url]);

        //! HTTP リクエストの設定
        $options = [
            "http" => [
                "method" => "POST",
                "header" => "Content-Type: application/json\r\n",
                "content" => $data
            ]
        ];

        //! コンテキストの作成
        $context = stream_context_create($options);

        //! リクエストの送信
        $response = file_get_contents($this->api_url, false, $context);

        if($response === FALSE)
        {
            $error = error_get_last();
            echo "Error : " . $error["message"];
        }

        $result = json_decode($response, true);

        $this->url = $result['video_url'] ?? "None";
        $this->id = $result['video_id'] ?? "None";
        $this->title = $result['title'] ?? "None";
        $this->date = $result['release_timestamp'] ?? "None";
        $this->channel = $result['channel'] ?? "None";
        $this->channel_url = $result['channel_url'] ?? "None";
        $this->thumbnail = $result['thumbnail'] ?? "None";
    }
}

function update_database($data_info, $database_info)
{
    $host = $database_info["host"];
    $port = $database_info["port"];
    $user = $database_info["user"];
    $pass = $database_info["pass"];
    $database = $database_info["database"];
    $schema = $database_info["schema"];
    $table = $database_info["table"];
    $hours = (int) $data_info["hours"];
    $minutes = (int) $data_info["minutes"];
    $seconds = (int) $data_info["seconds"];
    $text = $data_info["text"];
    $id = $data_info["id"];

    $pdo = new PDO("pgsql:host=$host;port=$port;dbname=$database;", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $stmt = $pdo->prepare("UPDATE $table WHERE id = :id SET hours = :hours, minutes = :minutes, seconds = :seconds, text = :text");
    $stmt->bindParam(':id', $id, PDO::PARAM_INT);
    $stmt->bindParam(':hours', $hours, PDO::PARAM_INT);
    $stmt->bindParam(':minutes', $minutes, PDO::PARAM_INT);
    $stmt->bindParam(':seconds', $seconds, PDO::PARAM_INT);
    $stmt->bindParam(':text', $text, PDO::PARAM_STR);

    $stmt->execute();
}

function insert_database($data_info, $database_info)
{
    $host = $database_info["host"];
    $port = $database_info["port"];
    $user = $database_info["user"];
    $pass = $database_info["pass"];
    $database = $database_info["database"];
    $schema = $database_info["schema"];
    $table = $database_info["table"];

    $html = '';

    $pdo = new PDO("pgsql:host=$host;port=$port;dbname=$database;", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $api_url = "http://192.168.3.26:25254/video_info";
    $yt_content = new YouTubeContent($api_url);
    $yt_content->getContents($data_info['url']);
    $baselink = "https://www.youtube.com/watch?v=";

    $video_id = $yt_content->id;
    $title = $yt_content->title;

    $date = date('Y-m-d H:i:s', $yt_content->date);

    $channel = $yt_content->channel;
    $channel_url = $yt_content->channel_url;
    $thumbnail_img = file_get_contents($yt_content->thumbnail);
    $thumbnail_url = $yt_content->thumbnail;

    $url = $baselink . $video_id;
    $hours = (int) $data_info["hours"];
    $minutes = (int) $data_info["minutes"];
    $seconds = (int) $data_info["seconds"];
    $text = $data_info["text"];

    $stmt = $pdo->prepare("INSERT INTO $table (video_id, title, date, channel, channel_url, thumbnail_url, url, hours, minutes, seconds, text) VALUES (:video_id, :title, :date, :channel, :channel_url, :thumbnail_url, :url, :hours, :minutes, :seconds, :text)");

    // プレースホルダーに値をバインド
    $stmt->bindParam(':video_id', $video_id, PDO::PARAM_STR);
    $stmt->bindParam(':title', $title, PDO::PARAM_STR);
    $stmt->bindParam(':date', $date, PDO::PARAM_STR);
    $stmt->bindParam(':channel', $channel, PDO::PARAM_STR);
    $stmt->bindParam(':channel_url', $channel_url, PDO::PARAM_STR);
    $stmt->bindParam(':thumbnail_url', $thumbnail_url, PDO::PARAM_STR);
    $stmt->bindParam(':url', $url, PDO::PARAM_STR);
    $stmt->bindParam(':hours', $hours, PDO::PARAM_INT);
    $stmt->bindParam(':minutes', $minutes, PDO::PARAM_INT);
    $stmt->bindParam(':seconds', $seconds, PDO::PARAM_INT);
    $stmt->bindParam(':text', $text, PDO::PARAM_STR);

    // クエリの実行
    $stmt->execute();
}
?>

