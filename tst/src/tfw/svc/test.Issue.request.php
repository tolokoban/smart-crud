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

    $fields = Array( 'id', 'title', 'content', 'date', 'status', 'type' );
    $sqlFields = implode( ',', array_map( "surroundWithQuotes", $fields ) );
    $sql = "SELECT $sqlFields FROM " . $DB->table( 'Issue' );
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
    $output = Array( "total" => $row[0] );    

    // Loop over rows.
    $stm = $DB->query( $sql . $sqlWhere );
    $rows = Array();
    while( $row = $stm->fetch() ) {
        $data = Array( 'id' => $row['id'] );
        foreach( $fields as $field ) {
            $data[] = $row[$field];
        }
        $rows[] = $data;
    }
    $output['rows'] = $rows;

    // Linked lists.
    $lists = Array('comments' => Array('Comment', 'issue'),
        'votes' => Array('Vote', 'issue'),
        'tags' => Array('Tag', 'tag'));
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
    // Pagination.
    $page = 0;
    if( array_key_exists( 'page', $inputs ) ) {
        $page = intVal( $inputs["page"] );
    }
    $limit = 20;
    if( array_key_exists( 'limit', $input ) ) {
        $limit = intVal( $input["limit"] );
    }
    $output['page'] = $page;
    $output['limit'] = $limit;

    return $output;
}


function surroundWithQuotes( $item ) {
    return "'$item'";
}

?>
