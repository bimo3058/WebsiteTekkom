<?php

namespace App\Services;

use Symfony\Component\Process\Process;

class WindowsPulseServerMetrics
{
    private ?array $snapshot = null;

    public function cpu(): int
    {
        return $this->snapshot()['cpu'];
    }

    public function memory(): array
    {
        return $this->snapshot()['memory'];
    }

    private function snapshot(): array
    {
        if ($this->snapshot !== null && $this->snapshot['expires_at'] > microtime(true)) {
            return $this->snapshot;
        }

        // WMIC is absent on recent Windows installations. Query the same counters through CIM.
        $script = <<<'POWERSHELL'
$ErrorActionPreference = 'Stop'
$operatingSystem = Get-CimInstance Win32_OperatingSystem
$processors = Get-CimInstance Win32_Processor
@{
    cpu = [int][Math]::Round(($processors | Measure-Object -Property LoadPercentage -Average).Average)
    total_kb = [long]$operatingSystem.TotalVisibleMemorySize
    free_kb = [long]$operatingSystem.FreePhysicalMemory
} | ConvertTo-Json -Compress
POWERSHELL;
        $process = new Process(['powershell.exe', '-NoLogo', '-NoProfile', '-NonInteractive', '-WindowStyle', 'Hidden', '-Command', $script]);
        $process->setTimeout(15)->mustRun();
        $metrics = json_decode($process->getOutput(), true, 512, JSON_THROW_ON_ERROR);

        return $this->snapshot = [
            'expires_at' => microtime(true) + 10,
            'cpu' => max(0, min(100, (int) $metrics['cpu'])),
            'memory' => [
                'total' => (int) ($metrics['total_kb'] / 1024),
                'used' => (int) (($metrics['total_kb'] - $metrics['free_kb']) / 1024),
            ],
        ];
    }
}
