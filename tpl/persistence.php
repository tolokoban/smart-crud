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
namespace {{NAME}} {
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
        if( !$row ) throw new Exception('[{{NAME}}] There is no data!', NOT_FOUND);
        return $row;
    }
    function exec() {
        global $DB;
        call_user_func_array( query, func_get_args() );
        return $DB->lastId;
    }
}
