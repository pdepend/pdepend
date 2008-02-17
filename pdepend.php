#!/usr/bin/env php
<?php
/**
 * This file is part of PHP_Depend.
 *
 * PHP Version 5
 *
 * Copyright (c) 2008, Manuel Pichler <mapi@pmanuel-pichler.de>.
 * All rights reserved.
 *
 * Redistribution and use in source and binary forms, with or without
 * modification, are permitted provided that the following conditions
 * are met:
 *
 *   * Redistributions of source code must retain the above copyright
 *     notice, this list of conditions and the following disclaimer.
 *
 *   * Redistributions in binary form must reproduce the above copyright
 *     notice, this list of conditions and the following disclaimer in
 *     the documentation and/or other materials provided with the
 *     distribution.
 *
 *   * Neither the name of Manuel Pichler nor the names of his
 *     contributors may be used to endorse or promote products derived
 *     from this software without specific prior written permission.
 *
 * THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS
 * "AS IS" AND ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT
 * LIMITED TO, THE IMPLIED WARRANTIES OF MERCHANTABILITY AND FITNESS
 * FOR A PARTICULAR PURPOSE ARE DISCLAIMED. IN NO EVENT SHALL THE
 * COPYRIGHT OWNER OR CONTRIBUTORS BE LIABLE FOR ANY DIRECT, INDIRECT,
 * INCIDENTAL, SPECIAL, EXEMPLARY, OR CONSEQUENTIAL DAMAGES (INCLUDING,
 * BUT NOT LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES;
 * LOSS OF USE, DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER
 * CAUSED AND ON ANY THEORY OF LIABILITY, WHETHER IN CONTRACT, STRICT
 * LIABILITY, OR TORT (INCLUDING NEGLIGENCE OR OTHERWISE) ARISING IN
 * ANY WAY OUT OF THE USE OF THIS SOFTWARE, EVEN IF ADVISED OF THE
 * POSSIBILITY OF SUCH DAMAGE.
 *
 * @category  QualityAssurance
 * @package   PHP_Depend
 * @author    Manuel Pichler <mapi@manuel-pichler.de>
 * @copyright 2008 Manuel Pichler. All rights reserved.
 * @license   http://www.opensource.org/licenses/bsd-license.php  BSD License
 * @version   SVN: $Id$
 * @link      http://www.manuel-pichler.de/
 */

// PEAR/svn workaround
if (strpos('@php_bin@', '@php_bin') === 0) {
    set_include_path(get_include_path() . PATH_SEPARATOR . dirname(__FILE__));
}
 
if (count($GLOBALS['argv']) < 2) {
    echo "Usage phpdep.php <source-dir> [<output-dir>]\n";
    exit(1);
}

$source = realpath($GLOBALS['argv'][1]);
if (!is_dir($source)) {
    echo $GLOBALS['argv'][1] . " doesn't exist.\n";
    exit(1);
}

if (count($GLOBALS['argv']) > 2) {
    $output = $GLOBALS['argv'][2];
    
    if (!is_dir($output) && !mkdir($output, 0755, true)) {
        echo "Cannot create output directory {$output}.\n";
        exit(1);
    }
} else {
    $output = getcwd();
}

require_once 'PHP/Depend.php';
require_once 'PHP/Depend/Renderer/GdChartRenderer.php';
require_once 'PHP/Depend/Renderer/XMLRenderer.php';
require_once 'PHP/Depend/Util/ExcludePathFilter.php';
require_once 'PHP/Depend/Util/FileExtensionFilter.php';

$pdepend = new PHP_Depend();
$pdepend->addDirectory($source);

$pdepend->addFilter(new PHP_Depend_Util_FileExtensionFilter(array('php', 'inc')));
$pdepend->addFilter(new PHP_Depend_Util_ExcludePathFilter(array('tests/')));

$packages = $pdepend->analyze();

$renderer = new PHP_Depend_Renderer_GdChartRenderer($output . '/php_depend.png');
$renderer->render($packages);

$renderer = new PHP_Depend_Renderer_XMLRenderer($output . '/php_depend.xml');
$renderer->render($packages);
