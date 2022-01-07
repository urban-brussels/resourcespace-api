<?php
namespace App\Service;

use Symfony\Component\HttpClient\HttpClient;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;

class RspaceApi
{
    private string $private_key;
    private string $user;
    private string $path;
    private string $query_url;
    private array $access_parameters;
    private string $function;
    private string $search;
    private array $search_parameters;

    public function __construct(string $private_key, string $user, string $path, array $access_parameters = [])
    {
        $this->private_key = $private_key;
        $this->user = $user;
        $this->path = $path;
        $this->access_parameters = $access_parameters;
    }

    /**
     * @return \stdClass|null
     */
    public function getResults(): ?array
    {
        $httpClient = HttpClient::create();

        try {
                $response = $httpClient->request('GET', $this->path.$this->getQueryUrl().'&sign='.$this->getSign(), $this->access_parameters);
                $statusCode = $response->getStatusCode();

            if ($statusCode !== 200) {
                return null;
            }

            return json_decode($response->getContent(), false);

        } catch (TransportExceptionInterface) {
            return null;
        }
    }

    public function getQueryFields(): array
    {
        $fields = array(
            'user' => $this->user,
            'function' => $this->function,
        );

        if ($this->search) {
            $fields['search'] = $this->search;
        }

        if ($this->search_parameters) {
            foreach ($this->search_parameters as $k => $v) {
                $fields[$k] = $v;
            }
            $fields['search'] = $this->search;
        }

        return $fields;
    }
    
    public function doSearch(string $search, array $search_parameters = []): self {
        $this->function = 'do_search';
        $this->search = $search;
        $this->search_parameters = $search_parameters;
        return $this;
    }

    public function searchGetPreviews(string $search, array $search_parameters = ['getsizes' => 'thm', 'fetchrows' => 50]): self {
        $this->function = 'search_get_previews';
        $this->search = $search;
        $this->search_parameters = $search_parameters;
        return $this;
    }

    public function getQueryUrl(): string
    {
        return http_build_query($this->getQueryFields());
    }

    private function getSign(): string
    {
        return hash("sha256",$this->private_key . $this->getQueryUrl());
    }

}
