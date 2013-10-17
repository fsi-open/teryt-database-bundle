<?php

/**
 * (c) FSi sp. z o.o <info@fsi.pl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace FSi\Bundle\TerytDatabaseBundle\Teryt;

use FSi\Bundle\TerytDatabaseBundle\Exception\DownloadPageException;
use Guzzle\Http\Client;
use Symfony\Component\DomCrawler\Crawler;

class DownloadPageParser
{
    /**
     * @var \Symfony\Component\DomCrawler\Crawler
     */
    protected $crawler;

    /**
     * @var \Guzzle\Http\Client
     */
    private $client;

    /**
     * @param Client $client
     */
    public function __construct(Client $client)
    {
        $this->client = $client;
    }

    /**
     * @param $terytDownloadPageUrl
     * @return string
     */
    public function getStreetsFileUrl($terytDownloadPageUrl)
    {
        return $this->getFileDownloadUrl($terytDownloadPageUrl, 'table#row tbody tr:first-child td:last-child > a');
    }

    /**
     * @param $terytDownloadPageUrl
     * @return string
     */
    public function getPlacesFileUrl($terytDownloadPageUrl)
    {
        return $this->getFileDownloadUrl($terytDownloadPageUrl, 'table#row tbody tr:nth-child(2) td:last-child > a');
    }

    /**
     * @param $terytDownloadPageUrl
     * @return string
     */
    public function getPlacesDictionaryFileUrl($terytDownloadPageUrl)
    {
        return $this->getFileDownloadUrl($terytDownloadPageUrl, 'table#row tbody tr:nth-child(3) td:last-child > a');
    }

    /**
     * @param $terytDownloadPageUrl
     * @return string
     */
    public function getTerritorialDivisionFileUrl($terytDownloadPageUrl)
    {
        return $this->getFileDownloadUrl($terytDownloadPageUrl, 'table#row tbody tr:nth-child(4) td:last-child > a');
    }

    /**
     * @param $terytDownloadPageUrl
     * @return int
     */
    public function getStreetsFileRoundedSize($terytDownloadPageUrl)
    {
        return $this->getFileSize($terytDownloadPageUrl, 'table#row tbody tr:first-child td:nth-child(5)');
    }

    /**
     * @param $terytDownloadPageUrl
     * @return int
     */
    public function getPlacesFileRoundedSize($terytDownloadPageUrl)
    {
        return $this->getFileSize($terytDownloadPageUrl, 'table#row tbody tr:nth-child(2) td:nth-child(5)');
    }

    /**
     * @param $terytDownloadPageUrl
     * @return int
     */
    public function getPlacesDictionaryFileRoundedSize($terytDownloadPageUrl)
    {
        return $this->getFileSize($terytDownloadPageUrl, 'table#row tbody tr:nth-child(3) td:nth-child(5)');
    }

    /**
     * @param $terytDownloadPageUrl
     * @return int
     */
    public function getTerritorialDivisionFileRoundedSize($terytDownloadPageUrl)
    {
        return $this->getFileSize($terytDownloadPageUrl, 'table#row tbody tr:nth-child(4) td:nth-child(5)');
    }

    /**
     * @param $terytDownloadPageUrl
     * @param $selector
     * @return string
     */
    protected function getFileDownloadUrl($terytDownloadPageUrl, $selector)
    {
        $urlHtmlNode = $this->getUrlHtmlNode($terytDownloadPageUrl, $selector);

        return $this->formatDownloadFileUrl($terytDownloadPageUrl, $urlHtmlNode);
    }

    /**
     * @param $terytDownloadPageUrl
     * @param $selector
     * @return Crawler
     */
    protected function getUrlHtmlNode($terytDownloadPageUrl, $selector)
    {
        $crawler = $this->createDownloadPageCrawler($terytDownloadPageUrl);
        $urlHtmlNode = $crawler->filter($selector);

        return $urlHtmlNode;
    }

    /**
     * @param $terytDownloadPageUrl
     * @param $urlHtmlNode
     * @throws \FSi\Bundle\TerytDatabaseBundle\Exception\DownloadPageException
     * @return string
     */
    protected function formatDownloadFileUrl($terytDownloadPageUrl, $urlHtmlNode)
    {
        $dir = dirname($terytDownloadPageUrl);

        try {
            $href = $urlHtmlNode->attr('href');
        } catch (\Exception $e) {
            throw new DownloadPageException(sprintf("Cant parse \"%s\" page html", $terytDownloadPageUrl));
        }

        return sprintf("%s/%s", $dir, $href);
    }

    /**
     * @param $terytDownloadPageUrl
     * @throws \FSi\Bundle\TerytDatabaseBundle\Exception\DownloadPageException
     * @return Crawler
     */
    protected function createDownloadPageCrawler($terytDownloadPageUrl)
    {
        if (isset($this->crawler)) {
            return $this->crawler;
        }

        $response = $this->client->get($terytDownloadPageUrl, null, array(
            'timeout' => 5,
            'connect_timeout' => 5
        ))->send();

        if ($response->isError()) {
            throw new DownloadPageException(sprintf("Teryt download page \"%s\" is not available", $terytDownloadPageUrl));
        }

        $content = $response->getBody(true);

        $this->crawler = new Crawler($content);

        return $this->crawler;
    }

    /**
     * @param $sizeString
     * @return int
     */
    private function formatSize($sizeString)
    {
        $sizeString = iconv('UTF-8', 'ASCII//TRANSLIT', $sizeString);
        $sizeString = preg_replace('/\s+/', '', $sizeString);

        return (int) $sizeString;
    }

    /**
     * @param $terytDownloadPageUrl
     * @param $selector
     * @return int
     */
    public function getFileSize($terytDownloadPageUrl, $selector)
    {
        $crawler = $this->createDownloadPageCrawler($terytDownloadPageUrl);
        $sizeNode = $crawler->filter($selector);
        $sizeString = $sizeNode->html();
        $size = $this->formatSize($sizeString) * 1024;
        return $size;
    }
}
