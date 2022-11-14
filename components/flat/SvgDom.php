<?php

namespace app\components\flat;

/**
 * Svg dom
 */
class SvgDom
{
    public $svgFile;
    public $svgDom;

    public function __construct($filePath) {
        $this->svgFile = $this->loadFromPath($filePath);

        $this->svgDom = new \DOMDocument();
        @$this->svgDom->loadHTML($this->svgFile, LIBXML_NOERROR | LIBXML_HTML_NOIMPLIED | LIBXML_HTML_NODEFDTD);
    }

    /**
     * Load svg file from path
     * @param string $filePath path to svg file
     * @return string $svgContent
     */
    public function loadFromPath($filePath)
    {
        if(!file_exists($filePath)) {
            return null;
        }

        $svgData = file_get_contents($filePath);

        
        $svgData =  preg_replace("/<!DOCTYPE[\s\S]+?(]>)/", '', $svgData);
        //$svgData =  preg_replace('/[а-яА-ЯёЁ]/u', '', $svgData);
        $svgData = str_replace('xmlns:x="&ns_extend;" xmlns:i="&ns_ai;" xmlns:graph="&ns_graphs;"', '', $svgData);
        $svgData = preg_replace('/<\?xml[\s\S]+?\?>/', '', $svgData);

        return $svgData;
    }

    /**
     * Append nodes to svg
     * @param array $nodeList list of nodes
     */
    public function appendNodes($nodeList)
    {
        if(!$this->svgDom) {
            $this->createDom();
        }
        $rootSvg = $this->svgDom->getElementsByTagName('svg')->item(0);
        /*$rootSvg = $rootSvg->item(0);
        $rootSvg->setAttribute("id", "left");*/

        if($rootSvg) {
            foreach($nodeList as $nodeItem) {
                $node = $this->svgDom->createElement($nodeItem['name']);

                if($nodeItem['attributes']) {
                    foreach($nodeItem['attributes'] as $name => $value) {
                        $nodeAttribute = $this->svgDom->createAttribute($name);
                        $nodeAttribute->value = $value;

                        $node->appendChild($nodeAttribute);
                    }
                }

                if(isset($nodeItem['children'])) {
                    foreach($nodeItem['children'] as $child) {
                        $childNode = $this->svgDom->createElement($child['name']);

                        if($child['attributes']) {
                            foreach($child['attributes'] as $name => $value) {
                                $nodeAttribute = $this->svgDom->createAttribute($name);
                                $nodeAttribute->value = $value;

                                $childNode->appendChild($nodeAttribute);
                            }
                        }
                        $node->appendChild($childNode);
                    }
                }

                $rootSvg->appendChild($node);
            }
        }

        $this->svgFile = $this->svgDom->saveHTML();
    }

    /**
     * Append node to svg
     * @param string $nodeName
     * @param $attributes
     */
    public function appendNode($nodeName, $attributes)
    {
        if(!$this->svgDom) {
            $this->createDom();
        }
        $rootSvg = $this->svgDom->getElementsByTagName('svg');
        $rootSvg = $rootSvg->item(0);

        if($rootSvg) {
            $node = $this->svgDom->createElement($nodeName);

            if(is_array($attributes)) {
                foreach($attributes as $name => $value) {
                    $nodeAttribute = $this->svgDom->createAttribute($name);
                    $nodeAttribute->value = $value;

                    $node->appendChild($nodeAttribute);
                }
            }

            $rootSvg->appendChild($node);
        }

        $this->svgFile = $this->svgDom->saveHTML();
    }

    /**
     * Return content of svg file
     * @return $svgContent
     */
    public function getFileContent()
    {
        return $this->svgFile;
    }

    /**
     * Create DonDocument from svg file
     */
    private function createDom()
    {
        $this->svgDom = new \DOMDocument();
        $this->svgDom->loadHTML($this->svgFile, LIBXML_NOERROR | LIBXML_HTML_NOIMPLIED | LIBXML_HTML_NODEFDTD);
    }

    public static function isNameSvg($filePath)
    {
        return strpos($filePath, '.svg') !== false;
    }
}