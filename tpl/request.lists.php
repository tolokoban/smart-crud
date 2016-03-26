    // Linked lists.
    $lists = Array({{LISTS}});
    $ids = Array();
    foreach( $rows as $row ) {
        $ids[] = $row['id'];
    }
    foreach ( $lists as $field => $link ) {
        $linkTable = $link[0];
        $linkField = $link[1];
        $values = Array();
        $stm = $DB->query( "SELECT id, `$linkField` FROM " 
                         . $DB->table( $linkTable )
                         . " WHERE `$linkField` IN ?", $ids );
        while( $row = $stm->fetch() ) {
            $idLink = '#' . $row[0];
            $id = '#' . $row[1];
            if( !array_key_exists( $id, $values ) ) {
                $values[$id] = Array();
            }
            $values[$id][] = $idLink;
        }
        $output['rows'][$field] = Array();
        foreach( $id as $ids ) {
            $output['rows'][$field][] = $values['#' . $id];
        }
    }
