<?php
require_once dirname(__DIR__) . '/middleware/BaseMiddleware.php';

class CartMiddleware extends BaseMiddleware
{
    public function  __construct()
    {
        parent::__construct('costumer');
        parent::verifyUser();
    }
}
