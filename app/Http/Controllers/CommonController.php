<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use GuzzleHttp\Client;
use Carbon\Carbon;
use DateInterval;
use GuzzleHttp\Exception\RequestException;
class CommonController extends Controller
{
	// Get video details new code
	// function get_video_details(Request $request, $url = "")
	// {
	// 	if ($url == "") {
	// 		$url = $request->url;
	// 	}

	// 	$host = explode('.', str_replace('www.', '', strtolower(parse_url($url, PHP_URL_HOST))));
	// 	$host = isset($host[0]) ? $host[0] : $host;

	// 	$vimeo_api_key = get_settings('vimeo_api_key');
	// 	$youtube_api_key = get_settings('youtube_api_key');

	// 	if ($host == 'vimeo') {
	// 		$video_id = substr(parse_url($url, PHP_URL_PATH), 1);
	// 		$options = array('http' => array(
	// 			'method'  => 'GET',
	// 			'header' => 'Authorization: Bearer ' . $vimeo_api_key
	// 		));
	// 		$context  = stream_context_create($options);

	// 		try {
	// 			$hash = json_decode(file_get_contents("https://api.vimeo.com/videos/{$video_id}", false, $context));
	// 		} catch (\Throwable $th) {
	// 			$hash = '';
	// 		}

	// 		if ($hash == '') return;


	// 		return array(
	// 			'provider'          => 'Vimeo',
	// 			'video_id'			=> $video_id,
	// 			'title'             => $hash->name,
	// 			'thumbnail'         => $hash->pictures->sizes[0]->link,
	// 			'video'             => $hash->link,
	// 			'embed_video'       => "https://player.vimeo.com/video/" . $video_id,
	// 			'duration'			=>	gmdate("H:i:s", $hash->duration)
	// 		);
	// 	} elseif ($host == 'youtube' || $host == 'youtu') {
	// 		preg_match('%(?:youtube(?:-nocookie)?\.com/(?:[^/]+/.+/|(?:v|e(?:mbed)?)/|.*[?&]v=)|youtu\.be/)([^"&?/ ]{11})%i', $url, $match);
	// 		$video_id = $match[1];

	// 		try {
	// 			$hash = json_decode(file_get_contents("https://www.googleapis.com/youtube/v3/videos?part=snippet,contentDetails&id=$video_id&key=$youtube_api_key"));
	// 		} catch (\Throwable $th) {
	// 			$hash = '';
	// 		}

	// 		if ($hash == '') return;

	// 		$duration = new DateInterval($hash->items[0]->contentDetails->duration);
	// 		return array(
	// 			'provider'          => 'YouTube',
	// 			'video_id'			=> $video_id,
	// 			'title'             => $hash->items[0]->snippet->title,
	// 			'thumbnail'         => 'https://i.ytimg.com/vi/' . $hash->items[0]->id . '/default.jpg',
	// 			'video'             => "http://www.youtube.com/watch?v=" . $hash->items[0]->id,
	// 			'embed_video'       => "http://www.youtube.com/embed/" . $hash->items[0]->id,
	// 			'duration'       	=> $duration->format('%H:%I:%S'),
	// 		);
	// 	} elseif ($host == 'drive') {
	// 	}
	// }



	function get_video_details(Request $request, $url = "")
	{
		if ($url == "") {
			$url = $request->url;
		}
	
		$host = parse_url($url, PHP_URL_HOST);
		$host = str_replace('www.', '', strtolower($host));
	
		$vimeo_api_key = get_settings('vimeo_api_key');
	
		if ($host === 'youtube.com' || $host === 'youtu.be') {
			preg_match('%(?:youtube(?:-nocookie)?\.com/(?:[^/]+/.+/|(?:v|e(?:mbed)?)/|.*[?&]v=)|youtu\.be/)([^"&?/ ]{11})%i', $url, $match);
	
			if (!isset($match[1])) {
				return response()->json(['error' => 'Invalid YouTube URL'], 400);
			}
	
			$video_id = $match[1];
			$video_page_url = "https://www.youtube.com/watch?v={$video_id}";
	
			$ch = curl_init();
			curl_setopt($ch, CURLOPT_URL, $video_page_url);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
			curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0');
			$html = curl_exec($ch);
			curl_close($ch);
	
			if (preg_match('/"lengthSeconds":"(\d+)"/', $html, $matches)) {
				$duration = gmdate("H:i:s", $matches[1]);
			} else {
				return response()->json(['error' => 'Failed to fetch YouTube video details'], 500);
			}
	
			return [
				'provider'     => 'YouTube',
				'video_id'     => $video_id,
				'title'        => '', // لا يمكن جلب العنوان بدون API
				'thumbnail'    => "https://i.ytimg.com/vi/{$video_id}/default.jpg",
				'video'        => "http://www.youtube.com/watch?v={$video_id}",
				'embed_video'  => "http://www.youtube.com/embed/{$video_id}",
				'duration'     => $duration,
			];
		}
	
		if ($host === 'vimeo.com') {
			$video_id = substr(parse_url($url, PHP_URL_PATH), 1);
	
			$options = [
				'http' => [
					'method'  => 'GET',
					'header'  => 'Authorization: Bearer ' . $vimeo_api_key
				]
			];
			$context  = stream_context_create($options);
	
			try {
				$hash = json_decode(file_get_contents("https://api.vimeo.com/videos/{$video_id}", false, $context));
	
				if (!$hash) {
					return response()->json(['error' => 'Failed to fetch Vimeo video details'], 500);
				}
	
				return [
					'provider'     => 'Vimeo',
					'video_id'     => $video_id,
					'title'        => $hash->name ?? '',
					'thumbnail'    => $hash->pictures->sizes[0]->link ?? '',
					'video'        => $hash->link ?? '',
					'embed_video'  => "https://player.vimeo.com/video/{$video_id}",
					'duration'     => gmdate("H:i:s", $hash->duration ?? 0),
				];
			} catch (\Throwable $th) {
				return response()->json(['error' => 'Failed to fetch Vimeo video details'], 500);
			}
		}
	
		return response()->json(['error' => 'Unsupported video provider'], 400);
	}
	


	public function rendered_view($path = "", Request $request)
	{
		$page_data = array();
		foreach ($request->all() as $key => $value) :
			$page_data[$key] = $value;
		endforeach;

		return view($path, $page_data)->render();
	}
}
