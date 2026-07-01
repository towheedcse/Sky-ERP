<?php

if (!function_exists('userCondition')) {
    function userCondition($onlyImran = false, $extraUsers = [])
    {
        $userId = getFromSession('userid');
        $userArray = $onlyImran ? ["imran"] : ["imran", "superadmin"];
        if (!empty($extraUsers)) {
            $userArray = array_merge($userArray, $extraUsers);
        }
        return in_array($userId, $userArray, true);
    }
}

if (!function_exists('userNotAllowed')) {
    function userNotAllowed($users = [])
    {
        $userId = getFromSession('userid');
        if (empty($users)) {
            $users = ["superadmin"];
        }
        return !in_array($userId, $users, true);
    }
}

if (!function_exists('hasApprovedPermission')) {
    /**
     * True if the user is allowed to approve, driven by the
     * `approved_permission` column on the `user` table.
     * imran/superadmin remain allowed as an admin fallback.
     */
    function hasApprovedPermission($userId = null)
    {
        if ($userId === null) {
            $userId = getFromSession('userid');
        }
        if (userCondition(false)) {
            return true;
        }
        if (empty($userId)) {
            return false;
        }
        $safeUser = mysql_real_escape_string($userId);
        $row = mysql_fetch_object(mysql_query(
            "SELECT approved_permission FROM " . USER_TBL . " WHERE userid = '$safeUser' LIMIT 1"
        ));
        return $row && (int) $row->approved_permission === 1;
    }
}


if (!function_exists('dd')) {
    /**
     * Dump and Die helper (smarter version)
     * Handles arrays, objects, scalars, booleans, null
     */
    function dd($var, $label = null)
    {
        echo '<div style="background:#1e1e1e;color:#dcdcdc;padding:15px;border-radius:5px;font-family:monospace;word-wrap: break-word;">';

        if ($label) {
            echo "<strong style='color:#9cdcfe;'>{$label}:</strong><br>";
        }

        if (is_array($var) || is_object($var)) {
            echo '<pre style="background:#252526;color:#dcdcdc;padding:10px;border-radius:5px;overflow:auto;">';
            print_r($var);
            echo '</pre>';
        } elseif (is_bool($var)) {
            echo '<span style="color:#569cd6;">' . ($var ? 'true' : 'false') . '</span>';
        } elseif (is_null($var)) {
            echo '<span style="color:#569cd6;">NULL</span>';
        } else {
            echo '<span style="color:#ce9178;">' . htmlspecialchars($var) . '</span>';
        }

        echo '</div>';
        die();
    }
}

if (!function_exists('dump')) {
    /**
     * Just dump without stopping execution
     */
    function dump($var, $label = null)
    {
        echo '<div style="background:#1e1e1e;color:#dcdcdc;padding:15px;border-radius:5px;font-family:monospace;">';

        if ($label) {
            echo "<strong style='color:#9cdcfe;'>{$label}:</strong><br>";
        }

        if (is_array($var) || is_object($var)) {
            echo '<pre style="background:#252526;color:#dcdcdc;padding:10px;border-radius:5px;overflow:auto;">';
            print_r($var);
            echo '</pre>';
        } elseif (is_bool($var)) {
            echo '<span style="color:#569cd6;">' . ($var ? 'true' : 'false') . '</span>';
        } elseif (is_null($var)) {
            echo '<span style="color:#569cd6;">NULL</span>';
        } else {
            echo '<span style="color:#ce9178;">' . htmlspecialchars($var) . '</span>';
        }

        echo '</div>';
    }
}
