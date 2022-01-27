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

    public function __construct(Connexion $connexion, int $ref, array $attributes = [])
    {
        $this->connexion = $connexion;
        $this->ref = $ref;
        $this->attributes = $attributes;

        $this->creation_date = $this->setCreationDate();
        $this->file_extension = $this->setFileExtension();
        $this->file_size = $this->setFileSize();
        $this->modification_date = $this->setModificationDate();
//        $this->coord = $this->setCoord();
        $this->previews = $this->setPreviews();
        $this->checksum = $this->setChecksum();

    }


    private function getData(string $function): self
    {
        $httpClient = HttpClient::create();

        $query_url = "user=" . $this->connexion->getUser() . "&function=".$function."&resource=".$this->ref;

        try {
            $response = $httpClient->request(
                'GET',
                $this->connexion->getPath().$query_url.'&sign='.$this->connexion->getSign($query_url),
                $this->connexion->getAccessParameters()
            );
            $statusCode = $response->getStatusCode();

            if ($statusCode !== 200) {
                return $this;
            }

            $attributes = json_decode($response->getContent(), true);

        } catch (TransportExceptionInterface) {
            return $this;
        }

            $this->attributes = array_merge($this->attributes, $attributes);

        return $this;
    }

    public function setFieldData(): self
    {
        $this->getData('get_resource_field_data');
        return $this;
    }

    private function setCreationDate(): DateTime
    {
        $date = $this->attributes['creation_date'];
        return DateTime::createFromFormat('Y-m-d H:i:s', $date);
    }

    private function setModificationDate(): DateTime
    {
        $date = $this->attributes['modified'];
        return DateTime::createFromFormat('Y-m-d H:i:s', $date);
    }

    private function setRef(): int
    {
        return $this->attributes['ref'];
    }

    private function setFileExtension(): string
    {
        return $this->attributes['file_extension'];
    }

    private function setFileSize(): int
    {
        return $this->attributes['file_size'];
    }

    private function setPreviews(): array
    {
        $previews = [];

        foreach ($this->attributes as $attribute => $value)
        {
            if(str_contains($attribute, 'url_')) {
                $name = str_replace('url_', '', $attribute);
                $previews[$name] = $value;
            }
        }

        return $previews;
    }

    private function setCoord(): array
    {
        return [$this->attributes['geo_lat'], $this->attributes['geo_long']];
    }

    private function setChecksum(): string
    {
        return $this->attributes['file_checksum'];
    }
}