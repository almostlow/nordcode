<?php

namespace App\Crawlers;

use Symfony\Component\DomCrawler\Crawler;
use Psr\Log\LoggerInterface;

class WikiCrawler
{
    private $wikiUrl;

    private $logger;

    private $commonWords;

    private const COMMON_WORDS_COUNT = 50;

    public function __construct(String $wikiUrl, LoggerInterface $logger)
    {
        $this->wikiUrl = $wikiUrl;
        $this->commonWords = [];
        $this->logger = $logger;
    }

    /**
     * Load most common words from wiki
     * @return void
     */
    public function loadMostCommonWords():void
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
            if (empty($this->commonWords)) {
                $this->commonWords = $this->fallbackCommonWords();
            }
        } catch (\Exception $e) {
            $this->logger->error($e->getMessage());
            $this->commonWords = $this->fallbackCommonWords();
        }
    }

    /**
     * @return common words array
     */
    public function getCommonWords():array
    {
        return $this->commonWords;
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
