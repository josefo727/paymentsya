<?php

namespace Tests\Unit;

use PHPUnit\Framework\TestCase;
use App\Services\DomainUrlService;

class DomainUrlServiceTest extends TestCase
{
    /** @var DomainUrlService */
    protected $domainUrlService;

    protected function setUp(): void
    {
        parent::setUp();
        $this->domainUrlService = new DomainUrlService();
    }

    /** @test */
    public function is_workspace_returns_true(): void
    {
        $origin = "https://pse--massivespacenew.myvtex.com";
        $master = "https://massivespacenew.myvtex.com/";

        $result = $this->domainUrlService->isWorkspace($origin, $master);

        $this->assertTrue($result);
    }

    /** @test */
    public function is_workspace_returns_false(): void
    {
        $origin = "https://massivespacenew.myvtex.com";
        $master = "https://massivespacenew.myvtex.com/";

        $result = $this->domainUrlService->isWorkspace($origin, $master);

        $this->assertFalse($result);
    }

    /** @test */
    public function is_workspace_with_different_master_returns_false(): void
    {
        $origin = "https://pse--massivespacenew.myvtex.com";
        $master = "https://differentmaster.myvtex.com/";

        $result = $this->domainUrlService->isWorkspace($origin, $master);

        $this->assertFalse($result);
    }
}
