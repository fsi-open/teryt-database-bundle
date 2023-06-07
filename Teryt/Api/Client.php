<?php

/**
 * (c) FSi sp. z o.o <info@fsi.pl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace FSi\Bundle\TerytDatabaseBundle\Teryt\Api;

use DateTime;
use SoapClient;
use SplTempFileObject;

class Client
{
    /**
     * @var string
     */
    private $url;

    /**
     * @var string
     */
    private $username;

    /**
     * @var string
     */
    private $password;

    /**
     * @var SoapClient
     */
    private $soapClient;

    public function __construct(string $url, string $username, string $password)
    {
        $this->url = $url;
        $this->username = $username;
        $this->password = $password;

        $this->initClient();
    }

    public function getTerritorialDivisionData(): SplTempFileObject
    {
        return $this->getFile('PobierzKatalogTERC');
    }

    public function getPlacesData(): SplTempFileObject
    {
        return $this->getFile('PobierzKatalogSIMC');
    }

    public function getStreetsData(): SplTempFileObject
    {
        return $this->getFile('PobierzKatalogULIC');
    }

    public function getPlacesDictionaryData(): SplTempFileObject
    {
        return $this->getFile('PobierzKatalogWMRODZ');
    }

    private function getFile(string $functionName): SplTempFileObject
    {
        $response = $this->makeCall($functionName, [
            'DataStanu' => (new DateTime())->format('Y-m-d')
        ]);

        $resultKey = $functionName . 'Result';

        return $this->prepareTempFile($response->{$resultKey}->plik_zawartosc);
    }

    /**
     * @param array<string, mixed> $args
     * @return mixed
     */
    private function makeCall(string $functionName, array $args)
    {
        return $this->soapClient->__soapCall($functionName, [$args]);
    }

    private function prepareTempFile(string $data): SplTempFileObject
    {
        $tempXml = new SplTempFileObject();
        $tempXml->fwrite(base64_decode($data));

        return $tempXml;
    }

    private function initClient(): void
    {
        $this->soapClient = new TerytSoapClient($this->url, [
            'soap_version' => SOAP_1_1,
            'exceptions' => true,
            'cache_wsdl' => WSDL_CACHE_BOTH,
        ]);

        $this->soapClient->addUserToken($this->username, $this->password);
    }
}
