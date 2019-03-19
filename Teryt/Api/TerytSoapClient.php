<?php

/**
 * (c) FSi sp. z o.o. <info@fsi.pl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace FSi\Bundle\TerytDatabaseBundle\Teryt\Api;

use DOMDocument;
use RobRichards\WsePhp\WSSESoap;
use SoapClient;

class TerytSoapClient extends SoapClient
{
    /**
     * @var string
     */
    private $username;

    /**
     * @var string
     */
    private $password;

    /**
     * @var bool
     */
    private $digest;

    public function addUserToken(string $username, string $password, bool $digest = false): void
    {
        $this->username = $username;
        $this->password = $password;
        $this->digest = $digest;
    }

    public function __doRequest($request, $location, $action, $version, $one_way = 0): string
    {
        $doc = new DOMDocument('1.0');
        $doc->loadXML($request);

        $wsa = new WSASoap($doc);
        $wsa->addAction($action);

        $doc = $wsa->getDoc();

        $wsse = new WSSESoap($doc, false);
        $wsse->signAllHeaders = false;
        $wsse->addUserToken($this->username, $this->password, $this->digest);

        $request = $wsse->saveXML();

        return parent::__doRequest($request, $location, $action, $version, $one_way);
    }
}
