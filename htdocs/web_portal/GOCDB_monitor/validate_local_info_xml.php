<?php
/**
 * 
 */
function libxml_display_error($error)
{
    $return = "<br/>\n";

    switch ($error->level) {
    case LIBXML_ERR_WARNING:
        $return .= "<b>Warning $error->code</b>: ";
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
 * 
 */
function libxml_display_errors() 
{
    $message = "";

    $errors = libxml_get_errors();
    foreach ($errors as $error) {
        $message .= libxml_display_error($error);
    }
    libxml_clear_errors();

    return $message;
}

function validate_local_info_xml ($path)
{
    // Enable user error handling
    libxml_use_internal_errors(true);

    $xml = new DOMDocument();

    $xml->load($path);

    $xsd = preg_replace( '/\.xml$/', '.xsd', $path);

    if (!$xml->schemaValidate($xsd)) {
        throw new Exception (libxml_display_errors());
    } else {
        return;
    }
}
?>