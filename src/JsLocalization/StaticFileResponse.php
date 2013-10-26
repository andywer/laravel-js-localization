<?php
namespace JsLocalization;

use App;
use DateTime;
use Exception;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\File;

class StaticFileResponse extends Response
{
    public function __construct ($filePath)
    {
        parent::__construct();

        if (!File::isFile($filePath)) {
            throw new Exception("Cannot read file: $filePath");
        }

        $fileContent = file_get_contents($filePath);
        $this->setContent($fileContent);

        $lastModified = new DateTime();
        $lastModified->setTimestamp( File::lastModified($filePath) );

        $this->setLastModified($lastModified);

        $this->isNotModified(App::make('request'));
    }
}