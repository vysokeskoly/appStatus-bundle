<?php declare(strict_types=1);

namespace VysokeSkoly\AppStatusBundle\Services;

use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use VysokeSkoly\AppStatusBundle\Entity\Item;

class AppStatusCollectorTest extends TestCase
{
    /**
     * @dataProvider mainStatusKeyProvider
     */
    public function testShouldCollectBuildInfoData(?string $mainStatusKey, string $expectedMainStatus): void
    {
        $appRoot = __DIR__ . '/../..';
        $sourceFile = 'tests/Fixtures/buildinfo.xml';
        $appStatusCollector = new AppStatusCollector($appRoot, $sourceFile, $mainStatusKey);

        $appStatusCollector->collect(new Request(), new Response());

        $this->assertFileExists($sourceFile);
        $this->assertSame('appStatus', $appStatusCollector->getName());
        $this->assertSame($sourceFile, $appStatusCollector->getStatusFile());
        $this->assertSame($expectedMainStatus, $appStatusCollector->getMainStatus());
        $this->assertEquals(
            [
                'name' => new Item('name', 'vs-app-status'),
                'version' => new Item('version', '2017.03.08.16.30.28-68.gad10e8af8'),
                'sourceRevision' => new Item('sourceRevision', 'ad10e8af8814f825e36e629ab1a19c5078a6d257'),
                'repository' => new Item('repository', 'ssh://git/app-status-bundle.git'),
                'buildNumber' => new Item('buildNumber', '666'),
                'buildBranch' => new Item('buildBranch', 'feature/app-status-bundle'),
                'buildUrl' => new Item('buildUrl', 'https://jenkins/job/app-status/666/'),
                'project' => new Item('project', 'app_status_bundle'),
                'hostName' => new Item('hostName', (string) gethostname()),
                'node' => new Item('node', ''),
            ],
            $appStatusCollector->getAppStatus()
        );
    }

    public function mainStatusKeyProvider(): array
    {
        return [
            'empty' => [null, 'Unknown status'],
            'name' => ['name', 'vs-app-status'],
            'hostName' => ['hostName', gethostname()],
        ];
    }

    public function testShouldCollectEnvData(): void
    {
        $appRoot = __DIR__ . '/../..';
        $sourceFile = 'tests/Fixtures/buildinfo.xml';
        $envFile = __DIR__ . '/../Fixtures/config.xml';
        $appStatusCollector = new AppStatusCollector($appRoot, $sourceFile, null, $envFile);

        $appStatusCollector->collect(new Request(), new Response());
        $envName = $appStatusCollector->getEnvName();
        $envColor = $appStatusCollector->getEnvColor();

        $this->assertSame('prod', $envName);
        $this->assertSame('green', $envColor);
    }
}
