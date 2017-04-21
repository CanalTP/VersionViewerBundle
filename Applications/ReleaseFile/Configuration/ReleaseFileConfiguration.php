<?php
namespace Bbr\VersionViewerBundle\Applications\ReleaseFile\Configuration;

/**
 * configuration of a release file.
 * used to store configuration about properties to get
 * from file and global comparison value
 *
 * @author bbonnesoeur
 *        
 */
class ReleaseFileConfiguration
{

    /**
     *
     * @var array[string] properties to parse in release file
     */
    public $filteredProperties = array();

    /**
     *
     * @var string $comparisonValue propertie to base comparison between instance
     */
    public $comparisonValue = null;

    /**
     *
     * @var PropertieTransformerInterface propertie configuration transformer
     */
    public $propertieTransformer = null;

    /**
     *
     * @return string comparison value
     *        
     */
    public function getComparisonValue()
    {
        return $this->comparisonValue;
    }

    /**
     *
     * @return array[string] properties to filter
     */
    public function getFilteredProperties()
    {
        return $this->filteredProperties;
    }

    /**
     *
     * @return array[string] properties to filter mutted to be used directly on a json decode feed.
     */
    public function getTransformedFilteredProperties()
    {
        if ($this->propertieTransformer != null) {
            return $this->propertieTransformer->transform($this->getFilteredProperties());
        }
        return $this->getFilteredProperties();
    }

    /**
     *
     * @param
     *            array[string] $filteredProperties
     */
    public function setFilteredProperties(array $filteredProperties)
    {
        $this->filteredProperties = $filteredProperties;
        return $this;
    }

    /**
     * merge actual filtered properties with another configuration.
     * Typically will merge application type configuraiton with application override.
     *
     * @param array[string] $filteredProperties            
     */
    public function mergeFilteredProperties(array $filteredProperties)
    {
        $this->filteredProperties = array_merge($this->filteredProperties, $filteredProperties);
        return $this;
    }

    /**
     * remove properties from the filteredProperties list acording list in parameter.
     *
     * @param array[string] $propertiesToRemove
     *            array containing value of Properties to remove.
     *            
     *            Array
     *               (
     *                   [0] => 'propertie-one'
     *                   [1] => 'propertie-two
     *               )
     */
    public function removeFilteredProperties(array $propertiesToRemove)
    {
        foreach ($propertiesToRemove  as $value) {
            unset($this->filteredProperties[$value]);
        }
        
    }

    /**
     *
     * @param string $comparisonValue            
     */
    public function setComparisonValue($comparisonValue)
    {
        $this->comparisonValue = $comparisonValue;
        return $this;
    }

    /**
     *
     * @return the PropertieTransformerInterface
     */
    public function getPropertieTransformer()
    {
        return $this->propertieTransformer;
    }

    /**
     *
     * @param PropertieTransformerInterface $propertieTransformer            
     */
    public function setPropertieTransformer(PropertieTransformerInterface $propertieTransformer)
    {
        $this->propertieTransformer = $propertieTransformer;
        return $this;
    }
}