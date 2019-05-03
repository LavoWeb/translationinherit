<?php

namespace LavoWeb\TranslationInherit\Framework;

use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\App\ObjectManager;

class Translate extends \Magento\Framework\Translate
{
    const PARENT_LOCALE_PATH = 'general/locale/parent_code';

    /**
     * Initialize translation data
     * @override If parent locale is set in configuration, we load that translations first
     *
     * @param string|null $area
     * @param bool $forceReload
     * @return $this
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function loadData($area = null, $forceReload = false)
    {
        $this->_data = [];
        if ($area === null) {
            $area = $this->_appState->getAreaCode();
        }
        $this->setConfig(
            [
                self::CONFIG_AREA_KEY => $area,
            ]
        );

        if (!$forceReload) {
            $data = $this->_loadCache();
            if (false !== $data) {
                $this->_data = $data;
                return $this;
            }
        }

        // Begin override
        $parentLocale = $this->getParentLocale();
        $currentLocale = $this->getLocale();
        if ($parentLocale != '' && $parentLocale != $currentLocale) {
            $this->setLocale($parentLocale);

            $this->_loadModuleTranslation();
            $this->_loadPackTranslation();
            $this->_loadThemeTranslation();
            $this->_loadDbTranslation();

            $this->setLocale($currentLocale);
        }
        // End override

        $this->_loadModuleTranslation();
        $this->_loadPackTranslation();
        $this->_loadThemeTranslation();
        $this->_loadDbTranslation();

        if (!$forceReload) {
            $this->_saveCache();
        }

        return $this;
    }

    /**
     * Get parent locale
     *
     * @return string
     */
    protected function getParentLocale()
    {
        $config = ObjectManager::getInstance()->get(ScopeConfigInterface::class);
        return $config->getValue(
            self::PARENT_LOCALE_PATH,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
    }
}