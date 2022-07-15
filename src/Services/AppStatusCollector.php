<?php declare(strict_types=1);

namespace VysokeSkoly\AppStatusBundle\Services;

use Assert\Assertion;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\DataCollector\DataCollector;
use VysokeSkoly\AppStatusBundle\Entity\Item;

class AppStatusCollector extends DataCollector
{
    public const DEFAULT_MAIN_STATUS = 'Unknown status';

    public function __construct(
        private string $projectRoot,
        private string $appStatusFilePath,
        private ?string $mainStatusKey = null,
        private ?string $envFile = null,
    ) {
    }

    public function getName(): string
    {
        return 'appStatus';
    }

    public function collect(Request $request, Response $response, ?\Throwable $exception = null): void
    {
        $appStatus = [];
        $filename = $this->projectRoot . '/' . $this->appStatusFilePath;
        $envName = '';
        $envColor = '';

        if (file_exists($filename) && is_readable($filename)) {
            $statusArray = $this->loadXmlAsArray($filename);

            foreach ($statusArray as $key => $value) {
                if (is_array($value)) {
                    $value = empty($value)
                        ? ''
                        : var_export($value, true);
                }

                if ($key === 'hostName') {
                    $value = str_replace('__HOSTNAME__', (string) gethostname(), $value);
                }

                $appStatus[$key] = new Item((string) $key, trim($value));
            }
        }

        if (file_exists((string) $this->envFile) && is_readable((string) $this->envFile)) {
            $xml = $this->loadXml((string) $this->envFile);
            $attributes = $xml->attributes();
            Assertion::notNull($attributes);

            $envName = (string) ($attributes->name ?? '');

            if ($envName === 'prod') {
                $envColor = 'green';
            } elseif ($envName === 'dev') {
                $envColor = 'yellow';
            }
        }

        $this->data = [
            'mainStatus' => $this->findMainStatus($appStatus),
            'appStatus' => $appStatus,
            'statusFile' => $this->appStatusFilePath,
            'envName' => $envName,
            'envColor' => $envColor,
        ];
    }

    private function loadXmlAsArray(string $file): array
    {
        return json_decode((string) json_encode($this->loadXml($file)), true);
    }

    private function loadXml(string $file): \SimpleXMLElement
    {
        $simpleXMLElement = simplexml_load_string((string) file_get_contents($file));
        if ($simpleXMLElement === false) {
            throw new \RuntimeException(sprintf('XML "%s" could not be loaded.', $file));
        }

        return $simpleXMLElement;
    }

    /**
     * @param Item[] $appStatus
     */
    private function findMainStatus(array $appStatus): string
    {
        return array_key_exists((string) $this->mainStatusKey, $appStatus)
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

    public function getEnvName(): string
    {
        return $this->data['envName'];
    }

    public function getEnvColor(): string
    {
        return $this->data['envColor'];
    }
}
