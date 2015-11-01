<?PHP
require_once "constants.php";

//Die Klasse verarbeitet Datenbank-Zugriffe. 
class MySQL
{
	private $pLink;					//Link auf die Datenbank

	//Konstruktor
	public function connectDB()
	{
		//Verbindung herstellen
		$this->pLink = mysql_connect( DB_HOST, DB_USER, DB_PASS ) or die( "Keine Verbindung möglich: ".mysql_error() );
		$this->selectDatabase( MYSQLDB );
		//echo("Verbindung zur DB hergestellt...");
		
		mysql_set_charset('utf8', $this->pLink); 
	}

	//Datenbank auswählen
	private function selectDatabase( $sDatabase )
	{
		mysql_select_db( $sDatabase ) or die( "Auswahl der Datenbank fehlgeschlagen" );
	}

	//Beendet die Verbindung zum Datenbankserver
	public function disconnectFromDatabase()
	{
		mysql_close( $this->pLink );
	}
	
	//------------------------------------------------------------------------------------------------------------------------------------
	
	public function getMembers($col, $attr)
	{
		if( $this->pLink == false )
			return false;
		
		$sQuery = "SELECT * FROM ".MEMBER_TABLE." WHERE $col = '".$attr."' ORDER BY ID";
		$sResult = mysql_query( $sQuery, $this->pLink ) or die( "Anfrage fehlgeschlagen: " . mysql_error() );
		
		$it = mysql_num_rows($sResult);
		
		if( $it > 0 )
		{
			$pArray = array($it);
			$counter = 0;
		
			while($sRow = mysql_fetch_assoc( $sResult ) )
			{
				$name = $sRow['firstname']." ".$sRow['name'];
				$pArray[$counter] = $name;
				$counter++;
			}
		}
		mysql_free_result( $sResult );
		return $pArray;
	}
	
	
	//-------------------------------------------------------------------------------------
	
	
	//Sprachen für die Startseite holen
	public function getLanguages()
	{
		
		if( $this->pLink == false )
			return false;

		$sQuery = "SELECT * FROM ".LANGUAGE_TABLE." ORDER BY sort_index";
		$sResult = mysql_query( $sQuery, $this->pLink ) or die( "Anfrage fehlgeschlagen: " . mysql_error() );
		
		$it = mysql_num_rows($sResult);
		
		if( $it > 0 )
		{
			$pArray = array($it);
			$counter = 0;
		
			while($sRow = mysql_fetch_assoc( $sResult ) )
			{
				$tmp = $sRow['language'];
				$pArray[$counter] = $tmp;
				$counter++;
			}
		}
		mysql_free_result( $sResult );
		return $pArray;
	}
	
	public function getFirstLanguage()
	{
		
		if( $this->pLink == false )
			return false;

		$sQuery = "SELECT * FROM ".LANGUAGE_TABLE." ORDER BY sort_index";
		$sResult = mysql_query( $sQuery, $this->pLink ) or die( "Anfrage fehlgeschlagen: " . mysql_error() );
		
		$it = mysql_num_rows($sResult);
		
		if( $it > 0 )
		{
			$pArray = array($it);
			$counter = 0;
		
			while($sRow = mysql_fetch_assoc( $sResult ) )
			{
				$tmp = $sRow['language'];
				$pArray[$counter] = $tmp;
				$counter++;
			}
		}
		mysql_free_result( $sResult );
		return $pArray[0];
	}
	
	public function getNames($cLang)
	{
		if( $this->pLink == false )
			return false;

		$sQuery = "SELECT * FROM languages WHERE language = '".$cLang."'";
		$sResult = mysql_query( $sQuery, $this->pLink ) or die( "Anfrage fehlgeschlagen: " . mysql_error() );
		
		$it = mysql_num_rows($sResult);
		
		if( $it > 0 )
		{
			$pArray = array($it);
			
		
			while($sRow = mysql_fetch_assoc( $sResult ) )
			{
				$tmp = $sRow['language'];
				$pArray[0] = $sRow['name'];
				$pArray[1] = $sRow['firstname'];
				$pArray[2] = $sRow['btnStart'];
				$pArray[3] = $sRow['index_headline'];
				$pArray[4] = $sRow['index_subtext'];
				$pArray[5] = $sRow['index_note'];
				$pArray[6] = $sRow['index_lang_select'];
				$pArray[7] = $sRow['index_company'];
				$pArray[8] = $sRow['footer_lnk_imprint'];
				$pArray[9] = $sRow['footer_lnk_ds'];
				$pArray[10] = $sRow['headline_training'];
				$pArray[11] = $sRow['headline_backend'];
			}
		}
		mysql_free_result( $sResult );
		return $pArray;
	}
	
	
	public function writeUserValues($firstname, $name, $firma, $cLang)
	{
		if( $this->pLink == false )
			return false;
			
		$exists = false;
		
		$sQuery = "SELECT * FROM ".USER_TABLE;
		$sResult = mysql_query( $sQuery, $this->pLink ) or die( "Anfrage fehlgeschlagen: " . mysql_error() );
		
		$it = mysql_num_rows($sResult);
		
		while($sRow = mysql_fetch_assoc( $sResult ) )
		{
			$nn = $sRow['name'];
			$fn = $sRow['firstname'];
			$ln = $sRow['language'];
			$frm = $sRow['company'];
			
			if($nn == $name && $fn == $firstname && $frm == $firma && $ln == $cLang)
				$exists = true;
		}
		
	
		$cDate = date("Y-m-d");
		if(!$exists)
		{
			$sQuery = "INSERT INTO ".USER_TABLE." (name, firstname, date, language, company) VALUES ('".$name."', '".$firstname."', '".$cDate."', '".$cLang."', '".$firma."')";
		}
		else
		{
			$sQuery = "UPDATE ".USER_TABLE." SET date='".$cDate."' WHERE name='".$name."' AND firstname='".$firstname."' AND language='".$cLang."' AND company='".$firma."'";
		}
		mysql_query( $sQuery, $this->pLink ) or die( "Anfrage fehlgeschlagen: " . mysql_error() );
		
		return $exists;
	}
	
	public function getUserID($firstname, $name, $firma, $cLang)
	{
		if( $this->pLink == false )
			return false;
			
		$sQuery = "SELECT * FROM ".USER_TABLE." WHERE name='".$name."' AND firstname='".$firstname."' AND language='".$cLang."' AND company='".$firma."'";
		$sResult = mysql_query( $sQuery, $this->pLink ) or die( "Anfrage fehlgeschlagen: " . mysql_error() );
		
		$it = mysql_num_rows($sResult);
		
		if( $it > 0 )
		{
			while($sRow = mysql_fetch_assoc( $sResult ) )
			{
				$tmp = $sRow['ID'];
			}
		}
		
		return $tmp;
	}
	
	public function writeWBT($nn, $id)
	{
		if( $this->pLink == false )
			return false;
		
		$wtb = "wtb".$nn;
		$sQuery = "UPDATE ".USER_TABLE." SET $wtb='1' WHERE ID='".$id."'";
		mysql_query( $sQuery, $this->pLink ) or die( "Anfrage fehlgeschlagen: " . mysql_error() );
		
	}
	
	public function getCheckedTrainings($id)
	{
		if( $this->pLink == false )
			return false;
	
		$sQuery = "SELECT * FROM ".USER_TABLE." WHERE ID='".$id."' ";
		$sResult = mysql_query( $sQuery, $this->pLink ) or die( "Anfrage fehlgeschlagen: " . mysql_error() );
		
		$it = mysql_num_rows($sResult);
		
		if( $it > 0 )
		{
			$pArray = array($it);
			while($sRow = mysql_fetch_assoc( $sResult ) )
			{
				$pArray[0] = $sRow['wtb1'];
				$pArray[1] = $sRow['wtb2'];
				$pArray[2] = $sRow['wtb3'];
				$pArray[3] = $sRow['wtb4'];
			}
		}
		
		return $pArray;
	
	}
	
	public function getUserData($sortArg)
	{
		if( $this->pLink == false )
			return false;
	
		$sQuery = "SELECT * FROM ".USER_TABLE." ORDER BY ".$sortArg.";";
		$sResult = mysql_query( $sQuery, $this->pLink ) or die( "Anfrage fehlgeschlagen: " . mysql_error() );
		
		$it = mysql_num_rows($sResult);
		$cnt = 0;
		
		if( $it > 0 )
		{
			$pArray = array();
			while($sRow = mysql_fetch_assoc( $sResult ) )
			{
				$nArr = array();
				
				$nArr[0] = $sRow['firstname'];
				$nArr[1] = $sRow['name'];
				$nArr[2] = $sRow['date'];
				$nArr[3] = $sRow['language'];
				
				$nArr[4] = $sRow['certificate'];
				
				$nArr[5] = $sRow['quest_1'];
				$nArr[6] = $sRow['quest_2'];
				$nArr[7] = $sRow['quest_3'];
				$nArr[8] = $sRow['quest_4'];
				$nArr[9] = $sRow['quest_5'];
				$nArr[10] = $sRow['quest_6'];
				
				$nArr[11] = $sRow['wtb1'];
				$nArr[12] = $sRow['wtb2'];
				$nArr[13] = $sRow['wtb3'];
				$nArr[14] = $sRow['wtb4'];
				
				$nArr[15] = $sRow['company'];
				
				
				$pArray[$cnt] = $nArr;
				$cnt ++;
			}
		}
		
		return $pArray;
	
	}
	
	public function getTrainingTexts($lang)
	{
		if( $this->pLink == false )
			return false;
	
		$sQuery = "SELECT * FROM ".LANGUAGE_TABLE." WHERE language='".$lang."' ";
		$sResult = mysql_query( $sQuery, $this->pLink ) or die( "Anfrage fehlgeschlagen: " . mysql_error() );
		
		$it = mysql_num_rows($sResult);
		
		if( $it > 0 )
		{
			$pArray = array($it);
			while($sRow = mysql_fetch_assoc( $sResult ) )
			{
				$pArray[0] = $sRow['btn_wbt1'];
				$pArray[1] = $sRow['btn_wbt2'];
				$pArray[2] = $sRow['btn_wbt3'];
				$pArray[3] = $sRow['btn_wbt4'];
				$pArray[4] = $sRow['btn_survey'];
				$pArray[5] = $sRow['script_wbt1'];
				$pArray[6] = $sRow['script_wbt2'];
				$pArray[7] = $sRow['script_wbt3'];
				$pArray[8] = $sRow['script_wbt4'];
			}
		}
		
		return $pArray;
	
	}
	
	public function getSurveyTexts($lang)
	{
		if( $this->pLink == false )
			return false;
	
		$sQuery = "SELECT * FROM ".LANGUAGE_TABLE." WHERE language='".$lang."' ";
		$sResult = mysql_query( $sQuery, $this->pLink ) or die( "Anfrage fehlgeschlagen: " . mysql_error() );
		
		$it = mysql_num_rows($sResult);
		
		if( $it > 0 )
		{
			$pArray = array($it);
			while($sRow = mysql_fetch_assoc( $sResult ) )
			{
				$pArray[0] = $sRow['btn_print_certificate'];
				
				$pArray[1] = $sRow['survey_quest_1'];
				$pArray[2] = $sRow['survey_quest_2'];
				$pArray[3] = $sRow['survey_quest_3'];
				$pArray[4] = $sRow['survey_quest_4'];
				$pArray[5] = $sRow['survey_quest_5'];
				$pArray[6] = $sRow['survey_quest_6'];
				
				$pArray[7] = $sRow['hint_1'];
				$pArray[8] = $sRow['hint_2'];
				
				$pArray[9] = $sRow['headline_survey'];
				
			}
		}
		
		return $pArray;
	
	}
	
	public function getCertificatePreviewTexts($lang)
	{
		if( $this->pLink == false )
			return false;
	
		$sQuery = "SELECT * FROM ".LANGUAGE_TABLE." WHERE language='".$lang."' ";
		$sResult = mysql_query( $sQuery, $this->pLink ) or die( "Anfrage fehlgeschlagen: " . mysql_error() );
		
		$it = mysql_num_rows($sResult);
		
		if( $it > 0 )
		{
			$pArray = array($it);
			while($sRow = mysql_fetch_assoc( $sResult ) )
			{
				$pArray[0] = $sRow['cp_headline'];
				$pArray[1] = $sRow['cp_button'];
				$pArray[2] = $sRow['cp_text'];
			}
		}
		
		return $pArray;
	
	}
	
	public function setAnswers($uid, $arr)
	{
		if( $this->pLink == false )
			return false;
		
		for($i = 0; $i < sizeof($arr); $i ++)
		{
			$c = $i + 1;
			$q = "quest_".$c;
			$sQuery = "UPDATE ".USER_TABLE." SET $q='".$arr[$i]."' WHERE ID='".$uid."'";
			mysql_query( $sQuery, $this->pLink ) or die( "Anfrage fehlgeschlagen: " . mysql_error() );
		}
	
	}
	
	public function setCertificate($uid)
	{
		if( $this->pLink == false )
			return false;
		
		$sQuery = "UPDATE ".USER_TABLE." SET certificate='1' WHERE ID='".$uid."'";
		mysql_query( $sQuery, $this->pLink ) or die( "Anfrage fehlgeschlagen: " . mysql_error() );
	}
	
	public function isAdmin($user, $pass)
	{
		if( $this->pLink == false )
			return false;
	
		$sQuery = "SELECT * FROM ".ADMIN_TABLE;
		$sResult = mysql_query( $sQuery, $this->pLink ) or die( "Anfrage fehlgeschlagen: " . mysql_error() );
		
		$it = mysql_num_rows($sResult);
		
		if( $it > 0 )
		{
			while($sRow = mysql_fetch_assoc( $sResult ) )
			{
				if($sRow['user'] == $user && $sRow['pass'] == $pass)
				{
					return true;
				}
				
			}
		}
		
		return false;
	
	}
	
	// +++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	
	
	
	//Vorhandene Dokumente einer Sprache holen
	public function getDocuments($lang)
	{
		if( $this->pLink == false )
			return false;

		/*
		$sQuery = "SELECT * FROM ".LANGUAGE_TABLE." WHERE language = '".$lang."'";
		$sResult = mysql_query( $sQuery, $this->pLink ) or die( "Anfrage fehlgeschlagen: " . mysql_error() );
		
		$lIndex = mysql_num_rows($sResult);
		
		if( $lIndex > 0 )
		{
			while($sRow = mysql_fetch_assoc( $sResult ) )
			{
				$tmp = $sRow['id'];
			}
		}
		
		mysql_free_result( $sResult );
		*/
		
		$tmp = $lang;
		
		$sQuery2 = "SELECT * FROM ".DOCUMENT_TABLE." WHERE language = '".$tmp."' ORDER BY sort_index";
		$sResult2 = mysql_query( $sQuery2, $this->pLink ) or die( "Anfrage fehlgeschlagen: " . mysql_error() );
		
		$lDocs = mysql_num_rows($sResult2);
		
		if( $lDocs > 0 )
		{
			$docArray = array($lDocs);
			$counter = 0;
			
			while($sDoc = mysql_fetch_assoc( $sResult2 ) )
			{
				$cdoc 		= $sDoc['document'];
				$cpath 		= $sDoc['path'];
				$pArr 		= array();
				$pArr[0] 	= $cdoc;
				$pArr[1] 	= $cpath;
				$pArr[2]	= $tmp;
				
				$docArray[$counter] = $pArr;
				$counter++;
			}
		}
		
		mysql_free_result( $sResult2 );
		return $docArray;
	}
	
	//Texte holen
	public function getText($ind)
	{
		if( $this->pLink == false )
			return false;

		$sQuery = "SELECT * FROM ".LANGUAGE_TABLE." WHERE id = '".$ind."'";
		$sResult = mysql_query( $sQuery, $this->pLink ) or die( "Anfrage fehlgeschlagen: " . mysql_error() );
		
		$lIndex = mysql_num_rows($sResult);
		
		if( $lIndex > 0 )
		{
			$txtArray = array($lIndex);
			while($sRow = mysql_fetch_assoc( $sResult ) )
			{
				$headline 			= $sRow['headline'];
				$description 		= $sRow['description'];
				$button 			= $sRow['button'];
				$select_all			= $sRow['select_all'];
				$email_txt			= $sRow['email_txt'];
				$m_subject 			= $sRow['mail_subject'];
				$m_msg 				= $sRow['mail_message'];
				$d_msg 				= $sRow['download_message'];
				$d_msg2				= $sRow['download_message_2'];
				$agree				= $sRow['agreement'];
				
				$txtArray[0] 		= $headline;
				$txtArray[1] 		= $description;
				$txtArray[2] 		= $button;
				$txtArray[3] 		= $m_subject;
				$txtArray[4] 		= $m_msg;
				$txtArray[5] 		= $d_msg;
				$txtArray[6] 		= $d_msg2;
				$txtArray[7] 		= $email_txt;
				$txtArray[8] 		= $select_all;
				$txtArray[9] 		= $agree;
			}
		}
		
		mysql_free_result( $sResult );
		return $txtArray;
	}
	
	//Downloads in die DB schreiben
	public function writeRequest($mail, $lang, $doc, $dt)
	{
		if( $this->pLink == false )
			return false;
	
		$sQuery = "INSERT INTO ".REQUEST_TABLE." (email, language, document, date) VALUES ('".$mail."', '".$lang."', '".$doc."', '".$dt."')";
		mysql_query( $sQuery, $this->pLink ) or die( "Anfrage fehlgeschlagen: " . mysql_error() );
		
		$sQuery = "UPDATE ".DOCUMENT_TABLE." SET counter=counter+1 WHERE path='".$doc."'";
		mysql_query( $sQuery, $this->pLink ) or die( "Anfrage fehlgeschlagen: " . mysql_error() );
	}
	
	//Button Beschriftung holen
	public function getBtnText($doc)
	{
		if( $this->pLink == false )
			return false;
	
		$sQuery = "SELECT * FROM ".DOCUMENT_TABLE." WHERE path = '".$doc."'";
		$sResult = mysql_query( $sQuery, $this->pLink ) or die( "Anfrage fehlgeschlagen: " . mysql_error() );
		
		$lIndex = mysql_num_rows($sResult);
		
		if( $lIndex > 0 )
		{
			while($sRow = mysql_fetch_assoc( $sResult ) )
			{
				$btn = $sRow['document'];
			}
		}
		
		return $btn;
	}
	
	
	
	
	
	
	
	
	/*
	//Aktuelle Frage aus d. DB zur�ckgeben.
	public function getQuestion($index)
	{
		if( $this->pLink == false )
			return false;

		$sQuery = "SELECT * FROM ".QUESTION_TABLE." WHERE id = '".$index."'";
		$sResult = mysql_query( $sQuery, $this->pLink ) or die( "Anfrage fehlgeschlagen: " . mysql_error() );
		
		$it = mysql_num_rows($sResult);
		if( $it > 0 )
		{
			$pArray = array($it);
			$counter = 0;
		
			while($sRow = mysql_fetch_assoc( $sResult ) )
			{
				$tmpArray = array( $sRow['Frage'], $sRow['Antwort1'], $sRow['Antwort2'], $sRow['Antwort3'], $sRow['Antwort4'], $sRow['Antwort5'] );
				$pArray[$counter] = $tmpArray;
				$counter++;
			}
		}
		mysql_free_result( $sResult );
		return $pArray;
	}
	
	//Pr�fen, ob Gewinn f�llig ist, ggf. Aktuellen Gewinncode zur�ckgeben. 
	public function getCurrentCode()
	{
		if( $this->pLink == false )
			return false;
	
		//$sQuery = "SELECT * FROM ".CODE_TABLE." WHERE date='".$_SESSION['curDay']."'";
		$sQuery = "SELECT * FROM ".CODETEST_TABLE." WHERE date='".$_SESSION['curDay']."'";
		$sResult = mysql_query( $sQuery, $this->pLink ) or die( "Anfrage fehlgeschlagen: " . mysql_error() );
		
		$it = mysql_num_rows($sResult);
		if( $it > 0 )
		{
			$pArray = array($it);
			$counter = 0;
			$ccode = STR_LOSE;
			
			while($sRow = mysql_fetch_assoc( $sResult ) )
			{
				$used = $sRow['used'];
				$counter ++;
				$chr = false;
				
				if($_SESSION['curHour'] > 9 && $counter < 2 && $used == 0)
					$chr = true;
					
				if($_SESSION['curHour'] > 10 && $counter < 3 && $used == 0)
					$chr = true;
					
				if($_SESSION['curHour'] > 11 && $counter < 4 && $used == 0)
					$chr = true;
					
				if($_SESSION['curHour'] > 12 && $counter < 5 && $used == 0)
					$chr = true;
					
				if($_SESSION['curHour'] > 13 && $counter < 6 && $used == 0)
					$chr = true;
					
				if($_SESSION['curHour'] > 14 && $counter < 7 && $used == 0)
					$chr = true;
					
				if($_SESSION['curHour'] > 15 && $counter < 9 && $used == 0)
					$chr = true;
					
				if($_SESSION['curHour'] > 16 && $counter < 10 && $used == 0)
					$chr = true;
				
				
				if(!$chr)
				{
					switch($counter)
					{
						case 1:
							if($_SESSION['curHour'] == 9 && $_SESSION['curMin'] > 5 && $_SESSION['curMin'] <= 50 && $used == 0)
								$chr = true;
						break;
						
						case 2:
							if(($_SESSION['curHour'] == 9 && $_SESSION['curMin'] > 55) || $_SESSION['curHour'] == 10 &&( $_SESSION['curMin'] <= 40) && $used == 0)
								$chr = true;
						break;
						
						case 3:
							if(($_SESSION['curHour'] == 10 && $_SESSION['curMin'] > 45) || $_SESSION['curHour'] == 11 &&( $_SESSION['curMin'] <= 30) && $used == 0)
								$chr = true;
						break;
						
						case 4:
							if(($_SESSION['curHour'] == 11 && $_SESSION['curMin'] > 35) || $_SESSION['curHour'] == 12 &&( $_SESSION['curMin'] <= 20) && $used == 0)
								$chr = true;
						break;
						
						case 5:
							if(($_SESSION['curHour'] == 12 && $_SESSION['curMin'] > 25) || $_SESSION['curHour'] == 13 &&( $_SESSION['curMin'] <= 20) && $used == 0)
								$chr = true;
						break;
						
						case 6:
							if(($_SESSION['curHour'] == 13 && $_SESSION['curMin'] > 25) || $_SESSION['curHour'] == 14 &&( $_SESSION['curMin'] <= 10) && $used == 0)
								$chr = true;
						break;
						
						case 7:
							if($_SESSION['curHour'] == 14 && $_SESSION['curMin'] > 15 && $_SESSION['curMin'] <= 59 && $used == 0)
								$chr = true;
						break;
						
						case 8:
							if($_SESSION['curHour'] == 15 && $_SESSION['curMin'] > 5 && $_SESSION['curMin'] <= 50 && $used == 0)
								$chr = true;
						break;
						
						case 9:
							if(($_SESSION['curHour'] == 15 && $_SESSION['curMin'] > 55) || $_SESSION['curHour'] == 16 &&( $_SESSION['curMin'] <= 40) && $used == 0)
								$chr = true;
						break;
						
						case 10:
							if(($_SESSION['curHour'] == 16 && $_SESSION['curMin'] > 45) || $_SESSION['curHour'] == 17 &&( $_SESSION['curMin'] <= 55) && $used == 0)
								$chr = true;
						break;
					}
				}
				
				if($chr)
				{
					$ccode = $sRow['code'];
					$cid = $sRow['id'];
					//$sQuery = "UPDATE ".CODE_TABLE." SET used=1 WHERE id='".$cid."'";
					$sQuery = "UPDATE ".CODETEST_TABLE." SET used=1 WHERE id='".$cid."'";
					mysql_query( $sQuery, $this->pLink ) or die( "Anfrage fehlgeschlagen: " . mysql_error() );
					break;
				}
			}
		}
		mysql_free_result( $sResult );
		return $ccode;
	}
	
	
	//Umfrage-Ergebnis in die DB schreiben
	public function writeResults($arr)
	{
		if( $this->pLink == false )
			return false;
	
		$sQuery = "INSERT INTO ".RESULT_TABLE." (Frage1, Frage2, Frage3, Frage4, Frage5, Frage6, Frage7, Frage8, Frage9) VALUES ('".$arr[0]."', '".$arr[1]."', '".$arr[2]."', '".$arr[3]."', '".$arr[4]."', '".$arr[5]."', '".$arr[6]."', '".$arr[7]."', '".$arr[8]."')";
		mysql_query( $sQuery, $this->pLink ) or die( "Anfrage fehlgeschlagen: " . mysql_error() );
	
	}
	
	*/
	
}
?>