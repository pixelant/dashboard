<?php
namespace TYPO3\CMS\Dashboard\DashboardWidgets;

/**
 * Class RssWidget
 * @package TYPO3\CMS\Dashboard\DashboardWidgets
 */

use TYPO3\CMS\Dashboard\DashboardWidgetInterface;

class RssWidget extends AbstractWidget implements DashboardWidgetInterface {

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
	 * @var integer
	 */
	protected $feedLimit = 0;

	/**
	 * Limit, If set, it will limit the results in the list.
	 *
	 * @var integer
	 */
	protected $cacheLifetime = NULL;

	/**
	 * Widget settings
	 *
	 * @var array
	 */
	protected $widget = NULL;

	/**
	 * Renders content
	 * @param \TYPO3\CMS\Dashboard\Domain\Model\DashboardWidgetSettings $dashboardWidgetSetting
	 * @return string the rendered content
	 */
	public function render($dashboardWidgetSetting = NULL) {

		$this->initialize($dashboardWidgetSetting);

		$content = false;
		if ($this->cacheLifetime == NULL) {
			$content = $this->generateContent();
		} else {
			/** @var \TYPO3\CMS\Core\Cache\CacheManager $cacheManager */
			$cacheManager = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance('TYPO3\\CMS\\Extbase\\Object\\ObjectManager')->get('TYPO3\\CMS\\Core\\Cache\\CacheManager');
			$cacheIdentifier = 'dashboardWidget_' . (int)$dashboardWidgetSetting->getUid();

			if (TRUE === $cacheManager->hasCache('dashboard') && TRUE === $cacheManager->getCache('dashboard')->has($cacheIdentifier)) {
				$content = $cacheManager->getCache('dashboard')->get($cacheIdentifier);
			} else {
				$content = $this->generateContent();
				$cacheManager->getCache('dashboard')->set($cacheIdentifier, $content, array(), $this->cacheLifetime);
			}
			unset($cacheManager);
		}
		return $content;
	}

	/**
	 * Initializes settings from flexform
	 * @param \TYPO3\CMS\Dashboard\Domain\Model\DashboardWidgetSettings $dashboardWidgetSetting
	 * @return void
	 */
	private function initialize($dashboardWidgetSetting = NULL) {
        $flexformSettings = $this->getFlexFormSettings($dashboardWidgetSetting);
        $this->feedUrl = $flexformSettings['settings']['feedUrl'];
        $this->feedLimit = (int)$flexformSettings['settings']['feedLimit'];
        $this->cacheLifetime = (int)$flexformSettings['settings']['cacheLifetime'] * 60;
        $this->widget = $GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['dashboard']['widgets'][$dashboardWidgetSetting->getWidgetIdentifier()];
	}

	/**
	 * Generates the content
	 * @return string
	 */
	private function generateContent() {
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
	private function getFeed() {
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
				} else {
					throw new \Exception("The response was empty", 1910020001);
				}
			}
			if (!empty($content)) {
				$simpleXmlElement = simplexml_load_string( $content ,'SimpleXMLElement');
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
	private function rssToArray($simpleXmlElement) {
		$rss2Array = array();
		$rss2Array = $this->sxeToArray($simpleXmlElement->channel);
		foreach($simpleXmlElement->channel->item as $simpleXmlElementItem) {
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
	private function sxeToArray($simpleXmlElement) {
		$returnArray = false;
		$children = $simpleXmlElement->children();
		$sxeChildrenToArray = $this->sxeChildrenToArray($children);
		if ($sxeChildrenToArray) {
			$returnArray = $sxeChildrenToArray;
		}
		$namespaces = $simpleXmlElement->getNamespaces(TRUE);
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
	private function sxeChildrenToArray($children) {
		$nodeData = array();
		if (count($children) > 0) {
			foreach ($children as $elementName => $node) {
				$nodeName = $this->stringToArrayKey((string)$elementName);
				$nodeData[$nodeName] = array();
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
		} else {
			return false;
		}
	}

	/**
	 * stringToArrayKey Returns a string which is ok to use as array key
	 * @param  string $key The array key to check
	 * @return string
	 */
	private function stringToArrayKey($key) {
		return str_replace('.', '_', trim((string)$key));
	}
}
