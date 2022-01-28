<?php

namespace UrbanBrussels\ResourcespaceApi;

use DateTime;
use Symfony\Component\HttpClient\HttpClient;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;

class Resource
{
    public int $ref;
    private array $attributes;
    public DateTime $creation_date;
    public DateTime $modification_date;
    public string $file_extension;
    public int $file_size;
    public array $previews;
    private Connexion $connexion;
    public array $coord;
    public string $checksum;
    public ?string $language;
    public string $original_filename;
    public int $created_by;
    public bool $has_image;
    public int $resource_type;

    public function __construct(Connexion $connexion, int $ref, ?string $language = null, bool $details = true, array $attributes = [])
    {
        $this->connexion = $connexion;
        $this->attributes = $attributes;
        $this->language = $language;
        $this->ref = $ref;

        if ($details === true) {
            $this->attributes = $this->getData('get_resource_data');
            $this->setFieldData();
            $this->coord = $this->setCoord();
        }
        else {
            if(empty($this->attributes)) {
                $this->attributes = $this->getData('get_resource_data');
                $this->coord = $this->setCoord();
            }
        }

        if(isset($this->attributes['original_filename']) || isset($this->attributes['originalfilename'])) {
            $this->original_filename = $this->setOriginalFilename();
        }

        $this->ref = $this->setRef();

        $this->creation_date = $this->setCreationDate();
        $this->file_extension = $this->setFileExtension();
        $this->file_size = $this->setFileSize();
        $this->modification_date = $this->setModificationDate();

        $this->previews = $this->setPreviews();
        $this->checksum = $this->setChecksum();
        $this->created_by = $this->setCreatedBy();
        $this->resource_type = $this->setResourceType();
        $this->has_image = $this->setHasImage();
    }


    private function getData(string $function): array
    {
        $httpClient = HttpClient::create();

        $query_url = "user=" . $this->connexion->getUser() . "&function=".$function."&resource=".$this->ref.(isset($this->language) ? '&language='.$this->language : '');

        try {
            $response = $httpClient->request(
                'GET',
                $this->connexion->getPath().$query_url.'&sign='.$this->connexion->getSign($query_url),
                $this->connexion->getAccessParameters()
            );
            $statusCode = $response->getStatusCode();

            if ($statusCode !== 200) {
                return [];
            }

            $attributes = json_decode($response->getContent(), true);

        } catch (TransportExceptionInterface) {
            return [];
        }

            return $attributes;

    }

    public function setFieldData(): self
    {
        $fields = $this->getData('get_resource_field_data');

        foreach($fields as $field) {
            $this->attributes[$field['name']] = $field['value'];
        }

        return $this;
    }

    private function setCreationDate(): DateTime
    {
        $date = $this->attributes['creation_date'];
        unset($this->attributes['creation_date']);
        return DateTime::createFromFormat('Y-m-d H:i:s', $date);
    }

    private function setModificationDate(): DateTime
    {
        $date = $this->attributes['modified'];
        unset($this->attributes['modified']);
        return DateTime::createFromFormat('Y-m-d H:i:s', $date);
    }

    private function setRef(): int
    {
        $ref = $this->attributes['ref'];
        unset($this->attributes['ref']);
        return $ref;
    }

    private function setFileExtension(): string
    {
        $file_extension = $this->attributes['file_extension'];
        unset($this->attributes['file_extension']);
        return $file_extension;
    }

    private function setFileSize(): int
    {
        $file_size = $this->attributes['file_size'];
        unset($this->attributes['file_size']);
        return $file_size;
    }

    private function setPreviews(): array
    {
        $previews = [];

        foreach ($this->attributes as $attribute => $value)
        {
            if(str_contains($attribute, 'url_')) {
                $name = str_replace('url_', '', $attribute);
                $previews[$name] = $value;
                unset($this->attributes[$attribute]);
            }
        }

        return $previews;
    }

    private function setCoord(): array
    {
        $coord = [$this->attributes['geo_lat'], $this->attributes['geo_long']];
        if($this->attributes['geo_lat'] === "" or $this->attributes['geo_long'] === "") {
            $coord = [];
        }
        unset($this->attributes['geo_lat'], $this->attributes['geo_long']);
        return $coord;
    }

    private function setChecksum(): string
    {
        $checksum = $this->attributes['file_checksum'];
        unset($this->attributes['file_checksum']);
        return $checksum;
    }


    private function setOriginalFilename(): string
    {
        $original_filename = $this->attributes['original_filename'] ?? $this->attributes['originalfilename'];
        unset($this->attributes['original_filename'], $this->attributes['originalfilename']);
        return $original_filename;
    }

    private function setCreatedBy(): int
    {
        $created_by = $this->attributes['created_by'];
        unset($this->attributes['created_by']);
        return $created_by;
    }

    private function setResourceType(): int
    {
        $resource_type = $this->attributes['resource_type'];
        unset($this->attributes['resource_type']);
        return $resource_type;
    }

    private function setHasImage(): bool
    {
        $has_image = $this->attributes['has_image'] === "1";
        unset($this->attributes['has_image']);
        return $has_image;
    }
}