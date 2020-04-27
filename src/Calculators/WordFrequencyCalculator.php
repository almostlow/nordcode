<?php

namespace App\Calculators;

use App\Crawlers\WikiCrawler;

class WordFrequencyCalculator
{
    private $wikiCrawler;

    public function __construct(WikiCrawler $wikiCrawler)
    {
        $this->wikiCrawler = $wikiCrawler;
    }

    /**
    * Calculate most frequently workds in feed
    * @param $entries - array
    * @return array
    */
    public function calculate(array $entries):array
    {
        $this->wikiCrawler->loadMostCommonWords();
        $commonWords = $this->wikiCrawler->getCommonWords();
        $words = [];
        foreach ($entries as $entry) {
            foreach ($entry as $attribute => $property) {
                if ($attribute === 'author') {
                    continue;
                }
                $property = strip_tags(trim($property));
                $splited = explode(' ', $property);
                if (!empty($splited)) {
                    foreach ($splited as $spl) {
                        $spl = trim(strtolower($spl));
                        if (in_array($spl, $commonWords)) {
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
        return ['words' => $words, 'excluded' => $commonWords];
    }
}
