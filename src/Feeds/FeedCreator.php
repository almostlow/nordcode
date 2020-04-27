<?php

namespace App\Feeds;

use Symfony\Component\DomCrawler\Crawler;

class FeedCreator
{
    private $url;

    private $wikiUrl;

    private $commonWords;

    private const COMMON_WORDS_COUNT = 50;

    public function __construct(String $url, String $wikiUrl)
    {
        $this->url = $url;
        $this->wikiUrl = $wikiUrl;
        $this->commonWords = [];
    }

    /**
     * Create feed from thirt party resource
     * @return array
     */
    public function create():array
    {
        try {
            $xml = simplexml_load_string(file_get_contents($this->url));
            $output = [];
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
        } catch (\Exception $e) {
            return ['feed' => [], 'words' => []];
        }
    }

    /**
     * Calculate most frequently workds in feed
     * @param $entries - array
     * @return array
     */
    private function determineMostFrequentWords(array &$entries):void
    {
        $this->loadMostCommonWords();
        $words = [];
        foreach ($entries['feed'] as $entry) {
            foreach ($entry as $attribute) {
                $splited = explode(' ', $attribute);
                if (!empty($splited)) {
                    foreach ($splited as $spl) {
                        if (in_array($spl, $this->commonWords)) {
                            continue;
                        }
                        if (!ctype_alpha($spl)) {
                            continue;
                        }
                        if (!isset($words[$spl])) {
                            $words[$spl] = 0;
                        }
                        $words[$spl]++;
                    }
                }
            }
        }
        uasort($words, fn ($a, $b) => $b - $a);
        $words = array_splice($words, 0, 10);
        $entries['words'] = $words;
    }

    /**
     * Load most common words from wiki
     * @return void
     */
    private function loadMostCommonWords():void
    {
        try {
            $params = [
                "action" => "parse",
                "page" => "Most common words in English",
                "format" => "json",
            ];
            $url = $this->wikiUrl . '?' . http_build_query($params);
            $json = json_decode(file_get_contents($url));
            $html = $json->parse->text;
            $crawler = new Crawler($html->{'*'});
            $crawler->filter('.wikitable a.extiw')->each(function (Crawler $node, $i) {
                if ($i > self::COMMON_WORDS_COUNT) {
                    return;
                }
                $this->commonWords[] = $node->text();
            });
        } catch (\Exception $e) {
            $this->commonWords = $this->fallbackCommonWords();
        }
    }

    /**
     * Fallback common words if wiki page not accessible
     * @return array
     */
    private function fallbackCommonWords():array
    {
        return [
            "the",
            "be",
            "to",
            "of",
            "and",
            "a",
            "in",
            "that",
            "have",
            "I",
            "it",
            "for",
            "not",
            "on",
            "with",
            "he",
            "as",
            "you",
            "do",
            "at",
            "this",
            "but",
            "his",
            "by",
            "from",
            "they",
            "we",
            "say",
            "her",
            "she",
            "or",
            "an",
            "will",
            "my",
            "one",
            "all",
            "would",
            "there",
            "their",
            "what",
            "so",
            "up",
            "out",
            "if",
            "about",
            "who",
            "get",
            "which",
            "go",
            "me",
            "when",
        ];
    }
}
