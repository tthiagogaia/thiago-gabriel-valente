<?php

namespace App\Helpers;

use JsonMachine\Items;
use JsonMachine\JsonDecoder\ExtJsonDecoder;
use Prewk\XmlStringStreamer;
use SimpleXMLElement;

class FileToArrayConverter
{
    public static function make(string $filePath): Items | array
    {
        if (self::isJson($filePath)) {
            return Items::fromFile(
                $filePath,
                ['decoder' => new ExtJsonDecoder(true)]
            );
        }

        $usersConverted = [];
        $streamer       = XmlStringStreamer::createStringWalkerParser($filePath);

        while ($node = $streamer->getNode()) {
            $usersConverted[] = self::nodeToArray(new SimpleXMLElement($node));
        }

        return $usersConverted;
    }

    protected static function nodeToArray(SimpleXMLElement $node): array
    {
        return [
            'account'       => self::sanitize($node->account),
            'address'       => self::sanitize($node->address),
            'checked'       => filter_var(self::sanitize($node->checked), FILTER_VALIDATE_BOOLEAN),
            'credit_card'   => self::sanitize($node->credit_card),
            'date_of_birth' => self::sanitize($node->date_of_birth),
            'description'   => self::sanitize($node->description),
            'email'         => self::sanitize($node->email),
            'interest'      => self::sanitize($node->interest),
            'name'          => self::sanitize($node->name),
        ];
    }

    protected static function sanitize(SimpleXMLElement $node): array | string | null
    {
        $sanitized = json_decode(json_encode($node), true);

        if ($node->hasChildren()) {
            return $sanitized;
        }

        return $sanitized[0] ?? null;
    }

    protected static function isJson(string $filePath): bool
    {
        $possibleJson = file_get_contents($filePath);
        json_decode($possibleJson);

        return json_last_error() === JSON_ERROR_NONE;
    }
}
