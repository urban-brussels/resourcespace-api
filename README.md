# Resourcespace Api
PHP wrapper for ResourceSpace API (Open Source Digital Asset Management).    
Method names and attributes based on the RS documentation here: [https://www.resourcespace.com/knowledge-base/api/](https://www.resourcespace.com/knowledge-base/api/)    
More features coming soon.

## Installation

```sh
composer require urban-brussels/resourcespace-api
```

## Usage

```php 
use urban_brussels\resourcespace_api\ResourcespaceApi;

$path = 'https://media.example.com/api/?'; // Path to the API part of your ResourceSpace instance
$private_key = '3eabfbcbea3404b1b5c2f884ec8e86bf686cff53d484f4fb2744530721ff65dzerrs'; // Available at https://media.example.com/pages/api_test.php
$user = 'rs_user'; // A username in your ResourceSpace Instance

// Create instance of ResourcespaceApi
$rs = new ResourcespaceApi($private_key, $user, $path);

// An array of all files containing the keyword "Tree"
$results = $rs->doSearch('Tree')->getResults();
// An array of the 50 first files containing the keyword "Tree", with thumbnail links, sorted by descending resource id
$results = $rs->searchGetPreviews('Tree', ['getsizes' => 'thm', 'fetchrows' => 50, 'order_by' => 'resourceid', 'sort' => 'desc'])->getResults(); 

```
