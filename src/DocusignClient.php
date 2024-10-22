<?php

namespace DocusignIntegration;

use DocuSign\eSign\Client\ApiClient;
use DocuSign\eSign\Configuration;
use Dotenv\Dotenv;
use Throwable;

class DocusignClient
{
    private $apiClient;

    public function __construct()
    {
        $dotenv = Dotenv::createImmutable(__DIR__ . '/../');
        $dotenv->load();

        $config = new Configuration();
        $config->setHost($_ENV['DOCUSIGN_BASE_URL']);
        $this->apiClient = new ApiClient($config);

        $this->authenticate();
    }

    private function authenticate()
    {
        try {
            $this->apiClient->getOAuth()->setOAuthBasePath($_ENV['DOCUSIGN_AUTHORIZATION_SERVER']);
            $privateKey = file_get_contents( __DIR__."/../".$_ENV['DOCUSIGN_PRIVATE_KEY_PATH']);
            $jwt_scope = "signature impersonation";
            $response = $this->apiClient->requestJWTUserToken( $aud = $_ENV['DOCUSIGN_INTEGRATION_KEY'], $aud = $_ENV['DOCUSIGN_USER_ID'], $aud = $privateKey, $aud = $jwt_scope);
            $token = $response[0];
            $this->apiClient->getConfig()->setAccessToken($token->getAccessToken());
        } catch ( Throwable $e ) {
            if (strpos($e->getMessage(), "consent_required") !== false) {
                $authorizationURL = 'https://account-d.docusign.com/oauth/auth?'
                .http_build_query(
                    [
                        'scope' => $jwt_scope,
                        'client_id' => $_ENV['DOCUSIGN_INTEGRATION_KEY'],
                        'redirect_uri' => 'https://httpbin.org/get',
                        'response_type' => 'code'
                    ]
                );

                echo "It appears that you are using this integration key for the first time. Pelase visit the following link to grant consent authoriziation\n\n";
                echo $authorizationURL;

                exit();
            }
        }
    }

    public function getApiClient()
    {
        return $this->apiClient;
    }
}
