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
        $output = ['feed'];
        foreach ($xml->entry as $entry) {
        	$modifiedEntry = [];
        	$modifiedEntry['updated'] = date('Y-m-d H:i:s', strtotime($entry->updated));
        	$modifiedEntry['author'] = $entry->author->name;
        	$modifiedEntry['title'] = $entry->title;
        	$modifiedEntry['summary'] = $entry->summary;
        	$modifiedEntry['link'] = $entry->link['href'];
        	$output['feed'][] = $modifiedEntry;
        }
        $this->determineMostFrequentWords($output);
        return $output;
	}

	/**
	 * Calculate most frequently workds in feed
	 * @param $entries - array
	 * @return array
     */
	private function determineMostFrequentWords(array &$entries):void
	{
		$words = [];
        foreach ($entries['feed'] as $entry) {
        	foreach ($entry as $attribute) {
                $splited = explode(' ', $attribute);
                if (!empty($splited)) {
	                foreach ($splited as $spl) {
	                	if (!ctype_alpha($spl)) continue;
	                    if (!isset($words[$spl])) {
                            $words[$spl] = 0;
	                    }
	                    $words[$spl]++;
	                }
                }
        	}
        }
        uasort($words, fn($a, $b) => $b - $a);
        $words = array_splice($words, 0, 10);
        $entries['words'] = $words;
	}
}