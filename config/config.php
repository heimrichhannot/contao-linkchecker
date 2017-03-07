<?php
/**
 * Contao Open Source CMS
 *
 * Copyright (c) 2016 Heimrich & Hannot GmbH
 *
 * @author  Rico Kaltofen <r.kaltofen@heimrich-hannot.de>
 * @license http://www.gnu.org/licences/lgpl-3.0.html LGPL
 */

/**
 * Back end form fields
 */
$GLOBALS['BE_FFL']['linkChecker'] = 'HeimrichHannot\LinkChecker\Widgets\LinkChecker';

/**
 * Assets
 */
$GLOBALS['TL_JAVASCRIPT']['promise-polyfill'] = 'system/modules/linkchecker/assets/vendor/promise-polyfill/promise.min.js|static';
$GLOBALS['TL_JAVASCRIPT']['linkchecker'] = 'system/modules/linkchecker/assets/js/linkchecker.js|static';

if (TL_MODE == 'BE')
{
    $GLOBALS['TL_CSS']['linkchecker'] = 'system/modules/linkchecker/assets/css/be_linkchecker.min.css|static';
}

/**
 * Hooks
 */
$GLOBALS['TL_HOOKS']['executePreActions'][] = array('HeimrichHannot\LinkChecker\Widgets\LinkChecker', 'executePreActionsHook');