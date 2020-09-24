<?php
return array(
    // base url
    '(/)?$' => array('controller'=> 'accountica\\controllers\\Dashboard'),

    // api entry points

    '/api/1/' => array('patterns' => array(
        '(?P<controller>[^/]+)(/(?P<action>[^/]+)?)?(/(?P<params>.+))?$' => array(
        'filter'=>array('controller'=>array('\\Furina\\core\\Inflector', 'classify')),
        'controller'=> 'accountica\\controllers\\$controller',
        'named_params'=> array(
            'output' => 'json',
            'api_version'=> '1'
        )
        //'generate' => array('required'=> array('app'=>'accountica', 'controller', 'action'), 'format'=>'/accountica/$controller/$action')
        //'action' => 'index',
    ))),

    '/(?P<controller>[^/]+)(/((?P<action>[^/]+)(/((?P<params>.+)?)?)?)?)?$' => array(
        'filter'=>array('controller'=>array('\\Furina\\core\\Inflector', 'classify')),
        'controller'=> 'accountica\\controllers\\$controller',
        //'action' => 'index',
    ),
//    '/(?P<controller>[^/]+)(/(?P<action>[^/]+)?)?(/(?P<params>.+))?$' => array(
//        'filter'=>array('controller'=>array('\\Furina\\core\\Inflector', 'classify')),
//        'controller'=> 'accountica\\controllers\\$controller',
//        //'action' => 'index',
//    )
);

