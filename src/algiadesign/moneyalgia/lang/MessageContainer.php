<?php
namespace algiadesign\moneyalgia\lang;

use pocketmine\utils\TextFormat;

use algiadesign\moneyalgia\MoneyalgiaPlugin;

class MessageContainer
{
    private static $messages = [];

    public static function load(MoneyalgiaPlugin $plugin)
    {
        $resource = $plugin->getResource('message.json');
        self::$messages = json_decode(stream_get_contents($resource), true);

        fclose($resource);
    }

    public static function get(string $key, array $params = []): string
    {
        $keys = explode('.', $key);
        $msg = self::$messages;
        foreach ($keys as $k) {
            if (isset($msg[$k])) {
                $msg = $msg[$k];
            } else {
                $msg = $key;
                break;
            }
        }

        $search = [];
        $replace = [];
        for ($i = 0; $i < count($params); ++$i) {
            $search[] = '%' . ($i + 1);
            $replace[] = $params[$i];
        }

        return TextFormat::colorize(str_replace($search, $replace, $msg));
    }

    private function __construct()
    {
    }
}
