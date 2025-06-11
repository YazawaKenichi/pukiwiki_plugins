<?php
function plugin_citeurl_convert()
{
    $args = func_get_args();
    if(empty($args[0]))
    {
        return "Usage : #citeurl(https://examples.com)";
    }
    $url = $args[0];
    try
    {
        $meta = fetchPageMeta($url);
        $citation = formatPageCitation($meta);
        return $citation;
    }
    catch(RuntimeException $e)
    {
        return "Error : " . $e->getMessage();
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

    // --- DOM 解析 ---
    libxml_use_internal_errors(true);
    $dom = new DOMDocument();
    $dom->loadHTML($html, LIBXML_NOWARNING | LIBXML_NOERROR);
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
    $srcUrl = $url;

    // 6. 閲覧日（関数実行日）
    $accessed = (new DateTime('now', new DateTimeZone('Asia/Tokyo')))->format('Y-m-d');

    return compact('author', 'updated', 'title', 'site', 'srcUrl') + ['accessed' => $accessed];
}

function formatPageCitation(array $meta): string
{
    $meta['srcUrl'] = '<a href = "' . $meta['srcUrl'] . '">' . $meta['srcUrl'] . '</a>';

    $author   = isset($meta['author'])   ? $meta['author']  . ". " : '';
    $title    = isset($meta['title'])    ? $meta['title']   . ". " : '';
    $site     = isset($meta['site'])     ? $meta['site']    . ", " : '';
    $updated  = isset($meta['updated'])  ? $meta['updated'] . ". " : '';
    $srcUrl   = isset($meta['srcUrl'])   ? $meta['srcUrl']  . ", " : '';
    $accessed = isset($meta['accessed']) ? "(visited on "   . $meta['accessed'] . "). " : '';

    return sprintf(
        '%s%s%s%s%s<br>',
        $author,
        $title,
        $srcUrl,
        $updated,
        $accessed
    );
}
?>

