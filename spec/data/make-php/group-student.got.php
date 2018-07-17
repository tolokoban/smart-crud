<?php
namespace DataPersistence {
    const NOT_FOUND = -1;
    const SQS_ERROR = -9;

    function query() {
        global $DB;
        try {
            return call_user_func_array( $DB->query, func_get_args() );
        }
        catch( Exception $ex ) {
            throw new Exception( $ex->getMessage(), SQS_ERROR );
        }
    }
    function fetch() {
        $stm = call_user_func_array( query, func_get_args() );
        $row = $stm->fetch();
        if( !$row ) throw new Exception('[DataPersistence] There is no data!', NOT_FOUND);
        return $row;
    }
    function exec() {
        global $DB;
        call_user_func_array( query, func_get_args() );
        return $DB->lastId;
    }
}
namespace DataPersistence\Group {
    function all() {
        global $DB;
        $stm = \DataPersistence\query('SELECT id FROM' . $DB->table('Group'));
        $ids = [];
        while( null != ($row = $stm->fetch()) ) {
            $ids[] = intVal($row[0]);
        }
        return $ids;
    }
    function get( $id ) {
        global $DB;
        $row = \DataPersistence\fetch('SELECT * FROM' . $DB->table('Group') . 'WHERE id=?', $id );
        return ['id' => intVal($row['id']),
                'name' => $row['name']];
    }
    function add( $fields ) {
        global $DB;
        return \DataPersistence\exec(
            'INSERT INTO' . $DB->table('Group') . '(`name`)'
          . 'VALUES(?)',
            $fields['name']);
    }
    function upd( $id, $values ) {
        global $DB;
        \DataPersistence\exec(
            'UPDATE' . $DB->table('Group')
          . 'SET `name`=? '
          . 'WHERE id=?',
            $id,
            $values['name']);
    }
    function del( $id ) {
        global $DB;
        \DataPersistence\exec(
            'DELETE FROM' . $DB->table('Group')
          . 'WHERE id=?', $id);
    }
    function getStudents( $id ) {
        global $DB;
        $stm = \DataPersistence\query(
            'SELECT id FROM' . $DB->table('Student')
          . 'WHERE `group`=?', $id);
        $ids = [];
        while( null != ($row = $stm->fetch()) ) {
            $ids[] = intVal($row[0]);
        }
        return $ids;
    }
}
namespace DataPersistence\Student {
    function all() {
        global $DB;
        $stm = \DataPersistence\query('SELECT id FROM' . $DB->table('Student'));
        $ids = [];
        while( null != ($row = $stm->fetch()) ) {
            $ids[] = intVal($row[0]);
        }
        return $ids;
    }
    function get( $id ) {
        global $DB;
        $row = \DataPersistence\fetch('SELECT * FROM' . $DB->table('Student') . 'WHERE id=?', $id );
        return ['id' => intVal($row['id']),
                'name' => $row['name']];
    }
    function add( $fields ) {
        global $DB;
        return \DataPersistence\exec(
            'INSERT INTO' . $DB->table('Student') . '(`name`)'
          . 'VALUES(?)',
            $fields['name']);
    }
    function upd( $id, $values ) {
        global $DB;
        \DataPersistence\exec(
            'UPDATE' . $DB->table('Student')
          . 'SET `name`=? '
          . 'WHERE id=?',
            $id,
            $values['name']);
    }
    function del( $id ) {
        global $DB;
        \DataPersistence\exec(
            'DELETE FROM' . $DB->table('Student')
          . 'WHERE id=?', $id);
    }
    function getGroup( $id ) {
        global $DB;
        $row = \DataPersistence\fetch(
            'SELECT `group` FROM' . $DB->table('Student')
          . 'WHERE id=?', $id);
        return intVal($row[0]);
    }
}
namespace DataPersistence\User {
    function all() {
        global $DB;
        $stm = \DataPersistence\query('SELECT id FROM' . $DB->table('User'));
        $ids = [];
        while( null != ($row = $stm->fetch()) ) {
            $ids[] = intVal($row[0]);
        }
        return $ids;
    }
    function get( $id ) {
        global $DB;
        $row = \DataPersistence\fetch('SELECT * FROM' . $DB->table('User') . 'WHERE id=?', $id );
        return ['id' => intVal($row['id']),
                'login' => $row['login'],
                'password' => $row['password'],
                'name' => $row['name'],
                'roles' => $row['roles'],
                'enabled' => $row['enabled'],
                'creation' => $row['creation'],
                'data' => $row['data']];
    }
    function add( $fields ) {
        global $DB;
        return \DataPersistence\exec(
            'INSERT INTO' . $DB->table('User') . '(`login`,`password`,`name`,`roles`,`enabled`,`creation`,`data`)'
          . 'VALUES(?,?,?,?,?,?,?)',
            $fields['login'],
            $fields['password'],
            $fields['name'],
            $fields['roles'],
            $fields['enabled'],
            $fields['creation'],
            $fields['data']);
    }
    function upd( $id, $values ) {
        global $DB;
        \DataPersistence\exec(
            'UPDATE' . $DB->table('User')
          . 'SET `login`=?,,`password`=?,,`name`=?,,`roles`=?,,`enabled`=?,,`creation`=?,,`data`=? '
          . 'WHERE id=?',
            $id,
            $values['login'],
            $values['password'],
            $values['name'],
            $values['roles'],
            $values['enabled'],
            $values['creation'],
            $values['data']);
    }
    function del( $id ) {
        global $DB;
        \DataPersistence\exec(
            'DELETE FROM' . $DB->table('User')
          . 'WHERE id=?', $id);
    }
}
?>