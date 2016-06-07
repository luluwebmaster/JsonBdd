<?php
//Class JSON BDD
class JBDD
{
	//Attibuts
	private $_bddName = false,
			$_bddPassword = false,
			$_jsonExt = ".dat",
			$_connected = false,
			$_dataJson = false,
			$_dirJsonBdd = false;
	public  $_reponse = false,
			$_error = false;
	//Methodes
	//Metohde pour préparer la connexion
	public function connect($settings = array())
	{
		if(empty($settings['name']) OR $settings['name'] == "")
		{
			throw new Exception('Please enter a database name.');
			return false;
		}
		else if(empty($settings['password']) OR $settings['password'] == "")
		{
			throw new Exception('Please enter a database password.');
			return false;
		}
		else
		{
			$dirJson = dirname(__FILE__)."/jsons/";
			$jsonParseName = 'bdd_'.$this->parseBdd($settings['name']);
			$dirJsonBdd = $dirJson.$jsonParseName.$this->_jsonExt;
			if(file_exists($dirJsonBdd))
			{
				if($dataJson = @file_get_contents($dirJsonBdd))
				{
					$dataJson = $this->pcrypt('decrypt', $dataJson, md5($settings['password']));
					$dataJson = json_decode($dataJson, true);
					if($dataJson['infos']['password'] == md5($settings['password']))
					{
						$dirJson = dirname(__FILE__)."/jsons/";
						$dirJsonBdd = $dirJson.'bdd_'.$this->parseBdd($settings['name']).$this->_jsonExt;
						$this->_dataJson = $dataJson;
						$this->_bddName = $settings['name'];
						$this->_bddPassword = $settings['password'];
						$this->_connected = true;
						$this->_dirJsonBdd = $dirJsonBdd;
						return true;
					}
					else
					{
						throw new Exception('Wrong password.');
						return false;
					}
				}
				else
				{
					throw new Exception('An error has occurred.');
					return false;
				}
			}
			else
			{
				$bddJson = array(
					'infos' => array(
						'name' => $settings['name'],
						'password' => md5($settings['password'])
					),
					'values' => array()
				);
				$bddJson = json_encode($bddJson);
				$bddJson = $this->pcrypt('crypt', $bddJson, md5($settings['password']));
				if(file_put_contents($dirJsonBdd, $bddJson))
				{
					$dirJson = dirname(__FILE__)."/jsons/";
					$dirJsonBdd = $dirJson.'bdd_'.$this->parseBdd($settings['name']).$this->_jsonExt;
					$this->_bddName = $settings['name'];
					$this->_bddPassword = $settings['password'];
					$this->_connected = true;
					$this->_dirJsonBdd = $dirJsonBdd;
					return true;
				}
				else
				{
					throw new Exception('An error has occurred.');
					return false;
				}
			}
		}
	}
	public function insert($settings = array())
	{
		if($this->_connected == true)
		{
			if(isset($settings['table']) AND $settings['table'] != "" AND isset($settings['values']) AND $settings['values'] != "" )
			{
				$dataJson = $this->_dataJson;
				$idIncrment = 1;
				$firstElement = false;
				$tableName = 'tb_'.$this->parseBdd($settings['table']);
				$bddValue = @$dataJson['values'][$tableName];
				if(empty($bddValue) OR !is_array($bddValue))
				{
					$dataJson['values'][$tableName] = array();
					$firstElement = true;
				}
				if($firstElement == false)
				{
					$idIncrment = count($dataJson['values'][$tableName]) + 1;
				}
				$dataJson['values'][$tableName][$idIncrment] = array();
				$dataJson['values'][$tableName][$idIncrment] = $settings['values'];
				$this->_dataJson = $dataJson;
				$dataJson = json_encode($dataJson);
				$dataJson = $this->pcrypt('crypt', $dataJson, md5($this->_bddPassword));
				if(file_put_contents($this->_dirJsonBdd, $dataJson))
				{
					$this->_reponse = array();
					$this->_reponse['aid'] = $idIncrment;
					$this->_reponse['values'] = $settings['values'];
					return true;
				}
				else
				{
					$this->_error = ('An error has occurred.');
					return false;
				}
			}
			else
			{
				$this->_error = ('An error has occurred.');
				return false;
			}
		}
		else
		{
			$this->_error = ('The database is not connected.');
			return false;
		}
	}
	//Methode pour recherche une valeur
	public function get($settings = array())
	{
		if($this->_connected == true)
		{
			if(isset($settings['table']) AND $settings['table'] != "")
			{
				$dataJson = $this->_dataJson;
				$tableName = 'tb_'.$this->parseBdd($settings['table']);
				$bddValue = @$dataJson['values'][$tableName];
				//Avec l'id d'auto increment
				if(isset($settings['aid']) AND $settings['aid'] != "")
				{
					if(is_array($bddValue[$settings['aid']]))
					{
						$this->_reponse = $bddValue[$settings['aid']];
						return true;
					}
					else
					{
						$this->_reponse = false;
						$this->_error = ('Could not find this value.');
						return false;
					}
				}
				//Toutes les valeurs
				elseif(isset($settings['all']) AND $settings['all'] == true)
				{
					$this->_reponse = $bddValue;
					if(isset($settings['reverse']) AND $settings['reverse'] == true)
					{
						krsort($this->_reponse);
					}
					if(isset($settings['smin']) AND $settings['smin'] !== "" AND is_int($settings['smin']))
					{
						for($i=1;$i<=$settings['smin'];$i++)
						{
							array_pop($this->_reponse);
						}
					}
					if(isset($settings['smax']) AND $settings['smax'] !== "" AND is_int($settings['smax']))
					{
						$newArray = array();
						$startArray = 0;
						foreach($this->_reponse AS $key => $value)
						{
							if($startArray <= $settings['smax'])
							{
								$newArray[$startArray] = $value;
								$startArray = $startArray + 1;
							}
						}
						$this->_reponse = $newArray;
					}
					return true;
				}
				//Avec des parametres de recherches
				elseif(isset($settings['where']) AND is_array($settings['where']))
				{
					$listeResults = array();
					if(is_array($bddValue))
					{
						foreach($bddValue AS $key => $value)
						{
							foreach($settings['where'] AS $key2 => $value2)
							{
								if(array_key_exists($key2, $value) AND strstr($value[$key2], $value2))
								{
									$listeResults[$key] = $bddValue[$key];
								}
							}
						}
					}
					$listeResults = array_values($listeResults);
					if(isset($listeResults[0]))
					{
						$this->_reponse = $listeResults;
						if(isset($settings['reverse']) AND $settings['reverse'] == true)
						{
							krsort($this->_reponse);
						}
						if(isset($settings['smin']) AND $settings['smin'] !== "" AND is_int($settings['smin']))
						{
							for($i=1;$i<=$settings['smin'];$i++)
							{
								array_pop($this->_reponse);
							}
						}
						if(isset($settings['smax']) AND $settings['smax'] !== "" AND is_int($settings['smax']))
						{
							$newArray = array();
							$startArray = 0;
							foreach($this->_reponse AS $key => $value)
							{
								if($startArray <= $settings['smax'])
								{
									$newArray[$startArray] = $value;
									$startArray = $startArray + 1;
								}
							}
							$this->_reponse = $newArray;
						}
						return true;
					}
					else
					{
						$this->_reponse = false;
						$this->_error = ('Could not find this value.');
						return false;
					}
				}
				else
				{
					$this->_error = ('Could not find this value.');
					return false;
				}
			}
			else
			{
				$this->_error = ('An error has occurred.');
				return false;
			}
		}
		else
		{
			$this->_error = ('The database is not connected.');
			return false;
		}
	}
	//Methode pour update une valeur
	public function update($settings = array())
	{
		if($this->_connected == true)
		{
			if(isset($settings['table']) AND $settings['table'] != "" AND isset($settings['newValue']) AND is_array($settings['newValue']))
			{
				$dataJson = $this->_dataJson;
				$tableName = 'tb_'.$this->parseBdd($settings['table']);
				$bddValue = @$dataJson['values'][$tableName];
				$listeValue = array();
				//Avec l'id d'auto increment
				if(isset($settings['aid']) AND $settings['aid'] != "")
				{
					$listeValue[$settings['aid']] = $bddValue[$settings['aid']];
				}
				//Avec des parametres de recherches
				elseif(isset($settings['where']) AND is_array($settings['where']))
				{
					$listeResults = array();
					foreach($bddValue AS $key => $value)
					{
						foreach($settings['where'] AS $key2 => $value2)
						{
							if(array_key_exists($key2, $value) AND strstr($value[$key2], $value2))
							{
								$listeValue[$key] = $bddValue[$key];
							}
						}
					}
				}
				//On update
				$this->_reponse = array();
				foreach($listeValue AS $key => $value)
				{
					foreach($settings['newValue'] AS $key2 => $value2)
					{
						if(array_key_exists($key2, $listeValue[$key]) AND isset($listeValue[$key][$key2]))
						{
							$dataJson['values'][$tableName][$key][$key2] = $value2;
							$this->_reponse[$key]['aid'] = $key;
							$this->_reponse[$key]['values'] = $dataJson['values'][$tableName][$key];
						}
					}
				}
				$this->_reponse = array_values($this->_reponse);
				$this->_dataJson = $dataJson;
				$dataJson = json_encode($dataJson);
				$dataJson = $this->pcrypt('crypt', $dataJson, md5($this->_bddPassword));
				if(file_put_contents($this->_dirJsonBdd, $dataJson))
				{
					return true;
				}
				else
				{
					$this->_error = ('An error has occurred.');
					return false;
				}
			}
			else
			{
				$this->_error = ('An error has occurred.');
				return false;
			}
		}
		else
		{
			$this->_error = ('The database is not connected.');
			return false;
		}
	}
	//Methode pour delete une valeur
	public function delete($settings = array())
	{
		if($this->_connected == true)
		{
			if(isset($settings['table']) AND $settings['table'] != "")
			{
				$dataJson = $this->_dataJson;
				$tableName = 'tb_'.$this->parseBdd($settings['table']);
				$bddValue = @$dataJson['values'][$tableName];
				$listeValue = array();
				//Avec l'id d'auto increment
				if(isset($settings['aid']) AND $settings['aid'] != "")
				{
					$listeValue[$settings['aid']] = $bddValue[$settings['aid']];
				}
				//Avec des parametres de recherches
				elseif(isset($settings['where']) AND is_array($settings['where']))
				{
					$listeResults = array();
					foreach($bddValue AS $key => $value)
					{
						foreach($settings['where'] AS $key2 => $value2)
						{
							if(array_key_exists($key2, $value) AND strstr($value[$key2], $value2))
							{
								$listeValue[$key] = $bddValue[$key];
							}
						}
					}
				}
				//On delete
				foreach($listeValue AS $key => $value)
				{
					$dataJson['values'][$tableName][$key] = "delete";
				}
				$this->_dataJson = $dataJson;
				$dataJson = json_encode($dataJson);
				$dataJson = $this->pcrypt('crypt', $dataJson, md5($this->_bddPassword));
				if(file_put_contents($this->_dirJsonBdd, $dataJson))
				{
					return true;
				}
				else
				{
					$this->_error = ('An error has occurred.');
					return false;
				}
			}
			else
			{
				$this->_error = ('An error has occurred.');
				return false;
			}
		}
		else
		{
			$this->_error = ('The database is not connected.');
			return false;
		}
	}
	//Methode pour delete une valeur
	public function tableDelete($name)
	{
		if($this->_connected == true)
		{
			if(isset($name) AND $name != "")
			{
				$dataJson = $this->_dataJson;
				$tableName = 'tb_'.$this->parseBdd($name);
				unset($dataJson['values'][$tableName]);
				$this->_dataJson = $dataJson;
				$dataJson = json_encode($dataJson);
				$dataJson = $this->pcrypt('crypt', $dataJson, md5($this->_bddPassword));
				if(file_put_contents($this->_dirJsonBdd, $dataJson))
				{
					return true;
				}
				else
				{
					$this->_error = ('An error has occurred.');
					return false;
				}
			}
			else
			{
				$this->_error = ('An error has occurred.');
				return false;
			}
		}
		else
		{
			$this->_error = ('The database is not connected.');
			return false;
		}
	}
	//Methode pour reset la bdd
	public function bddReset()
	{
		if($this->_connected == true)
		{
			if(@unlink($this->_dirJsonBdd))
			{
				$this->connect(array(
					'name' => $this->_bddName,
					'password' => $this->_bddPassword
				));
				return true;
			}
			else
			{
				$this->_error = ('An error has occurred.');
				return false;
			}
		}
		else
		{
			$this->_error = ('The database is not connected.');
			return false;
		}
	}
	//Methode pour parser des entrer de la bdd
	private function parseBdd($name)
	{
		//Source : http://tassedecafe.org/fr/nettoyer-chaine-caracteres-php-31
		$caracteres = array(
			'À' => 'a', 'Á' => 'a', 'Â' => 'a', 'Ä' => 'a', 'à' => 'a', 'á' => 'a', 'â' => 'a', 'ä' => 'a', '@' => 'a',
			'È' => 'e', 'É' => 'e', 'Ê' => 'e', 'Ë' => 'e', 'è' => 'e', 'é' => 'e', 'ê' => 'e', 'ë' => 'e', '€' => 'e',
			'Ì' => 'i', 'Í' => 'i', 'Î' => 'i', 'Ï' => 'i', 'ì' => 'i', 'í' => 'i', 'î' => 'i', 'ï' => 'i',
			'Ò' => 'o', 'Ó' => 'o', 'Ô' => 'o', 'Ö' => 'o', 'ò' => 'o', 'ó' => 'o', 'ô' => 'o', 'ö' => 'o',
			'Ù' => 'u', 'Ú' => 'u', 'Û' => 'u', 'Ü' => 'u', 'ù' => 'u', 'ú' => 'u', 'û' => 'u', 'ü' => 'u', 'µ' => 'u',
			'Œ' => 'oe', 'œ' => 'oe',
			'$' => 's');
		$name = strtr($name, $caracteres);
		$name = preg_replace('#[^A-Za-z0-9]+#', '-', $name);
		$name = trim($name, '-');
		$name = strtolower($name);
		return $name;
	}
	//Methode crypter et décrypter le données de la BDD
	private function pcrypt($action, $value, $password)
	{
		$output = false;
		$password = md5(hash('sha256', $password));
		if($action == 'crypt')
		{
			$output = mcrypt_encrypt(MCRYPT_RIJNDAEL_256, $password, base64_encode($value), MCRYPT_MODE_ECB);
		}
		else if($action == 'decrypt')
		{
			$output = base64_decode(mcrypt_decrypt(MCRYPT_RIJNDAEL_256, $password, $value, MCRYPT_MODE_ECB));
		}
		return $output;
	}
}
?>