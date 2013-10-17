<?php

namespace spec\FSi\Bundle\TerytDatabaseBundle\Teryt;

use FSi\Bundle\TerytDatabaseBundle\Exception\DownloadPageException;
use Guzzle\Http\Client;
use Guzzle\Http\Message\Request;
use Guzzle\Http\Message\Response;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class DownloadPageParserSpec extends ObjectBehavior
{
    protected $downloadPageUrl;

    function let(Client $client, Request $request, Response $response)
    {
        $this->downloadPageUrl = 'http://www.stat.gov.pl/broker/access/prefile/listPreFiles.jspa';
        $downloadPageContentFile = __DIR__ . '/../../../../../Behat/Fixtures/TerytPage/listTerytFiles.html';

        $this->beConstructedWith($client);
        $client->get($this->downloadPageUrl, null, array(
            'timeout' => 5,
            'connect_timeout' => 5
        ))->willReturn($request);
        $request->send()->willReturn($response);
        $response->getBody(true)->willReturn(file_get_contents($downloadPageContentFile));
        $response->isError()->willReturn(false);
    }

    function it_returns_streets_file_url_from_teryt_download_page()
    {
        $fileUrl = dirname($this->downloadPageUrl) . '/downloadPreFile.jspa?id=805';

        $this->getStreetsFileUrl($this->downloadPageUrl)->shouldReturn($fileUrl);
    }

    function it_returns_streets_file_size_from_teryt_download_page()
    {
        $this->getStreetsFileRoundedSize($this->downloadPageUrl)->shouldReturn(4365312);
    }

    function it_returns_places_file_url_from_teryt_download_page()
    {
        $fileUrl = dirname($this->downloadPageUrl) . '/downloadPreFile.jspa?id=649';

        $this->getPlacesFileUrl($this->downloadPageUrl)->shouldReturn($fileUrl);
    }

    function it_returns_places_file_size_from_teryt_download_page()
    {
        $this->getPlacesFileRoundedSize($this->downloadPageUrl)->shouldReturn(2520064);
    }

    function it_returns_places_dictionary_file_url_from_teryt_download_page()
    {
        $fileUrl = dirname($this->downloadPageUrl) . '/downloadPreFile.jspa?id=648';

        $this->getPlacesDictionaryFileUrl($this->downloadPageUrl)->shouldReturn($fileUrl);
    }

    function it_returns_places_dictionary_file_size_from_teryt_download_page()
    {
        $this->getPlacesDictionaryFileRoundedSize($this->downloadPageUrl)->shouldReturn(0);
    }

    function it_returns_territorial_division_file_url_from_teryt_download_page()
    {
        $fileUrl = dirname($this->downloadPageUrl) . '/downloadPreFile.jspa?id=594';

        $this->getTerritorialDivisionFileUrl($this->downloadPageUrl)->shouldReturn($fileUrl);
    }

    function it_returns_territorial_division_file_size_from_teryt_download_page()
    {
        $this->getTerritorialDivisionFileRoundedSize($this->downloadPageUrl)->shouldReturn(45056);
    }

    function it_throws_an_exception_when_download_page_is_not_avaialble(Client $client, Request $request)
    {
        $url = 'http://this_is_not_valid_url';

        $client->get($url, null, Argument::type('array'))->willReturn($request);
        $request->send()->willReturn(new Response(404));

        $exception = new DownloadPageException("Teryt download page \"http://this_is_not_valid_url\" is not available");

        $this->shouldThrow($exception)->during('getStreetsFileUrl', array($url));
        $this->shouldThrow($exception)->during('getPlacesDictionaryFileUrl', array($url));
        $this->shouldThrow($exception)->during('getPlacesFileUrl', array($url));
        $this->shouldThrow($exception)->during('getTerritorialDivisionFileUrl', array($url));
    }

    function it_throws_an_exception_when_page_content_prevents_finding_file_url(Client $client, Request $request, Response $response)
    {
        $teryt404PageUrl = __DIR__ . '/../../../../../Behat/Fixtures/TerytPage/404page.html';

        $client->get($teryt404PageUrl, null, Argument::type('array'))->willReturn($request);
        $request->send()->willReturn($response);
        $response->getBody(true)->willReturn(file_get_contents($teryt404PageUrl));

        $exception = new DownloadPageException(sprintf("Cant parse \"%s\" page html", $teryt404PageUrl));

        $this->shouldThrow($exception)->during('getStreetsFileUrl', array($teryt404PageUrl));
        $this->shouldThrow($exception)->during('getPlacesDictionaryFileUrl', array($teryt404PageUrl));
        $this->shouldThrow($exception)->during('getPlacesFileUrl', array($teryt404PageUrl));
        $this->shouldThrow($exception)->during('getTerritorialDivisionFileUrl', array($teryt404PageUrl));
    }
}
