<?php

namespace Axllent\EmailObfuscator\Control\Middleware;

use SilverStripe\Control\HTTPRequest;
use SilverStripe\Control\HTTPResponse;
use SilverStripe\Control\Middleware\HTTPMiddleware;

/**
 * SilverStripe Email Obfuscator
 * =============================
 *
 * Middleware filter to automatically encode all email
 * addresses (including mailto: links) in outputted HTML.
 * Switches between ASCII & hexadecimal encoding.
 *
 * Usage: Simply extract to your SilverStripe website path
 * and run a ?flush=1
 *
 * License: MIT-style license http://opensource.org/licenses/MIT
 * Authors: Techno Joy development team (www.technojoy.co.nz)
 */
class EmailObfuscator implements HTTPMiddleware
{
    /**
     * Filter executed AFTER a request
     * Run output through ObfuscateEmails filter
     * encoding emails in the $response
     *
     * @param HTTPRequest  $request  HTTP request
     * @param HTTPResponse $response HTTP response
     *
     * @return HTTPResponse
     */
    public function process(HTTPRequest $request, callable $delegate)
    {
        $response = $delegate($request);

        if ($response
            && preg_match('/text\/html/', $response->getHeader('Content-Type'))
            && !preg_match('/^(admin|dev)\//', $request->getURL())
        ) {
            $response->setBody(
                $this->obfuscateEmails($response->getBody())
            );
        }

        return $response;
    }

    /**
     * Obfuscate all matching emails
     *
     * @param string $html HTML string
     *
     * @return string
     */
    public function obfuscateEmails($html)
    {
        $reg = '/[:_a-z0-9-+]+(\.[_a-z0-9-+]+)*@[a-z0-9-]+(\.[a-z0-9-]+)*(\.[a-z]{2,})/i';
        if (!is_null($html) && preg_match_all($reg, $html, $matches)) {
            $searchstring = $matches[0];
            for ($i = 0; $i < count($searchstring); ++$i) {
                $html = preg_replace(
                    '/' . $searchstring[$i] . '/',
                    $this->encode($searchstring[$i]),
                    $html
                );
            }
        }

        return $html;
    }

    /**
     * Obscure email address.
     *
     * @param $originalString string The email address
     *
     * @return string The encoded (ASCII & hexadecimal) email address
     */
    protected function encode($originalString)
    {
        $encodedString  = '';
        $nowCodeString  = '';
        $originalLength = strlen($originalString);
        for ($i = 0; $i < $originalLength; ++$i) {
            $encodeMode = (0 == $i % 2) ? 1 : 2; // Switch encoding odd/even

            switch ($encodeMode) {
                case 1: // Decimal code
                    $nowCodeString = '&#' . ord($originalString[$i]) . ';';

                    break;

                case 2: // Hexadecimal code
                    $nowCodeString = '&#x' . dechex(ord($originalString[$i])) . ';';

                    break;

                default:
                    return 'ERROR: wrong encoding mode.';
            }
            $encodedString .= $nowCodeString;
        }

        return $encodedString;
    }
}
