<?php

    $xmlElement = '<notita>
                    <catre>Alex\'</catre>
                    <mesaj>Te "Iubesc!</mesaj>
                    <dela><m>Ioana</m><m>Florin</m><m><t>Fla</t></m></dela>
                </notita>';

    $xml = simplexml_load_string($xmlElement) or die("Error: Invalid XML found!");

    function parseXmlToJson(SimpleXMLElement $xml, string $path) {
        $out = '';
        $matchingElements = $xml->xpath($path);
        if ($matchingElements != FALSE) {
            $matchingElementsCount = count($matchingElements);
            if ($matchingElementsCount > 1) {
                $out .= '"'.$matchingElements[0]->getName().'":[';
                    foreach ($matchingElements as $element) {
                        $matchingElementsCount--;
                        $childrenCount = $element->count();
                        if ($childrenCount>0) {
                            $out .= '{';
                            $childrenNames = [];
                            foreach ( $element->children() as $child) {
                                $childrenNames[$child->getName()] = $childrenNames[$child->getName()] ?? 0;
                                $childrenNames[$child->getName()]++;
                            }

                            end($childrenNames);
                            $lastChildName = key($childrenNames);
                            foreach ( $element->children() as $child) {
                                $childrenCount--;

                                if (isset($childrenNames[$child->getName()])) {
                                    $out .= parseXmlToJson($xml, $path.'/'.$child->getName());
                                }
                                if ($childrenCount != 0 && $lastChildName != $child->getName()) {
                                    $out .= ',';
                                }

                                unset($childrenNames[$child->getName()]);
                            }
                            $out .= '}';
                        }
                        else {
                            $out .= '"'.escapeString($element).'"';
                        }

                        if ($matchingElementsCount !=0) {
                            $out .= ',';
                        }
                    }
                $out .= ']';
            }
            else {
                $element = $matchingElements[0];
                $out .= '"'.$element->getName().'":';
                $childrenCount = $element->count();
                if ($childrenCount>0) {
                    $out .= '{';
                        $childrenNames = [];
                        foreach ( $element->children() as $child) {
                            $childrenNames[$child->getName()] = $childrenNames[$child->getName()] ?? 0;
                            $childrenNames[$child->getName()]++;
                        }

                        end($childrenNames);
                        $lastChildName = key($childrenNames);
                        foreach ( $element->children() as $child) {
                            $childrenCount--;

                            if (isset($childrenNames[$child->getName()])) {
                                $out .= parseXmlToJson($xml, $path.'/'.$child->getName());
                            }
                            if ($childrenCount != 0 && $lastChildName != $child->getName()) {
                                $out .= ',';
                            }

                            unset($childrenNames[$child->getName()]);
                        }
                    $out .= '}';
                }
                else {
                    $out .= '"'.escapeString($element).'"';
                }

            }

        }

        return $out;
    }

    function escapeString (string $string) {
        return  preg_replace("/[^,\\\\]\"/","\\\"",$string); ;
    }

    echo '{'.parseXmlToJson($xml, '/*').'}';


?>