# JsonBdd
A venir

# Comment ça marche ?

Connexion à la base de donnée : 

try
{
	$bdd = new JBDD();
	$bdd->connect(array(
		'name' => "Nom de la base de donnée",
		'password' => "Mot de passe de la base de donnée"
	));
}
catch(Exception $e)
{
	echo($e->getMessage());
}

Liste des fonctions : 
