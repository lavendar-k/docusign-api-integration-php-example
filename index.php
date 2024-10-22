<?php

require 'vendor/autoload.php';

use DocusignIntegration\DocusignClient;
use DocusignIntegration\DocusignService;
use DocusignIntegration\CallbackHandler;

// Initialize DocusignClient
$docusignClient = new DocusignClient();

// Initialize DocusignService
$docusignService = new DocusignService($docusignClient);

// Simulate sending a document for signature
$filePath = __DIR__ . '/tests/sample.pdf'; // Ensure this file exists
$email = 'xcroftsolution@gmail.com';

$docusignService->readDocument($filePath);
$docusignService->addSignatureField($email, 'Test Signer', 200, 200);

$envelopeSummary = $docusignService->sendDocumentForSignature("Please add signature to this document");

echo "Document sent for signature. Envelope ID: " . $envelopeSummary->getEnvelopeId() . "\n";

?>