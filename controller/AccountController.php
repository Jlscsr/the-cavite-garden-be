<?php
require_once dirname(__DIR__) . '/helpers/JWTHelper.php';
require_once dirname(__DIR__) . '/helpers/ResponseHelper.php';
require_once dirname(__DIR__) . '/helpers/HeaderHelper.php';
require_once dirname(__DIR__) . '/helpers/CookieManager.php';

require_once dirname(__DIR__) . '/model/AccountsModel.php';

class AccountController
{
    private $jwt;
    private $accounts_model;
    private $cookie_manager;

    public function __construct($pdo)
    {
        $this->jwt = new JWTHelper();
        $this->accounts_model = new AccountsModel($pdo);
        $this->cookie_manager = new CookieManager($this->jwt);
    }

    public function getAccountById()
    {

        HeaderHelper::setHeaders();

        $headers = getallheaders();

        if (!isset($headers['Cookie'])) {
            $this->cookie_manager->resetCookieHeader();
            ResponseHelper::sendUnauthrizedResponse('Invalid Token');
            return;
        }

        $token = $headers['Cookie'];
        $token = str_replace("tcg_access_token=", "", $token);

        $customer_id = $this->jwt->decodeData($token)->id;

        $response = $this->accounts_model->getAccountById($customer_id);

        if (empty($response)) {
            ResponseHelper::sendSuccessResponse([], 'No Account found', 200);
            return;
        }

        ResponseHelper::sendSuccessResponse($response, 'Successfully fetched account', 200);
    }

    public function addNewUserAddress($data)
    {
        HeaderHelper::setHeaders();
        if (!is_array($data) || empty($data)) {
            ResponseHelper::sendErrorResponse("Invalid data or data is empty", 400);
            return;
        }

        $headers = getallheaders();

        if (!isset($headers['Cookie'])) {
            $this->cookie_manager->resetCookieHeader();
            ResponseHelper::sendUnauthrizedResponse('Invalid Token');
            return;
        }

        $token = $headers['Cookie'];
        $token = str_replace("tcg_access_token=", "", $token);

        $customer_id = $this->jwt->decodeData($token)->id;

        $response = $this->accounts_model->addNewUserAddress($customer_id, $data);

        if (!$response) {
            ResponseHelper::sendErrorResponse($response, 500);
            return;
        }

        ResponseHelper::sendSuccessResponse(null, 'Address added successfully', 201);
    }
}
