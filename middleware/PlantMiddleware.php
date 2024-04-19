<?php
require_once dirname(__DIR__) . '/middleware/BaseMiddleware.php';
class PlantMiddleware extends BaseMiddleware
{

    public function __construct()
    {
        parent::__construct('admin');
        parent::verifyUser();
    }
}
