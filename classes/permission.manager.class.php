<?php

/**
 * PermissionManager
 * Handles admin CRUD for permissions, roles, user-roles, and user-permissions.
 * The actual access-checking logic lives in canAccess() inside general.lib.php.
 */
class PermissionManager
{
    /* ================================================================
     *  ROUTE DISPATCH
     * ============================================================== */
    public function run()
    {
        $cmd = getRequest('cmd');
        switch ($cmd) {
            // ---- page views ----
            case 'permission_list':
                $this->showPermissionList();
                break;
            case 'role_list':
                $this->showRoleList();
                break;
            case 'role_create':
                $this->showRoleCreate();
                break;
            case 'role_edit':
                $this->showRoleEdit();
                break;
            case 'user_role':
                $this->showUserRole();
                break;
            case 'user_permission':
                $this->showUserPermission();
                break;
            // ---- ajax api ----
            case 'api_permission_list':
                $this->apiPermissionList();
                break;
            case 'api_permission_by_id':
                $this->apiPermissionById();
                break;
            case 'api_store_permission':
                $this->apiStorePermission();
                break;
            case 'api_delete_permission':
                $this->apiDeletePermission();
                break;
            case 'api_role_list':
                $this->apiRoleList();
                break;
            case 'api_store_role':
                $this->apiStoreRole();
                break;
            case 'api_update_role':
                $this->apiUpdateRole();
                break;
            case 'api_delete_role':
                $this->apiDeleteRole();
                break;
            case 'api_user_role_list':
                $this->apiUserRoleList();
                break;
            case 'api_update_user_role':
                $this->apiUpdateUserRole();
                break;
            case 'api_user_permission_list':
                $this->apiUserPermissionList();
                break;
            case 'api_update_user_permission':
                $this->apiUpdateUserPermission();
                break;
            default:
                $this->showPermissionList();
                break;
        }
    }

    /* ================================================================
     *  PAGE VIEWS
     * ============================================================== */
    public function showPermissionList()
    {
        require_once(PERMISSION_LIST_SKIN);
    }

    public function showRoleList()
    {
        require_once(ROLE_LIST_SKIN);
    }

    public function showRoleCreate()
    {
        $permissions = $this->getAllPermissions();
        $permission_groups = $this->getPermissionGroups($permissions);
        require_once(ROLE_CREATE_SKIN);
    }

    public function showRoleEdit()
    {
        $role_id = (int)getRequest('role_id');
        if (!$role_id) {
            header('Location: ?app=permission.manager&cmd=role_list');
            exit;
        }
        $role = $this->getRoleById($role_id);
        if (!$role) {
            header('Location: ?app=permission.manager&cmd=role_list');
            exit;
        }
        $permissions = $this->getAllPermissions();
        $permission_groups = $this->getPermissionGroups($permissions);
        $rolePermissionIds = $this->getRolePermissionIds($role_id);
        $role_permissions = json_encode($rolePermissionIds);   // JS-ready array string
        require_once(ROLE_EDIT_SKIN);
    }

    public function showUserRole()
    {
        $allroles = $this->getAllRoles();
        require_once(USER_ROLE_SKIN);
    }

    public function showUserPermission()
    {
        $permissions = $this->getAllPermissions();
        require_once(USER_PERMISSION_SKIN);
    }

    /* ================================================================
     *  API — PERMISSION CRUD
     * ============================================================== */
    public function apiPermissionList()
    {
        $search = mysql_real_escape_string(isset($_POST['search']) ? trim($_POST['search']) : '');
        $page_no = (int)(isset($_POST['page_no']) ? $_POST['page_no'] : 1);
        if ($page_no < 1) $page_no = 1;
        $perPage = 10;
        $offset = ($page_no - 1) * $perPage;

        $where = $search
            ? "WHERE name LIKE '%$search%' OR slug LIKE '%$search%' OR group_name LIKE '%$search%'"
            : '';

        $total = (int)mysql_fetch_object(mysql_query("SELECT COUNT(*) as c FROM `permission` $where"))->c;

        $res = mysql_query("SELECT * FROM `permission` $where ORDER BY group_name, name LIMIT $perPage OFFSET $offset");

        $html = $this->buildPermissionTableHtml($res, $offset);
        $pagination = $this->buildPagination($total, $perPage, $page_no);

        $this->jsonOk(array('html' => $html, 'pagination' => $pagination));
    }

    public function apiPermissionById()
    {
        $id = (int)(isset($_POST['id']) ? $_POST['id'] : 0);
        if (!$id) $this->jsonFail('Invalid ID');

        $row = mysql_fetch_object(mysql_query("SELECT * FROM `permission` WHERE id = $id"));
        if (!$row) $this->jsonFail('Not found');

        $this->jsonOk(array(
            'id' => (int)$row->id,
            'name' => $row->name,
            'slug' => $row->slug,
            'group_name' => $row->group_name,
        ));
    }

    public function apiStorePermission()
    {
        $name = mysql_real_escape_string(trim(isset($_POST['name']) ? $_POST['name'] : ''));
        $slug = mysql_real_escape_string(trim(isset($_POST['slug']) ? $_POST['slug'] : ''));
        $group = mysql_real_escape_string(trim(isset($_POST['group']) ? $_POST['group'] : ''));
        $edit_id = (int)(isset($_POST['edit_id']) ? $_POST['edit_id'] : 0);

        if (empty($name) || empty($slug)) $this->jsonFail('Name and Slug are required');

        if ($edit_id) {
            $dup = mysql_fetch_object(mysql_query("SELECT id FROM `permission` WHERE slug='$slug' AND id != $edit_id"));
            if ($dup) $this->jsonFail('Slug already exists');
            mysql_query("UPDATE `permission` SET name='$name', slug='$slug', group_name='$group', updated_at=NOW() WHERE id=$edit_id");
            $msg = 'Permission updated successfully!';
        } else {
            $dup = mysql_fetch_object(mysql_query("SELECT id FROM `permission` WHERE slug='$slug'"));
            if ($dup) $this->jsonFail('Slug already exists');
            mysql_query("INSERT INTO `permission` (name, slug, group_name, created_at, updated_at) VALUES ('$name','$slug','$group',NOW(),NOW())");
            $msg = 'Permission created successfully!';
        }

        removeFromSession('_acl_perms');
        $this->jsonOk(null, $msg);
    }

    public function apiDeletePermission()
    {
        $id = (int)(isset($_POST['deleted_id']) ? $_POST['deleted_id'] : 0);
        if (!$id) $this->jsonFail('Invalid ID');

        mysql_query("DELETE FROM `permission`         WHERE id = $id");
        mysql_query("DELETE FROM `role_permission`    WHERE permission_id = $id");
        mysql_query("DELETE FROM `user_permission`    WHERE permission_id = $id");
        removeFromSession('_acl_perms');

        $this->jsonOk(null, 'Deleted successfully');
    }

    /* ================================================================
     *  API — ROLE CRUD
     * ============================================================== */
    public function apiRoleList()
    {
        $search = mysql_real_escape_string(isset($_POST['search']) ? trim($_POST['search']) : '');
        $page_no = (int)(isset($_POST['page_no']) ? $_POST['page_no'] : 1);
        if ($page_no < 1) $page_no = 1;
        $perPage = 10;
        $offset = ($page_no - 1) * $perPage;

        $where = $search ? "WHERE r.name LIKE '%$search%'" : '';

        $total = (int)mysql_fetch_object(mysql_query("SELECT COUNT(*) as c FROM `role` r $where"))->c;

        $sql = "SELECT r.id, r.name,
                       GROUP_CONCAT(p.name ORDER BY p.name SEPARATOR ', ') AS perms
                FROM `role` r
                LEFT JOIN `role_permission` rp ON rp.role_id = r.id
                LEFT JOIN `permission` p ON p.id = rp.permission_id
                $where
                GROUP BY r.id, r.name
                ORDER BY r.name
                LIMIT $perPage OFFSET $offset";
        $res = mysql_query($sql);

        $editIcon = '<svg xmlns="http://www.w3.org/2000/svg" width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/></svg>';
        $deleteIcon = '<svg xmlns="http://www.w3.org/2000/svg" width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="3 6 5 6 21 6"/><path d="M19 6l-1 14a2 2 0 0 1-2 2H8a2 2 0 0 1-2-2L5 6"/><path d="M10 11v6"/><path d="M14 11v6"/><path d="M9 6V4h6v2"/></svg>';

        $html = '<table class="table table-zebra w-full">';
        $html .= '<thead class="bg-base-200 text-base"><tr><th>SL</th><th>Name</th><th>Permissions</th><th class="text-center">Options</th></tr></thead><tbody>';
        $sl = $offset + 1;
        $count = 0;
        $styBadge = 'display:inline-flex;align-items:center;background-color:#223763;color:#fff;padding:0 .5rem;border-radius:9999px;font-size:.75rem;line-height:1.5rem;margin-right:.25rem;margin-bottom:.25rem;';
        while ($row = mysql_fetch_object($res)) {
            $count++;
            if ($row->perms) {
                $permsText = '';
                foreach (explode(', ', $row->perms) as $p) {
                    $permsText .= '<span style="' . $styBadge . '">' . htmlspecialchars(trim($p)) . '</span>';
                }
            } else {
                $permsText = '<span class="text-sm opacity-50">Not assign</span>';
            }
            $html .= '<tr>';
            $html .= '<td>' . $sl++ . '</td>';
            $html .= '<td>' . htmlspecialchars($row->name) . '</td>';
            $html .= '<td>' . $permsText . '</td>';
            $html .= '<td class="text-center whitespace-nowrap">';
            $html .= '<a href="?app=permission.manager&cmd=role_edit&role_id=' . (int)$row->id . '" class="btn btn-xs btn-ghost border mr-1" title="Edit">' . $editIcon . '</a>';
            $html .= '<button class="btn btn-xs btn-ghost border text-error" onclick="deleteRole(' . (int)$row->id . ')" title="Delete">' . $deleteIcon . '</button>';
            $html .= '</td></tr>';
        }
        if ($count === 0) {
            $html .= '<tr><td colspan="4" class="text-center">No record found</td></tr>';
        }
        $html .= '</tbody></table>';

        $pagination = $this->buildPagination($total, $perPage, $page_no);
        $this->jsonOk(array('html' => $html, 'pagination' => $pagination));
    }

    public function apiStoreRole()
    {
        $name = mysql_real_escape_string(trim(isset($_POST['name']) ? $_POST['name'] : ''));
        $permissionIds = isset($_POST['permissions']) ? $_POST['permissions'] : array();

        if (empty($name)) $this->jsonFail('Role name is required');
        if (empty($permissionIds)) $this->jsonFail('At least one permission is required');

        $dup = mysql_fetch_object(mysql_query("SELECT id FROM `role` WHERE name='$name'"));
        if ($dup) $this->jsonFail('Role name already exists');

        mysql_query("INSERT INTO `role` (name, created_at, updated_at) VALUES ('$name', NOW(), NOW())");
        $role_id = (int)mysql_insert_id();

        foreach ($permissionIds as $pid) {
            $pid = (int)$pid;
            if ($pid > 0) {
                mysql_query("INSERT INTO `role_permission` (role_id, permission_id, created_at, updated_at) VALUES ($role_id, $pid, NOW(), NOW())");
            }
        }
        removeFromSession('_acl_perms');
        $this->jsonOk(null, 'Role created successfully!');
    }

    public function apiUpdateRole()
    {
        $role_id = (int)(isset($_POST['role_id']) ? $_POST['role_id'] : 0);
        $name = mysql_real_escape_string(trim(isset($_POST['name']) ? $_POST['name'] : ''));
        $permissionIds = isset($_POST['permissions']) ? $_POST['permissions'] : array();

        if (!$role_id) $this->jsonFail('Invalid role');
        if (empty($name)) $this->jsonFail('Role name is required');
        if (empty($permissionIds)) $this->jsonFail('At least one permission is required');

        $dup = mysql_fetch_object(mysql_query("SELECT id FROM `role` WHERE name='$name' AND id != $role_id"));
        if ($dup) $this->jsonFail('Role name already exists');

        mysql_query("UPDATE `role` SET name='$name', updated_at=NOW() WHERE id=$role_id");
        mysql_query("DELETE FROM `role_permission` WHERE role_id=$role_id");

        foreach ($permissionIds as $pid) {
            $pid = (int)$pid;
            if ($pid > 0) {
                mysql_query("INSERT INTO `role_permission` (role_id, permission_id, created_at, updated_at) VALUES ($role_id, $pid, NOW(), NOW())");
            }
        }
        removeFromSession('_acl_perms');
        $this->jsonOk(null, 'Role updated successfully!');
    }

    public function apiDeleteRole()
    {
        $id = (int)(isset($_POST['deleted_id']) ? $_POST['deleted_id'] : 0);
        if (!$id) $this->jsonFail('Invalid ID');

        mysql_query("DELETE FROM `role`             WHERE id=$id");
        mysql_query("DELETE FROM `role_permission`  WHERE role_id=$id");
        mysql_query("DELETE FROM `user_role`        WHERE role_id=$id");
        removeFromSession('_acl_perms');
        $this->jsonOk(null, 'Role deleted successfully!');
    }

    /* ================================================================
     *  API — USER ROLE
     * ============================================================== */
    public function apiUserRoleList()
    {
        $search = mysql_real_escape_string(isset($_POST['search']) ? trim($_POST['search']) : '');
        $page_no = (int)(isset($_POST['page_no']) ? $_POST['page_no'] : 1);
        if ($page_no < 1) $page_no = 1;
        $perPage = 10;
        $offset = ($page_no - 1) * $perPage;

        $where = $search ? "WHERE u.userid LIKE '%$search%'" : '';

        $total = (int)mysql_fetch_object(mysql_query("SELECT COUNT(*) as c FROM " . USER_TBL . " u $where"))->c;
        $usersRes = mysql_query("SELECT u.userid FROM " . USER_TBL . " u $where ORDER BY u.userid LIMIT $perPage OFFSET $offset");
        $users = array();

        while ($uRow = mysql_fetch_object($usersRes)) {
            $uid = mysql_real_escape_string($uRow->userid);
            $rolesRes = mysql_query("SELECT ur.role_id, r.name FROM `user_role` ur JOIN `role` r ON r.id=ur.role_id WHERE ur.userid='$uid'");
            $roles = array();
            $roleNames = array();
            while ($rRow = mysql_fetch_object($rolesRes)) {
                $roles[] = array('role_id' => (int)$rRow->role_id);
                $roleNames[] = $rRow->name;
            }
            $users[] = array(
                'userid' => $uRow->userid,
                'roles' => $roles,
                'roleNames' => $roleNames,
            );
        }

        $html = '<table class="table table-zebra w-full">';
        $html .= '<thead class="bg-base-200 text-base"><tr><th>SL</th><th>User</th><th>Role</th><th class="text-center">Option</th></tr></thead><tbody>';
        $sl = $offset + 1;
        if (empty($users)) {
            $html .= '<tr><td colspan="4" class="text-center">No record found</td></tr>';
        } else {
            $styBadge = 'display:inline-flex;align-items:center;background-color:#223763;color:#fff;padding:0 .5rem;border-radius:9999px;font-size:.75rem;line-height:1.5rem;margin-right:.25rem;margin-bottom:.25rem;';
            $editIcon = '<svg xmlns="http://www.w3.org/2000/svg" width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/></svg>';
            foreach ($users as $u) {
                if (!empty($u['roleNames'])) {
                    $display = '';
                    foreach ($u['roleNames'] as $rn) {
                        $display .= '<span style="' . $styBadge . '">' . htmlspecialchars($rn) . '</span>';
                    }
                } else {
                    $display = '<span class="text-sm opacity-50">Not assign</span>';
                }
                $label = htmlspecialchars($u['userid']);
                $html .= '<tr>';
                $html .= '<td>' . $sl++ . '</td>';
                $html .= '<td>' . $label . '</td>';
                $html .= '<td>' . $display . '</td>';
                $html .= '<td class="text-center"><button class="btn btn-xs btn-ghost border" onclick="handleEdit(\'' . htmlspecialchars($u['userid']) . '\')" title="Edit">' . $editIcon . '</button></td>';
                $html .= '</tr>';
            }
        }
        $html .= '</tbody></table>';

        $pagination = $this->buildPagination($total, $perPage, $page_no);
        $this->jsonOk(array('html' => $html, 'pagination' => $pagination, 'users' => $users));
    }

    public function apiUpdateUserRole()
    {
        $userid = mysql_real_escape_string(isset($_POST['userid']) ? trim($_POST['userid']) : '');
        $roleIds = isset($_POST['roles']) ? $_POST['roles'] : array();

        if (empty($userid)) $this->jsonFail('User is required');

        mysql_query("DELETE FROM `user_role` WHERE userid='$userid'");
        foreach ($roleIds as $rid) {
            $rid = (int)$rid;
            if ($rid > 0) {
                mysql_query("INSERT INTO `user_role` (userid, role_id, created_at, updated_at) VALUES ('$userid', $rid, NOW(), NOW())");
            }
        }
        removeFromSession('_acl_perms');
        $this->jsonOk(null, 'User role updated successfully!');
    }

    /* ================================================================
     *  API — USER PERMISSION
     * ============================================================== */
    public function apiUserPermissionList()
    {
        $search = mysql_real_escape_string(isset($_POST['search']) ? trim($_POST['search']) : '');
        $page_no = (int)(isset($_POST['page_no']) ? $_POST['page_no'] : 1);
        if ($page_no < 1) $page_no = 1;
        $perPage = 10;
        $offset = ($page_no - 1) * $perPage;

        $where = $search ? "WHERE u.userid LIKE '%$search%'" : '';

        $total = (int)mysql_fetch_object(mysql_query("SELECT COUNT(*) as c FROM " . USER_TBL . " u $where"))->c;

        $usersRes = mysql_query("SELECT u.userid FROM " . USER_TBL . " u $where ORDER BY u.userid LIMIT $perPage OFFSET $offset");

        $users = array();
        while ($uRow = mysql_fetch_object($usersRes)) {
            $uid = mysql_real_escape_string($uRow->userid);
            $permsRes = mysql_query("SELECT up.permission_id, p.name FROM `user_permission` up JOIN `permission` p ON p.id=up.permission_id WHERE up.userid='$uid'");
            $perms = array();
            $permNames = array();
            while ($pRow = mysql_fetch_object($permsRes)) {
                $perms[] = array('permission_id' => (int)$pRow->permission_id);
                $permNames[] = $pRow->name;
            }
            $users[] = array(
                'userid' => $uRow->userid,
                'permissions' => $perms,
                'permNames' => $permNames,
            );
        }

        $html = '<table class="table table-zebra w-full">';
        $html .= '<thead class="bg-base-200 text-base"><tr><th>SL</th><th>User</th><th>Permission</th><th class="text-center">Option</th></tr></thead><tbody>';
        $sl = $offset + 1;
        if (empty($users)) {
            $html .= '<tr><td colspan="4" class="text-center">No record found</td></tr>';
        } else {
            $styBadge = 'display:inline-flex;align-items:center;background-color:#223763;color:#fff;padding:0 .5rem;border-radius:9999px;font-size:.75rem;line-height:1.5rem;margin-right:.25rem;margin-bottom:.25rem;';
            $editIcon = '<svg xmlns="http://www.w3.org/2000/svg" width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/></svg>';
            foreach ($users as $u) {
                if (!empty($u['permNames'])) {
                    $display = '';
                    foreach ($u['permNames'] as $pn) {
                        $display .= '<span style="' . $styBadge . '">' . htmlspecialchars($pn) . '</span>';
                    }
                } else {
                    $display = '<span class="text-sm opacity-50">Not assign</span>';
                }
                $label = htmlspecialchars($u['userid']);
                $html .= '<tr>';
                $html .= '<td>' . $sl++ . '</td>';
                $html .= '<td>' . $label . '</td>';
                $html .= '<td>' . $display . '</td>';
                $html .= '<td class="text-center"><button class="btn btn-xs btn-ghost border" onclick="handleEdit(\'' . htmlspecialchars($u['userid']) . '\')" title="Edit">' . $editIcon . '</button></td>';
                $html .= '</tr>';
            }
        }
        $html .= '</tbody></table>';

        $pagination = $this->buildPagination($total, $perPage, $page_no);
        $this->jsonOk(array('html' => $html, 'pagination' => $pagination, 'users' => $users));
    }

    public function apiUpdateUserPermission()
    {
        $userid = mysql_real_escape_string(isset($_POST['userid']) ? trim($_POST['userid']) : '');
        $permIds = isset($_POST['permissions']) ? $_POST['permissions'] : array();

        if (empty($userid)) $this->jsonFail('User is required');

        mysql_query("DELETE FROM `user_permission` WHERE userid='$userid'");
        foreach ($permIds as $pid) {
            $pid = (int)$pid;
            if ($pid > 0) {
                mysql_query("INSERT INTO `user_permission` (userid, permission_id, created_at, updated_at) VALUES ('$userid', $pid, NOW(), NOW())");
            }
        }
        removeFromSession('_acl_perms');
        $this->jsonOk(null, 'User permission updated successfully!');
    }

    /* ================================================================
     *  PRIVATE HELPERS
     * ============================================================== */
    private function getAllPermissions()
    {
        $res = mysql_query("SELECT * FROM `permission` ORDER BY group_name, name");
        $rows = array();
        while ($row = mysql_fetch_assoc($res)) {
            $rows[] = $row;
        }
        return $rows;
    }

    private function getPermissionGroups($permissions)
    {
        $counts = array();
        foreach ($permissions as $p) {
            $g = $p['group_name'];
            if (!isset($counts[$g])) $counts[$g] = 0;
            $counts[$g]++;
        }
        $groups = array();
        foreach ($counts as $name => $total) {
            $groups[] = array('group_name' => $name, 'groupTotal' => $total);
        }
        return $groups;
    }

    private function getAllRoles()
    {
        $res = mysql_query("SELECT * FROM `role` ORDER BY name");
        $rows = array();
        while ($row = mysql_fetch_assoc($res)) {
            $rows[] = $row;
        }
        return $rows;
    }

    private function getRoleById($role_id)
    {
        return mysql_fetch_assoc(mysql_query("SELECT * FROM `role` WHERE id=" . (int)$role_id));
    }

    private function getRolePermissionIds($role_id)
    {
        $res = mysql_query("SELECT permission_id FROM `role_permission` WHERE role_id=" . (int)$role_id);
        $ids = array();
        while ($row = mysql_fetch_object($res)) {
            $ids[] = (int)$row->permission_id;
        }
        return $ids;
    }

    private function buildPermissionTableHtml($res, $offset)
    {
        $editIcon = '<svg xmlns="http://www.w3.org/2000/svg" width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/></svg>';
        $deleteIcon = '<svg xmlns="http://www.w3.org/2000/svg" width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="3 6 5 6 21 6"/><path d="M19 6l-1 14a2 2 0 0 1-2 2H8a2 2 0 0 1-2-2L5 6"/><path d="M10 11v6"/><path d="M14 11v6"/><path d="M9 6V4h6v2"/></svg>';

        $html = '<table class="table table-zebra w-full">';
        $html .= '<thead class="bg-base-200 text-base"><tr><th>SL</th><th>Name</th><th>Slug</th><th>Group</th><th class="text-center">Options</th></tr></thead><tbody>';
        $sl = $offset + 1;
        $count = 0;
        while ($row = mysql_fetch_object($res)) {
            $count++;
            $html .= '<tr>';
            $html .= '<td>' . $sl++ . '</td>';
            $html .= '<td>' . htmlspecialchars($row->name) . '</td>';
            $html .= '<td><span class="badge badge-ghost badge-sm">' . htmlspecialchars($row->slug) . '</span></td>';
            $html .= '<td>' . htmlspecialchars($row->group_name) . '</td>';
            $html .= '<td class="text-center whitespace-nowrap">';
            $html .= '<button class="btn btn-xs btn-ghost border mr-1" onclick="editPermission(' . (int)$row->id . ')" title="Edit">' . $editIcon . '</button>';
            $html .= '<button class="btn btn-xs btn-ghost border text-error" onclick="deletePermission(' . (int)$row->id . ')" title="Delete">' . $deleteIcon . '</button>';
            $html .= '</td></tr>';
        }
        if ($count === 0) {
            $html .= '<tr><td colspan="5" class="text-center">No record found</td></tr>';
        }
        $html .= '</tbody></table>';
        return $html;
    }

    private function buildPagination($total, $perPage, $currentPage)
    {
        $totalPages = (int)ceil($total / $perPage);
        if ($totalPages <= 1) return '';
        $html = '';
        for ($i = 1; $i <= $totalPages; $i++) {
            $active = ($i == $currentPage) ? 'btn-active' : '';
            $html .= '<button class="join-item btn btn-sm ' . $active . '" onclick="goPage(' . $i . ')">' . $i . '</button>';
        }
        return $html;
    }

    private function jsonOk($data = null, $message = 'Success')
    {
        while (ob_get_level() > 0) {
            ob_end_clean();
        }
        header('Content-Type: application/json; charset=utf-8');
        $resp = array('status' => true, 'message' => $message);
        if ($data !== null) $resp['data'] = $data;
        echo json_encode($resp);
        exit;
    }

    private function jsonFail($message = 'Error')
    {
        while (ob_get_level() > 0) {
            ob_end_clean();
        }
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode(array('status' => false, 'message' => $message));
        exit;
    }

} // end class PermissionManager
?>
