<?php

namespace App\Constants;

/**
 * Property Management Constants
 *
 * This class defines constants used throughout the property management system.
 */
class PropertyConstants
{
    /**
     * ERMS ID Configuration
     */
    public const ERMS_ID_PREFIX = 'ERMS';

    public const ERMS_ID_LENGTH = 6;

    public const ERMS_ID_DIGIT_MIN = 0;

    public const ERMS_ID_DIGIT_MAX = 9;

    /**
     * Account Number Configuration
     */
    public const ACCOUNT_NUMBER_PREFIX = 'ERMS/C-000';

    public const ACCOUNT_NUMBER_LENGTH = 7;

    public const ACCOUNT_NUMBER_MIN = 1;

    public const ACCOUNT_NUMBER_MAX = 9999999;

    public const ACCOUNT_SUFFIX_LENGTH = 5;

    public const ACCOUNT_SUFFIX_CHARACTERS = '0123456789abcdefghijklmnopqrstuvwxyz';

    /**
     * Building ID Configuration
     */
    public const BUILDING_ID_MIN = 0;

    public const BUILDING_ID_MAX = 9999999;

    public const BUILDING_ID_LENGTH = 7;

    /**
     * Coordinate Validation
     */
    public const LATITUDE_MIN = -90;

    public const LATITUDE_MAX = 90;

    public const LONGITUDE_MIN = -180;

    public const LONGITUDE_MAX = 180;

    public const COORDINATE_PRECISION = 8;

    /**
     * Map Configuration
     */
    public const MAP_DEFAULT_ZOOM = 17;

    /**
     * Rate Configuration
     */
    public const DEFAULT_RATE_JSON = '{"basic": true, "sanit": true}';

    public const DEFAULT_VALIDATION_ID = 'sucfl';

    public const DEFAULT_GENERATE_INV = '1';

    public const DEFAULT_REMARKS_PREFIX = '[PRR] - ';

    public const DEFAULT_RATEABLE_VALUE = '0.00';

    /**
     * Maximum Retry Attempts for ID Generation
     * Prevents infinite loops in case of database issues
     */
    public const MAX_ID_GENERATION_ATTEMPTS = 1000;
}
