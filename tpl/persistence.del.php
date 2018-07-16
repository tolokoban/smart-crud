    function del( $id ) {
        global $DB;
        {{NAME}}\exec(
            'DELETE FROM' . $DB->table('{{TABLE}}')
          . 'WHERE id=?', $id);
    }
