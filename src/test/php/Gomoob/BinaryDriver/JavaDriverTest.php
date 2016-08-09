<?php

/**
 * Copyright 2016 SARL GOMOOB. All rights reserved.
 */
namespace Gomoob\BinaryDriver;

use Monolog\Logger;

use PHPUnit\Framework\TestCase;
use Monolog\Handler\StreamHandler;
use Alchemy\BinaryDriver\Exception\ExecutionFailureException;
use Alchemy\BinaryDriver\Listeners\DebugListener;

/**
 * Test case used to test the {@link JavaDriver} class.
 *
 * @author Baptiste GAILLARD (baptiste.gaillard@gomoob.com)
 */
class JavaDriverTest extends TestCase
{
    /**
     * Test method for `create($logger, $configuration)`.
     */
    public function testCreate()
    {
        $logger = new Logger('JavaDriver');
        $logger->pushHandler(new StreamHandler('php://output'));
        
        $listener = new DebugListener();

        // Calls the method to be tested
        $javaDriver = JavaDriver::create($logger);
        $javaDriver->listen($listener);
        
        $javaDriver->on('debug', function ($line) {
            echo $line;
        });
        
        // TODO: To be deleted
        $output = $javaDriver->command(['-version']);
        var_dump($output);

        // Makes a simple call to ensure it works
        try {
            $output = $javaDriver->command(
                [
                    '-classpath',
                    str_replace('\\', '/', MAIN_RESOURCES_DIRECTORY . '/jars/*'),
                    'com.drew.imaging.ImageMetadataReader',
                    realpath(TEST_RESOURCES_DIRECTORY . '/elephant.jpg')
                ]
            );
            
            var_dump($output);
        } catch (ExecutionFailureException $efex) {
            $curEx = $efex;
            
            while ($curEx) {
                var_dump($curEx->getMessage());
                $curEx = $efex->getPrevious();
            }
        }
        $this->assertContains('[JPEG] Compression Type = Baseline', $output);
        $this->assertContains('[JPEG] Data Precision = 8 bits', $output);
        $this->assertContains('[JPEG] Image Height = 1280 pixels', $output);
        $this->assertContains('[JPEG] Image Width = 1920 pixels', $output);
        $this->assertContains('[JPEG] Number of Components = 3', $output);
        $this->assertContains(
            '[JPEG] Component 1 = Y component: Quantization table 0, Sampling factors 2 horiz/2 vert',
            $output
        );
        $this->assertContains(
            '[JPEG] Component 2 = Cb component: Quantization table 1, Sampling factors 1 horiz/1 vert',
            $output
        );
        $this->assertContains(
            '[JPEG] Component 3 = Cr component: Quantization table 1, Sampling factors 1 horiz/1 vert',
            $output
        );
        $this->assertContains('[JFIF] Version = 1.1', $output);
        $this->assertContains('[JFIF] Resolution Units = inch', $output);
        $this->assertContains('[JFIF] X Resolution = 300 dots', $output);
        $this->assertContains('[JFIF] Y Resolution = 300 dots', $output);
        $this->assertContains('[JFIF] Thumbnail Width Pixels = 0', $output);
        $this->assertContains('[JFIF] Thumbnail Height Pixels = 0', $output);
        $this->assertContains('[Exif IFD0] Make = Canon', $output);
        $this->assertContains('[Exif IFD0] Model = Canon EOS 70D', $output);
        $this->assertContains('[Exif IFD0] Exposure Time = 1/250 sec', $output);
        $this->assertContains('[Exif SubIFD] Exposure Time = 1/250 sec', $output);
        $this->assertContains('[Exif SubIFD] F-Number = f/8,0', $output);
        $this->assertContains('[Exif SubIFD] ISO Speed Ratings = null', $output);
        $this->assertContains('[Exif SubIFD] Date/Time Original = 2016:07:17 10:35:28', $output);
        $this->assertContains('[Exif SubIFD] Flash = null', $output);
        $this->assertContains('[Exif SubIFD] Focal Length = 51 mm', $output);
        $this->assertContains('[Exif SubIFD] Lens Model = EF-S17-55mm f/2.8 IS USM', $output);
        $this->assertContains('[File] File Name = elephant.jpg', $output);
        $this->assertContains('[File] File Size = 830001 bytes', $output);
        $this->assertContains('[File] File Modified Date = ', $output);
    }
}
