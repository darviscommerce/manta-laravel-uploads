# laravel-uploads

Add this to the `disks` section of `config/filesystems.php`:

```php
        'azure' => [ // NB This need not be set to "azure", because it's just the name of the connection - feel free to call it what you want, or even set up multiple blobs with different names
            'driver'    => 'azure', // As this is the name of the driver, this MUST be set to "azure"
            'name'      => env('AZURE_STORAGE_NAME'),
            'key'       => env('AZURE_STORAGE_KEY'),
            'container' => env('AZURE_STORAGE_CONTAINER'),
            'url'       => env('AZURE_STORAGE_URL'),
            'prefix'    => null,
            'connection_string' => env('AZURE_STORAGE_CONNECTION_STRING') // optional, will override default endpoint builder 
        ],
```

Set .env value  
`FILESYSTEM_DISK=azure`
