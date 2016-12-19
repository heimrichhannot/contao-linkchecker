<?php
/**
 * Contao Open Source CMS
 *
 * Copyright (c) 2016 Heimrich & Hannot GmbH
 *
 * @author  Rico Kaltofen <r.kaltofen@heimrich-hannot.de>
 * @license http://www.gnu.org/licences/lgpl-3.0.html LGPL
 */

$arrLang = &$GLOBALS['TL_LANG']['linkChecker'];

/**
 * Messages
 */
$arrLang['statusCodes'][\HeimrichHannot\LinkChecker\LinkChecker::STATUS_MAILTO] = 'E-Mail Adressen werden nicht geprüft.';
$arrLang['statusCodes'][\HeimrichHannot\LinkChecker\LinkChecker::STATUS_INVALID] = 'Ungültige URL, kann nicht geprüft werden.';
$arrLang['statusCodes'][\HeimrichHannot\LinkChecker\LinkChecker::STATUS_TIMEOUT] = 'HTTP/1.1 408 Request Time-out';
$arrLang['statusCodes'][\HeimrichHannot\LinkChecker\LinkChecker::STATUS_OK] = 'HTTP/1.1 200 <span class="lc-success">OK</span>';

