<?php
/**
 * Check that local_info.xml matches its .xsd schema
 * definition.
 */

 /**
 * Return a formatted error message given an input error object
 */
function libxml_display_error($error)
{
    $return = "<br/>\n";

    switch ($error->level) {
    case LIBXML_ERR_WARNING:$return .= "<b>Warning $error->code</b>: ";
        break;
    case LIBXML_ERR_ERROR:$return .= "<b>Error $error->code</b>: ";
        break;
    case LIBXML_ERR_FATAL:$return .= "<b>Fatal Error $error->code</b>: ";
        break;
    }
    $return .= trim($error->message);

    if ($error->file) {
        $return .= " in <b>$error->file</b>";
    }
    $return .= " on line <b>$error->line</b>\n";
    return $return;
}
/**
 * Loop over all errors printing a message for each
 */
function libxml_display_errors()
{
    $errors = libxml_get_errors();
    foreach ($errors as $error) {
        print libxml_display_error($error);
    }
    libxml_clear_errors();
}

 // Enable user error handling
libxml_use_internal_errors(true);

$xml = new DOMDocument();

$xml->load('../../config/local_info.xml');

if (!$xml->schemaValidate('../../config/local_info.xsd')) {
    print '<p>Errors found.</p>';
    libxml_display_errors();
} else {
    print '<p>Validated. No errors found.<p/>';
}
?>
