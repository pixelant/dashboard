<?php
declare(strict_types = 1);
namespace Pixelant\Dashboard\Service;

use TYPO3\CMS\Core\Localization\Locales;
use TYPO3\CMS\Core\Localization\LocalizationFactory;
use TYPO3\CMS\Core\SingletonInterface;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Configuration\ConfigurationManagerInterface;
use TYPO3\CMS\Extbase\Object\ObjectManager;
use TYPO3\CMS\Frontend\Controller\TypoScriptFrontendController;
use TYPO3\CMS\Lang\LanguageService;

class TranslationService implements SingletonInterface
{
    /**
     * Local Language content
     *
     * @var array
     */
    protected $LOCAL_LANG = [];

    /**
     * Contains those LL keys, which have been set to (empty) in TypoScript.
     * This is necessary, as we cannot distinguish between a nonexisting
     * translation and a label that has been cleared by TS.
     * In both cases ['key'][0]['target'] is "".
     *
     * @var array
     */
    protected $LOCAL_LANG_UNSET = [];

    /**
     * Key of the language to use
     *
     * @var string
     */
    protected $languageKey = null;

    /**
     * Pointer to alternative fall-back language to use
     *
     * @var array
     */
    protected $alternativeLanguageKeys = [];

    /**
     * @var \TYPO3\CMS\Extbase\Configuration\ConfigurationManagerInterface
     */
    protected $configurationManager = null;

    /**
     * Return TranslationService as singleton
     *
     * @return TranslationService
     */
    public static function getInstance()
    {
        return GeneralUtility::makeInstance(ObjectManager::class)->get(self::class);
    }

    /**
     * Returns the localized label of the LOCAL_LANG key, $key.
     *
     * @param mixed $key The key from the LOCAL_LANG array for which to return the value.
     * @param array $arguments the arguments of the extension, being passed over to vsprintf
     * @param string $locallangPathAndFilename
     * @param string $language
     * @param mixed $defaultValue
     * @return mixed The value from LOCAL_LANG or $defaultValue if no translation was found.
     */
    public function translate(
        $key,
        array $arguments = null,
        string $locallangPathAndFilename = null,
        string $language = null,
        $defaultValue = ''
    ) {
        $value = null;
        $key = (string) $key;

        if ($locallangPathAndFilename) {
            $key = $locallangPathAndFilename . ':' . $key;
        }

        $keyParts = explode(':', $key);
        if (GeneralUtility::isFirstPartOfStr($key, 'LLL:')) {
            $locallangPathAndFilename = $keyParts[1] . ':' . $keyParts[2];
            $key = $keyParts[3];
        } elseif (GeneralUtility::isFirstPartOfStr($key, 'EXT:')) {
            $locallangPathAndFilename = $keyParts[0] . ':' . $keyParts[1];
            $key = $keyParts[2];
        } else {
            if (count($keyParts) === 2) {
                $locallangPathAndFilename = $keyParts[0];
                $key = $keyParts[1];
            }
        }

        if ($language) {
            $this->languageKey = $language;
        }

        $this->initializeLocalization($locallangPathAndFilename);

        // The "from" charset of csConv() is only set for strings from TypoScript via _LOCAL_LANG
        if (!empty($this->LOCAL_LANG[$this->languageKey][$key][0]['target'])
            || isset($this->LOCAL_LANG_UNSET[$this->languageKey][$key])
        ) {
            // Local language translation for key exists
            $value = $this->LOCAL_LANG[$this->languageKey][$key][0]['target'];
        } elseif (!empty($this->alternativeLanguageKeys)) {
            $languages = array_reverse($this->alternativeLanguageKeys);
            foreach ($languages as $language) {
                if (!empty($this->LOCAL_LANG[$language][$key][0]['target'])
                    || isset($this->LOCAL_LANG_UNSET[$language][$key])
                ) {
                    // Alternative language translation for key exists
                    $value = $this->LOCAL_LANG[$language][$key][0]['target'];
                    break;
                }
            }
        }

        if ($value === null && (!empty($this->LOCAL_LANG['default'][$key][0]['target'])
                || isset($this->LOCAL_LANG_UNSET['default'][$key]))
        ) {
            // Default language translation for key exists
            // No charset conversion because default is English and thereby ASCII
            $value = $this->LOCAL_LANG['default'][$key][0]['target'];
        }

        if (is_array($arguments) && !empty($arguments) && $value !== null) {
            $value = vsprintf($value, $arguments);
        } else {
            if (empty($value)) {
                $value = $defaultValue;
            }
        }

        return $value;
    }

    /**
     * Recursively translate values.
     *
     * @param array $array
     * @param array|string|null $translationFile
     * @return array the modified array
     */
    public function translateValuesRecursive(array $array, $translationFile = null): array
    {
        $result = $array;
        foreach ($result as $key => $value) {
            if (is_array($value)) {
                $result[$key] = $this->translateValuesRecursive($value, $translationFile);
            } else {
                $translationFiles = null;
                if (is_string($translationFile)) {
                    $translationFiles = [$translationFile];
                } elseif (is_array($translationFile)) {
                    $translationFiles = $this->sortArrayWithIntegerKeysDescending($translationFile);
                }

                if ($translationFiles) {
                    foreach ($translationFiles as $_translationFile) {
                        $translatedValue = $this->translate($value, null, $_translationFile, null);
                        if (!empty($translatedValue)) {
                            $result[$key] = $translatedValue;
                            break;
                        }
                    }
                } else {
                    $result[$key] = $this->translate($value, null, $translationFile, null, $value);
                }
            }
        }
        return $result;
    }

    /**
     * @param string $languageKey
     */
    public function setLanguage(string $languageKey)
    {
        $this->languageKey = $languageKey;
    }

    /**
     * @return string
     */
    public function getLanguage(): string
    {
        return $this->languageKey;
    }

    /**
     * @param array $translationKeyChain
     * @param string $language
     * @param array $arguments
     * @return string|null
     */
    protected function processTranslationChain(
        array $translationKeyChain,
        string $language = null,
        array $arguments = null
    ) {
        $translatedValue = null;
        foreach ($translationKeyChain as $translationKey) {
            $translatedValue = $this->translate($translationKey, $arguments, null, $language);
            if (!empty($translatedValue)) {
                break;
            }
        }
        return $translatedValue;
    }

    /**
     * @param string $locallangPathAndFilename
     */
    protected function initializeLocalization(string $locallangPathAndFilename)
    {
        if (empty($this->languageKey)) {
            $this->setLanguageKeys();
        }

        if (!empty($locallangPathAndFilename)) {
            /** @var $languageFactory LocalizationFactory */
            $languageFactory = GeneralUtility::makeInstance(LocalizationFactory::class);
            $this->LOCAL_LANG = $languageFactory->getParsedData($locallangPathAndFilename, $this->languageKey, 'utf-8');

            foreach ($this->alternativeLanguageKeys as $language) {
                $tempLL = $languageFactory->getParsedData($locallangPathAndFilename, $language, 'utf-8');
                if ($this->languageKey !== 'default' && isset($tempLL[$language])) {
                    $this->LOCAL_LANG[$language] = $tempLL[$language];
                }
            }
        }
        $this->loadTypoScriptLabels();
    }

    /**
     * Sets the currently active language/language_alt keys.
     * Default values are "default" for language key and "" for language_alt key.
     */
    protected function setLanguageKeys()
    {
        $this->languageKey = 'default';

        $this->alternativeLanguageKeys = [];
        if (TYPO3_MODE === 'FE') {
            if (isset($this->getTypoScriptFrontendController()->config['config']['language'])) {
                $this->languageKey = $this->getTypoScriptFrontendController()->config['config']['language'];
                if (isset($this->getTypoScriptFrontendController()->config['config']['language_alt'])) {
                    $this->alternativeLanguageKeys[] = $this->getTypoScriptFrontendController()->config['config']['language_alt'];
                } else {
                    /** @var $locales \TYPO3\CMS\Core\Localization\Locales */
                    $locales = GeneralUtility::makeInstance(Locales::class);
                    if (in_array($this->languageKey, $locales->getLocales(), true)) {
                        foreach ($locales->getLocaleDependencies($this->languageKey) as $language) {
                            $this->alternativeLanguageKeys[] = $language;
                        }
                    }
                }
            }
        } elseif (!empty($GLOBALS['BE_USER']->uc['lang'])) {
            $this->languageKey = $GLOBALS['BE_USER']->uc['lang'];
        } elseif (!empty($this->getLanguageService()->lang)) {
            $this->languageKey = $this->getLanguageService()->lang;
        }
    }

    /**
     * Overwrites labels that are set via TypoScript.
     * TS locallang labels have to be configured like:
     * plugin.tx_form._LOCAL_LANG.languageKey.key = value
     */
    protected function loadTypoScriptLabels()
    {
        $frameworkConfiguration = $this->getConfigurationManager()
            ->getConfiguration(ConfigurationManagerInterface::CONFIGURATION_TYPE_FRAMEWORK, 'form');

        if (!is_array($frameworkConfiguration['_LOCAL_LANG'])) {
            return;
        }
        $this->LOCAL_LANG_UNSET = [];
        foreach ($frameworkConfiguration['_LOCAL_LANG'] as $languageKey => $labels) {
            if (!(is_array($labels) && isset($this->LOCAL_LANG[$languageKey]))) {
                continue;
            }
            foreach ($labels as $labelKey => $labelValue) {
                if (is_string($labelValue)) {
                    $this->LOCAL_LANG[$languageKey][$labelKey][0]['target'] = $labelValue;
                    if ($labelValue === '') {
                        $this->LOCAL_LANG_UNSET[$languageKey][$labelKey] = '';
                    }
                } elseif (is_array($labelValue)) {
                    $labelValue = $this->flattenTypoScriptLabelArray($labelValue, $labelKey);
                    foreach ($labelValue as $key => $value) {
                        $this->LOCAL_LANG[$languageKey][$key][0]['target'] = $value;
                        if ($value === '') {
                            $this->LOCAL_LANG_UNSET[$languageKey][$key] = '';
                        }
                    }
                }
            }
        }
    }

    /**
     * Flatten TypoScript label array; converting a hierarchical array into a flat
     * array with the keys separated by dots.
     *
     * Example Input:  array('k1' => array('subkey1' => 'val1'))
     * Example Output: array('k1.subkey1' => 'val1')
     *
     * @param array $labelValues Hierarchical array of labels
     * @param string $parentKey the name of the parent key in the recursion; is only needed for recursion.
     * @return array flattened array of labels.
     */
    protected function flattenTypoScriptLabelArray(array $labelValues, string $parentKey = ''): array
    {
        $result = [];
        foreach ($labelValues as $key => $labelValue) {
            if (!empty($parentKey)) {
                $key = $parentKey . '.' . $key;
            }
            if (is_array($labelValue)) {
                $labelValue = $this->flattenTypoScriptLabelArray($labelValue, $key);
                $result = array_merge($result, $labelValue);
            } else {
                $result[$key] = $labelValue;
            }
        }
        return $result;
    }

    /**
     * If the array contains numerical keys only, sort it in descending order
     *
     * @param array $array
     * @return array
     */
    protected function sortArrayWithIntegerKeysDescending(array $array)
    {
        if (count(array_filter(array_keys($array), 'is_string')) === 0) {
            krsort($array);
        }
        return $array;
    }

    /**
     * Returns instance of the configuration manager
     *
     * @return ConfigurationManagerInterface
     */
    protected function getConfigurationManager(): ConfigurationManagerInterface
    {
        if ($this->configurationManager !== null) {
            return $this->configurationManager;
        }

        $this->configurationManager = GeneralUtility::makeInstance(ObjectManager::class)
            ->get(ConfigurationManagerInterface::class);
        return $this->configurationManager;
    }

    /**
     * @return LanguageService
     */
    protected function getLanguageService(): LanguageService
    {
        return $GLOBALS['LANG'];
    }

    /**
     * @return TypoScriptFrontendController
     */
    protected function getTypoScriptFrontendController(): TypoScriptFrontendController
    {
        return $GLOBALS['TSFE'];
    }
}
