<?php

namespace SuperVMar\App\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class SaleBillPreviewCommand extends Command
{
    protected static string $defaultName = 'sale:bill:preview';

    public function __construct(
        private readonly string $projectDir,
    ) {
        parent::__construct(self::$defaultName);
    }

    protected function configure(): void
    {
        $this->setDescription('Preview a generated ESC/POS bill in the terminal')
            ->addArgument('path', InputArgument::REQUIRED, 'Relative path to the .bin file (e.g. documents/bills/2026/05/uuid-date.bin) or full absolute path');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $path = (string) $input->getArgument('path');

        $fullPath = str_starts_with($path, '/') ? $path : $this->projectDir . '/public/' . $path;

        if (!file_exists($fullPath)) {
            $output->writeln("<error>File not found: {$fullPath}</error>");
            return self::FAILURE;
        }

        $raw = file_get_contents($fullPath);

        $text = $this->stripEscPos($raw);

        $output->writeln('');
        $output->writeln('<info>┌' . str_repeat('─', 50) . '┐</info>');
        foreach (explode("\n", $text) as $line) {
            $output->writeln('<info>│</info> ' . $line);
        }
        $output->writeln('<info>└' . str_repeat('─', 50) . '┘</info>');
        $output->writeln('');

        return self::SUCCESS;
    }

    private function stripEscPos(string $raw): string
    {
        $patterns = [
            "\x1B\x40",         // INIT
            "\x1B\x45\x01",     // BOLD ON
            "\x1B\x45\x00",     // BOLD OFF
            "\x1B\x61\x01",     // ALIGN CENTER
            "\x1B\x61\x00",     // ALIGN LEFT
            "\x1B\x61\x02",     // ALIGN RIGHT
            "\x1B\x21\x10",     // DOUBLE HEIGHT ON
            "\x1B\x21\x00",     // DOUBLE HEIGHT OFF
            "\x1D\x56\x42\x00", // CUT
        ];

        $text = str_replace($patterns, '', $raw);

        $text = preg_replace('/\x1B./s', '', $text);
        $text = preg_replace('/\x1D./s', '', $text);
        $text = preg_replace('/[\x00-\x09\x0B-\x1F\x7F]/', '', $text);

        return trim($text);
    }
}
