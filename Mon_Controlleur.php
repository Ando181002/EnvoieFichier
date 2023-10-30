<?php 	
defined('BASEPATH') OR exit('No direct script access allowed');

class Mon_Controlleur extends CI_Controller{
	public function __construct(){
        parent::__construct();
    }
    public function index()
    {
        $this->load->view('Accueil');
    }
    public function transfert(){
        $numenvoyeur=$_POST['numenvoyeur'];
        $numrecepteur=$_POST['numrecepteur'];
        $montant=$_POST['montant'];
        $objet=$_POST['objet'];
        $codesecret=$_POST['codesecret'];
        $this->load->model('Mon_Model');
        $resultat=$this->Mon_Model->transfert($numenvoyeur,$numrecepteur,$montant,$codesecret,$objet);
        if ($resultat=="Transfert réussi") {
            $response = array(
                'status' => 'success',
                'message' => 'Transfert réussi',
                'data' => $resultat
            );
        } else {
            $response = array(
                'status' => 'error',
                'message' => 'Échec de transfert',
                'data' => $resultat,
            );
        }

        $this->output
            ->set_content_type('application/json')
            ->set_output(json_encode($response));

    }
    public function historique() {
        $numero=$_GET['numero'];
        $this->load->model('Mon_Model');
        $idutilisateur=$this->Mon_Model->getIdByNumero($numero);
        $historique=$this->Mon_Model->historiqueTransaction($idutilisateur);
        $data = array(
            'status' => 'success',
            'message' => 'Historique de transactions',
            'data' => $historique,
        );
    
        $this->output
            ->set_content_type('application/json')
            ->set_output(json_encode($data));
    }
    public function creerEquipe(){
        $totalHeure=4;
        $totalMinute=$totalHeure*60;
        $nbrTerrain=3;
        $dureeMatch=40;
        $nbrMatchPossible=($totalMinute/$dureeMatch)*$nbrTerrain;
        $nbrInscrit=200;
        $nbrJoueur_Equipe=7;
        $nbMatch=intval($nbrInscrit/($nbrJoueur_Equipe*2));
        if($nbMatch<=$nbrMatchPossible){
            echo "Mety";
        }
        else{
            $nbrJoueur_Equipe=$nbrJoueur_Equipe+1;
            $nbMatch=intval($nbrInscrit/($nbrJoueur_Equipe*2));
            echo $nbMatch;
        }
    }
}
?>
