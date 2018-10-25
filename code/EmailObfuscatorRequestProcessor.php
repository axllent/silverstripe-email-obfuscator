<?php
/**
 * SilverStripe Email Obfuscator
 * =============================
 *
 * RequestProcessor filter to automatically encode all email
 * addresses (including mailto: links) in outputted HTML.
 * Switches between ASCII & hexadecimal encoding.
 *
 * Usage: Simply extract to your SilverStripe website path
 * and run a ?flush=1
 *
 * License: MIT-style license http://opensource.org/licenses/MIT
 * Authors: Techno Joy development team (www.technojoy.co.nz)
 */

class EmailObfuscatorRequestProcessor implements RequestFilter
{
    /**
     * Filter executed AFTER a request
     * Run output through ObfuscateEmails filter
     * encoding emails in the $response
     */
    public function postRequest(SS_HTTPRequest $request, SS_HTTPResponse $response, DataModel $model)
    {
        $base = preg_quote(Director::baseURL(), '/');
        if (preg_match('/text\/html/', $response->getHeader('Content-Type')) &&
            !preg_match('/^' . $base . '(admin|dev)\//', $request->getVar('url'))
        ) {
            $response->setBody(
                $this->obfuscateEmails($response->getBody())
            );
        }
    }

    /*
     * Obfuscate all matching emails
     * @param string
     * @return string
     */
    public function obfuscateEmails($html)
    {
        $reg = '/[:_a-z0-9-+]+(\.[_a-z0-9-+]+)*@[a-z0-9-]+(\.[a-z0-9-]+)*(\.[a-z]{2,4})/i';
        if (preg_match_all($reg, $html, $matches)) {
            $searchstring = $matches[0];
            for ($i=0; $i < count($searchstring); $i++) {
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
     * @param string The email address
     * @return string The encoded (ASCII & hexadecimal) email address
     */
    protected function encode($originalString)
    {
        $encodedString = '';
        $nowCodeString = '';
        $originalLength = strlen($originalString);
        for ($i = 0; $i < $originalLength; $i++) {
            $encodeMode = ($i % 2 == 0) ? 1 : 2; // Switch encoding odd/even
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

    public function preRequest(SS_HTTPRequest $request, Session $session, DataModel $model)
    {
    }
}
