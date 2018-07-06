<?php
namespace DataPersistence {
    const NOT_FOUND = -1;
    const SQS_ERROR = -9;

    function query() {
        global $DB;
        try {
            return call_user_func_array( $DB->query, func_get_args() );
        }
        catch( ex ) {
            throw new Exception( ex->getMessage(), SQS_ERROR );
        }
    }

    function fetch() {
        $stm = call_user_func_array( query, func_get_args() );
        $row = $stm->fetch();
        if( !$row )
            throw new Exception('[DataPersistence\Group\get] There is no "Group" with id=' . $id . '!', NOT_FOUND);
        return $row;
    }

    function exec() {
        global $DB;
        call_user_func_array( query, func_get_args() );
        return $DB->lastId;
    }
}

namespace DataPersistence\Group {
    function get( $id ) {
        $stm = \DataPersistence\fetch('SELECT * FROM' . $DB->table('Group') . 'WHERE id=?', $id );
        return ['id' => intVal($row['id']), 'name' => $row['name']];
    }
    function add( $name ) {
        return \DataPersistence\exec('INSERT INTO' . $DB->table('Group') . '(`name`)VALUES(?)', $name);
    }
    function del( $id ) {
        \DataPersistence\delete([
            'Student'
        ]);
    }
}

?>
