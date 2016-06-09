## JsonBdd

<h4>Comment ça marche ?</h4>

C'est très simple ! Dans un premier temps, vous devez télécharger le dossier dispo en fin de l'article, inclure ce dossier à votre projet, et pour finir, "include" ou "require" le fichier "json_bdd.php" dans vos fichiers qui ont besoin de l'utiliser.
Une fois cela réalisé, vous allez dans un premier temps vous connecter à votre base de données ( Si elle n'est pas créée, elle le sera automatiquement ).

<h4>Connecter sa base de données :</h4>
<i>  
try  
{  
	$bdd = new JBDD();  
	$bdd->connect(array(  
		'name' => //Nom de la base de données,  
		'password' => //Mot de passe de la base de données  
	));  
}  
catch(Exception $e)  
{  
	echo($e->getMessage());  
}</i>

Ensuite, plusieurs possibilités s'offrent à vous.

<i>Note : Des erreurs sont retournées en cas de problèmes, pour les récupérer : $error = $bdd->_error</i>

<h4>Reset la base de données sur laquelle vous êtes connectés :</h4>

<i>$bdd->bddReset();</i>

<h4>Insertion d'une valeur dans une table de la base de données ( La table est automatiquement créée si elle n’existe pas ) :</h4>

<i>$bdd->insert(array(  
	'table' => //Nom de votre table,  
	'values' => array(  
		'id de valeur 1' => //Valeur 1,  
		'id de valeur 2' => //Valeur 2  
		//Etc ...  
	)  
));  
//Valeur de réponse : Un tableau contenant toutes les infos sur la valeur entrée  
$reponse = $bdd->_reponse;</i>

<h4>Rechercher une valeur dans une table :</h4>

<i>//Recherche avec l'id d'auto increment :  
$bdd->get(array(  
	'table' => //Nom de votre table de recherche,  
	'aid' => //Id numérique de recherche  
));  
//Valeur de réponse : Un tableau contenant toutes les infos sur la valeur recherchée  
$reponse = $bdd->_reponse;  
//Recherche toutes les valeurs d'une table :  
$bdd->get(array(  
	'table' => //Nom de votre table de recherche,  
	'all' => true  
));  
//Valeur de réponse : Un tableau contenant toutes les infos sur les valeurs recherchées  
$reponse = $bdd->_reponse;  
//Recherche avec une valeur ( Équivalent d'un WHERE en SQL ) :  
$bdd->get(array(  
	'table' => //Nom de votre table de recherche,  
	'where' => array(  
		'id de valeur de recherche 1' => //Valeur de recherche 1,  
		'id de valeur de recherche 2' => //Valeur de recherche 2  
		//Etc ...  
	)  
));  
//Valeur de réponse : Un tableau contenant toutes les infos sur les valeurs recherchées  
$reponse = $bdd->_reponse;  
//Options disponibles :   
// 'whereLike' => true : Vous permet de sélectionner une valeur qui contient les valeurs définies dans le tableau "where" ( Dispo qu'avec le mode de recherche "where" )  
// 'reverse' => true : Inverser les données du tableau ( Dispo qu'avec les modes de recherches "all" et "where")  
// 'smin' => 2 et 'smax' => 40 : Équivalent à "LIMIT" du SQL ( Dispo qu'avec les modes de recherches "all" et "where")</i>

<h4>Mettre à jour une valeur :</h4>

<i>//Mettre à jour avec l'id d'auto increment :  
$bdd->update(array(  
	'table' => //Nom de votre table à mettre à jour,  
	'aid' => //Id numérique de recherche,  
	'newValue' => array(  
		'id de valeur 1' => //Nouvelle valeur 1,  
		'id de valeur 2' => //Nouvelle valeur 2  
		//Etc ...  
	)  
));  
//Valeur de réponse : Un tableau contenant toutes les infos sur la nouvelle valeur entrée  
$reponse = $bdd->_reponse;  
//Mettre à jour avec une valeur de recherche ( Équivalent d'un WHERE en SQL ) :  
$bdd->update(array(  
	'table' => //Nom de votre table à mettre à jour,  
	'where' => array(  
		'id de valeur de recherche 1' => //Valeur de recherche 1,  
		'id de valeur de recherche 2' => //Valeur de recherche 2  
		//Etc ...  
	),  
	'newValue' => array(  
		'id de valeur 1' => //Nouvelle valeur 1,  
		'id de valeur 2' => //Nouvelle valeur 2  
		//Etc ...  
	)
));
//Valeur de réponse : Un tableau contenant toutes les infos sur la nouvelle valeur entrée  
$reponse = $bdd->_reponse;  
//Options disponibles :   
// 'whereLike' => true : Vous permet de sélectionner une valeur qui contient les valeurs définies dans le tableau "where" ( Dispo qu'avec le mode de recherche "where" )</i>

<h4>Supprimer une valeur :</h4>

<i>//Supprimer avec l'id d'auto increment :  
$bdd->delete(array(  
	'table' => //Nom de votre table de recherche,  
	'aid' => //Id numérique de l'entré à supprimer  
));  
//Supprimer avec une valeur de recherche ( Équivalent d'un WHERE en SQL ) :  
$bdd->delete(array(  
	'table' => //Nom de votre table de recherche,  
	'where' => array(  
		'id de valeur de recherche 1' => //Valeur de recherche 1,  
		'id de valeur de recherche 2' => //Valeur de recherche 2  
		//Etc ...  
	)  
));  
//Options disponibles :   
// 'whereLike' => true : Vous permet de sélectionner une valeur qui contient les valeurs définies dans le tableau "where" ( Dispo qu'avec le mode de recherche "where" )</i>

<h4>Et pour finir, vous pouvez supprimer une table :</h4>

<i>$bdd->tableDelete('Nom de votre table a supprimer');</i>

Voila, vous pouvez trouver l'article complet ainsi qu'un exemple ici : 

- http://www.luluwebmaster.fr/creation-50/jsonbdd-une-base-de-donnees-en-json.htm
