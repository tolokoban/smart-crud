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
namespace Data {
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
        $stm = \call_user_func_array( "\\Data\\query", func_get_args() );
        $row = $stm->fetch();
        if( !$row ) {
            error_log("[\\Data\\fetch] No data: " . json_encode(func_get_args()));
            throw new \Exception('[Data] There is no data!', NOT_FOUND);
        }
        return $row;
    }
    function exec() {
        global $DB;
        \call_user_func_array( "\\Data\\query", func_get_args() );
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
namespace Data\User {
    function name() {
        global $DB;
        return $DB->table('user');
    }
    function all() {
        $stm = \Data\query('SELECT id FROM' . \Data\User\name());
        $ids = [];
        while( null != ($row = $stm->fetch()) ) {
            $ids[] = intVal($row[0]);
        }
        return $ids;
    }
    function get( $id ) {
        if( is_array( $id ) ) {
            $ids = \Data\ensureArrayOfInt( $id );
            return \Data\query(
                'SELECT * FROM' . \Data\User\name() . 'WHERE id IN ' . $ids);
        }
        $row = \Data\fetch('SELECT * FROM' . \Data\User\name() . 'WHERE id=?', $id );
        return ['id' => intVal($row['id']),
                'dashboard' => $row['dashboard'],
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
            $allowedFields = ['dashboard','login','password','name','roles','enabled','creation','data'];
            foreach( $values as $key => $val ) {
                if( !in_array( $key, $allowedFields ) )
                    throw new \Exception("[\\Data\\User\\add()] Unknown field: $key!");
                $sets[] = "?";
                $args[] = $val;
                $fields[] = '`' . $key . '`';
            }
            $args[0] = 'INSERT INTO' . \Data\User\name() . '(' . implode(',', $fields) . ')'
                     . 'VALUES(' . implode(',', $sets) . ')';
            return call_user_func_array( "\\Data\\exec", $args );
        }
        catch( \Exception $e ) {
            error_log("Exception in \\Data\\User\\add( " . json_encode($values) . ")!");
            error_log("   error:  " . $e->getMessage());
            error_log("   values: " . json_encode( $values ));
            throw $e;
        }
    }
    function upd( $id, $values ) {
        try {
            $args = [null];
            $sets = [];
            $fields = ['dashboard','login','password','name','roles','enabled','creation','data'];
            foreach( $values as $key => $val ) {
                if( !in_array( $key, $fields ) )
                    throw new \Exception("[\\Data\\User\\upd()] Unknown field: $key!");
                $sets[] = "`$key`=?";
                $args[] = $val;
            }
            $args[0] = 'UPDATE' . \Data\User\name() . 'SET '
                     . implode(',', $sets) . ' WHERE id=?';
            $args[] = $id;
            call_user_func_array( "\\Data\\query", $args );
        }
        catch( \Exception $e ) {
            error_log("Exception in \\Data\\User\\upd( $id, values )!");
            error_log("   error:  " . $e->getMessage());
            error_log("   values: " . json_encode( $values ));
            throw $e;
        }
    }
    function del( $id ) {
        \Data\exec( 'DELETE FROM' . \Data\User\name() . 'WHERE id=?', $id );
    }
    function getOrganizations( $idUser ) {
        global $DB;
        $stm = \Data\query(
            'SELECT `Organization` FROM' . $DB->table('Organization_User')
          . 'WHERE `User`=?', $idUser);
        $ids = [];
        while( null != ($row = $stm->fetch()) ) {
            $ids[] = intVal($row[0]);
        }
        return $ids;
    }
    function linkOrganizations( $idUser, $idOrganization ) {
        global $DB;
        \Data\query(
            'INSERT INTO' . $DB->table('Organization_User')
          . '(`User`, `Organization`)'
          . 'VALUES(?,?)', $idUser, $idOrganization);
    }
    function unlinkOrganizations( $idUser, $idOrganization=null ) {
        global $DB;
        if( $idOrganization == null ) {
          \Data\query(
              'DELETE FROM' . $DB->table('Organization_User')
            . 'WHERE `User`=?', $idUser);
        }
        else {
          \Data\query(
              'DELETE FROM' . $DB->table('Organization_User')
            . 'WHERE `User`=? AND `Organization`=?', $idUser, $idOrganization);
        }
    }
    function getCarecenters( $idUser ) {
        global $DB;
        $stm = \Data\query(
            'SELECT `Carecenter` FROM' . $DB->table('Carecenter_User')
          . 'WHERE `User`=?', $idUser);
        $ids = [];
        while( null != ($row = $stm->fetch()) ) {
            $ids[] = intVal($row[0]);
        }
        return $ids;
    }
    function linkCarecenters( $idUser, $idCarecenter ) {
        global $DB;
        \Data\query(
            'INSERT INTO' . $DB->table('Carecenter_User')
          . '(`User`, `Carecenter`)'
          . 'VALUES(?,?)', $idUser, $idCarecenter);
    }
    function unlinkCarecenters( $idUser, $idCarecenter=null ) {
        global $DB;
        if( $idCarecenter == null ) {
          \Data\query(
              'DELETE FROM' . $DB->table('Carecenter_User')
            . 'WHERE `User`=?', $idUser);
        }
        else {
          \Data\query(
              'DELETE FROM' . $DB->table('Carecenter_User')
            . 'WHERE `User`=? AND `Carecenter`=?', $idUser, $idCarecenter);
        }
    }
}
namespace Data\Organization {
    function name() {
        global $DB;
        return $DB->table('organization');
    }
    function all() {
        $stm = \Data\query('SELECT id FROM' . \Data\Organization\name());
        $ids = [];
        while( null != ($row = $stm->fetch()) ) {
            $ids[] = intVal($row[0]);
        }
        return $ids;
    }
    function get( $id ) {
        if( is_array( $id ) ) {
            $ids = \Data\ensureArrayOfInt( $id );
            return \Data\query(
                'SELECT * FROM' . \Data\Organization\name() . 'WHERE id IN ' . $ids);
        }
        $row = \Data\fetch('SELECT * FROM' . \Data\Organization\name() . 'WHERE id=?', $id );
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
                    throw new \Exception("[\\Data\\Organization\\add()] Unknown field: $key!");
                $sets[] = "?";
                $args[] = $val;
                $fields[] = '`' . $key . '`';
            }
            $args[0] = 'INSERT INTO' . \Data\Organization\name() . '(' . implode(',', $fields) . ')'
                     . 'VALUES(' . implode(',', $sets) . ')';
            return call_user_func_array( "\\Data\\exec", $args );
        }
        catch( \Exception $e ) {
            error_log("Exception in \\Data\\Organization\\add( " . json_encode($values) . ")!");
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
                    throw new \Exception("[\\Data\\Organization\\upd()] Unknown field: $key!");
                $sets[] = "`$key`=?";
                $args[] = $val;
            }
            $args[0] = 'UPDATE' . \Data\Organization\name() . 'SET '
                     . implode(',', $sets) . ' WHERE id=?';
            $args[] = $id;
            call_user_func_array( "\\Data\\query", $args );
        }
        catch( \Exception $e ) {
            error_log("Exception in \\Data\\Organization\\upd( $id, values )!");
            error_log("   error:  " . $e->getMessage());
            error_log("   values: " . json_encode( $values ));
            throw $e;
        }
    }
    function del( $id ) {
        \Data\exec( 'DELETE FROM' . \Data\Organization\name() . 'WHERE id=?', $id );
    }
    function getCarecenters( $idOrganization ) {
        $stm = \Data\query(
            'SELECT id FROM' . \Data\Carecenter\name()
          . 'WHERE `organization`=?', $idOrganization);
        $ids = [];
        while( null != ($row = $stm->fetch()) ) {
            $ids[] = intVal($row[0]);
        }
        return $ids;
    }
    function linkCarecenters( $idOrganization, $idCarecenter ) {
        \Data\query(
            'UPDATE' . \Data\Carecenter\name()
          . 'SET `organization`=? '
          . 'WHERE id=?', $idOrganization, $idCarecenter);
    }
    function getStructures( $idOrganization ) {
        $stm = \Data\query(
            'SELECT id FROM' . \Data\Structure\name()
          . 'WHERE `organization`=?', $idOrganization);
        $ids = [];
        while( null != ($row = $stm->fetch()) ) {
            $ids[] = intVal($row[0]);
        }
        return $ids;
    }
    function linkStructures( $idOrganization, $idStructure ) {
        \Data\query(
            'UPDATE' . \Data\Structure\name()
          . 'SET `organization`=? '
          . 'WHERE id=?', $idOrganization, $idStructure);
    }
    function getAdmins( $idOrganization ) {
        global $DB;
        $stm = \Data\query(
            'SELECT `User` FROM' . $DB->table('Organization_User')
          . 'WHERE `Organization`=?', $idOrganization);
        $ids = [];
        while( null != ($row = $stm->fetch()) ) {
            $ids[] = intVal($row[0]);
        }
        return $ids;
    }
    function linkAdmins( $idOrganization, $idUser ) {
        global $DB;
        \Data\query(
            'INSERT INTO' . $DB->table('Organization_User')
          . '(`Organization`, `User`)'
          . 'VALUES(?,?)', $idOrganization, $idUser);
    }
    function unlinkAdmins( $idOrganization, $idUser=null ) {
        global $DB;
        if( $idUser == null ) {
          \Data\query(
              'DELETE FROM' . $DB->table('Organization_User')
            . 'WHERE `Organization`=?', $idOrganization);
        }
        else {
          \Data\query(
              'DELETE FROM' . $DB->table('Organization_User')
            . 'WHERE `Organization`=? AND `User`=?', $idOrganization, $idUser);
        }
    }
}
namespace Data\Structure {
    function name() {
        global $DB;
        return $DB->table('structure');
    }
    function all() {
        $stm = \Data\query('SELECT id FROM' . \Data\Structure\name());
        $ids = [];
        while( null != ($row = $stm->fetch()) ) {
            $ids[] = intVal($row[0]);
        }
        return $ids;
    }
    function get( $id ) {
        if( is_array( $id ) ) {
            $ids = \Data\ensureArrayOfInt( $id );
            return \Data\query(
                'SELECT * FROM' . \Data\Structure\name() . 'WHERE id IN ' . $ids);
        }
        $row = \Data\fetch('SELECT * FROM' . \Data\Structure\name() . 'WHERE id=?', $id );
        return ['id' => intVal($row['id']),
                'name' => $row['name'],
                'exams' => $row['exams'],
                'vaccins' => $row['vaccins'],
                'patient' => $row['patient'],
                'forms' => $row['forms'],
                'types' => $row['types']];
    }
    function add( $values ) {
        try {
            $args = [null];
            $sets = [];
            $fields = [];
            $allowedFields = ['name','exams','vaccins','patient','forms','types'];
            foreach( $values as $key => $val ) {
                if( !in_array( $key, $allowedFields ) )
                    throw new \Exception("[\\Data\\Structure\\add()] Unknown field: $key!");
                $sets[] = "?";
                $args[] = $val;
                $fields[] = '`' . $key . '`';
            }
            $args[0] = 'INSERT INTO' . \Data\Structure\name() . '(' . implode(',', $fields) . ')'
                     . 'VALUES(' . implode(',', $sets) . ')';
            return call_user_func_array( "\\Data\\exec", $args );
        }
        catch( \Exception $e ) {
            error_log("Exception in \\Data\\Structure\\add( " . json_encode($values) . ")!");
            error_log("   error:  " . $e->getMessage());
            error_log("   values: " . json_encode( $values ));
            throw $e;
        }
    }
    function upd( $id, $values ) {
        try {
            $args = [null];
            $sets = [];
            $fields = ['name','exams','vaccins','patient','forms','types'];
            foreach( $values as $key => $val ) {
                if( !in_array( $key, $fields ) )
                    throw new \Exception("[\\Data\\Structure\\upd()] Unknown field: $key!");
                $sets[] = "`$key`=?";
                $args[] = $val;
            }
            $args[0] = 'UPDATE' . \Data\Structure\name() . 'SET '
                     . implode(',', $sets) . ' WHERE id=?';
            $args[] = $id;
            call_user_func_array( "\\Data\\query", $args );
        }
        catch( \Exception $e ) {
            error_log("Exception in \\Data\\Structure\\upd( $id, values )!");
            error_log("   error:  " . $e->getMessage());
            error_log("   values: " . json_encode( $values ));
            throw $e;
        }
    }
    function del( $id ) {
        \Data\exec( 'DELETE FROM' . \Data\Structure\name() . 'WHERE id=?', $id );
    }
    function getOrganization( $idStructure ) {
        $row = \Data\fetch(
            'SELECT `organization` FROM' . \Data\Structure\name()
          . 'WHERE id=?', $idStructure);
        return intVal($row[0]);
    }
    function linkOrganization( $idStructure, $idOrganization ) {
        \Data\query(
            'UPDATE' . \Data\Structure\name()
          . 'SET `organization`=? '
          . 'WHERE id=?', $idOrganization, $idStructure);
    }
    function getCarecenters( $idStructure ) {
        $stm = \Data\query(
            'SELECT id FROM' . \Data\Carecenter\name()
          . 'WHERE `structure`=?', $idStructure);
        $ids = [];
        while( null != ($row = $stm->fetch()) ) {
            $ids[] = intVal($row[0]);
        }
        return $ids;
    }
    function linkCarecenters( $idStructure, $idCarecenter ) {
        \Data\query(
            'UPDATE' . \Data\Carecenter\name()
          . 'SET `structure`=? '
          . 'WHERE id=?', $idStructure, $idCarecenter);
    }
}
namespace Data\Carecenter {
    function name() {
        global $DB;
        return $DB->table('carecenter');
    }
    function all() {
        $stm = \Data\query('SELECT id FROM' . \Data\Carecenter\name());
        $ids = [];
        while( null != ($row = $stm->fetch()) ) {
            $ids[] = intVal($row[0]);
        }
        return $ids;
    }
    function get( $id ) {
        if( is_array( $id ) ) {
            $ids = \Data\ensureArrayOfInt( $id );
            return \Data\query(
                'SELECT * FROM' . \Data\Carecenter\name() . 'WHERE id IN ' . $ids);
        }
        $row = \Data\fetch('SELECT * FROM' . \Data\Carecenter\name() . 'WHERE id=?', $id );
        return ['id' => intVal($row['id']),
                'name' => $row['name'],
                'code' => $row['code']];
    }
    function add( $values ) {
        try {
            $args = [null];
            $sets = [];
            $fields = [];
            $allowedFields = ['name','code'];
            foreach( $values as $key => $val ) {
                if( !in_array( $key, $allowedFields ) )
                    throw new \Exception("[\\Data\\Carecenter\\add()] Unknown field: $key!");
                $sets[] = "?";
                $args[] = $val;
                $fields[] = '`' . $key . '`';
            }
            $args[0] = 'INSERT INTO' . \Data\Carecenter\name() . '(' . implode(',', $fields) . ')'
                     . 'VALUES(' . implode(',', $sets) . ')';
            return call_user_func_array( "\\Data\\exec", $args );
        }
        catch( \Exception $e ) {
            error_log("Exception in \\Data\\Carecenter\\add( " . json_encode($values) . ")!");
            error_log("   error:  " . $e->getMessage());
            error_log("   values: " . json_encode( $values ));
            throw $e;
        }
    }
    function upd( $id, $values ) {
        try {
            $args = [null];
            $sets = [];
            $fields = ['name','code'];
            foreach( $values as $key => $val ) {
                if( !in_array( $key, $fields ) )
                    throw new \Exception("[\\Data\\Carecenter\\upd()] Unknown field: $key!");
                $sets[] = "`$key`=?";
                $args[] = $val;
            }
            $args[0] = 'UPDATE' . \Data\Carecenter\name() . 'SET '
                     . implode(',', $sets) . ' WHERE id=?';
            $args[] = $id;
            call_user_func_array( "\\Data\\query", $args );
        }
        catch( \Exception $e ) {
            error_log("Exception in \\Data\\Carecenter\\upd( $id, values )!");
            error_log("   error:  " . $e->getMessage());
            error_log("   values: " . json_encode( $values ));
            throw $e;
        }
    }
    function del( $id ) {
        \Data\exec( 'DELETE FROM' . \Data\Carecenter\name() . 'WHERE id=?', $id );
    }
    function getOrganization( $idCarecenter ) {
        $row = \Data\fetch(
            'SELECT `organization` FROM' . \Data\Carecenter\name()
          . 'WHERE id=?', $idCarecenter);
        return intVal($row[0]);
    }
    function linkOrganization( $idCarecenter, $idOrganization ) {
        \Data\query(
            'UPDATE' . \Data\Carecenter\name()
          . 'SET `organization`=? '
          . 'WHERE id=?', $idOrganization, $idCarecenter);
    }
    function getStructure( $idCarecenter ) {
        $row = \Data\fetch(
            'SELECT `structure` FROM' . \Data\Carecenter\name()
          . 'WHERE id=?', $idCarecenter);
        return intVal($row[0]);
    }
    function linkStructure( $idCarecenter, $idStructure ) {
        \Data\query(
            'UPDATE' . \Data\Carecenter\name()
          . 'SET `structure`=? '
          . 'WHERE id=?', $idStructure, $idCarecenter);
    }
    function getPatients( $idCarecenter ) {
        $stm = \Data\query(
            'SELECT id FROM' . \Data\Patient\name()
          . 'WHERE `carecenter`=?', $idCarecenter);
        $ids = [];
        while( null != ($row = $stm->fetch()) ) {
            $ids[] = intVal($row[0]);
        }
        return $ids;
    }
    function linkPatients( $idCarecenter, $idPatient ) {
        \Data\query(
            'UPDATE' . \Data\Patient\name()
          . 'SET `carecenter`=? '
          . 'WHERE id=?', $idCarecenter, $idPatient);
    }
    function getAdmins( $idCarecenter ) {
        global $DB;
        $stm = \Data\query(
            'SELECT `User` FROM' . $DB->table('Carecenter_User')
          . 'WHERE `Carecenter`=?', $idCarecenter);
        $ids = [];
        while( null != ($row = $stm->fetch()) ) {
            $ids[] = intVal($row[0]);
        }
        return $ids;
    }
    function linkAdmins( $idCarecenter, $idUser ) {
        global $DB;
        \Data\query(
            'INSERT INTO' . $DB->table('Carecenter_User')
          . '(`Carecenter`, `User`)'
          . 'VALUES(?,?)', $idCarecenter, $idUser);
    }
    function unlinkAdmins( $idCarecenter, $idUser=null ) {
        global $DB;
        if( $idUser == null ) {
          \Data\query(
              'DELETE FROM' . $DB->table('Carecenter_User')
            . 'WHERE `Carecenter`=?', $idCarecenter);
        }
        else {
          \Data\query(
              'DELETE FROM' . $DB->table('Carecenter_User')
            . 'WHERE `Carecenter`=? AND `User`=?', $idCarecenter, $idUser);
        }
    }
}
namespace Data\Patient {
    function name() {
        global $DB;
        return $DB->table('patient');
    }
    function all() {
        $stm = \Data\query('SELECT id FROM' . \Data\Patient\name());
        $ids = [];
        while( null != ($row = $stm->fetch()) ) {
            $ids[] = intVal($row[0]);
        }
        return $ids;
    }
    function get( $id ) {
        if( is_array( $id ) ) {
            $ids = \Data\ensureArrayOfInt( $id );
            return \Data\query(
                'SELECT * FROM' . \Data\Patient\name() . 'WHERE id IN ' . $ids);
        }
        $row = \Data\fetch('SELECT * FROM' . \Data\Patient\name() . 'WHERE id=?', $id );
        return ['id' => intVal($row['id']),
                'key' => $row['key'],
                'edited' => $row['edited']];
    }
    function add( $values ) {
        try {
            $args = [null];
            $sets = [];
            $fields = [];
            $allowedFields = ['key','edited'];
            foreach( $values as $key => $val ) {
                if( !in_array( $key, $allowedFields ) )
                    throw new \Exception("[\\Data\\Patient\\add()] Unknown field: $key!");
                $sets[] = "?";
                $args[] = $val;
                $fields[] = '`' . $key . '`';
            }
            $args[0] = 'INSERT INTO' . \Data\Patient\name() . '(' . implode(',', $fields) . ')'
                     . 'VALUES(' . implode(',', $sets) . ')';
            return call_user_func_array( "\\Data\\exec", $args );
        }
        catch( \Exception $e ) {
            error_log("Exception in \\Data\\Patient\\add( " . json_encode($values) . ")!");
            error_log("   error:  " . $e->getMessage());
            error_log("   values: " . json_encode( $values ));
            throw $e;
        }
    }
    function upd( $id, $values ) {
        try {
            $args = [null];
            $sets = [];
            $fields = ['key','edited'];
            foreach( $values as $key => $val ) {
                if( !in_array( $key, $fields ) )
                    throw new \Exception("[\\Data\\Patient\\upd()] Unknown field: $key!");
                $sets[] = "`$key`=?";
                $args[] = $val;
            }
            $args[0] = 'UPDATE' . \Data\Patient\name() . 'SET '
                     . implode(',', $sets) . ' WHERE id=?';
            $args[] = $id;
            call_user_func_array( "\\Data\\query", $args );
        }
        catch( \Exception $e ) {
            error_log("Exception in \\Data\\Patient\\upd( $id, values )!");
            error_log("   error:  " . $e->getMessage());
            error_log("   values: " . json_encode( $values ));
            throw $e;
        }
    }
    function del( $id ) {
        \Data\exec( 'DELETE FROM' . \Data\Patient\name() . 'WHERE id=?', $id );
    }
    function getCarecenter( $idPatient ) {
        $row = \Data\fetch(
            'SELECT `carecenter` FROM' . \Data\Patient\name()
          . 'WHERE id=?', $idPatient);
        return intVal($row[0]);
    }
    function linkCarecenter( $idPatient, $idCarecenter ) {
        \Data\query(
            'UPDATE' . \Data\Patient\name()
          . 'SET `carecenter`=? '
          . 'WHERE id=?', $idCarecenter, $idPatient);
    }
    function getFields( $idPatient ) {
        $stm = \Data\query(
            'SELECT id FROM' . \Data\PatientField\name()
          . 'WHERE `patient`=?', $idPatient);
        $ids = [];
        while( null != ($row = $stm->fetch()) ) {
            $ids[] = intVal($row[0]);
        }
        return $ids;
    }
    function linkFields( $idPatient, $idPatientField ) {
        \Data\query(
            'UPDATE' . \Data\PatientField\name()
          . 'SET `patient`=? '
          . 'WHERE id=?', $idPatient, $idPatientField);
    }
    function getAdmissions( $idPatient ) {
        $stm = \Data\query(
            'SELECT id FROM' . \Data\Admission\name()
          . 'WHERE `patient`=?', $idPatient);
        $ids = [];
        while( null != ($row = $stm->fetch()) ) {
            $ids[] = intVal($row[0]);
        }
        return $ids;
    }
    function linkAdmissions( $idPatient, $idAdmission ) {
        \Data\query(
            'UPDATE' . \Data\Admission\name()
          . 'SET `patient`=? '
          . 'WHERE id=?', $idPatient, $idAdmission);
    }
    function getAttachments( $idPatient ) {
        $stm = \Data\query(
            'SELECT id FROM' . \Data\Attachment\name()
          . 'WHERE `patient`=?', $idPatient);
        $ids = [];
        while( null != ($row = $stm->fetch()) ) {
            $ids[] = intVal($row[0]);
        }
        return $ids;
    }
    function linkAttachments( $idPatient, $idAttachment ) {
        \Data\query(
            'UPDATE' . \Data\Attachment\name()
          . 'SET `patient`=? '
          . 'WHERE id=?', $idPatient, $idAttachment);
    }
    function getVaccins( $idPatient ) {
        $stm = \Data\query(
            'SELECT id FROM' . \Data\Vaccin\name()
          . 'WHERE `patient`=?', $idPatient);
        $ids = [];
        while( null != ($row = $stm->fetch()) ) {
            $ids[] = intVal($row[0]);
        }
        return $ids;
    }
    function linkVaccins( $idPatient, $idVaccin ) {
        \Data\query(
            'UPDATE' . \Data\Vaccin\name()
          . 'SET `patient`=? '
          . 'WHERE id=?', $idPatient, $idVaccin);
    }
}
namespace Data\PatientField {
    function name() {
        global $DB;
        return $DB->table('patientField');
    }
    function all() {
        $stm = \Data\query('SELECT id FROM' . \Data\PatientField\name());
        $ids = [];
        while( null != ($row = $stm->fetch()) ) {
            $ids[] = intVal($row[0]);
        }
        return $ids;
    }
    function get( $id ) {
        if( is_array( $id ) ) {
            $ids = \Data\ensureArrayOfInt( $id );
            return \Data\query(
                'SELECT * FROM' . \Data\PatientField\name() . 'WHERE id IN ' . $ids);
        }
        $row = \Data\fetch('SELECT * FROM' . \Data\PatientField\name() . 'WHERE id=?', $id );
        return ['id' => intVal($row['id']),
                'key' => $row['key'],
                'value' => $row['value']];
    }
    function add( $values ) {
        try {
            $args = [null];
            $sets = [];
            $fields = [];
            $allowedFields = ['key','value'];
            foreach( $values as $key => $val ) {
                if( !in_array( $key, $allowedFields ) )
                    throw new \Exception("[\\Data\\PatientField\\add()] Unknown field: $key!");
                $sets[] = "?";
                $args[] = $val;
                $fields[] = '`' . $key . '`';
            }
            $args[0] = 'INSERT INTO' . \Data\PatientField\name() . '(' . implode(',', $fields) . ')'
                     . 'VALUES(' . implode(',', $sets) . ')';
            return call_user_func_array( "\\Data\\exec", $args );
        }
        catch( \Exception $e ) {
            error_log("Exception in \\Data\\PatientField\\add( " . json_encode($values) . ")!");
            error_log("   error:  " . $e->getMessage());
            error_log("   values: " . json_encode( $values ));
            throw $e;
        }
    }
    function upd( $id, $values ) {
        try {
            $args = [null];
            $sets = [];
            $fields = ['key','value'];
            foreach( $values as $key => $val ) {
                if( !in_array( $key, $fields ) )
                    throw new \Exception("[\\Data\\PatientField\\upd()] Unknown field: $key!");
                $sets[] = "`$key`=?";
                $args[] = $val;
            }
            $args[0] = 'UPDATE' . \Data\PatientField\name() . 'SET '
                     . implode(',', $sets) . ' WHERE id=?';
            $args[] = $id;
            call_user_func_array( "\\Data\\query", $args );
        }
        catch( \Exception $e ) {
            error_log("Exception in \\Data\\PatientField\\upd( $id, values )!");
            error_log("   error:  " . $e->getMessage());
            error_log("   values: " . json_encode( $values ));
            throw $e;
        }
    }
    function del( $id ) {
        \Data\exec( 'DELETE FROM' . \Data\PatientField\name() . 'WHERE id=?', $id );
    }
    function getPatient( $idPatientField ) {
        $row = \Data\fetch(
            'SELECT `patient` FROM' . \Data\PatientField\name()
          . 'WHERE id=?', $idPatientField);
        return intVal($row[0]);
    }
    function linkPatient( $idPatientField, $idPatient ) {
        \Data\query(
            'UPDATE' . \Data\PatientField\name()
          . 'SET `patient`=? '
          . 'WHERE id=?', $idPatient, $idPatientField);
    }
}
namespace Data\File {
    function name() {
        global $DB;
        return $DB->table('file');
    }
    function all() {
        $stm = \Data\query('SELECT id FROM' . \Data\File\name());
        $ids = [];
        while( null != ($row = $stm->fetch()) ) {
            $ids[] = intVal($row[0]);
        }
        return $ids;
    }
    function get( $id ) {
        if( is_array( $id ) ) {
            $ids = \Data\ensureArrayOfInt( $id );
            return \Data\query(
                'SELECT * FROM' . \Data\File\name() . 'WHERE id IN ' . $ids);
        }
        $row = \Data\fetch('SELECT * FROM' . \Data\File\name() . 'WHERE id=?', $id );
        return ['id' => intVal($row['id']),
                'name' => $row['name'],
                'hash' => $row['hash'],
                'mime' => $row['mime'],
                'size' => $row['size']];
    }
    function add( $values ) {
        try {
            $args = [null];
            $sets = [];
            $fields = [];
            $allowedFields = ['name','hash','mime','size'];
            foreach( $values as $key => $val ) {
                if( !in_array( $key, $allowedFields ) )
                    throw new \Exception("[\\Data\\File\\add()] Unknown field: $key!");
                $sets[] = "?";
                $args[] = $val;
                $fields[] = '`' . $key . '`';
            }
            $args[0] = 'INSERT INTO' . \Data\File\name() . '(' . implode(',', $fields) . ')'
                     . 'VALUES(' . implode(',', $sets) . ')';
            return call_user_func_array( "\\Data\\exec", $args );
        }
        catch( \Exception $e ) {
            error_log("Exception in \\Data\\File\\add( " . json_encode($values) . ")!");
            error_log("   error:  " . $e->getMessage());
            error_log("   values: " . json_encode( $values ));
            throw $e;
        }
    }
    function upd( $id, $values ) {
        try {
            $args = [null];
            $sets = [];
            $fields = ['name','hash','mime','size'];
            foreach( $values as $key => $val ) {
                if( !in_array( $key, $fields ) )
                    throw new \Exception("[\\Data\\File\\upd()] Unknown field: $key!");
                $sets[] = "`$key`=?";
                $args[] = $val;
            }
            $args[0] = 'UPDATE' . \Data\File\name() . 'SET '
                     . implode(',', $sets) . ' WHERE id=?';
            $args[] = $id;
            call_user_func_array( "\\Data\\query", $args );
        }
        catch( \Exception $e ) {
            error_log("Exception in \\Data\\File\\upd( $id, values )!");
            error_log("   error:  " . $e->getMessage());
            error_log("   values: " . json_encode( $values ));
            throw $e;
        }
    }
    function del( $id ) {
        \Data\exec( 'DELETE FROM' . \Data\File\name() . 'WHERE id=?', $id );
    }
}
namespace Data\Admission {
    function name() {
        global $DB;
        return $DB->table('admission');
    }
    function all() {
        $stm = \Data\query('SELECT id FROM' . \Data\Admission\name());
        $ids = [];
        while( null != ($row = $stm->fetch()) ) {
            $ids[] = intVal($row[0]);
        }
        return $ids;
    }
    function get( $id ) {
        if( is_array( $id ) ) {
            $ids = \Data\ensureArrayOfInt( $id );
            return \Data\query(
                'SELECT * FROM' . \Data\Admission\name() . 'WHERE id IN ' . $ids);
        }
        $row = \Data\fetch('SELECT * FROM' . \Data\Admission\name() . 'WHERE id=?', $id );
        return ['id' => intVal($row['id']),
                'enter' => $row['enter'],
                'exit' => $row['exit']];
    }
    function add( $values ) {
        try {
            $args = [null];
            $sets = [];
            $fields = [];
            $allowedFields = ['enter','exit'];
            foreach( $values as $key => $val ) {
                if( !in_array( $key, $allowedFields ) )
                    throw new \Exception("[\\Data\\Admission\\add()] Unknown field: $key!");
                $sets[] = "?";
                $args[] = $val;
                $fields[] = '`' . $key . '`';
            }
            $args[0] = 'INSERT INTO' . \Data\Admission\name() . '(' . implode(',', $fields) . ')'
                     . 'VALUES(' . implode(',', $sets) . ')';
            return call_user_func_array( "\\Data\\exec", $args );
        }
        catch( \Exception $e ) {
            error_log("Exception in \\Data\\Admission\\add( " . json_encode($values) . ")!");
            error_log("   error:  " . $e->getMessage());
            error_log("   values: " . json_encode( $values ));
            throw $e;
        }
    }
    function upd( $id, $values ) {
        try {
            $args = [null];
            $sets = [];
            $fields = ['enter','exit'];
            foreach( $values as $key => $val ) {
                if( !in_array( $key, $fields ) )
                    throw new \Exception("[\\Data\\Admission\\upd()] Unknown field: $key!");
                $sets[] = "`$key`=?";
                $args[] = $val;
            }
            $args[0] = 'UPDATE' . \Data\Admission\name() . 'SET '
                     . implode(',', $sets) . ' WHERE id=?';
            $args[] = $id;
            call_user_func_array( "\\Data\\query", $args );
        }
        catch( \Exception $e ) {
            error_log("Exception in \\Data\\Admission\\upd( $id, values )!");
            error_log("   error:  " . $e->getMessage());
            error_log("   values: " . json_encode( $values ));
            throw $e;
        }
    }
    function del( $id ) {
        \Data\exec( 'DELETE FROM' . \Data\Admission\name() . 'WHERE id=?', $id );
    }
    function getPatient( $idAdmission ) {
        $row = \Data\fetch(
            'SELECT `patient` FROM' . \Data\Admission\name()
          . 'WHERE id=?', $idAdmission);
        return intVal($row[0]);
    }
    function linkPatient( $idAdmission, $idPatient ) {
        \Data\query(
            'UPDATE' . \Data\Admission\name()
          . 'SET `patient`=? '
          . 'WHERE id=?', $idPatient, $idAdmission);
    }
    function getConsultations( $idAdmission ) {
        $stm = \Data\query(
            'SELECT id FROM' . \Data\Consultation\name()
          . 'WHERE `admission`=?', $idAdmission);
        $ids = [];
        while( null != ($row = $stm->fetch()) ) {
            $ids[] = intVal($row[0]);
        }
        return $ids;
    }
    function linkConsultations( $idAdmission, $idConsultation ) {
        \Data\query(
            'UPDATE' . \Data\Consultation\name()
          . 'SET `admission`=? '
          . 'WHERE id=?', $idAdmission, $idConsultation);
    }
}
namespace Data\Consultation {
    function name() {
        global $DB;
        return $DB->table('consultation');
    }
    function all() {
        $stm = \Data\query('SELECT id FROM' . \Data\Consultation\name());
        $ids = [];
        while( null != ($row = $stm->fetch()) ) {
            $ids[] = intVal($row[0]);
        }
        return $ids;
    }
    function get( $id ) {
        if( is_array( $id ) ) {
            $ids = \Data\ensureArrayOfInt( $id );
            return \Data\query(
                'SELECT * FROM' . \Data\Consultation\name() . 'WHERE id IN ' . $ids);
        }
        $row = \Data\fetch('SELECT * FROM' . \Data\Consultation\name() . 'WHERE id=?', $id );
        return ['id' => intVal($row['id']),
                'enter' => $row['enter'],
                'exit' => $row['exit']];
    }
    function add( $values ) {
        try {
            $args = [null];
            $sets = [];
            $fields = [];
            $allowedFields = ['enter','exit'];
            foreach( $values as $key => $val ) {
                if( !in_array( $key, $allowedFields ) )
                    throw new \Exception("[\\Data\\Consultation\\add()] Unknown field: $key!");
                $sets[] = "?";
                $args[] = $val;
                $fields[] = '`' . $key . '`';
            }
            $args[0] = 'INSERT INTO' . \Data\Consultation\name() . '(' . implode(',', $fields) . ')'
                     . 'VALUES(' . implode(',', $sets) . ')';
            return call_user_func_array( "\\Data\\exec", $args );
        }
        catch( \Exception $e ) {
            error_log("Exception in \\Data\\Consultation\\add( " . json_encode($values) . ")!");
            error_log("   error:  " . $e->getMessage());
            error_log("   values: " . json_encode( $values ));
            throw $e;
        }
    }
    function upd( $id, $values ) {
        try {
            $args = [null];
            $sets = [];
            $fields = ['enter','exit'];
            foreach( $values as $key => $val ) {
                if( !in_array( $key, $fields ) )
                    throw new \Exception("[\\Data\\Consultation\\upd()] Unknown field: $key!");
                $sets[] = "`$key`=?";
                $args[] = $val;
            }
            $args[0] = 'UPDATE' . \Data\Consultation\name() . 'SET '
                     . implode(',', $sets) . ' WHERE id=?';
            $args[] = $id;
            call_user_func_array( "\\Data\\query", $args );
        }
        catch( \Exception $e ) {
            error_log("Exception in \\Data\\Consultation\\upd( $id, values )!");
            error_log("   error:  " . $e->getMessage());
            error_log("   values: " . json_encode( $values ));
            throw $e;
        }
    }
    function del( $id ) {
        \Data\exec( 'DELETE FROM' . \Data\Consultation\name() . 'WHERE id=?', $id );
    }
    function getAdmission( $idConsultation ) {
        $row = \Data\fetch(
            'SELECT `admission` FROM' . \Data\Consultation\name()
          . 'WHERE id=?', $idConsultation);
        return intVal($row[0]);
    }
    function linkAdmission( $idConsultation, $idAdmission ) {
        \Data\query(
            'UPDATE' . \Data\Consultation\name()
          . 'SET `admission`=? '
          . 'WHERE id=?', $idAdmission, $idConsultation);
    }
    function getDatas( $idConsultation ) {
        $stm = \Data\query(
            'SELECT id FROM' . \Data\Data\name()
          . 'WHERE `consultation`=?', $idConsultation);
        $ids = [];
        while( null != ($row = $stm->fetch()) ) {
            $ids[] = intVal($row[0]);
        }
        return $ids;
    }
    function linkDatas( $idConsultation, $idData ) {
        \Data\query(
            'UPDATE' . \Data\Data\name()
          . 'SET `consultation`=? '
          . 'WHERE id=?', $idConsultation, $idData);
    }
}
namespace Data\Data {
    function name() {
        global $DB;
        return $DB->table('data');
    }
    function all() {
        $stm = \Data\query('SELECT id FROM' . \Data\Data\name());
        $ids = [];
        while( null != ($row = $stm->fetch()) ) {
            $ids[] = intVal($row[0]);
        }
        return $ids;
    }
    function get( $id ) {
        if( is_array( $id ) ) {
            $ids = \Data\ensureArrayOfInt( $id );
            return \Data\query(
                'SELECT * FROM' . \Data\Data\name() . 'WHERE id IN ' . $ids);
        }
        $row = \Data\fetch('SELECT * FROM' . \Data\Data\name() . 'WHERE id=?', $id );
        return ['id' => intVal($row['id']),
                'key' => $row['key'],
                'value' => $row['value']];
    }
    function add( $values ) {
        try {
            $args = [null];
            $sets = [];
            $fields = [];
            $allowedFields = ['key','value'];
            foreach( $values as $key => $val ) {
                if( !in_array( $key, $allowedFields ) )
                    throw new \Exception("[\\Data\\Data\\add()] Unknown field: $key!");
                $sets[] = "?";
                $args[] = $val;
                $fields[] = '`' . $key . '`';
            }
            $args[0] = 'INSERT INTO' . \Data\Data\name() . '(' . implode(',', $fields) . ')'
                     . 'VALUES(' . implode(',', $sets) . ')';
            return call_user_func_array( "\\Data\\exec", $args );
        }
        catch( \Exception $e ) {
            error_log("Exception in \\Data\\Data\\add( " . json_encode($values) . ")!");
            error_log("   error:  " . $e->getMessage());
            error_log("   values: " . json_encode( $values ));
            throw $e;
        }
    }
    function upd( $id, $values ) {
        try {
            $args = [null];
            $sets = [];
            $fields = ['key','value'];
            foreach( $values as $key => $val ) {
                if( !in_array( $key, $fields ) )
                    throw new \Exception("[\\Data\\Data\\upd()] Unknown field: $key!");
                $sets[] = "`$key`=?";
                $args[] = $val;
            }
            $args[0] = 'UPDATE' . \Data\Data\name() . 'SET '
                     . implode(',', $sets) . ' WHERE id=?';
            $args[] = $id;
            call_user_func_array( "\\Data\\query", $args );
        }
        catch( \Exception $e ) {
            error_log("Exception in \\Data\\Data\\upd( $id, values )!");
            error_log("   error:  " . $e->getMessage());
            error_log("   values: " . json_encode( $values ));
            throw $e;
        }
    }
    function del( $id ) {
        \Data\exec( 'DELETE FROM' . \Data\Data\name() . 'WHERE id=?', $id );
    }
    function getConsultation( $idData ) {
        $row = \Data\fetch(
            'SELECT `consultation` FROM' . \Data\Data\name()
          . 'WHERE id=?', $idData);
        return intVal($row[0]);
    }
    function linkConsultation( $idData, $idConsultation ) {
        \Data\query(
            'UPDATE' . \Data\Data\name()
          . 'SET `consultation`=? '
          . 'WHERE id=?', $idConsultation, $idData);
    }
}
namespace Data\Shapshot {
    function name() {
        global $DB;
        return $DB->table('shapshot');
    }
    function all() {
        $stm = \Data\query('SELECT id FROM' . \Data\Shapshot\name());
        $ids = [];
        while( null != ($row = $stm->fetch()) ) {
            $ids[] = intVal($row[0]);
        }
        return $ids;
    }
    function get( $id ) {
        if( is_array( $id ) ) {
            $ids = \Data\ensureArrayOfInt( $id );
            return \Data\query(
                'SELECT * FROM' . \Data\Shapshot\name() . 'WHERE id IN ' . $ids);
        }
        $row = \Data\fetch('SELECT * FROM' . \Data\Shapshot\name() . 'WHERE id=?', $id );
        return ['id' => intVal($row['id']),
                'key' => $row['key'],
                'value' => $row['value']];
    }
    function add( $values ) {
        try {
            $args = [null];
            $sets = [];
            $fields = [];
            $allowedFields = ['key','value'];
            foreach( $values as $key => $val ) {
                if( !in_array( $key, $allowedFields ) )
                    throw new \Exception("[\\Data\\Shapshot\\add()] Unknown field: $key!");
                $sets[] = "?";
                $args[] = $val;
                $fields[] = '`' . $key . '`';
            }
            $args[0] = 'INSERT INTO' . \Data\Shapshot\name() . '(' . implode(',', $fields) . ')'
                     . 'VALUES(' . implode(',', $sets) . ')';
            return call_user_func_array( "\\Data\\exec", $args );
        }
        catch( \Exception $e ) {
            error_log("Exception in \\Data\\Shapshot\\add( " . json_encode($values) . ")!");
            error_log("   error:  " . $e->getMessage());
            error_log("   values: " . json_encode( $values ));
            throw $e;
        }
    }
    function upd( $id, $values ) {
        try {
            $args = [null];
            $sets = [];
            $fields = ['key','value'];
            foreach( $values as $key => $val ) {
                if( !in_array( $key, $fields ) )
                    throw new \Exception("[\\Data\\Shapshot\\upd()] Unknown field: $key!");
                $sets[] = "`$key`=?";
                $args[] = $val;
            }
            $args[0] = 'UPDATE' . \Data\Shapshot\name() . 'SET '
                     . implode(',', $sets) . ' WHERE id=?';
            $args[] = $id;
            call_user_func_array( "\\Data\\query", $args );
        }
        catch( \Exception $e ) {
            error_log("Exception in \\Data\\Shapshot\\upd( $id, values )!");
            error_log("   error:  " . $e->getMessage());
            error_log("   values: " . json_encode( $values ));
            throw $e;
        }
    }
    function del( $id ) {
        \Data\exec( 'DELETE FROM' . \Data\Shapshot\name() . 'WHERE id=?', $id );
    }
}
namespace Data\Attachment {
    function name() {
        global $DB;
        return $DB->table('attachment');
    }
    function all() {
        $stm = \Data\query('SELECT id FROM' . \Data\Attachment\name());
        $ids = [];
        while( null != ($row = $stm->fetch()) ) {
            $ids[] = intVal($row[0]);
        }
        return $ids;
    }
    function get( $id ) {
        if( is_array( $id ) ) {
            $ids = \Data\ensureArrayOfInt( $id );
            return \Data\query(
                'SELECT * FROM' . \Data\Attachment\name() . 'WHERE id IN ' . $ids);
        }
        $row = \Data\fetch('SELECT * FROM' . \Data\Attachment\name() . 'WHERE id=?', $id );
        return ['id' => intVal($row['id']),
                'name' => $row['name'],
                'desc' => $row['desc'],
                'date' => $row['date'],
                'mime' => $row['mime']];
    }
    function add( $values ) {
        try {
            $args = [null];
            $sets = [];
            $fields = [];
            $allowedFields = ['name','desc','date','mime'];
            foreach( $values as $key => $val ) {
                if( !in_array( $key, $allowedFields ) )
                    throw new \Exception("[\\Data\\Attachment\\add()] Unknown field: $key!");
                $sets[] = "?";
                $args[] = $val;
                $fields[] = '`' . $key . '`';
            }
            $args[0] = 'INSERT INTO' . \Data\Attachment\name() . '(' . implode(',', $fields) . ')'
                     . 'VALUES(' . implode(',', $sets) . ')';
            return call_user_func_array( "\\Data\\exec", $args );
        }
        catch( \Exception $e ) {
            error_log("Exception in \\Data\\Attachment\\add( " . json_encode($values) . ")!");
            error_log("   error:  " . $e->getMessage());
            error_log("   values: " . json_encode( $values ));
            throw $e;
        }
    }
    function upd( $id, $values ) {
        try {
            $args = [null];
            $sets = [];
            $fields = ['name','desc','date','mime'];
            foreach( $values as $key => $val ) {
                if( !in_array( $key, $fields ) )
                    throw new \Exception("[\\Data\\Attachment\\upd()] Unknown field: $key!");
                $sets[] = "`$key`=?";
                $args[] = $val;
            }
            $args[0] = 'UPDATE' . \Data\Attachment\name() . 'SET '
                     . implode(',', $sets) . ' WHERE id=?';
            $args[] = $id;
            call_user_func_array( "\\Data\\query", $args );
        }
        catch( \Exception $e ) {
            error_log("Exception in \\Data\\Attachment\\upd( $id, values )!");
            error_log("   error:  " . $e->getMessage());
            error_log("   values: " . json_encode( $values ));
            throw $e;
        }
    }
    function del( $id ) {
        \Data\exec( 'DELETE FROM' . \Data\Attachment\name() . 'WHERE id=?', $id );
    }
    function getPatient( $idAttachment ) {
        $row = \Data\fetch(
            'SELECT `patient` FROM' . \Data\Attachment\name()
          . 'WHERE id=?', $idAttachment);
        return intVal($row[0]);
    }
    function linkPatient( $idAttachment, $idPatient ) {
        \Data\query(
            'UPDATE' . \Data\Attachment\name()
          . 'SET `patient`=? '
          . 'WHERE id=?', $idPatient, $idAttachment);
    }
}
namespace Data\Vaccin {
    function name() {
        global $DB;
        return $DB->table('vaccin');
    }
    function all() {
        $stm = \Data\query('SELECT id FROM' . \Data\Vaccin\name());
        $ids = [];
        while( null != ($row = $stm->fetch()) ) {
            $ids[] = intVal($row[0]);
        }
        return $ids;
    }
    function get( $id ) {
        if( is_array( $id ) ) {
            $ids = \Data\ensureArrayOfInt( $id );
            return \Data\query(
                'SELECT * FROM' . \Data\Vaccin\name() . 'WHERE id IN ' . $ids);
        }
        $row = \Data\fetch('SELECT * FROM' . \Data\Vaccin\name() . 'WHERE id=?', $id );
        return ['id' => intVal($row['id']),
                'key' => $row['key'],
                'date' => $row['date'],
                'lot' => $row['lot']];
    }
    function add( $values ) {
        try {
            $args = [null];
            $sets = [];
            $fields = [];
            $allowedFields = ['key','date','lot'];
            foreach( $values as $key => $val ) {
                if( !in_array( $key, $allowedFields ) )
                    throw new \Exception("[\\Data\\Vaccin\\add()] Unknown field: $key!");
                $sets[] = "?";
                $args[] = $val;
                $fields[] = '`' . $key . '`';
            }
            $args[0] = 'INSERT INTO' . \Data\Vaccin\name() . '(' . implode(',', $fields) . ')'
                     . 'VALUES(' . implode(',', $sets) . ')';
            return call_user_func_array( "\\Data\\exec", $args );
        }
        catch( \Exception $e ) {
            error_log("Exception in \\Data\\Vaccin\\add( " . json_encode($values) . ")!");
            error_log("   error:  " . $e->getMessage());
            error_log("   values: " . json_encode( $values ));
            throw $e;
        }
    }
    function upd( $id, $values ) {
        try {
            $args = [null];
            $sets = [];
            $fields = ['key','date','lot'];
            foreach( $values as $key => $val ) {
                if( !in_array( $key, $fields ) )
                    throw new \Exception("[\\Data\\Vaccin\\upd()] Unknown field: $key!");
                $sets[] = "`$key`=?";
                $args[] = $val;
            }
            $args[0] = 'UPDATE' . \Data\Vaccin\name() . 'SET '
                     . implode(',', $sets) . ' WHERE id=?';
            $args[] = $id;
            call_user_func_array( "\\Data\\query", $args );
        }
        catch( \Exception $e ) {
            error_log("Exception in \\Data\\Vaccin\\upd( $id, values )!");
            error_log("   error:  " . $e->getMessage());
            error_log("   values: " . json_encode( $values ));
            throw $e;
        }
    }
    function del( $id ) {
        \Data\exec( 'DELETE FROM' . \Data\Vaccin\name() . 'WHERE id=?', $id );
    }
    function getPatient( $idVaccin ) {
        $row = \Data\fetch(
            'SELECT `patient` FROM' . \Data\Vaccin\name()
          . 'WHERE id=?', $idVaccin);
        return intVal($row[0]);
    }
    function linkPatient( $idVaccin, $idPatient ) {
        \Data\query(
            'UPDATE' . \Data\Vaccin\name()
          . 'SET `patient`=? '
          . 'WHERE id=?', $idPatient, $idVaccin);
    }
}
?>