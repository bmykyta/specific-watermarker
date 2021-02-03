<?php


namespace App\Library\ImageSampler;

class ImageSampler
{
    private $img;

    protected int $percent = 5;
    protected int $steps = 10;

    public $width, $height;
    public int $sample_w = 0;
    public int $sample_h = 0;

    /**
     * ImageSampler constructor.
     *
     * @throws \InvalidArgumentException
     *
     * @param $filename
     */
    public function __construct($filename)
    {
        if(!$this->img = imagecreatefromstring(file_get_contents($filename))) {
            throw new \InvalidArgumentException('Invalid type of image.');
        }
        $this->width = imagesx($this->img);
        $this->height = imagesy($this->img);
    }


    /**
     * The percentage of pixels to be sampled
     *
     * @throws \InvalidArgumentException
     *
     * @param int $percent
     */
    public function set_percent(int $percent)
    {
        $percent = intval($percent);
        if(($percent < 1) || ($percent > 50)) {
            throw new \InvalidArgumentException("Your \$percent value needs to be between 1 and 50.");
        }
        $this->percent = $percent;
    }

    /**
     * The number of sections to be sampled and averaged will be $steps^2
     * @param int $steps
     */
    public function set_steps(int $steps)
    {
        $steps = intval($steps);
        if(($steps < 1) || ($steps > 50)) {
            throw new \InvalidArgumentException("Your \$steps value needs to be between 1 and 50.");
        }
        $this->steps = $steps;
    }

    public function init(): void
    {
        $this->sample_w = $this->width / $this->steps;
        $this->sample_h = $this->height / $this->steps;
    }

    /**
     * Returns RGB color values
     *
     * @param $x
     * @param $y
     * @return int[]
     */
    private function get_pixel_color($x, $y): array
    {
        $rgb = imagecolorat($this->img, $x, $y);
        $r = ($rgb >> 16) & 0xFF;
        $g = ($rgb >> 8) & 0xFF;
        $b = $rgb & 0xFF;
        return [$r, $g, $b];
    }

    /**
     * Sampling the image into a grid
     *
     * @return array
     */
    public function sample(): array
    {
        $this->init();
        if(($this->sample_w < 2) || ($this->sample_h < 2)) {
            throw new \InvalidArgumentException("Your sampling size is too small for this image - reduce the \$steps value.");
        }

        $sample_size = round($this->sample_w * $this->sample_h * $this->percent / 100);

        $rgb_array = [];
        for($i=0, $y=0; $i < $this->steps; $i++, $y += $this->sample_h) {
            $row_rgb_array = [];
            for($j=0, $x=0; $j < $this->steps; $j++, $x += $this->sample_w) {
                $total_r = $total_g = $total_b = 0;
                for($k=0; $k < $sample_size; $k++) {
                    $pixel_x = $x + rand(0, $this->sample_w-1);
                    $pixel_y = $y + rand(0, $this->sample_h-1);
                    list($r, $g, $b) = $this->get_pixel_color($pixel_x, $pixel_y);
                    $total_r += $r;
                    $total_g += $g;
                    $total_b += $b;
                }
                $avg_r = round($total_r/$sample_size);
                $avg_g = round($total_g/$sample_size);
                $avg_b = round($total_b/$sample_size);
                $row_rgb_array[] = [$avg_r, $avg_g, $avg_b];
            }
            $rgb_array[] = $row_rgb_array;
        }

        return $rgb_array;
    }
}
