<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="fr" lang="fr">
    <head>        
        <title>Test momo API</title>
        <meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
    </head>    
    <body>      
        <?php
            require 'vendor/autoload.php';
            use Lepresk\MomoApi\MomoApi;
            use Lepresk\MomoApi\Utilities;
            use Lepresk\MomoApi\Config;
            use Lepresk\MomoApi\Models\PaymentRequest;

            // Récupéreration de  la subscriptionKey dans son profile 
            $subscriptionKey = '2cacd57e0c5842aea7f65e836a9d6686';

            // Récupéreration du client Momo
            $momoClient = MomoApi::create(MomoApi::ENVIRONMENT_SANDBOX);
            print_r($momoClient);

            // Création d' une api user
            $uuid = Utilities::guidv4(); // Ou tout autre guuidv4 valide
            $callbackHost = 'https://my-domain.com/callback';

            $apiUser = $momoClient->sandbox($subscriptionKey)->createApiUser($uuid, $callbackHost);
            echo "Api user created: $apiUser\n";     
            #### Récupération des informations d'un utilisateur
            $data = $momoClient->sandbox($subscriptionKey)->getApiUser($apiUser);
            print_r($data);


            #### Création d'une api key
            $apiKey = $momoClient->sandbox($subscriptionKey)->createApiKey($apiUser);
            echo "Api token created: $apiKey\n";
            
            ### Intéragir avec le produit collection
            //Configuration pour l'API collection
            // Création d' un object Config

            $config =  Config::collection($subscriptionKey, $apiUser, $apiKey, $callbackHost);

            // Définir la configuration sur l'instance en cours de MomoApi
            $momoClient->setupCollection($config);

            #### Obtenir un token oauth
            $token = $momoClient->collection()->getAccessToken();
            echo "Access_Token: === ";
            echo  $token->getAccessToken(); // Token
            echo "\nExpires Token: === ";
            echo  $token->getExpiresIn(); // Date d'expiration du token




          
            #### Récupérer le solde du compte
            $balance = $momoClient->collection()->getAccountBalance();

            echo "Solde du compte: = ";
            echo $balance->getAvailableBalance(); // Solde du compte
            echo "Devise: = ";
            echo $balance->getCurrency(); // Devise du compte
           
            #### Faire une requête requestToPay
            //Pour initier un paiement requestToPay
            $request = new PaymentRequest(1, 'EUR', 'ORDER-5', '674991248', 'Payer message', 'Payer note');
            $paymentId = $momoClient->collection()->requestToPay($request);
            echo "Id de paiement : = ";
            echo $paymentId;
            
            #### Vérifier le status d'une transactiono   
            // Vérifier le statut du paiement
            $transaction = $momoClient->collection()->checkRequestStatus($paymentId);

            echo $transaction->getStatus(); // Pour obtenir le statut de la transaction
            
            #### Gérer le hook du callback
            use Lepresk\MomoApi\Models\Transaction;

            // Créer un objet transaction depuis le tableau GET
            $transaction = Transaction::parse($_GET);

            echo $transaction->getStatus(); // Pour obtenir le statut de la transaction
            echo $transaction->getAmount(); // Pour récuperer le montant de la transaction
            

        ?>  
    </body> 
</html>
