<?php

declare(strict_types=1);

namespace Tests\Database\Class\LionDatabase\MySQL;

use Database\Class\LionDatabase\MySQL\DocumentTypes;
use Lion\Bundle\Interface\CapsuleInterface;
use PHPUnit\Framework\Attributes\Test as Testing;
use Tests\Test;

class DocumentTypesTest extends Test
{
    private const string ENTITY = 'document_types';
    private const int IDDOCUMENT_TYPES = 1;
    private const string DOCUMENT_TYPES_NAME = 'Passport';

    private DocumentTypes $documentTypes;

    protected function setUp(): void
    {
        $this->documentTypes = new DocumentTypes();
    }

    #[Testing]
    public function capsule(): void
    {
        $this->assertCapsule($this->documentTypes, self::ENTITY);
    }

    #[Testing]
    public function getIddocumentTypes(): void
    {
        $this->documentTypes->setIddocumentTypes(self::IDDOCUMENT_TYPES);

        $this->assertSame(self::IDDOCUMENT_TYPES, $this->documentTypes->getIddocumentTypes());
    }

    #[Testing]
    public function setIddocumentTypes(): void
    {
        $this->assertInstances($this->documentTypes->setIddocumentTypes(self::IDDOCUMENT_TYPES), [
            DocumentTypes::class,
            CapsuleInterface::class,
        ]);

        $this->assertSame(self::IDDOCUMENT_TYPES, $this->documentTypes->getIddocumentTypes());
    }

    #[Testing]
    public function getDocumentTypesName(): void
    {
        $this->documentTypes->setDocumentTypesName(self::DOCUMENT_TYPES_NAME);

        $this->assertSame(self::DOCUMENT_TYPES_NAME, $this->documentTypes->getDocumentTypesName());
    }

    #[Testing]
    public function setDocumentTypesName(): void
    {
        $this->assertInstances($this->documentTypes->setDocumentTypesName(self::DOCUMENT_TYPES_NAME), [
            DocumentTypes::class,
            CapsuleInterface::class,
        ]);

        $this->assertSame(self::DOCUMENT_TYPES_NAME, $this->documentTypes->getDocumentTypesName());
    }
}
