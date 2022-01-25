<?php

namespace UrbanBrussels\ResourcespaceApi;

use DateTime;

class Resource
{
    public int $ref;
    private array $attributes_array;
    public DateTime $creation_date;
    public DateTime $modification_date;
    public string $file_extension;
    public int $file_size;
    public array $previews;

    public function __construct(array $attributes_array = [])
    {
        $this->attributes_array = $attributes_array;

        $this->ref = $this->setRef();
        $this->creation_date = $this->setCreationDate();
        $this->file_extension = $this->setFileExtension();
        $this->file_size = $this->setFileSize();
        $this->modification_date = $this->setModificationDate();
        $this->previews = $this->setPreviews();
    }

    private function setCreationDate(): DateTime
    {
        $date = $this->attributes_array['creation_date'];
        return DateTime::createFromFormat('Y-m-d H:i:s', $date);
    }

    private function setModificationDate(): DateTime
    {
        $date = $this->attributes_array['modified'];
        return DateTime::createFromFormat('Y-m-d H:i:s', $date);
    }

    private function setRef(): int
    {
        return $this->attributes_array['ref'];
    }

    private function setFileExtension(): string
    {
        return $this->attributes_array['file_extension'];
    }

    private function setFileSize(): int
    {
        return $this->attributes_array['file_size'];
    }

    private function setPreviews(): array
    {
        $previews = [];

        foreach ($this->attributes_array as $attribute => $value)
        {
            if(str_contains($attribute, 'url_')) {
                $name = str_replace('url_', '', $attribute);
                $previews[$name] = $value;
            }
        }

        return $previews;
    }
}