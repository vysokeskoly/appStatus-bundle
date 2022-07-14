<?php declare(strict_types=1);

namespace VysokeSkoly\AppStatusBundle\Services;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\DataCollector\DataCollector;
use VysokeSkoly\AppStatusBundle\Entity\Item;

class AppStatusCollector extends DataCollector
{
    public const DEFAULT_MAIN_STATUS = 'Unknown status';

    private string $appRoot;
    private string $appStatusFilePath;
    private ?string $mainStatusKey;

    public function __construct(string $appRoot, string $appStatusFilePath, ?string $mainStatusKey = null)
    {
        $this->appRoot = $appRoot;
        $this->appStatusFilePath = $appStatusFilePath;
        $this->mainStatusKey = $mainStatusKey;
    }

    public function getName(): string
    {
        return 'appStatus';
    }

    public function collect(Request $request, Response $response, ?\Throwable $exception = null): void
    {
        $appStatus = [];
        $filename = $this->appRoot . '/' . $this->appStatusFilePath;

        if (file_exists($filename) && is_readable($filename)) {
            $xmlContent = simplexml_load_string((string) file_get_contents($filename));
            $statusArray = json_decode((string) json_encode($xmlContent), true);

            foreach ($statusArray as $key => $value) {
                if ($key === 'hostName') {
                    $value = str_replace('__HOSTNAME__', (string) gethostname(), $value);
                }

                $appStatus[$key] = new Item($key, trim($value));
            }
        }

        $this->data = [
            'mainStatus' => $this->findMainStatus($appStatus),
            'appStatus' => $appStatus,
            'statusFile' => $this->appStatusFilePath,
        ];
    }

    /**
     * @param Item[] $appStatus
     */
    private function findMainStatus(array $appStatus): string
    {
        return is_string($this->mainStatusKey) && array_key_exists($this->mainStatusKey, $appStatus)
            ? $appStatus[$this->mainStatusKey]->getValue()
            : self::DEFAULT_MAIN_STATUS;
    }

    public function getStatusFile(): string
    {
        return $this->data['statusFile'];
    }

    /**
     * @return Item[]
     */
    public function getAppStatus(): array
    {
        return $this->data['appStatus'];
    }

    public function getMainStatus(): string
    {
        return $this->data['mainStatus'];
    }

    public function reset(): void
    {
        $this->data = [];
    }
}
