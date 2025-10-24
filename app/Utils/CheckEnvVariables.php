<?php

namespace App\Utils;

class CheckEnvVariables
{
    public static function checkEnvVariables(array $configs)
    {
        $missingEnvVars = array_filter($configs, function ($value) {
            return empty($value);
        });

        if (!empty($missingEnvVars)) {
            $missingKeys = implode(', ', array_keys($missingEnvVars));

            return response()->json([
                'success' => false,
                'errors' => EnvVariablesAreInComplete::Builder($missingKeys)->getMessage(),
            ], 429)->send();
        }
    }

}
