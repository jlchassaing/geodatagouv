<?php
/**
 * @author jlchassaing <jlchassaing@gmail.com>
 * @licence MIT
 */

namespace GeoDataGouv\DataFlow;

interface ReaderInterface
{
    public function read(string $filename): iterable;
}