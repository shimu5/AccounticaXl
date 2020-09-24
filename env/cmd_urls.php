<?php
return array(
    '/accountica' => array( 'patterns'=> include(CMD_ROOT.'/apps/accountica/urls.php')),
    '/scheduler' => array( 'patterns'=> include(CMD_ROOT.'/apps/scheduler/urls.php')),
    '/samples' => array('patterns'=> include(CMD_ROOT.'/apps/samples/urls.php')),
    '/furina' => array('patterns'=> include(CMD_ROOT.'/vendor/Furina/app/urls.php')),
    '/?$', array('controller'=>'\\accountica\\controllers\\Dashboard', 'action'=>'index')
);
