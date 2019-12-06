<?php

namespace Tests\Browser;

use Tests\DuskTestCase;
use Laravel\Dusk\Browser;
use Illuminate\Foundation\Testing\DatabaseMigrations;

class JavascriptLogsTests extends DuskTestCase
{
    public function testHomePage()
    {
        $this->browse(function(Browser $browser)
        {
            $FailTrigger = FALSE;
            $ErrorArray  = array();
            $High        = 0;
            $Sites       = array(
                '/admin/network/groups',
                '/admin/nodes',
                '/admin/nodes/create',
                '/admin/profiles',
                '/admin/profiles/calendar',
                '/admin/profiles/calendar/updates',
                '/admin/network/settings',
                '/admin/faults',
                '/admin/energy',
                '/admin/gateways',
                '/admin/gateways/create',
                '/admin/users',
                '/admin/roles',
                '/admin/permissions',
                '/admin/network/downlink',
                '/admin/ota-testing',
                '/admin/firmware',
                '/admin/firmware/updates',
                '/admin/jobs/failed',
                '/admin/users/account'
            );
            
            function logParse($browser)
            {
                $ErrorArray = array();
                $logs       = $browser->driver->manage()->getLog('browser');
                
                foreach ($logs as $key => $log) {
                    if ($log['level'] == 'SEVERE') {
                        array_push($ErrorArray, 'ERROR: ' . $log['message']);
                    } elseif (isset($log)) {
                        array_push($ErrorArray, 'WARNING: ' . $log['message']);
                    }
                }
                return $ErrorArray;
            }
            
            $browser->visit(env('APP_URL') . '/login')->type('input[type="email"]', 'test@parall.ax')->type('input[type="password"]', 'mnBD^5r67t8yiuhgu')->pause(3000);
            
            foreach (logParse($browser) as $key => $Log) {
                array_push($ErrorArray, $Log);
            }
            
            $browser->click('button[type="submit"]')->pause(3000);
            
            foreach ($Sites as $key => $Site) {
                $browser->visit(env('APP_URL') . $Site)->pause(1500);
                foreach (logParse($browser) as $key => $Log) {
                    array_push($ErrorArray, $Log);
                }
            }
            
            
            if (count($ErrorArray) > 0) {
                $Length = (max(array_map('strlen', $ErrorArray)));
                echo PHP_EOL . PHP_EOL;
                foreach ($ErrorArray as $key => $Error) {
                    echo str_repeat("-", $Length) . PHP_EOL;
                    if (substr($Error, 0, 6) != 'ERROR:') {
                        echo "\e[1;33;40m" . $Error . "\e[0m" . PHP_EOL;
                        $FailTrigger = TRUE;
                    } else {
                        echo "\e[1;31;40m" . $Error . "\e[0m" . PHP_EOL;
                    }
                }
                echo str_repeat("-", $Length) . PHP_EOL;
            }
            
            if ($FailTrigger == TRUE) {
                $this->assertTrue(false);
            } else {
                $this->assertTrue(true);
            }
            
        });
    }
}