<?php
/**
 * Created by JetBrains PhpStorm.
 * User: zubair
 * Date: 2/5/14
 * Time: 2:55 PM
 * To change this template use File | Settings | File Templates.
 */

Furina::register('Session', new Furina\app\components\Session());
Furina::register('Cookie', new Furina\app\components\Cookie());
Furina::register('Router', new Furina\app\components\Router());
Furina::register('Auth', new Furina\app\components\Router());
Furina::register('View', new Furina\framework\view\View());
Furina::register('Usertype', new accountica\Usertype());

