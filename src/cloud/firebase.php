<?php
class FirebaseConfig {
    public $apiKey = 'AIzaSyC4esh_tMmEgDrJ-_VDYF0EQuGeqt4DyaI';
    public $authDomain = 'misy-web.firebaseapp.com';
    public $projectId = 'misy-web';  
    public $storageBucket = 'misy-web.appspot.com';  
    public $messagingSenderId = '130466195701';  
    public $appId = '1:130466195701:web:9df0fdb62e719559f77dbc';  
    public $measurementId = 'G-DJKEMSXE5J';  

    function getApiKey() { return $this -> apiKey; }
    function getAuthDomain() { return $this -> authDomain; }
    function getProjectId() { return $this -> projectId; }
    function getStorageBucket() { return $this -> storageBucket; }
    function getMessagingSenderId() { return $this -> messagingSenderId; }
    function getAppId() { return $this -> appId; }
    function getMeasurementId() { return $this -> measurementId; }
}
