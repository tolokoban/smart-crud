<?php
$ROLE = "";

function isGranted( $inputs ) {
    // @TODO Implement a security test.
    error_log( '[{{NAME}}] This service is  not secure!' );
    error_log( 'Please edit the following file: "{{NAME}}.security".' );
    return True;
}
?>
