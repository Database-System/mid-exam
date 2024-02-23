<?php

namespace Exam\Utils;

if (!session_id()) session_start();
class Utils
{
    public static function StrriposFunction(string $val = "", string $type = ""): string
    {
        switch ($type) {
            case "1":
                $fileName = $val;
                if (false !== $pos = strripos($fileName, '.')) $fileName = substr($fileName, 0, $pos);
                break;

            case "2":
                $fileName = $val;
                if (false !== $pos = strripos($fileName, '.')) $fileName = substr($fileName, $pos + 1, strlen($fileName));
                break;
        }
        return $fileName;
    }
    public static function OutputFiles(string $path): array|bool
    {
        if (file_exists($path) && is_dir($path)) {
            $result = scandir($path);
            $files = array_diff($result, array('.', '..'));
            return $files;
        } else die("Error0");
    }
    public static function GetRoot()
    {
        $q = explode('/', $_SERVER['PHP_SELF']);
        array_pop($q);
        $q = implode("/", $q);
        $_SESSION['WEB_ROOT'] = $q;
        if (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on') $link = "https";
        else $link = "http";
        $link .= "://" . $_SERVER['SERVER_NAME'];
        $_SESSION['WEB_ROOT_URL'] = ($link);
    }
    public static function isLogin()
    {
        if (!isset($_SESSION['userID'])) {
            header('Location: /login');
            exit;
        }
    }
}
