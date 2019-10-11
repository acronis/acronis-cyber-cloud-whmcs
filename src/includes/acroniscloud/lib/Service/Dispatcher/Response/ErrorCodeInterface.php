<?php
/**
 * @Copyright © 2002-2019 Acronis International GmbH. All rights reserved
 */

namespace AcronisCloud\Service\Dispatcher\Response;

interface ErrorCodeInterface
{
    const ERROR_SERVICE_TEMPLATE_NOT_FOUND = 'service_template_not_found';
    const ERROR_SERVICE_TEMPLATE_IS_USED = 'service_template_is_used';
    const ERROR_SERVICE_TEMPLATE_NO_SERVER_ID = 'service_template_no_server_id';

    const ERROR_API_INTERNAL_SERVER_ERROR = 'api_internal_server_error';
    const ERROR_API_UNAUTHORIZED = 'api_unauthorized';
    const ERROR_API_FORBIDDEN = 'api_forbidden';
    const ERROR_API_NOT_FOUND = 'api_not_found';
    const ERROR_API_BAD_REQUEST = 'api_bad_request';
    const ERROR_API_CONFLICT = 'api_conflict';
    const ERROR_API_CONNECTION_PROBLEM = 'api_connection_problem';
}