<?php

require __DIR__ . '/vendor/autoload.php';

use Ddrv\Env\Env;
use Ddrv\Env\VariableProvider\EnvVariableProvider;
use Ddrv\Env\VariableProvider\FileVariableProvider;
use \Ovh\Api;

try {
    $env = new Env(
        new EnvVariableProvider(),
        new FileVariableProvider('../ovh.env'),
    );

    /**
     * Instanciate an OVH Client.
     * You can generate new credentials with full access to your account on
     * the token creation page
     */
    $ovh = new Api( $env->get('OVH_Application_Key'),  // Application Key
                    $env->get('OVH_Application_Secret'),  // Application Secret
                    'ovh-eu',      // Endpoint of API OVH Europe (List of available endpoints)
                    $env->get('OVH_Consumer_Key')); // Consumer Key

    if (empty($_POST['VPS']))
    {
        $result = $ovh->get('/vps');

        $vps_servers = $result;
    }
} catch (\Throwable $th) {
    echo $th;
}
?>
<!DOCTYPE html>
<html lang="fr">
  <head>
    <meta charset="utf-8">
    <title>Big Red Button 🚨</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-GLhlTQ8iRABdZLl6O3oVMWSktQOp6b7In1Zl3/Jr59b6EGGoI1aFkw7cmDA6j6gD" crossorigin="anonymous">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js" integrity="sha384-w76AqPfDkMBDXo30jS1Sgez6pr3x5MlQ1ZAGC+nuZB+EYdgRZgiwxhTBTkF7CXvN" crossorigin="anonymous"></script>
  </head>
  <body>

<?php

if (!empty($vps_servers))
{
    ?>

    <div class="alert alert-primary" role="alert">
        <b>Cliquez sur le serveur que vous souhaitez redémarrer. Le temps d'arrêt est d'à peu près une minute.</b>
    </div>

    <div class="mx-auto my-5" style="width: 300px; font-size: 200%">

    <form method="POST">
        <div class="mb-3">
            <label for="password" class="form-label">Mot de passe :</label>
            <input type="password" name="password" class="form-control">
        </div>

        <?php
    foreach ($vps_servers as $aServer)
        echo '<div class="mb-3 form-check"><input value="'.$aServer.'" name="VPS" type="radio" class="form-check-input" id="'.$aServer.'Check"><label class="form-check-label" for="'.$aServer.'Check">'.$aServer.'</label></div>';
    ?>
        <button type="submit" class="btn btn-primary">Redémarrer le serveur !!</button>
    </form>
    </div>
    <div class="text-center"><img src="panic_button.jpg"/></div>
    <?php
}

if (!empty($_POST['VPS']))
{
    $passphrase = $env->get('OVH_PASSPHRASE');
    if ($_POST['password'] != $passphrase || empty($passphrase))
    {
        echo '<div class="alert alert-danger" role="alert">
                Veuillez saisir le mot de passe pour redémarrer le serveur !
              </div>';
    }
    else
    {
        echo '<div class="alert alert-danger" role="alert">
                Serveur en cours de redémarrage... Veuillez patienter quelques dizaines de secondes !
              </div>';

        $result = $ovh->post('/vps/' . $_POST['VPS'] . '/reboot');

        echo '<pre>';
        print_r($result);
        echo '</pre>';
    }
}

?>

  </body>
</html>
