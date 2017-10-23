<?php
namespace Pixelant\Dashboard\Widget;

use Pixelant\Dashboard\Widget\WidgetInterface;

class RssWidget implements WidgetInterface
{
    const IDENTIFIER = '41385600';

    /**
     * Feed URL
     *
     * @var string
     * @validate NotEmpty
     */
    protected $feedUrl = '';

    /**
     * Limit, If set, it will limit the results in the list.
     *
     * @var int
     */
    protected $feedLimit = 0;

    /**
     * Limit, If set, it will limit the results in the list.
     *
     * @var int
     */
    protected $cacheLifetime = 0;

    /**
     * Widget settings
     *
     * @var array
     */
    protected $widget = null;

    /**
     * Renders content
     * @param \Pixelant\Dashboard\Domain\Model\DashboardWidgetSettings $dashboardWidgetSetting
     * @return string the rendered content
     */
    public function render($dashboardWidgetSetting = null): string
    {
        $this->initialize($dashboardWidgetSetting);
        if ($this->cacheLifetime > 0) {
            $content = $this->generateContent();
        } else {
            /** @var \TYPO3\CMS\Core\Cache\CacheManager $cacheManager */
            $cacheManager = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance('TYPO3\\CMS\\Extbase\\Object\\ObjectManager')->get('TYPO3\\CMS\\Core\\Cache\\CacheManager');
            $cacheIdentifier = 'dashboardWidget_' . (int)$dashboardWidgetSetting->getUid();

            if (true === $cacheManager->hasCache('dashboard') && true === $cacheManager->getCache('dashboard')->has($cacheIdentifier)) {
                $content = $cacheManager->getCache('dashboard')->get($cacheIdentifier);
            } else {
                $content = $this->generateContent();
                $cacheManager->getCache('dashboard')->set($cacheIdentifier, $content, [], $this->cacheLifetime);
            }
            unset($cacheManager);
        }
        return $content;
    }

    /**
     * Initializes settings from flexform
     * @param \Pixelant\Dashboard\Domain\Model\DashboardWidgetSettings $dashboardWidgetSetting
     * @return void
     */
    private function initialize($dashboardWidgetSetting = null)
    {
        $settings = $dashboardWidgetSetting->getSettings();
        $this->feedUrl = $settings['feedUrl'];
        $this->feedLimit = (int)$settings['feedLimit'];
        $this->cacheLifetime = (int)$settings['cacheLifetime'] * 60;
        $this->widget = $settings;
    }

    /**
     * Generates the content
     * @return string
     */
    private function generateContent()
    {
        $widgetTemplateName = $this->widget['template'];
        $objectManager = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance('TYPO3\\CMS\\Extbase\\Object\\ObjectManager');
        $rssView = $objectManager->get('TYPO3\\CMS\\Fluid\\View\\StandaloneView');
        $template = \TYPO3\CMS\Core\Utility\GeneralUtility::getFileAbsFileName($widgetTemplateName);
        $rssView->setTemplatePathAndFilename($template);
        $feed = $this->getFeed();
        $rssView->assign('feed', $feed);
        $rssView->assign('currentdate', time());
        $rssView->assign('cacheLifetime', $this->cacheLifetime);
        return $rssView->render();
    }

    /**
     * Loads feed and cuts unneeded items
     *
     * @return array Array from xml
     */
    private function getFeed()
    {
        $feed = [];
        $report = [];
        if (\TYPO3\CMS\Core\Utility\GeneralUtility::isValidUrl($this->feedUrl)) {
            $content = \TYPO3\CMS\Core\Utility\GeneralUtility::getUrl(
                $this->feedUrl,
                0,
                null,
                $report
            );
            if (!$content) {
                if (isset($report['message'])) {
                    throw new \Exception($report['message'], $report['error']);
                }
                throw new \Exception('The response was empty', 1910020001);
            }
            if (!empty($content)) {
                $simpleXmlElement = simplexml_load_string($content, 'SimpleXMLElement');
                if (!$simpleXmlElement) {
                    throw new \Exception('The response is not valid XML', 1910020003);
                }
                $feed['channel'] = $this->rssToArray($simpleXmlElement);
                if ((int)$this->feedLimit > 0) {
                    $feed['channel']['item'] = array_splice($feed['channel']['item'], 0, $this->feedLimit);
                }
            } else {
                throw new \Exception('An error occured', 1910020002);
            }
        } else {
            throw new \Exception('The provided url is not valid', 1910020004);
        }
        return $feed;
    }

    /**
     * rssToArray RSS to array from a SimpleXMLElement
     * @param  \SimpleXmlElement $simpleXmlElement
     * @return array
     */
    private function rssToArray($simpleXmlElement)
    {
        $rss2Array = [];
        $rss2Array = $this->sxeToArray($simpleXmlElement->channel);
        foreach ($simpleXmlElement->channel->item as $simpleXmlElementItem) {
            $simpleXmlElementArray = $this->sxeToArray($simpleXmlElementItem);
            if ($simpleXmlElementArray) {
                $rss2Array['item'][] = $simpleXmlElementArray;
            }
        }
        return $rss2Array;
    }

    /**
     * sxeToArray Generates the base array for the element, also includes namespaces.
     * @param  \SimpleXMLElement $simpleXmlElement The element to create an array of
     * @return array
     */
    private function sxeToArray($simpleXmlElement)
    {
        $returnArray = false;
        $children = $simpleXmlElement->children();
        $sxeChildrenToArray = $this->sxeChildrenToArray($children);
        if ($sxeChildrenToArray) {
            $returnArray = $sxeChildrenToArray;
        }
        $namespaces = $simpleXmlElement->getNamespaces(true);
        foreach ($namespaces as $ns => $nsuri) {
            $children = $simpleXmlElement->children($ns, true);
            $sxeChildrenToArray = $this->sxeChildrenToArray($children);
            if ($sxeChildrenToArray) {
                $returnArray[$ns] = $sxeChildrenToArray;
            }
        }
        return $returnArray;
    }

    /**
     * sxeChildrenToArray Returns an array of the elements children and attributes recursively
     * @param  mixed $children The children of a element
     * @return array
     */
    private function sxeChildrenToArray($children)
    {
        $nodeData = [];
        if (count($children) > 0) {
            foreach ($children as $elementName => $node) {
                $nodeName = $this->stringToArrayKey((string)$elementName);
                $nodeData[$nodeName] = [];
                $nodeAttributes = $node->attributes();
                if (count($nodeAttributes) > 0) {
                    foreach ($nodeAttributes as $nodeAttributeName => $nodeAttributeValue) {
                        $arrayKey = $this->stringToArrayKey((string)$nodeAttributeName);
                        $arrayValue = trim((string)$nodeAttributeValue->__toString());
                        $nodeData[$nodeName][$arrayKey] = $arrayValue;
                    }
                }
                $nodeValue = trim((string)$node);
                if (strlen($nodeValue) > 0) {
                    if (count($nodeAttributes) == 0) {
                        $nodeData[$nodeName] = $nodeValue;
                    } else {
                        $nodeData[$nodeName]['value'] = $nodeValue;
                    }
                } else {
                    if ($nodeName != 'item') {
                        $childs = $this->sxeToArray($node);
                        if ($childs) {
                            $nodeData[$nodeName] = $childs;
                        }
                    }
                }
            }
            return $nodeData;
        }
        return false;
    }

    /**
     * stringToArrayKey Returns a string which is ok to use as array key
     * @param  string $key The array key to check
     * @return string
     */
    private function stringToArrayKey($key)
    {
        return str_replace('.', '_', trim((string)$key));
    }
}
