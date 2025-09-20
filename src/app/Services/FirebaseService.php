<?php

namespace App\Services;

use Kreait\Firebase\Factory;
use Kreait\Firebase\Auth;

class FirebaseService
{
    protected $auth;

    public function __construct()
    {

        $serviceAccountPath = realpath(base_path(env('FIREBASE_CREDENTIALS')));
        $databaseUri = env('FIREBASE_DATABASE_URL');

        $factory = (new Factory)
            ->withServiceAccount($serviceAccountPath)
            ->withDatabaseUri($databaseUri)
            ->withProjectId(json_decode(file_get_contents($serviceAccountPath), true)['project_id']);


        $this->auth = $factory->createAuth();
    }

    public function getAuth(): Auth
    {
        return $this->auth;
    }
}
