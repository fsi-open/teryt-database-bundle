<?php

/**
 * (c) FSi sp. z o.o. <info@fsi.pl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace FSi\Bundle\TerytDatabaseBundle\Teryt\Api;

use Assert\Assertion;
use DOMDocument;
use DOMElement;
use DOMNode;
use DOMNodeList;
use DOMXPath;

/**
 * https://github.com/robrichards/wse-php/issues/31
 */
class WSASoap
{
    private const WSANS = 'http://www.w3.org/2005/08/addressing';
    private const WSAPFX = 'wsa';

    /**
     * @var string
     */
    private $soapNS;
    /**
     * @var string
     */
    private $soapPFX;
    /**
     * @var DOMDocument
     */
    private $soapDoc;
    /**
     * @var DOMElement
     */
    private $envelope;
    /**
     * @var DOMXPath
     */
    private $SOAPXPath;
    /**
     * @var DOMNode|null
     */
    private $header;

    public function __construct(DOMDocument $doc)
    {
        $this->soapDoc = $doc;
        $this->envelope = $doc->documentElement;
        $this->soapNS = $this->envelope->namespaceURI;
        $this->soapPFX = $this->envelope->prefix;
        $this->SOAPXPath = new DOMXPath($doc);
        $this->SOAPXPath->registerNamespace('wssoap', $this->soapNS);
        $this->SOAPXPath->registerNamespace('wswsa', self::WSANS);

        $this->envelope->setAttributeNS('http://www.w3.org/2000/xmlns/', 'xmlns:' . self::WSAPFX, self::WSANS);
        $this->locateHeader();
    }

    public function addAction(string $action): void
    {
        /* Add the WSA Action */
        $header = $this->locateHeader();

        $nodeAction = $this->soapDoc->createElementNS(self::WSANS, self::WSAPFX . ':Action', $action);
        $header->appendChild($nodeAction);
    }

    public function getDoc(): ?DOMDocument
    {
        return $this->soapDoc;
    }

    private function locateHeader(): DOMNode
    {
        if ($this->header === null) {
            $headers = $this->SOAPXPath->query('//wssoap:Envelope/wssoap:Header');
            Assertion::isInstanceOf($headers, DOMNodeList::class);
            $header = $headers->item(0);
            if (!$header) {
                $header = $this->soapDoc->createElementNS($this->soapNS, $this->soapPFX . ':Header');
                $this->envelope->insertBefore($header, $this->envelope->firstChild);
            }
            $this->header = $header;
        }

        return $this->header;
    }
}
