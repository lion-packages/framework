<?php

declare(strict_types=1);

namespace Database\Class\LionDatabase\MySQL;

use Lion\Bundle\Interface\CapsuleInterface;

/**
 * Capsule for the 'DocumentTypes' entity
 *
 * @property int $iddocument_types [Property for iddocument_types]
 * @property string $document_types_name [Property for document_types_name]
 *
 * @package Database\Class\LionDatabase\MySQL
 */
class DocumentTypes implements CapsuleInterface
{
	/**
	 * [Property for iddocument_types]
	 *
	 * @var int|null $iddocument_types
	 */
	private ?int $iddocument_types = null;

	/**
	 * [Property for document_types_name]
	 *
	 * @var string|null $document_types_name
	 */
	private ?string $document_types_name = null;

	/**
	 * {@inheritdoc}
	 * */
	public function jsonSerialize(): array
	{
		return get_object_vars($this);
	}

	/**
	 * {@inheritdoc}
	 * */
	public function capsule(): DocumentTypes
	{
		$this
			->setIddocumentTypes(request->iddocument_types ?? null)
			->setDocumentTypesName(request->document_types_name ?? null);

		return $this;
	}

    /**
     * Getter method for 'iddocument_types'
     *
     * @return int|null
     */
    public function getIddocumentTypes(): ?int
    {
        return $this->iddocument_types;
    }

    /**
     * Setter method for 'iddocument_types'
     *
     * @param int|null $iddocument_types
     *
     * @return DocumentTypes
     */
    public function setIddocumentTypes(?int $iddocument_types = null): DocumentTypes
    {
        $this->iddocument_types = $iddocument_types;

        return $this;
    }

    /**
     * Getter method for 'document_types_name'
     *
     * @return string|null
     */
    public function getDocumentTypesName(): ?string
    {
        return $this->document_types_name;
    }

    /**
     * Setter method for 'document_types_name'
     *
     * @param string|null $document_types_name
     *
     * @return DocumentTypes
     */
    public function setDocumentTypesName(?string $document_types_name = null): DocumentTypes
    {
        $this->document_types_name = $document_types_name;

        return $this;
    }
}