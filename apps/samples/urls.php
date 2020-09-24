<?php
return array(
    // base url
    '(/)?$' => array('controller'=> 'samples\\controllers\\Dashboard'),

    '/(?P<controller>[^/]+)(/(?P<action>[^/]+)?)?(/(?P<params>.+))?$' => array(
        'filter'=>array('controller'=>array('\\Furina\\core\\Inflector', 'classify')),
        'controller'=> 'samples\\controllers\\$controller',
    )
);

