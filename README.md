# Email obfuscator for Silverstripe

A HTTPMiddleware filter to automatically obfuscate all visible email
addresses in all HTML output via the ContentController by replacing
them with an encoded (switching between ASCII & hexadecimal) version.

## Example

```
me@site.com
```

becomes:

```
&amp;#109;&amp;#x65;&amp;#64;&amp;#x73;&amp;#105;&amp;#x74;&amp;#101;&amp;#x2e;&amp;#99;&amp;#x6f;&amp;#109;
```

## Requirements

-   Silverstripe ^4 || ^5 || ^6

Silverstripe 3 should check out the silverstripe3 branch

## Installation

`composer require axllent/silverstripe-email-obfuscator`

## Usage

The filter automatically encodes any email address outputted through the
ContentController provided it contains the default text/html header.

No configuration required.
