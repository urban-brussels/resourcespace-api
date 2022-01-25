<?php

namespace UrbanBrussels\ResourcespaceApi;

use DateTime;

class Resource
{
    public int $resource;
    private array $attributes_array;
    public DateTime $creation_date;
    public DateTime $modification_date;
    public string $file_extension;
    public int $file_size;
    public string $url_thumbnail;

    public function __construct(array $attributes_array = [])
    {
        $this->attributes_array = $attributes_array;

        $this->creation_date = $this->setCreationDate();
        $this->file_extension = $this->setFileExtension();
        $this->file_size = $this->setFileSize();
        $this->modification_date = $this->setModificationDate();
        $this->url_thumbnail = $this->setUrlThumbnail();
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

    private function setResource(): int
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

    private function setUrlThumbnail(): string
    {
        return $this->attributes_array['url_thm'];
    }
}