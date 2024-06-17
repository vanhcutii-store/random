<?php

header('Content-Type: application/json');

// Function to fetch website information and return as JSON
function getWebsiteInfo($url) {
    $info = array();

    // Function to fetch headers and content using cURL
    function fetchHeadersAndContent($url) {
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HEADER, true);
        $response = curl_exec($ch);
        $headerSize = curl_getinfo($ch, CURLINFO_HEADER_SIZE);
        $headers = substr($response, 0, $headerSize);
        $content = substr($response, $headerSize);
        curl_close($ch);
        
        return array('headers' => $headers, 'content' => $content);
    }

    // Fetch headers and content using cURL
    $result = fetchHeadersAndContent($url);
    $headers = $result['headers'];
    $content = $result['content'];

    // Get title from HTML content
    preg_match("/<title>(.*?)<\/title>/i", $content, $matches);
    $title = isset($matches[1]) ? $matches[1] : "No title found";

    // Get IP address from headers (if available)
    $ip = isset($headers['Location']) ? parse_url($headers['Location'], PHP_URL_HOST) : parse_url($url, PHP_URL_HOST);

    // Get ISP information using ipinfo.io API
    $ipinfo = @file_get_contents("https://ipinfo.io/{$ip}/json");
    if ($ipinfo !== false) {
        $ipinfo = json_decode($ipinfo, true);
        $isp = isset($ipinfo['org']) ? $ipinfo['org'] : "Unknown ISP";
    } else {
        $isp = "Unknown ISP";
    }

    // Generate JA3 and JA4 (placeholders)
    $ja3 = md5("JA3 placeholder");
    $ja4 = md5("JA4 placeholder");

    // Get Request Headers
    $request_headers = getallheaders();

    // Parse general headers
    $general_headers = parseGeneralHeaders($headers);

    // Prepare the response
    $info['url'] = $url;
    $info['isp'] = $isp;
    $info['title'] = $title;
    $info['cplist'] = $cplist;
    $info['request_headers'] = $request_headers;
    $info['ja3'] = $ja3;
    $info['ja4'] = $ja4;
    $info['general_headers'] = $general_headers;
    $info['response_headers'] = parseResponseHeaders($headers); // Include all response headers

    // Format headers array for better JSON output
    $formatted_headers = array();
    foreach ($headers as $key => $value) {
        if (!is_numeric($key)) { // Exclude numeric keys (which are HTTP status lines)
            $formatted_headers[$key] = $value;
        }
    }
    $info['headers'] = $formatted_headers;

    return json_encode($info, JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT);
}

// Function to parse general headers from raw headers
function parseGeneralHeaders($raw_headers) {
    $general_headers = array();

    // Extract and parse general headers
    preg_match_all('/^(.+?):\s+(.*)$/m', $raw_headers, $matches, PREG_SET_ORDER);
    foreach ($matches as $match) {
        $header_name = trim($match[1]);
        $header_value = trim($match[2]);
        $general_headers[$header_name] = $header_value;
    }

    return $general_headers;
}

// Function to parse all response headers from raw headers
function parseResponseHeaders($raw_headers) {
    // Split headers into lines
    $lines = explode("\r\n", $raw_headers);

    // Remove the status line
    array_shift($lines);

    // Parse each header into key => value
    $response_headers = array();
    foreach ($lines as $line) {
        $parts = explode(': ', $line, 2);
        if (count($parts) === 2) {
            $header_name = $parts[0];
            $header_value = $parts[1];
            $response_headers[$header_name] = $header_value;
        }
    }

    return $response_headers;
}

// Function to check status code of a website multiple times asynchronously
function checkStatusCodeAsync($url, $times) {
    $mh = curl_multi_init();
    $curl_handles = array();
    $results = array();

    for ($i = 0; $i < $times; $i++) {
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_NOBODY, true); // HEAD request to get headers only
        curl_multi_add_handle($mh, $ch);
        $curl_handles[] = $ch;
    }

    $running = null;
    do {
        curl_multi_exec($mh, $running);
    } while ($running > 0);

    foreach ($curl_handles as $ch) {
        $status_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $response_time = curl_getinfo($ch, CURLINFO_TOTAL_TIME) * 1000; // in milliseconds

        // Get title
        $content = curl_exec($ch);
        preg_match("/<title>(.*?)<\/title>/i", $content, $matches);
        $title = isset($matches[1]) ? $matches[1] : "No title found";

        // Prepare result
        $result = array(
            'url' => $url,
            'status_code' => $status_code,
            'title' => $title,
            'response_time' => round($response_time) . ' ms'
        );

        // Save result
        $results[] = $result;

        curl_multi_remove_handle($mh, $ch);
        curl_close($ch);

        // Delay for 1 second (optional)
        usleep(1000000); // 1 second = 1,000,000 microseconds
    }

    curl_multi_close($mh);

    // Return JSON-encoded array of results
    return json_encode($results, JSON_PRETTY_PRINT);
}

// Main logic to handle different cases
if (isset($_GET['url'])) {
    $url = $_GET['url'];
    if (filter_var($url, FILTER_VALIDATE_URL)) {
        if (isset($_GET['check'])) {
            // Case 2: Perform status code check asynchronously
            $check_times = intval($_GET['check']);
            $status_codes = checkStatusCodeAsync($url, $check_times);
            echo $status_codes;
        } else {
            // Case 1: Get website information
            $website_info = getWebsiteInfo($url);
            echo $website_info;
        }
    } else {
        echo json_encode(array('error' => 'Invalid URL'), JSON_PRETTY_PRINT);
    }
} else {
    // Handle case when URL parameter is not provided
    echo json_encode(array('error' => 'Missing URL parameter'), JSON_PRETTY_PRINT);
}

?>
