<?php

namespace DocusignIntegration;

use DocuSign\eSign\Api\EnvelopesApi;
use DocuSign\eSign\Model\EnvelopeDefinition;
use DocuSign\eSign\Model\Document;
use DocuSign\eSign\Model\Signer;
use DocuSign\eSign\Model\SignHere;
use DocuSign\eSign\Model\Recipients;
use DocuSign\eSign\Client\ApiException;

class DocusignService
{
    private $client;
    private $document;
    private $signer;
    private $signHere;
    private $recipients;

    public function __construct(DocusignClient $client)
    {
        $this->client = $client;
    }

    public function readDocument($filePath, $name = "Test Document")
    {
        $this->document = new Document();
        $this->document->setDocumentBase64(base64_encode(file_get_contents($filePath)));
        $this->document->setName($name);
        $this->document->setFileExtension('pdf');
        $this->document->setDocumentId('1');
    }

    public function addSignatureField($email, $name = "Test Signer", $xPos, $yPos, $docId = '1', $pageNum = '1')
    {
        $this->signer = new Signer();
        $this->signer->setEmail($email);
        $this->signer->setName($name);
        $this->signer->setRecipientId('1');
        $this->signHere = new SignHere();
        $this->signHere->setXPosition($xPos);
        $this->signHere->setYPosition($yPos);
        $this->signHere->setDocumentId($docId);
        $this->signHere->setPageNumber($pageNum);
        $this->signer->setTabs(['signHereTabs' => [$this->signHere]]);

        $this->recipients = new Recipients();
        $this->recipients->setSigners([$this->signer]);
    }

    public function sendDocumentForSignature($comment = "Please Sign this Document")
    {
        $envelopeApi = new EnvelopesApi($this->client->getApiClient());

        $envelopeDefinition = new EnvelopeDefinition();
        $envelopeDefinition->setEmailSubject($comment);
        $envelopeDefinition->setDocuments([$this->document]);
        $envelopeDefinition->setRecipients($this->recipients);
        $envelopeDefinition->setStatus('sent');

        return $envelopeApi->createEnvelope('me', $envelopeDefinition);
    }

    public function downloadSignedDocuments($envelopId)
    {
        $envelopeApi = new EnvelopesApi($this->client->getApiClient());

        try {
            $documentsList = $envelopeApi->listDocuments($_ENV['DOCUSIGN_API_ACCOUNT_ID'], $envelopId);

            $documents = $documentsList->getEnvelopeDocuments();

            foreach ($documents as $document) {
                $doc = $envelopeApi->getDocument($_ENV['DOCUSIGN_API_ACCOUNT_ID'], $document->getDocumentId(), $envelopId);
                if ($document->getType() === "content") {
                    file_put_contents(__DIR__ . "/../download/" . $document->getName() . ".pdf", file_get_contents($doc->getRealPath()));
                }
            }

            echo "Documents downloaded successfully\n";
        } catch (ApiException $e) {
            echo "Error downloading documents: " . $e->getMessage() . "\n";
        }
    }
}
