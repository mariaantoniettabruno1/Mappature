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
class ActivitiesEvaluationModel extends Base
{

    /**
     * nom de taula SQL
     *
     * @var string
     */
    const TABLE = 'activities_evaluation';

    /**
     * Crea o modifica a la base de dades el registre d'avaluai� d'activitats per a un usuari i una votaci�.
     * Retorna el Id de l'avaluai� d'activitats que pertany a una votaci�.
     *
     * @access public
     * @param array $values
     * @return integer
     */
    public function evaluateActivity($values)
    {
        $evaluation = $this->db->table(self::TABLE)
            ->eq('voting_id', $values['voting_id'])
            ->eq('user_id', $values['user_id'])
            ->isNull('date')
            ->findOne();
        
        if (empty($evaluation)) {
            // crea
            $evaluation_id = $this->db->table(self::TABLE)->persist($values);
        } else {
            // modifica
            $evaluation_id = $this->db->table(self::TABLE)
                ->eq('id', $evaluation['id'])
                ->save($values);
        }
        
        return $evaluation_id;
    }

    /**
     * Cerca a la base de dades totes les avaluacions de tots els usuaris que pertanyen a la votaci� indicada per par�metre.
     * Retorna la informaci� de les avaluaions d'activitats que pertanyen a la votaci�.
     *
     * @access public
     * @param integer $voting_id
     * @return array
     */
    public function getEvaluations($voting_id)
    {
        $evaluations = $this->db->table(self::TABLE)
            ->eq('voting_id', $voting_id)
            ->findAll();
        
        return $evaluations;
    }
}