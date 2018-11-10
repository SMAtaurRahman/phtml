<?php
declare(strict_types=1);
namespace PHTML;

class Html
{

//    const WITHOUT_NOISE = 0b0001;
//    const WITH_NOISE = 0b0010;
    const WITHOUT_NOISE = 1;
    const WITH_NOISE = 2;

    protected $html;
    protected $options;

    public function __construct(string $html, $options = self::WITHOUT_NOISE)
    {
        $this->html = $html;
        $this->options = $options;
    }

    public function breakDown()
    {
        $html = htmlspecialchars_decode($this->html);

        if ($this->options & self::WITHOUT_NOISE) {
            $html = $this->cleanNoise($html);
        }

        $html = str_replace('>', '>' . PHP_EOL, str_replace(PHP_EOL, '', $html));
        $html = str_replace('<', PHP_EOL . '<', $html);
        $html = str_replace('</', PHP_EOL . '</', $html);


        return array_filter(array_map(function($value) {
                return trim($value);
            }, explode(PHP_EOL, $html)));
    }

    public function cleanNoise(string $html)
    {

        $html = preg_replace('#<script([\s\S]+?)</script>#', '', $html);
        $html = preg_replace('#<noscript([\s\S]+?)</noscript>#', '', $html);
        $html = preg_replace('#<style([\s\S]+?)</style>#', '', $html);
        $html = preg_replace('#<!--([\s\S]+?)-->#', '', $html);

        return $html;
    }
}
