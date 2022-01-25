<?php

namespace UrbanBrussels\ResourcespaceApi;

use DateTime;

class Resource
{
    public int $resource;
    private array $attributes_array;
    public DateTime $creation_date;

    public function __construct(int $resource, array $attributes_array = [])
    {
        $this->attributes_array = $attributes_array;

        $this->creation_date = $this->setCreationDate();

    }


    private function setCreationDate(): DateTime
    {
        $date = $this->attributes_array['creation_date'];
        return DateTime::createFromFormat('Y-m-d H:i:s', $date);
    }
}