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
            $nbArgs = func_num_args();
            $args = func_get_args();
            $sql = $args[0];
            switch( $nbArgs ) {
                case 1: return $DB->query( $sql );
                case 2: return $DB->query( $sql, $args[1] );
                case 3: return $DB->query( $sql, $args[1], $args[2] );
                case 4: return $DB->query( $sql, $args[1], $args[2], $args[3] );
                case 5: return $DB->query( $sql, $args[1], $args[2], $args[3], $args[4] );
                case 6: return $DB->query( $sql, $args[1], $args[2], $args[3], $args[4], $args[5] );
                case 7: return $DB->query( $sql, $args[1], $args[2], $args[3], $args[4], $args[5], $args[6] );
                case 8: return $DB->query( $sql, $args[1], $args[2], $args[3], $args[4], $args[5], $args[6], $args[7] );
                case 9: return $DB->query( $sql, $args[1], $args[2], $args[3], $args[4], $args[5], $args[6], $args[7], $args[8] );
                case 10: return $DB->query( $sql, $args[1], $args[2], $args[3], $args[4], $args[5], $args[6], $args[7], $args[8], $args[9] );
                default: throw new \Exception( "Too many args: $nbArgs!" );
            }
        }
        catch( Exception $ex ) {
            throw new \Exception( $ex->getMessage(), SQS_ERROR );
        }
    }
    function fetch() {
        $stm = call_user_func_array( query, func_get_args() );
        $row = $stm->fetch();
        if( !$row ) throw new Exception('[Data] There is no data!', NOT_FOUND);
        return $row;
    }
    function exec() {
        global $DB;
        call_user_func_array( query, func_get_args() );
        return $DB->lastId;
    }
}
namespace Data\User {
    function name() {
        global $DB;
        return $DB->table('user');
    }
    function all() {
        global $DB;
        $stm = \Data\query('SELECT id FROM' . \Data\User\name());
        $ids = [];
        while( null != ($row = $stm->fetch()) ) {
            $ids[] = intVal($row[0]);
        }
        return $ids;
    }
    function get( $id ) {
        global $DB;
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
    function add( $fields ) {
        global $DB;
        return \Data\exec(
            'INSERT INTO' . \Data\User\name() . '(`dashboard`,`login`,`password`,`name`,`roles`,`enabled`,`creation`,`data`)'
          . 'VALUES(?,?,?,?,?,?,?,?)',
            $fields['dashboard'],
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
        \Data\exec(
            'UPDATE' . \Data\User\name()
          . 'SET `dashboard`=?,,`login`=?,,`password`=?,,`name`=?,,`roles`=?,,`enabled`=?,,`creation`=?,,`data`=? '
          . 'WHERE id=?',
            $id,
            $values['dashboard'],
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
        \Data\exec( 'DELETE FROM' . \Data\User\name() . 'WHERE id=?', $id );
    }
    function getOrganizations( $id ) {
        global $DB;
        $stm = \Data\query(
            'SELECT id FROM' . \Data\Organization\name()
          . 'WHERE `admins`=?', $id);
        $ids = [];
        while( null != ($row = $stm->fetch()) ) {
            $ids[] = intVal($row[0]);
        }
        return $ids;
    }
    function getCarecenters( $id ) {
        global $DB;
        $stm = \Data\query(
            'SELECT id FROM' . \Data\Carecenter\name()
          . 'WHERE `admins`=?', $id);
        $ids = [];
        while( null != ($row = $stm->fetch()) ) {
            $ids[] = intVal($row[0]);
        }
        return $ids;
    }
}
namespace Data\Organization {
    function name() {
        global $DB;
        return $DB->table('organization');
    }
    function all() {
        global $DB;
        $stm = \Data\query('SELECT id FROM' . \Data\Organization\name());
        $ids = [];
        while( null != ($row = $stm->fetch()) ) {
            $ids[] = intVal($row[0]);
        }
        return $ids;
    }
    function get( $id ) {
        global $DB;
        $row = \Data\fetch('SELECT * FROM' . \Data\Organization\name() . 'WHERE id=?', $id );
        return ['id' => intVal($row['id']),
                'name' => $row['name']];
    }
    function add( $fields ) {
        global $DB;
        return \Data\exec(
            'INSERT INTO' . \Data\Organization\name() . '(`name`)'
          . 'VALUES(?)',
            $fields['name']);
    }
    function upd( $id, $values ) {
        global $DB;
        \Data\exec(
            'UPDATE' . \Data\Organization\name()
          . 'SET `name`=? '
          . 'WHERE id=?',
            $id,
            $values['name']);
    }
    function del( $id ) {
        global $DB;
        \Data\exec( 'DELETE FROM' . \Data\Organization\name() . 'WHERE id=?', $id );
    }
    function getCarecenters( $id ) {
        global $DB;
        $stm = \Data\query(
            'SELECT id FROM' . \Data\Carecenter\name()
          . 'WHERE `organization`=?', $id);
        $ids = [];
        while( null != ($row = $stm->fetch()) ) {
            $ids[] = intVal($row[0]);
        }
        return $ids;
    }
    function getStructures( $id ) {
        global $DB;
        $stm = \Data\query(
            'SELECT id FROM' . \Data\Structure\name()
          . 'WHERE `organization`=?', $id);
        $ids = [];
        while( null != ($row = $stm->fetch()) ) {
            $ids[] = intVal($row[0]);
        }
        return $ids;
    }
    function getAdmins( $id ) {
        global $DB;
        $stm = \Data\query(
            'SELECT id FROM' . \Data\User\name()
          . 'WHERE `organizations`=?', $id);
        $ids = [];
        while( null != ($row = $stm->fetch()) ) {
            $ids[] = intVal($row[0]);
        }
        return $ids;
    }
}
namespace Data\Carecenter {
    function name() {
        global $DB;
        return $DB->table('carecenter');
    }
    function all() {
        global $DB;
        $stm = \Data\query('SELECT id FROM' . \Data\Carecenter\name());
        $ids = [];
        while( null != ($row = $stm->fetch()) ) {
            $ids[] = intVal($row[0]);
        }
        return $ids;
    }
    function get( $id ) {
        global $DB;
        $row = \Data\fetch('SELECT * FROM' . \Data\Carecenter\name() . 'WHERE id=?', $id );
        return ['id' => intVal($row['id']),
                'name' => $row['name']];
    }
    function add( $fields ) {
        global $DB;
        return \Data\exec(
            'INSERT INTO' . \Data\Carecenter\name() . '(`name`)'
          . 'VALUES(?)',
            $fields['name']);
    }
    function upd( $id, $values ) {
        global $DB;
        \Data\exec(
            'UPDATE' . \Data\Carecenter\name()
          . 'SET `name`=? '
          . 'WHERE id=?',
            $id,
            $values['name']);
    }
    function del( $id ) {
        global $DB;
        \Data\exec( 'DELETE FROM' . \Data\Carecenter\name() . 'WHERE id=?', $id );
    }
    function getOrganization( $id ) {
        global $DB;
        $row = \Data\fetch(
            'SELECT `organization` FROM' . \Data\Carecenter\name()
          . 'WHERE id=?', $id);
        return intVal($row[0]);
    }
    function getStructure( $id ) {
        global $DB;
        $row = \Data\fetch(
            'SELECT `structure` FROM' . \Data\Carecenter\name()
          . 'WHERE id=?', $id);
        return intVal($row[0]);
    }
    function getAdmins( $id ) {
        global $DB;
        $stm = \Data\query(
            'SELECT id FROM' . \Data\User\name()
          . 'WHERE `carecenters`=?', $id);
        $ids = [];
        while( null != ($row = $stm->fetch()) ) {
            $ids[] = intVal($row[0]);
        }
        return $ids;
    }
}
namespace Data\Structure {
    function name() {
        global $DB;
        return $DB->table('structure');
    }
    function all() {
        global $DB;
        $stm = \Data\query('SELECT id FROM' . \Data\Structure\name());
        $ids = [];
        while( null != ($row = $stm->fetch()) ) {
            $ids[] = intVal($row[0]);
        }
        return $ids;
    }
    function get( $id ) {
        global $DB;
        $row = \Data\fetch('SELECT * FROM' . \Data\Structure\name() . 'WHERE id=?', $id );
        return ['id' => intVal($row['id']),
                'name' => $row['name'],
                'exams' => $row['exams'],
                'vaccins' => $row['vaccins'],
                'patient' => $row['patient'],
                'forms' => $row['forms'],
                'types' => $row['types']];
    }
    function add( $fields ) {
        global $DB;
        return \Data\exec(
            'INSERT INTO' . \Data\Structure\name() . '(`name`,`exams`,`vaccins`,`patient`,`forms`,`types`)'
          . 'VALUES(?,?,?,?,?,?)',
            $fields['name'],
            $fields['exams'],
            $fields['vaccins'],
            $fields['patient'],
            $fields['forms'],
            $fields['types']);
    }
    function upd( $id, $values ) {
        global $DB;
        \Data\exec(
            'UPDATE' . \Data\Structure\name()
          . 'SET `name`=?,,`exams`=?,,`vaccins`=?,,`patient`=?,,`forms`=?,,`types`=? '
          . 'WHERE id=?',
            $id,
            $values['name'],
            $values['exams'],
            $values['vaccins'],
            $values['patient'],
            $values['forms'],
            $values['types']);
    }
    function del( $id ) {
        global $DB;
        \Data\exec( 'DELETE FROM' . \Data\Structure\name() . 'WHERE id=?', $id );
    }
    function getOrganization( $id ) {
        global $DB;
        $row = \Data\fetch(
            'SELECT `organization` FROM' . \Data\Structure\name()
          . 'WHERE id=?', $id);
        return intVal($row[0]);
    }
    function getCarecenters( $id ) {
        global $DB;
        $stm = \Data\query(
            'SELECT id FROM' . \Data\Carecenter\name()
          . 'WHERE `structure`=?', $id);
        $ids = [];
        while( null != ($row = $stm->fetch()) ) {
            $ids[] = intVal($row[0]);
        }
        return $ids;
    }
}
namespace Data\Patient {
    function name() {
        global $DB;
        return $DB->table('patient');
    }
    function all() {
        global $DB;
        $stm = \Data\query('SELECT id FROM' . \Data\Patient\name());
        $ids = [];
        while( null != ($row = $stm->fetch()) ) {
            $ids[] = intVal($row[0]);
        }
        return $ids;
    }
    function get( $id ) {
        global $DB;
        $row = \Data\fetch('SELECT * FROM' . \Data\Patient\name() . 'WHERE id=?', $id );
        return ['id' => intVal($row['id']),
                'key' => $row['key']];
    }
    function add( $fields ) {
        global $DB;
        return \Data\exec(
            'INSERT INTO' . \Data\Patient\name() . '(`key`)'
          . 'VALUES(?)',
            $fields['key']);
    }
    function upd( $id, $values ) {
        global $DB;
        \Data\exec(
            'UPDATE' . \Data\Patient\name()
          . 'SET `key`=? '
          . 'WHERE id=?',
            $id,
            $values['key']);
    }
    function del( $id ) {
        global $DB;
        \Data\exec( 'DELETE FROM' . \Data\Patient\name() . 'WHERE id=?', $id );
    }
    function getAdmissions( $id ) {
        global $DB;
        $stm = \Data\query(
            'SELECT id FROM' . \Data\Admission\name()
          . 'WHERE `patient`=?', $id);
        $ids = [];
        while( null != ($row = $stm->fetch()) ) {
            $ids[] = intVal($row[0]);
        }
        return $ids;
    }
    function getAttachments( $id ) {
        global $DB;
        $stm = \Data\query(
            'SELECT id FROM' . \Data\Attachment\name()
          . 'WHERE `patient`=?', $id);
        $ids = [];
        while( null != ($row = $stm->fetch()) ) {
            $ids[] = intVal($row[0]);
        }
        return $ids;
    }
    function getVaccins( $id ) {
        global $DB;
        $stm = \Data\query(
            'SELECT id FROM' . \Data\Vaccin\name()
          . 'WHERE `patient`=?', $id);
        $ids = [];
        while( null != ($row = $stm->fetch()) ) {
            $ids[] = intVal($row[0]);
        }
        return $ids;
    }
}
namespace Data\PatientField {
    function name() {
        global $DB;
        return $DB->table('patientField');
    }
    function all() {
        global $DB;
        $stm = \Data\query('SELECT id FROM' . \Data\PatientField\name());
        $ids = [];
        while( null != ($row = $stm->fetch()) ) {
            $ids[] = intVal($row[0]);
        }
        return $ids;
    }
    function get( $id ) {
        global $DB;
        $row = \Data\fetch('SELECT * FROM' . \Data\PatientField\name() . 'WHERE id=?', $id );
        return ['id' => intVal($row['id']),
                'key' => $row['key'],
                'value' => $row['value']];
    }
    function add( $fields ) {
        global $DB;
        return \Data\exec(
            'INSERT INTO' . \Data\PatientField\name() . '(`key`,`value`)'
          . 'VALUES(?,?)',
            $fields['key'],
            $fields['value']);
    }
    function upd( $id, $values ) {
        global $DB;
        \Data\exec(
            'UPDATE' . \Data\PatientField\name()
          . 'SET `key`=?,,`value`=? '
          . 'WHERE id=?',
            $id,
            $values['key'],
            $values['value']);
    }
    function del( $id ) {
        global $DB;
        \Data\exec( 'DELETE FROM' . \Data\PatientField\name() . 'WHERE id=?', $id );
    }
}
namespace Data\File {
    function name() {
        global $DB;
        return $DB->table('file');
    }
    function all() {
        global $DB;
        $stm = \Data\query('SELECT id FROM' . \Data\File\name());
        $ids = [];
        while( null != ($row = $stm->fetch()) ) {
            $ids[] = intVal($row[0]);
        }
        return $ids;
    }
    function get( $id ) {
        global $DB;
        $row = \Data\fetch('SELECT * FROM' . \Data\File\name() . 'WHERE id=?', $id );
        return ['id' => intVal($row['id']),
                'name' => $row['name'],
                'hash' => $row['hash'],
                'mime' => $row['mime'],
                'size' => $row['size']];
    }
    function add( $fields ) {
        global $DB;
        return \Data\exec(
            'INSERT INTO' . \Data\File\name() . '(`name`,`hash`,`mime`,`size`)'
          . 'VALUES(?,?,?,?)',
            $fields['name'],
            $fields['hash'],
            $fields['mime'],
            $fields['size']);
    }
    function upd( $id, $values ) {
        global $DB;
        \Data\exec(
            'UPDATE' . \Data\File\name()
          . 'SET `name`=?,,`hash`=?,,`mime`=?,,`size`=? '
          . 'WHERE id=?',
            $id,
            $values['name'],
            $values['hash'],
            $values['mime'],
            $values['size']);
    }
    function del( $id ) {
        global $DB;
        \Data\exec( 'DELETE FROM' . \Data\File\name() . 'WHERE id=?', $id );
    }
}
namespace Data\Admission {
    function name() {
        global $DB;
        return $DB->table('admission');
    }
    function all() {
        global $DB;
        $stm = \Data\query('SELECT id FROM' . \Data\Admission\name());
        $ids = [];
        while( null != ($row = $stm->fetch()) ) {
            $ids[] = intVal($row[0]);
        }
        return $ids;
    }
    function get( $id ) {
        global $DB;
        $row = \Data\fetch('SELECT * FROM' . \Data\Admission\name() . 'WHERE id=?', $id );
        return ['id' => intVal($row['id']),
                'enter' => $row['enter'],
                'exit' => $row['exit']];
    }
    function add( $fields ) {
        global $DB;
        return \Data\exec(
            'INSERT INTO' . \Data\Admission\name() . '(`enter`,`exit`)'
          . 'VALUES(?,?)',
            $fields['enter'],
            $fields['exit']);
    }
    function upd( $id, $values ) {
        global $DB;
        \Data\exec(
            'UPDATE' . \Data\Admission\name()
          . 'SET `enter`=?,,`exit`=? '
          . 'WHERE id=?',
            $id,
            $values['enter'],
            $values['exit']);
    }
    function del( $id ) {
        global $DB;
        \Data\exec( 'DELETE FROM' . \Data\Admission\name() . 'WHERE id=?', $id );
    }
    function getPatient( $id ) {
        global $DB;
        $row = \Data\fetch(
            'SELECT `patient` FROM' . \Data\Admission\name()
          . 'WHERE id=?', $id);
        return intVal($row[0]);
    }
    function getConsultations( $id ) {
        global $DB;
        $stm = \Data\query(
            'SELECT id FROM' . \Data\Consultation\name()
          . 'WHERE `admission`=?', $id);
        $ids = [];
        while( null != ($row = $stm->fetch()) ) {
            $ids[] = intVal($row[0]);
        }
        return $ids;
    }
}
namespace Data\Consultation {
    function name() {
        global $DB;
        return $DB->table('consultation');
    }
    function all() {
        global $DB;
        $stm = \Data\query('SELECT id FROM' . \Data\Consultation\name());
        $ids = [];
        while( null != ($row = $stm->fetch()) ) {
            $ids[] = intVal($row[0]);
        }
        return $ids;
    }
    function get( $id ) {
        global $DB;
        $row = \Data\fetch('SELECT * FROM' . \Data\Consultation\name() . 'WHERE id=?', $id );
        return ['id' => intVal($row['id']),
                'date' => $row['date']];
    }
    function add( $fields ) {
        global $DB;
        return \Data\exec(
            'INSERT INTO' . \Data\Consultation\name() . '(`date`)'
          . 'VALUES(?)',
            $fields['date']);
    }
    function upd( $id, $values ) {
        global $DB;
        \Data\exec(
            'UPDATE' . \Data\Consultation\name()
          . 'SET `date`=? '
          . 'WHERE id=?',
            $id,
            $values['date']);
    }
    function del( $id ) {
        global $DB;
        \Data\exec( 'DELETE FROM' . \Data\Consultation\name() . 'WHERE id=?', $id );
    }
    function getAdmission( $id ) {
        global $DB;
        $row = \Data\fetch(
            'SELECT `admission` FROM' . \Data\Consultation\name()
          . 'WHERE id=?', $id);
        return intVal($row[0]);
    }
    function getDatas( $id ) {
        global $DB;
        $stm = \Data\query(
            'SELECT id FROM' . \Data\Data\name()
          . 'WHERE `consultation`=?', $id);
        $ids = [];
        while( null != ($row = $stm->fetch()) ) {
            $ids[] = intVal($row[0]);
        }
        return $ids;
    }
}
namespace Data\Data {
    function name() {
        global $DB;
        return $DB->table('data');
    }
    function all() {
        global $DB;
        $stm = \Data\query('SELECT id FROM' . \Data\Data\name());
        $ids = [];
        while( null != ($row = $stm->fetch()) ) {
            $ids[] = intVal($row[0]);
        }
        return $ids;
    }
    function get( $id ) {
        global $DB;
        $row = \Data\fetch('SELECT * FROM' . \Data\Data\name() . 'WHERE id=?', $id );
        return ['id' => intVal($row['id']),
                'key' => $row['key'],
                'value' => $row['value']];
    }
    function add( $fields ) {
        global $DB;
        return \Data\exec(
            'INSERT INTO' . \Data\Data\name() . '(`key`,`value`)'
          . 'VALUES(?,?)',
            $fields['key'],
            $fields['value']);
    }
    function upd( $id, $values ) {
        global $DB;
        \Data\exec(
            'UPDATE' . \Data\Data\name()
          . 'SET `key`=?,,`value`=? '
          . 'WHERE id=?',
            $id,
            $values['key'],
            $values['value']);
    }
    function del( $id ) {
        global $DB;
        \Data\exec( 'DELETE FROM' . \Data\Data\name() . 'WHERE id=?', $id );
    }
    function getConsultation( $id ) {
        global $DB;
        $row = \Data\fetch(
            'SELECT `consultation` FROM' . \Data\Data\name()
          . 'WHERE id=?', $id);
        return intVal($row[0]);
    }
}
namespace Data\Shapshot {
    function name() {
        global $DB;
        return $DB->table('shapshot');
    }
    function all() {
        global $DB;
        $stm = \Data\query('SELECT id FROM' . \Data\Shapshot\name());
        $ids = [];
        while( null != ($row = $stm->fetch()) ) {
            $ids[] = intVal($row[0]);
        }
        return $ids;
    }
    function get( $id ) {
        global $DB;
        $row = \Data\fetch('SELECT * FROM' . \Data\Shapshot\name() . 'WHERE id=?', $id );
        return ['id' => intVal($row['id']),
                'key' => $row['key'],
                'value' => $row['value']];
    }
    function add( $fields ) {
        global $DB;
        return \Data\exec(
            'INSERT INTO' . \Data\Shapshot\name() . '(`key`,`value`)'
          . 'VALUES(?,?)',
            $fields['key'],
            $fields['value']);
    }
    function upd( $id, $values ) {
        global $DB;
        \Data\exec(
            'UPDATE' . \Data\Shapshot\name()
          . 'SET `key`=?,,`value`=? '
          . 'WHERE id=?',
            $id,
            $values['key'],
            $values['value']);
    }
    function del( $id ) {
        global $DB;
        \Data\exec( 'DELETE FROM' . \Data\Shapshot\name() . 'WHERE id=?', $id );
    }
}
namespace Data\Attachment {
    function name() {
        global $DB;
        return $DB->table('attachment');
    }
    function all() {
        global $DB;
        $stm = \Data\query('SELECT id FROM' . \Data\Attachment\name());
        $ids = [];
        while( null != ($row = $stm->fetch()) ) {
            $ids[] = intVal($row[0]);
        }
        return $ids;
    }
    function get( $id ) {
        global $DB;
        $row = \Data\fetch('SELECT * FROM' . \Data\Attachment\name() . 'WHERE id=?', $id );
        return ['id' => intVal($row['id']),
                'name' => $row['name'],
                'desc' => $row['desc'],
                'date' => $row['date'],
                'mime' => $row['mime']];
    }
    function add( $fields ) {
        global $DB;
        return \Data\exec(
            'INSERT INTO' . \Data\Attachment\name() . '(`name`,`desc`,`date`,`mime`)'
          . 'VALUES(?,?,?,?)',
            $fields['name'],
            $fields['desc'],
            $fields['date'],
            $fields['mime']);
    }
    function upd( $id, $values ) {
        global $DB;
        \Data\exec(
            'UPDATE' . \Data\Attachment\name()
          . 'SET `name`=?,,`desc`=?,,`date`=?,,`mime`=? '
          . 'WHERE id=?',
            $id,
            $values['name'],
            $values['desc'],
            $values['date'],
            $values['mime']);
    }
    function del( $id ) {
        global $DB;
        \Data\exec( 'DELETE FROM' . \Data\Attachment\name() . 'WHERE id=?', $id );
    }
    function getPatient( $id ) {
        global $DB;
        $row = \Data\fetch(
            'SELECT `patient` FROM' . \Data\Attachment\name()
          . 'WHERE id=?', $id);
        return intVal($row[0]);
    }
}
namespace Data\Vaccin {
    function name() {
        global $DB;
        return $DB->table('vaccin');
    }
    function all() {
        global $DB;
        $stm = \Data\query('SELECT id FROM' . \Data\Vaccin\name());
        $ids = [];
        while( null != ($row = $stm->fetch()) ) {
            $ids[] = intVal($row[0]);
        }
        return $ids;
    }
    function get( $id ) {
        global $DB;
        $row = \Data\fetch('SELECT * FROM' . \Data\Vaccin\name() . 'WHERE id=?', $id );
        return ['id' => intVal($row['id']),
                'key' => $row['key'],
                'date' => $row['date'],
                'lot' => $row['lot']];
    }
    function add( $fields ) {
        global $DB;
        return \Data\exec(
            'INSERT INTO' . \Data\Vaccin\name() . '(`key`,`date`,`lot`)'
          . 'VALUES(?,?,?)',
            $fields['key'],
            $fields['date'],
            $fields['lot']);
    }
    function upd( $id, $values ) {
        global $DB;
        \Data\exec(
            'UPDATE' . \Data\Vaccin\name()
          . 'SET `key`=?,,`date`=?,,`lot`=? '
          . 'WHERE id=?',
            $id,
            $values['key'],
            $values['date'],
            $values['lot']);
    }
    function del( $id ) {
        global $DB;
        \Data\exec( 'DELETE FROM' . \Data\Vaccin\name() . 'WHERE id=?', $id );
    }
    function getPatient( $id ) {
        global $DB;
        $row = \Data\fetch(
            'SELECT `patient` FROM' . \Data\Vaccin\name()
          . 'WHERE id=?', $id);
        return intVal($row[0]);
    }
}
?>