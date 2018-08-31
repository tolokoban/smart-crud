<?php
/**
 * # Filters
 * [ fld, equal ]
 * [ fld, [ in ] ]
 * [ fld, min, max ]
 * [ fld%, like ]
 * [ fld>, lower limit ]
 * [ fld<, upper limit ]
 * [ fld>=, min  ]
 * [ fld<=, max ]
 * [ fld!, not equal ]
 * [ fld!, [not in] ]
 */
namespace DataPersistence {
    const NOT_FOUND = -1;
    const SQS_ERROR = -9;

    function query() {
        global $DB;
        try {
            return \call_user_func_array( Array($DB, "query"), func_get_args() );
        }
        catch( Exception $ex ) {
            throw new \Exception( $ex->getMessage(), SQS_ERROR );
        }
    }
    function fetch() {
        $stm = \call_user_func_array( "\\DataPersistence\\query", func_get_args() );
        $row = $stm->fetch();
        if( !$row ) {
            error_log("[\\DataPersistence\\fetch] No data: " . json_encode(func_get_args()));
            throw new \Exception('[DataPersistence] There is no data!', NOT_FOUND);
        }
        return $row;
    }
    function exec() {
        global $DB;
        \call_user_func_array( "\\DataPersistence\\query", func_get_args() );
        return $DB->lastId();
    }
    function begin() {
        global $DB;
        $DB->begin();
    }
    function commit() {
        global $DB;
        $DB->commit();
    }
    function rollback() {
        global $DB;
        $DB->rollback();
    }
    function ensureArrayOfInt( &$arr ) {
        if( !is_array( $arr ) ) return '()';
        $values = [];
        foreach( $arr as $item ) {
            if( is_numeric( $item ) ) $values[] = intval( $item );
        }
        return '(' . implode(',', $values) . ')';
    }
}
namespace DataPersistence\Group {
    function name() {
        global $DB;
        return $DB->table('group');
    }
    function all() {
        $stm = \DataPersistence\query('SELECT id FROM' . \DataPersistence\Group\name());
        $ids = [];
        while( null != ($row = $stm->fetch()) ) {
            $ids[] = intVal($row[0]);
        }
        return $ids;
    }
    function get( $id ) {
        if( is_array( $id ) ) {
            $ids = \DataPersistence\ensureArrayOfInt( $id );
            return \DataPersistence\query(
                'SELECT * FROM' . \DataPersistence\Group\name() . 'WHERE id IN ' . $ids);
        }
        $row = \DataPersistence\fetch('SELECT * FROM' . \DataPersistence\Group\name() . 'WHERE id=?', $id );
        return ['id' => intVal($row['id']),
                'name' => $row['name']];
    }
    function add( $values ) {
        try {
            $args = [null];
            $sets = [];
            $fields = [];
            $allowedFields = ['name'];
            foreach( $values as $key => $val ) {
                if( !in_array( $key, $allowedFields ) )
                    throw new \Exception("[\\DataPersistence\\Group\\add()] Unknown field: $key!");
                $sets[] = "?";
                $args[] = $val;
                $fields[] = '`' . $key . '`';
            }
            $args[0] = 'INSERT INTO' . \DataPersistence\Group\name() . '(' . implode(',', $fields) . ')'
                     . 'VALUES(' . implode(',', $sets) . ')';
            return call_user_func_array( "\\Data\\exec", $args );
        }
        catch( \Exception $e ) {
            error_log("Exception in \\DataPersistence\\Group\\add( " . json_encode($values) . ")!");
            error_log("   error:  " . $e->getMessage());
            error_log("   values: " . json_encode( $values ));
            throw $e;
        }
    }
    function upd( $id, $values ) {
        try {
            $args = [null];
            $sets = [];
            $fields = ['name'];
            foreach( $values as $key => $val ) {
                if( !in_array( $key, $fields ) )
                    throw new \Exception("[\\DataPersistence\\Group\\upd()] Unknown field: $key!");
                $sets[] = "`$key`=?";
                $args[] = $val;
            }
            $args[0] = 'UPDATE' . \DataPersistence\Group\name() . 'SET '
                     . implode(',', $sets) . ' WHERE id=?';
            $args[] = $id;
            call_user_func_array( "\\Data\\query", $args );
        }
        catch( \Exception $e ) {
            error_log("Exception in \\DataPersistence\\Group\\upd( $id, values )!");
            error_log("   error:  " . $e->getMessage());
            error_log("   values: " . json_encode( $values ));
            throw $e;
        }
    }
    function del( $id ) {
        \DataPersistence\exec( 'DELETE FROM' . \DataPersistence\Group\name() . 'WHERE id=?', $id );
    }
    function getStudents( $idGroup ) {
        $stm = \DataPersistence\query(
            'SELECT id FROM' . \DataPersistence\Student\name()
          . 'WHERE `group`=?', $idGroup);
        $ids = [];
        while( null != ($row = $stm->fetch()) ) {
            $ids[] = intVal($row[0]);
        }
        return $ids;
    }
    function linkStudents( $idGroup, $idStudent ) {
        \DataPersistence\query(
            'UPDATE' . \DataPersistence\Student\name()
          . 'SET `group`=? '
          . 'WHERE id=?', $idGroup, $idStudent);
    }
    function getTeachers( $idGroup ) {
        global $DB;
        $stm = \DataPersistence\query(
            'SELECT `Teacher` FROM' . $DB->table('Group_Teacher')
          . 'WHERE `Group`=?', $idGroup);
        $ids = [];
        while( null != ($row = $stm->fetch()) ) {
            $ids[] = intVal($row[0]);
        }
        return $ids;
    }
    function linkTeachers( $idGroup, $idTeacher ) {
        global $DB;
        \DataPersistence\query(
            'INSERT INTO' . $DB->table('Group_Teacher')
          . '(`Group`, `Teacher`)'
          . 'VALUES(?,?)', $idGroup, $idTeacher);
    }
    function unlinkTeachers( $idGroup, $idTeacher=null ) {
        global $DB;
        if( $idTeacher == null ) {
          \DataPersistence\query(
              'DELETE FROM' . $DB->table('Group_Teacher')
            . 'WHERE `Group`=?', $idGroup);
        }
        else {
          \DataPersistence\query(
              'DELETE FROM' . $DB->table('Group_Teacher')
            . 'WHERE `Group`=? AND `Teacher`=?', $idGroup, $idTeacher);
        }
    }
    function getAssistants( $idGroup ) {
        global $DB;
        $stm = \DataPersistence\query(
            'SELECT `Teacher` FROM' . $DB->table('Group_Teacher_2')
          . 'WHERE `Group`=?', $idGroup);
        $ids = [];
        while( null != ($row = $stm->fetch()) ) {
            $ids[] = intVal($row[0]);
        }
        return $ids;
    }
    function linkAssistants( $idGroup, $idTeacher ) {
        global $DB;
        \DataPersistence\query(
            'INSERT INTO' . $DB->table('Group_Teacher_2')
          . '(`Group`, `Teacher`)'
          . 'VALUES(?,?)', $idGroup, $idTeacher);
    }
    function unlinkAssistants( $idGroup, $idTeacher=null ) {
        global $DB;
        if( $idTeacher == null ) {
          \DataPersistence\query(
              'DELETE FROM' . $DB->table('Group_Teacher_2')
            . 'WHERE `Group`=?', $idGroup);
        }
        else {
          \DataPersistence\query(
              'DELETE FROM' . $DB->table('Group_Teacher_2')
            . 'WHERE `Group`=? AND `Teacher`=?', $idGroup, $idTeacher);
        }
    }
}
namespace DataPersistence\Student {
    function name() {
        global $DB;
        return $DB->table('student');
    }
    function all() {
        $stm = \DataPersistence\query('SELECT id FROM' . \DataPersistence\Student\name());
        $ids = [];
        while( null != ($row = $stm->fetch()) ) {
            $ids[] = intVal($row[0]);
        }
        return $ids;
    }
    function get( $id ) {
        if( is_array( $id ) ) {
            $ids = \DataPersistence\ensureArrayOfInt( $id );
            return \DataPersistence\query(
                'SELECT * FROM' . \DataPersistence\Student\name() . 'WHERE id IN ' . $ids);
        }
        $row = \DataPersistence\fetch('SELECT * FROM' . \DataPersistence\Student\name() . 'WHERE id=?', $id );
        return ['id' => intVal($row['id']),
                'name' => $row['name']];
    }
    function add( $values ) {
        try {
            $args = [null];
            $sets = [];
            $fields = [];
            $allowedFields = ['name'];
            foreach( $values as $key => $val ) {
                if( !in_array( $key, $allowedFields ) )
                    throw new \Exception("[\\DataPersistence\\Student\\add()] Unknown field: $key!");
                $sets[] = "?";
                $args[] = $val;
                $fields[] = '`' . $key . '`';
            }
            $args[0] = 'INSERT INTO' . \DataPersistence\Student\name() . '(' . implode(',', $fields) . ')'
                     . 'VALUES(' . implode(',', $sets) . ')';
            return call_user_func_array( "\\Data\\exec", $args );
        }
        catch( \Exception $e ) {
            error_log("Exception in \\DataPersistence\\Student\\add( " . json_encode($values) . ")!");
            error_log("   error:  " . $e->getMessage());
            error_log("   values: " . json_encode( $values ));
            throw $e;
        }
    }
    function upd( $id, $values ) {
        try {
            $args = [null];
            $sets = [];
            $fields = ['name'];
            foreach( $values as $key => $val ) {
                if( !in_array( $key, $fields ) )
                    throw new \Exception("[\\DataPersistence\\Student\\upd()] Unknown field: $key!");
                $sets[] = "`$key`=?";
                $args[] = $val;
            }
            $args[0] = 'UPDATE' . \DataPersistence\Student\name() . 'SET '
                     . implode(',', $sets) . ' WHERE id=?';
            $args[] = $id;
            call_user_func_array( "\\Data\\query", $args );
        }
        catch( \Exception $e ) {
            error_log("Exception in \\DataPersistence\\Student\\upd( $id, values )!");
            error_log("   error:  " . $e->getMessage());
            error_log("   values: " . json_encode( $values ));
            throw $e;
        }
    }
    function del( $id ) {
        \DataPersistence\exec( 'DELETE FROM' . \DataPersistence\Student\name() . 'WHERE id=?', $id );
    }
    function getGroup( $idStudent ) {
        $row = \DataPersistence\fetch(
            'SELECT `group` FROM' . \DataPersistence\Student\name()
          . 'WHERE id=?', $idStudent);
        return intVal($row[0]);
    }
    function linkGroup( $idStudent, $idGroup ) {
        \DataPersistence\query(
            'UPDATE' . \DataPersistence\Student\name()
          . 'SET `group`=? '
          . 'WHERE id=?', $idGroup, $idStudent);
    }
}
namespace DataPersistence\Teacher {
    function name() {
        global $DB;
        return $DB->table('teacher');
    }
    function all() {
        $stm = \DataPersistence\query('SELECT id FROM' . \DataPersistence\Teacher\name());
        $ids = [];
        while( null != ($row = $stm->fetch()) ) {
            $ids[] = intVal($row[0]);
        }
        return $ids;
    }
    function get( $id ) {
        if( is_array( $id ) ) {
            $ids = \DataPersistence\ensureArrayOfInt( $id );
            return \DataPersistence\query(
                'SELECT * FROM' . \DataPersistence\Teacher\name() . 'WHERE id IN ' . $ids);
        }
        $row = \DataPersistence\fetch('SELECT * FROM' . \DataPersistence\Teacher\name() . 'WHERE id=?', $id );
        return ['id' => intVal($row['id']),
                'name' => $row['name']];
    }
    function add( $values ) {
        try {
            $args = [null];
            $sets = [];
            $fields = [];
            $allowedFields = ['name'];
            foreach( $values as $key => $val ) {
                if( !in_array( $key, $allowedFields ) )
                    throw new \Exception("[\\DataPersistence\\Teacher\\add()] Unknown field: $key!");
                $sets[] = "?";
                $args[] = $val;
                $fields[] = '`' . $key . '`';
            }
            $args[0] = 'INSERT INTO' . \DataPersistence\Teacher\name() . '(' . implode(',', $fields) . ')'
                     . 'VALUES(' . implode(',', $sets) . ')';
            return call_user_func_array( "\\Data\\exec", $args );
        }
        catch( \Exception $e ) {
            error_log("Exception in \\DataPersistence\\Teacher\\add( " . json_encode($values) . ")!");
            error_log("   error:  " . $e->getMessage());
            error_log("   values: " . json_encode( $values ));
            throw $e;
        }
    }
    function upd( $id, $values ) {
        try {
            $args = [null];
            $sets = [];
            $fields = ['name'];
            foreach( $values as $key => $val ) {
                if( !in_array( $key, $fields ) )
                    throw new \Exception("[\\DataPersistence\\Teacher\\upd()] Unknown field: $key!");
                $sets[] = "`$key`=?";
                $args[] = $val;
            }
            $args[0] = 'UPDATE' . \DataPersistence\Teacher\name() . 'SET '
                     . implode(',', $sets) . ' WHERE id=?';
            $args[] = $id;
            call_user_func_array( "\\Data\\query", $args );
        }
        catch( \Exception $e ) {
            error_log("Exception in \\DataPersistence\\Teacher\\upd( $id, values )!");
            error_log("   error:  " . $e->getMessage());
            error_log("   values: " . json_encode( $values ));
            throw $e;
        }
    }
    function del( $id ) {
        \DataPersistence\exec( 'DELETE FROM' . \DataPersistence\Teacher\name() . 'WHERE id=?', $id );
    }
    function getGroups( $idTeacher ) {
        global $DB;
        $stm = \DataPersistence\query(
            'SELECT `Group` FROM' . $DB->table('Group_Teacher')
          . 'WHERE `Teacher`=?', $idTeacher);
        $ids = [];
        while( null != ($row = $stm->fetch()) ) {
            $ids[] = intVal($row[0]);
        }
        return $ids;
    }
    function linkGroups( $idTeacher, $idGroup ) {
        global $DB;
        \DataPersistence\query(
            'INSERT INTO' . $DB->table('Group_Teacher')
          . '(`Teacher`, `Group`)'
          . 'VALUES(?,?)', $idTeacher, $idGroup);
    }
    function unlinkGroups( $idTeacher, $idGroup=null ) {
        global $DB;
        if( $idGroup == null ) {
          \DataPersistence\query(
              'DELETE FROM' . $DB->table('Group_Teacher')
            . 'WHERE `Teacher`=?', $idTeacher);
        }
        else {
          \DataPersistence\query(
              'DELETE FROM' . $DB->table('Group_Teacher')
            . 'WHERE `Teacher`=? AND `Group`=?', $idTeacher, $idGroup);
        }
    }
    function getAssistedGroups( $idTeacher ) {
        global $DB;
        $stm = \DataPersistence\query(
            'SELECT `Group` FROM' . $DB->table('Group_Teacher_2')
          . 'WHERE `Teacher`=?', $idTeacher);
        $ids = [];
        while( null != ($row = $stm->fetch()) ) {
            $ids[] = intVal($row[0]);
        }
        return $ids;
    }
    function linkAssistedGroups( $idTeacher, $idGroup ) {
        global $DB;
        \DataPersistence\query(
            'INSERT INTO' . $DB->table('Group_Teacher_2')
          . '(`Teacher`, `Group`)'
          . 'VALUES(?,?)', $idTeacher, $idGroup);
    }
    function unlinkAssistedGroups( $idTeacher, $idGroup=null ) {
        global $DB;
        if( $idGroup == null ) {
          \DataPersistence\query(
              'DELETE FROM' . $DB->table('Group_Teacher_2')
            . 'WHERE `Teacher`=?', $idTeacher);
        }
        else {
          \DataPersistence\query(
              'DELETE FROM' . $DB->table('Group_Teacher_2')
            . 'WHERE `Teacher`=? AND `Group`=?', $idTeacher, $idGroup);
        }
    }
}
namespace DataPersistence\User {
    function name() {
        global $DB;
        return $DB->table('user');
    }
    function all() {
        $stm = \DataPersistence\query('SELECT id FROM' . \DataPersistence\User\name());
        $ids = [];
        while( null != ($row = $stm->fetch()) ) {
            $ids[] = intVal($row[0]);
        }
        return $ids;
    }
    function get( $id ) {
        if( is_array( $id ) ) {
            $ids = \DataPersistence\ensureArrayOfInt( $id );
            return \DataPersistence\query(
                'SELECT * FROM' . \DataPersistence\User\name() . 'WHERE id IN ' . $ids);
        }
        $row = \DataPersistence\fetch('SELECT * FROM' . \DataPersistence\User\name() . 'WHERE id=?', $id );
        return ['id' => intVal($row['id']),
                'login' => $row['login'],
                'password' => $row['password'],
                'name' => $row['name'],
                'roles' => $row['roles'],
                'enabled' => $row['enabled'],
                'creation' => $row['creation'],
                'data' => $row['data']];
    }
    function add( $values ) {
        try {
            $args = [null];
            $sets = [];
            $fields = [];
            $allowedFields = ['login','password','name','roles','enabled','creation','data'];
            foreach( $values as $key => $val ) {
                if( !in_array( $key, $allowedFields ) )
                    throw new \Exception("[\\DataPersistence\\User\\add()] Unknown field: $key!");
                $sets[] = "?";
                $args[] = $val;
                $fields[] = '`' . $key . '`';
            }
            $args[0] = 'INSERT INTO' . \DataPersistence\User\name() . '(' . implode(',', $fields) . ')'
                     . 'VALUES(' . implode(',', $sets) . ')';
            return call_user_func_array( "\\Data\\exec", $args );
        }
        catch( \Exception $e ) {
            error_log("Exception in \\DataPersistence\\User\\add( " . json_encode($values) . ")!");
            error_log("   error:  " . $e->getMessage());
            error_log("   values: " . json_encode( $values ));
            throw $e;
        }
    }
    function upd( $id, $values ) {
        try {
            $args = [null];
            $sets = [];
            $fields = ['login','password','name','roles','enabled','creation','data'];
            foreach( $values as $key => $val ) {
                if( !in_array( $key, $fields ) )
                    throw new \Exception("[\\DataPersistence\\User\\upd()] Unknown field: $key!");
                $sets[] = "`$key`=?";
                $args[] = $val;
            }
            $args[0] = 'UPDATE' . \DataPersistence\User\name() . 'SET '
                     . implode(',', $sets) . ' WHERE id=?';
            $args[] = $id;
            call_user_func_array( "\\Data\\query", $args );
        }
        catch( \Exception $e ) {
            error_log("Exception in \\DataPersistence\\User\\upd( $id, values )!");
            error_log("   error:  " . $e->getMessage());
            error_log("   values: " . json_encode( $values ));
            throw $e;
        }
    }
    function del( $id ) {
        \DataPersistence\exec( 'DELETE FROM' . \DataPersistence\User\name() . 'WHERE id=?', $id );
    }
}
?>