    function del( $id ) {
        {{NAME}}\exec( 'DELETE FROM' . {{TABLE}} . 'WHERE id=?', $id );
    }
