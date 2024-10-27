<?php

namespace App\Libs;

class ValueUtil
{
    /**
     * Get value list from yml config file
     *
     * @param string $keys
     * @param array $options
     * @return array|string|null
     */
    public static function get($keys, $options = [])
    {
        return ConfigUtil::getValueList($keys, $options);
    }

    /**
     * Get value list contain japanese and english
     *
     * @param string $keys
     * @param array $options
     * @return array|null
     */
    public static function getList($keys, $options = [])
    {
        $options['getList'] = true;

        return ConfigUtil::getValueList($keys, $options);
    }

    /**
     * Convert from value into text in view
     *
     * @param string|int $value property value Ex: 1
     * @param string $listKey list defined in yml Ex: web.type
     * @return string|null text if exists else blank
     */
    public static function valueToText($value, $listKey)
    {
        // check params
        if (! isset($value) || ! isset($listKey)) {
            return null;
        }
        // get list options
        $list = ValueUtil::get($listKey);
        if (empty($list)) {
            $list = ValueUtil::getList($listKey);
        }
        if (is_array($list) && isset($list[$value])) {
            return $list[$value];
        }

        // can't get value
        return null;
    }

    /**
     * Get value from const (in Yml config file)
     *
     * @param string $keys
     * @return int|string|null
     */
    public static function constToValue($keys)
    {
        return ConfigUtil::getValue($keys);
    }

    /**
     * Get text from const (in Yml config file)
     *
     * @param string $keys
     * @return int|string|null
     */
    public static function constToText($keys)
    {
        return ConfigUtil::getValue($keys, true);
    }

    /**
     * Get value from test i
     *
     * @param string $searchText
     * @param string $keys
     * @return int|string|null
     */
    public static function textToValue($searchText, $keys)
    {
        $valueList = ValueUtil::get($keys);
        foreach ($valueList as $key => $text) {
            if ($searchText == $text) {
                return $key;
            }
        }

        return null;
    }

    /**
     * Remove decimal trailing zeros
     *
     * @param string $number
     * @param bool $addZeroWidth
     * @return string
     */
    public static function removeDecimalTrailingZeros($number, $addZeroWidth = false)
    {
        if (empty($number)) {
            return $number;
        }
        $integerPath = intval($number);
        $decimalPath = explode('.', $number);
        $decimalPath = isset($decimalPath[1]) ? $decimalPath[1] : '';
        $decimalPath = rtrim($decimalPath, '0');
        $decimalPath = ! empty($decimalPath) ? '.' . $decimalPath : '';

        return $integerPath . ($addZeroWidth ? '&zwj;' : '') . $decimalPath;
    }

    /**
     * Format String (YYYY/MM) to Date.YearMonth
     * Ex: 201906 to 2019/06 || 20190601 to 2019/06/01
     *
     * @param string $str
     * @param string $charParse
     * @param bool $incudeDay
     * @return string
     */
    public static function formatStringToDate($str, $charParse = '/', $incudeDay = false)
    {
        $result = '';
        if (! empty($str)) {
            $year = substr($str, 0, 4);
            $month = substr($str, 4, 2);
            $result = $year . $charParse . $month;
            if ($incudeDay == true) {
                $day = substr($str, 6, 2);
                $result = $result . $charParse . $day;
            }
        }

        return $result;
    }

    /**
     * Format Date.YearMonth to String (YYYY/MM)
     * Ex: 2019/06 to 201906
     *
     * @param string $date
     * @param string $charParse
     * @return string
     */
    public static function formatDateToString($date, $charParse = '/')
    {
        $result = '';
        if (! empty($date)) {
            $year = explode($charParse, $date)[0];
            $month = explode($charParse, $date)[1];
            $result = $year . $month;
        }

        return $result;
    }

    /**
     * Get deep link by screen_id
     *
     * @param string $screenId
     * @return string
     */
    public static function screenDeepLink($screenId)
    {
        $deepLinkBaseUrl = config('mimaki.deep_link_base_url');
        $deepLinkScreens = self::getList('common.deep_link_screens');
        $deepLinkParam = $deepLinkScreens[$screenId] ?? '';

        return $deepLinkBaseUrl . $deepLinkParam;
    }

    /**
     * Convert string to array
     *
     * @param string $string
     * @return array
     */
    public static function stringToArray($string)
    {
        if (empty($string)) {
            return [];
        }
        $replaceSearch = ['[', ']', ' ', '"', "'"];
        $string = str_replace($replaceSearch, '', $string);

        return explode(',', $string);
    }

    /**
     * Rounding unit_price
     *
     * @param int|float $unitPrice
     * @return int
     */
    public static function roundingUnitPrice($unitPrice)
    {
        return intval(floor($unitPrice));
    }

    /**
     * Convert <br> tag to break line char
     *
     * @param string $string
     * @return string
     */
    public static function br2nl($string)
    {
        return preg_replace('#<br\s*/?>#i', "\n", $string);
    }

    /**
     * Round a number to a specified number of decimal places.
     *
     * @param float $num The number to round.
     * @param int $decimalPlaces The number of decimal places to round to (default is 10).
     * @return float The rounded number.
     */
    public static function roundToNDecimalPlaces($num, $decimalPlaces = 10)
    {
        $multiplier = pow(10, $decimalPlaces);

        return round($num * $multiplier) / $multiplier;
    }

    /**
     * format number without rounding decimals.
     *
     * @param float $number The number to round.
     * @return float The rounded number.
     */
    public static function formatNumberWithCommas($number)
    {
        // Divide number into integer and decimal parts
        $parts = explode('.', $number);
        // Format the integer part with commas
        $formatted_integer = number_format($parts[0], 0, '.', ',');
        // If there is a decimal part, add it back
        $formatted_number = isset($parts[1]) ? $formatted_integer . '.' . $parts[1] : $formatted_integer;

        return $formatted_number;
    }
}
