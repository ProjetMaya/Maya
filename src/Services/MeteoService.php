<?php

namespace App\Services;

use DateTime;
use DateTimeZone;

class MeteoService
{
    public static function getMeteo() {
        // récupérer la variable d'environnement désignant l'URl de l'API
        $urlAPI = $_SERVER['URL_API_OWM'];

        // Initialiser une session CURL
        $clientURL = curl_init();
        // Récupérer le contenu de la page
        curl_setopt($clientURL, CURLOPT_RETURNTRANSFER, 1);
        // Transmettre l'URL
        curl_setopt($clientURL, CURLOPT_URL, $urlAPI);
        // Exécutez la requête HTTP
        $reponse = curl_exec($clientURL);
        // Fermer la session
        curl_close($clientURL);
        // Récupérer les données au format JSON
        $donneesTemps = json_decode($reponse);

        $dateJour = new DateTime('now', new DateTimeZone('Europe/Paris'));

        return [
            "ville" => $donneesTemps->name,
            "dateJour" => $dateJour->format("d/m/Y à H\hi"),
            "description" => ucfirst($donneesTemps->weather[0]->description),
            "tempsIcone" => "http://openweathermap.org/img/w/" . $donneesTemps->weather[0]->icon .".png",
            "temp_max" => $donneesTemps->main->temp_max,
            "temp_min" => $donneesTemps->main->temp_min,
            "humidity" => $donneesTemps->main->humidity,
            "wind" => $donneesTemps->wind->speed,
        ];
    }
}