    // Linked lists.
    if( count( $rows ) > 0 ) {
        $indexes = Array( {{INDEXES}} );
        $lists = Array({{LISTS}});
        $ids = implode( ',', $ids );
        $k = 0;
        foreach( $lists as $field => $link ) {
            $linkTable = $link[0];
            $linkField = $link[1];
            $sql = "SELECT id, `$linkField` FROM " 
                   . $DB->table( $linkTable )
                   . " WHERE `$linkField` IN ($ids)";
error_log( $sql );
            $stm = $DB->query( $sql );
            $index = $indexes[$k];
error_log( "index = $index");
            $k++;
            while( $row = $stm->fetch() ) {
                $idLink = intVal( $row[0] );
                $id = $row[1];
error_log( "id=$id, idLink=$idLink" );
                $rows[$id][$index][] = $idLink;
error_log( json_encode( $rows[$id] ) );
            }
        }
    }

