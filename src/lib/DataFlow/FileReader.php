<?php
/**
 * @author jlchassaing <jlchassaing@gmail.com>
 * @licence MIT
 */

namespace GeoDataGouv\DataFlow;


use GuzzleHttp\Client;
use GuzzleHttp\Psr7\Request;
use Symfony\Component\Filesystem\Filesystem;

class FileReader extends AbstractReader
{
    protected $keys;

    public function read(string $filename): iterable
    {
        if (!$this->fileExists($filename)){
            throw new FileReaderException("Unable to open file '".$filename."' for read.");
        }

        if (@!$fh = fopen($filename, 'r')) {
            throw new FileReaderException("Unable to read file '".$filename."'.");
        }

        while (false !== ($read = fgets($fh,2048))) {
            $data = $this->getData($read);
            if (empty($data)) continue;
            else yield $data;
        }
    }

    private function getData($line)
    {
        $data = str_getcsv($line,';','"');
        if (empty($this->keys)){
            $this->keys = $data;
            return null;
        }
        return array_combine($this->keys,$data);
    }
}
