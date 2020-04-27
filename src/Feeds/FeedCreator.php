<?php

namespace App\Feeds;

class FeedCreator
{
	private $url;

	public function __construct(String $url) {
	    $this->url = $url;
	}

	/**
	 * Create feed from thirt party resource
	 * @return array
	 */

	public function create():array
	{
        $xml = simplexml_load_string(file_get_contents($this->url));
        $output = [];
        dump($xml);
        foreach ($xml->entry as $entry) {
        	$modifiedEntry = [];
        	$modifiedEntry['updated'] = date('Y-m-d H:i:s', strtotime($entry->updated));
        	$modifiedEntry['author'] = $entry->author->name;
        	$modifiedEntry['title'] = $entry->title;
        	$modifiedEntry['summary'] = $entry->summary;
        	$modifiedEntry['link'] = $entry->link['href'];
        	$output[] = $modifiedEntry;
        }
        return $output;
	}
}