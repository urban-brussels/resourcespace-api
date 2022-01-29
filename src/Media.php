<?php

namespace UrbanBrussels\ResourcespaceApi;
use DateTime;
use Symfony\Component\HttpClient\HttpClient;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;

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

    public function __construct(int $ref)
    {
        $this->setRef($ref);
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


    protected function addData(string $function, Connexion $connexion, ?string $language = null): array
    {
        $httpClient = HttpClient::create();
        $query_url = "user=" . $connexion->getUser() . "&function=".$function."&resource=".$this->getRef().(!is_null($language) ? '&language='.$language : '');

        try {
            $response = $httpClient->request(
                'GET',
                $connexion->getPath().$query_url.'&sign='.$connexion->getSign($query_url),
                $connexion->getAccessParameters()
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

    public function __set($property, $value)
    {
            $this->$property= $value;
    }

    public function __isset($name){
        $getter = 'get'.ucfirst($name);
        return method_exists($this, $getter) && !is_null($this->$getter());
    }

    public function __get($property)
    {
        return $this->$property;
    }

    public function addFieldsData(Connexion $connexion, ?string $language = null): self {
        $fields = $this->addData('get_resource_field_data', $connexion, $language);

        foreach($fields as $field) {
            $this->__set($field['name'], $field['value']);
        }

        return $this;
    }

    public function addPreviewsData(Connexion $connexion): self {
        $fields = $this->addData('get_resource_all_image_sizes', $connexion);
        $previews = [];

        foreach($fields as $field) {
            $previews[$field['size_code']] = $field['url'];
        }

        $this->setPreviews($previews);

        return $this;
    }
}