<?php

/**
 * @param mixed $param
 * @param bool|int $continue
 * @param string|null $label
 *
 * @return void|null
 */
function pr($param = [], $continue = true, string $label = null)
{
    if (!APP_DEBUG) {
        return null;
    }
    if (!empty($label)) {
        echo '<p>-- '.$label.' --</p>';
    }

    echo '<pre>';
    print_r($param);
    echo '</pre><br />';

    if (!$continue) {
        die('-- code execution discontinued --');
    }
}

/**
 * @param $respHeaders
 *
 * @return array
 */
function getHeaders($respHeaders): array
{
    $headers = [];

    $headerText = substr($respHeaders, 0, strpos($respHeaders, "\r\n\r\n"));

    foreach (explode("\r\n", $headerText) as $i => $line) {
        if (($i === 0) && (preg_match('/^HTTP\/(\d+\.\d+)\s+(\d+)\s*(.+)?$/', $line, $result))) {
            $headers['http_str'] = $result[0];
            $headers['http_version'] = $result[1];
            $headers['http_code'] = $result[2];
            $headers['http_status_string'] = $result[3];
        } else {
            list($key, $value) = explode(': ', $line);
            $headers[$key] = $value;
        }
    }

    return $headers;
}

/**
 * @param string $uri
 *
 * @return string
 */
function getSiteURL(string $uri = '/'): string
{
    return rtrim(BASE_URL, '/').'/'.ltrim($uri, '/');
}

/**
 * @param string $uri
 *
 * @return string
 */
function getAssetURI(string $uri): string
{
    return rtrim(getSiteURL(), '/').'/src/views/'.ltrim($uri, '/');
}

/**
 * @return string
 *
 * @throws Exception
 */
function generateCSRFToken(): string
{
    $t = bin2hex(random_bytes(32));
    $_SESSION['csrf_token'] = $t;

    return $t;
}
