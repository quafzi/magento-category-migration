<?php
require_once 'abstract.php';

/**
 * Magento Category Export Shell Script
 *
 * @category    Mage
 * @package     Mage_Shell
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_Shell_ExportCategories extends Mage_Shell_Abstract
{
    protected $connection;

    /**
     * Run script
     *
     */
    public function run()
    {
        $rootCategoryIds = array();
        if ($this->getArg('roots')) {
            // e.g.  "2,148,147,150,146,590,235,1007,1032,867,1056"
            $rootCategoryIds = explode(',', $this->getArg('roots'));
        }
        if (false === count($rootCategoryIds)) {
            die ('Please provide at least one root category id. (E.g. --roots 1,3,595)');
        }

        if ($this->getArg('file')) {
            $path = $this->getArg('file');
            echo 'writing data to ' . $path . PHP_EOL;
            $file = fopen($path, 'w');
            foreach ($rootCategoryIds as $categoryId) {
                $category = Mage::getModel('catalog/category')->load($categoryId);
                echo '* ' . $category->getName() . sprintf(' (%s products)', $category->getProductCount()) . PHP_EOL;
                $this->exportData($category, $file);
            }
            fclose($file);
        } else {
            echo $this->usageHelp();
        }
    }

    protected function exportData(Mage_Catalog_Model_Category $category, $file, $depth=0)
    {
        $data = array(
            'id'               => $category->getId(),
            'parent_id'        => $category->getParentId(),
            'attribute_set_id' => $category->getAttributeSetId(),
            'urlPath'          => $category->getUrlPath(),
            'urlKey'           => $category->getUrlKey(),
            'path'             => $category->getPath(),
            'position'         => $category->getPosition(),
            'page_layout'      => $category->getPageLayout(),
            'description'      => $category->getDescription(),
            'display_mode'     => $category->getDisplayMode(),
            'is_active'        => $category->getIsActive(),
            'is_anchor'        => $category->getIsAnchor(),
            'include_in_menu'  => $category->getIncludeInMenu(),
            'custom_design'    => $category->getCustomDesign(),
            'level'            => $category->getLevel(),
            'name'             => $category->getName(),
            'metaTitle'        => $category->getMetaTitle(),
            'metaKeywords'     => $category->getMetaKeywords(),
            'metaDescription'  => $category->getMetaDescription(),
        );
        echo str_repeat('  ', $depth);
        echo '* ' . $category->getName() . sprintf(' (%s products)', $category->getProductCount()) . PHP_EOL;
        fputcsv($file, $data);
        if ($category->hasChildren()) {
            $children = Mage::getModel('catalog/category')->getCategories($category->getId());
            foreach ($children as $child) {
                $child = Mage::getModel('catalog/category')->load($child->getId());
                $this->exportData($child, $file, $depth+1);
            }
        }
    }

    /**
     * Retrieve Usage Help Message
     *
     */
    public function usageHelp()
    {
        return <<<USAGE
Usage:  php -f exportCategories.php -- --file <csv_file> --roots <root_category_ids (separated by comma)>

  help                        This help

USAGE;
    }
}

$shell = new Mage_Shell_ExportCategories();
$shell->run();
