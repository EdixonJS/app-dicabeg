<?php

namespace V2\Middleware;

use Exception;
use V2\Interfaces\IData;

class Input implements IData
{
    public function validate($body)
    {
        if (!empty($body)) {
            foreach ($body as $key => $value) {
                if (!in_array($key, self::INPUT_DATA)) {
                    throw new Exception(
                        "attribute {{$key}} in body not validat",
                        400
                    );
                }

                if (is_null($value) or empty($value))
                    throw new Exception(
                    "attribute {{$key}} in body is null or is empty",
                    400
                );
            }
        } else throw new Exception("body empty", 400);
    }
}
