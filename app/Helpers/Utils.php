<?php


namespace App\Helpers;


class Utils {

    /**
     * checks if string is in json format
     * @param $string
     * @return bool
     */
    public static function isJson( $string ) {
        json_decode($string);
        return (json_last_error() == JSON_ERROR_NONE);
    }
    /**
     * used to add some numbers to id so ids won't be easily incremented
     * just hiding real user id value
     * @param $int
     * @return string
     */
    public static function convertInt( $int )
    {
        // a simple integer conversion to another integer to hide auto incremented IDs
        // can be modified if needed to have more complex conversions but be sure to modify recoverInt() as well
        return   ( intval( $int ) * 19 ) + 91214;
    }
    /**
     * Used to recover the real id value from convertInt
     *
     * @param $int
     * @return int
     */
    public static function recoverInt( $int )
    {
        $id = intval( $int ) - 91214;
        $id = $id / ( 19 );

        return intval( $id );
    }
    /**
     * unserialize a string with support to multibyte characters
     *
     * @param $string
     * @return mixed
     */
    public static function mb_unserialize( $string ) {
        if( !is_string( $string )){
            return null;
        }

        $string = preg_replace_callback(
            '!s:(\d+):"(.*?)";!s',
            function ($matches) {
                if ( isset( $matches[2] ) )
                    return 's:'.strlen($matches[2]).':"'.$matches[2].'";';
            },
            $string
        );

        $str = '';

        try{
            $str = unserialize( $string );
        }catch( \Exception $e ){

        }

        return $str;
    }

    /**
     * create a slug
     *
     * @param $text
     * @return mixed|string
     */
    public static function slugify( $text)
    {
        // replace non letter or digits by -
        $text = preg_replace('~[^\pL\d]+~u', '-', $text);

        // transliterate
        $text = iconv('utf-8', 'us-ascii//TRANSLIT', $text);

        // remove unwanted characters
        $text = preg_replace('~[^-\w]+~', '', $text);

        // trim
        $text = trim($text, '-');

        // remove duplicate -
        $text = preg_replace('~-+~', '-', $text);

        // lowercase
        $text = strtolower($text);

        if (empty($text)) {
            return 'n-a';
        }

        return $text;
    }

}