<?php
/**
 * This file is part of mxp.
 * 
 * PHP Version 5.2.5
 *
 * mxp is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 3 of the License, or
 * (at your option) any later version.
 *
 * mxp is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with mxp; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
 * 
 * @category   Tools
 * @package    Controller
 * @subpackage Command
 * @author     Manuel Pichler <mapi@manuel-pichler.de>
 * @copyright  2007-2008 Manuel Pichler. All rights reserved.
 * @license    GPL http://www.gnu.org/licenses/gpl-3.0.txt
 * @version    SVN: $Id$
 * @link       http://www.manuel-pichler.de
 */

/**
 * This class provides a configurable command manager.
 * 
 * @category   Tools
 * @package    Controller
 * @subpackage Command
 * @author     Manuel Pichler <mapi@manuel-pichler.de>
 * @copyright  2007-2008 Manuel Pichler. All rights reserved.
 * @license    GPL http://www.gnu.org/licenses/gpl-3.0.txt
 * @version    Release: @package_version@
 * @link       http://www.manuel-pichler.de
 */
class mxpControllerCommandManager
{
    /**
     * GoF singleton instance of the manager.
     *
     * @type mxpControllerCommandManager
     * @var mxpControllerCommandManager $manager
     */
    private static $manager = null;

    /**
     * Command manager singleton method which returns a configured instance
     * or <b>null</b>.
     *
     * @return mxpControllerCommandManager
     */
    public static function get()
    {
        return self::$manager;
    }

    /**
     * Singleton setter method for the system command manager.
     *
     * @param mxpControllerCommandManager $manager The default command manager
     * implementation.
     * 
     * @return void
     */
    public static function set( mxpControllerCommandManager $manager )
    {
        self::$manager = $manager;
    }
    
    /**
     * The directory with the configuration file.
     * 
     * @type string
     * @var string $configDir
     */
    protected $configDir = '';

    /**
     * The internal used config storage.
     *
     * @type mxpControllerCommandStorage
     * @var mxpControllerCommandStorage $storage
     */
    protected $storage = null;
    
    /**
     * An optional role policy checker for commands.
     *
     * @type mxpControllerRolePolicy
     * @var mxpControllerRolePolicy $rolePolicy
     */
    protected $rolePolicy = null;

    /**
     * The ctor takes the configuration folder as argument.
     *
     * @param string                  $path   The directory with the configuration
     *                                        file.
     * @param mxpControllerRolePolicy $policy An optional policy for command
     *                                        retrieval.
     */
    public function __construct( $path, mxpControllerRolePolicy $policy = null )
    {
        $this->configDir  = $path;
        $this->rolePolicy = $policy;
        
        $this->init();
    }
}