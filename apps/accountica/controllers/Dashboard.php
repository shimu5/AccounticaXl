<?php
namespace accountica\controllers;
use Furina\mvc\controller\Response;

class Dashboard extends \Furina\mvc\controller\Controller {

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

        $this->__execute($request, $response, $function);
        return $response;
    }

    public function anonIndex($request, $response) {
        //var_dump($request);
        $response->set('world', 'hello');
    }

}

