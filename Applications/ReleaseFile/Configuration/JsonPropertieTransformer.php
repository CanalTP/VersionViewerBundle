<?php
namespace Bbr\VersionViewerBundle\Applications\ReleaseFile\Configuration;

/**
 * Transform propertie from configuration to be usable directly in code
 *
 * @author bbonnesoeur
 *
 */
class JsonPropertieTransformer implements PropertieTransformerInterface
{

    /**
     * transform a js notation from configuration to an applicable php access method after a json_decode call on a json feed.
     * basically change '.' to '->'
     *
     * @param array[string] $properties
     *            json expressions from configuration
     *
     * @return array[string] transformed json expression
     */
    public function transform($properties)
    {
        $transformedProp = array();
        foreach ($properties as $propertie => $jsonExpr) {
            $transformedProp[$propertie] = $this->transformPropertie($jsonExpr);
        }

        return $transformedProp;
    }

    /**
     *
     * @param string $jsonExpression
     *
     * @return array[] tokenized path usable as object property
     */
    private function transformPropertie($jsonExpression)
    {
        $properties = explode('.', $jsonExpression);

        $propPathArray = array();

        foreach ($properties as $value) {
            array_push($propPathArray, $value);
        }

        return $propPathArray;
    }
}