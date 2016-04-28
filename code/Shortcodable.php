<?php
/**
 * Shortcodable
 * Manages shortcodable configuration and register shortcodable objects
 *
 * @author shea@livesource.co.nz
 **/
class Shortcodable extends SS_Object
{
    private static $shortcodable_classes = array();

    public static function register_classes($classes)
    {
        if (is_array($classes) && count($classes)) {
            foreach ($classes as $class) {
                self::register_class($class);
            }
        }
    }

    public static function register_class($class)
    {
        if (class_exists($class)) {
            if (!singleton($class)->hasMethod('parse_shortcode')) {
                user_error("Failed to register \"$class\" with shortcodable. $class must have the method parse_shortcode(). See /shortcodable/README.md", E_USER_ERROR);
            }
            $kw = $class;
            if (singleton($class)->hasMethod('getShortcodeKeyword')) {
                $kw = singleton($class)->getShortcodeKeyword();
            }
            ShortcodeParser::get('default')->register($kw, array($class, 'parse_shortcode'));
            singleton('ShortcodableParser')->register($kw);
        }
    }

    public static function get_shortcodable_classes()
    {
        return Config::inst()->get('Shortcodable', 'shortcodable_classes');
    }

    public static function get_shortcodable_classes_fordropdown()
    {
        $classList = self::get_shortcodable_classes();
        $classes = array();
        foreach ($classList as $class) {
            $classes[$class] = singleton($class)->hasMethod('getShortcodeNiceName') ? singleton($class)->getShortcodeNiceName() : (singleton($class)->hasMethod('singular_name') ? singleton($class)->singular_name() : $class);
        }
        return $classes;
    }

    public static function get_shortcodable_classes_with_placeholders()
    {
        $classes = array();
        foreach (self::get_shortcodable_classes() as $class) {
            if (singleton($class)->hasMethod('getShortcodePlaceHolder')) {
                $classes[] = $class;
            }
        }
        return $classes;
    }
}
