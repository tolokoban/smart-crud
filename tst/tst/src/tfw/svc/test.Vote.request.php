<?php
include 'test.Vote.request.security';

/**
 * Service test.Vote.request
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

    $fields = Array( 'user', 'issue', 'vote' );
    $sqlFields = implode( ',', array_map( "surroundWithQuotes", $fields ) );
    $sql = "SELECT id, $sqlFields FROM " . $DB->table( 'Vote' );
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
    $stm = $DB->query( "SELECT Count(*) FROM " . $DB->table( 'Vote' ) . $sqlWhere );
    $row = $stm->fetch();
    $output = Array( "total" => intVal( $row[0] ) );    

    // Loop over rows.
    $stm = $DB->query( $sql . $sqlWhere );
    $rows = Array();
    $ids = Array();
    while( $row = $stm->fetch() ) {
        $id = $row['id'];
        $ids[] = $id;
        $rows[$id] = Array(
            intVal( $row['user'] ),
            intVal( $row['issue'] ),
            intVal( $row['vote'] ));
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

    $output['rows'] = $rows;

    return $output;
}


function surroundWithQuotes( $item ) {
    return "`$item`";
}

?>
