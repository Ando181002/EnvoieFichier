<?php if( ! defined('BASEPATH')) exit('No direct script access allowed');
class Mon_Model extends CI_Model{
	public function verifierNumero($numero){
		if (preg_match('/^032\d{7}$/', $numero)) {
			return 0;
		} else {
			return 1;
		}
	}
	public function verifierMontant($montant){
		if (is_numeric($montant) && $montant>0) {
			return 0;
		} else {
			return 1;
		}
	}
	public function verifierCodeSecret($codeSecret){
		if (preg_match('/^\d{4}$/', $codeSecret)) {
			return 0;
		} else {
			return 1;
		}		
	}
	public function verifierSiUtilisateurSouscrit($numero){
		$query=$this->db->query("SELECT * from v_utilisateurom where telephone='".$numero."'");
		$resultat=array();
		foreach ($query->result_array() as $row) {
			$resultat[]=$row;
		}
		if(count($resultat)!=0){
			return 0;
		}
		else {
			return 1;
		}
	}
	public function verifierSoldeEnvoyeur($numenvoyeur,$montantAEnvoyer){
		$query=$this->db->query("SELECT * from v_utilisateurom where telephone='".$numenvoyeur."'");
		$row=$query->row_array();
		$solde=$row['solde'];
		if($montantAEnvoyer<$solde){
			return 0;
		}
		else{
			return 1;
		}
	}
	public function verifCodeUtilisateur($numero,$codeSecret){
		$query=$this->db->query("SELECT * from v_utilisateurom where telephone='".$numero."' and codesecret='".$codeSecret."'");
		$resultat=array();
		foreach ($query->result_array() as $row) {
			$resultat[]=$row;
		}
		if(count($resultat)!=0){
			return 0;
		}
		else {
			return 1;
		}
	}
	public function getIdByNumero($numero){
		$query=$this->db->query("SELECT * from v_utilisateurom where telephone='".$numero."'");
		$row=$query->row_array();
		$idutilisateur=$row['idutilisateur'];	
		return $idutilisateur;	
	}
	public function effectuerTransfert($numenvoyeur,$numdestinataire,$montant,$objet,$codeSecret,$messageTransaction){
		$idenvoyeur=$this->getIdByNumero($numenvoyeur);
		$idrecepteur=$this->getIdByNumero($numdestinataire);
		if($messageTransaction=="Transfert réussi"){
			$statut=200;
			$sql1="INSERT INTO Transactions(datetransaction,idenvoyeur,idrecepteur,montant,descri,statut,messagetransaction) VALUES (now(),'%s','%s','%s','%s','%s','%s')";
			$sql1=sprintf($sql1,$idenvoyeur,$idrecepteur,$montant,$objet,$statut,$messageTransaction);
			$sql1=$this->db->query($sql1);
			$sql2="UPDATE Utilisateurom SET solde=solde-'%s' where idutilisateurom='%s'";
			$sql2=sprintf($sql2,$montant,$idenvoyeur);
			$sql2=$this->db->query($sql2);	
			$sql3="UPDATE Utilisateurom SET solde=solde+'%s' where idutilisateurom='%s'";
			$sql3=sprintf($sql3,$montant,$idrecepteur);
			$sql3=$this->db->query($sql3);
		}
		else{
			$statut=500;
			$sql1="INSERT INTO Transactions(datetransaction,idenvoyeur,idrecepteur,montant,descri,statut,messagetransaction) VALUES (now(),'%s','%s','%s','%s','%s','%s')";
			$sql1=sprintf($sql1,$idenvoyeur,$idrecepteur,$montant,$objet,$statut,$messageTransaction);
			$sql1=$this->db->query($sql1);
		}
	}
	public function transfert($numenvoyeur,$numdestinataire,$montant,$codeSecret,$objet){
		$message="";
		$verifEnvoyeur=$this->verifierNumero($numenvoyeur);
		if($verifEnvoyeur==0){
			//$message=$message."Numero Envoyeur OK ";
			$verifDestinataire=$this->verifierNumero($numdestinataire);
			if($verifDestinataire==0){
				//$message=$message."Numero destinataire OK ";
				$verifMontant=$this->verifierMontant($montant);
				if($verifMontant==0){
					//$message=$message."Montant OK ";
					$verifCode=$this->verifierCodeSecret($codeSecret);
					if($verifCode==0){
						//$message=$message."Code secret OK ";
						$souscriptionEnvoyeur=$this->verifierSiUtilisateurSouscrit($numenvoyeur);
						if($souscriptionEnvoyeur==0){
							//$message=$message."Envoyeur souscrit";
							$souscriptionRecepteur=$this->verifierSiUtilisateurSouscrit($numdestinataire);
							if($souscriptionRecepteur==0){
								//$message=$message."Destinataire souscrit";
								$verifSolde=$this->verifierSoldeEnvoyeur($numenvoyeur,$montant);
								if ($verifSolde==0) {
									//$message=$message."Ampy ny solde";
									$testcode=$this->verifCodeUtilisateur($numenvoyeur,$codeSecret);
									if ($testcode==0) {
										//$message=$message."Code secret correct";
										$message=$message."Transfert réussi";
									}
									else{
										$message=$message."Code secret incorrect";
									}
								}
								else{
									$message=$message."Solde insuffisant";
								}
							}
							else{
								$message=$message."Destinataire non souscrit";
							}
						}
						else{
							$message=$message."Envoyeur non souscrit";
						}
					}
					else{
						$message=$message."Code secret invalide";
					}
				}
				else{
					$message=$message."Montant invalide ";
				}
			}
			else{
				$message=$message."Numero destinataire invalide";
			}
		}
		else{
			$message=$message."Numero Envoyeur invalide";
		}
		$this->effectuerTransfert($numenvoyeur,$numdestinataire,$montant,$objet,$codeSecret,$message);
		return $message;
	}
	public function historiqueTransaction($idutilisateur){
		$sql="SELECT *,CASE WHEN idenvoyeur = '".$idutilisateur."' THEN 'envoyé' WHEN idrecepteur = '".$idutilisateur."' THEN 'reçu' END AS statut FROM Transactions WHERE idenvoyeur = '".$idutilisateur."' OR idrecepteur = '".$idutilisateur."'";
		$query=$this->db->query($sql);
		$resultat=array();
		foreach ($query->result_array() as $row) {
			$resultat[]=$row;
		}
		return $resultat;
	}

}
?>