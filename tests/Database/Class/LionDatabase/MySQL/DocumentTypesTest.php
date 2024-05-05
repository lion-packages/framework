<?php

declare(strict_types=1);

namespace Tests\Database\Class\LionDatabase\MySQL;

use Database\Class\LionDatabase\MySQL\DocumentTypes;
use Lion\Bundle\Interface\CapsuleInterface;
use Lion\Test\Test;

class DocumentTypesTest extends Test
{
    const IDDOCUMENT_TYPES = 1;
    const DOCUMENT_TYPES_NAME = 'Passport';

    private DocumentTypes $documentTypes;

    protected function setUp(): void
    {
        $this->documentTypes = new DocumentTypes();
    }

    public function testGetIddocumentTypes(): void
    {
        $this->documentTypes->setIddocumentTypes(self::IDDOCUMENT_TYPES);

        $this->assertSame(self::IDDOCUMENT_TYPES, $this->documentTypes->getIddocumentTypes());
    }

    public function testSetIddocumentTypes(): void
    {
        $this->assertInstances($this->documentTypes->setIddocumentTypes(self::IDDOCUMENT_TYPES), [
            DocumentTypes::class,
            CapsuleInterface::class,
        ]);

        $this->assertSame(self::IDDOCUMENT_TYPES, $this->documentTypes->getIddocumentTypes());
    }

    public function testGetDocumentTypesName(): void
    {
        $this->documentTypes->setDocumentTypesName(self::DOCUMENT_TYPES_NAME);

        $this->assertSame(self::DOCUMENT_TYPES_NAME, $this->documentTypes->getDocumentTypesName());
    }

    public function testSetDocumentTypesName(): void
    {
        $this->assertInstances($this->documentTypes->setDocumentTypesName(self::DOCUMENT_TYPES_NAME), [
            DocumentTypes::class,
            CapsuleInterface::class,
        ]);

        $this->assertSame(self::DOCUMENT_TYPES_NAME, $this->documentTypes->getDocumentTypesName());
    }
}
