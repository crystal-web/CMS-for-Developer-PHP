<?php
/**
* @title Connection
* @author Christophe BUFFET <developpeur@crystal-web.org> 
* @license Creative Commons By 
* @license http://creativecommons.org/licenses/by-nd/3.0/
* @description 
*/

Class AccessControlList {
private $idGroup;
private $controlCode;
private $controlCodeGrant;

private $controller;
private $action;
private $fullPower = false;
private $request;

	public function __construct($request, $group)
	{
		if (is_int($group))
		{
		// Has guest 
		$this->idGroup=0;
		}
		else
		{
			// Si le group est different de *
			// C'est un admin
			if ($group != '*')
			{
			$this->idGroup = explode('|',trim($group, '|'));
			}
			/***************************************
			*	C'est un (super)admin
			***************************************/
			else
			{
			$this->fullPower = true;
			}
		}
		
		$this->controller			= $request->controller;
		$this->action 				= $request->action;
		$this->params				= $request->params;
		
		$this->controlCode 			= strtolower($this->controller.'.'.$this->action);
		$this->controlCodeGrant 	= strtolower($this->controller.'.*');
		return $this;
	}
	
	
	/***************************************
	*	Demande si l'utilisateur a le droit
	***************************************/
	public function isAllowed($allowedParams=NULL)
	{

		/***************************************
		*	C'est un (super)admin
		***************************************/
		if ($this->fullPower)
		{
		return true;
		}

		/***************************************
		*	On charge le model
		***************************************/
		$m = $this->loadModel('Acl');

		/***************************************
		*	On parcourt le tableau des groupes
		*	Un utilisateur, peut avoir plusieurs groupes
		***************************************/
		if (is_array($this->idGroup))
		{
		foreach ($this->idGroup AS $key=>$data)
			{
				/***************************************
				*	Prepare la requete en demandant
				*	Toutes les possibilité
				***************************************/
				$query = array(
					'fields' => 'controller, params',
					'conditions' => "identifiant =".$data." AND controller LIKE  '".$this->controller."%'",
					);
				if ($respon = $m->find($query))
				{
				/***************************************
				*	Si on obtiens une reponse,
				*	on test si l'utilisateur a le droit
				***************************************/
					foreach ($respon AS $k=>$v)
					{
						/***************************************
						*	Si une correspondance existe
						***************************************/
						if ($v->controller == $this->controlCode
									or
							$v->controller == $this->controlCodeGrant)
						{

							/***************************************
							*	Si on demande un paramettre particulier
							*	EN TEST
							***************************************/
							if (!is_null($allowedParams))
							{
								if (preg_match('#'.$allowedParams.'#Ui', $v->params))
								{
								return true;
								}
							}
							else
							{
								return true;
							}

						}
					}
				}
			}
		}
		/***************************************
		*	Si toutes les requêtes echoue,
		*	L'utilisateur n'as pas le droit
		***************************************/
		return false;
	}
	
	
	public function isGrant()
	{
	return $this->fullPower;	
	}
	
	
	
	/***************************************
	*	Methode privé
	***************************************/
	private function loadModel($name)
	{		
	// L'endroit ou le model est chargé
	$file = __APP_PATH . DS . 'model' . DS . $name . '.model.php';
		if (file_exists($file))
		{
		require_once $file;
			if (!isSet($this->$name))
			{
			return new $name();
			}
		}
		elseif (__DEV_MODE)
		{
		debug('File model not found '.$file);
		}
	}
}
?>