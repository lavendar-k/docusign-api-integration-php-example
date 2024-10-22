<?php

namespace DocusignIntegration;

use DocuSign\eSign\Api\EnvelopesApi;

class CallbackHandler
{
    private $client;

    public function __construct(DocusignClient $client)
    {
        $this->client = $client;
    }

    public function handleCallback($data)
    {
        $envelopeId = $data['envelopeId'];
        $envelopeApi = new EnvelopesApi($this->client->getApiClient());
        $documents = $envelopeApi->listDocuments($envelopeId);

        foreach ($documents->getEnvelopeDocuments() as $document) {
            $fileContent = $envelopeApi->getDocument($envelopeId, $document->getDocumentId());
            file_put_contents("signed_documents/{$document->getName()}", $fileContent);
        }

        mail('test_email@example.com', 'Document Signed', 'The document has been signed.');
    }
}
