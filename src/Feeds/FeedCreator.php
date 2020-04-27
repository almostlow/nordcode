<?php

namespace App\Feeds;

use Psr\Log\LoggerInterface;
use App\Calculators\WordFrequencyCalculator;

class FeedCreator
{
    private $url;

    private $logger;

    private $wordFrequencyCalculator;

    private $cacheProvider;

    public function __construct(String $url, LoggerInterface $logger, WordFrequencyCalculator $wordFrequencyCalculator, FeedCacheProvider $cacheProvider)
    {
        $this->url = $url;
        $this->logger = $logger;
        $this->wordFrequencyCalculator = $wordFrequencyCalculator;
        $this->cacheProvider = $cacheProvider;
    }

    /**
     * Create feed from thirt party resource
     * @return array
     */
    public function create():array
    {
        try {
            $cacheProvider = $this->cacheProvider;
            $cacheProvider->loadData();
            if ($cacheProvider->isSuccessfull()) {
                $data = $cacheProvider->getData();
                return $data;
            }
            $xml = simplexml_load_string(file_get_contents($this->url));
            $output = [];
            foreach ($xml->entry as $entry) {
                $modifiedEntry = [];
                $modifiedEntry['updated'] = date('Y-m-d H:i:s', strtotime($entry->updated));
                $modifiedEntry['author'] = (string)$entry->author->name;
                $modifiedEntry['title'] = (string)$entry->title;
                $modifiedEntry['summary'] = (string)$entry->summary;
                $modifiedEntry['link'] = (string)$entry->link['href'];
                $output['feed'][] = $modifiedEntry;
            }
            $formattedOutput = $this->wordFrequencyCalculator->calculate($output['feed']);
            $formattedOutput['feed'] = $output['feed'];
            $setCache = true;
            foreach ($formattedOutput as $out) {
                if (empty($out)) {
                    $setCache = false;
                }
            }
            if ($setCache) {
                $this->cacheProvider->set($formattedOutput);
            }
            return $formattedOutput;
        } catch (\Exception $e) {
            $this->logger->error($e->getMessage());
            return ['feed' => [], 'words' => [], 'excluded' => []];
        }
    }
}
