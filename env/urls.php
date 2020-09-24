<?php
return array(
    '/accountica' => array( 'patterns'=> include('../apps/accountica/urls.php')),
    '/scheduler' => array( 'patterns'=> include('../apps/scheduler/urls.php')),
    '/samples' => array('patterns'=> include('../apps/samples/urls.php')),
    '/furina' => array('patterns'=> include('../vendor/Furina/app/urls.php')),
    '/?$', array('controller'=>'\\accountica\\controllers\\Dashboard', 'action'=>'index')
);
