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
    public string $image_color;

    public function __construct(Connexion $connexion, int $ref, ?string $language = null, array $attributes = [])
    {
        $this->connexion = $connexion;
        $this->attributes = $attributes;
        $this->language = $language;

        if(empty($this->attributes)) {
            $this->attributes = $this->getData('get_resource_data');
            $this->original_filename = $this->setOriginalFilename();
        }

        $this->ref = $this->setRef();

        $this->creation_date = $this->setCreationDate();
        $this->file_extension = $this->setFileExtension();
        $this->file_size = $this->setFileSize();
        $this->modification_date = $this->setModificationDate();
//        $this->coord = $this->setCoord();
        $this->previews = $this->setPreviews();
        $this->checksum = $this->setChecksum();
        $this->created_by = $this->setCreatedBy();
        $this->resource_type = $this->setResourceType();
        $this->has_image = $this->setHasImage();
        $this->image_color = $this->setImageColor();


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
        $original_filename = $this->attributes['original_filename'];
        unset($this->attributes['original_filename']);
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

    private function setImageColor(): string
    {
        $image_color = sprintf("#%02x%02x%02x", $this->attributes['image_red'], $this->attributes['image_green'], $this->attributes['image_blue']);
        unset($this->attributes['image_red'], $this->attributes['image_green'], $this->attributes['image_blue']);
        return $image_color;
    }
}