<?php

namespace Gosas\Core\Helpers;

use SimpleXMLElement;
use Symfony\Component\Serializer\Encoder\XmlEncoder;

class XmlHelper
{
    public static function ArrayToXml($array, $rootElement = null, $xml = null)
    {
        $_xml = $xml;
        if ($_xml === null) {
            $_xml = new SimpleXMLElement($rootElement !== null ? $rootElement : '<root/>');
        }

        foreach ($array as $k => $v) {
            if (is_array($v)) {
                XmlHelper::ArrayToXml($v, $k, $_xml->addChild($k));
            } else {
                $_xml->addChild($k, $v);
            }
        }

        return $_xml->asXML();
    }

    public static function XMLStringToObject($data)
    {
        $encoder = new XmlEncoder();
        $xml = $encoder->decode($data, 'xml');
        return (object) json_decode(json_encode($xml));
    }
}
