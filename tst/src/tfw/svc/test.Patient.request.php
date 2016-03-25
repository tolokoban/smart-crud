<?php
include 'test.Patient.request.security';

/**
 * Service test.Patient.request
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

    $fields = Array( 'id', 'name', 'age', 'gender' );
    $sqlFields = implode( ',', array_map( "surroundWithQuotes", $fields ) );
    $sql = "SELECT $sqlFields FROM " $DB->table( 'Patient' );
    $sqlWhere = ''
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
    $stm = "SELECT Count(*) FROM " . $DB->table( 'Patient' ) . $sqlWhere;
    $row = $stm->fetch();
    $output = Array( "total" => $row[0] );    

    // Loop over rows.
    $stm = $DB->query( $sql . $sqlWhere );
    $rows = Array();
    while( $row = $stm->fetch() ) {
        $data = Array();
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
