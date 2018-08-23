    function del( $id ) {
        global $DB;
        {{NAME}}\exec( 'DELETE FROM' . {{TABLE}} . 'WHERE id=?', $id );
    }
