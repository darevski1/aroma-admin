<?php

namespace App\Services\Chatbot\Support;

/**
 * Normalizes visitor questions and stored FAQ/knowledge text so they can be
 * compared regardless of case, punctuation, whitespace or script: Macedonian
 * Cyrillic and Latin (with or without diacritics) map to the same plain
 * Latin form, so "Контакт", "kontakt" and "KONTAKT!" all become "kontakt".
 */
class TextNormalizer
{
    private const CYRILLIC = [
        'а' => 'a', 'б' => 'b', 'в' => 'v', 'г' => 'g', 'д' => 'd', 'ѓ' => 'gj', 'е' => 'e',
        'ж' => 'zh', 'з' => 'z', 'ѕ' => 'dz', 'и' => 'i', 'ј' => 'j', 'к' => 'k', 'л' => 'l',
        'љ' => 'lj', 'м' => 'm', 'н' => 'n', 'њ' => 'nj', 'о' => 'o', 'п' => 'p', 'р' => 'r',
        'с' => 's', 'т' => 't', 'ќ' => 'kj', 'у' => 'u', 'ф' => 'f', 'х' => 'h', 'ц' => 'c',
        'ч' => 'ch', 'џ' => 'dzh', 'ш' => 'sh',
        // Serbian / Russian letters visitors may type
        'ђ' => 'gj', 'ћ' => 'kj', 'й' => 'j', 'щ' => 'sh', 'ъ' => '', 'ы' => 'i', 'ь' => '',
        'э' => 'e', 'ю' => 'ju', 'я' => 'ja', 'ё' => 'e', 'ѐ' => 'e', 'ѝ' => 'i',
    ];

    // Latin transliteration variants of Macedonian letters → same form as above.
    private const LATIN = [
        'š' => 'sh', 'č' => 'ch', 'ž' => 'zh', 'ć' => 'kj', 'ḱ' => 'kj', 'ǵ' => 'gj', 'đ' => 'gj',
        'ǆ' => 'dzh', 'dž' => 'dzh', 'á' => 'a', 'à' => 'a', 'ä' => 'a', 'â' => 'a', 'é' => 'e',
        'è' => 'e', 'ë' => 'e', 'ê' => 'e', 'í' => 'i', 'ì' => 'i', 'ï' => 'i', 'î' => 'i',
        'ó' => 'o', 'ò' => 'o', 'ö' => 'o', 'ô' => 'o', 'ú' => 'u', 'ù' => 'u', 'ü' => 'u',
        'û' => 'u', 'ñ' => 'n', 'ç' => 'c', 'ß' => 'ss',
    ];

    private const STOPWORDS = [
        // English
        'a', 'an', 'the', 'is', 'are', 'am', 'was', 'were', 'be', 'do', 'does', 'did', 'you', 'your',
        'i', 'me', 'my', 'we', 'our', 'us', 'to', 'of', 'in', 'on', 'for', 'and', 'or', 'it', 'its',
        'what', 'how', 'can', 'could', 'would', 'with', 'have', 'has', 'at', 'about', 'please', 'there',
        'this', 'that', 'will', 'from', 'by', 'any', 'some', 'tell', 'hi', 'hello', 'which', 'who',
        // Macedonian (transliterated)
        'e', 'se', 'na', 'vo', 'za', 'od', 'so', 'da', 'li', 'ne', 'sto', 'shto', 'kako', 'koj', 'koja',
        'koi', 'koe', 'dali', 'ima', 'imate', 'mi', 'vie', 'nie', 'toa', 'ova', 'kon', 'do', 'pa',
        'ili', 'ke', 'kje', 'sum', 'si', 'sme', 'ste', 'gi', 'go', 'ja', 'im', 'ni', 'vi', 'mozhe',
        'mozham', 'mozhete', 'molam', 'zdravo', 've', 'nekoj', 'nekoja', 'neshto', 'kaj', 'kade',
    ];

    public static function normalize(?string $text): string
    {
        $text = mb_strtolower((string) $text, 'UTF-8');
        $text = strtr($text, self::CYRILLIC);
        $text = strtr($text, self::LATIN);
        // Anything that is not a plain letter/digit becomes a word separator.
        $text = preg_replace('/[^a-z0-9]+/', ' ', $text) ?? '';

        return trim(preg_replace('/\s+/', ' ', $text) ?? '');
    }

    /** Meaningful words (normalized, without stopwords). */
    public static function tokens(?string $text): array
    {
        $words = explode(' ', self::normalize($text));

        return array_values(array_unique(array_filter(
            $words,
            fn ($w) => $w !== '' && (strlen($w) > 1 || ctype_digit($w)) && ! in_array($w, self::STOPWORDS, true)
        )));
    }

    /**
     * Crude stem: the first 5 letters of longer words. Macedonian and English
     * inflections mostly change word endings, so "printer"/"printeri" and
     * "uslugi"/"usluga" share a stem.
     */
    public static function stem(string $token): string
    {
        return strlen($token) > 5 ? substr($token, 0, 5) : $token;
    }

    public static function stems(?string $text): array
    {
        return array_values(array_unique(array_map([self::class, 'stem'], self::tokens($text))));
    }

    /** 'mk' when the text is mostly Cyrillic, otherwise 'en'. */
    public static function detectLanguage(?string $text): string
    {
        $cyrillic = preg_match_all('/\p{Cyrillic}/u', (string) $text);
        $latin = preg_match_all('/[a-zA-Z]/', (string) $text);

        return $cyrillic > 0 && $cyrillic >= $latin ? 'mk' : 'en';
    }
}
