<?php

   // le try catch, permet d'essayer un bout de code, et si il détecte le moindre problème il nous retourne une exception
   // on peut traiter cette exception pour avoir un message d'erreur détaillé
   try
   {
        // je crée une variable $db, qui va contenir l'accès à ma base de donnée.
        // cette variable va contenir l'objet PDO (nous verrons ce qu'est un objet plus tard dans la formation,
        // pour l'instant ça ne vous sea pas utile)
        // pour crée cette connexion il va vous falloir plusieurs parties :

        // le dsn (data source name), qui va correspondre à la base de donnée que l'on utilise, ici mysql
        // suivi de l'hote après host=, ici localhost
        // suivi du nom de la base de donnée après "dbname="
        $dsn = 'mysql:host=localhost;dbname=quiz_multijoueur';


        // le nom d'utilisateur, utilisé dans la base de donnée, oar défaut sur wampserver c'est 'root'
        $user = 'root';

        // le mot de passe utilisé sur votre base de donnée
        $password = '';

        $db = new PDO( $dsn, $user, $password);
   }
   catch (Exception $message){
        // dans le cas où la connexion à la base de donnée serait mal executé (par exemple un mauvais nom d'utilisateur)
        // vous pouvez utiliser l'objet Exception pour afficher un message d'erreur personnalisé comme ci-dessous
        echo "ya un blem <br>" . "<pre>$message</pre>" ;
   }


   ?>