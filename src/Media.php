<?php

namespace UrbanBrussels\ResourcespaceApi;
use DateTime;

class Media
{
    public int $ref;
    public ?string $language;
    public DateTime $creation_date;
    public DateTime $modification_date;
    public string $file_extension;
    public int $file_size;
    public array $previews;
    public array $coord;
    public string $checksum;
    public string $original_filename;
    public int $created_by;
    public bool $has_image;
    public int $resource_type;

    public function __construct(int $ref, ?string $language = null)
    {
        $this->setRef($ref);
        $this->setLanguage($language);
    }

    public function getRef(): int
    {
        return $this->ref;
    }

    public function setRef(int $ref): void
    {
        $this->ref = $ref;
    }

    public function getLanguage(): ?string
    {
        return $this->language;
    }

    public function setLanguage(?string $language): void
    {
        $this->language = $language;
    }

    public function getCreationDate(): DateTime
    {
        return $this->creation_date;
    }

    public function setCreationDate(DateTime $creation_date): void
    {
        $this->creation_date = $creation_date;
    }

    public function getModificationDate(): DateTime
    {
        return $this->modification_date;
    }

    public function setModificationDate(DateTime $modification_date): void
    {
        $this->modification_date = $modification_date;
    }

    public function getFileExtension(): string
    {
        return $this->file_extension;
    }

    public function setFileExtension(string $file_extension): void
    {
        $this->file_extension = $file_extension;
    }

    public function getFileSize(): int
    {
        return $this->file_size;
    }

    public function setFileSize(int $file_size): void
    {
        $this->file_size = $file_size;
    }

    public function getPreviews(): array
    {
        return $this->previews;
    }

    public function setPreviews(array $previews): void
    {
        $this->previews = $previews;
    }

    public function getCoord(): array
    {
        return $this->coord;
    }

    public function setCoord(array $coord): void
    {
        $this->coord = $coord;
    }

    public function getChecksum(): string
    {
        return $this->checksum;
    }

     public function setChecksum(string $checksum): void
    {
        $this->checksum = $checksum;
    }

    public function getOriginalFilename(): string
    {
        return $this->original_filename;
    }

    public function setOriginalFilename(string $original_filename): void
    {
        $this->original_filename = $original_filename;
    }

    public function getCreatedBy(): int
    {
        return $this->created_by;
    }

    public function setCreatedBy(int $created_by): void
    {
        $this->created_by = $created_by;
    }

    public function isHasImage(): bool
    {
        return $this->has_image;
    }

    public function setHasImage(bool $has_image): void
    {
        $this->has_image = $has_image;
    }

    public function getResourceType(): int
    {
        return $this->resource_type;
    }

    public function setResourceType(int $resource_type): void
    {
        $this->resource_type = $resource_type;
    }


}