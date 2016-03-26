    // Linked lists.
    if( count( $rows ) > 0 ) {
        $lists = Array({{LISTS}});

        $ids = Array();
        foreach( $rows as $id => &$row ) {
            $ids[] = $id;
            foreach( $lists => $x ) {
                $row[] = Array();
            }
        }
        $ids = implode( ',', $ids );
        foreach ( $lists as $field => $link ) {
            $linkTable = $link[0];
            $linkField = $link[1];
            $sql = "SELECT id, `$linkField` FROM " 
                   . $DB->table( $linkTable )
                   . " WHERE `$linkField` IN ($ids)";
error_log( "sql = $sql   " );
            $stm = $DB->query( $sql );
            while( $row = $stm->fetch() ) {
                $idLink = intVal( $row[0] );
                $id = $row[1];
                $rows[$id][6][] = $idLink;
            }
        }
    }

