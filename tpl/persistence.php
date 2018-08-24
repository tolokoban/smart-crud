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
            \call_user_func_array( Array($DB, "query"), func_get_args() );
        }
        catch( Exception $ex ) {
            throw new \Exception( $ex->getMessage(), SQS_ERROR );
        }
    }
    function fetch() {
        $stm = \call_user_func_array( "\\Data\\query", func_get_args() );
        $row = $stm->fetch();
        if( !$row ) throw new \Exception('[Data] There is no data!', NOT_FOUND);
        return $row;
    }
    function exec() {
        global $DB;
        \call_user_func_array( "\\Data\\query", func_get_args() );
        return $DB->lastId();
    }
}
