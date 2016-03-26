<?php
include 'test.Comment.request.security';

/**
 * Service test.Comment.request
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

    $fields = Array( 'id', 'content', 'date' );
    $sqlFields = implode( ',', array_map( "surroundWithQuotes", $fields ) );
    $sql = "SELECT $sqlFields FROM " . $DB->table( 'Comment' );
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
    $stm = $DB->query( "SELECT Count(*) FROM " . $DB->table( 'Comment' ) . $sqlWhere );
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
