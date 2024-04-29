<?php
$website = "https://vanhcutii.store";
$video_list = array(
    "example.com/1.mp4",
    "example.com/2.mp4",
    "example.com/3.mp4",
    "example.com/4.mp4",
);

$random_video = $video_list[array_rand($video_list)];
$ip = $_SERVER['REMOTE_ADDR'];
$location = file_get_contents("http://ipinfo.io/{$ip}/json");
$location_data = json_decode($location);
$user_agent = $_SERVER['HTTP_USER_AGENT'];

$browser_info = get_browser(null, true);
$os = isset($browser_info['platform']) ? $browser_info['platform'] : "unknown";

$cookie = isset($_COOKIE['your_cookie_name']) ? $_COOKIE['your_cookie_name'] : "Không có cookie được gửi từ trình duyệt";

$current_time = date("Y-m-d H:i:s");

$website_data = array(
    "website" => $website,
    "ip" => $ip,
    "city" => isset($location_data->city) ? $location_data->city : "Không xác định",
    "region" => isset($location_data->region) ? $location_data->region : "Không xác định",
    "country" => isset($location_data->country) ? $location_data->country : "Không xác định",
    "loc" => isset($location_data->loc) ? $location_data->loc : "Không xác định",
    "org" => isset($location_data->org) ? $location_data->org : "Không xác định",
    "postal" => isset($location_data->postal) ? $location_data->postal : "Không xác định",
    "timezone" => isset($location_data->timezone) ? $location_data->timezone : "Không xác định",
    "user_agent" => $user_agent,
    "cookie" => $cookie,
    "current_time" => $current_time,
    "os" => $os
);

$video_data = array(
    "video_id" => $random_video
);

$json_website_data = json_encode($website_data, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
$json_video_data = json_encode($video_data, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);

header('Content-Type: application/json');
echo $json_website_data . "\n";
echo $json_video_data;
?>
