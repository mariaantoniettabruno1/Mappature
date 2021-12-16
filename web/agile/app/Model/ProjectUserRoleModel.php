<?php

namespace Kanboard\Model;

use Kanboard\Core\Base;
use Kanboard\Core\Security\Role;

/**
 * Project User Role
 *
 * @package  Kanboard\Model
 * @author   Frederic Guillot
 */
class ProjectUserRoleModel extends Base
{
    /**
     * SQL table name
     *
     * @var string
     */
    const TABLE = 'project_has_users';

    /**
     * Get the list of active project for the given user
     *
     * @access public
     * @param integer $user_id
     * @return array
     */
    public function getActiveProjectsByUser($user_id)
    {
        $value =  $this->getProjectsByUser($user_id, array(ProjectModel::ACTIVE));
        return $value;
    }

    /**
     * Get the list of project visible for the given user
     *
     * @access public
     * @param integer $user_id
     * @param array $status
     * @return array
     */
    public function getProjectsByUser($user_id, $status = array(ProjectModel::ACTIVE, ProjectModel::INACTIVE))
    {
        $tempOwner = array();
        $tempCreator = array();
        $userProjects = $this->db
            ->hashtable(ProjectModel::TABLE)
            ->eq(self::TABLE . '.user_id', $user_id)
            ->in(ProjectModel::TABLE . '.is_active', $status)
            ->join(self::TABLE, 'project_id', 'id')
            ->getAll(ProjectModel::TABLE . '.id', ProjectModel::TABLE . '.name');
        //query per estrapolare i progetti di riferimento per le task assegnate ad un utente nel caso in cui sia owner e/o creator
        $userTaskOwner = $this->db
            ->table('MAPP_task_users_owner')
            ->eq('MAPP_task_users_owner.user_id', $user_id)
            ->join('tasks', 'id', 'task_id')
            ->findAll();

        $userProjectTaskOwner = $this->db
            ->table(ProjectModel::TABLE)
            ->eq(ProjectModel::TABLE . '.id', $userTaskOwner[0]['project_id'])
            ->findAll();

        $userTaskCreator = $this->db
            ->table('MAPP_task_users_creator')
            ->eq('MAPP_task_users_creator.user_id', $user_id)
            ->join('tasks', 'id', 'task_id')
            ->findAll();

        $userProjectTaskCreator = $this->db
            ->table(ProjectModel::TABLE)
            ->eq(ProjectModel::TABLE . '.id', $userTaskCreator[0]['project_id'])
            ->findAll();

        //salvataggio dei dati in un array
        for ($i = 0; $i < sizeof($userProjectTaskOwner); $i++) {
            array_push($tempOwner,$userProjectTaskOwner[$i]['name']);
            $tempOwner[$userProjectTaskOwner[$i]['id']] = $tempOwner[$i];
            unset($tempOwner[$i]);
        }

        for ($i = 0; $i < sizeof($userProjectTaskCreator); $i++) {
            array_push($tempCreator,$userProjectTaskCreator[$i]['name']);
            $tempOwner[$userProjectTaskCreator[$i]['id']] = $tempCreator[$i];
            unset($tempCreator[$i]);
        }
        $groupProjects = $this->projectGroupRoleModel->getProjectsByUser($user_id, $status);
        $projects = $userProjects + $groupProjects + $tempOwner + $tempCreator;

        asort($projects);

        return $projects;
    }

    /**
     * For a given project get the role of the specified user
     *
     * @access public
     * @param integer $project_id
     * @param integer $user_id
     * @return string
     */
    public function getUserRole($project_id, $user_id)
    {
        $role = $this->db->table(self::TABLE)->eq('user_id', $user_id)->eq('project_id', $project_id)->findOneColumn('role');

        if (empty($role)) {
            $role = $this->projectGroupRoleModel->getUserRole($project_id, $user_id);
        }

        return $role;
    }

    /**
     * Get all users associated directly to the project
     *
     * @access public
     * @param integer $project_id
     * @return array
     */
    public function getUsers($project_id)
    {
        return $this->db->table(self::TABLE)
            ->columns(
                UserModel::TABLE . '.id',
                UserModel::TABLE . '.username',
                UserModel::TABLE . '.name',
                UserModel::TABLE . '.email',
                self::TABLE . '.role'
            )
            ->join(UserModel::TABLE, 'id', 'user_id')
            ->eq('project_id', $project_id)
            ->asc(UserModel::TABLE . '.username')
            ->asc(UserModel::TABLE . '.name')
            ->findAll();
    }

    /**
     * Get all users (fetch users from groups)
     *
     * @access public
     * @param integer $project_id
     * @return array
     */
    public function getAllUsers($project_id)
    {
        $userMembers = $this->getUsers($project_id);
        $groupMembers = $this->projectGroupRoleModel->getUsers($project_id);
        $members = array_merge($userMembers, $groupMembers);

        return $this->userModel->prepareList($members);
    }

    /**
     * Get users grouped by role
     *
     * @access public
     * @param integer $project_id Project id
     * @return array
     */
    public function getAllUsersGroupedByRole($project_id)
    {
        $users = array();

        $userMembers = $this->getUsers($project_id);
        $groupMembers = $this->projectGroupRoleModel->getUsers($project_id);
        $members = array_merge($userMembers, $groupMembers);

        foreach ($members as $user) {
            if (!isset($users[$user['role']])) {
                $users[$user['role']] = array();
            }

            $users[$user['role']][$user['id']] = $user['name'] ?: $user['username'];
        }

        return $users;
    }

    /**
     * Get list of users that can be assigned to a task (only Manager and Member)
     *
     * @access public
     * @param integer $project_id
     * @return array
     */
    public function getAssignableUsers($project_id)
    {
        $userMembers = $this->db->table(self::TABLE)
            ->columns(UserModel::TABLE . '.id', UserModel::TABLE . '.username', UserModel::TABLE . '.name')
            ->join(UserModel::TABLE, 'id', 'user_id')
            ->eq(UserModel::TABLE . '.is_active', 1)
            ->eq(self::TABLE . '.project_id', $project_id)
            ->neq(self::TABLE . '.role', Role::PROJECT_VIEWER)
            ->findAll();

        $groupMembers = $this->projectGroupRoleModel->getAssignableUsers($project_id);
        $members = array_merge($userMembers, $groupMembers);

        return $this->userModel->prepareList($members);
    }

    /**
     * Get list of users that can be assigned to a task (only Manager and Member)
     *
     * @access public
     * @param integer $project_id Project id
     * @param bool $unassigned Prepend the 'Unassigned' value
     * @param bool $everybody Prepend the 'Everbody' value
     * @param bool $singleUser If there is only one user return only this user
     * @return array
     */
    public function getAssignableUsersList($project_id, $unassigned = true, $everybody = false, $singleUser = false)
    {
        $users = $this->getAssignableUsers($project_id);

        if ($singleUser && count($users) === 1) {
            return $users;
        }

        if ($unassigned) {
            $users = array(t('Unassigned')) + $users;
        }

        if ($everybody) {
            $users = array(UserModel::EVERYBODY_ID => t('Everybody')) + $users;
        }

        return $users;
    }

    /**
     * Add a user to the project
     *
     * @access public
     * @param integer $project_id
     * @param integer $user_id
     * @param string $role
     * @return boolean
     */
    public function addUser($project_id, $user_id, $role)
    {
        return $this->db->table(self::TABLE)->insert(array(
            'user_id' => $user_id,
            'project_id' => $project_id,
            'role' => $role,
        ));
    }

    /**
     * Remove a user from the project
     *
     * @access public
     * @param integer $project_id
     * @param integer $user_id
     * @return boolean
     */
    public function removeUser($project_id, $user_id)
    {
        return $this->db->table(self::TABLE)->eq('user_id', $user_id)->eq('project_id', $project_id)->remove();
    }

    /**
     * Change a user role for the project
     *
     * @access public
     * @param integer $project_id
     * @param integer $user_id
     * @param string $role
     * @return boolean
     */
    public function changeUserRole($project_id, $user_id, $role)
    {
        return $this->db->table(self::TABLE)
            ->eq('user_id', $user_id)
            ->eq('project_id', $project_id)
            ->update(array(
                'role' => $role,
            ));
    }

    /**
     * Copy user access from a project to another one
     *
     * @param integer $project_src_id
     * @param integer $project_dst_id
     * @return boolean
     */
    public function duplicate($project_src_id, $project_dst_id)
    {
        $rows = $this->db->table(self::TABLE)->eq('project_id', $project_src_id)->findAll();

        foreach ($rows as $row) {
            $result = $this->db->table(self::TABLE)->save(array(
                'project_id' => $project_dst_id,
                'user_id' => $row['user_id'],
                'role' => $row['role'],
            ));

            if (!$result) {
                return false;
            }
        }

        return true;
    }
}
