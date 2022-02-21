<?php

namespace app\components\offer;

use yii\base\Component;
use yii\helpers\Html;

/**
 * Component for SVG images in PDF
 */
class SvgImage extends Component
{
    /**
     * {@inheritdoc}
     */
    public static function get($image)
    {
        if(!file_exists($image)) {
            return null;
        }

        $svgData = file_get_contents($image);

        //$svgData =  preg_replace("/<!DOCTYPE[\s\S]+?(dtd\">)/", '', $svgData);
        $svgData =  preg_replace("/<!DOCTYPE[\s\S]+?(]>)/", '', $svgData);
        $svgData = str_replace('xmlns:x="&ns_extend;" xmlns:i="&ns_ai;" xmlns:graph="&ns_graphs;"', '', $svgData);
        $svgData = str_replace('requiredExtensions="&ns_ai;"', '', $svgData);
        $svgData = preg_replace('/<\?xml[\s\S]+?\?>/', '', $svgData);

        return $svgData;
    }
}
