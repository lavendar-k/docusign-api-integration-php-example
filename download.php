<?php

require 'vendor/autoload.php';

use DocusignIntegration\DocusignClient;
use DocusignIntegration\DocusignService;
use DocusignIntegration\CallbackHandler;

// Initialize DocusignClient
$docusignClient = new DocusignClient();

// Initialize DocusignService
$docusignService = new DocusignService($docusignClient);

$docusignService->downloadSignedDocuments('eb6633cc-ccce-487b-86d4-831f676c4d56');
