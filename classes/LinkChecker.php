<?php
/**
 * Contao Open Source CMS
 *
 * Copyright (c) 2016 Heimrich & Hannot GmbH
 *
 * @author  Rico Kaltofen <r.kaltofen@heimrich-hannot.de>
 * @license http://www.gnu.org/licences/lgpl-3.0.html LGPL
 */

namespace HeimrichHannot\LinkChecker;


use HeimrichHannot\Haste\Util\StringUtil;

class LinkChecker
{
    const STATUS_MAILTO  = 'mailto';
    const STATUS_INVALID = 'invalid';
    const STATUS_TIMEOUT = 'HTTP/1.1 408 Request Timeout';
    const STATUS_OK      = 'HTTP/1.1 200 OK';

    const CLASS_DEFAULT = 'lc-default';
    const CLASS_INFO    = 'lc-info';
    const CLASS_SUCCESS = 'lc-success';
    const CLASS_ERROR   = 'lc-error';


    /**
     * Test a given link, or all links that has been added with add()
     *
     * @param string|array $varLinks A single url or an array with multiple urls as value
     *
     * @return array|bool|mixed
     */
    public static function test($varLinks)
    {
        if (!is_array($varLinks))
        {
            return static::testOne($varLinks);
        }

        return static::testAll($varLinks);
    }

    /**
     * Test a single url
     *
     * @param $strUrl The url
     *
     * @return bool|mixed The translated status code, or false if the link was not tested
     */
    protected function testOne($strUrl)
    {
        if (StringUtil::startsWith($strUrl, 'mailto:'))
        {
            return static::getResult(static::STATUS_MAILTO);
        }

        if (!\Validator::isUrl($strUrl))
        {
            return static::getResult(static::STATUS_INVALID);
        }

        if ($arrHeaders = @get_headers($strUrl))
        {
            return static::getResult($arrHeaders[0]);
        }

        return static::getResult(static::STATUS_TIMEOUT);
    }

    /**
     * Test a list of links
     *
     * @param array $arrLinks Array with multiple urls as value
     *
     * @return array The  list of tested links with translated status code, or false if the link was not tested
     */
    protected function testAll(array $arrLinks)
    {
        $arrResults = array();

        foreach ($arrLinks as $strKey => $strUrl)
        {
            $arrResults[$strUrl] = static::testOne($strUrl);
            unset($arrLinks);
        }

        return $arrResults;
    }

    /**
     * Get the styled result
     *
     * @param $strResult
     *
     * @return mixed
     */
    protected static function getResult($strResult)
    {
        $objTemplate         = new \FrontendTemplate('linkchecker_result_default');
        $strLabel            = $GLOBALS['TL_LANG']['linkChecker']['statusCodes'][$strResult];
        $objTemplate->text   = $strLabel ?: $strResult;
        $objTemplate->status = static::getStatusClass($strResult);

        return $objTemplate->parse();
    }


    /**
     * Get the status class for a given result
     */
    protected static function getStatusClass($strResult)
    {
        $intStart = null;
        $arrResponse = explode(' ', $strResult);

        if(is_array($arrResponse) && $arrResponse[1])
        {
            $intStart = substr($arrResponse[1], 0, 1);
        }

        switch ($intStart)
        {
            //1xx Informational
            case '1':
                return static::CLASS_INFO;
            //2xx Success
            case '2':
                return static::CLASS_SUCCESS;
            //3xx Redirection
            case '3':
                return static::CLASS_INFO;
            //4xx Client Error
            case '4':
                return static::CLASS_ERROR;
            //5xx Server Error
            case '5':
                return static::CLASS_ERROR;
        }

        return static::CLASS_DEFAULT;
    }

}