<?php
/**
 * Contao Open Source CMS
 *
 * Copyright (c) 2016 Heimrich & Hannot GmbH
 *
 * @author  Rico Kaltofen <r.kaltofen@heimrich-hannot.de>
 * @license http://www.gnu.org/licences/lgpl-3.0.html LGPL
 */

namespace HeimrichHannot\LinkChecker\Widgets;


use HeimrichHannot\Ajax\Response\ResponseData;
use HeimrichHannot\Ajax\Response\ResponseSuccess;
use HeimrichHannot\Haste\Util\Url;
use Symfony\Component\CssSelector\Exception\SyntaxErrorException;
use Wa72\HtmlPageDom\HtmlPageCrawler;

class LinkChecker extends \Widget
{
    /**
     * Submit user input
     *
     * @var boolean
     */
    protected $blnSubmitInput = false;

    const LINKCHECKER_PARAM       = 'lc'; // The param within the url, that holds the test url
    const LINKCHECKER_TEST_ACTION = 'lc-test'; // The back end action, required for ajax request

    /**
     * Template
     *
     * @var string
     */
    protected $strTemplate = 'be_linkchecker_widget';

    protected $arrLinks = array();

    public function generate()
    {
        if ($this->collect())
        {
            return $this->test();
        }
    }

    public function executePreActionsHook($strAction)
    {
        if ($strAction !== static::LINKCHECKER_TEST_ACTION)
        {
            return false;
        }

        if (!\Input::post(static::LINKCHECKER_PARAM))
        {
            return false;
        }

        $strStatus = \HeimrichHannot\LinkChecker\LinkChecker::test(\Input::post(static::LINKCHECKER_PARAM));

        $objResponse = new ResponseSuccess();
        $objResponse->setResult(new ResponseData($strStatus));
        $objResponse->output();
    }

    protected function test()
    {
        $objTemplate = new \BackendTemplate('be_linkchecker');

        $rand = rand();

        $arrLinks = array();

        // Display the pages
        for ($i = 0, $c = count($this->arrLinks); $i < $c; $i++)
        {
            $strUrl = Url::addQueryString('REQUEST_TOKEN=' . \RequestToken::get(), 'contao/main.php');
            $strUrl = Url::addQueryString('action=' . static::LINKCHECKER_TEST_ACTION, $strUrl);
            $strUrl = Url::addQueryString(static::LINKCHECKER_PARAM . '=' . $this->arrLinks[$i], $strUrl);

            $objLink           = new \stdClass();
            $objLink->url      = urldecode($strUrl);
            $objLink->title    = $this->arrLinks[$i];
            $objLink->testUrl  = $this->arrLinks[$i] . '#' . $rand . $i;
            $objLink->targetID = 'lc-' . $rand . $i;
            $objLink->target   = '#lc-' . $rand . $i;
            $objLink->text     = \StringUtil::substr($this->arrLinks[$i], 100);
            $arrLinks[$i]      = $objLink;
            unset($this->arrLinks[$i]);
        }

        $objTemplate->links = $arrLinks;

        return $objTemplate->parse();
    }

    protected function collect()
    {
        // string
        $this->arrLinks = $this->value;

        // array
        if(is_array($this->value))
        {
            $this->arrLinks = $this->value;
            return true;
        }

        // html code
        if(strpos($this->value, '<') !== false)
        {
            $this->arrLinks = array();
            $objCrawler = new HtmlPageCrawler($this->value);

            // collect only inside body
            if ($objCrawler->isHtmlDocument())
            {
                $objCrawler->filter('body');
            }

            try
            {
                // container
                $objCrawler->filter('a[href]')->each(
                    function ($node, $i)
                    {
                        /** @var $node  HtmlPageCrawler */
                        return $this->addLinkFromNode($node);
                    }
                );
            } catch (SyntaxErrorException $e){}
        }



        return !empty($this->arrLinks);
    }

    /**
     * Add links from node to test list
     *
     * @param HtmlPageCrawler $node
     */
    protected function addLinkFromNode(HtmlPageCrawler $node)
    {
        $this->arrLinks[] = $node->attr('href');
    }

}