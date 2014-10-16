<?php
function NFSearch_handler($target) {
    global $blog, $entry, $suri;

    $context = Model_Context::getInstance();

    $referrer = isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : '';
    $host = $_SERVER['HTTP_HOST'];

    // when visitor access from search engine and entry is not found
    if(!empty($referrer) && stripos($referrer, $host) === false && (
       (stripos($suri['url'], 'entry') !== false && is_null($entry)) ||
       (array_key_exists('id', $suri) !== false && is_null($entry))
       )) {

        $keyword = extract_keyword($referrer);
        if(!empty($keyword)) {
            $url = '/search/' . $keyword;
            $meta = '<meta http-equiv="refresh" content="0; url=' . $url . '">';
            $target .= CRLF . $meta . CRLF;
        }
    }

    return $target;
}

function extract_keyword($ref) {
    $keyword = '';

    $fixed_ref = str_replace('?', '?&', $ref);
    $output = array();
    parse_str($fixed_ref, $output);

    $list = array(
        'q', 'query', 'k', 'keyword', 'search',
        'stext', 'oq', 'nlia', 'aqa', 'wd', 'p');
    foreach($list as $key) {
        if(array_key_exists($key, $output) !== false) {
            $keyword = $output[$key];
            break;
        }
    }
    unset($list);

    if(empty($keyword) &&
       preg_match('@/search/(?:\w+/)*([^/?]+)@i', $ref, $matches)) {
        if(isset($matches[1]) && !empty($matches[1])) {
            $keyword = $matches[1];
        }
    }

    if(!UTF8::validate($keyword)) {
        $keyword = UTF8::correct(UTF8::bring($keyword));
    }

    return $keyword;
}
?>