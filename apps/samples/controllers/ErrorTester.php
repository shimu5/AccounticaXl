<?php
namespace samples\controllers;

class ErrorTester extends \Furina\mvc\controller\Controller {
    public function anonIndex() {

    }
    public function anonUserTriggered() {
        trigger_error('E_UER_NOTICE triggered with trigger_error function');
    }
    public function anonRequire() {
        require 'invalid file';
    }
    public function anonInclude() {
        include 'invalid file';
    }
    public function anonParseError() {
        include __DIR__ . '/ErrorTesterHelpers/ParseError.php';
    }
    public function anonParseError2() {
        include __DIR__ . '/ErrorTesterHelpers/ParseError2.php';
    }
    public function anonParseError3() {
        include __DIR__ . '/ErrorTesterHelpers/ParseError3.php';
    }
    public function anonUndefinedFunction() {
        call_to_undefined_function();
    }
    public function anonNonObject() {
        $a = 10;
        $a->function_call();
    }
    public function anonNotice() {
        echo $a;
    }
    public function anonDivisionByZero() {
        $a = 1/0;
    }
    public function anonDeprecated($request, $response) {
        $response->set('splitted', split(' ', 'This is a test string'));
    }
    public function anonException() {
        throw new \Exception("Testing exception throwing");
    }
    public function anonConsecutive() {
        $a->nofun();
    }
}
