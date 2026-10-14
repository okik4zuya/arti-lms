<?php

namespace Tests\Feature;

use Tests\TestCase;

/**
 * Routes bind to config('app.domains.main') at boot, so the env var has to be in place
 * before the application is created in setUp().
 */
class DomainRoutingTest extends TestCase
{
    protected function setUp(): void
    {
        putenv('APP_DOMAIN_MAIN=main.example.test');
        $_ENV['APP_DOMAIN_MAIN'] = $_SERVER['APP_DOMAIN_MAIN'] = 'main.example.test';

        parent::setUp();
    }

    protected function tearDown(): void
    {
        parent::tearDown();

        putenv('APP_DOMAIN_MAIN');
        unset($_ENV['APP_DOMAIN_MAIN'], $_SERVER['APP_DOMAIN_MAIN']);
    }

    public function test_main_domain_serves_the_public_site_and_auth(): void
    {
        $this->get('http://main.example.test/')->assertOk();
        $this->get('http://main.example.test/fitur')->assertOk();
        $this->get('http://main.example.test/login')->assertOk();
        $this->get('http://main.example.test/robots.txt')->assertOk();
    }

    public function test_other_hosts_do_not_serve_main_domain_routes(): void
    {
        $this->get('http://toko.example.test/')->assertNotFound();
        $this->get('http://toko.example.test/fitur')->assertNotFound();
        $this->get('http://toko.example.test/login')->assertNotFound();
        $this->get('http://toko.example.test/jamrud')->assertNotFound();
    }

    public function test_generated_urls_use_the_main_domain(): void
    {
        $this->assertSame('http://main.example.test/fitur', route('fitur'));
        $this->assertSame('http://main.example.test/login', route('login'));
    }
}
