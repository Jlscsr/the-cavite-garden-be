<?php
require_once dirname(__DIR__) . '/middleware/BaseMiddleware.php';
class TransactionMiddleware extends BaseMiddleware
{

    public function __construct()
    {
        parent::__construct('customer');
        parent::verifyUser();
    }
}
