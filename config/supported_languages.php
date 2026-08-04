<?php
namespace mundophpbb\topictranslatesingle\config;

class supported_languages
{
    public static function get_popular()
    {
        return [
            'en'    => 'English',
            'pt'    => 'Portuguese',
            'es'    => 'Spanish',
            'fr'    => 'French',
            'de'    => 'German',
            'it'    => 'Italian',
            'ru'    => 'Russian',
            'ja'    => 'Japanese',
            'zh-CN' => 'Chinese (Simplified)',
            'zh-TW' => 'Chinese (Traditional)',
            'ar'    => 'Arabic',
            'hi'    => 'Hindi',
            'ko'    => 'Korean',
            'nl'    => 'Dutch',
            'pl'    => 'Polish',
            'sv'    => 'Swedish',
            'no'    => 'Norwegian',
            'da'    => 'Danish',
            'fi'    => 'Finnish',
            'tr'    => 'Turkish',
            'el'    => 'Greek',
        ];
    }

    public static function get_all()
    {
        return [
            'en' => 'English', 'ar' => 'Arabic', 'bg' => 'Bulgarian', 'zh-CN' => 'Chinese (Simplified)', 'zh-TW' => 'Chinese (Traditional)',
            'hr' => 'Croatian', 'cs' => 'Czech', 'da' => 'Danish', 'nl' => 'Dutch', 'fi' => 'Finnish', 'fr' => 'French', 'de' => 'German',
            'el' => 'Greek', 'hi' => 'Hindi', 'it' => 'Italian', 'ja' => 'Japanese', 'ko' => 'Korean', 'no' => 'Norwegian', 'pl' => 'Polish',
            'pt' => 'Portuguese', 'ro' => 'Romanian', 'ru' => 'Russian', 'es' => 'Spanish', 'sv' => 'Swedish', 'ca' => 'Catalan', 'tl' => 'Filipino',
            'iw' => 'Hebrew', 'id' => 'Indonesian', 'lv' => 'Latvian', 'lt' => 'Lithuanian', 'sr' => 'Serbian', 'sk' => 'Slovak', 'sl' => 'Slovenian',
            'uk' => 'Ukrainian', 'vi' => 'Vietnamese', 'sq' => 'Albanian', 'et' => 'Estonian', 'gl' => 'Galician', 'hu' => 'Hungarian', 'mt' => 'Maltese',
            'th' => 'Thai', 'tr' => 'Turkish', 'fa' => 'Persian', 'af' => 'Afrikaans', 'ms' => 'Malay', 'sw' => 'Swahili', 'ga' => 'Irish', 'cy' => 'Welsh',
            'be' => 'Belarusian', 'is' => 'Icelandic', 'mk' => 'Macedonian', 'yi' => 'Yiddish', 'hy' => 'Armenian', 'az' => 'Azerbaijani', 'eu' => 'Basque',
            'ka' => 'Georgian', 'ht' => 'Haitian Creole', 'ur' => 'Urdu', 'bn' => 'Bengali', 'bs' => 'Bosnian', 'ceb' => 'Cebuano', 'eo' => 'Esperanto',
            'gu' => 'Gujarati', 'ha' => 'Hausa', 'haw' => 'Hawaiian', 'hmn' => 'Hmong', 'ig' => 'Igbo', 'jw' => 'Javanese', 'kn' => 'Kannada',
            'kk' => 'Kazakh', 'km' => 'Khmer', 'ku' => 'Kurdish (Kurmanji)', 'ky' => 'Kyrgyz', 'lo' => 'Lao', 'la' => 'Latin', 'lb' => 'Luxembourgish',
            'mg' => 'Malagasy', 'ml' => 'Malayalam', 'mi' => 'Maori', 'mr' => 'Marathi', 'mn' => 'Mongolian', 'my' => 'Myanmar (Burmese)', 'ne' => 'Nepali',
            'ny' => 'Chichewa', 'pa' => 'Punjabi', 'ps' => 'Pashto', 'sm' => 'Samoan', 'gd' => 'Scottish Gaelic', 'sn' => 'Shona', 'sd' => 'Sindhi',
            'si' => 'Sinhala', 'so' => 'Somali', 'st' => 'Sesotho', 'su' => 'Sundanese', 'tg' => 'Tajik', 'ta' => 'Tamil', 'te' => 'Telugu',
            'uz' => 'Uzbek', 'xh' => 'Xhosa', 'yo' => 'Yoruba', 'zu' => 'Zulu', 'am' => 'Amharic', 'co' => 'Corsican', 'fy' => 'Frisian',
        ];
    }

    public static function get()
    {
        return self::get_all();
    }

    public static function get_flags()
    {
        return [
            'af' => 'za', 'sq' => 'al', 'am' => 'et', 'ar' => 'sa', 'hy' => 'am', 'az' => 'az', 'eu' => 'es',
            'be' => 'by', 'bn' => 'bd', 'bs' => 'ba', 'bg' => 'bg', 'ca' => 'es', 'ceb' => 'ph', 'ny' => 'mw',
            'zh-CN' => 'cn', 'zh-TW' => 'tw', 'co' => 'fr', 'hr' => 'hr', 'cs' => 'cz', 'da' => 'dk',
            'nl' => 'nl', 'en' => 'gb', 'eo' => 'un', 'et' => 'ee', 'tl' => 'ph', 'fi' => 'fi', 'fr' => 'fr',
            'fy' => 'nl', 'gl' => 'es', 'ka' => 'ge', 'de' => 'de', 'el' => 'gr', 'gu' => 'in', 'ht' => 'ht',
            'ha' => 'ng', 'haw' => 'us', 'iw' => 'il', 'hi' => 'in', 'hmn' => 'cn', 'hu' => 'hu', 'is' => 'is',
            'ig' => 'ng', 'id' => 'id', 'ga' => 'ie', 'it' => 'it', 'ja' => 'jp', 'jw' => 'id', 'kn' => 'in',
            'kk' => 'kz', 'km' => 'kh', 'ko' => 'kr', 'ku' => 'iq', 'ky' => 'kg', 'lo' => 'la', 'la' => 'va',
            'lv' => 'lv', 'lt' => 'lt', 'lb' => 'lu', 'mk' => 'mk', 'mg' => 'mg', 'ms' => 'my', 'ml' => 'in',
            'mt' => 'mt', 'mi' => 'nz', 'mr' => 'in', 'mn' => 'mn', 'my' => 'mm', 'ne' => 'np', 'no' => 'no',
            'ps' => 'af', 'fa' => 'ir', 'pl' => 'pl', 'pt' => 'pt', 'pa' => 'in', 'ro' => 'ro', 'ru' => 'ru',
            'sm' => 'ws', 'gd' => 'gb', 'sr' => 'rs', 'st' => 'ls', 'sn' => 'zw', 'sd' => 'pk', 'si' => 'lk',
            'sk' => 'sk', 'sl' => 'si', 'so' => 'so', 'es' => 'es', 'su' => 'id', 'sw' => 'tz', 'sv' => 'se',
            'tg' => 'tj', 'ta' => 'in', 'te' => 'in', 'th' => 'th', 'tr' => 'tr', 'uk' => 'ua', 'ur' => 'pk',
            'uz' => 'uz', 'vi' => 'vn', 'cy' => 'gb', 'xh' => 'za', 'yi' => 'il', 'yo' => 'ng', 'zu' => 'za',
        ];
    }
}
