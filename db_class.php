<?php

$DBYES = 'Y';
$DBNO = 'N';

class DBConnector
{
	private $database = null;
	private $host = null;
	private $name = null;
	private $user = null;
	private $password = null;
	
	/* **** CONSTRUCTOR ************************************************************************ */
	
	public function __construct($DBHost, $DBName, $DBUser, $DBPassword)
	{
		$this->host = $DBHost;
		$this->name = $DBName;
		$this->user = $DBUser;
		$this->password = $DBPassword;
	}
   
	/* **** DESTRUCTOR ************************************************************************* */
   
	public function __destruct()
	{
		$this->disconnect();
	}
	
	public function connect()
	{
		define('DB_NAME', $this->name);
		define('DB_USER', $this->user);
		define('DB_PASSWORD', $this->password); 
		define('DB_HOST', $this->host);
       
	 
		$this->database = mysqli_connect(DB_HOST, DB_USER, DB_PASSWORD) or die('Verbindung schlug fehl: '.mysqli_error($this->database));
		mysqli_select_db($this->database, DB_NAME) or die('Verbindung schlug fehl: '.mysqli_error($this->database));
		
		mysqli_query($this->database, "SET CHARSET 'utf8'") or die('Verbindung schlug fehl: '.mysqli_error($this->database)); 
		mysqli_query($this->database, "SET NAMES 'UTF8'") or die('Verbindung schlug fehl: '.mysqli_error($this->database)); 

	}
	
	public function disconnect()
	{
		// echo '<p>Calling disconnect</p>';
		if( $this->database != null ) 
		{
			mysqli_close($this->database);
			$this->database = null;
		}
		// echo '<p>Closing disconnect</p>';
	}
	
	
	/* **** STATIC ***************************************************************************** */
	
	static public function fetchEntity($QueryResult)
	{
		// echo 'Fetch entity <br />';
		return mysqli_fetch_object($QueryResult);
	}

	static public function fetchEntityArray($QueryResult)
	{
		$result = array();
		
		while( $item = DBConnector::fetchEntity($QueryResult) )
			$result[] = $item;
		
		return $result;
	}
	
	static public function fetchRow($QueryResult)
	{
		return mysqli_fetch_row($QueryResult);
	}

	static public function fetchValueArray($QueryResult)
	{
		$result = array();
		
		while( $item = DBConnector::fetchRow($QueryResult) )
			$result[] = $item[0];
		
		return $result;
	}

	static public function fetchArray($QueryResult)
	{
		return mysqli_fetch_array($QueryResult);
	}

	static public function getNumFields($QueryResult)
	{
		return mysqli_num_fields($QueryResult);
	}
	
	static public function getNumRows($QueryResult)
	{
		return mysqli_num_rows($QueryResult);
	}

	/* **** GETTERS **************************************************************************** */
	
	public function isConnected()
	{
		return $database != null;
	}

	public function getConnection()
	{
		return $this->database;
	}
	
	public function query($Query)
	{
		$result = mysqli_query($this->database, $Query); // or die('Error: '.$Query.'<br>'.mysql_error());
		
		return $result;
	}
	
	public function getAutoIncID()
	{
		return mysqli_insert_id($this->database);
	}
	
	public function queryE($Query, &$Error)
	{
		$result = $this->query($Query);
		
		if( !$result ) $Error .= ($Error == '' ? '' : "\n\n" ).$Query."\n".mysqli_error($this->database);
		
		return $result;
	}
	
	public function get_escape_string($inputString)
	{
		
		return mysqli_real_escape_string($this->database, $inputString);
		
	}
	
	public function getEntity($Query)
	{
		// echo 'Fetching: '. $Query .'<br />';
		$result = $this->query($Query);
		// echo 'Acquired result:'. $result .'<br />';
		
		if( $result )
			return $this->fetchEntity($result);
		else
			return false;
	}
	
	public function getEntityE($Query, &$Error)
	{
		$result = $this->queryE($Query, $Error);
		
		if( $result )
			return $this->fetchEntity($result);
		else
			return false;
	}

	public function getEntityArray($Query)
	{
		$result = $this->query($Query);
		
		if( $result )
			return $this->fetchEntityArray($result);
		else
			return false;
	}

	public function getValueArray($Query)
	{
		$result = $this->query($Query);
		
		if( $result )
			return $this->fetchValueArray($result);
		else
			return false;
	}
	
	/* **** BACKUP ***************************************************************************** */

	static private function startsWith($Text, $Starting)
	{
		return !strncmp($Text, $Starting, strlen($Starting));
	}
	
	static private function getFileName($Path)
	{
		$p = strrpos($Path, '/');
		
		if( $p === false ) $p = strrpos($Path, '\\');
			
		if( $p === false )
		{
			return $Path;
		}
		else
			return substr($Path, $p+1, strlen($Path)-1);
	}
	
	static private function isNumber($DBType)
	{
		return preg_match('/^(tiny|small|medium|big){0,1}int[\w\W]*|^(float|double|decimal)[\w\W]*/', $DBType);
	}
	static 
	private function isBinary($DBType)
	{
		return preg_match('/^((var){0,1}binary|(tiny|medium|long){0,1}blob)[\w\W]*/', $DBType);
	}
	
	static private function strToHex($string)
	{
		$hex = '';
		for ($i=0; $i<strlen($string); $i++){
			$ord = ord($string[$i]);
			$hexCode = dechex($ord);
			$hex .= substr('0'.$hexCode, -2);
		}
		return strToUpper($hex);
	}
	
	public function dumpTables($File, $Tables, $Compress)
	{
		$Tables = is_array($Tables) ? $Tables : explode(',', $Tables);
		
		$time = time();
		
		$return  = "-- -----------------------------------------------------------------------------\n";
		$return .= "-- Backup: ".date("Y-m-d H:m:s", $time)."\n";
		$return .= "-- -----------------------------------------------------------------------------\n";
		$return .= "\n";
		$return .= "\n";
		$return .= "/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;\n";
		$return .= "/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;\n";
		$return .= "/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;\n";
		$return .= "/*!40101 SET NAMES utf8 */;\n";
		$return .= "/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;\n";
		$return .= "/*!40103 SET TIME_ZONE='+00:00' */;\n";
		$return .= "/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;\n";
		$return .= "/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;\n";
		$return .= "/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;\n";
		$return .= "/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;\n";
		$return .= "\n";
		
		foreach($Tables as $table)
		{
			$create = $this->fetchRow($this->query('SHOW CREATE TABLE '.$table));
			$data = $this->query('SELECT * FROM '.$table);
			$fields = $this->getNumFields($data);
			
			$tblDescription = $this->getEntityArray('DESCRIBE '.$table);
			
			$return .= "\n";
			$return .= "-- -----------------------------------------------------------------------------\n";
			$return .= "-- table: ".$table."\n";
			$return .= "-- -----------------------------------------------------------------------------\n";
			$return .= "\n";
			$return .= "-- structure\n";
			$return .= "\n";
			$return .= "DROP TABLE IF EXISTS `".$table."`;\n";
			$return .= "\n";
			$return .= "/*!40101 SET @saved_cs_client     = @@character_set_client */;\n";
			$return .= "/*!40101 SET character_set_client = utf8 */;\n";
			$return .= "\n".$create[1].";\n\n";
			$return .= "/*!40101 SET character_set_client = @saved_cs_client */;\n";
			$return .= "\n";
			$return .= "-- data\n";
			$return .= "\n";
			$return .= "LOCK TABLES `".$table."` WRITE;\n";
			$return .= "/*!40000 ALTER TABLE `".$table."` DISABLE KEYS */;\n";
			$return .= "\n";

			for($i = 0; $i < $fields; $i++) 
			{
				while($row = $this->fetchRow($data))
				{
					$return.= 'INSERT INTO '.$table.' VALUES (';
					
					for($j = 0; $j < $fields; $j++) 
					{
						$row[$j] = addslashes($row[$j]);
						// $row[$j] = preg_replace("/\n/", "/\\n/", $row[$j]);
						$row[$j] = preg_replace("#\\r\\n#", '\\r\\n', $row[$j]);  
						// $row[$j] = preg_replace("/\n/", "\\r\\n", $row[$j]);
						if( isset($row[$j]) )
						{
							if( $this->isNumber($tblDescription[$j]->Type) )
								$return.= ''.$row[$j].'';
							// elseif( $this->isBinary($tblDescription[$j]->Type) )
								// $return.= '_binary 0x'.base64_decode($row[$j]).'';
							else
								$return.= '"'.$row[$j].'"';
						}
						else 
							$return.= '""';
						
						if( $j < ($fields-1) )
							$return.= ', ';
					}
					$return.= ");\n";
				}
			}
			
			$return .= "\n";
			$return .= "/*!40000 ALTER TABLE `".$table."` ENABLE KEYS */;\n";
			$return .= "UNLOCK TABLES;\n";
			$return .= "\n";
		}
		
		$return .= "\n";
		$return .= "/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;\n";
		$return .= "/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;\n";
		$return .= "/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;\n";
		$return .= "/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;\n";
		$return .= "/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;\n";
		$return .= "/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;\n";
		$return .= "/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;\n";
		$return .= "/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;\n";
		$return .= "\n";
		$return .= "\n";
		$return .= "-- -----------------------------------------------------------------------------\n";
		$return .= "-- Completed: ".date("Y-m-d H:m:s")."\n";
		$return .= "-- -----------------------------------------------------------------------------\n";
		
		//$return  = mb_convert_encoding($return,'UTF-8');
		
		//save file
		if( !extension_loaded("zlib") ) 
			$Compress = false; 
	   
		if( $Compress ) 
		{
			$fp = gzopen($File.'.sql.gz', 'w9');/*'_'.date('Y-m-d_H-m-s', $time).*/
			gzwrite($fp, utf8_encode("\xEF\xBB\xBF".$return));
			gzclose($fp);
			
			return $this->getFileName($File.'.sql.gz');
		}
		else
		{
			$fp = fopen($File.'.sql', 'wb');/*'_'.date('Y-m-d_H-m-s', $time).*/
			fwrite($fp, pack("CCC",0xef,0xbb,0xbf));
			fwrite($fp, $return);
			fclose($fp);
			
			return $this->getFileName($File.'.sql');
		}
	}
	
	public function dump($File, $Compress) 
	{	
		$exec = 'mysqldump --user='.$this->user.' --password='.$this->password.' --host='.$this->host.' '.$this->name.' > '.$File.'.sql';

		exec($exec);
		
		//save file
		if( !extension_loaded("zlib") ) 
			$Compress = false; 
		
		if( $Compress )
		{
			$file = $File.'.sql';
			$gzfile = $File.'.sql.gz';
			
			// Open the gz file (w9 is the highest compression)
			$fp = gzopen ($gzfile, 'w9');
			
			// Compress the file
			gzwrite ($fp, file_get_contents($file));
			
			// Close the gz file and we are done
			gzclose($fp);
			
			if( file_exists($gzfile) )
				unlink($file);
		}

		// $result = $this->query('SHOW TABLES');
		
		// $tables = array();
		// while($row = $this->fetchRow($result))
			// $tables[] = $row[0];
			
		// return $this->dumpTables($File, $tables, $Compress);
	}

	public function runMySQLScript($File, $IsCompressed)
	{
		if( $IsCompressed)
		{
			if( !extension_loaded("zlib") ) return false; 
			
			$fp = gzopen($File, 'r');
			if( $fp === false ) return false;
			
			$sql = '';
			$satetmentEnd = false;
			
			while( !gzeof($fp) )
			{
				$buffer = gzgets($fp/*, 4096*/);
				
				$buffer = trim($buffer);
				if( strlen($buffer) > 0 && !$this->startsWith($buffer, '--') )
				{
					$satetmentEnd = ( substr($buffer, -1) === ';' );
					$sql .= $buffer;

					if( $satetmentEnd )
					{
						$this->query($sql);
						$sql = '';
					}
				}
			}
			
			gzclose($fp);
		}
		else
		{
			$fp = fopen($File, 'r');
			if( $fp === false ) return false;
			
			$sql = '';
			$satetmentEnd = false;
			
			while( !feof($fp) )
			{
				$buffer = fgets($fp/*, 4096*/);
				
				$buffer = trim($buffer);
				if( strlen($buffer) > 0 && !$this->startsWith($buffer, '--') )
				{
					$satetmentEnd = ( substr($buffer, -1) === ';' );
					$sql .= $buffer;

					if( $satetmentEnd )
					{
						// $sql = preg_replace("/\\n/", "/\n/", $sql);
						$sql = stripslashes($sql);
						// $this->query(utf8_encode($sql));
						$this->query(mb_convert_encoding($sql,'UTF-8'));
						$sql = '';
					}
				}
			}
			
			fclose($fp);
		}
		
		return true;
	}
}

?>
