<?php

use Illuminate\Support\Facades\File;
use JsLocalization\StaticFileResponse;

class StaticFileResponseTest extends TestCase
{
    protected $testFilePath, $testFileContent;

    public function setUp ()
    {
        parent::setUp();

        $this->testFilePath = "/tmp/laravel-static";
        $this->testFileContent = "Test contents!";

        File::put($this->testFilePath, $this->testFileContent);
    }

    public function testServingFile ()
    {
        $response = new StaticFileResponse($this->testFilePath);

        $lastModified = new DateTime();
        $lastModified->setTimestamp( File::lastModified($this->testFilePath) );

        $this->assertEquals($response->getStatusCode(), 200);
        $this->assertEquals($response->getContent(), $this->testFileContent);
        $this->assertEquals($response->getLastModified()->getTimestamp(), $lastModified->getTimestamp());
    }

    public function testExceptionHandling ()
    {
        $filePath = "/tmp/x/y/z/does-not-exist";
        $this->setExpectedException('Exception', "Cannot read file: $filePath");

        $response = new StaticFileResponse($filePath);
    }
}
?>
