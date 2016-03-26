<?php
include 'test.Issue.request.security';

/**
 * Service test.Issue.request
 *
 * @param $inputs
 * * __id__
 * * __page__
 * * __limit__
 *
 * @return
 * * __total__
 */
function execService( $inputs ) {
    // Check security access.
    if( !isGranted( $inputs ) ) return -1;

    global $DB;

    $fields = Array( 'title', 'content', 'date', 'status', 'type' );
    $sqlFields = implode( ',', array_map( "surroundWithQuotes", $fields ) );
    $sql = "SELECT id, $sqlFields FROM " . $DB->table( 'Issue' );
    $sqlWhere = '';
    if( array_key_exists( "id", $inputs ) ) {
        $id = $inputs['id'];
        if( is_array( $id ) ) {
            $sqlWhere .= " WHERE id IN (" . implode( ',', $id ) . ")";
        } else {
            $sqlWhere .= " WHERE id=$id";
        }
    }
    else if( array_key_exists( "filter", $inputs ) ) {

    }

    // Select count.
    $stm = $DB->query( "SELECT Count(*) FROM " . $DB->table( 'Issue' ) . $sqlWhere );
    $row = $stm->fetch();
    $output = Array( "total" => intVal( $row[0] ) );    

    // Loop over rows.
    $stm = $DB->query( $sql . $sqlWhere );
    $rows = Array();
    while( $row = $stm->fetch() ) {
        $data = Array();
        foreach( $fields as $field ) {
            $data[] = $row[$field];
        }
        $rows[$row['id']] = $data;
    }
    $output['rows'] = $rows;

    // Linked lists.
    if( count( $rows ) > 0 ) {
        $lists = Array('comments' => Array('Comment', 'issue'),
        'votes' => Array('Vote', 'issue'),
        'tags' => Array('Issue_Tag', 'issue'));

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

    // Pagination.
    $page = 0;
    if( array_key_exists( 'page', $inputs ) ) {
        $page = intVal( $inputs["page"] );
    }
    $limit = 20;
    if( array_key_exists( 'limit', $inputs ) ) {
        $limit = intVal( $inputs["limit"] );
    }
    $output['page'] = $page;
    $output['limit'] = $limit;

    return $output;
}


function surroundWithQuotes( $item ) {
    return "`$item`";
}

?>
