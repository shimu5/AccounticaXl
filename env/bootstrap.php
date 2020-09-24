<?php
include('../vendor/autoload.php');
include('../vendor/Furina/furina.php');

use \Furina\core\Inflector;

include('../env/config.php');
Furina\app\components\Router::add(include('../env/urls.php'));

use Furina\event\Event;
use Furina\dev\ErrorType;

// force import Error Type class
\Furina\dev\ErrorType::$types;

if (!defined('DEBUG')) {
    define('DEBUG', 5);
}

function pr($array = array()){
    echo '<pre>';
    print_r($array);
    echo '</pre>';
}

function redirect($url){
    header( 'Location: '.$url ) ;
}

//function setFlash($type, $msg){
//    $_SESSION['FLASH'][$type] = $msg;
//}

$AUTHCOMPONENT = array('controller'=>'Admins', 'model'=>'Admin', 'fields' => array('password' => array('Admin', 'password'), 'status' => 0));

// Currency digit
define('CUR_DIGIT',8);

class ErrorCollector {
    protected static $error_list = array();
    protected static $sql_list = array();

    public static function getErrorList() {
        return static::$error_list;
    }

    public static function onError($event) {
        static::$error_list[] = $event->data;
//
//        $errno = $event->data['code'];
//        $message = $event->data['message'];
//        $file = $event->data['file'];
//        $line = $event->data['line'];

        //echo self::getExceptionHtml(new ErrorException($message, $errno, null, $file, $line));
        //echo static::getErrorHtml($e);
    }

    public static function onFallback($errno, $message, $file, $line, $event_triggered=false) {
        $dispatcher = new Furina\app\controllers\Dispatcher();
        $dispatcher->setView('php', new Furina\mvc\view\PhpView());
        $response = $dispatcher->handleException(new \Furina\mvc\controller\Request($_SERVER, $_GET, $_POST, $_FILES), new ErrorException($message, $errno, null, $file, $line), 500);
        $dispatcher->render($response);
//        echo self::getExceptionHtml(new ErrorException($message, $errno, null, $file, $line));
    }

    public static function onException($event) {
        //$a = ob_get_level();
        //ob_end_clean();
        //echo $a;
        $e = $event->data['exception'];
        static::$error_list[] = $e;
//        echo static::getExceptionHtml($e);
    }

    public static function getErrorHtml($e) {
        $errno = $e['code'];
        $message = $e['message'];
        $file = $e['file'];
        $line = $e['line'];
        return sprintf("<b>%s: %s</b><br/>\n(%s:%s)<br/>\n", ErrorType::get($errno), $message, $file, $line);
    }

    public static function getExceptionHtml($e) {
        return sprintf("<b>%s: %s</b><br/>\n(%s:%d)<br/>\n",
            $e instanceof ErrorException? 'ErrorException(' . ErrorType::get($e->getCode()) . ')': get_class($e),
            $e->getMessage(),
            $e->getFile(),
            $e->getLine());
    }

    public static function onSql($event) {
        static::$sql_list[] = array($event->event, $event->data);
    }

    public static function getSqlList() {
        return static::$sql_list;
    }
}

use \Furina\dev\FurinaSystemEvents;

FurinaSystemEvents::setErrorExceptionSeverity(E_ALL & (~E_DEPRECATED) & (~E_STRICT) & (~E_NOTICE) );
//FurinaSystemEvents::setErrorExceptionSeverity(E_DEPRECATED);
FurinaSystemEvents::attachErrorFallbackHandler(array('ErrorCollector', 'onFallback'));
Event::on('Error.*', array('ErrorCollector', 'onError'));
Event::on('Exception.*', array('ErrorCollector', 'onException'));
Event::on('SQL.*', array('ErrorCollector', 'onSql'));
//Event::on('Exception.*', array('ErrorCollector', 'onException'));
FurinaSystemEvents::registerAll();


try {
    $request = new Furina\mvc\controller\Request($_SERVER, $_GET, $_POST, $_FILES);

    $html->dirs = array(
        'css'=>$request->root.'public/static/dev/css/',
        'js'=>$request->root.'public/static/dev/js/',
        'img'=>$request->root.'public/static/dev/img/',
        'root'=>$request->root
    );


    $router = new Furina\app\components\Router();
    \Furina\core\InstanceContainer::registerInstance('Router', $router);

    $session = new Furina\app\components\Session();
    \Furina\core\InstanceContainer::registerInstance('Session', $session);

    Furina\event\Event::on('Dispatcher.ProcessRequest', array($session, 'processRequest'));
    \Furina\core\InstanceContainer::registerInstance('Cookie', new Furina\app\components\Cookie());
    
    $auth = new Furina\app\components\Auth();
    \Furina\core\InstanceContainer::registerInstance('Auth', $auth);
    Furina\event\Event::on('Dispatcher.ProcessRequest', array($auth, 'processRequest'));
    
    $pagination = new Furina\app\components\Pagination();
    \Furina\core\InstanceContainer::registerInstance('Pagination', $pagination);
    Furina\event\Event::on('Dispatcher.ProcessRequest', array($pagination, 'processRequest'));

    \Furina\core\Config::set('Furina\app\controllers\Dispatcher', array('components'=>array(
            'Session',
            'Cookie',
            'Auth',
            'Pagination'
        )));

    $dispatcher = new Furina\app\controllers\Dispatcher();
    $dispatcher->setView('template', new Furina\mvc\view\MambaView());
    $dispatcher->setView('php', new Furina\mvc\view\PhpView());
    $dispatcher->setView('json', new Furina\mvc\view\JsonView());
    $dispatcher->setView(null, new Furina\mvc\view\NullView());
    $dispatcher->dispatch($request);
//    foreach (ErrorCollector::getErrorList() as $e) {
//        if ($e instanceof Exception) {
//            echo ErrorCollector::getExceptionHtml($e);
//        }
//        else {
//            echo ErrorCollector::getErrorHtml($e);
//        }
//    }
//    throw new Exception("Manually raised an exception for test");

}
catch (Exception $e) {
    $dispatcher = new Furina\app\controllers\Dispatcher();
    $response = $dispatcher->handleException($request, $e, 500);
    $dispatcher->render($response);
}
?>


<?php if (DEBUG > 0): ?>
<div class="debug">
    <?php
//    foreach (ErrorCollector::getErrorList() as $e) {
//        if ($e instanceof Exception) {
//            echo ErrorCollector::getExceptionHtml($e);
//        }
//        else {
//            echo ErrorCollector::getErrorHtml($e);
//        }
//    }
    ?>

    <?php if (DEBUG > 2): ?>
    <table>
        <?php foreach (ErrorCollector::getSqlList() as $e): ?>
        <tr class="<?php substr($e[0], 5) ?>">
            <td><?php echo htmlspecialchars($e[1][0]) ?></td>
            <?php if ($e[0] == 'SQL.success'): ?>
            <td><?php echo $e[1][1] . ' / ' . $e[1][2] ?></td>
            <td><?php echo sprintf('%02f', $e[1][3]) ?></td>
            <?php else: ?>
            <td colspan="3"><?php echo $e[1][1] ?></td>
            <?php endif ?>
        </tr>
        <?php endforeach ?>
    </table>
    <?php endif ?>
</div>
<?php endif ?>