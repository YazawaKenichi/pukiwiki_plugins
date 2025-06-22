<?php
function plugin_citeurl_convert()
{
    static $count = 0;
    $count++;
    $args = func_get_args();
    if(empty($args[0]))
    {
        return "Usage : #citeurl(URL, [author=\"...\"], [title=\"...\"], [url=\"...\"], [updated=\"YYYY-MM-DD\"], [accessed=\"YYYY-MM-DD\"])";
    }
    $url = $args[0];
    $options = [];
    foreach(array_slice($args, 1) as $arg)
    {
        if(preg_match('/^(\w+)\s*=\s*"(.*?)"$/', $arg, $m))
        {
            $options[$m[1]] = $m[2];
            echo htmlspecialchars('Hello, World!');
        }
    }

    try
    {
        $meta = fetchPageMeta($url);
        $citation = formatPageCitation($meta, $options, $count);
        return $citation;
    }
    catch(RuntimeException $e)
    {
        return "Error : " . $e->getMessage();
    }
    catch(Throwable $e)
    {
        return "Fatal : " . $e->getMessage();
    }
}

function fetchPageMeta(string $url): array
{
    // --- HTML 取得 ---
    $context = stream_context_create(['http' => ['header' => "User-Agent: Mozilla/5.0\r\n"]]);
    $html = @file_get_contents($url, false, $context);
    if($html === false)
    {
        throw new RuntimeException("URL にアクセスできません : $url");
    }

    // --- 文字コードの変換 ---
    $encoding = mb_detect_encoding($html, ["UTF-8", "SJIS", "EUC-JP", "ISO-2022-JP", "ASCII"], true);
    if($encoding && $encoding !== "UTF-8")
    {
        $html = mb_convert_encoding($html, "UTF-8", $encoding);
    }

    // --- DOM 解析 ---
    libxml_use_internal_errors(true);
    $dom = new DOMDocument();
    $dom->loadHTML('<?xml encoding="UTF-8">' . $html, LIBXML_NOWARNING | LIBXML_NOERROR);
    $xpath = new DOMXPath($dom);

    // ヘルパ: XPath で最初にヒットした content を返す
    $first = fn(array $queries): ?string =>
        (function ($queries, $xpath)
        {
            foreach ($queries as $q)
            {
                $n = $xpath->query($q)->item(0);
                if($n instanceof DOMElement)
                {
                    return trim($n->getAttribute('content') ?: $n->textContent);
                }
            }
            return null;
        })($queries, $xpath);

    // 1. 著者名
    $author = $first([
        '//meta[@name="author"]',
        '//meta[@property="article:author"]',
        '//meta[@property="og:author"]'
    ]);

    // 2. 更新日
    $updated = $first([
        '//meta[@property="article:modified_time"]',
        '//meta[@name="last-modified"]',
        '//meta[@name="last_modified"]',
        '//time[@itemprop="dateModified"]',
        '//time[@property="dateModified"]'
    ]);

    // 3. ページ名（title 要素 / og:title）
    $node = $xpath->query('//title')->item(0);
    $title = $node ? trim($node->textContent) : null;

    // 4. サイト名（og:site_name > ドメイン）
    $site = $first(['//meta[@property="og:site_name"]']);
    if (!$site)
    {
        $host = parse_url($url, PHP_URL_HOST);
        $site = $host ?: null;
    }

    // 5. 入手先 URL（そのまま）
    $src = $url;

    // 6. 閲覧日（関数実行日）
    $accessed = (new DateTime('now', new DateTimeZone('Asia/Tokyo')))->format('Y-m-d');

    return [
        'author'   => $author,
        'updated'  => $updated,
        'title'    => $title,
        'site'     => $site,
        'src'      => $src,
        'accessed' => $accessed
    ];
}

function formatPageCitation(array $meta, array $options, int $index): string
{
    if(isset($options['src']))
    {
        $options['src'] = '<a href = "' . htmlspecialchars($options['src']) . '">' . htmlspecialchars($options['src']) . '</a>';
    }

    if(isset($meta['src']))
    {
        $meta['src'] = '<a href = "' . htmlspecialchars($meta['src']) . '">' . htmlspecialchars($meta['src']) . '</a>';
    }

    $_author   = $options['author']   ?? $meta['author']   ?? '';
    $_site     = $options['site']     ?? $meta['site']     ?? '';
    $_title    = $options['title']    ?? $meta['title']    ?? '';
    $_updated  = $options['updated']  ?? $meta['updated']  ?? '';
    $_srcUrl   = $options['src']      ?? $meta['src']      ?? '';
    $_accessed = $options['accessed'] ?? $meta['accessed'] ?? '';

    $author   = $_author   !== '' ? $_author   . '. ' : '';
    $title    = $_title    !== '' ? $_title    . '. ' : '';
    $site     = $_site     !== '' ? $_site     . '. ' : '';
    $updated  = $_updated  !== '' ? $_updated  . '. ' : '';
    $srcUrl   = $_srcUrl   !== '' ? $_srcUrl   . '. ' : '';
    $accessed = $_accessed !== '' ? '(visited on ' . $_accessed . '). ' : '';

    return sprintf(
        '[%d] %s%s%s%s%s<br>',
        $index,
        $author,
        $title,
        $srcUrl,
        $updated,
        $accessed
    );
}
?>

