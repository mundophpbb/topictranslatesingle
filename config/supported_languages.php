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
}
