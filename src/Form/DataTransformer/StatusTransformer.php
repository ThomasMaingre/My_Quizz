<?php

namespace App\Form\DataTransformer;

use Symfony\Component\Form\DataTransformerInterface;

class StatusTransformer implements DataTransformerInterface
{
    public function transform($value)
    {
        return $value == 2; // Convert integer to boolean (true if 2, false otherwise)
    }

    public function reverseTransform($value)
    {
        return $value ? 2 : 1; // Convert boolean to integer (2 if true, 1 if false)
    }
}
