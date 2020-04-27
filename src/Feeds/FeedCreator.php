<?php

namespace App\Feeds;

use Psr\Log\LoggerInterface;
use App\Calculators\WordFrequencyCalculator;

class FeedCreator
{
    private $url;

    private $logger;

    private $wordFrequencyCalculator;

    public function __construct(String $url, LoggerInterface $logger, WordFrequencyCalculator $wordFrequencyCalculator)
    {
        $this->url = $url;
        $this->logger = $logger;
        $this->wordFrequencyCalculator = $wordFrequencyCalculator;
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
            $formattedOutput = $this->wordFrequencyCalculator->calculate($output['feed']);
            $formattedOutput['feed'] = $output['feed'];
            return $formattedOutput;
        } catch (\Exception $e) {
            $this->logger->error($e->getMessage());
            return ['feed' => [], 'words' => []];
        }
    }
}
