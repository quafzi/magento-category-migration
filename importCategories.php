<?php
require_once 'abstract.php';

/**
 * Magento Category Import Shell Script
 *
 * @category    Mage
 * @package     Mage_Shell
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_Shell_ImportCategories extends Mage_Shell_Abstract
{
    protected $_attributeSetId;

    /**
     * Run script
     *
     */
    public function run()
    {
        if ($this->getArg('file')) {
            $path = $this->getArg('file');
            echo 'writing data to ' . $path . PHP_EOL;
            $file = fopen($path, 'r');
            if (false !== ($file = fopen($path, 'r'))) {
                while (false !== ($data = fgetcsv($file, 10000, ','))) {
                    $this->createCategory($data);
                }
                fclose($file);
            }
        } else {
            $this->usageHelp();
        }
    }

    protected function _getAttributeSetId()
    {
        if (is_null($this->_attributeSetId)) {
            $this->_attributeSetId = Mage::getModel('eav/entity_type')->load('catalog/category', 'entity_model')->getDefaultAttributeSetId();
        }
        return $this->_attributeSetId;
    }

    protected function createCategory($data)
    {
        echo str_repeat('  ', $data[14]);
        echo '* ' . $data[15] . ': ';
        $category = Mage::getModel('catalog/category');
        $category->setId($data[0]);
        $category->setParentId($data[1]);
        $category->setAttributeSetId($this->_getAttributeSetId());
        $category->setUrlPath($data[3]);
        $category->setUrlKey($data[4]);
        $category->setPath($data[5]);
        $category->setPosition($data[6]);
        $category->setPageLayout($data[7]);
        $category->setDescription($data[8]);
        $category->setDisplayMode($data[9]);
        $category->setIsActive($data[10]);
        $category->setIsAnchor($data[11]);
        $category->setIncludeInMenu($data[12]);
        $category->setCustomDesign($data[13]);
        $category->setLevel($data[14]);
        $category->setName($data[15]);
        $category->setMetaTitle($data[16]);
        $category->setMetaKeywords($data[17]);
        $category->setMetaDescription($data[18]);
        $category->save();
        $category = Mage::getModel('catalog/category')->load($data[0]);
        echo $category->getId() ? $category->getId() : '⚡';
        echo PHP_EOL;
    }

    /**
     * Retrieve Usage Help Message
     *
     */
    public function usageHelp()
    {
        return <<<USAGE
Usage:  php -f importCategories.php -- --file <csv_file>

  help                        This help

USAGE;
    }
}

$shell = new Mage_Shell_ImportCategories();
$shell->run();
