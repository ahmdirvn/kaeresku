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
        $serviceAccountPath = storage_path('app/firebase/kaeresku-firebase.json');
        $databaseUri = env('FIREBASE_DATABASE_URL');


        $factory = (new Factory)
            ->withServiceAccount($serviceAccountPath)
            ->withDatabaseUri(env('FIREBASE_DATABASE_URL'));

        $this->auth = $factory->createAuth();
    }

    public function getAuth(): Auth
    {
        return $this->auth;
    }
}
