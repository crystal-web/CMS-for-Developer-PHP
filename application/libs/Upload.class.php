<?php

class Upload{
private $file;					// Fichier uploader $_FILES['name']
private $tmp = './files/tmp/';	// Dossier temporaire
public $tmp_file;

public $ext;						// Extention
private $extBlacklist = array(
  # HTML may contain cookie-stealing JavaScript and web bugs
  'html', 'htm', 'js', 'jsb', 'mhtml', 'mht',
  # PHP scripts may execute arbitrary code on the server
  'php', 'phtml', 'php3', 'php4', 'php5', 'phps',
  # Other types that may be interpreted by some servers
  'shtml', 'jhtml', 'pl', 'py', 'cgi',
  # May contain harmful executables for Windows victims
  'exe', 'scr', 'dll', 'msi', 'vbs', 'bat', 'com', 'pif', 'cmd', 'vxd', 'cpl' );
  private $MimeTypeBlacklist = array(
        # HTML may contain cookie-stealing JavaScript and web bugs
 'text/html', 'text/javascript', 'text/x-javascript',  'application/x-shellscript',
        # PHP scripts may execute arbitrary code on the server
 'application/x-php', 'text/x-php',
        # Other types that may be interpreted by some servers
 'text/x-python', 'text/x-perl', 'text/x-bash', 'text/x-sh', 'text/x-csh',
        # Client-side hazards on Internet Explorer
 'text/scriptlet', 'application/x-msdownload',
        # Windows metafile, client-side vulnerability on some systems
 'application/x-msmetafile',
        # A ZIP file may be a valid Java archive containing an applet which exploits the
 # same-origin policy to steal cookies
 'application/zip',
 
 # MS Office OpenXML and other Open Package Conventions files are zip files
 # and thus blacklisted just as other zip files. If you remove these entries
 # from the blacklist in your local configuration, a malicious file upload
 # will be able to compromise the wiki's user accounts, and the user 
 # accounts of any other website in the same cookie domain.
 'application/x-opc+zip',
        'application/msword',
        'application/vnd.ms-powerpoint',
        'application/vnd.msexcel',
);
private $mime;
public $save_as;				// Nom du fichier de ortie
private $upload_max_filesize;	// Taille en octe maximum des fichiers uploader
public $log=array();			// log ;-)

/*** Télécharge le fichier et le stock temporairement ***/
public function __construct($file, $strict=true)
{
$this->file = $file;
$this->tmp_file = basename($this->file['name']);
$this->tmp_file = strtr($this->tmp_file,
     'ÀÁÂÃÄÅÇÈÉÊËÌÍÎÏÒÓÔÕÖÙÚÛÜÝàáâãäåçèéêëìíîïðòóôõöùúûüýÿ',
     'AAAAAACEEEEIIIIOOOOOUUUUYaaaaaaceeeeiiiioooooouuuuyy'); 

$this->tmp_file = preg_replace('/([^.a-z0-9]+)/i', '-', $this->tmp_file);

$this->ext = strtolower(strrchr($this->file['name'], '.'));

if (function_exists('finfo_open'))
{
$this->log[] = 'Recherche du mime-type par finfo';
$finfo = finfo_open(FILEINFO_MIME_TYPE); // Retourne le type mime à la extension mimetype
$this->mime = finfo_file($finfo, $this->file['tmp_name']);
finfo_close($finfo);
}
elseif (function_exists('mime_content_type'))
{
$this->log[] = 'Recherche du mime-type par mime_content_type';
$this->mime = mime_content_type($this->file['tmp_name']);
}
else
{
$this->log[] = '/!\ Serveur insuffisament s&eacute;ris&eacute; : Requ&egrave;te Mime-Type non trait&eacute;';
}

if (!$this->control($this->extBlacklist))
{
	//Si la fonction renvoie TRUE, c'est que ça a fonctionné...
	if(move_uploaded_file($this->file['tmp_name'], $this->tmp . $this->tmp_file)) 
	{
		$this->log[]='Upload effectu&eacute; avec succ&egrave;s !';
		return true;
	}
	else //Sinon (la fonction renvoie FALSE).
	{
		$this->log[]='Echec de l\'upload !';
		return false;
	}
}
else
{
	if ($strict)
	{
	die($this->ext . ' est interdit');
	}
	else
	{
	return false;
	}
}

}

private function generateRandomString($length = 5) 
{    
    $string = ""; 
    
    //character that can be used 
    $possible = "0123456789bcdfghjkmnpqrstvwxyz"; 
    
    for($i=0;$i < $length;$i++) 
    { 
        $char = substr($possible, rand(0, strlen($possible)-1), 1); 
        
        if (!strstr($string, $char)) 
        { 
            $string .= $char; 
        } 
    } 

    return $string; 
}


/*** Renomage du fichier ***/
private function rename($dir_to_save)
{
$rename = $dir_to_save.'/'.$this->generateRandomString().$this->tmp_file;
return (file_exists($rename)) ? $dir_to_save.'/'.$this->generateRandomString(6).$this->tmp_file : $rename;
}


/*** Création de l'arboresence ***/
private function mkdir($dir_to_save)
{
$dir_array = explode('/', $dir_to_save);
$last = NULL;

	foreach ($dir_array AS $key => $dir)
	{
	$last=$last.$dir.'/';

		if (!is_dir($last))
		{

			if (mkdir($last, 0775))
			{
			$this->log[] = 'Cr&eacute;ation du dossier "' . $last . ' effectu&eacute;"';
			}
			else
			{
			$this->log[] = 'Cr&eacute;ation du dossier "' . $last . ' a &eacute;chou&eacute;...';
			return false;
			}
			
		}

	}
return true;
}


 public function controlExtWhiteList($arrayWhiteList=array('.png','.jpeg','.jpg','.gif'))
  {
	// Control Liste blanche
	if (in_array($this->ext, $arrayWhiteList))
	{
	$this->log[]='Listed extention';
	$this->log[]='Controle white list extention : type is '.$this->ext;
	return true;
	}return false;
}

public function control($extAttendu)
{
	// Control BlackListage
	if (in_array($this->mime, $this->MimeTypeBlacklist))
	{
	$this->log[]='BlackListed MineType';
	return false;
	}
$this->log[]='Controle extention : type is '.$this->ext;
return in_array($this->ext, $extAttendu);
}

public function control_ext($extAttendu)
{
return $this->control($extAttendu);

}



/*** Enregistrement du fichier ***/
public function save($dir_to_save)
{
	if (!is_dir($dir_to_save))
	{
	$this->log[] = 'Dossier "' . $dir_to_save . ' n\'existe pas"';
		if ($this->mkdir($dir_to_save)==false) {return false;}
	}

$this->save_as = $dir_to_save.'/'.$this->tmp_file;

	if (file_exists($this->save_as))
	{
	$this->log[] = 'Fichier existe "'.$this->save_as.'" d&eacute;j&agrave;';
	$this->save_as=$this->rename($dir_to_save);
	$this->log[] = 'Fichier renom&eacute; "'.$this->save_as.'"';
	}

	if (!copy($this->tmp . $this->tmp_file, $this->save_as))
	{
	$this->log[] = 'La copie  du fichier "' . $this->save_as . '" a &eacute;chou&eacute;...';
	return false;
	}
	else
	{
	$this->log[] = 'La copie  du fichier "' . $this->save_as . '" effectu&eacute;...';
	return true;
	}

}


public function get_upload_max_filesize()
{
$max_filesize = ini_get('upload_max_filesize');
$mul = substr($this->upload_max_filesize, -1);
$mul = ($mul == 'M' ? 1048576 : ($mul == 'K' ? 1024 : ($mul == 'G' ? 1073741824 : 1)));
return $mul*(int)$max_filesize;
}

public function controlKill()
{
$this->log[]='Effacement du tampon';
if (file_exists($this->save_as) && !is_dir($this->save_as)) {@unlink($this->save_as);}
}
public function __destruct()
{
$this->log[]='Effacement du tampon';
if (file_exists($this->tmp . $this->tmp_file) && !is_dir($this->tmp .$this->tmp_file)) {@unlink($this->tmp .$this->tmp_file);}
}


}
?>