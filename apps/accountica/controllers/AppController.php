<?php
/**
 * Created by JetBrains PhpStorm.
 * User: zubair
 * Date: 4/21/14
 * Time: 10:34 PM
 * To change this template use File | Settings | File Templates.
 */
namespace accountica\controllers;
use Furina\mvc\controller\Response;

class AppController extends \Furina\mvc\controller\Controller {

    public function components() {
        return array_merge(parent::components(), array('Router', 'Session', 'Cookie', 'Auth', 'Pagination'));
    }

    public function handleRequest($request, $handler = null)
    {
        $response = new Response();
        $function = $this->__map($request, $handler);

        $template = str_replace('\\controllers\\', '\\templates\\', static::$__class__);
        $template .= '/' .$function.'.php';

        $output = $request->namedParam('output');
        if ( $output == 'json')
            $response->render('json');
        else
            $response->render('template', $template);

        $response->set('data', $request->data);
        $response->set('errors', $request->data);

        $this->__execute($request, $response, $function);
        return $response;
    }
}
