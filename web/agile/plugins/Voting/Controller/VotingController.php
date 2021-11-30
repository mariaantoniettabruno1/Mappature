<?php
namespace Kanboard\Plugin\Voting\Controller;

use Kanboard\Controller\BaseController;

/**
 * Voting Controller
 *
 * @package Kanboard\Plugin\Voting\Controller
 * @author Manel P�rez Clot <Open University of Catalonia (UOC)>
 * @version 1.0, 2018-05-13
 *         
 */
class VotingController extends BaseController
{

    /**
     * Mostra la llista de votacions pendents de realitzar per a l'usuari actual
     *
     * @access public
     */
    public function viewPendingVotes()
    {
        // array que contindr� les votacions amb els seus usuaris a avaluar
        $voting = array();
        // actualitza els pesos de tots els usuaris
        $this->weightCalculation();
        // obt� l'usuari actual
        $currentUser = $this->getUser();
        // obt� les votacions pendents per a l'usuari
        $votes = $this->votingModel->getPendingVotes($currentUser['id']);
        
        // recorre cada votaci� pendent
        foreach ($votes as $vote) {
            // carrega a l'array la votaci� i l'usuari a avaluar
            $voting[$vote['id']] = array(
                'vote' => $vote,
                'owner' => $this->userModel->getById($vote['evaluated_user_id'])
            );
        }
        
        // mostra la vista amb la llista de votacions
        $this->response->html($this->helper->layout->app('Voting:view/votingList', array(
            'voting' => $voting,
            'user' => $currentUser
        )));
    }

    /**
     * Mostra la llista d'usuaris amb el seu corresponent pes percentual
     *
     * @access public
     */
    public function viewUsersWeight()
    {
		// actualitza els pesos de tots els usuaris
        $this->weightCalculation();
        // obt� l'usuari actual
        $currentUser = $this->getUser();
        // obt� els grups d'usuaris als que pertany l'usuari actual
        $groups = $this->groupMemberModel->getGroups($currentUser['id']);
        // obt� tots els usuaris
        $allUsers = $this->userModel->getAll();
        
        // recorre tots els usuaris
        foreach ($allUsers as $user) {
            // recorre els grups de l'usuari actual per tenir en compte nom�s els usuaris dels seus grups
            foreach ($groups as $group) {
                // obt� un usuari que pertany a algun grup dels de l'usuari actual
                if ($this->groupMemberModel->isMember($group['id'], $user['id'])) {
                    // obt� la qualitat de l'usuari, i en el cas que sigui 'null' o 0, li assigna un 1%
                    $user['weight'] = (empty($user['weight']) ? 1 : ($user['weight'] = 0 ? 1 : $user['weight']));
                    
                    // carrega a l'array la votaci� i l'usuari a avaluar
                    $groupUser[$user['id']] = array(
                        'user' => $user
                    );
                }
            }
        }
        
        // mostra la vista amb la llista d'usuaris i el seu pes
        $this->response->html($this->helper->layout->app('Voting:view/weightUserList', array(
            'groupUser' => $groupUser
        )));
    }

    /**
     * Crea una nova votaci� per a la acci� que acaba de relitzar l'usuari
     *
     * @access public
     * @param string $title
     * @return boolean
     */
    public function addVoting($title)
    {
        // obt� l'usuari actual
        $currentUser = $this->getUser(); // usuari que acaba de fer l'acci� i ser� avaluat
                                         
        // prepara les dades de la nova cap�alera de la votaci�
        $values = array(
            'evaluated_user_id' => $currentUser['id'],
            'title' => $title,
            'date_creation' => time(),
            'date_completed' => null,
            'score' => null
        );
        // grava la nova cap�alera de la votaci�
        $voting_id = $this->votingModel->addVoting($values);
        
        // obt� els grups d'usuaris als que pertany l'usuari evaluat
        $groups = $this->groupMemberModel->getGroups($currentUser['id']);
        // obt� tots els usuaris
        $users = $this->userModel->getAll();
        
        // comprova si la nova votaci� s'ha gravat correctament
        if ($voting_id) {
            // recorre tots els usuaris
            foreach ($users as $user) {
                // recorre els grups de l'usuari actual per assignar els usuaris dels seus grups
                foreach ($groups as $group) {
                    // obt� un usuari que evaluar� en la votaci�
                    if ($this->groupMemberModel->isMember($group['id'], $user['id']) && $user['id'] != $currentUser['id']) {
                        // carrega l'usuari que evaluar�
                        $values = array(
                            'voting_id' => $voting_id,
                            'user_id' => $user['id']
                        );
                        
                        // afegeix l'usuari per a avaluar les activitats requerides en la votaci�
                        $evaluation_id = $this->activitiesEvaluationModel->evaluateActivity($values);
                        // salta a la comprovaci� del seg�ent usuari
                        break;
                    }
                }
            }
            
            // creaci� amb �xit
            $this->flash->success(t('New voting has been created.'));
            return true;
        } else {
            // creaci� no efectuada
            $this->flash->failure(t('New voting could not be created.'));
            return false;
        }
    }

    /**
     * Mostra la votaci� sel�leccionada per avaluar les activitats de l'usuari
     *
     * @access public
     */
    public function vote()
    {
        $voting = array();
        // obt� l'usuari actual
        $currentUser = $this->getUser();
        // obt� el Id de votaci� sol�licitat
        $vote_id = $this->request->getIntegerParam('vote_id');
        // obt� la llista de votacions pendents de realitzar per a l'usuari actual
        $votes = $this->votingModel->getPendingVotes($currentUser['id']);
        
        // recorre la llista de votacions
        foreach ($votes as $vote) {
            // si �s la votaci� seleccionada
            if ($vote['voting_id'] == $vote_id) {
                // assigna la votaci�
                $voting = $vote;
                // surt de la iteraci�
                break;
            }
        }
        
        // mostra la vista amb les activitats a avaluar en la votaci�
        $this->response->html($this->helper->layout->app('Voting:view/evaluation', array(
            'voting' => $voting,
            'user' => $currentUser
        )));
    }

    /**
     * Crea una nova votaci� per a la acci� que acaba de relitzar l'usuari.
     *
     * @access public
     */
    public function evaluateActivity()
    {
        // obt� els valors sol�licitats de l'avaluaci� de cada activitat
        $values = $this->request->getValues();
        // obt� l'usuari actual
        $currentUser = $this->getUser();
        
        // afegeix l'usuari i la data de votaci� als valors de la votaci�
        $values += array(
            'user_id' => $currentUser['id'],
            'date' => time()
        );
        
        // guarda els valors de la votaci� per a l'usuari actual
        if ($this->activitiesEvaluationModel->evaluateActivity($values)) {
            // gravaci� amb �xit
            $this->flash->success(t('Voting saved successfully.'));
        } else {
            // gravaci� no efectuada
            $this->flash->failure(t('Unable to save your voting.'));
        }
        
        // tanca la votaci� si �s necessari
        $this->closeVoting($values['voting_id']);
        // torna a la llista de votacions pendents
        $this->viewPendingVotes();
    }

    /**
     * Tanca la votaci� en el cas que sigui necessari.
     * Aplica l'algoritme de c�lcul de la qualitat de l'usuari evaluat
     * segons la ponderaci� de vots de la resta d'usuaris
     * en funci� del seu pes (qualitat)
     *
     * @access private
     * @param integer $voting_id
     */
    private function closeVoting($voting_id)
    {
        $allUsersReady = true;
        // obt� la votaci� amb el Id sol�licitat
        $voting = $this->votingModel->getVotingById($voting_id);
        
        // existeix la votaci�
        if (! empty($voting)) {
            
            // obt� les avaluacions dels usuaris que pertanyen a la votaci�
            $evaluations = $this->activitiesEvaluationModel->getEvaluations($voting_id);
            
            // recorre les avaluacions dels usuaris que pertanyen a la votaci�
            foreach ($evaluations as $evaluation) {
                
                // comprova que l'avaluaci� s'hagi realitzat
                if (! empty($evaluation['date'])) {
                    // calcula la mitjana entre les activitats avaluades per un usuari
                    $userAvgMark = ($evaluation['importance'] + $evaluation['accuracy'] + $evaluation['time'] + $evaluation['initiative'] + $evaluation['collaboration']) / 5;
                    // obt� l'usuari de la votaci� actual
                    $user = $this->userModel->getById($evaluation['user_id']);
                    
                    // afegeix a l'array la mitjana calculada i la qualitat de l'usuari al que pertany
                    $usersEval[] = array(
                        'mark' => $userAvgMark * $user['weight'],
                        'weight' => $user['weight']
                    );
                } else {
                    // marca que no tots els usuaris han finalitzat la votaci�
                    $allUsersReady = false;
                }
            }
            
            /*
             * comprova que tots els usuaris hagin votat
             * o hagin passat m�s de 2 dies de la data de creaci� de la votaci�
             */
            if ($allUsersReady == true || $voting['date_creation'] < strtotime("-2 day", time())) {
                // comprova que com a m�nim hagin participat en la votaci� la meitat dels usuaris
                if ($usersEval >= (count($evaluations) / 2)) {
                    
                    // recorre cada usuari participant
                    foreach ($usersEval as $userEval) {
                        // suma de les puntuacions i els pesos dels usuaris
                        $partMark += $userEval['mark'];
                        $totalQty += $userEval['weight'];
                    }
                    
                    // obt� el increment que s'aplicar� sobre el pes actual de l'usuari, a partir de les votacions de la resta
                    $points = (($partMark / $totalQty) - 5) * 2;
                    // obt� l'usuari avaluat en la votaci�
                    $evaluated_user = $this->userModel->getById($voting['evaluated_user_id']);
                    // actualitza el pes de l'usuari avaluat, incrementant o disminuint els punts obtinguts
                    $this->userWeightModel->updateWeight($evaluated_user['id'], $evaluated_user['weight'] + $points);
                    // informa a la votaci� de la puntuaci� obtinguda
                    $voting['score'] = $points;
                } else {
                    // la votaci� �s nul�la perque no s'arriba al m�nim de participants
                    $voting['score'] = null;
                }
                
                // data de tancament de la votaci�
                $voting['date_completed'] = time();
                // actualitza les dades de la votaci�
                $this->votingModel->addVoting($voting);
            }
        }
    }

    /**
     * Fa el repartiment de pesos per a tots els usuaris que treballen
     * amb l'usuari que te la sessi� actual
     *
     * @access private
     */
    private function weightCalculation()
    {
        $usergrouping = array();
        // obt� l'usuari actual
        $currentUser = $this->getUser();
        // obt� els grups d'usuaris als que pertany l'usuari actual
        $groups = $this->groupMemberModel->getGroups($currentUser['id']);
        // obt� tots els usuaris
        $allUsers = $this->userModel->getAll();
        
        // recorre tots els usuaris
        foreach ($allUsers as $user) {
            // recorre els grups de l'usuari actual per tenir en compte nom�s els usuaris dels seus grups
            foreach ($groups as $group) {
                // obt� un usuari que pertany a algun grup dels de l'usuari actual
                if ($this->groupMemberModel->isMember($group['id'], $user['id'])) {
                    // obt� la qualitat de l'usuari, i en el cas que sigui 'null' o 0, li assigna un 1%
                    $user['weight'] = (empty($user['weight']) ? 1 : ($user['weight'] = 0 ? 1 : $user['weight']));
                    // afegeix a l'array d'agrupaci� d'usuaris a tenir en compte
                    $usergrouping[] = $user;
                    // suma els valors de les qualitats de tots els usuaris
                    $total += $user['weight'];
                    // salta al seg�ent usuari
                    break;
                }
            }
        }
        
        // recorre l'agrupaci� d'usuaris a tenir en compte per controlar que ning� tingui majoria absoluta
        foreach ($usergrouping as $user) {
            // comprova si l'usuari t� majoria absoluta
            if ($user['weight'] > ($total / 2)) {
                // en el cas de majoria absoluta, s'asigna directament un pes del 50%, ja que no es permet que sigui major
                $user['weight'] = ($total / 2);
                // recalculem el valor total (100% dels pesos)
                $total -= $user['weight'] - ($total / 2);
            }
        }
        
        // recorre l'agrupaci� d'usuaris per asignar el pes
        foreach ($usergrouping as $user) {
            // actualitza la qualitat de l'usuari amb el seu pes respecte de la resta (percentatge)
            $this->userWeightModel->updateWeight($user['id'], ($user['weight'] * 100) / $total);
        }
    }
}
