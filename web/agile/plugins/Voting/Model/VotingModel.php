<?php
namespace Kanboard\Plugin\Voting\Model;

use Kanboard\Core\Base;

/**
 * Voting Model
 *
 * @package Kanboard\Plugin\Voting\Model
 * @author Manel P�rez Clot <Open University of Catalonia (UOC)>
 * @version 1.0, 2018-05-13
 *         
 */
class VotingModel extends Base
{

    /**
     * nom de taula SQL
     * 
     * @var string
     */
    const TABLE = 'voting';

    /**
     * Crea o modifica un registre a la base de dades amb la informaci� d'una votaci�.
     * Retorna el Id de la votaci�.
     *
     * @access public
     * @param array $values
     * @return integer
     */
    public function addVoting($values)
    {
        if (empty($values['id'])) {
            //crea
            $voting_id = $this->db->table(self::TABLE)->persist($values);
        } else {
            //modifica
            $voting_id = $this->db->table(self::TABLE)
                ->eq('id', $values['id'])
                ->save($values);
        }
        return $voting_id;
    }

    /**
     * Cerca a la base de dades la cap�alera d'una votaci� a partir del seu Id.
     * Retorna la informaci� de la votaci�.
     *
     * @access public
     * @param integer $voting_id
     * @return array
     */
    public function getVotingById($voting_id)
    {
        $voting = $this->db->table(self::TABLE)
            ->eq('id', $voting_id)
            ->findOne();
        
        return $voting;
    }

    /**
     * Cerca a la base de dades totes les votacions pendents de realitzar de l'usuari que es pasa per par�metre.
     * Retorna la informaci� de les votacions amb les evaluacions pendents.
     *
     * @access public
     * @param integer $user_id
     * @return array
     */
    public function getPendingVotes($user_id)
    {
        $votes = $this->db->table(self::TABLE)
            ->join(ActivitiesEvaluationModel::TABLE, 'voting_id', 'id')
            ->neq('evaluated_user_id', $user_id)
            ->eq('user_id', $user_id)
            ->isNull('date')
            ->asc('date_creation')
            ->findAll();
        
        return $votes;
    }
}